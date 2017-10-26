<?php

namespace Common\Model\Cash;

use Common\Model\Common\BaseModel;

class CashOrdersModel extends BaseModel {

    protected $tableName = 'cash_orders';

    public function getOrder($id) {
        $map['id'] = $id;
        return $this->where($map)->find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function addOrder($fields) {
        $id = $this->add($fields);

        return $this->getOrder($id);
//        $order = $this->getConnection()->insert($this->tableName, $fields);
//        if ($order <= 0) {
//            throw $this->createDaoException('Insert cash_orders account error.');
//        }
//        return $this->getOrder($this->getConnection()->lastInsertId());
    }

    public function getOrderBySn($sn, $lock = false) {
        //  echo  $sql = "SELECT * FROM {$this->tableName} WHERE sn = {$sn}  LIMIT 1" . ($lock ? ' FOR UPDATE' : '');
        $map['sn'] = $sn;
        return $this->where($map)->find();
        //    return $this->query($sql)? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE sn = ?  LIMIT 1" . ($lock ? ' FOR UPDATE' : '');
//        return $this->getConnection()->fetchAssoc($sql, array($sn)) ? : null;
    }

    public function updateOrder($id, $fields) {
        $map['id'] = $id;
        $this->where($map)->save($fields);
        return $this->getOrder($id);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
//        return $this->getOrder($id);
    }

    public function closeOrders($time) {
        $sql = "UPDATE {$this->tableName} set status ='cancelled' WHERE status = 'created' and createdTime < {$time}";
        return $this->query($sql);
//        $sql = "UPDATE {$this->tableName} set status ='cancelled' WHERE status = 'created' and createdTime < {$time}";
//        $this->getConnection()->exec($sql);
    }
    //合并数据
    public function updCashOrder($uid,$appUid){
        return $this->where(array('userId'=>$appUid))->save(array('userId'=>$uid));
    }
    //取消订单
    public function cancelOrder($id){
        $map['id']=  intval($id);
        return $this->where($map)->save(array('status'=>'cancelled'));
    }

    public function searchOrders($conditions, $orderBy, $start, $limit) {
//        return $this-> where($conditions)->order("$orderBy[0] $orderBy[1]")->limit($start, $limit);
        $builder = $this->createOrderQueryBuilder($conditions)
                ->select('*')
                ->orderBy($orderBy[0], $orderBy[1])
                ->setFirstResult($start)
                ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }
     public function searchOrdersList($conditions, $orderBy, $start, $limit) {
        return $this->where($conditions)->order('id desc')->select();
    }

    public function searchOrdersCount($conditions) {
//         return $this-> where($conditions)-> count("id");
        $builder = $this->createOrderQueryBuilder($conditions)
                ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function analysisAmount($conditions) {
//        return $this-> where($conditions)-> sum("amount");
        $builder = $this->createOrderQueryBuilder($conditions)
                ->select('sum(amount)');
        return $builder->execute()->fetchColumn(0);
    }

    private function createOrderQueryBuilder($conditions) {
        $conditions = array_filter($conditions);
        return $this->createDynamicQueryBuilder($conditions)
                        ->from($this->tableName, 'cash_orders')
                        ->andWhere('status = :status')
                        ->andWhere('userId = :userId')
                        ->andWhere('payment = :payment')
                        ->andWhere('title = :title')
                        ->andWhere('sn = :sn');
    }

}