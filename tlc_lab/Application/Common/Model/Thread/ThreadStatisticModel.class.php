<?php

namespace Common\Model\Thread;

use Common\Model\Common\BaseModel;

class ThreadStatisticModel extends BaseModel {

    protected $tableName = 'course_thread_statistic';

    public function getStatistic($courseId) {
        return $this->where("courseId = {$courseId}")->select();
    }

    public function getStatisticByDateTime($where) {
        return $this->where($where)->find();
    }

    public function updateStatistic($where, $fields) {
        return $this->where($where)->setInc($fields);
    }

    public function insertStatistic($fields) {
        return $this->add($fields);
    }

}