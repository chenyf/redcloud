<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class StatusModel extends BaseModel{
    
    protected $tableName = 'status';

    private $serializeFields = array(
        'properties' => 'json',
    );

    public function getStatus($id){
        $status = $this->where("id = {$id}")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        $status = $this->getConnection()->fetchAssoc($sql, array($id));
        return $status ? $this->createSerializer()->unserialize($status, $this->serializeFields) : null;
    }

    public function searchStatusesCount($conditions){
//        $where =  $this->_createSearchQueryBuilder($conditions);
//        return $this->where($where)->count("id");
        $builder = $this->_createSearchQueryBuilder($conditions)->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function findStatusesByUserIds($userIds, $start, $limit){
        if(empty($userIds)){
            return array();
        }
        $str = implode(',',$userIds);
        $statuses = $this->where("userId in ({$str})")->select();
//        $marks = str_repeat('?,', count($userIds) - 1) . '?';
//        $sql ="SELECT * FROM {$this->tableName} WHERE userId IN ({$marks});";
//        $statuses = $this->getConnection()->fetchAll($sql, $userIds);
        return $this->createSerializer()->unserializes($statuses, $this->serializeFields);
    }

    public function findStatusesByUserIdsCount($userIds){
        if(empty($userIds)){
            return array();
        }
        $str = implode(',',$userIds);
        $statuses = $this->where("userId in ({$str})")->count();
//        $marks = str_repeat('?,', count($userIds) - 1) . '?';
//        $sql ="SELECT COUNT(*) FROM {$this->tableName} WHERE userId IN ({$marks});";
//        $this->getConnection()->fetchColumn($sql, $userIds);
    }

    public function searchStatuses($conditions, $orderBy, $start, $limit){
        $this->filterStartLimit($start, $limit);
        $this->checkOrderBy($orderBy, array('createdTime'));

        $builder = $this->_createSearchQueryBuilder($conditions)
            ->select('*')
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->orderBy($orderBy[0], $orderBy[1]);
        $statuses = $builder->execute()->fetchAll() ? : array();
        
//        $where =  $this->_createSearchQueryBuilder($conditions);
//        $statuses = $this->where($where)->order("{$orderBy[0]} {$orderBy[1]}")->limit($start, $limit)->select();
        return $this->createSerializer()->unserializes($statuses, $this->serializeFields);
    }

    private function _createSearchQueryBuilder($conditions){
        return  $this->createDynamicQueryBuilder($conditions)->from($this->tableName, $this->tableName);
    }

    public function addStatus($fields) {
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $r = $this->add($fields);
        if(!$r) E("Insert status error.");
        return  $this->getStatus($r);
//        $affected = $this->getConnection()->insert($this->tableName, $fields);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert status error.');
//        }
//        return $this->getStatus($this->getConnection()->lastInsertId());
    }

    public function updateStatus($id, $fields){
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $this->where("id = {$id}")->save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getStatus($id);
    }

    public function deleteStatus($id){
        return $this->where("id = {$id}")->delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }
   public function deleteStatusesByUserIdAndTypeAndObject($userId, $type, $objectType, $objectId){
       return $this->where("userId = {$userId} and type = '{$type}' and objectType = '{$objectType}' and objectId = {$objectId}")->delete();
//        return $this->getConnection()->delete($this->tableName, array(
//            'userId' => $userId,
//            'type' =>$type,
//            'objectType'=>$objectType,
//            'objectId'=>$objectId
//            ));
    }
    
}
?>