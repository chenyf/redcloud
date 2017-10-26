<?php
/**
 * 用户申请老师
 * @author fubaosheng 2015-12-16
 */
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class UserApplyServiceModel extends BaseModel{
    
    private function getUserApplyDao(){
        return $this->createDao("User.UserApply");
    }
    
    public function getUserLastApply($uid){
        $apply = $this->getUserApplyDao()->getUserLastApply($uid);
        return $apply;
    }
    
    public function getApply($id){
        $apply = $this->getUserApplyDao()->getApply($id);
        return $apply;
    }
    
    public function addUserApply($data){
        $apply = array();
        $apply["applyUid"] = $data["uid"];
        $apply["applyTm"] = time();
        $apply["applyName"] = $data["applyName"] ? : "";
        $apply["applyMobile"] = $data["applyMobile"] ? : "";
        $apply["applyEmail"] = $data["applyEmail"] ? : "";
        $apply["applyCateid"] = $data["applyCateid"] ? : 0;
        $apply["applyRemark"] = $data["applyRemark"] ? : "";
        return $this->getUserApplyDao()->addUserApply($apply);
    }
    
    public function removeApply($id){
        return $this->getUserApplyDao()->removeApply($id);
    }

    public function searchApplyCount(array $conditions){
        return $this->getUserApplyDao()->searchApplyCount($conditions);
    }
    
    public function searchApplys(array $conditions, array $orderBy, $start, $limit){
        return $this->getUserApplyDao()->searchApplys($conditions, $orderBy, $start, $limit);
    }
    
    public function checkApply($data) {
        $user = $this->getCurrentUser();

        $applyData = array();
        $applyData["opUid"] = intval($user["id"]);
        $applyData["opTm"] = time();
        $applyData["status"] = intval($data['status']) == 1 ? 1 : 2;
        $applyData["opRemark"] = $data["opRemark"];

        return $this->getUserApplyDao()->checkApply($data["id"],$applyData);
    }
    
}
?>
