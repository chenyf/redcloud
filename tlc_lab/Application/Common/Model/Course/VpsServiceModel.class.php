<?php

namespace Common\Model\Course;

use Common\Lib\ArrayToolkit;
use Common\Model\Common\BaseModel;
class VpsServiceModel  extends BaseModel
{

    private function getUserVpsDao(){
        return $this->createService('Course.UserVpsModel');
    }

    public function getMyVps(){
        $uid = $this->getCurrentUser()->id;
        return $this->getUserVpsDao()->getUserVpsByUserId($uid);
    }

    public function getVps($vpsId){
        return $this->getUserVpsDao()->getUserVpsByVpsId($vpsId);
    }

    //为用户添加虚拟机记录
    public function addMyVps($vpsId){
        if(!empty($this->getMyVps())){
            return "你已经选择过虚拟机，不能重复选择";
        }

        if(!empty($this->getVps($vpsId))){
            return "该虚拟机已经被使用，不能再次被选择";
        }

        $uid = $this->getCurrentUser()->id;

        if($this->getUserVpsDao()->addUserVps($uid,$vpsId)){
            return "";
        }

        return "添加虚拟机失败！";
    }

    //为获取已经被使用虚拟机ID列表
    public function selectVpsIdList(){
        $list = $this->getUserVpsDao()->selectVpsIdList();
        return ArrayToolkit::column($list,'vpsId');
    }

}
