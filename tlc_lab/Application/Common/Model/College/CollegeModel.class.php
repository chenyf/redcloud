<?php
namespace Common\Model\College;
use Think\Model;
use Common\Model\Common\BaseModel;

class CollegeModel extends BaseModel
{

    protected $tableName = 'colleges';

    public function findAll(){
        return $this->select() ? : array();
    }

    public function selectAll($where){
        return $this->where($where)->select() ? : array();
    }

}