<?php 
namespace app\index\controller;

use think\Db;
use think\Session;

class Cominformation extends Base
{
	/*
	
	企业基本信息

	 */ 
	// 基本信息查看
	public function index(){
		// 登录人id
        $id = Session::get('admin_id');
        $res = Db::table('t_business_user')->where(['id'=>$id])->find();
		// 企业信息
        $org = Db::table('t_business_info')->where(['id'=>$res['org_id']])->find();
        $this->assign('org',$org);
        // dump($org);die;

        return $this->fetch();
	}
	// 基本信息更新
	public function update(){
		$id = Session::get('admin_id');
		 $res = Db::table('t_business_user')->where(['id'=>$id])->find();
		// 企业信息
        $org = Db::table('t_business_info')->where(['id'=>$res['org_id']])->find();
        $this->assign('org',$org);
        // dump($org);die;

        return view();
	}
	// 基本信息更新提交
	public function update_sub(){
		$id = Session::get('admin_id');
		$org_name = $_POST['org_name'];
		$business_address = $_POST['business_address'];
		$business_email = $_POST['business_email'];
		$content = $_POST['content'];
		$legal_person = $_POST['legal_person'];
		$birthday = $_POST['birthday'];
		$legal_person_phone = $_POST['legal_person_phone'];
		$time_limit2 = $_POST['time_limit2'];
		$safety_gear_person_mobile = $_POST['safety_gear_person_mobile'];
		$res = Db::table('t_business_user')->where(['id'=>$id])->find();
		$org = Db::table('t_business_info')->where(['id'=>$res['org_id']])->update([
				'org_name'          =>   $org_name,
				'business_address'  =>   $business_address,
				'business_email'    =>   $business_email,
				'manage_content'    =>   $content,
				'legal_person'      =>   $legal_person,
				'birthday'          =>   $birthday,
				'legal_person_phone'=>   $legal_person_phone,
				'time_limit2'       =>   $time_limit2,
				'safety_gear_person_mobile'=>$safety_gear_person_mobile
			]);
		if($org){
			 $this->success('信息修改成功','index');
        }else{
            $this->error('信息修改失败');
        }
		return view();
	}
}
