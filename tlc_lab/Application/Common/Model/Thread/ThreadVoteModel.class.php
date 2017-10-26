<?php

namespace Common\Model\Thread;
use Common\Model\Common\BaseModel;

class ThreadVoteModel extends BaseModel
{

    protected $tableName = 'thread_vote';

    public function getVote($id)
    {
        return $this-> where("id = {$id}")-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getVoteByThreadIdAndPostIdAndUserId($threadId, $postId, $userId)
    {
        return $this-> where("threadId = {$threadId} and poseId = {$postId} and userId = {$userId}")-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE threadId = ? AND postId =? AND userId = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($threadId, $postId, $userId)) ? : null;
    }

    public function addVote($fields)
    {
        $affected = $this-> add($fields);
        if($affected <= 0){
            E("Insert vote error");
        }
        return $affected;
//        $affected = $this->getConnection()->insert($this->tableName, $fields);
        
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert vote error.');
//        }
//        return $this->getVote($this->getConnection()->lastInsertId());
    }

}