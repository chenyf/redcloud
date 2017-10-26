<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class UserBindModel extends BaseModel{
    
    protected $tableName = 'user_bind';

    public function getBind($id){
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id));
    }

    public function getBindByFromId($fromId){
        return $this->where("fromId = {$fromId}")->find() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE fromId = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($fromId)) ? : array();
    }

    public function getBindByTypeAndFromId($type, $fromId){
        return $this->where("fromId = {$fromId} and type ='{$type}'")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE type = ? AND fromId = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($type, $fromId));
    }

    public function getBindByToIdAndType($type, $toId){
        return $this->where("toId = {$toId} and type ='{$type}'")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE type = ? AND toId = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($type, $toId));
    }

    public function getBindByToken($token){
         return $this->where("token ='{$token}'")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE token = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($token));
    }
    
    public function addBind($bind){
        $r = $this->add($bind);
        if(!$r) E("Insert bind error.");
        return  $this->getBind($r);
//        $affected = $this->getConnection()->insert($this->tableName, $bind);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert bind error.');
//        }
//        return $this->getBind($this->getConnection()->lastInsertId());
    }

    public function deleteBind($id){
        return $this->where("id = {$id}")->delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function findBindsByToId($toId){
        return $this->where("toId = {$toId}")->order("createdTime DESC")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE toId = ? ORDER BY createdTime DESC";
//        return $this->getConnection()->fetchAll($sql, array($toId));
    }

    
}
?>
