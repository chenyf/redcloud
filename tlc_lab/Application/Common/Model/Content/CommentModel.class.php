<?php

namespace Common\Model\Content;
use Common\Model\Common\BaseModel;
class CommentModel extends BaseModel
{
    protected $tableName = 'comment';

	public function getComment($id)
	{
            return $this-> where("id = {$id}")-> find()? : null;
//            $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//            return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
	}

	public function addComment($comment)
	{
            return $this-> add($comment);
//          $affected = $this->getConnection()->insert($this->tableName, $comment);
//          if ($affected <= 0) {
//              throw $this->createDaoException('Insert comment error.');
//          }
//          return $this->getComment($this->getConnection()->lastInsertId());
	}

	public function deleteComment($id)
	{
            return $this-> where("id = {$id}") -> delete();
//		return $this->getConnection()->delete($this->tableName, array('id' => $id));
	}

	public function findCommentsByObjectTypeAndObjectId($objectType, $objectId, $start, $limit)
	{
                $map["objectType"] = $objectType; 
                $map["objectId"]   = $objectId; 
                return $this-> where($map)->order("createdTime DESC")-> limit($start, $limit)->select();
//                $this->filterStartLimit($start, $limit);
//		$sql = "SELECT * FROM {$this->tableName} WHERE objectType = ? AND objectId = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//                return $this->getConnection()->fetchAll($sql, array($objectType, $objectId));
	}

	public function findCommentsByObjectType($objectType, $start, $limit)
	{
                $map["objectType"] = $objectType; 
                return $this-> where($map)-> order("createdTime desc")-> limit($start, $limit)-> select();
//                $this->filterStartLimit($start, $limit);
//		$sql = "SELECT * FROM {$this->tableName} WHERE objectType = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//                return $this->getConnection()->fetchAll($sql, array($objectType));
	}

	public function findCommentsCountByObjectType($objectType)
	{
            $map["objectType"] = $objectType; 
            return $this-> where($map)-> select();
//		$sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  objectType = ?";
//                return $this->getConnection()->fetchColumn($sql, array($objectType));
	}

}