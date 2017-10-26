<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class TmpStudInfoModel extends BaseModel{
    
    protected $tableName = 'tmp_stud_info';

    public function getTmpStudInfo($id){
        return $this->where("id = {$id}")->find() ? : null;
    }

    public function addTmpStudInfo($parm){
        $where['studName'] = $parm['studName'];
        $where['studNum'] = $parm['studNum'];
        $r = $this->switchCenterDB()->add($where);
        if($r) synchroData(array('db'=>'private','sql'=>$this->getLastSql(),'pk'=> $this->getAutoIncField(),'id'=>$r));
        return  $this->getTmpStudInfo($r);
    }
    
    public function saveTmpStudInfo($parm){
        $data['id'] =  $parm['id'];
        $where['studName'] = $parm['studName'];
        $where['studNum'] = $parm['studNum'];
        $r = $this->switchCenterDB()->where($data)->save($where);
        if($r) synchroData(array('db'=>'private','sql'=>$this->getLastSql()));
        return $r ? $this->getTmpStudInfo($r) : null;
    }
    
    public function existTmpStudInfo($parm){
        $where['studName'] = $parm['studName'];
        $where['studNum'] = $parm['studNum'];
        return $this->where($where)->find() ? : null;
    }
    
    public function delTmpStudInfo($id){
        $this->where("id = {$id}")->delete();
//       return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }  
    
}
?>
