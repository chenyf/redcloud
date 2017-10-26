<?php
namespace Common\Model\System;
use Think\Model;
use Think\RedisModel;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class PushLogServiceModel extends BaseModel{
    protected $tableName = "push_log";
    /**
     * 记录日志
     */
    
    public function addLog($log=array()){
        return $this->add($log);
    }
    
    /**
     * 日志查询
     * @param  array   $conditions 搜索条件，
     * @param  array   $sort       按什么排序
     * @param  integer $start      开始行数
     * @param  integer $limit      返回行数
     * @return array        	    数组    
     * 
     */
    public function searchLogs($conditions, $sort, $start, $limit){
        $builder = $this->createLogQueryBuilder($conditions)->select('*')->from($this->tableName, $this->tableName);
        $builder->addOrderBy($sort[0], $sort[1]);
        $builder->setFirstResult($start)->setMaxResults($limit);
       	return $builder->execute()->fetchAll() ? : array();
    }

    /**
     * 获得推送日志
     * @author 钱志伟 2015-05-13
     * @edit fubaosheng 2015-05-13
     */
    public function getPushLog($param = array()){
        $options = array(
            'uid'           => 0,
            'platform'      => '',
            'p'             => 1,
            'preNum'        => 10,
            'startDateTime' => 0,
            'endDateTime'   => time()
        );
        $options = array_merge($options, $param);
        extract($options);
        
        $conditions = array();
        
        if(!$uid) return array();
        
        $deviceArr = getUserDeviceInfo(array('uid'=>$uid));
        $channelIdArr = ArrayToolkit::column($deviceArr, 'channel_id');
        $userClassArr = createService('Group.GroupServiceModel')->getUserClass($uid);
        $userClassIdArr = ArrayToolkit::column($userClassArr, 'classId');
        
        $conditions['channel_ids'] = $channelIdArr;
        $conditions['classId'] = $userClassIdArr;
        $conditions['platform'] = $platform;
        $conditions['tagid'] = '*';
        $conditions['startDateTime'] = $startDateTime;
        $conditions['endDateTime'] = $endDateTime;
        
        $arr = array();
        $count = $this->searchLogCount($conditions);
        $arr['totalNum'] = intval($count);
        $arr['totalPage'] = ceil($count/$preNum);
        $arr['p'] = $p;
        $arr['preNum'] = $preNum;
//        $conditions['startDateTime'] = 0;
        $logs = $this->searchLogs($conditions,array('send_time','desc'),($p-1)*$preNum, $preNum);

        $logs = array_map(function($log){
            $log['sendName'] = getUserName($log['sendUid']);
            $log['friendTm'] = date('Y-m-d H:i:s', $log['send_time']);
            return ArrayToolkit::parts($log, array('id', 'title', 'sendUid', 'sendName', 'description', 'send_time', 'friendTm', 'platform'));
        }, $logs);
        $arr['logs'] = $logs;
        return $arr;
    }
    
     /**
     * 获得推送日志未读条数
     * @author 谈海涛 2016-02-17
     */
    public function getPushNoReadNum($param = array()){
        $options = array(
            'uid'           => 0,
            'platform'      => '',
        );
        $options = array_merge($options, $param);
        extract($options);
        
        $conditions = array();
        
        if(!$uid) return array();
        
        $deviceArr = getUserDeviceInfo(array('uid'=>$uid));
        $channelIdArr = ArrayToolkit::column($deviceArr, 'channel_id');
        $userClassArr = createService('Group.GroupServiceModel')->getUserClass($uid);
        $userClassIdArr = ArrayToolkit::column($userClassArr, 'classId');
        
        $conditions['channel_ids'] = $channelIdArr;
        $conditions['classId'] = $userClassIdArr;
        $conditions['platform'] = $platform;
        $conditions['tagid'] = '*';
        $conditions['startDateTime'] = $this->getLastPushTm(array('uid' => $uid));
        $conditions['endDateTime'] = time();
        
        $count = $this->searchLogCount($conditions);
        
        return $count;
    }
    
    /**
     * 根据指定搜索条件返回该条数。
     * @param  array   $conditions 搜索条件，
     *                 如array(
     *                 		'id'=>1, 
     *                      'deviceType'=>1,
     *                 );
     * @return interger           
     */
    public function searchLogCount($conditions){
        $builder = $this->createLogQueryBuilder($conditions)
                ->select('count(`id`) AS count')
                ->from($this->tableName, $this->tableName);
        return $builder->execute()->fetchColumn(0) ? : 0;
    }
        
    protected function createLogQueryBuilder($conditions){
        $conditions = array_filter($conditions);
        
        $obj = $this->createDynamicQueryBuilder($conditions);
        
        if($conditions['switch']){
            $obj = $this->createDynamicQueryBuilder($conditions);
        }
        
        $builder = $obj
                ->andWhere('platform = :platform')
                ->andWhere('send_time > :startDateTime')
                ->andWhere('send_time < :endDateTime')
                ;
        //edit by qzw 2015-05-15
        $staticWhereArr = array();
        if (isset($conditions['channel_ids'])) {
            if(!is_array($conditions['channel_ids'])) $staticWhereArr[] = 'channel_ids like "%'.$channelIds.'%"';
            else {
                foreach($conditions['channel_ids'] as $channelId) {
                    $staticWhereArr[] = 'channel_ids like "%'.$channelId.'%"';
                }
            }
        }
        if(isset($conditions['classId'])) {
//            $staticWhereArr[] = is_array($tagid) ? 'classId in ('.implode(',', $classId).')' : "classId = {$classId}";
            $staticWhereArr[] = is_array($conditions['classId']) ? 'classId in ('.implode(',', $conditions['classId']).')' : "classId = {$conditions['classId']}";
        }
        if(isset($conditions['tagid'])) {
            if($conditions['tagid'] == '*') {
                $staticWhereArr[] = 'tagid = "'.$conditions['tagid'].'"';
            }else{
                $builder->andWhere('tagid = "'.$conditions['tagid'].'"');
            }
        }
        if($staticWhereArr) $builder->andStaticWhere("(".  implode(' or ', $staticWhereArr).')');
        return $builder;
    }
    
    public function getPushLogById($id){
       return $this->where("id=".$id)->find();
    }
    
    /**
     * 记录用户最后获取日志列表的时间
     * @author fubaosheng 2015-05-26
     */
    public function setLastPushTm($param = array()){
         $options = array(
            'uid'   => 0
        );
        $options = array_merge($options, $param);
        extract($options);

        if(!$uid) return false;
        
        $key = "user_push_info_".$uid;
        $value = array('lastPushTm' => time());
        
        $redisCon = RedisModel::getInstance();
        $redisCon->select(C("REDIS_DIST")['USER_PUSH_INFO']);
        $redisCon->hashSet($key,$value);
        $redisCon->expire($key,3600*24*30*12*4);
    }
    
    /**
     * 获取用户最后推送时间
     * @author fubaosheng 2015-06-10
     */
    public function getLastPushTm($param = array()){
        $options = array(
            'uid'   => 0
        );
        $options = array_merge($options, $param);
        extract($options);
        
        if(!$uid) return 0;
        
        $redisCon = RedisModel::getInstance();
        $redisCon->select(C("REDIS_DIST")['USER_PUSH_INFO']);
        $userKey = "user_push_info_" . $uid;
        $lastPushTm = $redisCon->hashGet($userKey, 'lastPushTm') ? : 0;
        
        return $lastPushTm;
    }
}
?>
