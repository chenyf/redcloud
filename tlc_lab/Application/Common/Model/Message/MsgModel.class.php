<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Common\Model\Message;

use Common\Model\Common\BaseModel;
use Common\Lib\SendMessage;
use Common\Twig\Web\WebExtension;
use Common\Lib\ArrayToolkit;
use Common\Lib\MailBat;
class MsgModel extends BaseModel {
    protected $tableName = 'msg_message_task';
    protected $_transport = '';
    protected $_emailer = '';
    protected $_mailer = '';
    /**
     * 获取消息统计列表
     * @param array $paramArr 筛选条件列表
     * @return array 消息列表
     */
    public function getMessageList($paramArr, $limit = 30) {

        $options = array(
            'from_uid' => 0, //发送人的uid,参数类型可以是数组支持传递多个，也可以是字符串，多个的话用英文逗号分隔如123242342,325435345
            'to_uid' => 0,
                // 'status'   => 1,  //-1：作废  0:未开始 1:成功 2:失败',
                // 'ctime'    =>0  //创建时间
        );
        $param = array_merge($options, $paramArr);
        if (empty($param['from_id'])) {
            unset($param['from_uid']);
        }
        if ($param['to_uid'] === "") {
            unset($param['to_uid']);
        }
        if (isset($param['from_uid'])) {
            $param['from_uid'] = is_array($param['from_uid']) ? $param['from_uid'] : explode(",", $param['from_uid']);
            $param['from_uid'] = array('in', $param['from_uid']);
        }
        if (isset($param['to_uid'])) {
            $param['to_uid'] = is_array($param['to_uid']) ? $param['to_uid'] : explode(",", $param['to_uid']);
            $param['to_uid'] = array('in', $param['to_uid']);
        }
        $res = $this->table("msg_message_task")
                ->where($param)
                ->order("ctime desc")
                ->findPage($limit);
        return $res;
    }

    protected function getMessageService() {
        return $this->createService('User.MessageServiceModel');
    }

    /**
     * 发送站内信
     * @param array $paramArr 参数列表
     * @param array/int $uid 接收着uid
     * @return int 1=>发送成功 0=>发送失败 -1=>部分发送失败
     */
    public function postMessage($paramArr, $uid) {
        $option = array(
            'content' => '', //*
            'from_uid' => 0, //发送者id
            'to_uid' => 0, //接受者id
            'title' => '' //标题
        );
        $param = array_merge($option, $paramArr);
        extract($param);
        $to_uid = is_array($to_uid) ? $to_uid : explode(",", $to_uid);
        $length = $resLength = count($to_uid);
        $noticeServer  = createService("User.NotificationServiceModel");
        foreach ($to_uid as $k => $v) {
            $msg_res = $noticeServer->notify($v,"",array("message"=>$content)); //站内信调用通知的接口
            //$msg_res = $this->table("message")->add(array("fromId" => $from_uid, "toId" => $v, "title" => $title, "content" => $content, "createdTime" => time()));
            if (!$msg_res) {
                $length-=1;
            }
        };

        if ($length < $resLength && $length > 0) {
            return -1; //部分失败
        } else if ($length == $resLength && $length > 0) {
            return 1; //成功
        } else if ($length == 0) {
            return 0; //全部失败
        }
    }
    
    public function __construct(){
        parent::__construct();
        $config = $this->setting('mailer', array());
        $this->_transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'])
         ->setUsername($config['username'])
         ->setPassword($config['password']);
        $this->_mailer= \Swift_Mailer::newInstance($this->_transport);
        $this->_emailer =\Swift_Message::newInstance();
        
    }
    public function sendEmail($param) {

        $paramArr = array(
            'title' => "", //标题
            'content' => "", //内容
            'email' => '', //邮箱地址
            'format' => "html" //邮箱格式
                );
        $param = array_merge($paramArr, $param);
        extract($param);
        $format = ($format == 'html') ? 'text/html' : 'text/plain';
        $config = $this->setting('mailer', array());

        if (empty($config['enabled'])) {
            return false;
        }
        
        /*
        $transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'])
          ->setUsername($config['username'])
          ->setPassword($config['password']);
        $mailer = \Swift_Mailer::newInstance($transport);
       
        $emailer = \Swift_Message::newInstance();
         */
        $this->_emailer->setSubject($title);
        $this->_emailer->setFrom(array ($config['from'] => $config['name'] ));
        $this->_emailer->setTo($email);
        if ($format == 'text/html') {
            $this->_emailer->setBody($content, 'text/html');
        } else {
            $this->_emailer->setBody($content);
        }
        try{
           $info = $this->_mailer->send($this->_emailer); 
        }catch(\Swift_TransportException  $e){
            echo 'There was a problem communicating with SMTP: ' . $e->getMessage()."<br/>";
            return false;
        }
        
        return $info;
    }

