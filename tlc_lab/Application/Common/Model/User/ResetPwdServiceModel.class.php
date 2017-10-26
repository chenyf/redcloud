<?php
namespace Common\Model\User;

use Common\Model\Common\BaseModel;
use Think\RedisModel;
/**
 *重置密码服务model
 * @author fubaosheng 2015-10-30
 */
class ResetPwdServiceModel extends BaseModel {
    private $redisConn = false;
    
    public function __construct() {
        parent::__construct();
        $this->initRedis();
    }
    
    public function initRedis(){
        if($this->redisConn!=false) return false;
        $this->redisConn = RedisModel::getInstance(C('REDIS_DIST.USER_RECORD_CLASS'));
    }

    public function getUserImportKey($uid){
        return C("WEBSITE_CODE")."_".date("Ymd_His")."_"."{$uid}";
    }
    
    public function getUserImportListKey(){
        return C("WEBSITE_CODE")."_user_import_list";
    }
    
    public function getResetPwdTaskListKey(){
        return C("WEBSITE_CODE")."_reset_pwd_task_list";
    }
    
    public function getResetPwdKey($uid){
        return C("WEBSITE_CODE")."_reset_pwd_".time()."_".$uid;
    }
    
    public function getTaskUserList($key){
        return $key."_user_list";
    }
    
    public function getTaskUserKey($key,$uid){
        return $key."_user_".$uid;
    }

    public function setUserImportKey($uid,$user=array(),$fileName=''){
        if(!$uid || empty($fileName)) return false;
        $userImportKey = $this->getUserImportKey($uid);
        $userInfo = createService("User.UserModel")->getUser($uid);
        $count = count($user);
        $name = date("m/d H:i:s")." [发送密码数:{$count}] -".$fileName;
        $r = $this->redisConn->hashSet($userImportKey, array('uid'=>$uid,'time'=>time(),'num'=>$count,'user'=>json_encode($user),'userPwd'=>json_encode(array()),'name'=>$name));
        if($r) $this->redisConn->zAdd($this->getUserImportListKey(),getMicroTm(),$userImportKey);
        return $r ? $userImportKey : false;
    }
    
    public function setUserImportUser($key,$uid,$pwd){
        if(empty($uid) || empty($key) || empty($pwd)) return false;
        $user = $this->getRecoedUser($key);
        array_push($user, $uid);
        $user = array_unique($user);
        $count = count($user);
        $userPwd = $this->getRecoedUserPwd($key);
        $userPwd[$uid] = $pwd;
        $name = preg_replace("/\[发送密码数\:\d*\]/", "[发送密码数:{$count}]", $this->getRecoedName($key));
        return $this->redisConn->hashSet($key,array("user"=>json_encode($user),"num"=>$count,"name"=>$name,"userPwd"=>json_encode($userPwd)));
    }
    
    public function getUserImportList(){
        $userImportListKey = $this->getUserImportListKey();
        $userImportListArr = $this->redisConn->zRevrangeByScore($userImportListKey, '+inf','-inf', array());
        return $userImportListArr ? $this->getUserImportName($userImportListArr) : array();
    }
    
    public function getUserImportName($userImportListArr){
        $arr = array();
        foreach($userImportListArr as $key => $val){
            $arr[$key]['key'] = $val;
            $arr[$key]['name'] = $this->getRecoedName($val);
        }
        return array_values($arr);
    }
    
    public  function getResetPwdTaskList($limit = array()){
        $resetPwdTaskListKey = $this->getResetPwdTaskListKey();
        $limit = $limit ? array('limit'=>$limit) : array();
        $resetPwdTaskListKeyArr = $this->redisConn->zRevrangeByScore($resetPwdTaskListKey, '+inf','-inf', $limit);
        return $resetPwdTaskListKeyArr ? : array();
    }
    
    public  function getTaskUserListArr($key,$limit = array()){
        $taskUserListKey = $this->getTaskUserList($key);
        $limit = $limit ? array('limit'=>$limit) : array();
        $taskUserListArr = $this->redisConn->zRevrangeByScore($taskUserListKey, '+inf','-inf', $limit);
        return $taskUserListArr ? : array();
    }
    
    public function getTaskUserArr($key,$tm='-inf'){
        $taskUserListKey = $this->getTaskUserList($key);
        $taskUserListArr = $this->redisConn->zRangeByScore($taskUserListKey, $tm,'+inf', array());
        return $taskUserListArr ? : array();
    }
    
   public function getResetPwdTaskCount(){
        $resetPwdTaskListKey = $this->getResetPwdTaskListKey();
        return $this->redisConn->zCount($resetPwdTaskListKey, '-inf', '+inf') ? : 0;
   }
    
    /**
     * 0未开始，1进行中，2被中止，3已结束
     */
    public function setResetPwdKey($paramArr = array()){
        $options = array(
            'uid'  => 0,
            'type' => '',
            'recoedListArr' => array()
        );
        $options = array_merge($options,$paramArr);
        extract($options);
        
        if(!$uid) return false;
        if(!in_array($type, array('all','recoed'))) return false;
        
        $resetPwdKey = $this->getResetPwdKey($uid);
        $data = array('uid'=>$uid,'time'=>time(),'type'=>$type,'recoedList'=>json_encode($recoedListArr),'status'=>0,'startTm'=>0,'updateTm'=>0);
        $r = $this->redisConn->hashSet($resetPwdKey, $data);
        if($r) $this->redisConn->zAdd($this->getResetPwdTaskListKey(),getMicroTm(),$resetPwdKey);
        return $r ? true : false;
    }
    
