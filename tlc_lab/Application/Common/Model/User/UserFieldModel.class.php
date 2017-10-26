<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class UserFieldModel extends BaseModel {
    
    protected $tableName = "user_field";

    public function addField($field){
        $r = $this->add($field);
        if(!$r) E("Insert user_field error.");
        return  $this->getField($r);
//        $affected = $this->getConnection()->insert($this->tableName, $field);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert user_field error.');
//        }
//        return $this->getField($this->getConnection()->lastInsertId());
    }

    public function getField($id){
         return $this->where("id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getFieldByFieldName($fieldName){
           return $this->where("fieldName = '{$fieldName}'")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE fieldName = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($fieldName)) ? : null ;
    }

    public function searchFieldCount($condition){
//        $where = $this->_createSearchQueryBuilder($conditions);
//        return $this->where($where)->count("id");
        $builder = $this->_createSearchQueryBuilder($condition)->select('count(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function getAllFieldsOrderBySeq(){
        return $this->order("seq asc")->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} ORDER BY seq";
//        return $this->getConnection()->fetchAll($sql) ? : array();
    }

    public function getAllFieldsOrderBySeqAndEnabled(){
          return $this->where('enabled = 1')->order("seq asc")->select() ? : array();
//        $sql = "SELECT * FROM {$this->tableName} where enabled=1 ORDER BY seq";
//        return $this->getConnection()->fetchAll($sql) ? : array();
    }

    public function updateField($id,$fields){
        $this->where("id = {$id}")->save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
        return $this->getField($id);
    }

    public function deleteField($id){
        return $this->where("id = {$id}")->delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
    }

    private function _createSearchQueryBuilder($condition){   
        if(isset($condition['fieldName'])){
           $condition['fieldName'] = "%".$condition['fieldName']."%";
        } 
        $builder = $this->createDynamicQueryBuilder($condition)
            ->from($this->tableName, $this->tableName)
            ->andWhere('enabled = :enabled')
            ->andWhere('fieldName like :fieldName');

        return $builder;

    }
    
}
?>
