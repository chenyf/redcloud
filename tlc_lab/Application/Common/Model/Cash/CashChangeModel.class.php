<?php
namespace Common\Model\Cash;

use Common\Model\Common\BaseModel;

class CashChangeModel extends BaseModel
{
    protected $tableName = 'cash_change';

    public function getChange($id)
    {
        return $this-> where("id = {$id}")-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getChangeByUserId($userId, $lock = false)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE userId = $userId LIMIT 1" . ($lock ? ' FOR UPDATE' : '');
        return $this->query($sql)? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? LIMIT 1" . ($lock ? ' FOR UPDATE' : '');
//        return $this->getConnection()->fetchAssoc($sql, array($userId)) ? : null;
    }

    public function addChange($fields)
    {
        $affected = $this-> add($fields);
        if($affected <= 0){
            E("Insert cash account error");
        }
        return $affected;
//        $affected = $this->getConnection()->insert($this->tableName, $fields);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert cash change error.');
//        }
//        return $this->getChange($this->getConnection()->lastInsertId());
    }

    public function waveCashField($id, $value)
    {
        $sql = "UPDATE {$this->tableName} SET amount = amount + {$value} WHERE id = {$id} LIMIT 1";
        return $this-> query($sql);
//        $sql = "UPDATE {$this->tableName} SET amount = amount + ? WHERE id = ? LIMIT 1";
        
//        return $this->getConnection()->executeQuery($sql, array($value, $id));
    }

}