    public function getResetPwdKeyInfo($key){
        if(empty($key)) return array();
        return $this->redisConn->hGetAll($key) ? : array();
    }
    
    public function getResetPwdTask($limit=array()){
        $arr = array();
        $resetPwdTaskList = $this->getResetPwdTaskList($limit);
        if($resetPwdTaskList){
            foreach ($resetPwdTaskList as $resetPwdTask) {
                $arr[$resetPwdTask] = $this->getResetPwdKeyInfo($resetPwdTask);
                $arr[$resetPwdTask]['key'] =  $resetPwdTask;
                if($arr[$resetPwdTask]['type'] == "all"){
                    $arr[$resetPwdTask]['count'] = createService("User.UserModel")->searchUserCount(array('switch'=>true,'siteSelect'=>C("WEBSITE_CODE")));
                }else{
                    $num = 0;
                    $names = array();
                    $arr[$resetPwdTask]['recoedList'] = json_decode($arr[$resetPwdTask]['recoedList'],true);
                    foreach ($arr[$resetPwdTask]['recoedList'] as $key => $value) {
                        $num+=intval(count($this->getRecoedUser($value)));
                        $names[$key] = $this->getRecoedName($value);
                    }
                    $arr[$resetPwdTask]['count'] = $num;
                    $arr[$resetPwdTask]['names'] = $names;
                }
            }
        }
        return array_values($arr);
    }
    
    public function isExistsKey($key){
        if(empty($key)) return false;
        return $this->redisConn->exists($key);
    }
    
    public function getResetPwdTaskStatus($key){
        if(empty($key)) return 0;
        return $this->redisConn->hashGet($key, 'status');
    }
    
    public function setResetPwdTaskStatus($key,$status){
        return $this->redisConn->hashSet($key, array('status'=>$status));
    }
    
    public function setResetPwdTaskStartTm($key){
        return $this->redisConn->hashSet($key, array('startTm'=>time()));
    }
    
    public function setResetPwdTaskUpdateTm($key){
        return $this->redisConn->hashSet($key, array('updateTm'=>time()));
    }
    
    public function getRecoedUser($key){
        if(empty($key)) return array();
        $user = $this->redisConn->hashGet($key,'user');
        return  $user ? json_decode($user,true) : array();
    }
    
    public function getRecoedUserPwd($key){
        if(empty($key)) return array();
        $userPwd = $this->redisConn->hashGet($key,'userPwd');
        return  $userPwd ? json_decode($userPwd,true) : array();
    }
    
    public function getRecoedName($key){
        if(empty($key)) return "";
        $name = $this->redisConn->hashGet($key,'name');
        return  $name ? $name : "";
    }
    
    public function setTaskUserKey($param = array()){
        $options = array(
            'key' => '',
            'uid' => 0,
            'email' => '',
            'emailSend' => 0,
            'mobile' => '',
            'mobileSend' => 0
        );
        $options = array_merge($options,$param);
        extract($options);
        
        if(!$key || !$uid) return false;
        if(!$this->isExistsKey($key)) return false;
        $taskUserKey = $this->getTaskUserKey($key, $uid);
        $data = array('uid'=>$uid,'email'=>$email,'emailSend'=>$emailSend,'mobile'=>$mobile,'mobileSend'=>$mobileSend);
        $r = $this->redisConn->hashSet($taskUserKey, $data);
        if($r) $this->redisConn->zAdd($this->getTaskUserList($key),getMicroTm(),$taskUserKey);
        return $r ? true : false;
    }
    
    public function getTaskUserCount($key){
        $taskUserList = $this->getTaskUserList($key);
        return $this->redisConn->zCount($taskUserList, '-inf', '+inf') ? : 0;
    }
    
    public function getTaskUser($key,$limit = array()){
        $arr = array();
        $taskUserListArr = $this->getTaskUserListArr($key,$limit);
        if($taskUserListArr){
            foreach ($taskUserListArr as $taskUser) {
                $arr[$taskUser] = $this->getResetPwdKeyInfo($taskUser);
            }
        }
        return array_values($arr);
    }
    
    public function getPollTaskUser($param=array()){
        $options = array(
            'key' => '',
            'tm'  => ''
        );
        $options = array_merge($options,$param);
        extract($options);

        $arr = array();
        $taskUserArr = $this->getTaskUserArr($key,$tm);
        if(!empty($taskUserArr)){
            foreach ($taskUserArr as $userKey) {
                if($this->isExistsKey($userKey)){
                    $userArr = $this->getResetPwdKeyInfo($userKey);
                    $arr[$userKey] = $userArr;
                    $arr[$userKey]['uname'] = getUserName($userArr['uid']);             
                }
            }
        }
        return array_values($arr);
    }
}
