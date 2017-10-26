<?php
namespace Common\Model\Comment;
use Common\Traits\ServiceTrait;
use Think\Model;
use Common\Model\Common\BaseModel;
class CommentServiceModel extends BaseModel{
    protected $currentDb = CENTER;
    
    private function getCmtStatDao(){
        return $this->createDao("Comment.CmtStat");
    }
    
    private function getCmtListDao(){
        return $this->createDao("Comment.CmtList");
    }
    
    private function getCmtReplyListDao(){
        return $this->createDao("Comment.CmtReplyList");
    }
    
    private function getCmtAnonymousDao(){
        return $this->createDao("Comment.CmtAnonymous");
    }
    
    
     /**
     * 获得评论
     * @author tanhaitao 2015-11-19
     */
    public function getComment($id){
        return $this->getCmtListDao()->getCmtList($id);
    }
    
  
    /**
     * 获得页面评论数
     * @author tanhaitao 2015-11-19
     */
    public function getAllCmtCnt($cmtStatId){
        return $this->getCmtListDao()->getAllCmtCnt($cmtStatId);
    }
    
    /**
     * 根据页面唯一标识获得页面评论数
     * @author tanhaitao 2015-12-2
     */
    public function getCommentNum($cmtIdStr){
	    return $this->getCmtStatDao()->getCommentNum($cmtIdStr);
    }
    
    /**
     * 获得总人数
     * @author tanhaitao 2015-12-4
     */
    public function getCmtAllNum($cmtStatId){
        $number1 = $this->getUserCmtNum($cmtStatId);
        $number2 = $this->getAnonyCmtNum($cmtStatId);
        $num = $number1 + $number2;
        return $num;
    }
    
    
     /**
     * 获得用户是否评论且回复
     * @author tanhaitao 2015-12-4
     */
    public function getUserCmtNum($cmtStatId){
        $cmt = $this->getCmtNumByUid($cmtStatId);
        $reply = $this->getReplyNumByUid($cmtStatId);
        $arr = array();
        if($cmt){
            foreach ($cmt as $key => $value) {
               $arr[] = $value['sendUid']; 
            }
        }
        if($reply){
           foreach ($reply as $key => $value) {
           $arr[] = $value['replyUid']; 
           } 
        }
        $number = count(array_unique($arr));
        return $number ? $number : 0 ;
    }
    
    /**
     * 获得,匿名用户是否评论且回复
     * @author tanhaitao 2015-12-4
     */
    public function getAnonyCmtNum($cmtStatId){
        $cmt = $this->getCmtNumByIp($cmtStatId);
        $reply = $this->getReplyNumByIp($cmtStatId);
        $arr = array();
        if($cmt){
            foreach ($cmt as $key => $value) {
               $arr[] = $value['IP']; 
            }
        }
        if($reply){
           foreach ($reply as $key => $value) {
           $arr[] = $value['IP']; 
           } 
        }
        $number = count(array_unique($arr));
        return $number ? $number : 0 ;
    }
    
    
    /**
     * 根据ip获得匿名评论人数
     * @author tanhaitao 2015-11-19
     */
    public function getCmtNumByIp($cmtStatId){
        return $this->getCmtListDao()->getCmtNumByIp($cmtStatId);
    }
    
    /**
     * 获得登陆评论人数
     * @author tanhaitao 2015-11-19
     */
    public function getCmtNumByUid($cmtStatId){
        return $this->getCmtListDao()->getCmtNumByUid($cmtStatId);
    }
    
       /**
     * 根据ip获得匿名回复人数
     * @author tanhaitao 2015-11-19
     */
    public function getReplyNumByIp($cmtStatId){
        return $this->getCmtReplyListDao()->getReplyNumByIp($cmtStatId);
    }
    
    /**
     * 获得登陆回复人数
     * @author tanhaitao 2015-11-19
     */
    public function getReplyNumByUid($cmtStatId){
        return $this->getCmtReplyListDao()->getReplyNumByUid($cmtStatId);
    }
    