    protected function setting($name, $default = null) {
        return WebExtension::getSetting($name, $default);
        //    return $this->get('redcloud.twig.web_extension')->getSetting($name, $default);
    }
    
    /**
     *移动端推送
     * @param type $param
     * @author ZhaoZuoWu 2015-05-04
     * @return int 1=>发送成功 0=>发送失败 -1=>部分发送失败
     */
    public function pushMessage($param){
        $options = array(
            'title'=> '', //标题
            'content'=> '', //内容
            'from_uid'=> 0, //发送者用户id 
            'to_uid' =>0,  //被发送用户uid  可以是一个数组,array(3,4) 也可以是一个字符串3,4
            'class_id'=>0,   //班级id  可以是一个数组,array(3,4) 也可以是一个字符串3,4
            'selectSendType'=>0,   //默认等于0给班级推送，如果等于1表明给全部设备推送
        );
       $param = array_merge($options,$param);
       extract($param);
       $classIds = is_array($class_id) ? $class_id:(empty($class_id) ? array():explode(",",$class_id));
       $toUids= is_array($to_uid) ? $to_uid:explode(",",$to_uid);
       $classPushNum = 0;
       $userPushNum = 0;
       $groupPushService = $this->createService('Group.GroupPushService');
       $totalNum+=1;
       $pushResult = 0;
       $pushUserResult = 0;
       //给所有设备推送
       $msgConfig =C('msg_error_log_file');
       $filePath = $msgConfig[3]["filePath"];
       if($selectSendType ==1){
           $totalNum = 1;
           $pushAllOptions = array(
            'title' =>$title,
             'sendUid'      => $from_uid,
            'description'  =>$content,
            'data' => array()
            );
           $result = $groupPushService->pushAllUser($pushAllOptions);
          if($result){
              $pushResult=1;
          }else{
              $this->logError(array('input'=>$pushAllOptions, 'message'=>$result, 'function'=>'pushAllUser'),$filePath);
          }
       }else{
           //给班级推送
           $totalNum = count($classIds);
           foreach($classIds as $key=>$classId){
            $pushClassOptions = array(
            'classId'      => $classId,
            'sendUid'      => $from_uid,
            'title'        => $title,
            'description'  => $content
            );
            $result = $groupPushService->push($pushClassOptions);
            if($result){$pushResult =1;}else{
                $this->logError(array('input'=>$pushClassOptions, 'message'=>$result, 'function'=>'push'),$filePath);
            }
           } 
       }
       $pushUserOptions =array(
           'sendUid' => $from_uid,
            'uid'   => $to_uid,  #int|array
            'title' => $title,
            'description'  => $content,
            'data' => array()
        );
       $result =$groupPushService->pushUser($pushUserOptions);
       if($result){$pushUserResult =1;}else{
            $this->logError(array('input'=>$pushUserOptions, 'message'=>$result, 'function'=>'pushUser'),$filePath);
       }
       $sucessNum = $classPushNum+$userPushNum;
       
       if($pushResult || $pushUserResult){
           return 1;
       }else{
           return 0;
       }
       /*
       if ($sucessNum < $totalNum && $sucessNum > 0) {
            return -1; //部分失败
        } else if ($sucessNum == $totalNum && $sucessNum > 0) {
            return 1; //成功
        } else if ($sucessNum == 0) {
            return 0; //全部失败
        }
        
        */
    }
    /**
     * 根据uid获取用户的邮箱发送邮件
     * @param array $paramArr
     * @param array/string $uid
     * @author ZhaoZuoWu 2014-05-19
     * @return int 1=>发送成功 0=>发送失败 -1=>部分发送失败
     * @edit fbs 2014-10-31
     */
    public function sendMessageByEmail($paramArr) {
        $option = array(
            'content' => '', //*邮箱内容
            'to_uid' => 0,
            'email' => '', //如果邮箱为空，或者没有传值,那么根据用户uid获取email地址(多个邮箱之间可以用,间隔，也可以是一个是数组)
            'title' => '', //邮箱主题
            'from_uid' => 0 //发送者uid (0=>系统用户,1=>普通用户)
        );
        $param = array_merge($option, $paramArr);
        $uid = array();
        if (empty($param['email'])) {
            $uid = is_array($param['to_uid']) ? $param['to_uid'] : explode(",", $param['to_uid']);
            $map['id'] = array('in', $uid);
            $emails = $this->table("user")->where($map)->field("email")->select();
            $message['to'] = getSubByKey($emails, 'email');
        } else {
            $message['to'] = is_array($param['email']) ? $param['email'] : explode(",", $param['email']);
        }
        $length = $resLength = count($message['to']);
        $emailBat = MailBat::getInstance();
        $msgConfig =C('msg_error_log_file');
        $filePath = $msgConfig[2]["filePath"];
        foreach ($message['to'] as $k => $v) {
            $data = array(
                'to' => $v,
                'subject' => $param['title'],
                'html' => $param['content'],
            );
            $res = $emailBat->sendBatMailBySohu($data);
            $result= strpos($res, 'success')!==false ? 1 : 0;
            if (!$result){
                $length-=1;
                $this->logError(array('input'=>$data, 'message'=>$res, 'function'=>'sendMessageByEmail'),$filePath);
            }
        }
        
        if ($length < $resLength && $length > 0) {
            return -1; //部分失败
        } else if ($length == $resLength && $length > 0) {
            return 1; //成功
        } else if ($length == 0) {
            return 0; //全部失败
        }
    }

