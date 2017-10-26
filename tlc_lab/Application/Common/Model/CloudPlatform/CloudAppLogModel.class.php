<?php

namespace Common\Model\CloudPlatform;
use Common\Model\Common\BaseModel;
use Common\Model\Common\CloudPlatform;

class CloudAppLogModel extends BaseModel
{

    protected $tableName = 'cloud_app_logs';

    public function getLog($id)
    {
        return $this-> where("id = {$id}")-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getLastLogByCodeAndToVersion($code, $toVersion)
    {
        $map['code'] = array("eq", $code);
        $map['toVersion'] = array("eq", $toVersion);
        return $this-> where($map)-> order("createdTime desc")-> find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE code = ? AND toVersion = ? ORDER BY createdTime DESC LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($code, $toVersion));
    }

    public function findLogs($start, $limit)
    {
        $this->filterStartLimit($start, $limit);
        return $this-> order("createdTime desc")-> limit($start, $limit );
//        $sql = "SELECT * FROM {$this->tableName} ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql); 
    }

    public function findLogCount()
    {
        return $this-> count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName}";
//        return $this->getConnection()->fetchColumn($sql);
    }

    public function addLog($log)
    {
        $affected = $this-> add($log);
        if($affected <= 0){
            E("Insert app log error");
        }
        return $affected;
//        $affected = $this->getConnection()->insert($this->tableName, $log);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert app log error.');
//        }
//        return $this->getLog($this->getConnection()->lastInsertId());
    }

}