<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class MessageConversationModel extends BaseModel{

    protected $tableName = 'message_conversation';


	public function getThinkConnection(){
		return $this;
	}

    /**
     * 表中的toId 表示的是发送者, fromId表示的是接受者,理解的立场是从系统发送角度出发，先给toId这创建conversation.
     */
    public function getConversation($id){
        return $this->getThinkConnection()->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getThinkConnection()->fetchAssoc($sql, array($id)) ? : null;
    }
    
    public function addConversation($conversation){
        $r = $this->getThinkConnection()->add($conversation);
        if(!$r) E("Insert conversation error.");
        return  $this->getConversation($r);
//        $affected = $this->getThinkConnection()->insert($this->tableName, $conversation);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert conversation error.');
//        }
//        return $this->getConversation($this->getThinkConnection()->lastInsertId());
    }

    public function deleteConversation($id){
        return $this->getThinkConnection()->where("id = {$id}")->delete();
//        return $this->getThinkConnection()->delete($this->tableName, array('id' => $id));
    }

    public function getConversationByFromIdAndToId($fromId, $toId){
        return $this->getThinkConnection()->where("fromId = {$fromId} and toId = {$toId}")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE fromId = ? AND toId = ?";
//        return $this->getThinkConnection()->fetchAssoc($sql, array($fromId, $toId));
    }

    public function updateConversation($id, $toUpdateConversation){
	    $this->getThinkConnection()->where("id = {$id}")->save($toUpdateConversation);
//        $this->getThinkConnection()->update($this->tableName, $toUpdateConversation, array('id' => $id));
        return $this->getConversation($id);
    }

    public function findConversationsByToId($toId, $start, $limit){
        $this->filterStartLimit($start, $limit);
        return $this->getThinkConnection()->where("toId = {$toId}")->order("latestMessageTime DESC")->limit($start, $limit)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE toId = ? ORDER BY latestMessageTime DESC LIMIT {$start}, {$limit}";
//        return $this->getThinkConnection()->fetchAll($sql, array($toId));
    }

    public function getConversationCountByToId($toId){
         return $this->getThinkConnection()->where("toId = {$toId}")->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  toId = ?";
//        return $this->getThinkConnection()->fetchColumn($sql, array($toId));
    }
    
    public function getUserNoReadNum($toId){
        return $this->getThinkConnection()->where("toId = {$toId}")->sum("unreadNum") ? : 0;
    }
}
?>
