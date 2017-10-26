<?php
namespace User\Controller;

use Think\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Common\Component\OAuthClient\OAuthClientFactory;

class SigninController extends \Home\Controller\BaseController {

	public function indexAction(Request $request) {
            $user = $this->getCurrentUser();
            if ($user->isLogin()) {
                return $this->redirect(U('My/MyCourse/index'));
            }
            //获取密码输错的次数
            $cacheMem = Cache::getInstance('Memcache');
            $cacheKey = C("WEBSITE_CODE")."_".get_client_ip()."_".getBrowser();
            $errNum = $cacheMem->get($cacheKey) ? : 0;
            
            //获取是否需要学号
            $loginBind = $this->getSettingService()->get('login_bind');
            $studNumLogin = ($loginBind["studNumLogin"] == 1) ? 1 : 0;
            return $this->render('Signin/login',array(
                "errorNum" => $errNum,
                "studNumLogin" => $studNumLogin,
            ));
	}

    public function loginAction(Request $request){
        $user = $this->getCurrentUser();
        if ($user->isLogin()) {
            return $this->redirect(U('My/MyCourse/index'));
        }
        //获取密码输错的次数
        $cacheMem = Cache::getInstance('Memcache');
        $cacheKey = C("WEBSITE_CODE")."_".get_client_ip()."_".getBrowser();
        $errNum = $cacheMem->get($cacheKey) ? : 0;

        //获取是否需要学号
        $loginBind = $this->getSettingService()->get('login_bind');
        $studNumLogin = ($loginBind["studNumLogin"] == 1) ? 1 : 0;
        return $this->render('Signin/index',array(
            "errorNum" => $errNum,
            "studNumLogin" => $studNumLogin,
        ));
    }
        
        /**
         * 快捷登录
         * @author fubaosheng 2015-08-21
         */
        public function quicklyLoginAction(Request $request){
            $user = $this->getCurrentUser();
            if ($user->isLogin()) {
                return $this->createMessageResponse('info', '你已经登录了', null, 3000, U('My/MyCourse/index'));
            }
            $errorArr = array('error'=>I('error'));
            return $this->render('Signin/quickly-login',$errorArr);
        }
        
        public function quicklyLoginCheckAction(Request $request){
            if($request->getMethod() == "GET"){
                return $this->createJsonResponse('GET method');
            }
            $mobile = I('phone');
            $password = I('password');
            #判断用户是否是当前站或权限带R
            $user = $this->getUserService()->getUserByAccount($mobile);
            if(empty($user) || empty($mobile)){
                $this->redirect(U('User/Signin/quicklyLogin',array('error'=>'此手机号没有注册过账号')));
            }
            if($user['locked'] > 0){
                $this->redirect(U('User/Signin/quicklyLogin',array('error'=>'帐号已经被封禁，无法登录')));
            }
            $mobileCodeService = $this->getMobileCodeService();
            $res = $mobileCodeService->checkMobilePwd($mobile,$password);
            if($res){
                createService("Service.UserServiceModel")->_recordLogin($user['id']);
                $this->redirect(U('My/MyCourse/index'));
            }else{
                $errorArr = array('error'=>$mobileCodeService->getErrorMsg());
                $this->redirect(U('User/Signin/quicklyLogin',$errorArr));
            }
        }
        
        
	/**
	 * 登录
         * @author fubaosheng 2015-08-20
	 */
	public function loginCheckAction(Request $request,$return = false) {
        if($request->getMethod() == "GET"){
            return $this->createJsonResponse('GET method');
        }
		$username = I('username');
		$password = I('password');
        $captchaNum = I('captcha_num');
        $identity = I('identity');
		$remember = I('remember');
		$user = D('Service\UserService');
		$remember = empty($remember) ? false : true;

		if(!$return){
                    $loginStatus = $user->login($username,$password,$remember,$captchaNum,$identity);
                    if(!$loginStatus){
                        $errorArr = array('error'=>$user->error,'errorCode'=>$user->errorCode,'identity'=>$identity);
                        $this->redirect(U('User/Signin/index',$errorArr));
                        exit;
                    }
                    $this->redirect(U('My/MyCourse/index'));
		}else{
                    if(!$user->login($username,$password,$remember,$captchaNum,$identity)){
                        $data['success'] = false;
                        if($user->errorCode == "accountExistsError")
                           $user->error = "该账号不属于本校，请用本校账号登录";
                        $data['message'] = $user->error;
                        $cacheMem = Cache::getInstance('Memcache');
                        $errKey = C("WEBSITE_CODE")."_".get_client_ip()."_".getBrowser();
                        $data['errNum'] = $cacheMem->get($errKey) ? : 0;
                        $this->ajaxReturn($data);
                    }else{
                        $data['success'] = true;
                        $this->ajaxReturn($data);
                    }
		}

	}
        
