<?php
namespace Common\Model\Activity;
use Common\Model\Common\BaseModel;
class TeacherTaskRewardModel extends BaseModel
{
    protected $tableName = 'teacher_task_reward';

    public function getTaskRewardRecord($map)
    {
        return $this->where($map)->select();
    }
    
    
    public function addTeacherTaskReward($data){
        return $this->add($data);
    }

}