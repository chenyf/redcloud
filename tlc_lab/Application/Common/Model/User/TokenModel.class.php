<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class TokenModel extends BaseModel{
    
    protected $tableName = 'user_token';

    private $serializeFields = array(
        'data' => 'phpserialize',
    );
    
    private function initSet(){
        
    }

    public function getToken($id){
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        $token = $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
        $where['id'] = $id;
        $token = $this->where($where)->find();
        return $token ? $this->createSerializer()->unserialize($token, $this->serializeFields) : null;
    }
    
    public function getUserLastToken($userId){
        $where['userId'] = $userId;
        $token = $this->where($where)->order("createdTime desc")->find();
        return $token ? $this->createSerializer()->unserialize($token, $this->serializeFields) : null;
    }

    public function getTokenByToken($token){
//        $sql = "SELECT * FROM {$this->tableName} WHERE token = ? LIMIT 1";
//        $token = $this->getConnection()->fetchAssoc($sql, array($token));
        $where['token'] = $token;
        $token = $this->where($where)->find();
        return $token ? $this->createSerializer()->unserialize($token, $this->serializeFields) : null;
    }

    /**
     * 从中心库查token
     * @author fubaosheng 2015-08-11
     */
    public function findTokenByToken($token){
//        $sql = "SELECT * FROM {$this->tableName} WHERE token = ? LIMIT 1";
//        $token = $this->getConnection()->fetchAssoc($sql, array($token));
        $where['token'] = $token;
        $token = $this->where($where)->find();
        return $token ? $this->createSerializer()->unserialize($token, $this->serializeFields) : null;
    }

    /**
     * token 先填中心库 后填私有库
     * @author fubaosheng 2015-08-11
     */
    public function addToken(array $token,$siteSelect = "local"){
        $this->initSet();
        $token = $this->createSerializer()->serialize($token, $this->serializeFields);

        $id = $this->add($token);
        if(!$id) E("Insert token error.");
        //else synchroData(array('db'=>'private','sql'=>$this->getLastSql(),'pk'=> $this->getAutoIncField(),'id'=>$id));
        return  $this->getToken($id);
    }

    public function deleteToken($id){
        $where['id'] = $id;
        $r = $this->where($where)->delete();
        if($r) synchroData(array('db'=>'private','sql'=>$this->getLastSql()));
        return $r;
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    public function waveRemainedTimes($id, $diff){
        return $this->where("id = {$id}")->save(array('remainedTimes'=>'remainedTimes'.$diff));
//        $sql = "UPDATE {$this->tableName} SET remainedTimes = remainedTimes + ? WHERE id = ? LIMIT 1";
//        return $this->getConnection()->executeQuery($sql, array($diff, $id));
    }

    public function searchTokenCount($conditions){
//        $where = $this->_createSearchQueryBuilder($conditions);
//        return $this->where($where)->count("id");
        $builder = $this->_createSearchQueryBuilder($conditions)->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    private function _createSearchQueryBuilder($conditions){
        $builder = $this->createDynamicQueryBuilder($conditions)
            ->from($this->tableName, 'user_token')
            ->andWhere('type = :type');
        return $builder;
    }
    
}
?>
