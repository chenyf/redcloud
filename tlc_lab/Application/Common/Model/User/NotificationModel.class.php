<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class NotificationModel extends BaseModel{

    protected $tableName = 'notification';

	public function getThinkConnection(){
		return $this;
	}

    public function getNotification($id){
        return $this->getThinkConnection()->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getThinkConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function addNotification($notification){
        $r = $this->getThinkConnection()->add($notification);
        if(!$r) E("Insert notification error.");
        return  $this->getNotification($r);
//        $affected = $this->getThinkConnection()->insert($this->tableName, $notification);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert notification error.');
//        }
//        return $this->getNotification($this->getThinkConnection()->lastInsertId());
    }

    public function updateNotification($id, $fields){
	    $this->getThinkConnection()->where("id = {$id}")->save($fields);
//        $this->getThinkConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getNotification($id);
    }

    public function findNotificationsByUserId($userId, $start, $limit,$type = array()){
        $this->filterStartLimit($start, $limit);
        if(!empty($type)){
            $where['type'] = array("in",$type);
        }
        $where['userId'] = array("eq",$userId);
        return $this->getThinkConnection()->where($where)->order("createdTime desc")->limit($start, $limit)->select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getThinkConnection()->fetchAll($sql, array($userId));
    }

    public function getNotificationCountByUserId($userId,$type=array()){
        if(!empty($type)){
            $where['type'] = array("in",$type);
        }
        $where['userId'] = array("eq",$userId);
        return $this->getThinkConnection()->where($where)->count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  userId = ? ";
//        return $this->getThinkConnection()->fetchColumn($sql, array($userId));
    }

    public function getUserNoReadNum($uid){
        return $this->getThinkConnection()->where("userId = {$uid} and isRead = 0")->count();
    }

}
?>
