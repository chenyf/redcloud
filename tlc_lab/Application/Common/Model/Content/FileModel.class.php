<?php

namespace Common\Model\Content;
use Common\Model\Common\BaseModel;

class FileModel extends BaseModel
{
    protected $tableName = 'file';

	public function getFile($id)
	{
            $where['id'] = $id;
            return $this-> where($where)-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
	}

    public function getFilesByIds($ids)
    {
        if(empty($ids)) { 
            return array(); 
        }
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
        $marks = implode(",", $ids);
        $map['id']  = array('in',$marks);
        return $this-> where($map)-> select();
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";

//        return $this->getConnection()->fetchAll($sql, $ids);
    }

	public function findFiles($start, $limit)
	{
            return $this-> order("createTime desc")-> order($start, $limit)->select();
//        $this->filterStartLimit($start, $limit);
//		$sql = "SELECT * FROM {$this->tableName} ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql);
	}

	public function findFileCount()
	{
            return $this->count();
//		$sql = "SELECT COUNT(*) FROM {$this->tableName}";
//        return $this->getConnection()->fetchColumn($sql);
	}

	public function findFilesByGroupId($groupId, $start, $limit)
	{
            $map['groupId'] = $groupId;
            return $this-> where($map)-> order("createTime desc")-> limit($start, $limit)-> select();
//        $this->filterStartLimit($start, $limit);
//		$sql = "SELECT * FROM {$this->tableName} WHERE groupId = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($groupId));
	}

	public function findFileCountByGroupId($groupId)
	{
            $map['groupId'] = $groupId;
            return $this->where($map)-> count();
//		$sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  groupId = ?";
//        return $this->getConnection()->fetchColumn($sql, array($groupId));
	}

	public function addFile($file)
	{
            $id = $this-> add($file);
            if($id <= 0) E('Insert file error.');
            return $this->getFile($id);
            
//        if ($this->getConnection()->insert($this->tableName, $file) <= 0) {
//            throw $this->createDaoException('Insert file error.');
//        }
//        return $this->getFile($this->getConnection()->lastInsertId());
	}

	public function deleteFile($id)
	{
            $where['id'] = $id;
            return $this-> where($where)->delete();
//		return $this->getConnection()->delete($this->tableName, array('id' => $id));
	}

}