<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class FriendModel extends BaseModel{
    
    protected $tableName = 'friend';

    public function getFriend($id){
        return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function addFriend($friend){
        $r = $this->add($friend);
        if(!$r) E("Insert friend error.");
        return  $this->getFriend($r);
//        $affected = $this->getConnection()->insert($this->tableName, $friend);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert friend error.');
//        }
//        return $this->getFriend($this->getConnection()->lastInsertId());
    }

    public function deleteFriend($id){
        $this->where("id = {$id}")->delete();
//       return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function getFriendByFromIdAndToId($fromId, $toId){
        return $this->where("fromId = {$fromId} and toId = {$toId}")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE fromId = ? AND toId = ?";
//        return $this->getConnection()->fetchAssoc($sql, array($fromId, $toId));
    }

    public function findFriendsByFromId($fromId, $start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->where("fromId = {$fromId}")->order("createdTime DESC")->limit($start, $limit)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE fromId = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($fromId));
    }

    public function findAllUserFollowingByFromId($fromId){
         return $this->where("fromId = {$fromId}")->order("createdTime DESC")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE fromId = ? ORDER BY createdTime DESC ";
//        return $this->getConnection()->fetchAll($sql, array($fromId));
    }

    public function findAllUserFollowerByToId($toId){
        return $this->where("toId = {$toId}")->order("createdTime DESC")->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE toId = ? ORDER BY createdTime DESC ";
//        return $this->getConnection()->fetchAll($sql, array($toId));
    }

    public function findFriendCountByFromId($fromId){
        return $this->where("fromId = {$fromId}")->count("id");
//        $sql = "SELECT COUNT(id) FROM {$this->tableName} WHERE fromId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($fromId));
    }

    public function findFriendsByToId($toId, $start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->where("toId = {$toId}")->order("createdTime DESC")->limit($start, $limit)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE toId = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($toId));
    }

    public function findFriendCountByToId($toId){
        return $this->where("toId = {$toId}")->count("id");
//        $sql = "SELECT COUNT(id) FROM {$this->tableName} WHERE toId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($toId));
    }

    public function getFriendsByFromIdAndToIds($fromId, array $toIds){
        if (empty($toIds)) { return array(); }
        $toIds = array_values($toIds);
        $str = implode(',',$toIds);
        return $this->where("fromId = {$fromId} and toId in({$str})")->select();
//        $marks = str_repeat('?,', count($toIds) - 1) . '?';
//        $parmaters = array_merge(array($fromId), $toIds);
//        $sql ="SELECT * FROM {$this->tableName} WHERE fromId = ? AND toId IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $parmaters);
    }
    
    
}
?>