     /**
     * 获得页面评论
     * @author tanhaitao 2015-11-19
     */
    public function getAllCommentList($cmtStatId,$order ="",$start, $limit){
        return $this->getCmtListDao()->getAllCommentList($cmtStatId,$order ="",$start, $limit);
    }
    
    
    /**
     * 根据评论id获取相关回复数
     * @author tanhaitao 2015-11-19
     */
    public function getReplyListByCmtIdCount($cmtId){
        return $this->getCmtReplyListDao()->getReplyListByCmtIdCount($cmtId);
    }
    
    
     /**
     * 根据评论id获取相关回复
     * @author tanhaitao 2015-11-19
     */
    public function getReplyListByCmtId($cmtId,$order ="",$start, $limit){
        return $this->getCmtReplyListDao()->getReplyListByCmtId($cmtId,$order ="",$start, $limit);
    }
    
     /**
     * 根据id获取回复用户信息
     * @author tanhaitao 2015-11-25
     */
    public function getReplyUser($id){
        return $this->getCmtReplyListDao()->getReplyUid($id);
    }
    
    /**
     * 根据id获取评论用户信息
     * @author tanhaitao 2015-11-25
     */
    public function getCmtSendName($id){
        return $this->getCmtListDao()->getCmtSendName($id);
    }
    
    /**
     * 根据id获取用户信息
     * @author tanhaitao 2015-11-25
     */
    public function getReplyByCmtId($cmtId){
       $reply = $this->getCmtReplyListDao()->getReplyByCmtId($cmtId);
       if(empty($reply)) return array();
       foreach($reply as $k=> $v){
              //$reply[$k]['userFace'] = getUserFace($v['replyUid']);
              if($v['pid']){
                 $beReply = $this->getReplyUser($v['pid']); 
                 $reply[$k]['beReplyName'] = $beReply['replyName'];
                 $reply[$k]['beReplyUid'] = $beReply['replyUid'];
              }else{
                 $beReply = $this->getCmtSendName($v['cmtId']);
                 $reply[$k]['beReplyName'] = $beReply['sendName'];
                 $reply[$k]['beReplyUid'] = $beReply['sendUid'];
              }
          }
       return $reply;
    }
    
    
    
    /**
     * 获得匿名用户评论数（每天）
     * @author tanhaitao 2015-11-19
     */
    public function getAnonyDayCommentCnt(){
        $ip = get_client_ip();
        list($start_time,$end_time) = $this->cmtDayTime();
        $data['IP'] = $ip;
        $data['start_time'] = $start_time;
        $data['end_time'] = $end_time;
        return $this->getCmtListDao()->getAnonyDayCmtCnt($data);
    }
    
    /**
     * 获得登录用户评论数（每天）
     * @author tanhaitao 2015-11-19
     */
    public function getUserDayCommentCnt($uid){
        $data = $this->cmtDayTime();
        $data['sendUid'] = $uid;
        return $this->getCmtListDao()->getUserDayCmtCnt($data);
    }
   
   /**
     * 根据页面id获取页面信息
     * @author tanhaitao 2015-11-19
     */
   public function getCmtStat($cmtStatId){
        return $this->getCmtStatDao()->getCmtStat($cmtStatId);
    }
    
    
    
    /**
     * 根据页面id获取评论数
     * @author tanhaitao 2015-11-19
     */
    public function getCmtNumByCmtStatId($cmtStatId){
        return $this->getCmtListDao()->getCmtNumByCmtStatId($cmtStatId);
    }
    
     /**
     * 根据页面id获取回复数
     * @author tanhaitao 2015-11-19
     */
    public function getReplyNumByCmtStatId($cmtStatId){
        return $this->getCmtReplyListDao()->getReplyNumByCmtStatId($cmtStatId);
    }
    
    /**
     * 获得匿名用户评论回复数（每天）
     * @author tanhaitao 2015-11-19
     */
    public function getAnonyDayReplyCnt(){
        $data = $this->cmtDayTime();
        $ip = get_client_ip();
        $data['IP'] = $ip;
        return $this->getCmtReplyListDao()->getAnonyDayReplyCnt($data);
    }
    
    /**
     * 获得登录用户评论回复数（每天）
     * @author tanhaitao 2015-11-19
     */
    public function getUserDayReplyCnt($uid){
        $data = $this->cmtDayTime();
        $data['sendUid'] = $uid;
        return $this->getCmtReplyListDao()->getUserDayReplyCnt($data);
    }
    /**
     * 获得回复评论
     * @author tanhaitao 2015-11-21
     */
    public function getReplyComment($id){
        return $this->getCmtReplyListDao()->getCmtReply($id);
    }
    
