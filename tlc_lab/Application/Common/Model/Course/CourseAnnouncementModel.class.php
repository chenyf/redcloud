<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;

class CourseAnnouncementModel extends BaseModel{
    
    protected $tableName = 'course_announcement';

    public function getAnnouncement($id){
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function findAnnouncementsByCourseId($courseId, $start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->where("courseId = {$courseId}")->order("createdTime desc")->limit($start,$limit)->select() ? : array();
 //        $sql ="SELECT * FROM {$this->tableName} WHERE courseId=? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($courseId)) ? : array();
    }

    public function findAnnouncementsByCourseIds($ids, $start, $limit){
       if(empty($ids)) return array();
       $str = implode(",", $ids);
       return $this->where("courseId in ({$str})")->order("createdTime desc")->limit($start,$limit)->select();
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
//        $sql ="SELECT * FROM {$this->tableName} WHERE courseId IN ({$marks}) ORDER BY createdTime DESC LIMIT {$start}, {$limit};";
//        return $this->getConnection()->fetchAll($sql, $ids);
    }

    public function addAnnouncement($fields){
        $r = $this->add($fields);
        if(!$r) E("Insert announcement error.");
        return $this->getAnnouncement($r);
//        $affected = $this->getConnection()->insert($this->tableName, $fields);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert announcement error.');
//        }
//        return $this->getAnnouncement($this->getConnection()->lastInsertId());
    }

    public function deleteAnnouncement($id){
        return $this->where(" id = {$id}")->delete();
//        $sql = "DELETE FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->executeUpdate($sql, array($id));
    }

    public function updateAnnouncement($id, $fields){
        $this->where("id = {$id}")->save($fields);
//        $id = $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getAnnouncement($id);
    }
}
?>
