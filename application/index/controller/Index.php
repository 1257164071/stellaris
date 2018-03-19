<?php
namespace app\index\controller;

use think\Db;

class Index extends Base
{
    public function index()
    {

        return $this->fetch();
    }

    public function index_i()
    {

        return $this->fetch();
    }

    public function log_out()
    {

        session(null);
//        header("Location:".url('Login/index'));
        $this->redirect(url('Login/index'));
    }


    public function user_ctrl()
    {

        $this->get_data();
        return $this->fetch();
    }

    public function user_edit()
    {
        $id = $_GET['id'];
        $res = Db::table('t_business_user')->where(['id' => $id])->select()[0];

        $qx_list = Db::table('role')->select();
        $this->assign('qx_list',json_encode($qx_list));
        $this->assign('data', json_encode($res));
        $this->assign('id', $id);
        return $this->fetch();
    }


    public function user_edit_form()
    {
        $data = array(
            'user_name' =>$_POST['user_name'],
            'user_mobile'   => $_POST['user_mobile'],
            'user_post_id'  =>  $_POST['user_post_id']
        );
        $res = Db::table('t_business_user')->where(['id' => $_POST['id']])->update($data);

        if($res == null){
            $this->ajaxError('失败');
        }else{
            $this->ajaxSuccess("成功");
        }
    }

    public function user_del()
    {

    }


    public function test()
    {
        $arr = json_decode($_POST['data'], true);

        $res = Db::table('t_business_user')->where('id', 6)->select();

        $res = "12345111116";
        $table = <<<EOT
        
<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
    <tr><td>姓名</td><td>$res</td><td>发放</td><td>aaaaaaaa</td></tr>
    <tr><td>姓名</td><td>aaaaaaaa</td></tr>            
    <tr><td>姓名</td><td>aaaaaaaa</td></tr>            
</table>
EOT;

        echo $table;
    }


    //获取数据
    public function get_data()
    {
        $res = $this->show_table('t_business_user');
        $data = Db::table('t_business_user')->select();
        $this->assign('res', json_encode($res));
        $this->assign('data', json_encode($data));
    }


    //获取表头数据 和前台显示内容 封装数据

    public function show_table($tableName)
    {

        $res = Db::table('table_show')->where(['table_name' => $tableName, 'flag' => 1])->select();
        return $res;
    }

    public function index2()
    {

        return $this->fetch('user_ctrl');
    }

    //权限
    public function permis_index()
    {
        $res = Db::table('role')->select();
        $this->assign('list', json_encode($res));
        return $this->fetch();
    }

    public function add_role()
    {

        $res = Db::table('menu')->where(['f_parentid' => 0])->select();
        foreach ($res as $key => $val) {
            $res[$key]['data'] = Db::table('menu')->where(['f_parentid' => $val['f_moduleid']])->select();
        }
        $this->assign('permis_list', json_encode($res));

        return $this->fetch();
    }

    public function add_role_form()
    {
        $post = $_POST;
        $res = Db::table('role')->where(['role_name' => $_POST['role_name']])->select();

        if ($res != null) {
            echo "<script>alert('重复的角色名')</script>";
            echo "<script>window.location.href='" . url('Index/permis_index') . "'</script>";
            die;
        }
        Db::table('role')->insert(['role_name' => $_POST['role_name']]);
        $res = Db::table('role')->where(['role_name' => $_POST['role_name']])->value('id');
        unset($post['role_name']);

        foreach ($post as $key => $val) {
            $data = array(
                'role_id' => $res,
                'role_name' => $_POST['role_name'],
                'permis_id' => $val,
                'permis_name' => Db::table('menu')->where(['id' => $val])->value('f_fullname')
            );
//            dump($data);die;
            Db::table('role_menu')->insert($data);
        }
        $this->redirect('Index/permis_index');
    }

    public function role_change()
    {
        $role_id = $_GET['id'];
        $res = Db::table('menu')->where(['f_parentid' => 0])->select();
        foreach ($res as $key => $val) {
            $qx_data = Db::table('menu')->where(['f_parentid' => $val['f_moduleid']])->select();
            $res[$key]['data'] = $qx_data;
            //查询check状态
            foreach ($qx_data as $key2 => $val2) {
                $check_flag = Db::table('role_menu')->where(['role_id' => $role_id, 'permis_id' => $val2['id']])->select();
                if ($check_flag != null) {
                    $res[$key]['data'][$key2]['check_flag'] = true;
                } else {
                    $res[$key]['data'][$key2]['check_flag'] = false;
                }
            }
        }
        $this->assign('permis_list', json_encode($res));
        $this->assign('name', Db::table("role")->where(['id' => $role_id])->value('role_name'));
        $this->assign('id', $role_id);

        return $this->fetch();
    }

    public function role_delect()
    {

        $id = $_POST['id'];

        $res = Db::table('role')->where(['id' => $id])->delete();

        $list = Db::table('role_menu')->where(['role_id' => $id])->select();
        foreach ($list as $key => $val) {
            Db::table('role_menu')->where(['role_id' => $val['id']])->delete();
        }
        if ($res != null) {
            $this->ajaxSuccess('成功');
        } else {
            $this->ajaxError('失败');
        }

    }

    public function save_role()
    {

//        $role_table = Db::table('role');
        $id = $_POST['role_id'];
        $role_add = $_POST;
        Db::table('role')->where(['id' => $id])->setField('role_name', $_POST['role_name']);
        unset($role_add['role_id']);
        unset($role_add['role_name']);

        foreach ($role_add as $key => $val) {
            $data = array(
                'role_id' => $id,
                'permis_id' => $val,
            );
            $res = Db::table('role_menu')->where($data)->select();
            if ($res == null) {
                Db::table('role_menu')->data($data)->insert();
            }
        }

        $data = Db::table('role_menu')->where(['role_id' => $id])->select();
        $data_copy = $data;
        foreach ($data as $key => $val) {
            foreach ($role_add as $key2 => $val2) {
                if ($val['permis_id'] == $val2) {
                    unset($data_copy[$key]);
                }
            }
        }

        foreach ($data_copy as $key => $val) {
            Db::table('role_menu')->where(['id' => $val['id']])->delete();
        }

        $this->redirect('Index/permis_index');
    }






}