    /**
     * 发送手机短信消息
     * @param array $paramArr
     * @param string/array 发送者id号 多个uid之间可以用中文逗号分割,也可以以数组的形式
     * @return int 1=>发送成功 0=>发送失败 -1=>部分发送失败
     * @author ZhaoZuoWu  2014-05-19
     */
    public function sendMessageByMobile($paramArr) {
        $option = array(
            'content' => '', //*
            'to_uid' => 0, //接受者id号(to_uid,mobile两者二选一必须要传其中一项)
            'mobile' => '', //如果手机号为空，或者没有传值,那么根据用户uid获取mobile地址(多个手机号之间可以用,间隔，也可以是一个是数组)
            'from_uid' => 0  //发送者uid
        );
        $param = array_merge($option, $paramArr);
        if (empty($param['mobile'])) {
            $uid = is_array($param['to_uid']) ? $param['to_uid'] : explode(",", $param['to_uid']);
            $map['id'] = array('in', $uid);
            $mobiles = $this->table("user")->where($map)->field("verifiedMobile as mobile")->select();
            $message['to'] = getSubByKey($mobiles, 'mobile');
        } else {
            $message['to'] = is_array($param['mobile']) ? $param['mobile'] : explode(",", $param['mobile']);
        }
        $data = array(
            'mobile' => implode(",", $message['to']),
            'content' =>strip_tags($param['content'])."【退订】",
            'uid' => $param["from_uid"],
        );
        $msgConfig =C('msg_error_log_file');
        $filePath = $msgConfig[1]["filePath"];
        $sendMessage = new SendMessage();
        $res = $sendMessage->send($data);
        $res = $res == 0 ? 1 : ($res == -1 ? 0 : -1);
        //$this->logError(array('input'=>$data, 'message'=>$res, 'function'=>'sendMessageByMobile'),$filePath);
        if($res !=1)    $this->logError(array('input'=>$data, 'message'=>$res, 'function'=>'sendMessageByMobile'),$filePath);
        return $res;
    }
    
