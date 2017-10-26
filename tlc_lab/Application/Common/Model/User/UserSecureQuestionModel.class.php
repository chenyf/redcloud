<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class UserSecureQuestionModel extends BaseModel {
    
    protected $tableName = 'user_secure_question';

    public function getUserSecureQuestionsByUserId($userId){
         return $this->where("userId = {$userId}")->order("createdTime ASC")->select() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? ORDER BY createdTime ASC ";
//        return  $this->getConnection()->fetchAll($sql, array($userId)) ? : null; 
    }

    public function addOneUserSecureQuestion($filedsWithUserIdAndQuestionNumAndQuestionAndHashedAnswerAndAnswerSalt){
        $id = $this->add($filedsWithUserIdAndQuestionNumAndQuestionAndHashedAnswerAndAnswerSalt);
        if(!$id) E("Insert user_secure_question error.");
        else synchroData(array('db'=>'private','sql'=>$this->getLastSql(),'pk'=> $this->getAutoIncField(),'id'=>$id));
//        $affected = $this->getConnection()->insert($this->tableName, $filedsWithUserIdAndQuestionNumAndQuestionAndHashedAnswerAndAnswerSalt);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert user_secure_question error.');
//        }
        return true;      
    }
    
}
?>
