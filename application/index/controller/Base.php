<?php
/**
 * Created by PhpStorm.
 * User: nightdays
 * Date: 18-3-12
 * Time: 下午2:36
 */

namespace app\index\controller;

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
            $this->redirect('Login/index');
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


}