    public function logError($log,$filePath){
         \Think\Log::write(print_r($log, true), \Think\Log::CRIT, '',$filePath);
    }
    /**
     *根据默写条件查询
     * @param type $map
     */
    public function searchMsgTask($map){
        
        $info = $this->where($map)->select();
        return $info;
    }
    /**
     * 添加任务
     * @param type $data
     * @return type
     */
    public function addMsgTask($data){
        $taskId = $this->add($data);
        return $taskId ? $taskId:0;
    }
    /**
     * 根据任务id保存数据
     * @param type $task_id
     * @param type $data
     */
    public function modifyMsgTaskInfoById($task_id,$data){
        $res = $this->where("task_id='".$task_id."'")->save($data);
        return $res;
    }
    /**
     * 发送消息
     * @param array $paramArr 参数列表
     * @author ZhaoZuoWu 2014-05-16 
     */
    public function sendMessage($paramArr) {
        $options = array(
            'type' => 1, //*消息类型 1=>短信 2=>邮件 3=>客户端推送 4=>站内信
            'content' => '', //*
            'to_uid' => array(), //* 接受者  //type 数组或者字符串
            'title' => '', //标题,邮件主体
            'from_uid' => 0, //发送者uid
            'selectSendType' => 0 //当前type = 3 客户端推送时来判断是给班级推送还是给全部设备推送
        );
        $params = array_merge($options, $paramArr);
        extract($params);
        $to_uid = is_array($to_uid) ? $to_uid : explode(",", $to_uid);
        $messageData['content'] = $content;
        $messageData['to_uid'] = $to_uid;
        $messageData["from_uid"] = $from_uid;
        if ($type == 1) {
            $res = $this->sendMessageByMobile($messageData);
        } else if ($type == 2) {
            $messageData['title'] = $title;
            $res = $this->sendMessageByEmail($messageData);
        } else if ($type == 3) {
            $messageData["title"] = $title;
            $messageData["class_id"] = isset($class_id) ?  $class_id:0;
            $messageData["selectSendType"] = isset($selectSendType) ?  $selectSendType:0;
            $res = $this->pushMessage($messageData); 
        } else if ($type == 4) {
            $messageData['title'] = $title;
            $res = $this->postMessage($messageData);
        }
        if ($res == 1) {
            $info = array('data' => 'SUCCESS', 'info' => '消息发送成功', 'status' => 1);
        } else if ($res == 0) {
            $info = array('data' => 'FAILED_TO_SEND', 'info' => '消息发送失败', 'status' => 0);
        } else if ($res == -1) {
            $info = array('data' => 'FAILED_TO_SEND_PART', 'info' => '部分消息发送失败', 'status' => 1);
        }
        return $info;
    }

    /**
     * 讲消息作废
     * @author ZhaoZuoWu 2014-05-16
     */
    public function doInvalidMessage($message_id) {
        $message_id = is_array($message_id) ? $message_id : explode(",", $message_id);
        foreach ($message_id as $k => $v) {

            if (!is_numeric($v)) {
                unset($message_id[$k]);
            }
        }

        $map['message_id'] = array('in', $message_id);
        $data['status'] = -1;
        $res = $this->table("msg_message_task")->where($map)->save($data);
        return $res;
    }

