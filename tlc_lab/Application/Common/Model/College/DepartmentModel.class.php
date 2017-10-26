<?php
namespace Common\Model\College;
use Think\Model;
use Common\Model\Common\BaseModel;

class DepartmentModel extends BaseModel
{

    protected $tableName = 'departments';

    public function findAll(){
        return $this->select() ? : array();
    }

    public function selectAll($where){
        return $this->where($where)->select() ? : array();
    }

}