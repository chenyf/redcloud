<?php
namespace Common\Model\Content;
use Common\Model\Common\BaseModel;

class FileGroupModel extends BaseModel
{
    protected $tableName = 'file_group';

    #add by qzw 2015-09-08
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        parent::__construct($name, $tablePrefix, $connection);
    }
    
	public function getGroup($id)
	{
            return $this-> where("id = {$id}")-> find()? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
	}

	public function findGroupByCode($code)
	{
            return $this->where("code='{$code}'")-> find()? : array();
//        $sql = "SELECT * FROM {$this->tableName} WHERE code = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($code)) ? : null;
	}

	public function findAllGroups()
	{
            return $this-> select();
//		$sql = "SELECT * FROM {$this->tableName}";
//        return $this->getConnection()->fetchAll($sql);
	}

	public function addGroup($group)
	{
            if($this->add($group)){
                E("Insert file group error");
            }
            return $this-> add($group);
//        if ($this->getConnection()->insert($this->tableName, $group) <= 0) {
//            throw $this->createDaoException('Insert file group error.');
//        }
//        return $this->getGroup($this->getConnection()->lastInsertId());
	}

	public function deleteGroup($id)
	{
            return $this-> where("id = {$id}")-> delete();
//		return $this->getConnection()->delete($this->tableName, array('id' => $id));
	}

}