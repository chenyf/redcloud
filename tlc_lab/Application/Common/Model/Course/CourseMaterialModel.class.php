<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;

class CourseMaterialModel extends BaseModel{
    
    protected $tableName = 'course_material';
    
    public function getMaterial($id){
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function findMaterialsByCourseId($courseId, $start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->where("courseId = {$courseId}")->order("createdTime desc")->limit($start,$limit)->select() ? : array();
//        $sql ="SELECT * FROM {$this->tableName} WHERE courseId=? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($courseId)) ? : array();
    }

    public function findMaterialsByLessonId($lessonId, $start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->where("lessonId = {$lessonId}")->order("createdTime desc")->limit($start,$limit)->select() ? : array();
//        $sql ="SELECT * FROM {$this->tableName} WHERE lessonId=? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($lessonId)) ? : array();
    }

    public function getMaterialCountByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->count();
//        $sql ="SELECT COUNT(*) FROM {$this->tableName} WHERE courseId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($courseId));
    }

    public function addMaterial($material){
        $r = $this->add($material);
        if(!$r) E("Insert material error.");
        return  $this->getMaterial($r);
//        $affected = $this->getConnection()->insert($this->tableName, $material);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert material error.');
//        }
//        return $this->getMaterial($this->getConnection()->lastInsertId());
    }

    public function deleteMaterial($id){
        return $this->where("id = {$id}")->delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function deleteMaterialsByLessonId($lessonId){
         return $this->where("lessonId = {$lessonId}")->delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE lessonId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($lessonId));
    }

    public function deleteMaterialsByCourseId($courseId){
        return $this->where("courseId = {$courseId}")->delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE courseId = ?";
//        return $this->getConnection()->executeUpdate($sql, array($courseId));
    }
    
    public function getLessonMaterialCount($courseId,$lessonId){
        return $this->where("courseId = {$courseId} and lessonId = {$lessonId}")->count();
    //        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  courseId = ? AND lessonId = ?";
    //        return $this->getConnection()->fetchColumn($sql, array($courseId, $lessonId)); 
    }
    
    public function deleteCourseMaterial($id,$materialId){
        return $this->where("courseId = {$$id} and id = {$materialId}")->delete();
    }

    public function getMaterialCountByFileId($fileId){
        return $this->where("fileId = {$fileId}")->field('id')->count("id");
    //        $sql = "SELECT COUNT(id) FROM {$this->tableName} WHERE  fileId = ? ";
    //        return $this->getConnection()->fetchColumn($sql, array($fileId)); 
     }  
}
?>
