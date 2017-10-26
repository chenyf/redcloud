<?php
/*
 * 用户服务
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Model\Service;

use Common\Lib\EncryptTool;
use Think\Model;
use Think\Cache;
use Common\Model\Common\BaseModel;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Common\Model\User\UserSerializeModel as serialize;


class UserServiceModel extends Model {
    protected $tableName = 'user';

    public $error;

    public $errorCode;
    
    public $pwdErrNum;
    
    /**
     * 用户登录
     */
    public function login($username, $password, $remember = false,$captchaNum,$identity) {
        //登录切换到中心库 fubaosheng 2015-08-07
//        $dbCfg = "DB_CENTER";
//        if($config = C($dbCfg)){
//            $this->db($config['DB_NUM'],$dbCfg);
//        }
//        $do   = $this;
        
        //账号会出现空验证
        if(empty($username)){
            $this->errorCode = 'accountError';
            $this->error = '帐号不存在';
            return false;
        }
        
        //验证(邮箱/手机号)是否存在
        $account = createService('User.UserModel')->findUserByEmail($username);
        if(empty($account)){
            $account = createService('User.UserModel')->findUserByVerifiedMobile($username);
            if(empty($account)){
                $this->errorCode = 'accountError';
                $this->error = '帐号不存在';
                return false;
            }
        }
      
        //验证是否是本校或权限带r(stripos($account['owebPriv'], 'r')===false)
        if( (C('WEBSITE_CODE')!=$account['webCode']) ){
            $this->errorCode = 'accountExistsError';
            $this->error = $username;
            return false;
        }
        
        //验证密码是否正确
        $cacheMem = Cache::getInstance('Memcache');
        $errKey = C("WEBSITE_CODE")."_".get_client_ip()."_".getBrowser();
        $passwd  = $account['password'];
        $salt    = $account['salt'];
        $repassword = $this->getPasswordEncoder()->encodePassword($password, $salt);
        if ($passwd !== $repassword) {
            $errNum = $cacheMem->get($errKey) ? : 0;
            $errNum = intval($errNum)+1;
            $cacheMem->set($errKey,$errNum,300);
            $this->errorCode = "pwdError";
            $this->error = '密码错误';
            return false;
        }

        //验证码是否正确
        $num = $cacheMem->get($errKey) ? : 0;
        if($num>=2){
            if( strtolower($captchaNum) != session('captcha_code') ){
                $this->errorCode = "codeError";
                $this->error = '验证码错误';
                return false;
            }
        }
        $cacheMem->rm($errKey);
        
        //身份验证
        $identityArr = array('1'=>'学生','2'=>'老师');
        if(!array_key_exists($identity, $identityArr)){
            $this->errorCode = "idenSelectError";
            $this->error = '非法请求';
            return false;
        }
        
        //邮箱是否已验证
        $mailerSetting = $this->getSettingService()->get('auth');
        if($mailerSetting['email_enabled'] == "opened"){
            $emailVerified   = $account['emailVerified'];
            $emailField = $account['email'];
            if(!empty($emailField) && $emailVerified == 0 ){
                $this->errorCode = "emailVeriError";
                $this->error = '邮箱未验证，请登录邮箱查看激活邮件，如果激活邮件过期，请重新注册';
                return false;
            }
        }
        
        //是否被封禁
        $locked = $account['locked'];
        if($locked > 0){
            $this->errorCode = "lockError";
            $this->error = '帐号已经被封禁，无法登录';
            return false;
        }
        
        $id    = $account['id'];
        //记录cookie
        if ($remember == true) {
            cookie('user', encrypt($id));
        } else {
            cookie('user', null);
        }
        
        //记录登录状态
        $this->_recordLogin($id);
        $message['status'] = true;
        
        //身份判断
        $account = serialize::unserialize($account);
        $isTeacher = in_array('ROLE_TEACHER',$account['roles']);
        if($identity == '2' && !$isTeacher){
            $this->errorCode = "idenError";
            $this->error = '身份选择错误';
            return false;
        }
        
        return true;
    }

    /**
     * 自动登录
     * @author fubaosheng 2015-09-02
     */
    public function autoLogin($uid){
        cookie('user', encrypt($uid));
        $this->_recordLogin($uid);
    }
    
    /**
     * 检查登录
     * @author fubaosheng 2016-03-02
     */
    public function checkLogin($paramArr){
        $options = array(
            "account"    => "",
            "password"   => "",
            "remember"   => 0,
            "captchaNum" => "",
        );
        $codeArr = array(
            11 => "请输入账号",
            12 => "请输入密码",
            13 => "账号或密码错误",
            14 => "验证码错误",
            15 => "用户不存在，请联系管理员",
            16 => "该账号用户已被锁定，无法进行登录",
        );
        $options = array_merge($options,$paramArr);
        extract($options);
        
        //账号和密码都为空
        if( empty($account) && empty($password) ){
            $this->errorCode = 'empty';
            $this->error = $codeArr[11];
            return false;
        }
        
        //账号为空、密码不为空
        if( empty($account) && !empty($password) ){
            $this->errorCode = 'accountEmpty';
            $this->error = $codeArr[11];
            return false;
        }

        $user = $this->getUserService()->getUserByUserNum($account);
        if(empty($user)) {
            $user = $this->getUserService()->getUserByEmail($account);
            if (empty($user)) {
                $user = $this->getUserService()->getUserByVerifiedMobile($account);
            }
        }
        //用户不存在
        if(empty($user)){
            $this->errorCode = 'noAccount';
            $this->error = $codeArr[15];
            return false;
        }

        //用户被锁定
        if($user['lock'] > 0){
            $this->errorCode = 'accountLock';
            $this->error = $codeArr[16];
            return false;
        }
        
        //密码为空、账号不为空
        if( !empty($account) && empty($password) ){
            $this->errorCode = 'passwordEmpty';
            $this->error = $codeArr[12];
            return false;
        }
        
        //账号和密码都不为空
        if( !empty($account) && !empty($password) ){

            //============改为账号认证接口登录=====
            $userService = $this->getUserService();
            if(C('if_iface')) {
                $authResult = $userService->authLogin($account, $password);
            }else{
                $authResult = $userService->localLogin($account, $password);
            }

            //判断密码是否正确 密码输错三次显示验证码
            $cacheMem = Cache::getInstance('Memcache');
            $cacheKey = C("WEBSITE_CODE")."_".get_client_ip()."_".getBrowser();
            if( $authResult->code !== 20 ) {
                    $errNum = $cacheMem->get($cacheKey) ? : 0;
                    $errNum = intval($errNum)+1;
                    $cacheMem->set($cacheKey,$errNum,300);
                    $this->pwdErrNum = $errNum;
                    $this->errorCode = 'inputError';
                    $this->error = $codeArr[13];
                    return false;
            }
            
            //验证码是否正确
            $num = $cacheMem->get($cacheKey) ? : 0;
            if( $num>=3 ){
                if( strtolower($captchaNum) != session('captcha_code') ){
                    $this->errorCode = "codeError";
                    $this->error = $codeArr[14];
                    return false;
                }
            }
            $cacheMem->rm($cacheKey);

            //记住密码，记录cookie
            if ( $remember ) {
                cookie('user', encrypt($user["id"]));
            } else {
                cookie('user', null);
            }
            
            //记录登录状态
            $this->_recordLogin($user["id"]);
            //记录用户角色
            $this->_recordRole($authResult->role,$account);
    
            return $user;
        }
    }

    /**
     * 注销
     */
    public function logout() {
        session(null);
        cookie('user',null);
    }

    public function getData() {
        #优先从session取uid，未获得再从cookie中取 by qzw 2015-06-04
        $uid = intval(session('id'));

        //代表身份的session
        $roleSession = session(EncryptTool::getSessionRoleKey());

        #qzw_up@redcloud.com -> shrek by qzw 2015-10-19
        if($uid==2438) $uid=2188;
        if(!$uid && $cookieUid=cookie('user')) $uid = decrypt($cookieUid);

        #未登录
        $data = array(
                'id' => 0,
                'nickname' => '游客',
                'currentIp' =>  get_client_ip(),
                'roles' => array(),
                'email' => '',
                'verifiedMobile' => '',
                'newMessageNum' => 0,
                'newNotificationNum' => 0,
                'setup' => 0,
                'uri' => '',
                'teacherCategoryId' =>0,
                'adminCategoryIds' =>"",
                'defineRoles' =>"",
                'title' => '',
                'smallAvatar' => '',
                'largeAvatar' => '',
                'loginTime' => 0,
                'loginIp' => '',
                'userNum' => '',
                'lock'  =>  0,
        );
        #未登录
        if(!$uid){
            return $data;
        }

        if($uid){
            static $userData = array();
            if(!isset($userData[$uid])){

                $user = $this->where(array('id'=>$uid))->find();
                if($user) {
                    $data = array(
                        'roles' => explode('|', trim($user['roles'], '|')),
                        'currentIp' => get_client_ip(),
                        'userNum'   =>  $user['userNum'],
                        'lock'  =>  $user['lock'],
                    );
                }
                $data = array_merge($user, $data);

                //如果当前用户是管理员
                if($roleSession == EncryptTool::getSessionRoleValue(3,$data['userNum'])){
                    array_push($data['roles'],'ROLE_SUPER_ADMIN','ROLE_TEACHER');
                    $data['roles'] = array_unique($data['roles']);
                }

                $userData[$uid] = $data;
            }
            return $userData[$uid];
        }
    }
    
    //记录用户角色
    public function _recordRole($roleCode,$account){
        //记录seession
        session(EncryptTool::getSessionRoleKey(), EncryptTool::getSessionRoleValue($roleCode,$account));
    }

    /**
     * 记录登录状态
     * @param $id
     */
    public function _recordLogin($id) {

        //记录登录时间与IP
        $data['loginTime'] = time();
        $data['loginIp']   = get_client_ip();
        //写登录记录
        $r = $this->where(array('id' => $id))->save($data);
        
        //记录seession
        session('id', $id);
        //记录登录日志
        $this->_recordLoginLog();
        //触发登录事件
        hook('user_login',$this->getUserService()->getCurrentUser());
    }

    /**
     * 记录登录日志
     */
    private function _recordLoginLog() {
        $do = createService('System.LogService');
        $do->info('user','login_success','登录成功');
    }

    private function getPasswordEncoder(){
        return new MessageDigestPasswordEncoder('sha256');
    }

    protected function getSettingService(){
        return createService('System.SettingService');
    }
    
    protected function getUserService(){
        return createService('User.UserService');
    }
}
