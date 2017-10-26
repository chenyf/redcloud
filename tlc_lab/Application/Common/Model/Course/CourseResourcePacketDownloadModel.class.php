<?php

namespace Common\Model\Course;

use Think\Model;
use Common\Model\Common\BaseModel;

class CourseResourcePacketDownloadModel extends BaseModel
{

    protected $tableName = "resource_packet_download";

    public function getById($id){
        return $this->where(['id' => $id])->find() ?: null;
    }

    public function selectByUserId($userId){
        return $this->where(['userId' => $userId])->select() ?: array();
    }

    public function selectByPacketId($packetId){
        return $this->where(['packetId' => $packetId])->select() ?: array();
    }

    public function findAll(){
        return $this->select() ?: array();
    }

    public function addRecord($record){
        $r = $this->add($record);
        if(!$r) E("Insert resource packet download record error.");
        return $this->getById($r);
    }

    public function update($id,$record){
        return $this->where(array('id'=>$id))->save($record);
    }

    public function delete($id){
        return $this->where(array('id'=>$id))->delete();
    }


}