<?php

namespace Common\Model\Thread;

use Common\Model\Common\BaseModel;

class ThreadTeacherModel extends BaseModel {

    protected $tableName = 'course_thread_teacher';

    public function getTeacher($userId) {
        return $this->where("userId = {$userId}")->select();
    }

    public function getTeacherCourseId($userId) {
        $where['userId'] = $userId;
        return $this->where($where)->field('courseId')->select();
    }

    public function addThreadTeacher($fields) {
        $res = $this->add($fields);
        if (!$res)
            E("Insert course threadTeacher error.");
        return $res;
    }

    public function updateThreadTeacher($id, $fields) {
        return $this->where("id = {$id}")->save($fields);
    }

    public function deleteThreadTeacher($id) {
        return $this->where("id = {$id}")->delete();
    }

    public function searchTeacherCount($conditions) {
        return $this->where($conditions)->count();
    }

    public function searchTeacher($conditions, $orderBys, $start, $limi) {
        $this->filterStartLimit($start, $limit);
        return $this->where($conditions)
                        ->order("$orderBys[0] $orderBys[1]")
                        ->limit($start, $limi)
                        ->select();
    }

    #根据课程ID和老师ID 获取答疑老师信息

    public function findAnswerTeacher($conditions) {
        $option = array(
            'courseId' => 0,
            'userId' => 0
        );
        $conditions = array_merge($option, $conditions);
        return $this->where($conditions)->find();
    }

    /*
     * 判断是否是本课程答疑老师
     */

    public function isThreadTeacher($courseId, $userId) {
        return $this->where("courseId = {$courseId} and userId = {$userId}")->find();
    }

}