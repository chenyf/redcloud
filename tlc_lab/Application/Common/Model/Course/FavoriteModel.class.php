<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Lib\ArrayToolkit;
use Common\Model\Common\BaseModel;

class FavoriteModel extends BaseModel{
    
    protected $tableName = 'course_favorite';
    
    public function getFavoriteCourseCountByUserId($userId){
        return $this->where("userId = {$userId}")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  userId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($userId));
    }
    
    public function findCourseFavoritesByUserId($userId, $start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->where("userId = {$userId}")->order(" createdTime desc")->limit($start,$limit)->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($userId)) ? : array();
    }
    
    public function getFavorite($id){
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getFavoriteByUserIdAndCourseId($userId, $courseId){
        return $this->where("userId = {$userId} and courseId = {$courseId}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? AND courseId = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($userId, $courseId)) ? : null; 
    }

    public function addFavorite($favorite){
        $r = $this->add($favorite);
        if(!$r) E("Insert course favorite error.");
        return $this->getFavorite($r);
//        $affected = $this->getConnection()->insert($this->tableName, $favorite);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert course favorite error.');
//        }
//        return $this->getFavorite($this->getConnection()->lastInsertId());
    }

    public function deleteFavorite($id){
        return $this->where("id = {$id}")->delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }
    
}
?>