    /**
     * 获取用户身份所对应的
     * @param type $paramArr
     */
    public function getUserRoleCategoryTree($paramArr) {
        $options = array(
            "id" => 0, //用户id
            "roles" => array(),
            'teacherCategoryId' => "",
            'adminCategoryIds' => "",
            'defineRoles' => '',
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        if (empty($id) || empty($roles))
            return false;
        $categoryTree = array();
//        if(in_array("ROLE_SUPER_ADMIN",$roles)){
//            $categoryTree = createService("Group.GroupServiceModel")->getClassByCategoryId();
//            return $categoryTree;
//        }
        $cateList = array();
        $roles = is_array($roles) ? $roles : explode("|", $roles);
        $adminCategoryIdArr = array();
        if (in_array("ROLE_ADMIN", $roles)) {
            $adminCategoryIdArr = explode(",", $adminCategoryIds);
        }
        if (empty($adminCategoryIdArr) && empty($defineRoles))  return array();
        $roleService = $this->getRoleService();
        $list = array();
        $defineRolesList = json_decode($defineRoles);
        static $roleLevel = array();
        $defineRolesList = (array) $defineRolesList;
        $GLOBALS["rolesIds"] = array();
        array_walk_recursive($defineRolesList, function($value, $key) {
                    array_push($GLOBALS["rolesIds"], $value);
                });
        $categorys = $roleService->getRoleCategorys(array("status" => 1, "id" => array('in', $GLOBALS["rolesIds"])));
        unset($GLOBALS["rolesIds"]);
        $cateList = array();
        foreach ($categorys as $key => $category) {
            $cateObj = json_decode($category["categorys"]);
            $cateArr = get_object_vars($cateObj);
            foreach ($cateArr as $item => $cateVal) {
                if (!isset($cateList[$item]))
                    $cateList[$item] = array();
                $cateList[$item] = array_merge($cateList[$item], $cateVal);
            }
        }
        $cateList['L1'] = array_merge(is_array($cateList['L1']) ? $cateList['L1']:array(), $adminCategoryIdArr);
        return $cateList;
    }
    
      public function analysisTaskDataByTime($startTime,$endTime){
        return $this->field(" count(task_id) as count,from_unixtime(ctime,'%Y-%m-%d') as date")->where("`ctime`>={$startTime} and `ctime`<={$endTime}")->group("from_unixtime(`ctime`,'%Y-%m-%d')")->order("date ASC")->select();
//        $sql="SELECT count(id) as count, from_unixtime(createdTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE`createdTime`>={$startTime} and `createdTime`<={$endTime} group by from_unixtime(`createdTime`,'%Y-%m-%d') order by date ASC ";
//        return $this->getConnection()->fetchAll($sql);
    }
    /**
     * 根据条件查询消息列表
     * @param type $conditions
     * @param type $orderBy
     * @param type $start
     * @param type $limit
     * @return type
     */
    public function searchMessage($conditions, $orderBy, $start, $limit){
    
      $map = $this->createMsgQueryBuilder($conditions);
      $obj = $this;
      #当为中心站时 传入院校code则只查询该校下数据 不传则查询所有院校数据
        $isLocalCenter = \Common\Lib\WebCode::isLocalCenterWeb();
        $siteSelect = !empty($conditions['siteSelect']) ?  $conditions['siteSelect'] : '';
        if($isLocalCenter && $conditions['switch']){ 
            unset($map['from_uid']);
        }
      $list =  $obj
         ->where($map)
         ->field('*')
         ->order("{$orderBy[0]} {$orderBy[1]}")
         ->limit("{$start},{$limit}")
        ->select();
     return $list ? : array();
 }
    /**
     * 根据条件查询消息个数
     * @param type $conditions
     * @return type
     */
    public function searchMsgCount($conditions){
        $map =  $this->createMsgQueryBuilder($conditions);
        
        $obj = $this;
        #当为中心站时 传入院校code则只查询该校下数据 不传则查询所有院校数据
        $isLocalCenter = \Common\Lib\WebCode::isLocalCenterWeb();
        $siteSelect = !empty($conditions['siteSelect']) ?  $conditions['siteSelect'] : '';
        if($isLocalCenter && $conditions['switch']){ 
            unset($map['from_uid']);
        }
        
       $info =  $obj
       ->where($map)->field("count(task_id) as count")->find();
       if(empty($info)) return 0;
       return $info["count"];
        
    }
    
      /**
      * 根据抄送人id获取抄送人列表人数
      * @param type $teacherIds
      * @return type
      */
      public function findCopyNumByCId($copyPeopleIds){
        $list = array();
        foreach($copyPeopleIds as $k=>$v){
             //班级列表
           $userInfo =createService("User.UserModel")->getUserInfoByCustom(array("field"=>"a.nickname as name","where"=>"a.id in(".$v.")",'limit'=>0));
           $titleArr = ArrayToolkit::column($userInfo,"name");
           $list[$k] = count($titleArr);
           //$list[$k] = implode(",", $titleArr);
        }
         return $list;
//        $marks = str_repeat('?,', count($userIds) - 1) . '?';
//        $sql ="SELECT * FROM {$this->tableName} WHERE userId IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $userIds);
    }
    
     /**
      * 根据老师id获取老师列表人数
      * @param type $teacherIds
      * @return type
      */
      public function findTeacherNumByTeacherId($teacherIds){
        $list = array();
        foreach($teacherIds as $k=>$v){
             //班级列表
           $userInfo = createService("User.UserModel")->getUserInfoByCustom(array("field"=>"nickname","where"=>"a.id in(".$v.")",'limit'=>0));
           $titleArr = ArrayToolkit::column($userInfo,"nickname");
           $list[$k] = count($titleArr);
           //$list[$k] = implode(",", $titleArr);
        }
         return $list;
//        $marks = str_repeat('?,', count($userIds) - 1) . '?';
//        $sql ="SELECT * FROM {$this->tableName} WHERE userId IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $userIds);
    }
     /**
      * 根据classid获取班级列表人数
      * @param type $classIds
      * @return type
      */
      public function findClassNumByClassId($classIds){
        $list = array();
        foreach($classIds as $k=>$v){
             //班级列表
           $groupList = $this->getGroupService()->getGroupsByIds($v);
           $titleArr = ArrayToolkit::column($groupList,"title");
           $list[$k] = count($titleArr);
           
           
        }
         return $list;
//        $marks = str_repeat('?,', count($userIds) - 1) . '?';
//        $sql ="SELECT * FROM {$this->tableName} WHERE userId IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $userIds);
    }
      /**
       * 根据消息id查找信息
       * @param type $id
       */
      public function searchMessageById($id){
          return $this->where("task_id=".$id)->find() ? :array();
      }
    /**
     * 对消息条件进行过滤
     * @param type $conditions
     * @return type
     */
    private function createMsgQueryBuilder($conditions){
	    $conditions = array_filter($conditions,function($v){
		    if($v === 0){
			    return true;
		    }

		    if(empty($v)){
			    return false;
		    }
		    return true;
	    });
           $map = array();
	    if (isset($conditions['submenu'])) {
		    $map['noticetype'] = "{$conditions['submenu']}";
                     unset($conditions['submenu']);
	    }
	    if(isset($conditions['keywordType'])  && !empty($conditions["keywordType"])) {
		    $map["msg_type"]=$conditions['keywordType'];
		    unset($conditions['keywordType']);
	    }
            
	    if (isset($conditions['keyword'])) {
		    $map['title'] = array("like","%".$conditions['keyword']."%");
                    unset($conditions["keyword"]);
	    }
            if (isset($conditions['noticetype'])) {
		    $map['noticetype'] = $conditions["noticetype"];
                    unset($conditions["noticetype"]);
	    }
            if (isset($conditions['from_class_id'])) {
		    $map['from_class_id'] = $conditions["from_class_id"];
                    unset($conditions["from_class_id"]);
	    }
             if (isset($conditions['from_uid'])) {
		    $map['from_uid'] = $conditions["from_uid"];
                    unset($conditions["from_uid"]);
	    }
            $ctimeArr= array();
           
            if(!empty($conditions["startTime"])){
                $startTime = strtotime($conditions["startTime"]);
                $ctimeArr[] = array("egt",$startTime);
                 unset($conditions["startTime"]);
                 
            }
              if(!empty($conditions["endTime"])){
                $endTime = strtotime($conditions["endTime"]);
                $ctimeArr[]= array("elt",$endTime);
                 unset($conditions["endTime"]);
            }
            if(!empty($ctimeArr)) $map["ctime"] = $ctimeArr;
            return $map;
    }
    private function getRoleService() {
        return $this->createService("Role.RoleServiceModel");
    }

    private function getCateService() {
        return $this->createService('Taxonomy.CategoryService');
    }
     protected function getGroupService() {
        return $this->createService("Group.GroupServiceModel");
    }

}

?>
