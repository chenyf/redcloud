<?php
namespace Common\Model\Message;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;
use Common\Lib\SimpleValidator;
use Common\Common\ServiceKernel;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Common\Component\OAuthClient\OAuthClientFactory;
use  Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Common\Model\User\UserSerializeModel as serialize;

class MsgServiceModel extends BaseModel{
  
    /**
     * 根据条件查询消息任务数
     * @param array $conditions
     * @return type
     */
    
    public function searchMsgCount(array $conditions){
        return $this->getMessageDao()->searchMsgCount($conditions);
    }
    
    public function analysisTaskDataByTime($startTime,$endTime){
        return $this->getMessageDao()->analysisTaskDataByTime($startTime,$endTime);
    }
     public function searchMessage($conditions, $orderBy, $start, $limit){
        return $this->getMessageDao()->searchMessage($conditions, $orderBy, $start, $limit);
    }
    
    public function findClassNumByClassId($classIds){
        return $this->getMessageDao()->findClassNumByClassId($classIds);
    }
    
     public function findTeacherNumByTeacherId($teacherIds){
        return $this->getMessageDao()->findTeacherNumByTeacherId($teacherIds);
    }
      public function findCopyNumByCId($copyIds){
        return $this->getMessageDao()->findCopyNumByCId($copyIds);
    }
    
    public function searchMsgTask($map){
        return $this->getMessageDao()->searchMsgTask($map);
    }
    public function addMsgTask($data){
        return $this->getMessageDao()->addMsgTask($data);
    }
    public function modifyMsgTaskInfoById($task_id,$data){
        return $this->getMessageDao()->modifyMsgTaskInfoById($task_id,$data);
    }
     public function searchMessageById($id){
        return $this->getMessageDao()->searchMessageById($id); 
     }
     private function getMessageDao(){
        return $this->createDao("Message.Msg");
    }
    
    
}


