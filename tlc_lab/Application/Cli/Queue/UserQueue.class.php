<?php
/**
 * 用户队列
 * @author yjl 2015-06-15
 */
namespace Cli\Queue;
use Common\Lib\MailBat;
use Common\Lib\SendMessage;
use Common\Services\QueueService;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
class UserQueue {
    /**
     * 重置密码
     */
    public static function resetUserPass($param=array()){
        $options = array(
            'email'      => '',
            'mobile'     => '',
            'userId'     => 0, 
            'org'        => '' ,
            'schoolTitle'=> '',
        );
        $options = array_merge($options, $param);
        extract($options);
        $newPass = rand_string(6,'','123456789');
        $content = "您的密码是：{$newPass}，在{$org}-{$schoolTitle}高校云平台上可以重置密码";

        //echo PHP_EOL. $content.PHP_EOL;
        //将产生的新密码入库
        
        if($userId && $newPass){
            $obj = new MessageDigestPasswordEncoder('sha256');
            $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
            $password = $obj->encodePassword($newPass, $salt);
            
            $fields = array("password"=> $password, 'salt' => $salt);
            $res = createService('User.UserModel')->updateUser($userId,$fields);
        };
        if(!empty($res) && !empty($email)){
            $emailContent = "{$res['nickname']}:您好:<br/>&nbsp;&nbsp;&nbsp;&nbsp;我们{$schoolTitle}大学自己的App正式上线了，手机学习精彩课程，手机做作业，手机摇一摇点名签到，手机班级话题和同学交流。登录名是您的邮箱，您的密码是{$newPass}，第一次登录后请及时修改密码。扫描二维码马上加入我们的高校云<img src='{$org}/System/Mobile/downloadQrcodeAction' width='150' height='150'>，下载地址：<a href='{$org}' target='_blank'>{$org}</a>";
            //发送邮件
            $param['to']      = $email;
            $param['subject'] = $org."-高校云平台密码通知";
            $param['html']    = $emailContent;
            $mailBat = MailBat::getInstance();
            $mailBat->sendMailBySohu($param);
            
        }
        if(!empty($res) && !empty($mobile)){
            $mobileContent = "{$res['nickname']}您好，我们{$schoolTitle}自己的App上线啦，登录名是您的手机号，密码是{$newPass}，第一次登录后请及时修改密码。"; 
            //发送短信
            $param = array();
            $param['mobile'] = $mobile;
            $param['content'] = $mobileContent;
            $param['uid'] = $userId;
            $sendMessage = new SendMessage();
            $res = $sendMessage->sendMessage2($param);
        }
    }
    
