<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;

class CourseDraftModel extends \Common\Model\Common\BaseModel{
    
    protected $draftTable = 'course_draft';

    public function getCourseDraft($id){
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->draftTable} WHERE id = ? LIMIT 1";
//        return  $draft = $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function findCourseDraft($courseId,$lessonId, $userId){
        return $this->where("courseId = {$courseId} and lessonId = {$lessonId} and userId = {$userId}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->draftTable} WHERE courseId = ? AND lessonId = ? AND userId = ?";
//        return $this->getConnection()->fetchAssoc($sql, array($courseId,$lessonId, $userId)) ? : null;
    }

    public function addCourseDraft($draft){
     
        $r = $this->add($draft);
		 if(!$r) E("Insert draft error.");
//        return $this->getCourseDraft($r);
//        $affected = $this->getConnection()->insert($this->draftTable, $draft);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert draft error.');
//        }
       return $this->getCourseDraft($r);
    }

    public function updateCourseDraft($courseId,$lessonId, $userId,$fields){
        $this->where("courseId = {$courseId} and lessonId = {$lessonId} and userId = {$userId}")->save($fields);
//        $this->getConnection()->update($this->draftTable, $fields, array('courseId' => $courseId,'lessonId' => $lessonId,'userId' => $userId));
        return $this->findCourseDraft($courseId,$lessonId, $userId);
    }

    public function deleteCourseDrafts($courseId,$lessonId, $userId){
        return $this->where("courseId = {$courseId} and lessonId = {$lessonId} and userId = {$userId}")->delete();
//        $sql = "DELETE FROM {$this->draftTable} WHERE courseId = ? AND lessonId = ? AND userId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($courseId,$lessonId, $userId));
    }
}
?>
