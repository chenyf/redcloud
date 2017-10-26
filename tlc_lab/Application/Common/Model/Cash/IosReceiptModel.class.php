<?php
/*
 * 
 * @package
 * @author     linhaowei
 * @支付失败时候添加数据
 * @Creattime  2016/1/4
 */
namespace Common\Model\Cash;

use Common\Model\Common\BaseModel;

class IosReceiptModel extends BaseModel{
    
     protected $tableName = 'ios_receipt';
     
     public function addOrderFailure($data){
   
       return $this->add($data);
     }
     
     public function getCount($data){
        return   $this->where("webCode='$data'")->count();
     }
     
     public function getReceiptInfo($id){
         return $this->where("id = $id")->find();
     }

          public function getReceiptList($data,$start,$end,$order){
         return $this->where("webCode='$data'") ->limit($start, $end)->order($order) ->select();
     }
}

?>
