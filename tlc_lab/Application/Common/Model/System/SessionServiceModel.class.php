<?php
namespace Common\Model\System;
use Think\Model;

class SessionServiceModel extends \Common\Model\Common\BaseModel{
    
    public function get($id){
        return $this->getStatisticsDao()->get($id);
    }

    public function clear($id){
        return $this->getStatisticsDao()->delete($id);
    }

    public function clearByUserId($userId){
        return $this->getStatisticsDao()->deleteSessionByUserId($userId);
    }
    
    protected function getStatisticsDao (){
        return $this->createDao("System.Statistics");
    }
}
?>
