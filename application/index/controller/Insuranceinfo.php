<?php 
namespace app\index\controller;
use app\index\model\BInsuranceCompany as Insurancemodel;
use think\Db;
use think\Session;

class Insuranceinfo extends Base
{
	/*
	保险公司基本信息
	*/ 
	// 基本信息查看
	public  function index(){
		$id = Session::get('admin_id');
		$list = Db::table('b_insurance_company')->select();
		$this->assign('list',$list);
		return view();
	}
	// 基本信息修改
	public function update($id=""){
		// $id = Session::get('admin_id');
		$list = Db::table('b_insurance_company')->find($id);
		$this->assign('list',$list);
		return view();

	}
	// 基本信息修改提交
	public function update_sub(){
		// $id = Session::get('admin_id');
		$id = input('id');
		$name = $_POST['name'];
		$tel = $_POST['tel'];
		$address = $_POST['address'];
		// dump($id);die;
		$res = Db::table('b_insurance_company')
				->where(['id'=>$id])
				->update([
					'company_name'    =>    $name,
					'company_tel'     =>    $tel,
					'company_address' =>    $address
				]);
		if($res){
			$this->success('信息修改成功！','index');
		}else{
			$this->error('信息修改失败！','index');
		}
	}
	
	// 基本信息删除
	public function delete(){
		// $id = Session::get('admin_id');
		$id = input('id');
		$res = Db::table('b_insurance_company')->where(['id'=>$id])->delete();
		if($res){
			$this->success('信息删除成功！','index');
		}else{
			$this->error('信息删除失败！','index');
		}
	}
	// 增加跳转
	public function add(){
		$id = Session::get('admin_id');
		return view();
	}
	// 增加
	public function insert(){
		// $id = Session::get('admin_id');
		$user = new Insurancemodel;
		$name = $_POST['name'];
		$tel = $_POST['tel'];
		$address = $_POST['address'];
		$time = date("Y-m-d H:i:s");
		$res = $user->save([
				'company_name'    =>    $name,
				'company_tel'     =>    $tel,
				'company_address' =>    $address,
				'create_time'     =>    $time
			]);
		if($res){
			$this->success('信息增加成功！','index');
		}else{
			$this->error('信息增加失败！','index');
		}
	}
	// 保险公司搜索
	public function search(){
		$search = input('search');
		$res['company_name'] = array('like','%'.$search.'%');
		$list = Db::table('b_insurance_company')->where($res)->select();
		// dump($list);die;
		$this->assign('list',$list);
		if($list == null){
			$this->success('关键词无匹配！','index');
		}else{
			return view();
		}
	}
}
