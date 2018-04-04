<?php
/**
 * Created by PhpStorm.
 * User: nightdays
 * Date: 18-3-12
 * Time: 下午2:36
 */

namespace app\index\controller;

use Upload\UploadFile;
use think\Controller;
use think\Db;
use think\Request;
use think\Url;
use think\Session;

url::root('/index.php');
class Base extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct();
        //权限控制
        $admin_id = Session::get('admin_id');
        if($admin_id == null){
            header("Location:" . url('Login/index'));
            die;
        }

        //获得列表
        $this->getMenu();
    }

    public function getMenu()
    {
        $res = getMenuList();
        $admin_id = Session::get('admin_id');
        $res = Db::table('t_business_user')->where(['id'=>$admin_id])->value('user_post_id');
        $res = Db::table('role_menu')->where(['role_id'=>$res])->select();
        $arr = array();
        $res2 = array();
        foreach ($res as $key => $val){
            $data = Db::table('menu')->where(['id'=>$val['permis_id']])->find();
            $arr[] = $data['f_parentid'];
            $res2[] = $data;
        }
        $res = array();
        $arr = array_unique($arr);
        foreach ($arr as $key => $val){
            $res[]=Db::table('menu')->where(['f_moduleid'=>$val])->find();
        }
        $res = array_merge($res2,$res);
        $res = json_encode($res);

        $this->assign('MenuList', $res);
    }

    public function ajaxSuccess($msg = null, $data = array())
    {
        $data = json_decode(str_replace(':null', ':""', json_encode($data)));
        $result = array(
            'flag' => 'Success',
            'msg' => $msg,
            'data' => $data
        );
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result, JSON_UNESCAPED_SLASHES));
    }

    public function ajaxError($msg = null, $data = array())
    {
        $data = json_decode(str_replace(':null', ':""', json_encode($data)));
        $result = array(
            'flag' => 'Error',
            'msg' => $msg,
            'data' => $data
        );
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result, JSON_UNESCAPED_SLASHES));
    }

    public function upload()
    {

        if ($_FILES != '') {
            //如果有文件上传 上传附件
            return $this->_upload()[0];
        }


    }

    // 文件上传
    protected function _upload()
    {
        import('@.ORG.UploadFile');
        //导入上传类
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize = -1;
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,mp4,txt,png,jpeg');
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
        $data['image'] = $_POST['image'];
        $data['create_time'] = NOW_TIME;
//        $list   = $model->add($data);

        return $uploadList;
    }

    public function log_write($msg){
        $data = date('Y-m-d H:i:s',time())."\r\n";
        if(is_array($msg)){
            $msg = var_export($msg,true);
        }
        $data .= $msg."\r\n";
        $file = 'log.txt';
        file_put_contents($file,$data,FILE_APPEND);
    }


}
