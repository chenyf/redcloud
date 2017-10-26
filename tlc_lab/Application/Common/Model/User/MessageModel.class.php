<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class MessageModel extends BaseModel{
    
    protected $tableName = 'message';

	public function getThinkConnection(){
		return $this;
	}

    public function getMessage($id){
        return $this->getThinkConnection()->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getThinkConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function addMessage($message){
        $r = $this->getThinkConnection()->add($message);
        if(!$r) E("Insert message error.");
        return  $this->getMessage($r);
//        $affected = $this->getConnection()->insert($this->tableName, $message);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert message error.');
//        }
//        return $this->getMessage($this->getConnection()->lastInsertId());
    }

    public function deleteMessage($id){
        return $this->getThinkConnection()->where("id = {$id}")->delete();
//        return $this->getThinkConnection()->delete($this->tableName, array('id' => $id));
    } 

    private function _createSearchQueryBuilder($conditions){
        return $this->createDynamicQueryBuilder($conditions)
            ->from($this->tableName, 'message')
            ->andWhere('fromId = :fromId')
            ->andWhere('toId = :toId')
            ->andWhere('createdTime = :createdTime')
            ->andWhere('createdTime >= :startDate')
            ->andWhere('createdTime < :endDate')
            ->andWhere('content LIKE :content');
    }

    public function searchMessagesCount($conditions){
	    if (isset($conditions['content'])) {
		    $conditions['content'] = "%{$conditions['content']}%";
	    }

	    $builder = $this->_createSearchQueryBuilder($conditions)
		    ->select('COUNT(id)');

	    $builder->execute()->fetchColumn(0);

    }

    public function searchMessages($conditions, $orderBy, $start, $limit){
	    $this->filterStartLimit($start, $limit);
	    if (isset($conditions['content'])) {
		    $conditions['content'] = "%{$conditions['content']}%";
	    }

	    $builder = $this->_createSearchQueryBuilder($conditions)
		    ->select('*')
		    ->setFirstResult($start)
		    ->setMaxResults($limit)
		    ->orderBy('createdTime', 'DESC');

	    return $builder->execute()->fetchAll() ? : array();
    }

    public function getMessageByFromIdAndToId($fromId, $toId){
        return $this->getThinkConnection()->where("fromId = {$fromId} and toId = {$toId}")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE fromId = ? AND toId = ?";
//        return $this->getThinkConnection()->fetchAssoc($sql, array($fromId, $toId));
    }

    public function findMessagesByIds(array $ids){
        if(empty($ids)){ return array(); }
        $str = implode(',',$ids);
        return $this->getThinkConnection()->where("id in ({$str})")->select();
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
//        return $this->getThinkConnection()->fetchAll($sql, $ids);
    }

    public function deleteMessagesByIds(array $ids){
        if(empty($ids)){ return array(); }
        $str = implode(',',$ids);
        return $this->getThinkConnection()->where("id in ({$str})")->delete();
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
//        $sql ="DELETE FROM {$this->tableName} WHERE id IN ({$marks});";
//        return $this->getThinkConnection()->executeUpdate($sql, $ids);
    }
}
?>
