<?php
namespace Common\Model\System;

use Common\Model\Common\BaseModel;
use Think\RedisModel;
use \Common\Lib\ArrayToolkit;
/**
 * 课程签到服务model
 * @author 谈海涛 2015-11-27
 */
class AppraiseServiceModel extends \Common\Model\Common\BaseModel{
    private $redisConn = false;
    
    public function __construct() {
        parent::__construct();
        $this->initRedis();
    }
    
    public function initRedis(){
        if($this->redisConn!=false) return false;
        $this->redisConn = RedisModel::getInstance(C('REDIS_DIST.APPRAISE'));
    }
    //appraise_{webcode}_类型_参数id
    public function getAppraiseKey($type , $strId){
        $str =  "appraise:".C('WEBSITE_CODE').":{$type}:{$strId}";
        return strtolower($str);
    }


    /**
     * 点赞
     * @param  string $type , string $strId
     * @author 谈海涛 2015-11-27
     */
    public function addAppraise($type , $strId ,$data){
        if(!$type || !$strId || empty($data)) return false;
        $appraiseKey = $this->getAppraiseKey($type , $strId);
        $r = $this->redisConn->hashSet($appraiseKey, array('good'=>$data['good'], 'bad'=>$data['bad'], 'goodList'=>$data['goodList'],'badList'=>$data['badList']));
        return $r ? true : false;
    }
    
    /**
     * 获得点赞信息
     * @author 谈海涛 2015-11-27
     */
    public function getAppraiseInfo($type , $strId){
        if(!$type || !$strId ) return false;
        $appraiseKey = $this->getAppraiseKey($type , $strId);
        return $this->redisConn->hGetAll($appraiseKey);
    }
    
     /**
     * 增加好评
     * @param  string $type , string $strId
     * @author 谈海涛 2015-11-27
     */
    public function addGood($type , $strId ,$uid){
        if(!$type || !$strId || !$uid) return false;
        $appraiseKey = $this->getAppraiseKey($type , $strId);
        $data = $this->decorationGood($type , $strId ,$uid);
        $r = $this->redisConn->hashSet($appraiseKey, array('good'=>$data['good'], 'goodList'=>$data['goodList']));
        #更新海报数据库的喜欢
        if(strtolower($type) == 'poster' && $r){
            $id = str_replace('poster', '',strtolower($strId));
            createService('Poster.PosterService')->like(intval($id), $uid);
        }
        $goodInfo = $this->getGoodInfo($type , $strId);
        return $r ? $goodInfo : false;
    }
    
    public function decorationGood($type , $strId ,$uid){
        $goodInfo = $this->getGoodInfo($type , $strId);
        if(in_array($uid,$goodInfo['goodList'])) {
           $data['good'] =  $goodInfo['good'] - 1;
           unset($goodInfo['goodList'][array_search($uid , $goodInfo['goodList'])]);
           $data['goodList'] = array_values($goodInfo['goodList']) ? : '';
        }else{
            $data['good'] =  $goodInfo['good'] + 1;
            array_unshift($goodInfo['goodList'] , $uid);
            $data['goodList'] = $goodInfo['goodList'];
        }
        $data['good']  = intval($data['good']) > 0 ?  intval($data['good']) : 0; 
        $data['goodList'] = $data['goodList'] ? json_encode($data['goodList']):'';
        return $data;
    }
    
    public function decorationBad($type , $strId ,$uid){
        $badInfo = $this->getBadInfo($type , $strId);
        if(in_array($uid,$badInfo['baddList'])) {
           $data['bad'] =  $badInfo['bad'] - 1;
           unset($badInfo['badList'][array_search($uid , $badInfo['badList'])]);
           $data['badList'] = array_values($badInfo['badList']) ? : '';
        }else{
            $data['bad'] =  $badInfo['bad'] + 1;
            array_unshift($badInfo['badList'] , $uid);
            $data['badList'] = $badInfo['badList'];
        }
        
        $data['bad']  = $data['bad'] > 0 ? : 0;
        $data['badList'] = $data['badList'] ? json_encode($data['badList']):'';
        return $data;
    }


    /**
     * 增加差评
     * @param  string $type , string $strId
     * @author 谈海涛 2015-11-30
     */
    public function addBad($type , $strId ,$uid){
        if(!$type || !$strId || !$uid) return false;
        $appraiseKey = $this->getAppraiseKey($type , $strId);
        $data = $this->decorationBad($type , $strId ,$uid);
        $r = $this->redisConn->hashSet($appraiseKey, array('bad'=>$data['bad'], 'badList'=>$data['badList']));
        return $r ? true : false;
    }
    
