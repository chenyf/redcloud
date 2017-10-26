<?php

/**
 * 消息通知系统
 */

namespace Cli\Queue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Controller\BaseController;
use \Common\Services\PushService;
class MsgTaskQueue{
    
    /*
     /usr/local/php-5.5/bin/php  index_cli.php --m=Queue  --c=MsgTaskQueue --a=noticeByEmail
     * 
     */
    public function noticeByEmail($param){
        $options = array(
            'msg_type' =>'',
        );
        $param = array_merge($options,$param);
        self::startRunMsg($param);
        //startRunMsg($param);
         
    }
    public function noticeByNote($param){
         $options = array(
            'msg_type' =>'',
        );
        $param = array_merge($options,$param);
        self::startRunMsg($param);
    }
     public function noticeByPush($param){
         $options = array(
            'msg_type' =>'',
        );
        $param = array_merge($options,$param);
        self::startRunMsg($param);
    }
    
     public function noticeByPost($param){
         $options = array(
            'msg_type' =>'',
        );
        $param = array_merge($options,$param);
        self::startRunMsg($param);
    }
    public function pushMsg($param){
        $options = array(
            'task_id' =>0,
            'is_need_note_notice'=>0
        );
       
        $param = array_merge($options,$param);
        extract($param);
        $map["status"] = 0;
        $map["task_id"]=$task_id;
        $msgTaskList = self::getMessageService()->searchMsgTask($map);
        $date = date("Y-m-d H:i:s", time());
        if (empty($msgTaskList)) {
            echo "没有需要要执行客户端推送的任务,更新时间为:" . $date . "\r\n";
            return false;
        }
        $msgTaskList = $msgTaskList[0];
        $msgTaskList["class_id"] =rtrim($msgTaskList["class_id"],",");
        $msgModel = new \Common\Model\Message\MsgModel();
        $startTime = microtime(true);
        $count = 0;
        self::getMessageService()->modifyMsgTaskInfoById($task_id, array("status" =>1));
        $param = array(
            'type' =>3,
            'content' =>$msgTaskList["content"],
            'title' => $msgTaskList["title"],
            'from_uid' =>$msgTaskList["from_uid"],
            'class_id' => $msgTaskList["class_id"],
          );
         $sendInfo = $msgModel->sendMessage($param);
         if ($sendInfo["status"] == 1) {
            $count++;
             self::getMessageService()->modifyMsgTaskInfoById($task_id, array("status" => 2));
             $groupArr = self::getGroupService()->getMemberUidsByGroupId($msgTaskList["class_id"]);
              $userIdStr = "";
                foreach ($groupArr as $key => $value) {
                    $userIdStr.=$value["id"] . ",";
              }
             $param = array(
            'type' =>1,
            'content' =>$msgTaskList["title"]."_".$msgTaskList["content"],
            'from_uid' =>$msgTaskList["from_uid"],
            'to_uid' => rtrim($userIdStr, ","),
            );
            $sendInfo = $msgModel->sendMessage($param);
            //M("msg_message_task")->where("task_id=".$v["task_id"])->save(array("status"=>2));
         } else {
            //M("msg_message_task")->where("task_id=".$v["task_id"])->save(array("status"=>3)); 
            self::getMessageService()->modifyMsgTaskInfoById($task_id, array("status" => 3));
        }
        
     }
    /**
     * 执行消息
     * /usr/local/php-5.5/bin/php  index_cli.php --m=Queue  --c=MsgTask --a=runAction >> /tmp/edm_message_task.log
     */
    private static function startRunMsg($param) {
        $msg_type = isset($param["msg_type"]) ? $param["msg_type"]:""; 
        if(!empty($msg_type)) $map["msg_type"] = $msg_type;
        $map["status"] = 0;
        $msgContentArr = array("1"=>"短信","2"=>'邮件',"3"=>'客户端推送',"4"=>"站内信");
        $msg_content = isset($msgContentArr[$msg_type]) ? $msgContentArr[$msg_type]:'';
        $msgTaskList = self::getMessageService()->searchMsgTask($map);
        $date = date("Y-m-d H:i:s", time());
        if (empty($msgTaskList)) {
            echo "没有需要要执行".$msg_content."的任务,更新时间为:" . $date . "\r\n";
            return false;
        }
        $msgModel = new \Common\Model\Message\MsgModel();
        $startTime = microtime(true);
        $count = 0;
      
        foreach ($msgTaskList as $k => $v) {
           self::getMessageService()->modifyMsgTaskInfoById($v["task_id"], array("status" =>1));
            $class_id = $v["class_id"];
            $teacher_id = $v["teacher_id"];
            $copy_people = $v["copy_people"];
            if ($v["msg_type"] == 3) {
                if ($v["selectSendType"] == 1) {
                    $userIdStr.=$copy_people;
                    $param =array(
                        'type' => $v["msg_type"],
                        'content' => $v['content'],
                        'title' => $v['title'],
                        'to_uid' => rtrim($userIdStr, ","),
                        'from_uid' => $v["from_uid"],
                        'selectSendType' => 1,
                    );
                }else{
                    $userIdStr = "";
                    $userIdStr.=(!empty($teacher_id)) ? ($teacher_id . ",") : '';
                    $userIdStr.=$copy_people;
                    $param = array(
                        'type' => $v["msg_type"],
                        'content' => ($v["content"]),
                        'title' => $v["title"],
                        'to_uid' => rtrim($userIdStr, ","),
                        'from_uid' => $v["from_uid"],
                        'class_id' => $class_id,
                        'selectSendType' => $v["selectSendType"],
                    );
                }
            } else {
                // $groupArr = M("groups_member")->field("userId as id")->where("groupId in(".$class_id.")")->select();
                $groupArr = self::getGroupService()->getMemberUidsByGroupId($class_id);
                $userIdStr = "";
                foreach ($groupArr as $key => $value) {
                    $userIdStr.=$value["id"] . ",";
                }
                $userIdStr.=(!empty($teacher_id)) ? ($teacher_id . ",") : '';
                $userIdStr.=$copy_people;
                $param = array(
                    'type' => $v["msg_type"],
                    'content' => ($v["content"]),
                    'title' => $v["title"],
                    'to_uid' => rtrim($userIdStr, ","),
                    'from_uid' => $v["from_uid"],
                );
            }
            
            $sendInfo = $msgModel->sendMessage($param);
            if ($sendInfo["status"] == 1) {
                $count++;
                self::getMessageService()->modifyMsgTaskInfoById($v["task_id"], array("status" => 2));
                //M("msg_message_task")->where("task_id=".$v["task_id"])->save(array("status"=>2));
            } else {
                //M("msg_message_task")->where("task_id=".$v["task_id"])->save(array("status"=>3)); 
                self::getMessageService()->modifyMsgTaskInfoById($v["task_id"], array("status" => 3));
            }
        }
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        $infoStr = "总共有" . count($msgTaskList) . "个".$msg_content."任务,成功发送" . $count . "共花费了" .round($totalTime,2). "秒,更新时间为:" . $date . "\r\n";
        $info = M("setting")->where("name='msg_task'")->find();
        $value = array("msg" => $infoStr, "utime" => $date);
        $value = serialize($value);
        if (empty($info)) {
            $data = array(
                'name' => 'msg_task',
                'value' => $value,
            );
            M("setting")->add($data);
        } else {
            $data = array(
                'value' => $value,
            );
            M("setting")->where("name='msg_task'")->save($data);
        }
        echo $infoStr;
    }
    /**
     * 老师开始直播通知学生
     * @author tanhaitao 2016-1-27
     */
    public function startLivePushMsg($param){
         $title = $param['title'];
         $sendUid = $param['sendUid'];
         $receive = $param['receive'];
         foreach ($receive as $k => $v) {
            $this->getGroupPushService()->pushUser(array(
                   'sendUid'=>$sendUid, 		#发送人
                   'uid'=>$v, 				#接收人
                   'title'=>$title, 				#标题
                   'description'=>$description, #子标题
                   'type'=>$type,			#平台:android、ios
                   'data' => array( )			#附加信息
               ));
        }
    }
    
