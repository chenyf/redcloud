<?php

namespace Common\Model\CloudPlatform;
use Common\Model\Common\BaseModel;

class CloudAppModel extends BaseModel 
{
    protected $tableName = 'cloud_app';

    public function getApp($id)
    {
        return $this-> where("id = {$id}")-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getAppByCode($code)
    {
        $map['code'] = $code;
        return $this->where($map)->find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE code = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($code)) ? : null;
    }

    public function findAppsByCodes(array $codes)
    {
        if (empty($codes)) { 
            return array(); 
        }

//        $marks = str_repeat('?,', count($codes) - 1) . '?';
        $marks = implode(",", $codes);
        $map['code'] = array("in", $marks);
        return $this-> where($map)->select();
        
//        $sql ="SELECT * FROM {$this->tableName} WHERE code IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $codes);
    }

    public function findApps($start, $limit)
    {
        return $this-> order("installedTime desc")-> limit($start, $limit)-> select();
//         $this->filterStartLimit($start, $limit);
//        $sql = "SELECT * FROM {$this->tableName} ORDER BY installedTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql);       
    }

    public function findAppCount()
    {
        return $this->count(); 
//        $sql = "SELECT COUNT(*) FROM {$this->tableName}";
//        return $this->getConnection()->fetchColumn($sql);
    }

    public function addApp($App)
    {
        $affected = $this ->add($App);
        if($affected <= 0){
            E("Insert App error");
        }
        return $affected;
//        $affected = $this->getConnection()->insert($this->tableName, $App);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert App error.');
//        }
//        return $this->getApp($this->getConnection()->lastInsertId());
    }

    public function updateApp($id,$App)
    {
        return $this-> where("id = {$id}")-> save($App);
//        $this->getConnection()->update($this->tableName, $App, array('id' => $id));
//        return $this->getApp($id);
    }

	public function deleteApp($id)
	{
            return $this-> where("id = {$id}")-> delete();
//        return $this->getConnection()->delete($this->tableName, array('id' => $id));
	}
/**
 * $code    更新条件
 * version  要更新的字段
 */
    public function updateAppVersion($code,$version)
    {
        $this->where("code = {$code}") ->save($version);
//      $this->getConnection()->update($this->tableName, $version, array('code' => $code));
        return true;
    }
    
    public function updateAppFromVersion($code,$fromVersion)
    {
        $this-> where("code =  {$code}")-> save($fromVersion);
//        $this->getConnection()->update($this->tableName, $fromVersion, array('code' => $code));
        return true; 
    }
}