<?php
namespace Common\Model\Course;
use Think\Model;

class LessonVieweModel extends \Common\Model\Common\BaseModel{
    
    protected $tableName = 'course_lesson_viewed';

    public function deleteViewedsByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->order("createdTime DESC")->select();
//    	$sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? ORDER BY createdTime DESC";
//        return $this->getConnection()->fetchAll($sql, array($courseId));
    }
}
?>
