<?php

namespace Common\Model\Course;

use Think\Model;
use Common\Model\Common\BaseModel;

class CourseResourcePacketDownloadServiceModel extends BaseModel
{

    public function getDao() {
        return $this->createService('Course.CourseResourcePacketDownloadModel');
    }

    public function getById($id){
        return $this->getDao()->getById($id);
    }

    public function selectByUserId($userId){
        return $this->getDao()->selectByUserId($userId);
    }

    public function selectByPacketId($packetId){
        return $this->getDao()->selectByPacketId($packetId);
    }

    public function findAll(){
        $records = $this->getDao()->findAll();
        return $records;
    }

    public function addRecord($record){
        return $this->getDao()->addRecord($record);
    }

    public function update($id,$record){
        return $this->getDao()->update($id,$record);
    }

    public function delete($id){
        return $this->getDao()->delete($id);
    }

}