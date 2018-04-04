<?php 
namespace app\index\controller;

use think\Db;
use think\Session;

class Information extends Base
{
	/*
	
	登录人基本信息

	 */ 
	// 基本信息查看
	public function index(){
		// 登录人信息
        $id = Session::get('admin_id');
        $res = Db::table('t_business_user')->where(['id'=>$id])->find();
		$this->assign('user',$res);

		// 所属企业
        $org = Db::table('t_business_info')->where(['id'=>$res['org_id']])->find();
        $this->assign('org',$org);

        // 用户职位
        $user_post = Db::table('t_business_post')->where(['id'=>$res['user_post_id']])->find();
        $this->assign('user_post',$user_post);

        // 所属管辖
        $gov = Db::table('t_gov')->where(['id'=>$res['gov_id']])->find();
        $this->assign('gov',$gov);
        return $this->fetch();
    }

    // 基本信息修改
    public function update(){
    	$id = Session::get('admin_id');
        $res = Db::table('t_business_user')->where(['id'=>$id])->find();
		$this->assign('user',$res);

		// 所属企业
        $org = Db::table('t_business_info')->where(['id'=>$res['org_id']])->find();
        $this->assign('org',$org);

        // 用户职位
        $user_post = Db::table('t_business_post')->where(['id'=>$res['user_post_id']])->find();
        $this->assign('user_post',$user_post);

        // 所属管辖
        $gov = Db::table('t_gov')->where(['id'=>$res['gov_id']])->find();
        $this->assign('gov',$gov);
        return $this->fetch();
    }

    // 
    public function update_sub(){
    	$id = Session::get('admin_id');
    	$user_code = $_POST['user_code'];
    	$user_name = $_POST['user_name'];
    	$user_mobile = $_POST['user_mobile'];
    	$user_sex = $_POST['user_sex'];
        $file = input('file');

        $data = $this->upload();
        // dump($data);die;
        $res = Db::table('t_business_user')
            ->where(['id' => $id])
            ->update(
                [
                'user_code'    =>   $user_code,
                'user_name'    =>   $user_name,
                'user_mobile'  =>   $user_mobile,
                'user_sex'     =>   $user_sex,
                'img_path'     =>   $data[0]['savepath'].$data[0]['savename']
                ]
            );
        if($res){
             $this->success('信息修改成功','index');
        }else{
            $this->error('信息修改失败');
        }
        return view();
    }
}
