<?php
namespace Common\Model\System;
use Think\Model;

class CacheModel extends \Common\Model\Common\BaseModel{
    
    protected $tableName = "cache";
    
    public function getCache($id){
        return $this->where("id = {$id}")->find();
    }
    
    public function addCache($cache){
        return $this->add($cache);
    }
    
    public function findCachesByNames(array $names){
        if(empty($names)){
            return array();
        }
        $name = implode(",", $names);
        return $this->where("name in ('{$name}')")->select();
    }
    
    public function deleteCacheByName($name){
        return $this->where(" name = '{$name}'")->delete();
    }
    
    public function deleteAllCache(){
       return $this->delete();
    }
    
}
?>
