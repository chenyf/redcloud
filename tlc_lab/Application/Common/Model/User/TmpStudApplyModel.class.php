<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class TmpStudApplyModel extends BaseModel{
    
    protected $tableName = 'tmp_stud_apply';

    public function getTmpStudApply($id){
        return $this->where("id = {$id}")->find() ? : array();
    }
    
    public function getAllTmpStudApply($status, $orderBy, $start, $limit){
        if($status == 1) 
        return $this->where("status = 1")->order('examineTime desc')->limit($start, $limit)->select() ? : array();
        if($status == 'all' || $status=='') 
        return $this->order('applyTime desc')->limit($start, $limit)->select() ? : array();
        if($status == 0) 
        return $this->where("status = 0")->order('applyTime desc')->limit($start, $limit)->select() ? : array();
        if($status == 2) 
        return $this->where("status = 2")->order('examineTime desc')->limit($start, $limit)->select() ? : array();
    }

    public function addTmpStudApply($fields){
        
        $fields          = ArrayToolkit::filter($fields, array(
			'studName'         => '',
			'studNum'          => '',
			'classId'          => '',
			'studRemark'       => '',
			'teachRemark'      => '',
			'pictrue'          => '',
			'studUid'          => '',
			'teachUid'         => 0,
                        'status'           => 0,
		));
        
        $fields['applyTime'] = time();
        
        $r = $this->switchCenterDB()->add($fields);
        if($r) synchroData(array('db'=>'private','sql'=>$this->getLastSql(),'pk'=> $this->getAutoIncField(),'id'=>$r));
        return  $this->getTmpStudApply($r) ? : array();
    }
    
    public function updateTmpStudApply($id,$fields){
        if($fields['teachUid']) $fields['examineTime'] = time();
        $r = $this->switchCenterDB()->where("id = {$id}")->save($fields);
        if($r) synchroData(array('db'=>'private','sql'=>$this->getLastSql()));
        return  $this->getTmpStudApply($id) ? : array();
    }
    
    public function getApplyBystudUid($studUid){
        $where['studUid'] = $studUid;
        $where['status']  = 0; 
        return  $this->where($where)->find() ? : array();
    }
    
    
    public function delTmpStudApply($uid){
       return $this->where("studUid = {$uid} and status = 0")->delete() ? : array();
    } 
    
    public function searchApplyStatusCount($status){
        if($status == 1) 
        return $this->where("status = 1")->count() ? : 0;
        if($status == 'all' || $status=='') 
        return $this->count() ? : 0;
        if($status == 0) 
        return $this->where("status = 0")->count() ? : 0;
        if($status == 2) 
        return $this->where("status = 2")->count() ? : 0;
    }
    
    
}
?>
