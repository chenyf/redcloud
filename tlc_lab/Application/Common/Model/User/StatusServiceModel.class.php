<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class StatusServiceModel extends BaseModel{
    
    private function getStatusDao(){
        return $this->createDao("User.Status");
    }
    
    public function publishStatus($status,$deleteOld=true){
        if(!isset($status["userId"])) {
            $user = $this->getCurrentUser(); //
            if($user->id==0){
                return ;
            }
            $status['userId'] = $user->id;
        } 
        $status['createdTime'] = time();
        $status['message'] = empty($status['message']) ? '' : $status['message'];
        if($deleteOld){
            $this->deleteOldStatus($status);
        }
        return $this->getStatusDao()->addStatus($status);
    }
    
    private function  deleteOldStatus($status){
        if(!empty($status['userId']) && !empty($status['type']) && !empty($status['objectType']) && !empty($status['objectId'])){
            return $this->getStatusDao()->deleteStatusesByUserIdAndTypeAndObject($status['userId'], $status['type'], $status['objectType'], $status['objectId']);
        }
    }

    public function findStatusesByUserIds($userIds, $start, $limit){
        return $this->getStatusDao()->findStatusesByUserIds($userIds, $start, $limit);
    }

    public function searchStatuses($conditions, $sort, $start, $limit){
        return $this->getStatusDao()->searchStatuses($conditions, $sort, $start, $limit);
    }
    
}
?>
