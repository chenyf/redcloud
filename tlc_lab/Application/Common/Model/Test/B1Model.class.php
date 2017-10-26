<?php
/**
 * 系统设置model
 *
 * @author 钱志伟 2015-03-24
 */

namespace Common\Model\Test;
use \Common\Model\Common\BaseModel;

class B1Model extends BaseModel {
    protected $tableName = 'file';

    public function out()
    {
        $this->select();
        echo __FILE__,PHP_EOL;
    }    
    
    public function out2($conditions){
//        $where = $this->_createSearchQueryBuilder($conditions);
//        return $this->where($where)->count("id");
        $builder = $this->_createSearchQueryBuilder($conditions)->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    private function _createSearchQueryBuilder($conditions){
        return $this->createDynamicQueryBuilder($conditions)
            ->from($this->tableName, 'mmmmmmmmmm');
    }
}
