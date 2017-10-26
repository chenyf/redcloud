<?php

/**
 * 消息通知
 */

namespace BackManage\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Common\Model\Util\CloudClientFactory;

class DailyController extends BaseController {
    
     public function pushLogAction(Request $request) {
        $formData= $request->query->all();
        $keywordType = isset($formData["keywordType"]) ? $formData["keywordType"]:'';
        $keyword = isset($formData["keyword"]) ? $formData["keyword"]:'';
        $platform = isset($formData["platform"]) ? $formData["platform"]:'';
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
        if(!empty($platform))  $map['platform'] = $platform;;  
       
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
        return $this->render('Daily:pushlog', array(
                    'pushLogList' => $pushLogList,
                    'paginator' => $paginator,
        ));
    }
    public function indexAction(Request $request) {
       /*
       $result = createService('Group.GroupPushService')->pushUser(array(
            'uid'      => array(55),
            'title'        => '云课堂测试:'.date('Y-m-d H:i:s'),
            'description'  => 'hello,baby, qzw test!',
        ));
        */
        $fields = $request->query->all();
        $submenu = empty($fields['submenu']) ? "realtime" : $fields['submenu'];
        if ($submenu == "realtime") {
            $noticetype = 0;
        } else {
            $noticetype = 1;
        }
        $conditions = array(
            'keywordType' => '',
            'keyword' => '',
            'noticetype' => $noticetype
        );
        if (empty($fields)) {
            $fields = array();
        }

        $conditions = array_merge($conditions, $fields);
        $paginator = new Paginator(
                $this->get('request'), $this->getMessageService()->searchMsgCount($conditions), 20
        );
        $messageList = $this->getMessageService()->searchMessage(
                $conditions, array('ctime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );
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
        }
        /*
          foreach($messageList as $k=>$v){
          $classIds[$v["task_id"]] = $v["class_id"];
          $teacherIds[$v["task_id"]] = $v["teacher_id"];
          $copyIds[$v["task_id"]] = $v["copy_people"];
          }

          $classList = $this->getMessageService()->findClassNumByClassId($classIds);
          $teacherList= $this->getMessageService()->findTeacherNumByTeacherId($teacherIds);
          $copyPeopleList= $this->getMessageService()->findCopyNumByCId($copyIds);
         * 
         */
        /*
          $approvals = $this->getMessageService()->findUserApprovalsByUserIds(ArrayToolkit::column($users, 'id'));
          $approvals = ArrayToolkit::index($approvals, 'userId');
         */
        return $this->render('Daily:approving', array(
                    'messageList' => $messageList,
                    'paginator' => $paginator,
                    'approvals' => $approvals,
                    'submenu' => $submenu,
                    'msgtype' => $msgType,
                    'sendStatus' => $sendStatus,
                        // 'classList' =>$classList,
                        //  'teacherList' =>$teacherList,
                        // 'copyPeopleList' =>$copyPeopleList,
        ));
    }
    public function pushLogShowAction(Request $request,$id){
        $pushLogList = $this->getPushLogService()->getPushLogById($id);
        $classRow = $this->getGroupService()->getGroup($pushLogList["classId"]);
        $pushLogList["className"] = empty($classRow["title"])? '':$classRow["title"]; 
        return $this->render('Daily:pushlog-show', array(
                    'pushLogList' => $pushLogList,
                
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
        return $this->render('Daily:show-modal', array(
                    'message' => $message,
                    'msgTypeInfo' => $msgTypeInfo,
                    'sendStatusInfo' => $sendStatusInfo,
        ));
    }

    public function sendRealMessage($dailyData,$taskId) {
        if($dailyData["msg_type"] == 3){
              $userIdStr = "";
              $userIdStr.=(!empty($dailyData["teacher_id"])) ? ($dailyData["teacher_id"] . ",") : '';
              $userIdStr.=$dailyData["copy_people"];
              $param = array(
                'type' => $dailyData["msg_type"],
                'content' => ($dailyData['content']),
                'title' => $dailyData['title'],
                'to_uid' => rtrim($userIdStr, ","),
                'from_uid' => $dailyData["from_uid"],
                'class_id' => $dailyData["class_id"],
            );
            $msgModel = new \Common\Model\Message\MsgModel();
            $sendInfo = $msgModel->sendMessage($param);
            if ($sendInfo["status"] == 1) {
            //M("msg_message_task")->where("task_id=" . $taskId)->save(array("status" => 2));
             $this->getMessageService()->modifyMsgTaskInfoById($taskId,array("status"=>2));
            $this->ajaxReturn(array("status" => 1000, "msg" => "任务发送成功"));
             exit();
            } else {
               // M("msg_message_task")->where("task_id=" . $taskId)->save(array("status" => 3));
                $this->getMessageService()->modifyMsgTaskInfoById($taskId,array("status"=>3));
                $this->ajaxReturn(array("status" => 1001, "msg" => "任务发送失败"));
                exit();
            }
        }
        
        //其他类型的实时消息发送
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
        $msgModel = new \Common\Model\Message\MsgModel();
        $sendInfo = $msgModel->sendMessage($param);
        if ($sendInfo["status"] == 1) {
            $this->getMessageService()->modifyMsgTaskInfoById($taskId,array("status"=>2));
            $this->ajaxReturn(array("status" => 1000, "msg" => "任务发送成功"));
            exit();
        } else {
           $this->getMessageService()->modifyMsgTaskInfoById($taskId,array("status"=>3));
            $this->ajaxReturn(array("status" => 1001, "msg" => "任务发送失败"));
            exit();
        }
    }
    
    /**
     * 数据验证
     * @param type $dailyData
     */
    public function checkFormData($dailyData){
          $codeArr = array(
             1005 =>"请选择发送方式", 
             1006 =>"请选择接收者", 
             1007 =>"标题不能为空", 
             1008 =>"标题长度不能超过", 
             1009 =>"内容不能为空", 
             1010 =>"消息内容不能超过", 
          );
          if(empty($dailyData["msg_type"]))    $this->ajaxReturn(array("status" => 1005, "msg" =>$codeArr[1005]));
          if(empty($dailyData["copy_people"]) && empty($dailyData["class_id"]) && empty($dailyData["teacher_id"]))    
                  $this->ajaxReturn(array("status" => 1006, "msg" =>$codeArr[1006]));
          $strConfig = C("msg_str_len_config");
          $strArr = $strConfig[$dailyData["msg_type"]];
          $titleLen = mb_strlen($dailyData["title"],'utf-8');
          $contentLen = mb_strlen($dailyData["content"],'utf-8');
          if($strArr["require"] && empty($dailyData['title']))  $this->ajaxReturn(array("status" => 1007, "msg" =>$codeArr[1007]));
          if($strArr["titlelen"]!=0 && $titleLen>$strArr["titlelen"]) 
              $this->ajaxReturn(array("status" => 1008, "msg" =>$codeArr[1008].$strArr['titlelen']."个字符"));
          if(empty($dailyData["content"]))  $this->ajaxReturn(array("status" => 1009, "msg" =>$codeArr[1009]));
          if($strArr["contentlen"]!=0 && $contentLen >$strArr["contentlen"] )
               $this->ajaxReturn(array("status" => 10010, "msg" =>$codeArr[1010].$strArr['contentlen']."个字符"));
    }
    /**
     * 新建通知
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function createAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            //$this->ajaxReturn(array("status"=>1001,"msg"=>"任务添加成功"));exit();
            $formData = $request->request->all();
            $type = isset($formData['submenu']) ? $formData["submenu"] : "realtime";
            $dailyData['msg_type'] = isset($formData['msgtype']) ? $formData["msgtype"] + 0 : 1;
            $classArr = isset($formData['class']) ? $formData["class"] : array();
            $teacherArr = isset($formData['teacher']) ? $formData["teacher"] : array();
            $dailyData["class_id"] = implode(",", $classArr);
            $dailyData["teacher_id"] = implode(",", $teacherArr);
            $dailyData["copy_people"] = isset($formData["ccpeople"]) ? $formData["ccpeople"] : '';
            $user = $this->getCurrentUser();
            $dailyData["from_uid"] = $user["id"];
            $dailyData['title'] = isset($formData['title']) ? ($formData["title"]) : '';
            if($dailyData["msg_type"] == 1 || $dailyData["msg_type"] == 3){
                $dailyData['content'] = isset($formData['msgContent']) ? ($formData["msgContent"]) : '';
            }else{
                $dailyData['content'] = isset($formData['content']) ? ($formData["content"]) : '';
            }
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
                $this->sendRealMessage($dailyData,$taskId);//发送实时任务
            }
            $this->ajaxReturn(array("status" => 1000, "msg" => "定时通知已经入后台任务中,大约2分钟后完成"));
            exit();
        }
        $submenu = $request->query->get("submenu");
        $submenu = empty($submenu) ? "realtime" : $submenu;
        $user = $this->getCurrentUser();
        $classByCategoryList = $this->getGroupService()->getUserCategoryTree(array("uid" => $user["id"]));
        //班级列表
        $groupList = $this->getGroupService()->getGroupList();
        //获取班级老师
        $teacherList = $this->getGroupService()->getGroupTeacherList();
        return $this->render('Daily:create-task', array("submenu" => $submenu,
                    'controllerCate' => $controllerCate,
                    'classByCategoryList' => $classByCategoryList,
                    "groupList" => $groupList,
                    'teacherList' => $teacherList
        ));
    }

    public function getCCPeopleAction(Request $request) {
        $data = array();
        $queryString = $request->query->get('q');
        $msgtype = $request->query->get('msg_type');
        $callback = $request->query->get('callback');
        $tags = array();
        if ($msgtype == 2) {
            $tags = $this->getUserService()->getEmailByLikeName($queryString);
        } else if ($msgtype == 1) {
            $tags = $this->getUserService()->getMobileByLikeName($queryString);
        } else if ($msgtype == 4) {
            $tags = $this->getUserService()->getNickByLikeName($queryString);
        }else if($msgtype == 3){
           $tags = $this->getUserService()->getNickByLikeName($queryString); 
        }
        foreach ($tags as $tag) {
            $tag["name"] = empty($tag["name"]) ? "无" : $tag["name"];
            $tmp = $tag["nickname"] . '  <' . $tag['name'] . '>;';
            $tmp = htmlspecialchars($tmp);
            $data[] = array('id' => $tag['id'], 'name' => $tmp);
        }

        return $this->createJsonResponse($data);
    }

    /**
     * 意见反馈首页
     * @author fubaosheng 2015-05-11
     */
    public function feedBackAction(Request $request) {
        $fields = $request->query->all();
        $conditions = array(
            'type' => '',
            'roles' => '',
            'from' => '',
        );
        if (empty($fields)) {
            $fields = array();
        } else {
            if (isset($fields['type']) && $fields['type'])
                $fields['type'] = $fields['type'] - 1;
            if (isset($fields['roles']) && $fields['roles'])
                $fields['roles'] = $fields['roles'] - 1;
            if (isset($fields['from']) && $fields['from'])
                $fields['from'] = $fields['from'] - 1;
        }
        $conditions = array_merge($conditions, $fields);
//        dump($conditions);die;
        $paginator = new Paginator(
                $this->get('request'), $this->getFeedBackService()->searchProFeedbackCount($conditions), 20
        );
        $feedBacks = $this->getFeedBackService()->searchProFeedbacks(
                $conditions, array('ctm', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );
        
        return $this->render('Daily:feedBack', array(
                    'feedBacks' => $feedBacks,
                    'paginator' => $paginator,
                    'formData'  => $fields,
        ));
    }
    
    /**
     * 意见反馈弹框
     * @author tanhaitao 2015-10-28
     */
    public function problemShowAction(Request $request,$id) {
        
        
        $problem = $this->getFeedBackService()->getProblemFeedback($id);
        $src = SITE_PATH.$problem['picture'];
            $size = getimagesize($src);
            
            $width  = $size[0];
            $height = $size[1];
            
            if($width > $height){
                if($width > 400){
                    $height = (400/$width)*$height;
                    $width = 400;
                }
                    
            }else{
                if($height > 300){
                    $width = (300/$height)*$width;
                    $height = 300;
                }
            }
            
        return $this->render('Daily:problem-show-modal', array(
                    'problem' => $problem,
                    'width' => $width,
                    'height'=> $height
        ));
    }
    protected function getMessageService() {
        return createService('Message.MsgServiceModel');
    }

    protected function getCateService() {
        return createService('Taxonomy.CategoryService');
    }

    protected function getRoleService() {
        return createService('Role.RoleService');
    }

    protected function getGroupService() {
        return createService("Group.GroupServiceModel");
    }

    protected function getFeedBackService() {
        return createService("User.FeedBackService");
    }
      protected function getPushLogService(){
        return createService('System.PushLogService');
    }

}

?>