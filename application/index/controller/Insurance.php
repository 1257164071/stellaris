<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/19
 * Time: 12:52
 */

namespace app\index\controller;
use think\session;
use think\Controller;
use think\Db;


class Insurance extends Base
{
    public function insuranceList()
    {
        //待审核订单

        $list = Db::table("b_insurance_buy")->where("bill_flag > 0 && bill_flag != 4")->order("create_date desc")->select();

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
    <tr><td>保险单号:</td><td>$bill_no</td></tr>
    <tr><td>创建时间:</td><td>$create_date</td></tr>            
    <tr><td>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注:</td><td>$insurance_remark</td></tr>            
</table>
EOT;
        echo $table;
    }

    //人员详情
    public function staffInfo($bill_no)
    {
        $list = Db::table('b_insurance_buy_detail')->where("bill_no='{$bill_no}'")->select();

        $this->assign('list', $list);
        return $this->fetch();
    }

    //保单审核页面
    public function insuranceDetails($bill_no)
    {
        $info = Db::table('b_insurance_buy')->where("bill_no='{$bill_no}'")->find();

        $this->assign('info', $info);
        return $this->fetch();
    }

    //表单审核
    public function insuranceConfirm($bill_no)
    {
        $_POST['bill_flag'] = 2;
        $re = Db::table("b_insurance_buy")->where("bill_no='{$bill_no}'")->update($_POST);
        $url = url('Insurance/insuranceList');
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
    public function confirmChuDan($bill_no)
    {
        $data = $this->upload();
        $img_path = "{$data[0]['savepath']}" . "{$data[0]['savename']}";
        $arr = array(
            'img_path' => $img_path,
            'bill_flag' => 4
        );

        $re = Db::table('b_insurance_buy')->where("bill_no='{$bill_no}'")->update($arr);
        $url = url('Insurance/insuranceList');
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

    public function insu_policy()
    {
        $list = Db::table('b_insurance_buy')->where('bill_flag<4')->select();


        $this->assign('list', json_encode($list));
        return $this->fetch();
    }

    public function insu_add()
    {

        return $this->fetch();
    }

    public function insu_detel()
    {
        $data = Db::table('b_insurance_buy')->where(['id' => $_GET['id']])->select()[0];
        $user_list = Db::table('b_insurance_buy_detail')->where(['bill_no' => $data['bill_no']])->select();

        $this->assign('data', json_encode($data));
        $this->assign('user_list', json_encode($user_list));
        return $this->fetch();
    }

    //添加入保人员名单
    public function add_user()
    {
        //$this->log_write($_POST);
        $re=Db::name('b_insurance_buy_detail')->insert($_POST);
        if($re){
            $this->ajaxSuccess('添加成功');
        }else{
            $this->ajaxError('添加失败');
        }

    }

    //自定义写日志的函数
    public function log_write($msg){
        $data = date('Y-m-d H:i:s',time())."\r\n";
        if(is_array($msg)){
            $msg = var_export($msg,true);
        }
        $data .= $msg."\r\n";
        $file = 'log.txt';
        file_put_contents($file,$data,FILE_APPEND);
    }

    public function ajax_insu_add(){
        //接收人员id
        $id=Session::get('admin_id');
        //接收投保人数
        $pnums=$_POST['buy_punm'];
        //保单备注

//        $this->log_write($_POST);die;

        if(!in_array('insurance_remark',$_POST)){
            $insurance_remark='';
        }else{
            $insurance_remark = $_POST['insurance_remark'];
        }

        $org_id=Db::table('t_business_user')->where("id='{$id}'")->find()['org_id'];
        $businessInfo=Db::table('t_business_info')->where("id='{$org_id}'")->find();
        $bid=$businessInfo['industry'];
        $info=Db::table('b_insurance_item')->where("bid='{$bid}' and min_pnum<'{$pnums}' and max_pnum>='{$pnums}'")->find();

        if($info['insurance_type']==1){
            //按人数购买
            $insurance_amt=$info['insurance_baseamt']+$info['insurance_price']*$pnums;
        }elseif($info['insurance_type']==2){
            //按固定保额购买
            $insurance_amt=$info['insurance_baseamt'];
        }else{
            //固定保额+增加人数购买
            $a=$pnums-1000;
            $num=$a/200;
            $insurance_amt=$info['insurance_baseamt']+$num*$info['insurance_raiseprice'];
        }

        $arr=array(
            'insurance_remark'=>$insurance_remark,
            'bill_no'=>date('YmdHis').rand(10000000,99999999),
            'year_no'=>date("Y",time()),
            'org_code'=>$businessInfo['org_code'],
            'org_name'=>$businessInfo['org_name'],
            'org_address'=>$businessInfo['business_address'],
            //'org_spp'=>$businessInfo['image_path'],
            'insurance_name'=>$info['insurance_name'],
            'insurance_code'=>$info['insurance_code'],
            'min_pnum'=>$info['min_pnum'],
            'max_pnum'=>$info['max_pnum'],
            'buy_punm'=>$pnums,
            'insurance_type'=>$info['insurance_type'],
            'insurance_quota'=>$info['insurance_quota'],
            'insurance_baseamt'=>$info['insurance_baseamt'],
            'insurance_price'=>$info['insurance_price'],
            'insurance_raiseprice'=>$info['insurance_raiseprice'],
            'insurance_amt'=>$insurance_amt,
//            'insurance_float'=>'',
//            'insurance_real_amt'=>'',
            'bill_flag'=>0,
            'create_date'=>date('Y-m-d H:i:s',time()),
        );

        $re=Db::table('b_insurance_buy')->insert($arr);
        if($re){
            $this->ajaxSuccess();
        }else{
            $this->ajaxError();
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