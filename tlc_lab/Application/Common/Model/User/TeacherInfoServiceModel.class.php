<?php
/**
 * Created by PhpStorm.
 * User: sike
 * Date: 2016/8/8
 * Time: 17:43
 */

namespace Common\Model\User;

use Think\Model;
use Common\Model\Common\BaseModel;
class TeacherInfoServiceModel extends BaseModel
{

    public function getTeacherInfoDao(){
        return $this->createDao("User.TeacherInfo");
    }

    public function getTeacherInfo($id){
        return $this->getTeacherInfoDao()->getTeacherInfo($id);
    }

    public function getTeacherInfoByTeacherId($teacher_id){
        return $this->getTeacherInfoDao()->getTeacherInfoByTeacherId($teacher_id);
    }

    //添加教师信息记录
    public function addTeacherInfo($fields){
        return $this->getTeacherInfoDao()->addTeacherInfo($fields);
    }

    //更新教师信息记录
    public function updateTeacherInfo($teacher_id,$fields){
        return $this->getTeacherInfoDao()->updateTeacherInfo($teacher_id,$fields);
    }

    //过滤取出的值
    public function filterTeacherInfo($teacherInfo){




    }

}