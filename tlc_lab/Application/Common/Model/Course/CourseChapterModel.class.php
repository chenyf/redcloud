<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;

class CourseChapterModel extends \Common\Model\Common\BaseModel{
    
    protected $tableName = 'course_chapter';

    public function getChapter($id){
       
        return $this->where(" id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function addChapter(array $chapter){
        $r = $this->add($chapter);
        if(!$r) E('Insert course chapter error.');
        return $this->getChapter($r);
//        $affected = $this->getConnection()->insert($this->tableName, $chapter);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert course chapter error.');
//        }
//        return $this->getChapter($this->getConnection()->lastInsertId());
    }

    public function findChaptersByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->order("createdTime asc")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE courseId = ? ORDER BY createdTime ASC";
//        return $this->getConnection()->fetchAll($sql, array($courseId));
    }

    public function findChaptersByChapterId($chapterId){
        return $this->where("parentId = {$chapterId}")->order("createdTime asc")->select();
    }

    public function getChapterCountByCourseIdAndType($courseId, $type){
         return $this->where("courseId = {$courseId} and type = '{$type}'")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  courseId = ? AND type = ?";
//        return $this->getConnection()->fetchColumn($sql, array($courseId, $type));
    }

    public function getChapterCountByChapterIdAndType($chapterId,$type){
        return $this->where("parentId = {$chapterId} and type = '{$type}'")->count();
    }

    public function getChapterCountByCourseIdAndTypeAndParentId($courseId, $type, $parentId){
        return $this->where("courseId = {$courseId} and type = '{$type}' and parentId ={$parentId}")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  courseId = ? AND type = ? AND parentId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($courseId, $type, $parentId));
    }

    public function getLastChapterByCourseIdAndType($courseId, $type){
        return $this->where("courseId = {$courseId} and type = '{$type}'")->order("seq desc")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE  courseId = ? AND type = ? ORDER BY seq DESC LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($courseId, $type)) ? : null;
    }

    public function getLastChapterByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->order("seq desc")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE  courseId = ? ORDER BY seq DESC LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($courseId)) ? : null;
    }

    public function getChapterMaxSeqByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->max('seq');
//        $sql = "SELECT MAX(seq) FROM {$this->tableName} WHERE  courseId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($courseId));
    }

    public function updateChapter($id, array $chapter){
        $this->where("id ={$id}")->save($chapter);
//        $this->getConnection()->update($this->tableName, $chapter, array('id' => $id));
        return $this->getChapter($id);
    }

    public function deleteChapter($id){
        return $this->where("id = {$id}")->delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function deleteChaptersByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE courseId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($courseId));
    }
    public function accordingSeqGetChapter($map){
                //print_r($map);
        return $this->where($map)->select();
    }
}
?>