    /**
     *
     * @author tanhaitao 2015-11-21
     */
    public function getIdByReplyPid($id){
        return $this->getCmtReplyListDao()->getIdByReplyPid($id);
    }
    
     /**
     * 获得匿名者信息
     * @author tanhaitao 2015-11-21
     */
    public function getCmtAnonymous(){
        $ip = get_client_ip();
        return $this->getCmtAnonymousDao()->getCmtAnonymous($ip);
    }
    
     /**
     * 退出时物理删除匿名者信息
     * @author tanhaitao 2015-11-21
     */
    public function delCmtAnonymous(){
        $ip = get_client_ip();
        return $this->getCmtAnonymousDao()->delCmtAnonymous($ip);
    }
    
    
    /**
     * 添加匿名者信息
     * @author tanhaitao 2015-11-21
     */
    public function addCmtAnonymous($fields){
        $r = $this->getCmtAnonymous();
        if($r) return $r;
        $fields['IP'] = get_client_ip();
        $cmtStarId = $this->getCmtStatIdByCombination($fields);
        $fields['cmtStatId'] = $cmtStarId;
        return $this->getCmtAnonymousDao()->addCmtAnonymous($fields);
    }
    
    /**
     * 添加评论
     * @author tanhaitao 2015-11-19
     */
    public function addComment($fields){
        $fields['IP'] = get_client_ip();
        $cmtStarId = $this->getCmtStatIdByCombination($fields);
        $fields['cmtStatId'] = $cmtStarId;
        return $this->getCmtListDao()->addCmtList($fields);
    }
    
    /**
     * 根据评论id修改最后回复数
     * @author tanhaitao 2015-11-27
     */
    public function setReplyCntByCmtId($cmtId){
        $data['replyCnt'] = $this->getCmtReplyListDao()->getReplyNumByCmtId($cmtId);
        $data['id']  = $cmtId;
        $this->getCmtListDao($where)->setReplyCntById($data);
    }
    
    
    /**
     * 评论页面查询并修改
     * @author tanhaitao 2015-11-19
     */
    public function getCmtStatIdByCombination($fields){
        if($fields['uid'] != 0 ){
            $cmtStat = $this->getCmtStatByUid($fields);
        }else{
            $cmtStat = $this->getCmtStatByIp($fields);
        }
        
        if($cmtStat){
            $r = $this->getCmtStatDao()->updateCmtStat($cmtStat);  
        }else{
            $r = $this->getCmtStatDao()->addCmtStat($fields); 
        }
        return $r['id'];
    }
    
    
    
    /**
     * 某人是否评论过某个评论页面 
     * @author tanhaitao 2015-11-19
     */
    public function getCmtStatByIp($fields){
        $r = $this->getCmtStatDao()->getCmtStatByTypeAndIdStr($fields);
        if(!$r) return 0;
        $where['IP'] = $fields['IP'];
        $where['cmtStatId'] = $r['id'];
        $num = $this->getCmtListDao()->getCountByIp($where);
        //$num2 = $this->getCmtReplyListDao()->getCountByIp($where);
        $r['cmtCnt']     =   intval($r['cmtCnt'])  + 1;
        if(!$num) $r['userCnt']  = intval($r['userCnt']) + 1;
        return $r ;
    }
    
    /**
     * 某人是否评论过某个评论页面 
     * @author tanhaitao 2015-11-19
     */
    public function getCmtStatByUid($fields){
        $r = $this->getCmtStatDao()->getCmtStatByTypeAndIdStr($fields);
        if(!$r) return 0;
        $where['uid'] = $fields['uid'];
        $where['cmtStatId'] = $r['id'];
        $num1 = $this->getCmtListDao()-> getCountByUid($where);
        $num2 = $this->getCmtReplyListDao()-> getCountByUid($where);
        $num  = $num1 + $num2;
        $r['cmtCnt']     =   intval($r['cmtCnt'])  + 1;
        if(!$num) $r['userCnt']  = intval($r['userCnt']) + 1;
        return $r ;
    }
    
