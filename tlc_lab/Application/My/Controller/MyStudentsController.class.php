<?php

namespace My\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Topxia\WebBundle\Util\AvatarAlert;
use Common\Services\QueueService;
class MyStudentsController extends \Home\Controller\BaseController {
    
     public function _initialize(){
        die("前台禁用此功能");
        $app = $this->getCurrentUser();
        if(!$app->isLogin())
         $this->redirect('User/Signin/index');
    }            
    public function taskMessageAction(Request $request) {

        $fields = $request->query->all();
        $user = $this->getCurrentUser();
        $uid = $user["id"];
        $conditions = array(
            'keywordType' => '',
            'keyword' => '',
            'from_uid' => $uid,
        );
        if (empty($fields)) {
            $fields = array();
        }
        $conditions = array_merge($conditions, $fields);
        $conditions['switch'] = true;
        $paginator = new Paginator(
                $this->get('request'), $this->getMessageService()->searchMsgCount($conditions), 10
        );
        $messageList = $this->getMessageService()->searchMessage(
                $conditions, array('ctime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );
        //判断用户的身份（班级管理者，院系领导，讲课教师）
        $ownerClassList = $this->getGroupService()->searchGroups(array("ownerId" => $uid), array("id", "DESC"), 0, 1);
        $adminMemeberList = $this->getGroupService()->searchMembers(array('role' => "admin", "userId" => $uid), array('createdTime', 'DESC'), 0, 1);
        $teacherCategoryId = $user["teacherCategoryId"];
        $isSuperAdmin = isGranted("ROLE_SUPER_ADMIN");
        $msgType = array("1" => "短信", "2" => "邮件", '3' => "客户端推送", '4' => '站内信');
        $sendStatus = array("-1" => "作废", "0" => "未开始", '1' => "进行中", '2' => '完成', '3' => "失败");
        $classIds = array();
        $teacherIds = array();
        foreach ($messageList as $k => $v) {
            $classIdArr = empty($v["class_id"]) ? array() : explode(",", $v["class_id"]);
            $teacherIdArr = empty($v["teacher_id"]) ? array() : explode(",", $v["teacher_id"]);
            $copyIdArr = empty($v["copy_people"]) ? array() : explode(",", $v["copy_people"]);
            $messageList[$k]["teacherNum"] = empty($teacherIdArr) ? 0 : count($teacherIdArr);
            $messageList[$k]["classNum"] = empty($classIdArr) ? 0 : count($classIdArr);
            $messageList[$k]["copyNum"] = empty($copyIdArr) ? 0 : count($copyIdArr);
            $messageList[$k]["content"] = ($v["content"]);
            $messageList[$k]["title"] = ($v["title"]);
        }
        return $this->render('MyStudents:taskmessage', array(
                    'messageList' => $messageList,
                    'paginator' => $paginator,
                    'submenu' => $submenu,
                    'msgtype' => $msgType,
                    'sendStatus' => $sendStatus,
                    'ownerClassList' => $ownerClassList,
                    'adminMemeberList' => $adminMemeberList,
                    'teacherCategoryId' => $teacherCategoryId,
                    'isSuperAdmin' => $isSuperAdmin,
                    'startTime' => $fields["startTime"],
                    'endTime' => $fields["endTime"],
                    'user' => $user,
        ));
    }

    public function createTaskMessageAction(Request $request, $createType) {
        if ($request->getMethod() == 'POST') {
            //$this->ajaxReturn(array("status"=>1001,"msg"=>"任务添加成功"));exit();
            $formData = $request->request->all();
            $isSuperAdmin = isGranted("ROLE_SUPER_ADMIN"); //获取用户角色
            if ($isSuperAdmin) {
                //如果是超级管理员的话另一个东西
                $this->groupMessageCreateBySuperAdmin($formData);
            }
            $type = isset($formData['submenu']) ? $formData["submenu"] : "branch"; //批量通知
            $dailyData['msg_type'] = isset($formData['msgtype']) ? $formData["msgtype"] + 0 : 1;
            $classArr = isset($formData['class']) ? $formData["class"] : array();
            $dailyData["class_id"] = implode(",", $classArr);
            $dailyData["copy_people"] = isset($formData["ccpeople"]) ? $formData["ccpeople"] : '';
            $count = $this->getUserService()->searchUserCount();
            $userInfo = $this->getUserService()->searchUsers(array(), 0, $count);
            // var_dump($formData);exit();
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
            $dailyData["from_class_id"] = $groupInfo["id"];
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
                $this->sendRealMessage($dailyData, $taskId); //实时发送消息
            }
            //加入任务队列当中
            $jobId = $this->addJob(array("msg_type"=>$dailyData["msg_type"]));
             if(!$jobId){
                $this->getMessageService()->modifyMsgTaskInfoById($taskId, array("status" => 3));
                $this->ajaxReturn(array("status" => 1001, "msg" => "任务添加失败"));
            }
            $this->ajaxReturn(array("status" => 1000, "msg" => "定时通知已经入后台任务中,大约1分钟后完成"));
            exit();
        }
        $user = $this->getCurrentUser();
        $groupList = array();
        $teacherList =array();
        $classByCategoryList=array();
        switch($createType){
            case "manager":
            //获取班级管理列表   
            $adminCount = $this->getGroupService()->searchMembersCount(array('userId' =>$user["id"], 'role' => 'admin'));
            $groupAdmin = $this->getGroupService()->searchMembers(array('userId' => $user["id"], 'role' => 'admin'), array('createdTime', 'DESC'), 0, $adminCount);
            $headerCount = $this->getGroupService()->searchMembersCount(array('userId' =>$user["id"], 'role' => 'header')); 
            $headerAdmin = $this->getGroupService()->searchMembers(array('userId' => $user["id"], 'role' => 'header'), array('createdTime', 'DESC'), 0, $headerCount);
            $headerArr =ArrayToolkit::column($headerAdmin,"groupId");
            $classArr =ArrayToolkit::column($groupAdmin,"groupId");
            $ownerCount  =$this->getGroupService()->searchGroupsCount(array("ownerId"=>$user["id"]));
            $ownerList = $this->getGroupService()->searchGroups(array("ownerId"=>$user["id"]),array("createdTime","DESC"),0,$ownerCount);
            $ownerArr = ArrayToolkit::column($ownerList, "id");
            $classArr = array_merge($classArr,$ownerArr,$headerArr);
            $fmap["id"] = array("in",$classArr);
            $fmap["status"] = "open";
            $groupList = $this->getGroupService()->getClassList($fmap);
            break;
            case "leader":
           //院系权限列表
           $classByCategoryList = $this->getGroupService()->getUserCategoryTree(array("uid" => $user["id"]));
           //班级列表
           $groupList = $this->getGroupService()->getClassList();
           //获取班级老师
           $teacherList = $this->getGroupService()->getGroupTeacherList();
           break;
           case "teacher":
           //院系权限列表
           $classByCategoryList = $this->getGroupService()->getUserCategoryTree(array("uid" => $user["id"],"setSuperAdmin"=>1));
           //班级列表
           $groupList = $this->getGroupService()->getClassList();
           //获取班级老师
           $teacherList = $this->getGroupService()->getGroupTeacherList();
           break;
       }
        $isSuperAdmin = isGranted("ROLE_SUPER_ADMIN");
        $domainName = ($_SERVER['HTTP_HOST']=="demo.gkk.cn") ? true : false ;
        //班级信息
        return $this->render('MyStudents:create-message', array("submenu" => $submenu,
                    "classByCategoryList" => $classByCategoryList,
                    "groupList" => $groupList,
                    "teacherList" => $teacherList,
                    "isSuperAdmin" => $isSuperAdmin,
                    "createType" => $createType,
                    "domainName" => $domainName,
        ));
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