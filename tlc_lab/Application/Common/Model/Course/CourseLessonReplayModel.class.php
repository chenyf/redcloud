<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;

class CourseLessonReplayModel extends \Common\Model\Common\BaseModel{
    
    protected $tableName = "course_lesson_replay";
      
    public function addCourseLessonReplay($courseLessonReplay){
        $r = $this->add($courseLessonReplay);
        if(!$r) E("Insert course_lesson_replay error.");
        return $this->getCourseLessonReplay($r);
//        $affected = $this->getConnection()->insert(self::TABLENAME, $courseLessonReplay);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert course_lesson_replay error.');
//        }
//        return $this->getCourseLessonReplay($this->getConnection()->lastInsertId());
    }

    public function getCourseLessonReplay($id){
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->getTablename()} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function deleteLessonReplayByLessonId($lessonId){
        return $this->where("lessonId = {$lessonId}")->delete();
//        return $this->getConnection()->delete(self::TABLENAME, array('lessonId' => $lessonId));
    }

    public function getCourseLessonReplayByLessonId($lessonId){
        return $this->where("lessonId = {$lessonId}")->order(" replayId asc")->select();
//        $sql ="SELECT * FROM {$this->getTablename()} WHERE lessonId = ? ORDER BY replayId ASC";
//        return $this->getConnection()->fetchAll($sql, array($lessonId));
    }

    public function deleteLessonReplayByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->delete();
//        return $this->getConnection()->delete(self::TABLENAME, array('courseId' => $courseId));
    }
}
?>
