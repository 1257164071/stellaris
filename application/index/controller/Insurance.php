<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/19
 * Time: 12:52
 */

namespace app\index\controller;

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

    public function add_user()
    {
        $this->ajaxSuccess('a');
    }
}