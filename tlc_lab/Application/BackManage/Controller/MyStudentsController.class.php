<?php

namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Topxia\WebBundle\Util\AvatarAlert;
use Common\Services\QueueService;
class MyStudentsController extends BaseController {
    
    
    public function teacherListAction(Request $request){
        if ($request->getMethod() == 'POST') {
            $msgtype = $request->request->get('msgtype');
            //获取班级老师
           $teacherList = $this->getGroupService()->getGroupTeacherList();
           
           if($msgtype == 1){
               foreach ($teacherList as $k => $v) {
                  if(empty($v['verifiedMobile'])) unset($teacherList[$k]);
               }
           }
           
           if($msgtype == 2){
               foreach ($teacherList as $k => $v) {
                  if(empty($v['email'])) unset($teacherList[$k]);
               }
           }
           
           if($msgtype == 3){
               $userDevice = createService("User.UserDeviceService")->getAllUidUserDevice();
               $device = array();
               foreach ($userDevice as $value) {
                  $device[] = $value['uid']; 
               }
               $device = array_flip(array_flip($device));
               foreach ($teacherList as $k => $val) {
                  if(!in_array($val['id'], $device)) unset($teacherList[$k]);
               }
           }
           
           $data = '';
           foreach ($teacherList as $key => $titem) {
               $data .='<li class="hide"><label><input type="checkbox"  name="teacher[]" data-topCateid="'.$titem["teacherCategoryId"].'" value="'.$titem["id"].'" class="teacherList"/>'.$titem["nickname"].'</label></li>'; 
           }
           return $this->createJsonResponse(array('data'=>$data));
        }
    }

    /**
     * 加入任务队列当中
     */
    public function addJob($param){
         switch($param["msg_type"]){
             case 1:
             $jobName = "noticeByNote";
             break;
             case 2:
             $jobName = "noticeByEmail";
             break;
             case 3:
             $jobName = "noticeByPush";
             break;
             case 4:
             $jobName = "noticeByPost"; 
             break;
             default:
             $jobName = "noticeByNote"; 
             break;
         }
         //加入任务队列中   
         $jobId = QueueService::addJob(array(
        'jobName'=>$jobName,
         'param' =>array("msg_type"=>$param['msg_type']),
        ));
        return $jobId;
    }
    /**
     * 数据验证
     * @param type $dailyData
     */
    public function checkFormData($dailyData) {
        $codeArr = array(
            1005 => "请选择发送方式",
            1006 => "请选择接收者",
            1007 => "标题不能为空",
            1008 => "标题长度不能超过",
            1009 => "内容不能为空",
            1010 => "消息内容不能超过",
        );
        if (empty($dailyData["msg_type"]))
            $this->ajaxReturn(array("status" => 1005, "msg" => $codeArr[1005]));
        if (empty($dailyData["copy_people"]) && empty($dailyData["class_id"]) && empty($dailyData["teacher_id"]) && $dailyData["msg_type"]!=3)
            $this->ajaxReturn(array("status" => 1006, "msg" => $codeArr[1006]));
        $strConfig = C("msg_str_len_config");
        $strArr = $strConfig[$dailyData["msg_type"]];
        $titleLen = mb_strlen($dailyData["title"], 'utf-8');
       // $contentLen = mb_strlen($dailyData["content"], 'utf-8');
        preg_match_all("/(?:[\x{4e00}-\x{9fa5}])/iu",$dailyData["content"],$match);
        $contentLen =count($match[0]);
        if ($strArr["require"] && empty($dailyData['title']))
            $this->ajaxReturn(array("status" => 1007, "msg" => $codeArr[1007]));
        if ($strArr["titlelen"] != 0 && $titleLen > $strArr["titlelen"])
            $this->ajaxReturn(array("status" => 1008, "msg" => $codeArr[1008] . $strArr['titlelen'] . "个字符"));
        if (empty($dailyData["content"]))
            $this->ajaxReturn(array("status" => 1009, "msg" => $codeArr[1009]));
        if ($strArr["contentlen"] != 0 && $contentLen > $strArr["contentlen"])
            $this->ajaxReturn(array("status" => 10010, "msg" => $codeArr[1010] . $strArr['contentlen'] . "个字符"));
    }

