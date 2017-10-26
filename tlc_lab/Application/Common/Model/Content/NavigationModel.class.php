<?php
namespace Common\Model\Content;

use Common\Model\Common\BaseModel;

class NavigationModel extends BaseModel
{
    protected $tableName = 'navigation';

    #add by qzw 2015-09-08
    /*public function __construct($name = '', $tablePrefix = '', $connection = '') {
        parent::__construct($name, $tablePrefix, $connection);
        $this->setCommUseOWebPriv();
    }*/
    
    public function getNavigationsCountByType($type)
    {
        $map['type'] = $type;
        return $this-> where($map)-> count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  type = ?";
//        return $this->getConnection()->fetchColumn($sql, array($type));
    }

    public function getNavigation($id)
    {
        return $this-> where("id = {$id}")-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function addNavigation($navigation)
    {
        $affected = $this-> add($navigation);
        if($affected <= 0){
            E("Insert navigation error");
        }
        return $affected;
//        $affected = $this->getConnection()->insert($this->tableName, $navigation);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert navigation error.');
//        }

//        return $this->getConnection()->lastInsertId();
    }

    public function updateNavigation($id, $fields)
    {
        return $this-> where("id = {$id}")-> save($fields);
//        return $this->getConnection()->update($this->tableName, $fields, array('id' => $id));
    }

    public function deleteNavigation($id)
    {
        return $this-> where("id = {$id}")-> delete();
//        return ($this->getConnection()->delete($this->tableName, array('id' => $id))); 
    }

    public function deleteNavigationByParentId($parentId)
    {
        return $this-> where("parentId = {$parentId}")-> delete();
//        return ($this->getConnection()->delete($this->tableName, array('parentId' => $parentId))); 
    }
    
    public function getNavigationsCount()
    {
        return $this-> count();
//        $sql = "SELECT COUNT(*) FROM {$this->tableName}";
//        return $this->getConnection()->fetchColumn($sql, array());
    }

    public function findNavigationsByType($type, $start, $limit)
    {
        $map['type'] = $type;
        return $this-> where($map)-> order("sequence asc ")-> limit($start, $limit)->select();
//        $this->filterStartLimit($start, $limit);
//        $sql = "SELECT * FROM {$this->tableName} WHERE type = ? ORDER BY sequence ASC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array($type));
    }

    public function findNavigations($start, $limit)
    {
        return $this-> order("sequence asc")-> limit($start, $limit )-> select();
//        $this->filterStartLimit($start, $limit);
//        $sql = "SELECT * FROM {$this->tableName} ORDER BY sequence ASC LIMIT {$start}, {$limit}";
//        return $this->getConnection()->fetchAll($sql, array());
    }
    /**
     * 获得院校新闻
     * @author 谈海涛 2016-03-04
     */
    public function getNewsNavigations($news)
    {
        $map['name'] = $news;
        $map['type'] = 'top';
        $map['isOpen'] = 1;
        return $this-> where($map)-> find();
    }

}