<?php
/*
 * 数据层
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace  Common\Model\Taxonomy;

use Common\Model\Common\BaseModel;

class LocationModel extends BaseModel
{
    protected $tableName = 'location';

	public function getConnection() {
		return $this;
	}

    public function getLocation($id)
    {
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        return $this->getConnection()->where(array('id'=>$id))->find();
    }

    public function findLocationsByIds(array $ids)
    {
        if(empty($ids)){ return array(); }
        $marks = implode(',',$ids);
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
	    $where['id'] = array('in',$marks);
        return $this->getConnection()->where($where)->select();
    }

    public function findAllLocations()
    {
//        $sql = "SELECT * FROM {$this->tableName} ORDER BY createdTime DESC";
        return $this->getConnection()->order('createdTime DESC')->select();
    }

}