    /**
     *重置密码任务
     * @author fubaosheng 2015-11-02
     */
    public function resetPassTask($param=array()){
        $options = array(
            'key'       => '',
            'uid'       => 0,
            'org'        => '',
            'schoolTitle'=> '',
        );
        $options = array_merge($options, $param);
        extract($options);
        $resetPwdService = createService('User.ResetPwdService');
        $resetPwdKeyInfo = $resetPwdService->getResetPwdKeyInfo($key);
        
        //取出人员
        $userArr = array();
        $userPwdArr = array();
        if($resetPwdKeyInfo["type"] == "all"){
            $userArr = createService('User.UserModel')->getUserByField(array('id','email','verifiedMobile','nickname'));
        }
        if($resetPwdKeyInfo["type"] == "recoed"){
            $uidArr = array();
            $resetPwdKeyInfo["recoedList"] = json_decode($resetPwdKeyInfo["recoedList"],true);
            foreach ($resetPwdKeyInfo["recoedList"] as $recoed) {
                $userIds = $resetPwdService->getRecoedUser($recoed);
                $userPwds = $resetPwdService->getRecoedUserPwd($recoed);
                foreach ($userIds as $userId) {
                    if(in_array($userId, array_keys($userPwds)))
                        $userPwdArr[$userId] = $userPwds[$userId];
                    else
                        $userPwdArr[$userId] = rand_string(6,'','123456789');
                }
            }
            $uidArr = array_keys($userPwdArr);
            if(!empty($uidArr))
                $userArr = createService('User.UserModel')->findUsersByIds($uidArr);
        }
        
        //取出测试账号ID
        $testUserId = createService('User.UserModel')->getTestAccountId();
        
        //去除当前用户和测试账号
        foreach ($userArr as $userKey => $userVal) {
            if( ($userVal["id"] == $uid) || ($userVal["id"] == $testUserId) ) unset($userArr[$userKey]);
        }
        $userArr = array_values($userArr);
        
        //发通知
        if(!empty($userArr)){
            $count = count($userArr);
            foreach ($userArr as $k => $user) {
                $status = $resetPwdService->getResetPwdTaskStatus($key);
                if($status == "1"){
                    //修改密码
                    if(in_array($user['id'], array_keys($userPwdArr)))
                        $newPass = $userPwdArr[$user['id']];
                    else
                        $newPass = rand_string(6,'','123456789');
                    $obj = new MessageDigestPasswordEncoder('sha256');
                    $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
                    $password = $obj->encodePassword($newPass, $salt);
            
                    $fields = array("password"=> $password, 'salt' => $salt);
                    createService('User.UserModel')->updateUser($user['id'],$fields);
                    //短信和邮件通知
                    $emailSend = 0;
                    $email = $user['email'] ? : "";
                    if($email){
                        $emailContent = "{$user['nickname']}您好:<br/>&nbsp;&nbsp;&nbsp;&nbsp;我们<a href='{$org}' target='_blank'>{$schoolTitle}大学</a>"
                            ."自己的App正式上线了，手机学习精彩课程，手机做作业，手机摇一摇点名签到，手机班级话题和同学交流。登录名是您的邮箱，您的密码是{$newPass}，"
                            ."第一次登录后请及时修改密码。扫描二维码马上加入我们的高校云<img src='{$org}/System/Mobile/downloadQrcodeAction' width='150' height='150'>，"
                            ."下载地址：<a href='{$org}/app_down/' target='_blank'>{$org}/app_down/</a>";
                        //发送邮件
                        $emailParam = array();
                        $emailParam['to']      = $email;
                        $emailParam['subject'] = $org."-高校云平台密码通知";
                        $emailParam['html']    = $emailContent;
                        $mailBat = MailBat::getInstance();
                        $xml = $mailBat->sendMailBySohu($emailParam);
                         //解析返回的xml
                        $xmlArr = json_decode(json_encode((array) simplexml_load_string($xml)),true);
                        $emailSend = ($xmlArr['message'] == "success") ? 1 : 0;
                    }
                    $mobileSend = 0;
                    $mobile = $user['verifiedMobile'] ? : "";
                    if($mobile){
                        $smsTempLateIdArr = C("SMS_TEMPLATE_ID");
                        $passwordMessageId = $smsTempLateIdArr["PASSWORD_MESSAGE"];
                        $mobileParam = array();
                        $mobileParam['mobile'] = $mobile;
                        $mobileParam['datas'] = array($user['nickname'],$schoolTitle,$newPass,$org.'/app_down/');
                        $mobileParam['templateId'] = $passwordMessageId;
                        $sendMessage = new SendMessage();
                        $res = $sendMessage->sendSms($mobileParam);
                        $mobileSend = ($res == 0) ? 1 : 0;
                    }
                    $resetPwdService->setTaskUserKey(array('key'=>$key,'uid'=>$user['id'],'email'=>$email,'emailSend'=>$emailSend,'mobile'=>$mobile,'mobileSend'=>$mobileSend));
                    //更新状态
                    $resetPwdService->setResetPwdTaskUpdateTm($key);
                    if( ($k+1) == $count)
                        $resetPwdService->setResetPwdTaskStatus($key,3);
                    sleep(0.3);
                }
            }
        }else{
            $resetPwdService->setResetPwdTaskStatus($key,3);
        }
    }
    