    /**
     * 发送实时消息
     * @param type $dailyData
     */
    public function sendRealMessage($dailyData, $taskId) {
        if ($dailyData["msg_type"] == 3) {
            $userIdStr = "";
            if ($dailyData["selectSendType"] == 1) {
                $userIdStr.=$dailyData["copy_people"];
                $param = array(
                    'type' => $dailyData["msg_type"],
                    'content' => $dailyData['content'],
                    'title' => $dailyData['title'],
                    'to_uid' => rtrim($userIdStr, ","),
                    'from_uid' => $dailyData["from_uid"],
                    'selectSendType' => 1,
                );
            } else {
                $userIdStr.=(!empty($dailyData["teacher_id"])) ? ($dailyData["teacher_id"] . ",") : '';
                $userIdStr.=$dailyData["copy_people"];
                $param = array(
                    'type' => $dailyData["msg_type"],
                    'content' => $dailyData['content'],
                    'title' => $dailyData['title'],
                    'to_uid' => rtrim($userIdStr, ","),
                    'from_uid' => $dailyData["from_uid"],
                    'class_id' => $dailyData["class_id"],
                    'selectSendType' => 2,
                );
            }
        } else {
            //$groupArr = M("groups_member")->field("userId as id")->where("groupId in(" . $dailyData["class_id"] . ")")->select();
            $groupArr = $this->getGroupService()->getMemberUidsByGroupId($dailyData["class_id"]);
            $userIdStr = "";
            foreach ($groupArr as $key => $value) {
                $userIdStr.=$value["id"] . ",";
            }
            $userIdStr.=(!empty($dailyData["teacher_id"])) ? ($dailyData["teacher_id"] . ",") : '';
            $userIdStr.=$dailyData["copy_people"];
            $param = array(
                'type' => $dailyData["msg_type"],
                'content' => ($dailyData['content']),
                'title' => $dailyData['title'],
                'to_uid' => rtrim($userIdStr, ","),
                'from_uid' => $dailyData["from_uid"],
            );
        }
        $msgModel = new \Common\Model\Message\MsgModel();
        $sendInfo = $msgModel->sendMessage($param);
        if ($sendInfo["status"] == 1) {
            $this->getMessageService()->modifyMsgTaskInfoById($taskId, array("status" => 2));
            // M("msg_message_task")->where("task_id=" . $taskId)->save(array("status" => 2));
            $this->ajaxReturn(array("status" => 1000, "msg" => "任务发送成功"));
            exit();
        } else {
            $this->getMessageService()->modifyMsgTaskInfoById($taskId, array("status" => 3));
            $this->ajaxReturn(array("status" => 1001, "msg" => "任务发送失败"));
            exit();
        }
    }
   
    public function groupMessageCreateBySuperAdmin($formData) {
      
        $type = isset($formData['submenu']) ? $formData["submenu"] : "branch";
        $dailyData['msg_type'] = isset($formData['msgtype']) ? $formData["msgtype"] + 0 : 1;
        $classArr = isset($formData['class']) ? $formData["class"] : array();
        $teacherArr = isset($formData['teacher']) ? $formData["teacher"] : array();
        if ($dailyData['msg_type'] != 3 || $dailyData["selectSendType"] != 1) {
            $dailyData["class_id"] = implode(",", $classArr);
            $dailyData["teacher_id"] = implode(",", $teacherArr);
        }
        if ($dailyData["msg_type"] == 3) {
            $dailyData['selectSendType'] = isset($formData['selectSendType']) ? $formData["selectSendType"] + 0 : 0;
        }
        $dailyData["copy_people"] = isset($formData["ccpeople"]) ? $formData["ccpeople"] : '';
        $user = $this->getCurrentUser();
        $dailyData["from_uid"] = $user["id"];
        $dailyData['title'] = isset($formData['title']) ? ($formData["title"]) : '';
        $dailyData["title"] = ($dailyData["title"]);
        if ($dailyData["msg_type"] == 1 || $dailyData["msg_type"] == 3) {
            $dailyData['content'] = isset($formData['msgContent']) ? ($formData["msgContent"]) : '';
        } else {
            $dailyData['content'] = isset($formData['content']) ? ($formData["content"]) : '';
        }
        $dailyData["content"] = remove_xss($dailyData["content"]);
        $dailyData["fromRoles"] = isset($formData["createType"]) ? $formData["createType"]:'';
        $dailyData["ctime"] = time();
        $dailyData["ectime"] = time();
        $dailyData["status"] = 0;
        $dailyData["noticetype"] = ($type == "realtime" ? 0 : 1);
        //数据验证 
        $this->checkFormData($dailyData);
        $taskId = $this->getMessageService()->addMsgTask($dailyData);
        if (!$taskId) {
            $this->ajaxReturn(array("status" => 1001, "msg" => "任务添加失败"));
            exit();
        }
        $this->getLogService()->info('msg_task', 'add', "添加新的通知");
        if ($type == "realtime") {
            $this->sendRealMessage($dailyData, $taskId); //发送实时任务
        }
        $jobId = $this->addJob(array("msg_type"=>$dailyData["msg_type"]));
        if(!$jobId){
            $this->getMessageService()->modifyMsgTaskInfoById($taskId, array("status" => 3));
            $this->ajaxReturn(array("status" => 1001, "msg" => "任务添加失败"));
        }
        $this->ajaxReturn(array("status" => 1000, "msg" => "定时通知已经入后台任务中,大约1分钟后完成"));
        exit();
    }
    
