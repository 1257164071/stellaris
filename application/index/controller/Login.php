<?php
/**
 * Created by PhpStorm.
 * User: nightdays
 * Date: 18-3-15
 * Time: 上午10:17
 */

namespace app\index\controller;

use think\Controller;

use app\index\model\BusinessUser;
use think\Db;
use think\Session;

class Login extends Controller
{
    public function index()
    {

        return $this->fetch();
    }

    public function login_check()
    {
        $post = $_POST;
        $User = new BusinessUser();

        $post['user_password'] = md5($post['user_password']);
        $res = $User->get($post);
        if($res['id'] != null){
            Session::set('admin_id',$res['id']);
            $this->ajaxSuccess('成功');
        }else{
            $this->ajaxError('用户名或密码错误');
        }

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
}