    /**
     * 回复评论改变人数
     * @author tanhaitao 2015-11-19
     */
    public function setCmtStatByReply($cmtStatId){
        $where['id'] = $cmtStatId;
        $where['userCnt']  = $this->getCmtAllNum($cmtStatId);
        $this->getCmtStatDao()->updateCmtStat($where); 
    }
    
    
    /**
     * 删除评论
     * @author tanhaitao 2015-11-19
     */
    public function delComment($id,$cmtStatId){
        $this->getCmtReplyListDao()->delCmt($id);
        $r = $this->getCmtListDao()->delCmtList($id);
        if($r)
        $this ->changeCmtstat($cmtStatId);
        return $r;
    }
    
    /**
     * 根据删除时评论id 是否更改评论数与人数
     * @author tanhaitao 2015-11-19
     */
    public function changeCmtstat($cmtStatId){
        $cmtCnt = $this ->getAllCmtCnt($cmtStatId);
        $data['id'] = $cmtStatId;
        $data['cmtCnt'] = $cmtCnt;
        $data['userCnt'] = $this->getCmtAllNum($cmtStatId);
        $this ->getCmtStatDao()->changeCmtstat($data);
    }
   
    
    /**
     * 删除回复评论
     * @author tanhaitao 2015-11-21
     */
    public function delReplyComment($reply){
        $r = $this->getCmtReplyListDao()->delCmtReply($reply['id']);
        if($r){
            $this ->setReplyCntByCmtId($reply['cmtId']);
            $this->setCmtStatByReply($reply['cmtStatId']);
        }
        return $r ;
    }
    
    
    /**
     * 添加回复评论
     * @author tanhaitao 2015-11-19
     */
    public function addReplyComment($fields){
        $fields['IP'] = get_client_ip();
        if($fields['pid'] != 0) 
           $fields['pidSta'] = $this-> getPidSta($fields['pid']);
        $r = $this->getCmtReplyListDao()->addCmtReply($fields);
        $this->setCmtStatByReply($fields['cmtStatId']);
        $this ->setReplyCntByCmtId($r['cmtId']);
        return $r;
    }
    
    /**
     * 根据pid得到pidSta
     * @author tanhaitao 2015-11-21
     */
    public function getPidSta($pid){
        $data = $this->getCmtReplyListDao()->getPidSta($pid);
        if($data['pidSta'] == 0) 
            return $data['pid'];
        return $this-> getPidSta($data['pid']);
    }
     /**
     * 得到每天起始终结时间戳
     * @author tanhaitao 2015-11-21
     */
    public function cmtDayTime(){
        $date = date( "Y-m-d" ); //如'2012-10-31'
        list( $year, $month, $day ) = explode( '-', $date ); //分割后$year = '2012', $month = '10', $day = '31'
        //将字符串转换成整型
        $year = intval( $year );
        $month = intval( $month );
        $day = intval( $day );

        $param['start_time'] = mktime( 0, 0, 0, $month, $day, $year ); //当天的起始时间(时间戳)，如2012-10-30 00:00:00的时间戳
        $param['end_time']   = mktime( 23, 59, 59, $month, $day, $year ); // 当天最后时间(时间戳)
        return $param;
    }
    
    /**
     * 根据页面组合键获得此页面参数
     * @author tanhaitao 2015-11-21
     */
    public function getCmtStatByTypeAndIdStr($fields){
        return $this->getCmtStatDao()->getCmtStatByTypeAndIdStr($fields);
        
    }
    
