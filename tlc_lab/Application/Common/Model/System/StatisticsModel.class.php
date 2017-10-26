<?php
namespace Common\Model\System;
use Think\Model;

class StatisticsModel extends \Common\Model\Common\BaseModel{
    
    protected $tableName = "session2";
    
    public function get($id){
      return $this->where("session_id = '{$id}'")->find() ? : null;
    }
        
    public function getSessionByUserId($userId){
        return $this->where("user_id = {$userId}")->find() ? : null;
    }
    
    public function delete($id){
        return $this->where("session_id = {$id}")->delete();
    }
    
    public function deleteSessionByUserId($userId){
        return $this->where("user_id = {$userId}")->delete();
    }
    
    public function getOnlineCount($retentionTime){
        $minTm = time() - $retentionTime;
        $maxTm = time();
        return $this->where(" session_time>={$minTm} and session_time <={$maxTm} ")->count() ? : 0;
    }
    
    public function getLoginCount($retentionTime){
        $minTm = time() - $retentionTime;
        $maxTm = time();
        return $this->where(" session_time>={$minTm} and session_time <={$maxTm} and user_id>0")->count() ? : 0;
    }   
}