    /**
     * 新版登录
     * @author fubaosheng 2016-03-02
     */
    public function checkLoginAction(Request $request){
        if($request->getMethod() == "GET"){
            $this->ajaxReturn( array("status"=>0,"errorCode"=>"methodError","error"=>"错误的提交方式") );
        }
        $data = $request->request->all();
        $account = isset($data["account"]) ? $data["account"] : "";
        $password = isset($data["password"]) ? $data["password"] : "";
        $remember = isset($data["remember"]) ? intval($data["remember"]) : 0;
        $captchaNum = isset($data["captchaNum"]) ? $data["captchaNum"] : "";

        $arr = array("account"=>$account,"password"=>$password,"remember"=>$remember,"captchaNum"=>$captchaNum);
        $userService = createService("Service.UserServiceModel");
        $result = $userService->checkLogin($arr);
        if($result){
            $this->ajaxReturn( array("status"=>1,"errorCode"=>'success',"error"=>'登录成功',"data"=>$result) );
        }else{
            $errorArr = array("status"=>0,"errorCode"=>$userService->errorCode,"error"=>$userService->error);
            $errorArr["errorNum"] = ($userService->pwdErrNum>=3) ? 1 : 0;
            $this->ajaxReturn($errorArr);
        }
    }

	/**
	 * 授权登录
	 * ----------------
	 * 中心站管理员可以通过授权登录分站
	 */
	public function authLoginAction() {
		$data       = I('data');
		$url        = urldecode(I('url'));
		$timestamp  = I('timestamp');
		$auth_token = C('AUTH_TOKEN');
		if (md5($auth_token . $timestamp) == $data && time() - $timestamp < 10) {
                    //登录当前分站超级管理员
                    //先获取当前站的测试号id,没有测试号，自动创建;有测试号，自动登录
                    $testUid = $this->getUserService()->getTestAccountId();
                    if(empty($testUid)) $testUid = $this->getUserService()->autoRegister();
                    if(!empty($testUid)){
                        $user = D('Service\UserService');
                        $user->logout();
                        $user->autoLogin($testUid);
                    }
		}
		//跳转
		$this->redirect($url);
	}

	public function logoutAction(){
		$user = D('Service\UserService');
		$user->logout();
		$this->redirect(U('User/Signin/index'));
	}

	public function ajaxAction(Request $request) {
            //获取密码输错的次数
            $cacheMem = Cache::getInstance('Memcache');
            $cacheKey = C("WEBSITE_CODE")."_".get_client_ip()."_".getBrowser();
            $errNum = $cacheMem->get($cacheKey) ? : 0;
            
            //获取是否需要学号
            $loginBind = $this->getSettingService()->get('login_bind');
            $studNumLogin = ($loginBind["studNumLogin"] == 1) ? 1 : 0;
            
            return $this->render('Signin/ajax',array(
                "errorNum" => $errNum,
                "studNumLogin" => $studNumLogin,
            ));
	}

	public function checkEmailAction(Request $request) {
		$email = $request->query->get('value');
		$user  = $this->getUserService()->getUserByEmail($email);
		if ($user) {
			$response = array('success' => true, 'message' => '该Email地址可以登录');
		} else {
			$response = array('success' => false, 'message' => '该Email地址尚未注册');
		}
		return $this->createJsonResponse($response);
	}

	public function oauth2LoginsBlockAction($targetPath, $displayName = true) {
		$clients = OAuthClientFactory::clients();
		return $this->render('Signin/oauth2-logins-block', array(
			'clients'     => $clients,
			'targetPath'  => $targetPath,
			'displayName' => $displayName
		));
	}

	private function getTargetPath($request) {
		if ($request->query->get('goto')) {
			$targetPath = $request->query->get('goto');
		} else if (session('_target_path')) {
			$targetPath = session('_target_path');
		} else {
			$targetPath = $request->headers->get('Referer');
		}

		if ($targetPath == $this->generateUrl('login', array(), true)) {
			return $this->generateUrl('homepage');
		}

		$url = explode('?', $targetPath);

		if ($url[0] == $this->generateUrl('partner_logout', array(), true)) {
			return $this->generateUrl('homepage');
		}

		if ($url[0] == $this->generateUrl('password_reset_update', array(), true)) {
			$targetPath = $this->generateUrl('homepage', array(), true);
		}

		return $targetPath;
	}

        protected function getMobileCodeService()
        {
            return createService('Service.MobileCodeService');
        }
        
        protected function getSettingService(){
            return createService('System.SettingServiceModel');
        }
}
