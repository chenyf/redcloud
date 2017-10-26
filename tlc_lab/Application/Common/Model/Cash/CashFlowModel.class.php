<?php
namespace Common\Model\Cash;

use Common\Model\Common\BaseModel;

class CashFlowModel extends BaseModel
{
    protected $tableName = 'cash_flow';

    public function getFlow($id)
    {
        return $this-> where("id = {$id}")-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getFlowBySn($sn)
    {
        $map['sn'] = $sn;
        return $this-> where($map)-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE sn = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($sn)) ? : null;
    }

    public function getFlowByOrderSn($orderSn)
    {
        $map['orderSn'] = $orderSn;
        return $this-> where($map)-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE orderSn = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($orderSn)) ? : null;
    }

    public function searchFlows($conditions, $orderBy, $start, $limit)
    {
//        return $this-> where($conditions)->order("$orderBy[0] $orderBy[1]")->limit($start, $limit);
        $builder = $this->createFlowQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }
    
    public function analysisAmount($conditions)
    {
//        $builder = $this->createFlowQueryBuilder($conditions);
//        return $this-> where($builder)-> sum("amount");
        $builder = $this->createFlowQueryBuilder($conditions)
            ->select('sum(amount)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchFlowsCount($conditions)
    {
//        return $this-> where($conditions)-> count("id");
        $builder = $this->createFlowQueryBuilder($conditions)
            ->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function addFlow($flow)
    {
        $affected = $this-> add($flow);
        if($affected <= 0){
            E("Insert cash flow error");
        }
        return $affected;
//        $affected = $this->getConnection()->insert($this->tableName, $flow);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert cash flow error.');
//        }
//        return $this->getFlow($this->getConnection()->lastInsertId());
    }

    public function updateFlow($flow)
    {
        return $this-> where("id = {$id}")-> save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
//        return $this->getFlow($id);
    }

    public function findUserIdsByFlows($type,$createdTime, $orderBy, $start, $limit)
    {
        $sql="SELECT  userId,sum(amount) as amounts FROM `cash_flow` where ".($type ? "`type`={$type} and " : "" )." createdTime >= {$createdTime} group by userId  order by amounts {$orderBy} limit {$start},{$limit} ";
        return $this->query($sql);
//        $sql="SELECT  userId,sum(amount) as amounts FROM `cash_flow` where ".($type ? "`type`=? and " : "" )." createdTime >= ? group by userId  order by amounts {$orderBy} limit {$start},{$limit} ";
        
//        return $this->getConnection()->fetchAll($sql,$type ? array($type,$createdTime) : array($createdTime) ) ? : array() ;
    }

    public function findUserIdsByFlowsCount($type,$createdTime)
    {
        $sql="SELECT count( distinct userId) cnt  FROM `cash_flow` where ".($type ? "`type`={$type} and " : "" )." createdTime >= {$createdTime} ";
        $rs = $this-> query($sql);
        return isset($rs[0]['cnt']) ? $rs[0]['cnt'] : 0;
//        $sql="SELECT count( distinct userId)  FROM `cash_flow` where ".($type ? "`type`=? and " : "" )." createdTime >= ? ";
      
//        return $this->getConnection()->fetchColumn($sql,$type ? array($type,$createdTime) : array($createdTime)) ? : 0 ;
    }

    private function createFlowQueryBuilder($conditions)
    {

        $conditions = array_filter($conditions);
        return $this->createDynamicQueryBuilder($conditions)
            ->from($this->tableName, 'cash_flow')
            ->andWhere('userId = :userId')
            ->andWhere('type = :type')
            ->andWhere('cashType = :cashType')
            ->andWhere('status = :status')
            ->andWhere('category = :category')
            ->andWhere('sn = :sn')
            ->andWhere('name = :name')
            ->andWhere('orderSn = :orderSn')
            ->andWhere('createdTime >= :startTime') 
            ->andWhere('createdTime < :endTime');
    }

}