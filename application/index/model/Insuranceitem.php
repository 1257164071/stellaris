<?php
/**
 * Created by PhpStorm.
 * User: nightdays
 * Date: 18-3-20
 * Time: 下午16:14
 */

namespace app\index\model;

use think\Model;

class Insuranceitem extends Model{
	protected $table = 'b_insurance_item';
	public function saveitem($data){
		 return  $this::update(['insurance_name'=>$data['insurance_name'],'min_pnum'=>$data['min_pnum'],'max_pnum'=>$data['max_pnum'],'insurance_type'=>$data['insurance_type'],'insurance_quota'=>$data['insurance_quota'],'insurance_baseamt'=>$data['insurance_baseamt'],'insurance_price'=>$data['insurance_price'],'insurance_raiseprice'=>$data['insurance_raiseprice'],'id'=>$data['id']]);
	}
	public function delone($id){
           if($this::destroy($id)){
            return 1;
           }else{
            return 0;
           }

    }
}