    public function pushLogAction(Request $request) {
        $formData= $request->query->all();
        $keywordType = isset($formData["keywordType"]) ? $formData["keywordType"]:'';
        $keyword = isset($formData["keyword"]) ? $formData["keyword"]:'';
        $platform = isset($formData["platform"]) ? $formData["platform"]:'';
        $siteSelect = isset($formData["siteSelect"]) ? $formData["siteSelect"]:'';
        $where = "";
        switch($keywordType){
            case 1:
            $where.="   a.id like '%".$keyword."%'";
            break;
            case 2:
            $where.="   a.email like '%".$keyword."%'";
            break;
            case 3:
            $where.="   a.verifiedMobile like '%".$keyword."%'";
            break;
        } 
        if(!empty($where)){
            $userInfo =  $this->getUserService()->getUserInfoByCustom(array("field"=>"a.id","where"=>$where,"limit"=>0));
            $conditions["uid"]= ArrayToolkit::column($userInfo, "id");
            $deviceArr = getUserDeviceInfo($conditions);
            $channelIdArr = ArrayToolkit::column($deviceArr, 'channel_id');
            $userClassArr = createService('Group.GroupServiceModel')->getUserClassByUid($conditions["uid"]);
            $userClassIdArr = ArrayToolkit::column($userClassArr, 'id');
            $map['channel_ids'] = $channelIdArr;
            $map['classId'] = $userClassIdArr;
        }
        if(!empty($platform))  $map['platform'] = $platform;  
        if(!empty($siteSelect))  $map['siteSelect'] = $siteSelect;  
        $map['switch'] = true;
        $count = $this->getPushLogService()->searchLogCount($map);
        //$paramArr = array('uid' => $uid, 'platform'=>$platform, 'p' => $p, 'preNum' => $preNum);
        $paginator = new Paginator(
                $this->get('request'), $this->getPushLogService()->searchLogCount($map), 10
        );
        $pushLogList = $this->getPushLogService()->searchLogs(
                $map,array('send_time','desc'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );
        foreach ($pushLogList as $k => $v) {
          
            $channel_ids = empty($v["channel_ids"]) ? array() : explode(",", $v["channel_ids"]);
            $pushLogList[$k]["channel_ids_num"] = empty($channel_ids) ? 0 : count($channel_ids);
            $classRow = $this->getGroupService()->getGroup($v["classId"]);
            $pushLogList[$k]["class_name"] = empty($classRow["title"])? '':$classRow["title"]; 
           
        }
        return $this->render('MyStudents:pushlog', array(
                    'pushLogList' => $pushLogList,
                    'paginator' => $paginator,
                    'formData' => $formData,
        ));
    }

    public function createGroupMailTaskAction(Request $request, $createType) {
          
          if ($request->getMethod() == 'POST') {
              
             $formData = $request->request->all();
             //var_dump($formData);die;
             //$this->getGroupService()->insertGroupMialTask($formData);
             $sendeeList = $formData['sendeeList'];
             $pattern = "/[\w.%-]+@[\w.-]+\.[a-z]{2,4}/i";
             preg_match_all($pattern, $sendeeList,$arr);
             $sent   = 0; 
             $count  = count($arr[0]);
             $sentEmail = serialize($arr);
             $user = $this->getCurrentUser();
             $userInfo = $this->getUserService()->getUser($user['id']);
        
             $data = array(
                 'title'  => $formData['title'],
                 'content'=> $formData['content'],
                 'sent'   => $sent,
                 'count'  => $count,
                 'sentemail' => $sentEmail,
                 'create_time'=> time(),
                 'sender' => $userInfo['nickname'],
             );
             $arr['subject'] = $formData['title'];
             $arr['content'] = $formData['content'];
             
             $re = $this->getGroupService()->insertGroupMailTask($data);
             if($re){
                $arr['id'] = $re;
             }
             //echo json_encode(array("status" => 1000, "msg" => "邮件发送已经入后台任务中,大约1分钟后完成"));
             //添加任务
             $queue = 'default';
             $str = 'index_cli.php';
             putenv("QUEUE=$queue");
             putenv("APP_INCLUDE=$str");
             $this->addSendMailJobAction($arr);    
             $this->ajaxReturn(array("status" => 1000, "msg" => "邮件发送已经入后台任务中,大约1分钟后完成"));
         }
         return $this->render('MyStudents:create-groupmailtask',array(
             
         ));
     }
     //添加任务
     public function addSendMailJobAction($sendeeArr){
                $options = array(
                   'jobName'=>'',
               );
               $param = array_merge($options,$arr);
               $jobName = $param["jobName"] = 'groupMailTask';
               unset($param["jobName"]);
               $param = $sendeeArr; 
               $jobId = QueueService::addJob(array(
                   'jobName'=>$jobName,
                   'param' =>$param,
       //            'webCode' => 'qzw111',
               ));     
               if(!$jobId) die('add job fail!'.PHP_EOL.PHP_EOL);
               //echo 'job add succ:' . $jobId . PHP_EOL;
      }
     /**
      * 获取已发邮件列表
      */
      public function getGroupMailListAction(Request $request){
         if($_GET){
             $map = I('get.');
         }

         $paginator = new Paginator(
                 $this->get('request'), $this->getGroupService()->getGroupMialTaskCount($map), 20
         );
         $groupMailTaskList = $this->getGroupService()->getGroupMailTaskList(
                 $map,array('create_time'=>'desc'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
         );
         //$groupMailTaskList = $this->getGroupService()->getGroupMailTask();
         return $this->render('MyStudents:get-groupmaillist', array(
              "groupMailTaskList" => $groupMailTaskList,  
               'paginator' => $paginator,
         ));
      }
     
     
    /**
     * 消息任务展示
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @author ZhaoZuoWu 2015-05-13
     */
    public function messageShowAction(Request $request, $task_id) {
        $message = $this->getMessageService()->searchMessageById($task_id);
        $msgTypeInfo = array("1" => "短信", "2" => "邮件", '3' => "客户端推送", '4' => '站内信');
        $sendStatusInfo = array("-1" => "作废", "0" => "未开始", '1' => "进行中", '2' => '完成', '3' => "失败");
        $message["title"] = htmlspecialchars_decode($message["title"]);
        $message["content"] = $message["content"];
        return $this->render('MyStudents:show-modal', array(
                    'message' => $message,
                    'msgTypeInfo' => $msgTypeInfo,
                    'sendStatusInfo' => $sendStatusInfo,
        ));
    }
    public function getCCPeopleAction(Request $request) {
        $data = array();
        $queryString = $request->query->get('q');
        $msgtype = $request->query->get('msg_type');
        $callback = $request->query->get('callback');
        $classId = $request->query->get("classId");
        //$groupInfo = $this->getGroupService()->getGroup($classId);
        // if(!empty($groupInfo)) $this->createJsonResponse(array());
        $tags = array();
        if ($msgtype == 2) {
            $tags = $this->getUserService()->getEmailByLikeName($queryString);
        } else if ($msgtype == 1) {
            $tags = $this->getUserService()->getMobileByLikeName($queryString);
        } else if ($msgtype == 3) {
            $tags = $this->getUserService()->getNickByLikeName($queryString);
            
        } else if ($msgtype == 4) {
            $tags = $this->getUserService()->getNickByLikeName($queryString);
        }
        $userDeviceModel = createService("User.UserDeviceService");
        foreach ($tags as $key=> $tag) {
            $tag["name"] = empty($tag["name"]) ? "无" : $tag["name"];
            $tmp = $tag["nickname"] . '  <' . $tag['name'] . '>;';
            $tmp = htmlspecialchars($tmp);
            $userAndroidDevice = $userDeviceModel->getUserAndroidDevice($tag['id']);
            if($userAndroidDevice) $tmp.="<span class='m-android' title='".$userAndroidDevice['channel_id']."'></span>";
            $userIosDevice =$userDeviceModel->getUserIosDevice($tag['id']);
            if($userIosDevice) $tmp.="<span class='m-iphone' title='".$userIosDevice['channel_id']."'></span>";
            
            //
            $data[] = array('id' => $tag['id'], 'name' => $tmp);
        }

        return $this->createJsonResponse($data);
    }

    private function getThreadService() {
        return createService('Group.ThreadServiceModel');
    }

    protected function getMessageService() {
        return createService('Message.MsgServiceModel');
    }

    protected function getUserService() {
        return createService('User.UserService');
    }

    private function getGroupService() {
        return createService('Group.GroupServiceModel');
    }

    private function getNotifiactionService() {
        return createService('User.NotificationService');
    }
    protected function getPushLogService(){
        return createService('System.PushLogService');
    }

}