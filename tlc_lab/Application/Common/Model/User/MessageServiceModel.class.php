<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class MessageServiceModel extends BaseModel{
    
    CONST RELATION_ISREAD_ON = 1;
    CONST RELATION_ISREAD_OFF = 0;

    private function getMessageDao(){
        return $this->createDao("User.Message");
    }

    private function getConversationDao(){
        return $this->createDao("User.MessageConversation");
    }

    private function getRelationDao(){
        return $this->createService('User.MessageRelationModel');
    }

    private function getUserService(){
        return $this->createService('User.UserServiceModel');
    }

    /**
     * 发送私信
     * 
     * @param integer $fromId   发送者ID
     * @param integer $toId     接收者ID
     * @param string  $content  私信内容
     * 
     * @return array 私信的相关信息
     */
    public function sendMessage($fromId, $toId, $content){
//        if($fromId == $toId){
//            E("抱歉,不允许给自己发送私信!"); 
//        }
        $message = $this->addMessage($fromId, $toId, $content);
        $this->prepareConversationAndRelationForSender($message, $toId, $fromId);
        $this->prepareConversationAndRelationForReceiver($message, $fromId, $toId);
        $this->getUserService()->waveUserCounter($toId, 'newMessageNum', 1);
        return $message;
    }
    
    private function addMessage($fromId, $toId, $content){
        $message = array(
            'fromId' => $fromId,
            'toId' => $toId,
            'content' => $this->purifyHtml($content),
            'createdTime' => time(),
        );
        return $this->getMessageDao()->addMessage($message);
    }
    
    private function prepareConversationAndRelationForSender($message, $toId, $fromId){
        $conversation = $this->getConversationDao()->getConversationByFromIdAndToId($toId, $fromId);
        if ($conversation) {
            $this->getConversationDao()->updateConversation($conversation['id'], array(
                'messageNum' => $conversation['messageNum'] + 1,
                'latestMessageUserId' => $message['fromId'],
                'latestMessageContent' => $message['content'],
                'latestMessageTime' => $message['createdTime'],
            ));
        } else {
            $conversation = array(
                'fromId' => $toId,
                'toId' => $fromId,
                'messageNum' => 1,
                'latestMessageUserId' => $message['fromId'],
                'latestMessageContent' => $message['content'],
                'latestMessageTime' => $message['createdTime'],
                'unreadNum' => 0,
                'createdTime' => time(),
            );
            $conversation = $this->getConversationDao()->addConversation($conversation);
        }

        $relation = array(
            'conversationId' => $conversation['id'],
            'messageId' => $message['id'],
            'isRead' => 0
        );
        $relation = $this->getRelationDao()->addRelation($relation);
    }

    private function prepareConversationAndRelationForReceiver($message, $fromId, $toId){
        $conversation = $this->getConversationDao()->getConversationByFromIdAndToId($fromId, $toId);
        if ($conversation) {
            $this->getConversationDao()->updateConversation($conversation['id'], array(
                'messageNum' => $conversation['messageNum'] + 1,
                'latestMessageUserId' => $message['fromId'],
                'latestMessageContent' => $message['content'],
                'latestMessageTime' => $message['createdTime'],
                'unreadNum' => $conversation['unreadNum'] + 1,
            ));
        } else {
            $conversation = array(
                'fromId' => $fromId,
                'toId' => $toId,
                'messageNum' => 1,
                'latestMessageUserId' => $message['fromId'],
                'latestMessageContent' => $message['content'],
                'latestMessageTime' => $message['createdTime'],
                'unreadNum' => 1,
                'createdTime' => time(),
            );
            $conversation = $this->getConversationDao()->addConversation($conversation);
        }
        $relation = array(
            'conversationId' => $conversation['id'],
            'messageId' => $message['id'],
            'isRead'=>0
        );
        $relation = $this->getRelationDao()->addRelation($relation);
    }
    
    public function getConversation($conversationId){
        return $this->getConversationDao()->getConversation($conversationId);
    }

    /**
     * 获取会话的若干条私信
     * 
     * @param integer $conversationId 会话ID
     * @param integer $start          起始条数
     * @param integer $limit          限制条数
     *
     * @return array 特定会话的若干条私信
     */
    public function findConversationMessages($conversationId, $start, $limit){
        $relations = $this->getRelationDao()->findRelationsByConversationId($conversationId, $start, $limit);
        $messages = $this->getMessageDao()->findMessagesByIds(ArrayToolkit::column($relations, 'messageId'));
        $createUsers = $this->getUserService()->findUsersByIds(ArrayToolkit::column($messages, 'fromId'));
        foreach ($messages as &$message) {
            foreach ($createUsers as $createUser) {
               if($createUser['id'] == $message['fromId']){
                    $message['createdUser'] = $createUser;
               }
            }
        }
        return $this->sortMessages($messages);
    }
    
    /**
     * 装饰获取会话的若干条私信
     * 
     * @param integer $conversationId 会话ID
     * @param integer $start          起始条数
     * @param integer $limit          限制条数
     * @author yangjinlong 2015-05-08
     * @return array 特定会话的若干条私信
     */
    public function decorateConversationMessages($param=array()){
        $options = array(
            "conversationId" => 0,
            "start"          => 0,
            "limit"          => 15
        );
        $options = array_merge($options, $param);
        extract($options);
        $relations = $this->getRelationDao()->findRelationsByConversationId($conversationId, $start, $limit);
        $messages = $this->getMessageDao()->findMessagesByIds(ArrayToolkit::column($relations, 'messageId'));
        return $this->sortMessages($messages);
    }
    
    private function sortMessages($messages){
        usort($messages ,function($a, $b){
            if($a['createdTime'] > $b['createdTime']) return -1;
            if($a['createdTime'] ==  $b['createdTime']) return 0;
            if($a['createdTime'] < $b['createdTime']) return 1;
        });
        return $messages;
    }

    /**
     * 获取会话的私信条数
     * 
     * @param integer $conversationId 会话ID
     *
     * @return integer 特定会话的私信条数
     */
    public function getConversationMessageCount($conversationId){
        return $this->getRelationDao()->getRelationCountByConversationId($conversationId);
    }
    
    /**
     * 获取用户的会话条数
     * 
     * @param integer $userId 用户ID
     * 
     * @return integer 特定用户的会话条数
     */
    public function getUserConversationCount($userId){
        return $this->getConversationDao()->getConversationCountByToId($userId);
    }

    /**
     * 获取指定用户的若干个会话
     *
     * @param integer $userId 用户ID
     * @param integer $start  起始条数
     * @param integer $limit  限制条数
     * @return array  特定用户的若干条会话
     */
    public function findUserConversations($userId, $start, $limit){
         return $this->getConversationDao()->findConversationsByToId($userId, $start, $limit);
    }

    /**
     * 删除会话中的某条私信
     * @param integer $conversationId 指定的会话ID
     * @param integer $messageId      指定的私信ID
     * @return array 被删除的映射Relation
     */
    public function deleteConversationMessage($conversationId, $messageId){
        $relation = $this->getRelationDao()->getRelationByConversationIdAndMessageId($conversationId, $messageId);
        $conversation = $this->getConversationDao()->getConversation($conversationId);

        if($relation['isRead'] == self::RELATION_ISREAD_OFF){
            $this->safelyUpdateConversationMessageNum($conversation);
            $this->safelyUpdateConversationunreadNum($conversation);
        } else {
            $this->safelyUpdateConversationMessageNum($conversation);
        }
        
        $this->getRelationDao()->deleteConversationMessage($conversationId, $messageId);
        $relationCount = $this->getRelationDao()->getRelationCountByConversationId($conversationId);
        if($relationCount == 0){
            $this->getConversationDao()->deleteConversation($conversationId);
        }
    }
    
    private function safelyUpdateConversationMessageNum($conversation){
        if ($conversation['messageNum'] <= 0) {
            $this->getConversationDao()->updateConversation($conversation['id'], 
            array('messageNum'=>0));
        } else {
            $this->getConversationDao()->updateConversation($conversation['id'], 
            array('messageNum'=>$conversation['messageNum']-1));
        }
    }

    private function safelyUpdateConversationunreadNum($conversation){
        if ($conversation['unreadNum'] <= 0) {
            $this->getConversationDao()->updateConversation($conversation['id'], 
            array('unreadNum'=>0));
        } else {
            $this->getConversationDao()->updateConversation($conversation['id'], 
            array('unreadNum'=>$conversation['unreadNum']-1));
        }
    }

    /**
     * 删除某一会话
     * @param integer $conversationId 特定的会话ID
     * @return array  被删除的会话
     */
    public function deleteConversation($conversationId){
        $this->getRelationDao()->deleteRelationByConversationId($conversationId);
        return $this->getConversationDao()->deleteConversation($conversationId);   
    }

    /**
     * 标记会话为已读
     * 1:未读数目全部为0
     * 2:相关的relation都为IsRead=1
     * 
     * @param integer $conversationId 会话ID
     */
    public function markConversationRead($conversationId){
        $conversation = $this->getConversationDao()->getConversation($conversationId);
        if (empty($conversation)) {
            E(sprintf("私信会话#%s不存在。", $conversationId));
        }
        $updatedConversation = $this->getConversationDao()->updateConversation($conversation['id'], array('unreadNum'=>0));
        $this->getRelationDao()->updateRelationIsReadByConversationId($conversationId,array('isRead'=>1));
        return $updatedConversation;
    }

    /**通过会话拥有者和接受者来查询特定的会话
     *
     * @param integer $fromId 会话的接受者
     * @param integer $toId 会话的拥有者
     * $return array 会话
     */
    public function getConversationByFromIdAndToId($fromId, $toId){
        return $this->getConversationDao()->getConversationByFromIdAndToId($fromId, $toId);
    }

    /**
     * 搜索特定状态下的私信条数
     *
     * @param  array $conditions 搜索条件
     * 
     * @return integer   搜索出的私信数目
     */
    
    public function searchMessagesCount($conditions){
        return $this->getMessageDao()->searchMessagesCount($conditions);
    }

    /**
     * 搜索特定状态下的私信
     * @param  array $conditions 搜索条件
     * @param   array $排序规则
     * @param  integer $start      起始数目
     * @param  integer $limit      区间条数
     * 
     * @return array 搜索到的私信内容
     */
    public function searchMessages($conditions, $sort, $start, $limit){
        return $this->getMessageDao()->searchMessages($conditions, $sort, $start, $limit);
    }

    /**
     * 删除特定id的私信
     * @param  array  $ids 指定私信的id
     * @return true 总算删除成功
     */
    public function deleteMessagesByIds(array $ids=null){
        if(empty($ids)){
            E("Please select message item !");
        }
        foreach ($ids as $id) {
            $message = $this->getMessageDao()->getMessage($id);
            $conversation = $this->getConversationDao()->getConversationByFromIdAndToId($message['fromId'], $message['toId']);
            if(!empty($conversation)){
                $this->deleteConversationMessage($conversation['id'], $message['id']);
            }
            $conversation = $this->getConversationDao()->getConversationByFromIdAndToId($message['toId'], $message['fromId']);
            if(!empty($conversation)){
                $this->deleteConversationMessage($conversation['id'], $message['id']);
            }
            $this->getMessageDao()->deleteMessage($id);
        }
        return true;
    }

    public function clearUserNewMessageCounter($userId){
        $this->getUserService()->clearUserCounter($userId, 'newMessageNum');
    }
}
?>