    /**
     * 授课班导入成员
     * @author lfj 
     */
    public function courseClassImportMember($param=array()){
        
        $accounts = isset($param['accounts']) ? $param['accounts'] : array();
        $courseId = isset($param['courseId']) ? intval($param['courseId']) : 0; 
        $classId = isset($param['classId']) ? intval($param['classId']) : 0; 
        $taskId = isset($param['taskId']) ? intval($param['taskId']) : 0; 
        $failNum = isset($param['failNum']) ? intval($param['failNum']) : 0; //任务执行失败次数
        $untreated = array();
        
        echo sprintf("[pid=%d]stime=%s" . PHP_EOL, posix_getpid(), date('Y-m-d H:i:s'));
        
        $userSerObj = createService("User.UserService");
        $courseMemberObj = createService('Course.CourseMemberModel');
        $orderSerObj = createService('Order.OrderServiceModel');
        $courseSerObj = createService("Course.CourseServiceModel");
        $classMemberTaskObj = createService('Course.ImportClassMemberTaskService');
        $classMemberDetailObj = createService('Course.ImportClassMemberDetailService');
        $course = $courseSerObj->getCourse($courseId);
        foreach($accounts as $kNum=> $v){
            try { 
                #判断账号是否存在
                $account = str_replace(" ","",$v);
                $uInfo = $userSerObj->getUserByAccount($account);
                
                if (empty($uInfo)) {
                    echo sprintf("[pid=%d]#%d stime=%s  %s" . PHP_EOL, posix_getpid(), $kNum+1, date('Y-m-d H:i:s'), $account, ' no exists!');
                    
                    $status = 1;
                    $remark = '账号不存在';
                } else {                    
                    echo sprintf("[pid=%d]#%d stime=%s  %s" . PHP_EOL, posix_getpid(), $kNum+1, date('Y-m-d H:i:s'), $account);

                    #判断是否加入本课程
                    $joinUids = $courseMemberObj->getJoinUidByUid($courseId, array($uInfo['id']));
                    if (!empty($joinUids)) {
                        #将此学员的授课班id改为当前的授课班id
                        $courseMemberObj->updateUserClass($uInfo['id'], $courseId, $classId);
                    } else {
                        #将学员加入课程并加入本授课班
                        $order = $orderSerObj->createOrder(array(
                            'userId' => $uInfo['id'],
                            'title' => "购买课程《{$course['title']}》(授课班添加)",
                            'targetType' => 'course',
                            'targetId' => $courseId,
                            'amount' => 0,
                            'payment' => 'none',
                            'snPrefix' => 'C',
                            'payType' => 0
                        ));

                        #支付订单
                        $orderSerObj->payOrder(array(
                            'sn' => $order['sn'],
                            'status' => 'success', 
                            'amount' => $order['amount'], 
                            'paidTime' => time(),
                        ));

                        #加入学员
                        $courseSerObj->becomeStudent($order['targetId'], $order['userId'], array('orderId' => $order['id'], 'classId'=>$classId));
                    }
                    
                    $status = 2;
                    $remark = '';
                }
            } catch (Exception $e) {   
                $status = 1;
                $remark = print_r($e->getMessage(),true);
            }   
            
            #更新人员导入状态
            $cmdr = $classMemberDetailObj->updateMemberDetail($taskId, $account, $status, $remark);
            if (!$cmdr) {
                $untreated[] = $account;
                #qzw 2016-1-4
                $dbError[$account] = $classMemberDetailObj->getDbError();
            }
            //\Think\Log::write($account, \Think\Log::CRIT, '', C('QZW'));
        }
        
        echo sprintf("[pid=%d] etime=%s" . PHP_EOL, posix_getpid(), date('Y-m-d H:i:s'));
        
        if (!empty($untreated) && $failNum <4) {
            #将失败的任务再次加入队列
            $param['dbError'] = $dbError;
            $param['testDb'] = $classMemberDetailObj->query('select id from user limit 10,2');
            $param['accounts'] = $untreated;
            $param['courseId'] = $courseId;
            $param['classId'] = $classId;
            $param['taskId'] = $taskId;
            $param['failNum'] = $failNum+1;
            QueueService::addJob(array('jobName'=> 'courseClassImportMember','param' =>$param));
        }
        
        $taskStatus = $classMemberDetailObj->getTaskStatusNum($taskId);
        if ($taskStatus['waitNum'] == 0) {
            #更新任务状态
            $cmtr = $classMemberTaskObj->updateClassMemberStatus($taskId, 2);
            if (!$cmtr)
                $classMemberTaskObj->updateClassMemberStatus($taskId, 2);
        }
        
    }
}