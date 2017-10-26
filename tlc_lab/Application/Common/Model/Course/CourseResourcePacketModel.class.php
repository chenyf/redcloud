<?php

namespace Common\Model\Course;

use Think\Model;
use Common\Model\Common\BaseModel;


class CourseResourcePacketModel extends BaseModel
{

    protected $tableName = "resource_packet";

    public function getById($id){
        return $this->where(['id' => $id])->find() ?: null;
    }

    public function getByCourseId($courseId){
        return $this->where(['courseId' => $courseId,'deleted' => 0])->find() ?: null;
    }

    public function selectByUserId($userId){
        return $this->where(['userId' => $userId])->select() ?: array();
    }

    public function selectByCourseId($courseId){
        return $this->where(['courseId' => $courseId])->select() ?: array();
    }

    public function findAll(){
        return $this->select() ?: array();
    }

    public function addPacket($packet){
        $r = $this->add($packet);
        if(!$r) E("Insert course resource packet error.");
        return $this->getById($r);
    }

    public function update($id,$packet){
        return $this->where(array('id'=>$id))->save($packet);
    }

    public function delete($id){
        return $this->where(array('id'=>$id))->delete();
    }

}