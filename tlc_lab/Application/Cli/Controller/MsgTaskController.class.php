<?php

/**
 * 消息通知系统
 */

namespace Cli\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Controller\BaseController;

class MsgTaskController{

    /**
     * 执行消息
     * /usr/local/php-5.5/bin/php  index_cli.php --c=MsgTask --a=runAction >> /tmp/edm_message_task.log
     */
    public function runAction() {
        
        $msg_type = isset($_GET["msg_type"]) ? $_GET["msg_type"]:""; 
        if(!empty($msg_type)) $map["msg_type"] = $msg_type;
        $map["status"] = 0;
        $msgContentArr = array("1"=>"短信","2"=>'邮件',"3"=>'客户端推送',"4"=>"站内信");
        $msg_content = isset($msgContentArr[$msg_type]) ? $msgContentArr[$msg_type]:'';
        $msgTaskList = $this->getMessageService()->searchMsgTask($map);
        $date = date("Y-m-d H:i:s", time());
        if (empty($msgTaskList)) {
            echo "没有需要要执行".$msg_content."的任务,更新时间为:" . $date . "\r\n";
            exit();
        }
        $msgModel = new \Common\Model\Message\MsgModel();
        $startTime = microtime(true);
        $count = 0;
      
        foreach ($msgTaskList as $k => $v) {
            $this->getMessageService()->modifyMsgTaskInfoById($v["task_id"], array("status" =>1));
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
                $groupArr = $this->getGroupService()->getMemberUidsByGroupId($class_id);
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
                $this->getMessageService()->modifyMsgTaskInfoById($v["task_id"], array("status" => 2));
                //M("msg_message_task")->where("task_id=".$v["task_id"])->save(array("status"=>2));
            } else {
                //M("msg_message_task")->where("task_id=".$v["task_id"])->save(array("status"=>3)); 
                $this->getMessageService()->modifyMsgTaskInfoById($v["task_id"], array("status" => 3));
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

    protected function getMessageService() {
        return createService('Message.MsgServiceModel');
    }

    protected function getGroupService() {
        return createService("Group.GroupServiceModel");
    }

}

?>
