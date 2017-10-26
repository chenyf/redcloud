<?php
/**
 * 用户设备
 * @author fubaosheng 2015-05-14
 */
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class UserDeviceServiceModel extends BaseModel{
    
    protected $tableName = 'user_device';
    
    public function getUserAllDevice($uid){
        if(is_array($uid))
            $where['uid'] = array('in',implode (",", $uid));
        else
            $where['uid'] = array('eq',$uid);
        return $this->where($where)->field("uid,ctype,channel_id,user_id,device_id")->order("utime desc")->select() ? : array();
    }
    
    public function getUserDeviceByType($uid,$type){
        if(is_array($uid))
            $where['uid'] = array('in',implode (",", $uid));
        else
            $where['uid'] = array('eq',$uid);
        if($type == "android")
             $where['ctype'] = array("in",'1,3');
        else
            $where['ctype'] = array("in",'2,4');
        return $this->where($where)->field("uid, ctype,channel_id,user_id,device_id")->order("utime desc")->select() ? : array();
    }
    
    public function getAllUserDevice($type){
        $where = '1 and uid>0';
        if($type && $type != 'all') $where .= ' and ctype in ('.($type == 'android' ? '1,3' : '2,4').')';
        $sql = $this->where($where)->order("utime desc")->select(false);
        return $this->table($sql." aa")->field("uid,ctype,channel_id,user_id,device_id")->group("uid,ctype")->select() ? : array();
    }
    
    public function getAllUidUserDevice($type){
        return $this->field("uid")->select() ? : array();
    }
    
    public function getUserAndroidDevice($uid){
        return $this->where("uid = {$uid} and ctype = 1")->order("ctime desc")->find() ? : array();
    }
    
    public function getUserIosDevice($uid){
        return $this->where("uid = {$uid} and ctype = 2")->order("ctime desc")->find() ? : array();
    }
    
    public function getUserIdArr(){
        $where['uid'] = array('gt',0);
        $userArr = $this->where($where)->field("distinct uid")->select() ? : array();
        $idArr = array();
        if(!empty($userArr)){
            foreach ($userArr as $user) {
                $idArr[$user['uid']] = $user['uid'];
            }
        }
        $idArr = array_values(array_unique($idArr));
        return $idArr;
    }
}
?>