    /**
     * 评论发送通知
     * @param  string $type , string $strId
     */
    public function pushCommentLog($cmtType ,$cmtIdStr){
        if(!$cmtType || !$cmtIdStr) return false ; 
           $cmtIdStr = str_replace($cmtType, '', $cmtIdStr);
           $address = getScheme() . "://" . currentWebCode();
           //海报评论 通知
           if( $cmtType == 'Poster'){
              $poster = $this->getPosterService()->find($cmtIdStr); 
              if(!empty($poster)){
                 createService("Group.GroupPushServiceModel")->pushUser(
                        (string) $poster['uid'], # 字符串（如：'1,6,46,255'），消息接收者编号逗号拼接字符串
                        'poster', # 字符串，推送消息的 模块/类型
                        'commentPoster', # 字符串，推送消息的 执行名称 
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
           
    }
    
    /**
     * 回复发送通知
     * @param  string $type , string $strId
     */
    public function pushReplyLog($data){
        if(!$data['cmtType'] || !$data['cmtIdStr'] || !$data['replyId'] ) return false ; 
           $data['cmtIdStr'] = str_replace($data['cmtType'], '', $data['cmtIdStr']);
           $address = getScheme() . "://" . currentWebCode();
           if( $data['cmtType'] == 'Poster'){
              $poster = $this->getPosterService()->find($data['cmtIdStr']); 
              if(!empty($poster)){
                 //海报回复 通知
               createService("Group.GroupPushServiceModel")->pushUser(
                        (string) $poster['uid'], # 字符串（如：'1,6,46,255'），消息接收者编号逗号拼接字符串
                        'poster', # 字符串，推送消息的 模块/类型
                        'replyPoster', # 字符串，推送消息的 执行名称 
                        $pdata = array(
                        'image' => $poster['picture'] ? $address.$poster['picture'] : '',
                        'posterId' => $poster['id'],
                        'replyId' => $data['replyId'],
                        'cmtStatId' => $data['cmtStatId'] ? $data['cmtStatId'] : 0,
                        'posterTitle' => $poster['desc'],
                        ), # 数组， 推送消息附带的 附加参数
                        $needAddSenderName = true, # true/false， 是否需要在 推送消息前面 缀上 操作者名称， 默认值为true
                        $needLogin = true                      # true/false， 是否需要判断消息接收者的登录状态（即只给登录用户推消息）， 默认值为true
                   );
               
               createService("Group.GroupPushServiceModel")->pushUser(
                        (string) $poster['uid'], # 字符串（如：'1,6,46,255'），消息接收者编号逗号拼接字符串
                        'poster', # 字符串，推送消息的 模块/类型
                        'replyPosterComment', # 字符串，推送消息的 执行名称 
                        $rdata = array(
                        'image' => $poster['picture'] ? $address.$poster['picture'] : '',
                        'posterId' => $poster['id'],
                        'replyId' => $data['replyId'],
                        'cmtStatId' => $data['cmtStatId'] ? $data['cmtStatId'] : 0,
                        'replyTitle' => $poster['desc'],
                        ), # 数组， 推送消息附带的 附加参数
                        $needAddSenderName = true, # true/false， 是否需要在 推送消息前面 缀上 操作者名称， 默认值为true
                        $needLogin = true                      # true/false， 是否需要判断消息接收者的登录状态（即只给登录用户推消息）， 默认值为true
                   );
              } 
           }
           
    }
    
    /**
     * 根据页面组合键获得此页面所有评论
     * @author tanhaitao 2015-11-19
     */
    public function getAllComment($cmtStatId,$orderBy, $start, $limit){
        $cmtlist = $this->getCmtListDao()->getAllCmtList($cmtStatId,$orderBy, $start, $limit);
        if(!$cmtlist) return array();
        $idKey = 0;
            foreach ($cmtlist as $key => $val) {
                if($val['rid']){
                    if($idKey!= $val['id'] ){
                      $cmt = array();  
                    } 
                    if($val['pid'] == 0 && $val['pidSta']==0 )
                        $cmt[$val['rid']] = $val;
                    elseif(($val['pid'] != 0 && $val['pidSta']!=0 )) 
                        $cmt[$val['pidSta']]['child'][$val['pid']*1000+$val['rid']] = $val;
                    else
                        $cmt[$val['pid']]['child'][$val['rid']*1000] = $val; 
                }
                   if($cmt[$val['pidSta']]['child']) ksort($cmt[$val['pidSta']]['child']);
                    $list['id'] = $val['id'];
                    $list['cmtStatId'] = $val['cmtStatId'];
                    $list['comment'] = $val['comment'];
                    $list['sendUid'] = $val['sendUid'];
                    $list['sendName'] = $val['sendName'];
                    $list['replyCnt'] = $val['replyCnt'];
                    $list['ctm'] = $val['ctm'];
                    $list['IP'] = $val['IP'];
                    $list['webCode'] = $val['webCode'];
                    unset($cmtlist[$key]);
                    $idKey = $val['id'];
                    if($idKey!= $cmtlist[$key+1]['id'] && $val['rid']){
                       $list['replyComment'] = $cmt;
                       $cmtlist[$key]=$list; 
                    }
                    if(!$val['rid']){
                        unset($list['replyComment']);
                       $cmtlist[$key]=$list;  
                    }
                     
            }
            //$cmtlist = array_values($cmtlist);
            
            return $cmtlist;
        
    }
    
    
    
    private function getPosterService()
    {
        return createService('Poster.PosterService');
    }
    
    
    
    
}


