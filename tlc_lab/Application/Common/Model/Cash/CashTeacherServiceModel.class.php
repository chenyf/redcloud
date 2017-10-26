<?php

namespace Common\Model\Cash;

use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class CashTeacherServiceModel extends BaseModel {

    public function getCashTeacherSearch($parm) {
        return $this->getCashTeacherDao()->getCashTeacherSearch($parm);
    }

    public function searchCashTeacherCount($parm) {
        return $this->getCashTeacherDao()->searchCashTeacherCount($parm);
    }

    public function searchCashTeacherId($parm) {
        $data = $this->getCashTeacherDao()->searchCashTeacherId($parm);
        return ArrayToolkit::column($data, 'cashId');
    }

    public function searchCashTeacher($conditions, $sort = 'latest', $start, $limit) {
        $orderBy = array();
        if ($sort == 'latest') {
            $orderBy = array('createdTime', 'DESC');
        } elseif ($sort == 'early') {
            $orderBy = array('createdTime', 'ASC');
        } else {
            $orderBy = array('createdTime', 'DESC');
        }
        return $this->getCashTeacherDao()->searchCashTeacher($conditions, $orderBy, $start, $limit);
    }

    public function addCashTeaher($data) {
        return $this->getCashTeacherDao()->addCashTeaher($data);
    }

    public function getCashTeacherInfo($id) {
        return $this->getCashTeacherDao()->getCashTeacherInfo($id);
    }

    public function updCashTeacher($id, $data) {
        return $this->getCashTeacherDao()->updCashTeacher($id, $data);
    }

    public function getCashCashIdInfo($cashId) {
        return $this->getCashTeacherDao()->getCashCashIdInfo($cashId);
    }

    protected function getCashTeacherDao() {
        return $this->createService('Cash.CashTeacherModel');
    }

}