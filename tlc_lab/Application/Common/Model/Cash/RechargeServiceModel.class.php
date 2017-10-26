<?php
namespace Common\Model\Cash;
use Common\Model\Common\BaseModel;

class RechargeServiceModel extends BaseModel
{

    private $payWayDict = array(
        "ems" => "邮政",
        "bank" => "银行",
        "other" => "其他",
    );

    public function getPayWayDict(){
        return $this->payWayDict;
    }

    public function getRecharge($id){
        return $this->getRechargeDao()->getRecharge($id);
    }

    public function getRechagerHandled($id){
        return $this->handleRecharge($this->getRecharge($id));
    }

    public function updateRecharge($id, $fields){
        return $this->getRechargeDao()->updateRecharge($id, $fields);
    }

    private function getRechargePictureDao() {
        return $this->createDao('Cash.RechargePictureModel');
    }

    private function getRechargeDao() {
        return $this->createDao('Cash.RechargeModel');
    }

    private function getUserService(){
        return createService("User.UserService");
    }

    public function addRecharge($recharge){
        return $this->getRechargeDao()->addRecharge($recharge);
    }

    public function addRechargePictureList($pictureList){
        return $this->getRechargePictureDao()->addRechargePictureList($pictureList);
    }

    public function searchRechargeList($conditions, $orderBy, $start, $limit) {
        $conditions = $this->SearchConditions($conditions);
        $list = $this->getRechargeDao()->searchRechargeList($conditions, $orderBy, $start, $limit);
        return $this->handleRechargeList($list);
    }

    public function searchRechargeListCount($conditions) {
        $conditions = $this->SearchConditions($conditions);
        return $this->getRechargeDao()->searchRechargeCount($conditions);
    }

    private function handleRecharge($recharge){
        if(empty($recharge)){
            return null;
        }

        $recharge["operator"] = $this->getUserService()->findUserSimple($recharge["userId"]);
        $recharge["after_balance"] = $recharge["before_balance"] + $recharge["amount"];
        $recharge["pay_mode"] = $recharge["pay_way"];
        $recharge["pay_way"] = $this->payWayDict[$recharge["pay_way"]];
        if($recharge["pay_mode"] == "other"){
            if(!empty($recharge["pay_way_other"])){
                $recharge["pay_way"] = $recharge["pay_way_other"];
            }
        }
        $recharge["pictures"] = $this->getRechargePictureDao()->selectRechargePictures($recharge["id"]);

        $recharge["pictures"] = array_map(function($item){
            $item["small_picture"] = getParameter('wyzc.upload.public_url_path') . '/' . str_replace('public://', '', $item["small_picture"]);
            $item["original_picture"] = getParameter('wyzc.upload.public_url_path') . '/' . str_replace('public://', '', $item["original_picture"]);
            return $item;
        },$recharge["pictures"]);

        return $recharge;
    }

    private function handleRechargeList($recharges){
        return array_map(function($re){
            return $this->handleRecharge($re);
        },$recharges);
    }

    private function SearchConditions($conditions) {
        $data = array();
        if (!empty($conditions['org'])) {
            $data['orgId'] = intval($conditions['org']);
        }

        if (array_key_exists("deleted",$conditions)) {
            $data['deleted'] = $conditions["deleted"];
        }

        if (array_key_exists("status",$conditions)) {
            $data['status'] = $conditions["status"];
        }

        if(!empty($conditions['startDateTime']) || !empty($conditions['endDateTime'])) {
            $data['createdTime'] = array(array('GT', strtotime($conditions['startDateTime'])), array('LT', strtotime($conditions['endDateTime'])));
        }

        if($conditions['startAmount'] <= 0 and $conditions['endAmount'] > 0){
            $data['amount'] = array(array('ELT', strtotime($conditions['endAmount'])));
        }else if($conditions['startAmount'] > 0 and $conditions['endAmount'] <= 0){
            $data['amount'] = array(array('EGT', strtotime($conditions['startAmount'])));
        }else if($conditions['startAmount'] > 0 and $conditions['endAmount'] > 0){
            $data['amount'] = array(array('EGT', strtotime($conditions['startAmount'])), array('ELT', strtotime($conditions['endAmount'])));
        }

        return $data;
    }


}