    /**
     * 点赞发送通知
     * @param  string $type , string $strId
     */
     public function pushAppraiseLog($type ,$strId) {
           if(!$type || !$strId) return false ; 
           $id = str_replace($type, '', $strId);
           $type = strtolower($type) ;
           $address = getScheme() . "://" . currentWebCode();
           //海报点赞 通知
           if( $type == 'poster'){
              $poster = $this->getPosterService()->find($id); 
              if(!empty($poster)){
                  createService("Group.GroupPushServiceModel")->pushUser(
                        (string) $poster['uid'], # 字符串（如：'1,6,46,255'），消息接收者编号逗号拼接字符串
                        'poster', # 字符串，推送消息的 模块/类型
                        'apprisePoster', # 字符串，推送消息的 执行名称 
                        $data = array(
                        'image' => $poster['picture'] ? $address.$poster['picture'] : '',
                        'posterId' => $poster['id'],
                        'posterTitle' => $poster['desc'],
                        ), # 数组， 推送消息附带的 附加参数
                        $needAddSenderName = true, # true/false， 是否需要在 推送消息前面 缀上 操作者名称， 默认值为true
                        $needLogin = true                      # true/false， 是否需要判断消息接收者的登录状态（即只给登录用户推消息）， 默认值为true
                   );
              } 
           }
           
           //班级相册点赞 通知
           if( $type == 'photo'){
              $photo = $this->uploadphoto()->find($id);
              if(!empty($photo)){
                  createService("Group.GroupPushServiceModel")->pushUser(
                        (string) $photo['uid'], # 字符串（如：'1,6,46,255'），消息接收者编号逗号拼接字符串
                        'noticeclass', # 字符串，推送消息的 模块/类型
                        'appriseImage', # 字符串，推送消息的 执行名称 
                        $pdata = array(
                        'imageName' => $photo['title'] ? $photo['title'] : '',
                        'image' =>  $photo['url'] ? $photo['url'] : '',
                        ), # 数组， 推送消息附带的 附加参数
                        $needAddSenderName = true, # true/false， 是否需要在 推送消息前面 缀上 操作者名称， 默认值为true
                        $needLogin = true                      # true/false， 是否需要判断消息接收者的登录状态（即只给登录用户推消息）， 默认值为true
                   );
              } 
           }
           
           //班级话题点赞 通知
           if( $type == 'thread'){
              $thread = $this->ThreadService()->getThread($id);
              if(!empty($thread)){
                  createService("Group.GroupPushServiceModel")->pushUser(
                        (string) $thread['userId'], # 字符串（如：'1,6,46,255'），消息接收者编号逗号拼接字符串
                        'noticeclass', # 字符串，推送消息的 模块/类型
                        'appriseTopic', # 字符串，推送消息的 执行名称 
                        $tdata = array(
                        'topicId' => $thread['id'],
                        'topicTitle' =>  $thread['title'] ? $thread['title'] : '',
                        ), # 数组， 推送消息附带的 附加参数
                        $needAddSenderName = true, # true/false， 是否需要在 推送消息前面 缀上 操作者名称， 默认值为true
                        $needLogin = true                      # true/false， 是否需要判断消息接收者的登录状态（即只给登录用户推消息）， 默认值为true
                   );
              } 
           } 
    }
    
     /**
     * 取消好评
     * @param  string $type , string $strId
     * @author 谈海涛 2015-11-30
     */
    public function removeGood($type , $strId ,$uid){
        if(!$type || !$strId || !$uid) return false;
        $appraiseKey = $this->getAppraiseKey($type , $strId);
        $data = $this->decorationGood($type , $strId ,$uid);
        $r = $this->redisConn->hashSet($appraiseKey, array('good'=>$data['good'], 'goodList'=>$data['goodList']));
        #更新海报数据库的喜欢
        if(strtolower($type) == 'poster' && $r){
            $id = str_replace('poster', '',strtolower($strId));
            createService('Poster.PosterService')->dislike(intval($id), $uid);
        }
        $goodInfo = $this->getGoodInfo($type , $strId);
        return $r ? $goodInfo : false;
    }
    
    /**
     * 取消差评
     * @param  string $type , string $strId
     * @author 谈海涛 2015-11-30
     */
    public function removeBad($type , $strId ,$uid){
        if(!$type || !$strId || !$uid) return false;
        $appraiseKey = $this->getAppraiseKey($type , $strId);
        $data = $this->decorationBad($type , $strId ,$uid);
        $r = $this->redisConn->hashSet($appraiseKey, array('bad'=>$data['bad'], 'badList'=>$data['badList']));
        return $r ? true : false;
    }
    
