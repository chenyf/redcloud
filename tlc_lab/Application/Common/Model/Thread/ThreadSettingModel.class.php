<?php

namespace Common\Model\Thread;

use Common\Model\Common\BaseModel;

class ThreadSettingModel extends BaseModel {

    protected $tableName = 'course_thread_setting';

    public function addSetting($fields) {
        $option = array(
            'courseId' => 0,
            'isAllowPost' => 0,
            'isNeedPost' => 0,
            'isGrabMode' => 0,
            'maxNumber' => 0,
            'maxTime' => 0
        );
        $fields = array_merge($option, $fields);
        return $this->add($fields);
    }

    public function updSeeting($courseId, $fields) {
        $option = array(
            'isAllowPost' => 0,
            'isNeedPost' => 0,
            'isGrabMode' => 0,
            'maxNumber' => 0,
            'maxTime' => 0
        );
        $fields = array_merge($option, $fields);
        return $this->where("courseId = {$courseId}")->save($fields);
    }

    /*
     * 获取课程问答设置
     */

    public function getCourseThreadSetting($courseId) {
        return $this->where("courseId = {$courseId}")->find();
    }

}