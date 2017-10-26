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
class TeacherInfoModel extends BaseModel
{

    protected $tableName = 'teacher_info';
    private $serializeFields = array(
        'contacts' => 'json',
        'intros' => 'json',
        'teaches' => 'json',
        'researches' => 'json',
        'publications' => 'json',
    );

    public function getTeacherInfo($id){
        $teacher_info = $this->where("id = {$id}")->find();
        return $teacher_info ? $this->createSerializer()->unserialize($teacher_info, $this->serializeFields) : null;
    }

    public function getTeacherInfoByTeacherId($teacher_id){
        $teacher_info = $this->where("teacher_id = {$teacher_id}")->find();
        return $teacher_info ? $this->createSerializer()->unserialize($teacher_info, $this->serializeFields) : null;
    }

    //添加教师信息记录
    public function addTeacherInfo($fields){
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $r = $this->add($fields);
        if(!$r) E("Insert status error.");
        return  $this->getTeacherInfo($r);
    }

    //更新教师信息记录
    public function updateTeacherInfo($teacher_id,$fields){
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $this->where("teacher_id = {$teacher_id}")->save($fields);
        return $this->getTeacherInfoByTeacherId($teacher_id);
    }

}