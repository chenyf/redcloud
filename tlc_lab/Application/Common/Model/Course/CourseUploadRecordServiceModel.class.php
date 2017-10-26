<?php

namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;

class CourseUploadRecordServiceModel extends BaseModel
{

    public function getDao() {
        return $this->createService('Course.CourseUploadRecordModel');
    }

    private function getCourseService(){
        return createService('Course.CourseServiceModel');
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
        $records = $this->getDao()->findAll();
        foreach ($records as $key => $record){
            $records[$key]["course"] = $this->getCourseService()->getCourse($record["courseId"]);
        }
        return $records;
    }

    public function search($condition,$limit,$offset){
        return $this->getDao()->search($condition,$limit,$offset);
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