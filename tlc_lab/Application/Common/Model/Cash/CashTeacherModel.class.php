<?php

namespace Common\Model\Cash;

use Common\Model\Common\BaseModel;

class CashTeacherModel extends BaseModel {

    protected $tableName = 'cash_teacher';

    public function getCashTeacherSearch($parm) {
        return $this->where($parm)->select();
    }

    public function searchCashTeacherCount($parm) {
        return $this->where($parm)->count();
    }

    public function searchCashTeacherId($parm) {
        return $this->where($parm)->field('cashId')->select();
    }

    public function getCashTeacherInfo($id) {
        return $this->where(array('id' => $id))->find();
    }

    public function updCashTeacher($id, $data) {
       return $this->where(array('id' => $id))->save($data);
    }

    public function getCashCashIdInfo($cashId) {
        return $this->where(array('cashId' => $cashId))->find();
    }

    public function addCashTeaher($data) {
        return $this->add($data);
    }

    public function searchCashTeacher($conditions, $orderBy, $start, $limit) {
        return $this->where($conditions)
                        ->order("{$orderBy[0]} {$orderBy[1]}")
                        ->limit("{$start},{$limit}")
                        ->select();
    }

}