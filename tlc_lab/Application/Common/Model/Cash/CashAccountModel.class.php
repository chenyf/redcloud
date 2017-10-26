<?php

namespace Common\Model\Cash;

use Common\Model\Common\BaseModel;

class CashAccountModel extends BaseModel {

    protected $tableName = 'cash_account';

    public function getAccount($id) {
        return $this->where("id = {$id}")->find()? : null;
    }

    public function getAccountByUserId($orgId) {
        $map['orgId'] = $orgId;
        return $this->where($map)->find();
    }

    public function getAccountByOrgId($orgId) {
        $map['orgId'] = $orgId;
        return $this->where($map)->find();
    }

    public function findAccountsByUserIds($userIds) {
        if (empty($userIds)) {
            return array();
        }
//        $marks = str_repeat('?,', count($userIds) - 1) . '?';
        $marks = implode(",", $userIds);
        $map['userId'] = array("in", $marks);
        return $this->where($map)->select();
//        $sql ="SELECT * FROM {$this->tableName} WHERE userId IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $userIds);
    }

    public function addAccount($fields) {
        return $this->add($fields);

//        if($affected <= 0){
//            E("Insert cash account error");
//        }
//        return $affected;
//        $affected = $this->getConnection()->insert($this->tableName, $fields);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert cash account error.');
//        }
//        return $this->getAccount($this->getConnection()->lastInsertId());
    }

    public function updateAccount($id, $fields) {
        $map['id'] = $id;
        return $this->where($map)->save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
//        return $this->getAccount($id);
    }

    public function setDesCash($userId, $amount) {
        $map['userId'] = intval($userId);
        return $this->where($map)->setDec('cash', $amount);
    }
    public function setDesCashbyOrg($orgId, $amount) {
        $map['orgId'] = intval($orgId);
        return $this->where($map)->setDec('cash', $amount);
    }
    public function setIncCash($userId, $amount){
         $map['userId'] = intval($userId);
        return $this->where($map)->setInc('cash', $amount);
    }
    public function setDesIosCash($userId, $amount) {
        $map['userId'] = intval($userId);
        return $this->where($map)->setDec('iosCash', $amount);
    }
    public function setIncIosCash($userId, $amount) {
        $map['userId'] = intval($userId);
        return $this->where($map)->setInc('iosCash', $amount);
    }
    public function waveCashField($id, $value) {
//        return $this->where("id = {$id}")-> 
        $sql = "UPDATE {$this->tableName} SET cash = cash + {$value} WHERE id = {$id} LIMIT 1";
        return $this->query($sql);
//        $sql = "UPDATE {$this->tableName} SET cash = cash + ? WHERE id = ? LIMIT 1";
//        return $this->getConnection()->executeQuery($sql, array($value, $id));
    }

    public function waveDownCashField($id, $value) {

        $sql = "UPDATE {$this->tableName} SET cash = cash - {$value} WHERE id = {$id} LIMIT 1";
        return $this->query($sql);
//        $sql = "UPDATE {$this->tableName} SET cash = cash - ? WHERE id = ? LIMIT 1";
//        return $this->getConnection()->executeQuery($sql, array($value, $id));
    }

    public function searchAccount($conditions, $orderBy, $start, $limit) {
//        return $this-> where($conditions)->limit($start, $limit);
        $builder = $this->createAccountQueryBuilder($conditions)
                ->select('*')
                ->setFirstResult($start)
                ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }

    public function searchAccountCount($conditions) {
//        $this-> where($conditions)-> count("id");
        $builder = $this->createAccountQueryBuilder($conditions)
                ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    private function createAccountQueryBuilder($conditions) {

        $conditions = array_filter($conditions);
        return $this->createDynamicQueryBuilder($conditions)
                        ->from($this->tableName, 'cash_account')
                        ->andWhere('userId = :userId');
    }

}