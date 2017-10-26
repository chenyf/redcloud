<?php

namespace Common\Model\User;


use Common\Lib\ApiService;
use Think\Model;
use Common\Model\Common\BaseModel;

class CollegeServiceIfaceModel extends BaseModel
{

    public function getTeacherCollegeList(){
        $result = ApiService::postData(C('api_url.get_teacher_college_list'),[]);
        $list = is_object($result->list) ? get_object_vars($result->list) : $result->list;
        return is_array($list) ? $list : [];
    }

    public function getStudentCollegeList(){
        $result = ApiService::postData(C('api_url.get_student_college_list'),[]);
        $list = is_object($result->list) ? get_object_vars($result->list) : $result->list;
        return is_array($list) ? $list : [];
    }

    public function getDepartmentList($collegeId){
        $result = ApiService::postData(C('api_url.get_department_by_collegeid'),array(
            'collegeId' =>  $collegeId
        ));
        $list = is_object($result->list) ? get_object_vars($result->list) : $result->list;
        return is_array($list) ? $list : [];
    }

    public function getMajorList($collegeId){
        $result = ApiService::postData(C('api_url.get_major_by_collegeid'),array(
            'collegeId' =>  $collegeId
        ));
        $list = is_object($result->list) ? get_object_vars($result->list) : $result->list;
        return is_array($list) ? $list : [];
    }

}