    /**
     * 批量单用户移动端推送消息
     * @author lvwulong 2016.4.20
     */
    public function pushUser($param) {
        $pushList = $param['pushList'];
        # 推送数据
        $pushList = $pushList && is_array($pushList) ? $pushList : array();
        if (! $pushList) {
            return false;
        }
        # 批量推送
        foreach ($pushList as $uid => $tmp) {
            $setting = $tmp['setting'];
            if (isset($setting['night']) && intval($setting['night']) > 0) {
                $currentHour = intval(date('H', time()));
                if ($currentHour > 22 || $currentHour < 8) {
                    break;
                }
            }
            # 推送数据必传参数
            $options = array(
                'module' => $tmp['module'],
                'operation' => $tmp['operation'],
                'title' => $tmp['title'],
                'description' => $tmp['description'],
                'unreadedNum' => $tmp['unreadedNum'],
                'msgTopic' => C('PUSH_TOPIC_CLASS'),
                'data' => $tmp['data'],
                'ptype' => '',
                'channel_id' => ''
            );
            # 推送
            $channel_ids = array();
            foreach ($tmp['deviceInfo'] as $device) {
                $data = array_merge($options, $device);
                $flag = PushService::getInstance($data['ptype'])->pushByDevice($data);
                if ($flag) {
                    $channel_ids[] = $data['channel_id'];
                }
            }
            # 推送记录
            self::getGroupPushService()->pushLog(array(
                'sendUid' => $tmp['sendUid'],
                'module' => $tmp['module'],
                'operation' => $tmp['operation'],
                'getUid' => $uid,
                'channel_ids' => implode(',', $channel_ids),
                'title' => $tmp['title'],
                'description' => $tmp['description'],
                'data' => $tmp['data']
            ));
        }
    }
    
    protected function getMessageService() {
        return createService('Message.MsgServiceModel');
    }

    protected function getGroupService() {
        return createService("Group.GroupServiceModel");
    }
    
    protected function getGroupPushService() {
        return createService("Group.GroupPushServiceModel");
    }

}

?>
