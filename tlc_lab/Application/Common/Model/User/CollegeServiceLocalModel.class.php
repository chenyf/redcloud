<?php
namespace Common\Model\User;
use Common\Lib\ArrayToolkit;
use Think\Model;
use Common\Model\Common\BaseModel;


class CollegeServiceLocalModel extends BaseModel
{

    public function getProfileDao(){
        return $this->createDao("User.UserProfile");
    }

    public function getCollegeDao(){
        return $this->createDao("College.College");
    }

    public function getDeparmentDao(){
        return $this->createDao("College.Deparment");
    }

    public function getMajorDao(){
        return $this->createDao("College.Major");
    }

    public function getTeacherCollegeList(){
        return $this->getCollegeDao()->findAll();
//        return $this->getProfileDao()->getFieldList('college',array('role' => 'teacher'));
    }

    public function getStudentCollegeList(){
        return $this->getCollegeDao()->findAll();
//        return $this->getProfileDao()->getFieldList('college',array('role' => 'student'));
    }

    public function getDepartmentList($collegeId){
        return $this->getDeparmentDao()->selectAll(array('college_id' => $collegeId));
    }

    public function getMajorList($collegeId){
        $xi_list = $this->getDepartmentList($collegeId);
        $xi_id_list = ArrayToolkit::column($xi_list,'department_id');

        $xi_id_instr = implode(',',$xi_id_list);

        return $this->getMajorDao()->selectAll("department_id in ({$xi_id_instr})");
    }

    public function getMajorListByXiId($departmentId){
        return $this->getMajorDao()->selectAll(array('department_id' => $departmentId));
    }

}