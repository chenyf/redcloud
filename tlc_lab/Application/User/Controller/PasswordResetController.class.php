<?php
namespace User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\SmsToolkit;
use Common\Lib\MailBat;

class PasswordResetController extends \Home\Controller\BaseController
{

    public function indexAction(Request $request)
    {
        $user = $this->getCurrentUser();
        if ($user->isLogin()) {
            return $this->redirect(U('My/MyCourse/index'));
        }
        $type = isset($_GET['type']) ? $_GET['type'] : 'email';
        if(!in_array($type, array('email','mobile')))
            $type = 'email';

        return $this->render("PasswordReset:index", array(
            'type'  => $type,
        ));
    }

    // 好像不用了
    public function updateAction(Request $request)
    {
        $user = $this->getCurrentUser();
        if(!$user->isLogin()) $this->redirect("User/Signin/index");
        
        $token = $this->getUserService()->getToken('password-reset', $request->query->get('token')?:$request->request->get('token'));
        if (empty($token)) {
            return $this->render('PasswordReset:error');
        }
        if ($request->getMethod() == 'POST') {

                $data = I('post.')['form'];
                
                if(empty($token['userId'])){
                    return $this->createMessageResponse('error', '用户不存在，请重新找回');
                }
                if($token['userId'] != $user->id){
                    return $this->createMessageResponse('error', '请修改自己的密码');
                }
                if($data['password'] != $data['confirmPassword'] || empty($data['password'])){
                    return $this->createMessageResponse('error', '密码不能为空或俩次输入的密码不一致');
                }
                
                $this->getAuthService()->changePassword($token['userId'], null, $data['password']);

                $this->getUserService()->deleteToken('password-reset', $token['token']);

                return $this->render('PasswordReset:success');

        }

        return $this->render('PasswordReset:update', array(
           // 'form' => $form->createView(),
        ));
    }
    
    public function updatePasswordAction(Request $password,$uid){
        
        if($uid && $password){
            $this->getAuthService()->changePassword($uid, null, $password);
            return $this->render('PasswordReset:success');
        }
        
        return $this->render('PasswordReset:index', array(
           // 'form' => $form->createView(),
        ));
    }
    
