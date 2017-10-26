<?php

namespace Common\Model\Course;

use Think\Model;
use Common\Model\Common\BaseModel;
class UserVpsModel extends BaseModel
{

    protected $tableName = 'user_vps';

    public function selectUserVps($condition){
        return $this->where($condition)->select() ? : array();
    }

    public function getUserVpsById($id){
        return $this->where("id = {$id}")->find() ? : null;
    }

    public function getUserVpsByUserId($userId){
        return $this->where("userId = {$userId}")->find() ? : null;
    }

    public function getUserVpsByVpsId($vpsId){
        $map = array("vpsId" => $vpsId);
        return $this->where($map)->find() ? : null;
    }

    public function addUserVps($userId,$vpsId){
        $map = array(
            'userId'    =>  $userId,
            'vpsId'    =>  $vpsId,
            'createTime'    =>  time()
        );
        return $this->add($map);
    }

    public function selectVpsIdList(){
        return $this->field("DISTINCT vpsId")->select() ? : array();
    }

}