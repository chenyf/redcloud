<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Lib\ArrayToolkit;
use Common\Model\Common\BaseModel;

class FavoriteModel extends BaseModel{
    
    protected $draftTable = 'course_draft';
    protected $tableName = 'edit_draft';
    private $serializeFields = array(
        'data' => 'json',
    );

    public function getDraft($id){
        $draft = M($this->draftTable)->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->draftTable} WHERE id = ? LIMIT 1";
//        $draft = $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
        return $draft ? $this->createSerializer()->unserialize($draft, $this->serializeFields) : null;
    }

     public function getEditDraft($id){
         $draft = $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->TableName} WHERE id = ? LIMIT 1";
//        $draft = $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
        return $draft ? $this->createSerializer()->unserialize($draft, $this->serializeFields) : null;
    }

    public function getDrafts($courseId,$userId){
        return  M($this->draftTable)->where("courseId  = {$courseId} and userId = {$userId}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->draftTable} WHERE courseId = ? AND userId = ?";
//        return $this->getConnection()->fetchAssoc($sql, array($courseId,$userId)) ? : null;
    }

    public function getEditDrafts($courseId,$userId,$lessonId){
        return  $this->where("courseId  = {$courseId} and userId = {$userId} and lessonId = {$lessonId}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->TableName} WHERE courseId = ? AND userId = ? And lessonId = ?";
//        return $this->getConnection()->fetchAssoc($sql, array($courseId,$userId,$lessonId)) ? : null;
    }

    // public function findDraftsByCourseId($courseId,$userId)
    // {
    //     $sql = "SELECT * FROM {$this->draftTable} WHERE courseId = ? AND userId = ? ";
    //     return $this->getConnection()->fetchAll($sql, array($courseId,$userId));
    // }

    public function addDraft($draft){
        $draft = $this->createSerializer()->serialize($draft, $this->serializeFields);
        $r = M($this->draftTable)->add($draft);
        if(!$r) E("Insert draft error.");
        return $this->getDraft($r);
//        $affected = $this->getConnection()->insert($this->draftTable, $draft);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert draft error.');
//        }
//        return $this->getDraft($this->getConnection()->lastInsertId());
    }

    public function addEditDraft($draft){
        $draft = $this->createSerializer()->serialize($draft, $this->serializeFields);
        $r = $this->add($draft);
        if(!$r) E("Insert draft error.");
        return $this->getEditDraft($r);
//        $affected = $this->getConnection()->insert($this->TableName, $draft);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert draft error.');
//        }
//        return $this->getEditDraft($this->getConnection()->lastInsertId());
    }

    public function updateDraft($userId,$courseId, $fields){
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        M($this->draftTable)->where("courseId  = {$courseId} and userId = {$userId}")->save($fields);
//       $this->getConnection()->update($this->draftTable, $fields, array('courseId' => $courseId,'userId' => $userId));
       return $this->getDrafts($courseId,$userId);
    }

    public function updateEditDraft($userId,$courseId,$lessonId,$fields){
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $this->where("courseId  = {$courseId} and userId = {$userId} and lessonId = {$lessonId}")->save($fields);
//        $this->getConnection()->update($this->TableName, $fields, array('courseId' => $courseId,'userId' => $userId,'lessonId' => $lessonId));
        return $this->getEditDrafts($courseId,$userId,$lessonId);
    }

    public function deleteDraftByCourseIdAndUserId($courseId,$userId){
        return M($this->draftTable)->where("courseId  = {$courseId} and userId = {$userId}")->delete();
//        $sql = "DELETE FROM {$this->draftTable} WHERE courseId = ? AND userId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($courseId,$userId));
    }

    public function deleteDraftByCourseIdAndUserIdAndLessonId($courseId,$userId,$lessonId){
        return $this->where("courseId  = {$courseId} and userId = {$userId} and lessonId = {$lessonId}")->delete();
//        $sql = "DELETE FROM {$this->TableName} WHERE courseId = ? AND userId = ? AND lessonId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($courseId,$userId,$lessonId));
    }
    
}
?>
