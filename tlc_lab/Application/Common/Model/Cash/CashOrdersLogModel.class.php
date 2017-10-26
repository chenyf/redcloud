<?php

namespace Common\Model\Cash;

use Common\Model\Common\BaseModel;

class CashOrdersLogModel extends BaseModel
{   
    protected $tableName = 'cash_orders_log';

    public function getLogsByOrderId($orderId)
    {
        return $this-> where("orderId = {$orderId}")-> select();
//        $sql = "SELECT * FROM {$this->tableName} WHERE orderId = ? ";
//        return $this->getConnection()->fetchAll($sql, array($orderId)) ? : array();
    }

    public function addLog($fields)
    {
        $affected = $this-> add($fields);
        if($affected <= 0){
            E("Insert cash_orders account error.");
        }
        return $affected;
//        $order = $this->getConnection()->insert($this->tableName, $fields);
//        if ($order <= 0) {
//            throw $this->createDaoException('Insert cash_orders account error.');
//        }
//        return $this->getOrderLog($this->getConnection()->lastInsertId());
    }

    public function getOrderLog($id)
    {
        return $this-> where("id = {$id}")-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

}