    /**
     * 是否已经进行过好评
     * @param  string $type , string $strId
     * @author 谈海涛 2015-11-27
     */
    public function isGoodByUid($type , $strId ,$uid){
        
        if(!$type || !$strId || !$uid) return false;
        $userInfo = $this->getGoodList($type , $strId);
        $r = in_array($uid , $userInfo);
        return $r ? 1 : 2;
    }
    
    
    /**
     * 是否已经进行过差评
     * @param  string $type , string $strId
     * @author 谈海涛 2015-11-30
     */
    public function isBadByUid($type , $strId ,$uid){
        if(!$type || !$strId || !$uid) return false;
        $userInfo = $this->getBadList($type , $strId);
        $r = in_array($uid , $userInfo);
        return $r ? 1 : 2;
    }
    
    
    /**
     * 获得某次评价好评总数
     * @author 谈海涛 2015-11-27
     */
    public function getGoodNum($type , $strId){
        $appraiseInfo = $this->getAppraiseInfo($type , $strId);
        return isset($appraiseInfo['good']) ? intval($appraiseInfo['good']) : 0;
    }
    
    
    /**
     * 获得某次评价差评总数
     * @author 谈海涛 2015-11-27
     */
    public function getBadNum($type , $strId){
        $appraiseInfo = $this->getAppraiseInfo($type , $strId);
        return isset($appraiseInfo['bad']) ? intval($appraiseInfo['bad']) : 0;
    }
    
    
    /**
     * 获得某次评价好评用户列表
     * @author 谈海涛 2015-11-27
     */
    public function getGoodList($type , $strId){
        $appraiseInfo = $this->getAppraiseInfo($type , $strId);
        return  isset($appraiseInfo['goodList']) ? json_decode($appraiseInfo['goodList']) : array();
    }
    
    /**
     * 获得某次评价好评详细信息
     * @author 谈海涛 2015-11-30
     */
    public function getGoodInfo($type , $strId){
        $appraiseInfo = $this->getAppraiseInfo($type , $strId);
        $data['goodList'] = $appraiseInfo['goodList'] ? json_decode($appraiseInfo['goodList']) : array();
        $data['good']  = $appraiseInfo['good'] ? intval($appraiseInfo['good']) : 0;
        return $data;
    }
   
    
     /**
     * 获得某次评价差评用户列表
     * @author 谈海涛 2015-11-27
     */
    public function getBadList($type , $strId){
        $appraiseInfo = $this->getAppraiseInfo($type , $strId);
        return  isset($appraiseInfo['badList']) ? json_decode($appraiseInfo['badList']) : array();
    }
    
    /**
     * 获得某次评价差评详细信息
     * @author 谈海涛 2015-11-30
     */
    public function getBadInfo($type , $strId){
        $appraiseInfo = $this->getAppraiseInfo($type , $strId);
        $data['badList'] = $appraiseInfo['badList'] ? json_decode($appraiseInfo['badList']) : array();
        $data['bad']  = $appraiseInfo['bad'] ? intval($appraiseInfo['bad']) : 0;
        return $data;
    }

   /**
     * 获得某次评价好评详细信息
     * @author 谈海涛 2015-11-30
     */
    public function getGoodAppraiseInfo($type , $strId ,$limit){
        $appraiseInfo = $this->getGoodInfo($type , $strId);
        $arr = $appraiseInfo['goodList'];
        if($limit && count($arr) > 5)
            $arr = array_slice($arr,0 ,$limit);
        $data = array();
        foreach ($arr as $k => $v) {
            $data[$k] = decorateUserInfo($v);
            $r = createService('Group.GroupService')->getUserDefaultClass($v);
            $data[$k]['className'] =   isset($r['className']) ? $r['className'] : '暂未加入任何班级';
        }
        $appraiseInfo['goodList'] = $data;
        return $appraiseInfo ? : array();
    }
    
    
    /**
     * 获得某次评价信息
     * @author 谈海涛 2015-12-2
     */
    public function getGoodInfoList($type , $strId){
        $appraiseInfo = $this->getGoodInfo($type , $strId);
        $arr = $appraiseInfo['goodList'];
        $data = array();
        foreach ($arr as $k => $v) {
            $r= decorateUserInfo($v);
            $data[$k]['uid'] = $v;
            $data[$k]['userName'] = $r['userName'];
        }
        $type = strtolower($type);
        $appraise = C('APPRAISE');
        $maxNum = $appraise[$type]['listUserMaxNum'];
        $data = array_slice($data , 0 ,$maxNum);
        $appraiseInfo['goodList'] = $data;
        return $appraiseInfo ? : array();
    }
    
    
    private function getPosterService()
    {
        return createService('Poster.PosterService');
    }
    
   private function uploadphoto() {
        return createService('Group.ClassPhotoService');
    }
    
    private function ThreadService() {
        return createService('Group.ThreadServiceModel');
    }
    
    
}
