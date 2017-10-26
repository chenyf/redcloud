<?php
namespace Common\Model\System;
use Think\Model;

class StatisticsServiceModel extends \Common\Model\Common\BaseModel{
    
    public function getOnlineCount($retentionTime){
        return $this->getStatisticsDao()->getOnlineCount($retentionTime);
    }
	
    public function getloginCount($retentionTime){
        return $this->getStatisticsDao()->getLoginCount($retentionTime);	
    }
    
    protected function getStatisticsDao (){
//        return $this->createDao("System.Statistics");
        return $this->createService('System.StatisticsModel');
    }
    
}
?>
