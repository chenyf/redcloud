<?php
namespace Common\Model\User;
use Common\Model\User\CollegeServiceLocalModel;
use Common\Model\User\CollegeServiceIfaceModel;
use Think\Model;
use Common\Model\Common\BaseModel;

class CollegeServiceModel extends BaseModel
{

    protected function getCollegeLocalService()
    {
        return createService('User.CollegeServiceLocalModel');
    }

    protected function getCollegeIfaceService()
    {
        return createService('User.CollegeServiceIfaceModel');
    }

    public function getTeacherCollegeList(){
        if(C('if_iface')){
            return $this->getCollegeIfaceService()->getTeacherCollegeList();
        }else{
            return $this->getCollegeLocalService()->getTeacherCollegeList();
        }
    }

    public function getStudentCollegeList(){
        if(C('if_iface')){
            return $this->getCollegeIfaceService()->getStudentCollegeList();
        }else{
            return $this->getCollegeLocalService()->getStudentCollegeList();
        }
    }

    public function getDepartmentList($collegeId){
        if(C('if_iface')){
            return $this->getCollegeIfaceService()->getDepartmentList($collegeId);
        }else{
            return $this->getCollegeLocalService()->getDepartmentList($collegeId);
        }
    }

    public function getMajorList($collegeId){
        if(C('if_iface')){
            return $this->getCollegeIfaceService()->getMajorList($collegeId);
        }else{
            return $this->getCollegeLocalService()->getMajorList($collegeId);
        }
    }

}