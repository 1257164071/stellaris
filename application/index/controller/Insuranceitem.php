<?php
/**
 * Created by PhpStorm.
 * User: nightdays
 * Date: 18-3-20
 * Time: 下午13:27
 */

namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use app\index\model\Insuranceitem as InsuranceitemModel;
class Insuranceitem extends Base
{
	//保险管理列表
    public function index()
    {
        //读取数据库数据
        $res = Db::query('select * from b_insurance_item order by id asc');
        $this->assign('iteminfo',$res);
        return $this->fetch();
    }
     public function add()
    {
        //获取前台数据
        if(request()->isPost()){
           $itemdata=input('post.');
           $itemdata['insurance_code'] = date('YmdHis').rand(10000000,99999999);
           //实例化模型
            $data=InsuranceitemModel::create($itemdata);
            if($data){
            	 $this->success("添加保险类型信息成功！",url('index'));
            }else{
                 $this->error("添加保险类型信息失败！");
            }

        }
        $additem=new InsuranceitemModel();
        $types="select * from b_insurance_buytype";
        $typedata=$additem->query($types);
        $this->assign('typeseinfo',$typedata);

        return $this->fetch();
    }
     public function edit($id){
     	//获取id
     	$itemone=db('b_insurance_item')->find($id);
     	$edititem=new InsuranceitemModel();
     	$types="select * from b_insurance_buytype";
     	$typedata=$edititem->query($types);
     	if(request()->isPost()){
            $data=input('post.');
            $num=$edititem->saveitem($data);
            if($num!==false){
            $this->success('编辑保险类别信息成功',url('index'));
            }else{
            $this->error('编辑保险类别信息失败！');
            }
            return;
        }
        //分配模板变量
        $this->assign('itemoneinfo',$itemone);
        $this->assign('typeseinfo',$typedata);
     	return $this->fetch();
     }
     //删除单条数据
     public function del($id){
     	$delitem=new InsuranceitemModel();
        $num=$delitem->delone($id);
        if($num){
            $this->success('成功删除该保险记录',url('index'));
        }else{
            $this->error('删除失败');
        }

     }
     //批量删除多条数据
     public function delmore() {
     	$id=input('post.');//获取所有传过来的id
        $result = count ($id);
        if($result==1){
            $this->error('抱歉！您没有选中可删数据');
     	}else{
        foreach($data as $k=>$v){//遍历数组中的值
            $del=Db::name('b_insurance_item')->delete($v);
			}
        }
        $this->redirect(url('Insuranceitem/index'));
      }

}