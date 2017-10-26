<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class MessageRelationModel extends BaseModel{
    
    protected $tableName = 'message_relation';


	public function getThinkConnection(){
		return $this;
	}
	
    public function addRelation($relation){
        $r = $this->getThinkConnection()->add($relation);
        if(!$r) E("Insert relation error.");
        return  $this->getRelation($r);
//        $affected = $this->getThinkConnection()->insert($this->tableName, $relation);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert relation error.');
//        }
//        return $this->getRelation($this->getThinkConnection()->lastInsertId());
    }

    public function deleteRelation($id){
         return $this->getThinkConnection()->where("id = {$id}")->delete();
//       return $this->getThinkConnection()->delete($this->tableName, array('id' => $id));
    }

    public function updateRelation($id, $toUpdateRelation){
         $this->getThinkConnection()->where("id = {$id}")->save($toUpdateRelation);
//        $this->getThinkConnection()->update($this->tableName, $toUpdateRelation, array('id' => $id));
        return $this->getRelation($id);
    }

    public function updateRelationIsReadByConversationId($conversationId, array $isRead){
        return  $this->getThinkConnection()->where("conversationId = {$conversationId}")->save($isRead);
//        return $this->getThinkConnection()->update($this->tableName, $isRead, array('conversationId' => $conversationId));
    }

    public function deleteConversationMessage($conversationId, $messageId){
        return $this->getThinkConnection()->where("conversationId = {$conversationId} and messageId = {$messageId}")->delete();
//        return $this->getThinkConnection()->delete($this->tableName, array('conversationId' => $conversationId, 'messageId'=>$messageId));
    }

    public function deleteRelationByConversationId($conversationId){
        return $this->getThinkConnection()->where("conversationId = {$conversationId}")->delete();
//        return $this->getThinkConnection()->delete($this->tableName, array('conversationId' => $conversationId));
    }

    public function getRelationCountByConversationId($conversationId){
        return $this->getThinkConnection()->where("conversationId = {$conversationId}")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  conversationId = ?";
//        return $this->getThinkConnection()->fetchColumn($sql, array($conversationId));
    }

    public function findRelationsByConversationId($conversationId, $start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->getThinkConnection()->where("conversationId = {$conversationId}")->order("messageId desc")->limit($start, $limit)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE conversationId = ? ORDER BY messageId DESC LIMIT {$start}, {$limit}";
//        return $this->getThinkConnection()->fetchAll($sql, array($conversationId));
    }

    public function getRelation($id){
        return $this->getThinkConnection()->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getThinkConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getRelationByConversationIdAndMessageId($conversationId, $messageId){
         return $this->getThinkConnection()->where("conversationId = {$conversationId} and messageId = {$messageId}")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE conversationId = ? AND messageId = ?";
//        return $this->getThinkConnection()->fetchAssoc($sql, array($conversationId, $messageId));
    }
}
?>
