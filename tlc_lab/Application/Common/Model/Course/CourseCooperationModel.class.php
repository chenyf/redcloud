<?php

namespace Common\Model\Course;

use Common\Model\Common\BaseModel;

class CourseCooperationModel  extends BaseModel
{

    protected $tableName = 'course_cooperation';

    public function getCourseTeacherListByTeacherId($teacherId)
    {
        $list = $this->where(array('teacherId' => $teacherId))->select() ? : array();
        return $list;
    }

    public function getCourseTeacherListByCourseId($courseId)
    {
        $list = $this->where(array('courseId' => $courseId))->select() ? : array();
        return $list;
    }

    public function deleteByTeacherId($teacherId)
    {
        return $this->where(array('teacherId' => $teacherId))->delete();
    }

    public function deleteByCourseId($courseId)
    {
        return $this->where(array('courseId' => $courseId))->delete();
    }

    public function deleteByTeacherAndCourse($teacherId,$courseId){
        $map = array(
            'teacherId' =>  $teacherId,
            'courseId'  =>  $courseId
        );

        return $this->where($map)->delete();
    }


}