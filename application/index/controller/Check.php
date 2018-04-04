<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/30
 * Time: 13:54
 */
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Session;
class Check extends Base
{
    public function CheckList()
    {
        //待审核订单

        $list = Db::table("b_check_buy")->where("bill_flag > 0 && bill_flag != 5")->order("create_time desc")->select();

        $this->assign('list', $list);
        return $this->fetch();
    }

    public function detail()
    {
        $post = json_decode($_POST['data'], true);
        $res = Db::table('b_insurance_buy')->where(['id' => $post[1]])->select()[0];
        $bill_no = $res['bill_no'];
        $create_date = $res['create_date'];
        $insurance_remark = $res['insurance_remark'];
        $table = <<<EOT
        
<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
    <tr><td>体检单号:</td><td>$bill_no</td></tr>
    <tr><td>创建时间:</td><td>$create_date</td></tr>            
    <tr><td>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注:</td><td>$insurance_remark</td></tr>            
</table>
EOT;
        echo $table;
    }

    //人员详情
    public function staffInfo($bill_no)
    {
        $list = Db::table('b_check_buy_detail')->where("bill_no='{$bill_no}'")->select();

        $this->assign('list', $list);
        return $this->fetch();
    }

    //保单审核页面
    public function checkDetails($bill_no)
    {
        $info = Db::table('b_check_buy')->where("bill_no='{$bill_no}'")->find();

        $this->assign('info', $info);
        return $this->fetch();
    }

    //表单审核
    public function checkConfirm($bill_no)
    {
        $_POST['bill_flag'] = 2;
        $re = Db::table("b_check_buy")->where("bill_no='{$bill_no}'")->update($_POST);
        $url = url('Check/checkList');
        header("location:$url");
        die;
    }

    //确认出单页面
    public function confirm($bill_no)
    {

        $this->assign('bill_no', $bill_no);
        return $this->fetch();
    }

    public function upload()
    {
        if ($_FILES['file']['name'] != '') {
            //如果有文件上传 上传附件
            return $this->_upload();
        }
    }

    // 文件上传
    protected function _upload()
    {
        import('@.ORG.UploadFile');
        //导入上传类
        $upload = new \Upload\UploadFile();
        //设置上传文件大小
        $upload->maxSize = -1;
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,mp4,jpeg,bmp,png');
        //设置附件上传目录
        $upload->savePath = './Uploads/';
        // 设置引用图片类库包路径
        $upload->imageClassPath = '@.ORG.Image';
        //设置上传文件规则
        $upload->saveRule = 'uniqid';
        //删除原图
        $upload->thumbRemoveOrigin = true;
        if (!$upload->upload()) {
            //捕获上传异常
            $this->error($upload->getErrorMsg());
        } else {
            //取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();

            import('@.ORG.Image');
        }
//        $model  = M('Photo');
        //保存当前数据对象
//        $list   = $model->add($data);

        return $uploadList;
    }

    //确认出单
    public function confirmShouKuan($bill_no)
    {
        $arr = array(
            'bill_flag' => 4
        );

        $re = Db::table('b_check_buy')->where("bill_no='{$bill_no}'")->update($arr);
//        $this->log_write(Db::table('b_check_buy')->getLastSql());
        $url = url('Check/checkList');
        header("location:$url");
        die;
    }

    //历史订单查询
    public function insuranceRecord()
    {
        $list = Db::table('b_insurance_buy')->where("bill_flag=4")->select();

        $this->assign('list', $list);
        return $this->fetch();
    }

    public function check_policy()
    {
        $list = Db::table('b_check_buy')->where('bill_flag<5')->select();


        $this->assign('list', json_encode($list));
        return $this->fetch();
    }
    //体检单添加
    public function check_add()
    {
        $check_company = Db::table('b_check_company')->select();
        $this->assign('check_company', json_encode($check_company));

        return $this->fetch();
    }

    //添加体检单
    public function add_user_form(){
        $id=Session::get('admin_id');
        //接收投保人数
        $nums=$_POST['check_num'];
        //保单备注
        $check_remark=$_POST['check_remark'];

        //$this->log_write(Session::get());die;
        //$this->log_write($_POST);die;
        $check_company_id=$_POST['check_company_id'];
        $check_price=Db::name('b_check_company')->where("id='{$check_company_id}'")->find()['check_price'];
        $org_id=Db::table('t_business_user')->where("id='{$id}'")->find()['org_id'];
        //$this->log_write($org_id);
        $businessInfo=Db::table('t_business_info')->where("id='{$org_id}'")->find();
        //$this->log_write($businessInfo);

        $post=array(
            'check_remark'=>$check_remark,
            'bill_no'=>date('YmdHis').rand(10000000,99999999),
            'year_no'=>date("Y",time()),
            'org_code'=>$businessInfo['org_code'],
            'org_name'=>$businessInfo['org_name'],
            'org_address'=>$businessInfo['business_address'],
            'check_num'=>$nums,
            'check_company_id'=>$check_company_id,
            'check_price'=>$check_price,
//            'insurance_float'=>'',
//            'insurance_real_amt'=>'',
            'total_amt'=>$check_price*$nums,
            'bill_flag'=>1,
            'create_time'=>date('Y-m-d H:i:s',time()),
        );


        $re=Db::name('b_check_buy')->insert($post);
        if($re){
            $this->ajaxSuccess('添加成功');
        }else{
            $this->ajaxError('添加失败');
        }
    }
    //体检单详情
    public function check_detel()
    {
        $data = Db::table('b_check_buy')->where(['id' => $_GET['id']])->select()[0];
        $user_list = Db::table('b_check_buy_detail')->where(['bill_no' => $data['bill_no']])->select();

        $this->assign('data', json_encode($data));
        $this->assign('user_list', json_encode($user_list));
        return $this->fetch();
    }

    //添加人员名单
    public function add_user()
    {
        //$this->log_write($_POST);
        $re=Db::name('b_check_buy_detail')->insert($_POST);
        if($re){
            $this->ajaxSuccess('添加成功');
        }else{
            $this->ajaxError('添加失败');
        }

    }
    //删除人员
    public function del_user($id){
        $re=Db::name('b_check_buy_detail')->where("id='{$id}'")->delete();
        if($re){
            $this->ajaxSuccess('删除成功');
        }else{
            $this->ajaxError('删除失败');
        }
    }

    public function get_pay()
    {
//        $core = new \Pay\Core(['transCode'=>'']);
//        $core->toPay();
        $pay = new \Wxpay\Weixinpay();
        $order = array(
            'body' => 'test',
            'total_fee' => 1,
            'out_trade_no' => strval(time()),
            'product_id' => 1
        );
        $pay->pay($order);

    }


}