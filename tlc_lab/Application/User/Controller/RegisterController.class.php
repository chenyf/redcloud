<?php
namespace User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Form\RegisterType;
use Gregwar\Captcha\CaptchaBuilder;
use Common\Lib\SmsToolkit;
use Common\Lib\MailBat;
use Common\Lib\misc\Image;

class RegisterController extends \Home\Controller\BaseController
{

    public function captchaCheckAction(Request $request)
    {
        $captchaFilledByUser = strtolower($request->query->get('value'));
        if (session('captcha_code') == $captchaFilledByUser) {
            $response = array('success' => true, 'message' => '验证码正确');
        } else {
//            session('captcha_code',mt_rand(0,999999999)); 
            $response = array('success' => false, 'message' => '验证码输入错误');
        }
        return $this->createJsonResponse($response);
    }

    protected function getUserFieldService()
    {
        return createService('User.UserFieldService');
    }

    public function captchaAction(Request $request)
    {
        $imgBuilder = new Image();
        $imgBuilder->buildSecurityCode(5,150,32,17,false,false);
        die;
//        $imgBuilder = new CaptchaBuilder;
//        $imgBuilder->build($width = 150, $height = 32, $font = null);
//        session('captcha_code',strtolower($imgBuilder->getPhrase()));
//        ob_start();
//        $imgBuilder->output();
//        $str = ob_get_clean();
//        $imgBuilder = null;
//        $headers = array(
//            'Content-type' => 'image/jpeg',
//            'Content-Disposition' => 'inline; filename="'."reg_captcha.jpg".'"'
//        );
//        $resp = new Response($str, 200, $headers);
//        $resp->send();
    }

    protected function getSettingService()
    {
        return createService('System.SettingService');
    }

    protected function getEduCloudService()
    {
        return createService('EduCloud.EduCloudService');
    }   

    protected function getMessageService()
    {
        return createService('User.MessageService');
    }

    protected function getNotificationService()
    {
        return createService('User.NotificationService');
    }

    protected function getAuthService()
    {
        return createService('User.AuthService');
    }
    
    protected function getMobileCodeService()
    {
        return createService('Service.MobileCodeService');
    }

    private function getWebExtension()
    {
        return $this->container->get('redcloud.twig.web_extension');
    }

}
