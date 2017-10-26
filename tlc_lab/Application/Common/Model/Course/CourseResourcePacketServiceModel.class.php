<?php

namespace Common\Model\Course;

use Think\Model;
use Common\Model\Common\BaseModel;


class CourseResourcePacketServiceModel extends BaseModel
{

    public function getDao() {
        return $this->createService('Course.CourseResourcePacketModel');
    }

    public function getById($id){
        return $this->getDao()->getById($id);
    }

    public function getByCourseId($courseId){
        return $this->getDao()->getByCourseId($courseId);
    }

    public function selectByUserId($userId){
        return $this->getDao()->selectByUserId($userId);
    }

    public function selectByCourseId($courseId){
        return $this->getDao()->selectByCourseId($courseId);
    }

    public function findAll(){
        $packets = $this->getDao()->findAll();
        return $packets;
    }

    public function addPacket($packet){
        return $this->getDao()->addPacket($packet);
    }

    public function update($id,$packet){
        return $this->getDao()->update($id,$packet);
    }

    public function delete($id){
        return $this->getDao()->delete($id);
    }

}