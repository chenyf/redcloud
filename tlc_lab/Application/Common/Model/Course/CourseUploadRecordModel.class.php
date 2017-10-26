<?php

namespace Common\Model\Course;
use Common\Traits\DaoModelTrait;
use Think\Model;
use Common\Model\Common\BaseModel;

class CourseUploadRecordModel extends BaseModel
{
    use DaoModelTrait;

    protected $tableName = "upload_course_record";

    public function getById($id){
        return $this->where(['id' => $id])->find() ?: null;
    }

    public function getByCourseId($courseId){
        return $this->where(['courseId' => $courseId])->find() ?: null;
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

    public function search($condition,$limit,$offset){
        $fields = [];
        if(isset($condition["userId"])){
            $fields["userId"] = $condition["userId"];
        }
        if(isset($condition["courseId"])){
            $fields["courseId"] = $condition["courseId"];
        }

        if($limit > 0 && $offset >= 0){
            return $this->where($fields)->limit($offset,$limit)->select() ?: array();
        }

        return $this->where($fields)->limit($offset,$limit)->select() ?: array();
    }

    public function addRecord($record){
        $r = $this->add($record);
        if(!$r) E("Insert course upload record error.");
        return $this->getById($r);
    }

    public function update($id,$record){
        return $this->where(array('id'=>$id))->save($record);
    }

    public function delete($id){
        return $this->where(array('id'=>$id))->delete();
    }


}