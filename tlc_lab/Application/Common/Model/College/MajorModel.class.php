<?php
namespace Common\Model\College;
use Think\Model;
use Common\Model\Common\BaseModel;

class MajorModel extends BaseModel
{
    protected $tableName = 'majors';

    public function findAll(){
        return $this->select() ? : array();
    }

    public function selectAll($where){
        return $this->where($where)->select() ? : array();
    }
}