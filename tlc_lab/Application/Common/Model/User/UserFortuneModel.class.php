<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class UserFortuneModel extends BaseModel{
    
    protected $tableName = 'user_fortune_log';
    
    public function addLog(array $log){
        $r = $this->add($log);
        if(!$r) E("Insert log error");
        return  $this->getLog($r);
//        $affected = $this->getConnection()->insert($this->tableName, $log);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert log error');
//        }
//        return $this->getLog($this->getConnection()->lastInsertId());
    }

    public function getLog($id){
        return $this->where("id = {$id}")->find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id));
    }
    
}
?>
