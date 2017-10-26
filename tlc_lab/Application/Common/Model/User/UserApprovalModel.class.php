<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class UserApprovalModel extends BaseModel{
    
    protected $tableName = 'user_approval';

    public function addApproval($approval){
//        echo 123;die;
        $id = $this->add($approval);
        if(!$id) E("Insert user approval error.");
        else synchroData(array('db'=>'private','sql'=>$this->getLastSql(),'pk'=> $this->getAutoIncField(),'id'=>$id));
        return  $this->getApproval($id);
//        $affected = $this->getConnection()->insert($this->tableName, $approval);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert user approval error.');
//        }
//        return $this->getApproval($this->getConnection()->lastInsertId());
    }

    public function getApproval($id){
        $where['id'] = $id;
        return $this->where($where)->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function updateApproval($id, $fields){
        $where['id'] = $id;
        $r = $this->where($where)->save($fields);
        if($r) synchroData(array('db'=>'private','sql'=>$this->getLastSql()));
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getApproval($id);
    }

    public function getLastestApprovalByUserIdAndStatus($userId, $status){
        $where['userId'] = $userId;
        $where['status'] = $status;
        return $this->where($where)->order("createdTime DESC")->find() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? AND status = ? ORDER BY createdTime DESC LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($userId, $status));
    }

    public function findApprovalsByUserIds($userIds){
        if(empty($userIds)){return array();}
        $str = implode(',', $userIds);
        $where['userId'] = array('in',$str);
        return $this->where($where)->select() ? : array();
//        $marks = str_repeat('?,', count($userIds) - 1) . '?';
//        $sql ="SELECT * FROM {$this->tableName} WHERE userId IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $userIds);
    }
     
}
?>