    public function resetBySmsAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            if(empty($data['mobile'])){
                return $this->createMessageResponse('error', '请输入手机号');
            }
            if(!isValidMobile($data['mobile'])){
                return $this->createMessageResponse('error', '请输入正确的手机号码');
            }
            if(empty($data["captcha_num"])){
                return $this->createMessageResponse('error', '请输入验证码');
            }
            if(session('captcha_code') != strtolower($data["captcha_num"])){
                return $this->createMessageResponse('error', '验证码输入错误');
            }
            if(empty($data['sms_code'])){
                return $this->createMessageResponse('error', '请输入短信验证码');
            }
            if(empty($data['password'])){
                return $this->createMessageResponse('error', '请输入密码');
            }
            if( preg_match("/[\x7f-\xff]/", $data["password"]) || preg_match("/\s/", $data["password"]) ){
                return $this->createMessageResponse('error', '密码由5-20位英文、数字、符号组成，区分大小写,不能有空格');
            }
            if(strlen($data['password'])<5 || strlen($data['password'])>20){
                return $this->createMessageResponse('error', '密码由5-20位英文、数字、符号组成，区分大小写,不能有空格');
            }
            $targetUser = $this->getUserService()->getUserByAccount($data['mobile']);
            if (empty($targetUser)){
                return $this->createMessageResponse('error', '该账号不存在');
            }
            $uid = $targetUser['id'];
            #检测手机验证码是否正确
            $mobileCodeService = $this->getMobileCodeService();
            $result = $mobileCodeService->checkMobileCode($data['mobile'],$data['sms_code'],'sms_forget_password');
            if ($result){
                return $this->updatePasswordAction($data['password'],$uid);
            }else{
                return $this->createMessageResponse('error', $mobileCodeService->getErrorMsg());
            }
        }
        return $this->createJsonResponse('GET method');
    }
    
    public function resetByEmailAction(Request $request){
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            if(empty($data['email'])){
                return $this->createMessageResponse('error', '请输入邮箱');
            }
            if(!isValidEmail($data['email'])){
                return $this->createMessageResponse('error', '邮箱的格式不正确');
            }
            if(empty($data["captcha_num"])){
                return $this->createMessageResponse('error', '请输入验证码');
            }
            if(session('captcha_code') != strtolower($data["captcha_num"])){
                return $this->createMessageResponse('error', '验证码输入错误');
            }
            if(empty($data['email_code'])){
                return $this->createMessageResponse('error', '请输入邮件验证码');
            }
            if(empty($data['email_password'])){
                return $this->createMessageResponse('error', '请输入密码');
            }
            if( preg_match("/[\x7f-\xff]/", $data["email_password"]) || preg_match("/\s/", $data["email_password"]) ){
                return $this->createMessageResponse('error', '密码由5-20位英文、数字、符号组成，区分大小写,不能有空格');
            }
            if(strlen($data['email_password'])<5 || strlen($data['email_password'])>20){
                return $this->createMessageResponse('error', '密码由5-20位英文、数字、符号组成，区分大小写,不能有空格');
            }
            $targetUser = $this->getUserService()->getUserByAccount($data['email']);
            if (empty($targetUser)){
                return $this->createMessageResponse('error', '该账号不存在');
            }
            $uid = $targetUser['id'];
            #检测邮箱验证码是否正确
            $emailCodeService = $this->getEmailCodeService();
            $result = $emailCodeService->checkEmailCode($data['email'],$data['email_code'],'sms_forget_password');
            if ($result){
                return $this->updatePasswordAction($data['email_password'],$uid);
            }else{
                return $this->createMessageResponse('error', $emailCodeService->getErrorMsg());
            }
        }
        
        return $this->createJsonResponse('GET method');
    }
    
     /**
     * 检查手机是否在本站注册
     * @author fubaosheng 2015-09-02
     */
    public function checkEmailAction(Request $request){
        $email = $request->query->get('value');
        $email = str_replace('!', '.', $email);
        if(!isValidEmail($email))
            return $this->createJsonResponse(array('success'=>false,'message'=>"邮箱的格式不正确"));
        $user = $this->getUserService()->getUserByEmail($email);
        if(empty($user)){
            $response = array('success' => false, 'message' => '该账号不存在');
        }else{
            $response = array('success' => true, 'message' => '');
        }
        return $this->createJsonResponse($response);
    }
    
    /**
     * 检查手机是否在本站注册
     * @author fubaosheng 2015-09-02
     */
    public function checkMobileAction(Request $request){
        $mobile = $request->query->get('value');
        if(!isValidMobile($mobile))
            return $this->createJsonResponse(array('success' => false, 'message' => '请输入正确的手机号码'));
        $user = $this->getUserService()->getUserByVerifiedMobile($mobile);
        if(empty($user)){
            $response = array('success' => false, 'message' => '该账号不存在');
        }else{
            if($user['webCode'] != C('WEBSITE_CODE')){
                $schoolName = getWebNameByWebcode($user['webCode']);
                $response = array('success' => false, 'message' => "此手机号已被{$schoolName}占用");
            }else{
                $response = array('success' => true, 'message' => '');
            }
        }
        return $this->createJsonResponse($response);
    }

    public function getEmailLoginUrl ($email)
    {
        $host = substr($email, strpos($email, '@') + 1);

        if ($host == 'hotmail.com') {
            return 'http://www.' . $host;
        }

        if ($host == 'gmail.com') {
            return 'http://mail.google.com';
        }

        return 'http://mail.' . $host;
    }

    private function getAuthService()
    {
        return createService('User.AuthServiceModel');
    }
    
    protected function getMobileCodeService()
    {
        return createService('Service.MobileCodeService');
    }
    
    protected function getEmailCodeService()
    {
        return createService('Service.EmailCodeService');
    }
}