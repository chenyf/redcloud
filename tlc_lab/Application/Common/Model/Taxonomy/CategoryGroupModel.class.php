<?php
/*
 * 数据层
 * @package    
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace  Common\Model\Taxonomy;

use Common\Model\Common\BaseModel;

class CategoryGroupModel extends BaseModel
{
    protected $tableName = 'category_group';


    public function getConnection() {
        #add by qzw 2015-09-08
        $this->setCommUseOWebPriv();
            return $this;
    }

    public function getGroup($id)
    {
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        return $this->getConnection()->where(array('id'=>$id))->find();
    }

    public function findGroupByCode($code)
    {        
//        $sql = "SELECT * FROM {$this->tableName} WHERE code = ? LIMIT 1";
        return $this->getConnection()->where(array('code'=>$code))->find();
    }

    public function findGroups($start, $limit)
    {
        $this->filterStartLimit($start, $limit);
//        $sql = "SELECT * FROM {$this->tableName} LIMIT {$start}, {$limit}";
        return $this->getConnection()->limit($start,$limit)->select() ? : array();
    }

    public function findAllGroups()
    {
//        $sql = "SELECT * FROM {$this->tableName}";
        return $this->getConnection()->select() ? : array();
    }

    public function addGroup(array $group)
    {
        $affected = $this->getConnection()->add($group);
        if ($affected <= 0) {
           E('Insert group error.');
        }
        return $this->getGroup($affected);
    }

    public function deleteGroup($id)
    {
        return $this->getConnection()->where(array('id'=>$id))->delete();
    }
}