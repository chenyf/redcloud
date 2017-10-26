<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use PHPExcel_IOFactory;
use Imagine\Gd\Imagine;
use Imagine\Image\Point;
use Common\Model\User\UserRoleDictModel as UserRoleDict;
use Common\Services\QueueService;
use Common\Lib\MailBat;
use Common\Lib\WebCode;
use Common\Lib\Paginator;
use Common\Services\BatProcessService;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
class UserController extends BaseController 
{

    /**
     * 添加筛选条件
     * @author edit fubaosheng 2015-05-21
     */
    public function indexAction (Request $request){
        $fields = $request->query->all();
        $conditions = array(
            'roles'=>'',
            'keywordType'=>'',
            'keyword'=>'',
            'bindMobile'=>''
        );

        if(empty($fields)){
            $fields = array();
        }

        $conditions = array_merge($conditions, $fields);
        $conditions['keyword'] = trim($conditions['keyword']);

        $conditions['switch'] = true;   # 不影响其他的查询操作 传入一个开关

        $mobileType = array('1'=>'绑定','2'=>'未绑定');

        $paginator = new Paginator(
                $this->get('request'),
                $this->getUserService()->searchUserCount($conditions),
                20
        );
        $users = $this->getUserService()->searchUsers(
                $conditions,
                $paginator->getOffsetCount(),
                $paginator->getPerPageCount()
        );
        $app = $this->getAppService()->findInstallApp("UserImporter");
        $enabled = $this->getSettingService()->get('plugin_userImporter_enabled');

        return $this->render('User:index', array(
                'users'         => $users ,
                'paginator'     => $paginator,
                'formData'=> $fields,
                'webSiteCode'=>C('WEBSITE_CODE')
        ));
    }
    /**
     * 检查excel头部
     * @author 钱志伟  2015-12-17
     */
    private function checkExcelHead($excelFile, array $headItems, $headRow=1){
        if(!file_exists($excelFile)) return false;
        $currentSheet = $this->getPhpExcel($excelFile);
        if($headItems){
            foreach($headItems as $col=>$name){
                $factTxt = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord($col) - 65, $headRow)->getValue()); 
                if($name!=$factTxt) return false;
            }
        }
        return true;
    }
    
    /**
     * 导入用户
     * @author fubaosheng 2016-01-20
     */
    public function importUserAction(Request $request){
        $filePath = isset($_POST['filePath']) ? $_POST['filePath'] : "";
        $userType = isset($_POST['userType']) ? $_POST['userType'] : '';
        $fileName = isset($_POST['fileName']) ? $_POST['fileName'] : '';
        if(empty($userType)) die(json_encode(array("status" => false, "message"=>"系统错误：未知userType")));
        if(empty($filePath)) die(json_encode(array("status" => false, "message"=>"文件路径不正确")));
        if(empty($fileName)) die(json_encode(array("status" => false, "message"=>"文件名为空")));
        $filePath = realpath(__ROOT__).$filePath;
        if(!file_exists($filePath)) die(json_encode(array("status" => false, "message"=>"文件不存在")));
        
        $currentSheet = $this->getPhpExcel($filePath);
        $allRows = $currentSheet->getHighestRow();
        
        #检查表头
        $studExcelHead = array('A'=>'手机', 'B'=>'邮箱', 'C'=>'姓名', 'D'=>'性别', 'E'=>'学号', 'F'=>'班级','G'=>'初始密码');
        $teacherExcelHead = array('A'=>'手机', 'B'=>'邮箱', 'C'=>'姓名', 'D'=>'性别', 'E'=>'（班主任）所管班级','F'=>'初始密码');
        $headItems = $userType == 'teacher' ? $teacherExcelHead : $studExcelHead;
        $r = $this->checkExcelHead($filePath, $headItems, $headRow=1);
        if(!$r) {
            echo json_encode(array("status" => false, "message"=>"您上传的excel文件表头与所选用户身份不符，请修正! "));
            exit;
        }
        
        $accountRowArr = array(); 
        for($currentRow = 2;$currentRow <= $allRows;$currentRow++){
            $verifiedMobile = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('A') - 65, $currentRow)->getValue());
            $accountRowArr[$currentRow]['verifiedMobile'] = $verifiedMobile;
            $email = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('B') - 65, $currentRow)->getValue());
            $accountRowArr[$currentRow]['email'] = $email;
            $nickname = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('C') - 65, $currentRow)->getValue());
            $accountRowArr[$currentRow]['nickname'] = $nickname;
            $sex = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('D') - 65, $currentRow)->getValue());
            $accountRowArr[$currentRow]['sex'] = $sex;
            if($userType!="teacher"){
                $studNum = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('E') - 65, $currentRow)->getValue());
                $className = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('F') - 65, $currentRow)->getValue());
                $initPassword = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('G') - 65, $currentRow)->getValue());
            }else{
                $studNum = '';
                $className = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('E') - 65, $currentRow)->getValue());
                $initPassword = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('F') - 65, $currentRow)->getValue());
            }
            $studNum = trim(str_replace("'", "", $studNum));
            $accountRowArr[$currentRow]['studNum'] = $studNum;
            $className = str_replace('；',';',$className);
            $accountRowArr[$currentRow]['className'] = $className;
            $accountRowArr[$currentRow]['initPassword'] = $initPassword;
        }
        foreach ($accountRowArr as $key=>$account) {
            if(empty($account['verifiedMobile']) && empty($account['email']) && empty($account['nickname']) && empty($account['sex']) && empty($account['studNum']) && empty($account['className']) && empty($account['initPassword'])){
                unset($accountRowArr[$key]);
            }
        }
        $accountRowArr = array_values($accountRowArr);
        //创建重置密码key
        $user = $this->getCurrentUser();
        $userImportKey = $this->getResetPwdService()->setUserImportKey($user['id'],array(),$fileName);
        //开始创建任务
        $code = 'importUser';
        $strId = 'user';
        $data['rowData'] = $accountRowArr;
        $data['extData'] = array('userType'=>$userType,'userImportKey'=>$userImportKey);
        $BatProcessObj = new BatProcessService();
        $r = $BatProcessObj->createTask($code,$strId,$data);
        $batTask = C("BAT_TASK");
        $r['conf'] = $batTask[$code]["itemTask"];
        $this->ajaxReturn($r);
    }
    
    /**
     * 导入用户回调判断
     * @author fubaosheng 2016-01-20
     */
    public function importUserCallBack($paramArr = array()){
        $options = array(
            'code' => '',
            'strId' => '',
            'microtime' => '',
            'itemMicrotime' => '',
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        $codeArr = array(
            1000 => '添加成功',
            1001 => '无扩展数据',
            1002 => '数据为空',
            1003 => '手机号和邮箱至少填写一个',
            1004 => '学号必填',
            1005 => '姓名必填',
            1006 => '姓名应为2-20位的中英文/数字/下划线',
            1007 => '班级为空',
            1008 => '班级%s不存在',
            1009 => '手机号%s格式不正确',
            1010 => '邮箱%s格式不正确',
            1011 => '手机号%s和邮箱%s已存在',
            1012 => '手机号%s已存在',
            1013 => '邮箱%s已存在',
            1014 => '学号%s已经被占用',
            1015 => '%s未成功加入班级%s',
            1016 => '学号%s格式不正确',
            1017 => '密码%s格式不正确',
            1018 => '手机号、邮箱和学号至少填写一个',
            1019 => '密码必填',
            1020 => '添加失败',
            2000 => '导入到临时表'
        );
        
        $BatProcessObj = new BatProcessService();
        $UserServiceObj = createService('User.UserServiceModel');
        $groupObj  = createService('Group.GroupModel');
        $resetPwdService = createService('User.ResetPwdService');
        $UserProfileService = createService('User.UserProfileModel');

        # 先获取父项
        $taskInfo = $BatProcessObj->getTaskInfo($code, $strId, $microtime);
        $extData = json_decode($taskInfo["extData"],true);
        if( empty($extData) )
            return array("status" => false, "message" => $codeArr["1001"]);
        
        # 再获取子项
        $itemInfo = $BatProcessObj->getTaskItemInfo($code, $strId, $itemMicrotime);
        $itemData = json_decode($itemInfo["itemData"],true);
        if( empty($itemData) )
            return array("status" => false, "message" => $codeArr["1002"]);

        # 判断是否都为空
        $verifiedMobile =  $itemData["verifiedMobile"];
        $email          =  $itemData["email"];
        $nickname       =  $itemData["nickname"];
        $sex            =  $itemData["sex"];
        $studNum        =  $itemData["studNum"];
        $className      =  $itemData["className"];
        $initPassword   =  $itemData["initPassword"];
        if( empty($verifiedMobile) && empty($email) && empty($nickname) && empty($sex) && empty($studNum) && empty($className) && empty($initPassword))
            return array("status" => false, "message" => $codeArr["1002"]);
        
        # 姓名必填、必须合法
        if( empty($nickname) )
            return array("status" => false, "message" => $codeArr["1005"]); 
        
        if( !isLegalNickname($nickname) )
            return array("status" => false, "message" => $codeArr["1006"]);
        
        # 性别不填或不是男、女都是男
        if( empty($sex) ||  (!in_array($sex, array('男','女'))) )
            $sex = '男';
        $sex = ($sex == '女') ? 'female' : 'male';
        
        # 班级为选填，填了就判断班级是否存在 学生（一个） 老师（可以多个）
        $groupArr = array();
        if( !empty($className) ){
            if( $userType == "student" ){
                $groupExists = $groupObj->getGroupByTitleFind($className);
                if($groupExists)
                    $groupArr[] = $groupExists;
                else
                    return array("status" => false, "message" => sprintf($codeArr["1008"],$className));
            }else{
                $classNameArr = explode(";", trim($className,";"));
                $classError = "";
                foreach($classNameArr as $classNameVal){
                    $classExists = $groupObj->getGroupByTitleFind($classNameVal);
                    if($classExists)
                        $groupArr[] = $classExists;
                    else
                        $classError.= $classNameVal.",";
                }
                if( $classError )
                    return array("status" => false, "message" => sprintf($codeArr["1008"],trim($classError,",")));
            }
        }
        
        $mobileExists = false;
        $emailExists  = false;
        $studNumExists  = false;
        #填手机了，是否符合规则和是否被占
        if( !empty($verifiedMobile) ){
            if( !isValidMobile($verifiedMobile) )
                return array("status" => false, "message" => sprintf($codeArr["1009"],$verifiedMobile));
            $mobileExists = $UserServiceObj->findUserByVerifiedMobile($verifiedMobile);
        }
        #填邮箱了，是否符合规则和是否被占
        if( !empty($email) ){
            if( !isValidEmail($email) )
                return array("status" => false, "message" => sprintf($codeArr["1010"],$email));
            $emailExists  = $UserServiceObj->findUserByEmail($email);
        }
        #填学号了，是否符合规则和是否被占
        if( !empty($studNum) ){
            if( !isValidStudNum($studNum) )
                return array("status" => false, "message" => sprintf($codeArr["1016"],$studNum));
            $studNumExists = $UserServiceObj->findUserByStudNum($studNum);
        }
        #填密码了，是否符合规则
        if( !empty($initPassword) && !isValidPassword($initPassword) )
            return array("status" => false, "message" => sprintf($codeArr["1017"],$initPassword));
        
        # 学生和老师判断
        $userType = $extData["userType"];
        $userData = array();
        $userData["createdIp"] = get_client_ip();
        $userData["createdTime"] = time();
        $userData["type"] = "default";
        $userData["nickname"] = $nickname;
        $userData["roles"] = "|ROLE_USER|";
        if( $userType == "teacher" ){
            $userData["roles"] .= "ROLE_TEACHER|";
            # 手机、邮箱必填一个
            if( empty($verifiedMobile) && empty($email) )
                return array("status" => false, "message" => $codeArr["1003"]); 
        }else{
            # 手机、邮箱、学号必填一个
            if( empty($verifiedMobile) && empty($email) && empty($studNum) )
                return array("status" => false, "message" => $codeArr["1018"]); 
            #只要学号填，被占用报错
            if( !empty($studNum) && !empty($studNumExists) )
                return array("status" => false, "message" => sprintf($codeArr["1014"],$studNum));
            /*
            * 学号填、邮箱不填、手机不填 （需求说的）
            * 判断密码是否填
            * 没（返回报错）
            * 有 （填学号、姓名、密码，成功后修改性别，不发密码，最后加班，成功就算导入成功）  
            */
            if( empty($verifiedMobile) && empty($email) && !empty($studNum) ){
                if( !empty($initPassword) ){
                    $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
                    $pwd = self::getPasswordEncoder()->encodePassword($initPassword, $salt);
                    $userData["studNum"] = $studNum;
                    $userData["salt"] = $salt;
                    $userData["password"] = $pwd;
                    $user = createService('User.UserModel')->addExcelUser($userData);
                    if( empty($user) )
                        return array("status" => false, "message" => $codeArr["1020"]); 
                    $UserProfileService->addExcelProfile(array("id"=>$user["id"],"gender"=>$sex));
                    $joinGroupError = self::joinUserGroup($user, $groupArr, $userType);
                    if( !empty($joinGroupError) )
                        return array("status" => false, "message" => sprintf($codeArr["1015"],$nickname,trim($joinGroupError,",")));
                }else
                    return array("status" => false, "message" => $codeArr["1019"]);
            }
        }
        
        /*
        * 手机填，邮箱不填，学号密码非必填（需求说的）
        * 判断是否被占 (老师无学号)
        * 1、没有（填手机、姓名、学号，成功后修改性别，放入发送密码，最后加班，失败报错，成功就算导入成功）
        * 2、有（修改姓名、学号、性别，放入发送密码，最后加班，已在班级算加入成功，加入失败报错，成功就算导入成功）  
        * 
        * 邮箱填，手机不填，学号密码非必填（需求说的）
        * 判断是否被占 (老师无学号)
        * 1、没有（填邮箱、姓名、学号，成功后修改性别，放入发送密码，最后加班，失败报错，成功就算导入成功）
        * 2、有（修改姓名、学号、性别，放入发送密码，最后加班，已在班级算加入成功，加入失败报错，成功就算导入成功）  
        */
        if( !empty($verifiedMobile) && empty($email) ){
            $type = "mobile";
            $user = array();
            if( !empty($mobileExists) )
                $user = $mobileExists;
        }
        if( !empty($email) && empty($verifiedMobile) ){
            $type = "email";
            $user = array();
            if( !empty($emailExists) )
                $user = $emailExists;
        }
        if( in_array($type, array('mobile','email')) ){
            if( !empty($user) ){
                $updateArr = array('nickname'=>$nickname);
                if( !empty($studNum) && $userType != "teacher" ) 
                    $updateArr['studNum'] = $studNum;
                $user = $UserServiceObj->updateUser($user["id"],$updateArr);
                $UserProfileService->updateProfile($user["id"],array('gender'=>$sex));
                if( !empty($initPassword) )
                    $resetPwdService->setUserImportUser($extData["userImportKey"],$user["id"],$initPassword);
                $groupArr = self::filterUserGroup($user["id"],$groupArr);
                $joinGroupError = self::joinUserGroup($user, $groupArr, $userType);
                if( !empty($joinGroupError) )
                    return array("status" => false, "message" => sprintf($codeArr["1015"],$nickname,trim($joinGroupError,",")));
            }else{
                if( $type == "email" ){
                    $userData["email"] = $email;
                    $userData["emailVerified"] = 1;
                }
                if( $type == "mobile" )
                    $userData["verifiedMobile"] = $verifiedMobile;
                if( !empty($studNum) && $userType != "teacher" )
                    $userData["studNum"] = $studNum;
                $user = createService('User.UserModel')->addExcelUser($userData);
                if( !empty($user) ){
                    $UserProfileService->addExcelProfile(array("id"=>$user["id"],"gender"=>$sex));
                    $pwd = $initPassword ? $initPassword : rand_string(6,'','123456789');
                    $resetPwdService->setUserImportUser($extData["userImportKey"],$user["id"],$pwd);
                    $joinGroupError = self::joinUserGroup($user, $groupArr, $userType);
                    if( !empty($joinGroupError) )
                        return array("status" => false, "message" => sprintf($codeArr["1015"],$nickname,trim($joinGroupError,",")));
                }else
                     return array("status" => false, "message" => $codeArr["1020"]); 
            }
        }

        # 邮箱填，手机填，学号密码非必填
        if( !empty($verifiedMobile) && !empty($email) ){
            /*
             * 手机被占，邮箱没被占 （需求说的）
             * 修改邮箱、姓名、学号、性别，放入发送密码(老师无学号)
             * 最后加班，已在班级算加入成功，加入失败报错，成功就算导入成功
             * 
             * 邮箱被占，手机没被占 （需求说的）
             * 修改手机、姓名、学号、性别，放入发送密码(老师无学号)
             * 最后加班，已在班级算加入成功，加入失败报错，成功就算导入成功
             * 
             * 邮箱被占，手机被占 （需求说的）
             * 判断是否被同一人占用(老师无学号)
             * 否（返回报错）
             * 是（修改姓名、学号、性别，放入发送密码，最后加班，已在班级算加入成功，加入失败报错，成功就算导入成功）
             */
            if( !empty($mobileExists) && empty($emailExists) ){
                $type = 'mobileExists';
                $user = $mobileExists;
            }
            if( !empty($emailExists) && empty($mobileExists) ){
                $type = 'emailExists';
                $user = $emailExists;
            }
            if( !empty($emailExists) && !empty($mobileExists) ){
                if( ($mobileExists["id"] == $emailExists["id"]) ){
                    $type = 'accountExists';
                    $user = $mobileExists;
                }else
                    return array("status" => false, "message" => sprintf($codeArr["1011"],$verifiedMobile,$email));
            }
            if( in_array($type, array('mobileExists','emailExists','accountExists')) ){
                if( $type == 'mobileExists' )
                    $updateArr = array('email'=>$email,'emailVerified'=>1,'nickname'=>$nickname);
                if( $type == 'emailExists' )
                    $updateArr = array('verifiedMobile'=>$verifiedMobile,'nickname'=>$nickname);
                if( $type == 'accountExists' )
                    $updateArr = array('nickname'=>$nickname);
                if( !empty($studNum) && $userType != "teacher" )
                    $updateArr['studNum'] = $studNum;
                $user = $UserServiceObj->updateUser($user["id"],$updateArr);
                $UserProfileService->updateProfile($user["id"],array('gender'=>$sex));
                if( !empty($initPassword) )
                    $resetPwdService->setUserImportUser($extData["userImportKey"],$user["id"],$initPassword);
                $groupArr = self::filterUserGroup($user["id"],$groupArr);
                $joinGroupError = self::joinUserGroup($user, $groupArr, $userType);
                if( !empty($joinGroupError) )
                    return array("status" => false, "message" => sprintf($codeArr["1015"],$nickname,trim($joinGroupError,",")));
            }
            /*
             * 手机没被占，邮箱没被占 （需求说的）
             * 填手机、邮箱、姓名、学号，成功后修改性别，放入发送密码(老师无学号)
             * 最后加班，加入失败报错，成功就算导入成功
             */
            if( empty($emailExists) && empty($mobileExists) ){
                $userData["verifiedMobile"] = $verifiedMobile;
                $userData["email"] = $email;
                $userData["emailVerified"] = 1;
                if( !empty($studNum) && $userType != "teacher" )
                    $userData["studNum"] = $studNum;
                $user = createService('User.UserModel')->addExcelUser($userData);
                if( !empty($user) ){
                    $UserProfileService->addExcelProfile(array("id"=>$user["id"],"gender"=>$sex));
                    $pwd = $initPassword ? $initPassword : rand_string(6,'','123456789');
                    $resetPwdService->setUserImportUser($extData["userImportKey"],$user["id"],$pwd);
                    $joinGroupError = self::joinUserGroup($user, $groupArr, $userType);
                    if( !empty($joinGroupError) )
                        return array("status" => false, "message" => sprintf($codeArr["1015"],$nickname,trim($joinGroupError,",")));
                }else
                     return array("status" => false, "message" => $codeArr["1020"]); 
            }
        }

        return array("status" => true, "message" => $codeArr["1000"]);
    }
    
    /*
     * 过滤用户已经存在的班级
     */
    private function filterUserGroup($uid,$groupArr){
        if( !empty($groupArr) ){
            $groupMemberObj  = createService('Group.GroupServiceModel');
            $newGroup = array();
            foreach ($groupArr as $k => $group) {
                $r = $groupMemberObj->getMemberByGroupIdAndUserId($group['id'],$uid);
                if(empty($r))
                    $newGroup[$k] = $group;
            }
            return array_values($newGroup);
        }
        return array();
    }
    
    /*
     * 加入班级
     */
    private function joinUserGroup($user,$groupArr,$userType){
        $groupMemberObj  = createService('Group.GroupServiceModel');
        $joinGroupError = "";
        foreach ($groupArr as $group) {
            $joinGroup = $groupMemberObj->joinUserGroup($user ,$group['id'], $userType);
            if( !$joinGroup )
                $joinGroupError.= $group["title"].",";
        }
        return $joinGroupError;
    }
    
    /**
     * 上传excel 并校验
     * @author yjl 2015-06-12
     */
    public function importAction(Request $request){
        $type = isset($_GET['type']) ? $_GET['type'] : 'user';
        if($request->getMethod() == "POST"){
            $userType = isset($_POST['userType']) ? $_POST['userType'] : 'student';
            //判断文件是否存在
            if(!empty($_FILES['file']['name']) && $_FILES['file']['error']==0){
                $res = $this->uploadExcel($_FILES['file']);
            }else{
                return $this->render('User:import', array(
                    'result'    => "请选择文件",
                    'type'      => $type
                ));
            }
            
            //文件上传失败
            if(empty($res['success'])){
                return $this->render('User:import', array(
                    'result'    => $res['error'],
                    'type'      => $type
                ));
            }
            $currentSheet = $this -> getPhpExcel($res['success']);
            $result = $currentSheet->getHighestRow();
            $res['success'] = str_replace(realpath(__ROOT__), "", $res['success']);
            return $this->render('User:import', array(
                'verify'         => true ,
                'result'         => $result-1,
                'filePath'       => $res['success'],
                'userType'       => $userType,
                'type'           => $type,
                'fileName'       => $_FILES['file']['name']
            ));
         }
        
        $data = array('type'=>$type);
        if($type == "resetPwdTask"){
            $count = $this->getResetPwdService()->getResetPwdTaskCount();
            $paginator = new Paginator($this->get('request'), $count, 10);
            $limit = array($paginator->getOffsetCount(),$paginator->getPerPageCount());
            $resetPwdTask = $this->getResetPwdService()->getResetPwdTask($limit);
            $data['resetPwdTask'] = $resetPwdTask;
            $data['paginator'] = $paginator;
        }
        return $this->render('User:import', $data);
    }
    
     /**
     * 添加重置密码任务
     * @author fubaosheng 2015-10-30
     */
    public function resetPwdTaskAction(Request $request){
        $userImportList = $this->getResetPwdService()->getUserImportList();
        if ($request->getMethod() == 'POST') {
            $formData = $request->request->all();
            $type = isset($formData['type']) ? $formData['type'] : "";
            $recoedList = isset($formData['recoedList']) ? $formData['recoedList'] : array();
            if(!in_array($type, array('all','recoed')))
                return $this->createJsonResponse(array('success'=>false,'message'=>'非法操作'));
            if($type == "all")
                $recoedListArr = array();
            if($type == "recoed"){
                $recoedArr = array();
                foreach ($userImportList as $key => $value) {
                    $recoedArr[$key] = $value["key"];
                }
                $recoedArr = array_values($recoedArr);
                $recoedListArr = array_values(array_intersect($recoedList, $recoedArr));
                if(empty($recoedListArr))
                    return $this->createJsonResponse(array('success'=>false,'message'=>'请选择一条记录'));
            }
            $user = $this->getCurrentUser();
            $r = $this->getResetPwdService()->setResetPwdKey(array('uid'=>$user['id'],'type'=>$type,'recoedListArr'=>$recoedListArr));
            if(!$r)
                return $this->createJsonResponse(array('success'=>false,'message'=>'添加重置密码任务失败'));
            else
                return $this->createJsonResponse(array('success'=>true,'message'=>'添加重置密码任务成功'));
        }
        return $this->render('User:reset-pwd-task',array(
            'userImportList'=>$userImportList
            )
        );
    }
    
    /**
     * 开始/终止任务
     * @author fubaosheng 2015-11-05
     */
    public function updateResetPwdTaskStatusAction(Request $request){
        if ($request->getMethod() == 'POST'){
            $formData = $request->request->all();
            $type = isset($formData['type']) ? $formData['type'] : "";
            if(!in_array($type, array('start','kill')))
                return $this->createJsonResponse(array('success'=>false,'message'=>'非法操作'));
            $key = isset($formData['key']) ? $formData['key'] : "";
            $exists = $this->getResetPwdService()->isExistsKey($key);
            if(!$exists)
                return $this->createJsonResponse(array('success'=>false,'message'=>'任务不存在'));
            $status = $this->getResetPwdService()->getResetPwdTaskStatus($key);
            if($type == "kill"){
                if($status == "2")
                    return $this->createJsonResponse(array('success'=>false,'message'=>'任务已被终止'));
                if($status == "3")
                    return $this->createJsonResponse(array('success'=>false,'message'=>'任务已结束'));
                $this->getResetPwdService()->setResetPwdTaskStatus($key,2);
            }
            if($type == "start"){
                if($status == "1")
                    return $this->createJsonResponse(array('success'=>false,'message'=>'任务进行中'));
                $this->getResetPwdService()->setResetPwdTaskStatus($key,1);
                $this->getResetPwdService()->setResetPwdTaskStartTm($key);
                $user = $this->getCurrentUser();
                $param = array(
                    'key' => $key,
                    'uid'  => $user["id"], 
                    'org'    => getScheme().'://'.$_SERVER['HTTP_HOST'],
                    'schoolTitle' => getSetting("site")['name']
                );
                $jobId = QueueService::addJob(array(
                    'jobName'=> 'resetPassTask',
                    'param'  => $param,
                ));
            }
            return $this->createJsonResponse(array('success'=>true));
        }
        return $this->createJsonResponse(array('success'=>false,'message'=>'错误的提交方式'));
    }
    
    /**
     * 查看历史页面
     * @author fubaosheng 2015-11-05
     */
    public function showTaskUserAction(Request $request,$key){
        $count = $this->getResetPwdService()->getTaskUserCount($key);
        $paginator = new Paginator($this->get('request'), $count, 10);
        $limit = array($paginator->getOffsetCount(),$paginator->getPerPageCount());
        $taskUser = $this->getResetPwdService()->getTaskUser($key,$limit);

        return $this->render('User:show-task-user',array(
            'paginator'=>$paginator,
            'taskUser'=>$taskUser,
            )
        );
    }
    
    /**
     * 任务实时用户页面
     * @author fubaosheng 2015-11-05
     */
    public function seeTaskUserAction(Request $request,$key){
         return $this->render('User:see-task-user',array(
            'key' => $key,
            )
        );
    }
    
    /**
     * 实时用户
     * @author fubaosheng 2015-11-05
     */
    public function pollTaskUserAction(Request $request){
        $data = $request->request->all();
        $key = isset($data["key"]) ? $data["key"] : "";
        $tm = (isset($data["tm"]) && $data["tm"]) ? $data["tm"] : getMicroTm();
        $resetPwdService = $this->getResetPwdService();
        $i = 0;
        while($i++<55){
            $pollTaskUser = $resetPwdService->getPollTaskUser(array('key'=>$key,'tm'=>$tm));
            if(!empty($pollTaskUser)){
                $this->ajaxReturn(array('success'=>true,'info'=>'有数据','data'=>array('tm'=>getMicroTm(),'data'=>$pollTaskUser)));
            }
            sleep(1);
        }
        $this->ajaxReturn(array('success'=>false,'info'=>'请求超时'));
    }
    
     /*
     * 更新用户
     * @author yjl 2015-06-12
     */
    
    public function updateUser($type,$field){
        $obj    =    createService('User.UserModel');
        if($type == "email"){
            $result = $obj->findUserByEmail($field['email']);
        }else{
            $result = $obj->findUserByVerifiedMobile($field['verifiedMobile']);
        }
        $r = $obj ->updateUser($result['id'], $field);
        return $r;
    }
    
    /*
     * 上传excel文件
     * @author yjl 2015-06-12
     */
    private function uploadExcel($param = array()){
        $options = array(
            "name"  => "",
            "tmp_name"  => "",
            "size"  => 0,
        );
        $options = array_merge($options, $param);
        extract($options);
        $info = pathinfo($name);
        $fileType = $info['extension'];
        if(strtolower($fileType) != "xls" && strtolower($fileType) != "xlsx"){
            return array("error"=>'不是Excel文件，请重新选择文件上传');
        }
        if($size > 10485760){
            return array("error"=>'文件的大小不符合要求');
        }
        /*设置上传路径*/
        $savePath = getParameter('redcloud/upload/public_directory')."/tmp/";
        /*以时间来命名上传的文件*/
        $str = date('Ymdhis'); 
        $file_name = $str.".".$fileType;
        /*是否上传成功*/
        if (copy($tmp_name, $savePath.$file_name)){
            return array("success" => $savePath.$file_name);
        }else{
            return array("error"=>'上传失败');
        }
    }
    
    
    /*
     * 获取excel表对象
     * @author yjl 2015-06-12
     */
    private function getPhpExcel($filePath){
        if($filePath){
            require_once("./Vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php");
            //获取excel文件
            $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
            $currentSheet = $objPHPExcel->getSheet(0);
            return $currentSheet;          
        }
    }
    
     /*
     * 用户导入成功后 添加user_profile
     * @author yjl 2015-06-12
     */
    private function addUserProfile($paramArr=array(), $arr){
        $options = array(
            "id"   => 0,
            "sex"  => "female",
            "info" => "",
            "row"  => 0,
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        
        $mod  = createService('User.UserProfileModel');
        if(empty($sex))  {
            $sex = "female";
        }else{
            $sex = str_replace(" ", "", $sex);
        }
        if($sex == '男') $sex = "male";
        if($sex == '女') $sex = "female";
        $fields = array("id" => $id, "gender" => $sex);
        
        $userExists = createService('User.UserServiceModel')->getUserProfile($id);
        if( empty($userExists) ){
            $r = $mod->addExcelProfile($fields);
        }else{
            return ;
        }
        
        //如果添加失败 在添加一次
        if(!$r){
            $r = $mod->addExcelProfile($fields);
        }
        
        //如果再次失败 发送邮件给管理员
        if(!$r){
            $content = "通过excel导入用户时，第".$row."行，".$info."在添加到user_profile表中时失败，请检查";
            $param['subject'] = $_SERVER['SERVER_NAME']."-高校云平台通知";
            $param['html']    = $content;
            
            $configArr = C("SYSTEM_MANAGER");
            foreach($configArr as $v){
                $param['to'] = $v;
                $mailBat     = MailBat::getInstance();
                $mailBat->sendMailBySohu($param);
            }
        }
    }
    
    
    /*
     * 获取所有user_profile表用户信息
     * @author yjl 2015-06-16
     */
    private function getAllUserProfile(){
        $mod  = createService('User.UserProfileModel');
        $userProfileArr = $mod->getAll();
        $userProfileArr = array_column($userProfileArr, "id");
        return $userProfileArr;
    }
    
     /*
     * 根据导入类型获取所有user表用户信息
     * @author yjl 2015-06-16
     */
    private function getAllUser($type){
        $obj  = createService('User.UserModel');
        if($type=='email'){
            $userArr = $obj->getUserByFieldExcel('email');
            $userArr = array_column($userArr, 'email');
        }elseif(($type=='Mobile')){
            $userArr = $obj->getUserByFieldExcel('verifiedMobile');
            $userArr = array_column($userArr, 'verifiedMobile');
        }
        return $userArr;
    }
    
    public function mobileCheckAction(Request $request)
    {
        $mobile = $request->query->get('value');
        $mobile = str_replace('!', '.', $mobile);
        list($result, $message) = $this->getAuthService()->checkMobile($mobile);
        if ($result == 'success') {
            $response = array('success' => true, 'message' => '该手机号可以使用');
        } else {
            $response = array('success' => false, 'message' => $message);
        }
        return $this->createJsonResponse($response);
    }
    
    /**
     * 学号检查不重复(同一学校)
     * @author fubaosheng 2015-09-22
     */
    public function studNumCheckAction(Request $request){
        $studNum = $request->query->get('value');
        $uid = $request->query->get('uid');
        if(strlen($studNum)<1){
            return $this->createJsonResponse(array('success'=>true));
        }
        $result = $this->getUserService()->isUseStudNumStudNumAvaliable($studNum ,$uid);
        if ($result) {
            $response = array('success' => true, 'message' => '该学号可以使用');
        } else {
            $response = array('success' => false, 'message' => '该学号已被使用');
        }
        return $this->createJsonResponse($response);
    }

    public function emailCheckAction(Request $request)
    {
        $email = $request->query->get('value');
        $email = str_replace('!', '.', $email);
        list($result, $message) = $this->getAuthService()->checkEmail($email);
        if ($result == 'success') {
            $response = array('success' => true, 'message' => '该Email地址可以使用');
        } else {
            $response = array('success' => false, 'message' => $message);
        }
        return $this->createJsonResponse($response);
    }

    public function nicknameCheckAction(Request $request)
    {
        $nickname = $request->query->get('value');
        list($result, $message) = $this->getAuthService()->checkUsername($nickname);
        if ($result == 'success') {
            $response = array('success' => true, 'message' => '该姓名可以使用');
        } else {
            $response = array('success' => false, 'message' => $message);
        }
        return $this->createJsonResponse($response);
    }

    public function createAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $formData = $request->request->all();
            $str = $formData['email'];
            if(strpos($str, '@')!==false){
                $userData['email'] = $formData['email'];
                $userData['emailVerified'] = intval($formData['emailVerified']);
            }else{
                $userData['verifiedMobile'] = $formData['email'];
                $userData['email'] = '';
                $userData['regType'] = 'mobile';
            }
            $userData['nickname'] = $formData['nickname'];
            $userData['password'] = $formData['password'];
            $userData['createdIp'] = $request->getClientIp();
            $user = $this->getAuthService()->register($userData);
            $this->get('session')->set('registed_email', $user['email']);

            if(isset($formData['roles'])){
                $roles[] = 'ROLE_TEACHER';
                array_push($roles, 'ROLE_USER');
                $this->getUserService()->changeUserRoles($user['id'], $roles);
            }

            $this->getLogService()->info('user', 'add', "管理员添加新用户 {$user['nickname']} ({$user['id']})");

           $this->redirect($this->generateUrl('admin_user'));
        }
        return $this->render('User:create-modal');
    }

    public function editAction(Request $request, $id)
    {
        $user = $this->getUserService()->getUser($id);

        $profile = $this->getUserService()->getUserProfile($user['id']);
        if ($request->getMethod() == 'POST') {
            $profile = $request->request->all();

            $result = $this->getUserService()->updateUserProfile($user['id'], $profile);
            if($result->code != 20){
                return $this->createJsonResponse(array('status'=>false,'msg'=>$result->msg));
            }else{
                $this->getLogService()->info('user', 'edit', "管理员编辑用户资料 {$user['nickname']} (#{$user['id']})", $profile);
                return $this->createJsonResponse(array('status'=>true,'msg'=>''));
            }

        }

        return $this->render('User:edit-modal', array(
            'user' => $user,
            'profile'=>$profile
        ));
    }

    public function showAction(Request $request, $id)
    {
        $user = $this->getUserService()->getUser($id);
        $profile = $this->getUserService()->getUserProfile($id);
        $profile['title'] = $user['title'];

        $fields=$this->getFields();
            
        return $this->render('User:show-modal', array(
            'user' => $user,
            'profile' => $profile,
            'fields'=>$fields,
        ));
    }

	/**
	 * 设置用户的超级管理员身份
	 * @param $userId
	 * @param $status
	 */
	public function setSuperAdminAction($userId,$status) {
		if ($this->user->is_super_admin()) {
			$this->getUserService()->updateUser($userId, ['super_admin' => $status]);
			$this->success('设置成功');
		}
	}

    /**
     * 用户角色
     * @author fubaosheng 2015-04-28
     */
    public function rolesAction(Request $request, $id)
    {
        $currentUser = $this->getCurrentUser();
        if (empty(goBackEnd())) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUserService()->getUser($id);
        if ($request->getMethod() == 'POST') {
            
            $roles = $request->request->get('roles');
            #只有中心站设神管和大客户 fubaosheng 2015-07-13
            if(!WebCode::isLocalCenterWeb()){
                if(in_array('ROLE_GOLD_ADMIN',$user['roles'])){
                    if(!in_array('ROLE_GOLD_ADMIN', $roles)) array_push($roles, 'ROLE_GOLD_ADMIN');
                }else{
                    if(in_array('ROLE_GOLD_ADMIN', $roles)){
                        $gkey = array_search('ROLE_GOLD_ADMIN', $roles);
                        unset($roles[$gkey]);
                    }
                }
                if(in_array('ROLE_MARKET',$user['roles'])){
                    if(!in_array('ROLE_MARKET', $roles)) array_push($roles, 'ROLE_MARKET');
                }else{
                    if(in_array('ROLE_MARKET', $roles)){
                        $mkey = array_search('ROLE_MARKET', $roles);
                        unset($roles[$mkey]);
                    }
                }
            }
            #超管才能设置超管
            if(empty(isGranted("ROLE_SUPER_ADMIN"))){
                if(in_array('ROLE_SUPER_ADMIN',$user['roles'])){
                    if(!in_array('ROLE_SUPER_ADMIN', $roles)) array_push($roles, 'ROLE_SUPER_ADMIN');
                }else{
                    if(in_array('ROLE_SUPER_ADMIN', $roles)){
                        $skey = array_search('ROLE_SUPER_ADMIN', $roles);
                        unset($roles[$skey]);
                    }
                }
            }
            $r = WebCode::isLocalCenterWeb() || $user['webCode']!=C("WEBSITE_CODE");
            #中心站或非本站不能设置老师
            if($r){
                if(in_array('ROLE_TEACHER',$user['roles'])){
                    if(!in_array('ROLE_TEACHER', $roles)) array_push($roles, 'ROLE_TEACHER');
                    if(!empty($user['teacherCategoryId'])) $teacherCategoryId = $user['teacherCategoryId'];
                }else{
                    if(in_array('ROLE_TEACHER', $roles)){
                        $tkey = array_search('ROLE_TEACHER', $roles);
                        unset($roles[$tkey]);
                    }
                    $teacherCategoryId = 0;
                }
            }else{
                $teacherCategoryId = $request->request->get('teacherCategoryId');
            }
            #中心站或非本站或本站非超管不能设置管理员
            $aDel = !WebCode::isLocalCenterWeb() && ($user['webCode']==C("WEBSITE_CODE")) && !isGranted("ROLE_SUPER_ADMIN");
            if($r || $aDel){
                if(in_array('ROLE_ADMIN',$user['roles'])){
                    if(!in_array('ROLE_ADMIN', $roles)) array_push($roles, 'ROLE_ADMIN');
                    if(!empty($user['adminCategoryIds'])) $adminCategoryIds = $user['adminCategoryIds'];
                }else{
                    if(in_array('ROLE_ADMIN', $roles)){
                        $akey = array_search('ROLE_ADMIN', $roles);
                        unset($roles[$akey]);
                    }
                    $adminCategoryIds = null;
                }
            }else{
                $adminCategoryIds = $request->request->get('adminCategoryIds');
            }
            
            $roles = array_values(array_unique($roles));
            #中心站或非本站不能自定义
            if($r){
                if(!empty($user['defineRoles'])) $defineRoles = $user['defineRoles'];
                else $defineRoles = null;
            }else{
                $defineRoles = $request->request->get('defineRoles');
            }
            
            $this->getUserService()->changeUserRoles($user['id'], $roles);  
            $this->getUserService()->changeUserTeacherCategoryId($user['id'], (int)$teacherCategoryId);
            $this->getUserService()->changeUserAdminCategoryIds($user['id'], $adminCategoryIds);
            $this->getUserService()->changeUserDefineRoles($user['id'], $defineRoles);
                 
            $dataDict = new UserRoleDict();
            $roleDict = $dataDict->getDict();
            $newRole = array('ROLE_GOLD_ADMIN'=>'神管','ROLE_MARKET'=>'大客户');
            $roleDict = array_merge($roleDict,$newRole);
            $role = "";
            $roleCount = count($roles);
            $deletedRoles = array_diff($user['roles'], $roles);
            $addedRoles = array_diff($roles, $user['roles']);
            if(!empty($deletedRoles) || !empty($addedRoles) ){
                for ($i=0; $i<$roleCount; $i++) {
                    $role .= $roleDict[$roles[$i]];
                    if ($i<$roleCount - 1){
                        $role .= "、";
                    }
                }
                $this->getNotifiactionService()->notify($user['id'],'default',"您被“{$currentUser['nickname']}”设置为“{$role}”身份。");
            }

            if (in_array('ROLE_TEACHER', $user['roles']) && !in_array('ROLE_TEACHER', $roles)) {
                $this->getCourseService()->cancelTeacherInAllCourses($user['id']);
            }

            $user = $this->getUserService()->getUser($id);
            return $this->render('User:user-table-tr', array(
            'user' => $user,
            ));
        }      
        $default = $this->getSettingService()->get('default', array());     
        $topCategory = $this->getCateService()->getTopCategory();
        $roles = $this->getRoleService()->getRoleList(array('status'=>'enabled'));
        
        $roleCategory = array();
        foreach ($roles as $k => $v) {
            $categoryInfo                = $this->getCateService()->getCategoryById($v['categoryId']);
            if($categoryInfo){
                if($categoryInfo['isDelete'] == 1) $categoryInfo['name'] = "【已删除】".$categoryInfo['name'];
                $roleCategory[$v['categoryId']]['name'] = $categoryInfo['name'];
                $roleCategory[$v['categoryId']]['id'] = $v['categoryId'];
                $roleCategory[$v['categoryId']]['role'][] = $v;
            }
        }
        $roleCategory = array_values($roleCategory);
        
        $userDefineRoles = $user["defineRoles"];
        if($userDefineRoles){
            $userDefineRoles = json_decode($userDefineRoles,true);
            foreach ($roleCategory as $key => $category) {
                if(in_array($category['id'],  array_keys($userDefineRoles))){
                    foreach ($category['role'] as $categoryKey => $categoryRrole) {
                        if(in_array($categoryRrole["id"],$userDefineRoles[$category['id']])){
                            $roleCategory[$key]['role'][$categoryKey]['isDefine'] = 1;
                        }
                    }
                }
            }
        }
        
        return $this->render('User:roles-modal', array(
            'user' => $user,
            'default'=> $default,
            'topCategory' => $topCategory,
            'roleCategory' => $roleCategory,
            'webSiteCode'=>C('WEBSITE_CODE')
        ));
    }

    public function avatarAction(Request $request, $id)
    {
        if (false === isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUserService()->getUser($id);

        if ($request->getMethod() == 'POST') {
                if (empty($_FILES['form']) && !isset($_FILES['form'])) {
                    return $this->createJsonResponse(FALSE);
                }
                /* 实例化上传类 */
                $do = new \Think\Upload();
                $do->autoSub = false;
                $tmp = $_FILES['form'];
                $values = array();
                foreach($tmp as $k=>$v){
                    $values[] = $v['avatar'];
                }
                $keys = array_keys($tmp);
                $data = array_combine($keys,$values);
                $filenamePrefix = "user_{$user['id']}_";
                $hash = substr(md5($filenamePrefix . time()), -8);
                $filename = $filenamePrefix . $hash;

                $directory = getParameter('redcloud.upload.public_directory') . '/tmp/';

                $do->rootPath = $directory;
                $do->saveName = $filename;
                /* 执行上传 */
                $info = $do->uploadOne($data);
                $fileName = str_replace('.', '!',  $info['savename']);
                $avatarData = $this->avatar_2($id, $fileName);

                return $this->render('User:user-avatar-crop-modal', array(
                    'user' => $user,
                    'filename' => $fileName,
                    'pictureUrl' => $avatarData['pictureUrl'],
                    'naturalSize' => $avatarData['naturalSize'],
                    'scaledSize' => $avatarData['scaledSize']
                ));
            
        }

        $hasPartnerAuth = $this->getAuthService()->hasPartnerAuth();
        if ($hasPartnerAuth) {
            $partnerAvatar = $this->getAuthService()->getPartnerAvatar($user['id'], 'big');
        } else {
            $partnerAvatar = null;
        }

        return $this->render('User:user-avatar-modal', array(
            'user' => $this->getUserService()->getUser($user['id']),
            'partnerAvatar' => $partnerAvatar,
        ));
    }

    private function getFields()
    {
        $fields=$this->getUserFieldService()->getAllFieldsOrderBySeqAndEnabled();
        for($i=0;$i<count($fields);$i++){
            if(strstr($fields[$i]['fieldName'], "textField")) $fields[$i]['type']="text";
            if(strstr($fields[$i]['fieldName'], "varcharField")) $fields[$i]['type']="varchar";
            if(strstr($fields[$i]['fieldName'], "intField")) $fields[$i]['type']="int";
            if(strstr($fields[$i]['fieldName'], "floatField")) $fields[$i]['type']="float";
            if(strstr($fields[$i]['fieldName'], "dateField")) $fields[$i]['type']="date";
        }

        return $fields;
    }

    private function avatar_2 ($id, $filename)
    {
        if (false === isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $currentUser = $this->getCurrentUser();

        $filename = str_replace('!', '.', $filename);
        $filename = str_replace(array('..' , '/', '\\'), '', $filename);
        $pictureFilePath = getParameter('redcloud.upload.public_directory') . '/tmp/' . $filename;

        try {
            $imagine = new Imagine();
            $image = $imagine->open($pictureFilePath);
        } catch (\Exception $e) {
            @unlink($pictureFilePath);
            return $this->createMessageResponse('error', '该文件为非图片格式文件，请重新上传。');
        }

        $naturalSize = $image->getSize();
        $scaledSize = $naturalSize->widen(270)->heighten(270);
        $pictureUrl = 'tmp/' . $filename;

        return array(
            'naturalSize' => $naturalSize,
            'scaledSize' => $scaledSize,
            'pictureUrl' => $pictureUrl
        );
    }

    public function avatarCropAction(Request $request, $id)
    {
        if (false === isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if($request->getMethod() == 'POST') {
            $options = $request->request->all();
            $filename = $request->query->get('filename');
            $filename = str_replace('!', '.', $filename);
            $filename = str_replace(array('..' , '/', '\\'), '', $filename);
            $pictureFilePath = getParameter('redcloud.upload.public_directory') . '/tmp/' . $filename;
            $this->getUserService()->changeAvatar($id, realpath($pictureFilePath), $options);

            // return $this->redirect($this->generateUrl('admin_user'));
            return $this->createJsonResponse(true);
        }

        
    }
    
    /**
     * 解绑手机号
     * @author fubaosheng 2015-04-28
     */
    public function unbindMobileAction(Request $request,$id)
    {
        $this->getUserService()->unbindMobile($id);
        return $this->render('User:user-table-tr', array(
            'user' => $this->getUserService()->getUser($id),
        ));
    }

    public function lockAction(Request $request,$id)
    {
        $this->getUserService()->lockUser($id,'admin');
        return $this->render('User:user-table-tr', array(
            'user' => $this->getUserService()->getUser($id),
        ));
    }

    public function unlockAction(Request $request,$id)
    {
        $this->getUserService()->unlockUser($id);

        return $this->render('User:user-table-tr', array(
            'user' => $this->getUserService()->getUser($id),
        ));
    }

    protected function getNotificationService()
    {
        return createService('User.NotificationService');
    }

    protected function getLogService()
    {
        return createService('System.LogService');
    }

    protected function getSettingService()
    {
        return createService('System.SettingService');
    }

    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getAuthService()
    {
        return createService('User.AuthService');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }

    protected function getUserFieldService()
    {
        return createService('User.UserFieldService');
    }

    protected function getNotifiactionService()
    {
        return createService('User.NotificationService');
    }
    
    protected function getCateService() {
        return createService('Taxonomy.CategoryService');
    }
    
    protected function getRoleService() {
        return createService('Role.RoleService');
    }
    
    protected function getResetPwdService() {
        return createService('User.ResetPwdService');
    }
    
    private function getPasswordEncoder(){
        return new MessageDigestPasswordEncoder('sha256');
    }
    
}