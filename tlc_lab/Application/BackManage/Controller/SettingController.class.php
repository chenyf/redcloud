<?php

namespace BackManage\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\ArrayToolkit;
use Common\Lib\FileToolkit;
use Common\Component\OAuthClient\OAuthClientFactory;
//use Topxia\Component\OAuthClient\OAuthClientFactory;
use Common\Model\Util\LiveClientFactory;
use Common\Model\Util\CloudClientFactory;
use \Common\Lib\WebCode;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class SettingController extends BaseController
{
    public function siteAction(Request $request)
    {
        $user = $this->getCurrentUser();
        $site = $this->getSettingService()->get('site', array());
        $default = array(
            'name' => '',
            'school_name'   =>  '',
            'school_english_name'   =>  '',
            'slogan' => '',
            'url' => '',
            'logo' => '',
            'seo_keywords' => '',
            'seo_description' => '',
            'master_email' => '',
            'icp' => '',
            'contact'   => '',
            'analytics' => '',
            'status' => 'open',
            'closed_note' => '',
            'favicon' => '',
            'copyright' => '',
            'themeCfg' => C('DEFAULT_THEME_CFG'),//主题配置 qzw 2015-07-16
        );
        $site = array_merge($default, $site);
        
        if ($request->getMethod() == 'POST') {
            $site = $request->request->all();
            if(ONLY_KEPP_COMMON_THEME==1){
                if(!$site['themeCfg']['THEME_FRONT_COLOR'] || !$site['themeCfg']['THEME_BACK_COLOR']){
                    E("通用主题配置前景色或背景色不能为空");
                }
                if(!$site['themeCfg']['THEME_NAV_BACK_COLOR']){
                    E("导航条颜色值不能为空");
                }
            }
           
            $site = createService('System.AboutUs') ->childrenUnset($site,array('about_us'));
            $site = createService('System.AboutUs') ->childrenTrim($site,array('about_us_title'));
            $site['friend_link'] = createService('System.AboutUs') ->childrenTrim($site['friend_link'],array('title','url'));
            $site['friend_link'] = createService('System.AboutUs') ->rowNullDel($site['friend_link']);
            $this->getSettingService()->set('site', $site);
            $this->getLogService()->info('system', 'update_settings', "更新站点设置", $site);
            
            $about_save = createService('System.AboutUs') ->saveControl();
            
            $this->setFlashMessage('success', '站点信息设置已保存！');
        }
        
        if(goBackEnd() && !isGranted('ROLE_SUPER_ADMIN'))  return $this->forward('BackManage:Course:category');
        
        $iconColorArr = $this->getIconImgColor();
        
        $about_us_resource = createService('System.AboutUs') ->findAll();
        if(empty($about_us_resource) || count($about_us_resource) == 0 ){
            $about_us_resource[0] =array(
                'sequence' =>'',
                'title' =>'',
                'content'   =>''
            );
        }
        if( count($site['friend_link']) == 0 ){
            $site['friend_link'][0] =array(
                'title' =>'',
                'url'   =>''
            );
        }

        return $this->render('System:site', array(
            'site' => $site,
            'about_us' => $about_us_resource,
            'about_save'=>$about_save,
            'isCenter' =>false,
            'iconColorArr' =>$iconColorArr,
            'isOolyTheme' =>ONLY_KEPP_COMMON_THEME
        ));
    }
    
    private function getIconImgColor(){
        $img = array('png');      #所有图片的后缀名
        $dir = './Public/themes/redcloud-all/theme-img/';               #文件夹名称
        
        $picArr   = array();                  #图片数组
        $colorArr = array();                  #颜色数组

        foreach($img as $k=>$v){
            $pattern = $dir.'star*.'.$v;
            $all = glob($pattern);
            $picArr = array_merge($picArr,$all);
        }
        foreach ($picArr as $k=>$v){
            $key = floor($k/10);
            $position = strrpos($v,'_');
            $color = substr($v,$position+1,6);
            $colorArr[$key][]= $color;
        }
        $num = 10 - count($colorArr[$key]);
        for($i = 0; $i < $num ;$i ++){
            array_push($colorArr[$key],'');
        }
        return $colorArr;
    }

    public function mobileAction(Request $request)
    {
        $siteSelect = \Common\Lib\WebCode::getCurrentWebCode();
        if(empty($siteSelect)) $siteSelect = 'local';
        $mobile = $this->getSettingService()->get('mobile', array(),$siteSelect);
        $default = array(
            'enabled' => 0, // 网校状态
            'about' => '', // 网校简介
            'logo' => '', // 网校Logo
            'splash1' => '', // 启动图1
            'splash2' => '', // 启动图2
            'splash3' => '', // 启动图3
            'splash4' => '', // 启动图4
            'splash5' => '', // 启动图5
            'banner1' => '', // 轮播图1
            'banner2' => '', // 轮播图2
            'banner3' => '', // 轮播图3
            'banner4' => '', // 轮播图4
            'banner5' => '', // 轮播图5
            'bannerUrl1' => '', // 轮播图1的触发地址
            'bannerUrl2' => '', // 轮播图2的触发地址
            'bannerUrl3' => '', // 轮播图3的触发地址
            'bannerUrl4' => '', // 轮播图4的触发地址
            'bannerUrl5' => '', // 轮播图5的触发地址
            'bannerClick1' => '', // 轮播图1是否触发动作
            'bannerClick2' => '', // 轮播图2是否触发动作
            'bannerClick3' => '', // 轮播图3是否触发动作
            'bannerClick4' => '', // 轮播图4是否触发动作
            'bannerClick5' => '', // 轮播图5是否触发动作
            'title1' => '', // 轮播图1标题
            'title2' => '', // 轮播图2标题
            'title3' => '', // 轮播图3标题
            'title4' => '', // 轮播图4标题
            'title5' => '', // 轮播图5标题
            'banKey1' => '', // 轮播图1规则键
            'banKey2' => '', // 轮播图2规则键
            'banKey3' => '', // 轮播图3规则键
            'banKey4' => '', // 轮播图4规则键
            'banKey5' => '', // 轮播图5规则键
            'banVal1' => '', // 轮播图1规则值
            'banVal2' => '', // 轮播图2规则值
            'banVal3' => '', // 轮播图3规则值
            'banVal4' => '', // 轮播图4规则值
            'banVal5' => '', // 轮播图5规则值
            'bannerJumpToCourseId1' => ' ',
            'bannerJumpToCourseId2' => ' ',
            'bannerJumpToCourseId3' => ' ',
            'bannerJumpToCourseId4' => ' ',
            'bannerJumpToCourseId5' => ' ',
            'notice' => '', //公告
            'courseIds' => '', //每周精品课
        );
        
        
        //$mobile = array_merge($default, $mobile);  
        if ($request->getMethod() == 'POST') {
            $mobile = $request->request->all();
            
            for ($i = 1; $i <= $mobile['banNum']; $i++) {
                if(!empty($mobile['banner'.$i])){
                    //$mobile['bannerUrl'.$i] = $mobile['bannerClick'.$i] && $mobile['bannerUrl'.$i] ? $mobile['bannerUrl'.$i] : "";
                    #edit tanhaitao 2015-10-21
                    $mobile['banVal'.$i] = empty(trim($mobile['banVal'.$i])) ? 0 : trim($mobile['banVal'.$i]);
                    $mobile['bannerUrl'.$i] = $mobile['banVal'.$i] ? $mobile['banKey'.$i] .':'.$mobile['banVal'.$i] : $mobile['banKey'.$i] ;
                    if($mobile['banKey'.$i] == "webpage") $mobile['bannerUrl'.$i] = $mobile['banVal'.$i];
                }
            }
            
            $this->getSettingService()->set('mobile', $mobile, $siteSelect);
            $this->getLogService()->info('system', 'update_settings', "更新移动客户端设置", $mobile);
            $this->setFlashMessage('success', '移动客户端设置已保存！');
        }
        $courseIds = explode(",", rtrim($mobile['courseIds'],','));
        $courses = $this->getCourseService()->findCoursesByIds($courseIds);
        $courses = ArrayToolkit::index($courses, 'id');
        $sortedCourses = array();
        foreach ($courseIds as $value) {
            if (!empty($value)) {
                $sortedCourses[] = $courses[$value];
            }
        }
        $bannerCourse1 = ($mobile['bannerJumpToCourseId1'] != " ") ? $this->getCourseService()->getCourse($mobile['bannerJumpToCourseId1']) : null;
        $bannerCourse2 = ($mobile['bannerJumpToCourseId2'] != " ") ? $this->getCourseService()->getCourse($mobile['bannerJumpToCourseId2']) : null;
        $bannerCourse3 = ($mobile['bannerJumpToCourseId3'] != " ") ? $this->getCourseService()->getCourse($mobile['bannerJumpToCourseId3']) : null;
        $bannerCourse4 = ($mobile['bannerJumpToCourseId4'] != " ") ? $this->getCourseService()->getCourse($mobile['bannerJumpToCourseId4']) : null;
        $bannerCourse5 = ($mobile['bannerJumpToCourseId5'] != " ") ? $this->getCourseService()->getCourse($mobile['bannerJumpToCourseId5']) : null;

      
        
        $bannerRule = C('APP_BANNER_RULE');
        return $this->render('System:mobile', array(
            'mobile'        => $mobile,
            'courses'       => $sortedCourses,
            "bannerCourse1" => $bannerCourse1,
            "bannerCourse2" => $bannerCourse2,
            "bannerCourse3" => $bannerCourse3,
            "bannerCourse4" => $bannerCourse4,
            "bannerCourse5" => $bannerCourse5,
            "bannerRule"    => $bannerRule,
        ));
    }

    public function mobilePictureUploadAction(Request $request, $type)
    {
        $file = $request->files->get($type);
        if (!FileToolkit::isImageFile($file)) {
//            throw $this->createAccessDeniedException('图片格式不正确！');
        }
        $filename = 'mobile_picture' . time() . '.' . $file->getClientOriginalExtension();
        $directory = getParameter('redcloud.upload.public_directory')."/xitong";
        $file = $file->move($directory, $filename);
        $mobile = $this->getSettingService()->get('mobile', array());
        $mobile[$type] = getParameter('redcloud.upload.public_url_path')."/xitong/{$filename}";
        $mobile[$type] = ltrim($mobile[$type], '/');

        $this->getSettingService()->set('mobile', $mobile);

        $this->getLogService()->info('system', 'update_settings', "更新网校{$type}图片", array($type => $mobile[$type]));
        

        $response = array(
            'path' => $mobile[$type],
            'url' => '/'.$mobile[$type]
        );
        $rsp = new Response(json_encode($response));
        $rsp->send();exit;
//        return new Response(json_encode($response));
    }
    
    public function mobileAddBannerAction(Request $request)
    {
        $r = $request->request->all();
        $num = intval($r["banNum"]);
        $bannerRule = C('APP_BANNER_RULE');
        
        $html = $this->render('System:mobile-tr', array(
            "bannerRule"    =>  $bannerRule,
            "i"           =>  $num
        ));
        echo $html;
        exit;
    }

    public function mobilePictureRemoveAction(Request $request, $type)
    {
        $setting = $this->getSettingService()->get("mobile");
        $setting[$type] = '';

        $this->getSettingService()->set('mobile', $setting);

        $this->getLogService()->info('system', 'update_settings', "移除网校{$type}图片");

        return $this->createJsonResponse(true);
    }

    public function logoUploadAction(Request $request)
    {
        $file = $request->files->get('logo');
        if (!FileToolkit::isImageFile($file)) {
            throw $this->createAccessDeniedException('图片格式不正确！');
        }

        $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();

        $directory = getParameter('redcloud.upload.public_directory')."/xitong";
        $file = $file->move($directory, $filename);

        $site = $this->getSettingService()->get('site', array());

        $site['logo'] = getParameter('redcloud.upload.public_url_path')."/xitong/{$filename}";
        $site['logo'] = ltrim($site['logo'], '/');
        $this->getSettingService()->set('site', $site);

        $this->getLogService()->info('system', 'update_settings', "更新站点LOGO", array('logo' => $site['logo']));

        $response = array(
            'path' => ltrim($site['logo'],'/Public'),
            'url' =>'/'.$site['logo'],
        );
//        return new Response (json_encode($response));
        $this->createJsonResponse($response);

    }

    public function logoRemoveAction(Request $request)
    {
        $setting = $this->getSettingService()->get("site");
        $setting['logo'] = '';

        $this->getSettingService()->set('site', $setting);

        $this->getLogService()->info('system', 'update_settings', "移除站点LOGO");

        return $this->createJsonResponse(true);
    }

    public function liveLogoUploadAction(Request $request)
    {
        $file = $request->files->get('logo');
        if (!FileToolkit::isImageFile($file)) {
            throw $this->createAccessDeniedException('图片格式不正确！');
        }

        $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();

        $directory = getParameter('redcloud.upload.public_directory')."/xitong";
        $file = $file->move($directory, $filename);

        $courseSetting = $this->getSettingService()->get('course', array());

        $courseSetting['live_logo'] = getParameter('redcloud.upload.public_url_path')."/xitong/{$filename}";
        $courseSetting['live_logo'] = ltrim($courseSetting['live_logo'], '/');

        $this->getSettingService()->set('course', $courseSetting);

        $this->getLogService()->info('system', 'update_settings', "更新站点LOGO", array('live_logo' => $courseSetting['live_logo']));

        $response = array(
            'path' => ltrim($courseSetting['live_logo'],'/Public'),
            'url' => '/'.$courseSetting['live_logo'],
        );

//        return new Response(json_encode($response));
        $this->createJsonResponse($response);
    }

    public function liveLogoRemoveAction(Request $request)
    {
        $setting = $this->getSettingService()->get("course");
        $setting['live_logo'] = '';

        $this->getSettingService()->set('course', $setting);

        $this->getLogService()->info('system', 'update_settings', "移除直播LOGO");

        return $this->createJsonResponse(true);
    }

    public function faviconUploadAction(Request $request)
    {
        $file = $request->files->get('favicon');
        if (!FileToolkit::isImageFile($file)) {
            throw $this->createAccessDeniedException('图标格式不正确！');
        }
        $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();

        $directory = getParameter('redcloud.upload.public_directory')."/xitong";
        $file = $file->move($directory, $filename);

        $site = $this->getSettingService()->get('site', array());

        $site['favicon'] = getParameter('redcloud.upload.public_url_path')."/xitong/{$filename}";
        $site['favicon'] = ltrim($site['favicon'], '/');
        $this->getSettingService()->set('site', $site);
        $this->getLogService()->info('system', 'update_settings', "更新浏览器图标", array('favicon' => $site['favicon']));
        $response = array(
            'path' => ltrim($site['favicon'],'/Public'),
            'url' =>'/'.$site['favicon'],
        );
        echo  json_encode($response);

//        return new Response(json_encode($response));
    }

    public function faviconRemoveAction(Request $request)
    {
        $setting = $this->getSettingService()->get("site");
        $setting['favicon'] = '';

        $this->getSettingService()->set('site', $setting);

        $this->getLogService()->info('system', 'update_settings', "移除站点浏览器图标");

        return $this->createJsonResponse(true);
    }

    public function authAction(Request $request)
    {
        $auth = $this->getSettingService()->get('auth', array());

        $default = array(
            'register_mode' => 'closed',
            'email_enabled' => 'closed',
            'setting_time' => -1,
            'email_activation_title' => '',
            'email_activation_body' => '',
            'welcome_enabled' => 'closed',
            'welcome_sender' => '',
            'welcome_methods' => array(),
            'welcome_title' => '',
            'welcome_body' => '',
            'user_terms' => 'closed',
            'user_terms_body' => '',
            'registerFieldNameArray' => array(),
            'registerSort' => array(0 => "email", 1 => "nickname", 2 => "password"),
            'captcha_enabled' => 0,
            'register_protective' => 'none',
            'registerSuccessBackgroundPicIndex'=>0,
            'registerSuccessBackgroundPic'=>'',
            'registerPosterBackgroundPicIndex'=>0,
            'registerPosterBackgroundPic'=>'',
            'apply_teacher_enabled' => 0,
        );
        if (isset($auth['captcha_enabled']) && $auth['captcha_enabled']) {

            if (!isset($auth['register_protective'])) {

                $auth['register_protective'] = "low";
            }

        }
        $auth = array_merge($default, $auth);
        if ($request->getMethod() == 'POST') {

            if (isset($auth['setting_time']) && $auth['setting_time'] > 0) {
                $firstSettingTime = $auth['setting_time'];
                $auth = $request->request->all();
                $auth['setting_time'] = $firstSettingTime;
            } else {
                $auth = $request->request->all();
                $auth['setting_time'] = time();
            }
            if (empty($auth['welcome_methods'])) {
                $auth['welcome_methods'] = array();
            }
            if ($auth['register_protective'] == "none") {

                $auth['captcha_enabled'] = 0;

            } else {

                $auth['captcha_enabled'] = 1;
            }
            
            //edit fubaosheng 2015-08-11 检查发送方是否为该学校账号
            if(isset($auth['welcome_sender']) && !empty($auth['welcome_sender'])){
                $errorSender = createService("User.UserServiceModel")->getUserByAccount( $auth['welcome_sender'] );
                if(empty($errorSender))
                    return $this->createMessageResponse('error', '欢迎信息发送方必须为这个网站的账号。');
            }
            
            $this->getSettingService()->set('auth', $auth);
            $this->getLogService()->info('system', 'update_settings', "更新注册设置", $auth);
            $this->setFlashMessage('success', '注册设置已保存！');
        }
        $userFields = $this->getUserFieldService()->getAllFieldsOrderBySeqAndEnabled();
        if ($auth['registerFieldNameArray']) {
            foreach ($userFields as $key => $fieldValue) {
                if (!in_array($fieldValue['fieldName'], $auth['registerFieldNameArray'])) {
                    $auth['registerFieldNameArray'][] = $fieldValue['fieldName'];
                }
            }

        }

        return $this->render('System:auth', array(
            'auth' => $auth,
            'userFields' => $userFields,
        ));
    }
    
    private function setCloudSmsKey($key, $val)
    {
        $setting = $this->getSettingService()->get('cloud_sms', array());
        $setting[$key] = $val;
        $this->getSettingService()->set('cloud_sms', $setting);
    }

    public function mailerAction(Request $request)
    {
        $mailer = $this->getSettingService()->get('mailer', array());
        $default = array(
            'enabled' => 0,
            'host' => '',
            'port' => '',
            'username' => '',
            'password' => '',
            'from' => '',
            'name' => '',
        );
        $mailer = array_merge($default, $mailer);
        if ($request->getMethod() == 'POST') {
            $mailer = $request->request->all();
            $this->getSettingService()->set('mailer', $mailer);
            $this->getLogService()->info('system', 'update_settings', "更新邮件服务器设置", $mailer);
            $this->setFlashMessage('success', '电子邮件设置已保存！');
        }

        return $this->render('System:mailer', array(
            'mailer' => $mailer,
        ));
    }

    public function loginConnectAction(Request $request)
    {
        $loginConnect = $this->getSettingService()->get('login_bind', array());

        $default = array(
            'login_limit' => 0,
            'enabled' => 0,
            'verify_code' => '',
            'captcha_enabled' => 0,
            'temporary_lock_enabled' => 0,
            'temporary_lock_allowed_times' => 5,
            'temporary_lock_minutes' => 20,
            'studNumLogin' => 0,
            'backgroundImgIndex'=>0,
            'backgroundImg' => '',
        );
        
        #edit fubaosheng 2015-08-12 第三方登录
//        $clients = OAuthClientFactory::clients();
        $clients = array();
        foreach ($clients as $type => $client) {
            $default["{$type}_enabled"] = 0;
            $default["{$type}_key"] = '';
            $default["{$type}_secret"] = '';
            $default["{$type}_set_fill_account"] = 0;
        }

        $loginConnect = array_merge($default, $loginConnect);
        if ($request->getMethod() == 'POST') {
            $loginConnect = $request->request->all();
            $this->getSettingService()->set('login_bind', $loginConnect);
            $this->getLogService()->info('system', 'update_settings', "更新登录设置", $loginConnect);
            $this->setFlashMessage('success', '登录设置已保存！');
        }

        return $this->render('System:login-connect', array(
            'loginConnect' => $loginConnect,
            'clients' => $clients,
        ));
    }
    
    /**
     * 登录/注册成功背景图
     * @author fubaosheng 2016-03-04
     */
    public function pictureAutoAction(Request $request){
        $tab = $request->query->get('tab') ? : 0;
        $type = $request->query->get('type') ? : 'login';
        
        if($type == "login"){
            $loginConnect = $this->getSettingService()->get('login_bind', array());
            $index = $loginConnect["backgroundImgIndex"] ? : 0;
        }else if($type == "registerSuccess"){
            $registerConnect = $this->getSettingService()->get('auth', array());
            $index = $registerConnect["registerSuccessBackgroundPicIndex"] ? : 0;
        }else if($type == "registerPoster"){
            $registerConnect = $this->getSettingService()->get('auth', array());
            $index = $registerConnect["registerPosterBackgroundPicIndex"] ? : 0;
        }else{
            $index = 0;
        }
        if($request->getMethod() == 'POST'){
            $file = $request->files->get('picture');
            $type = $request->query->get('type') ? : 'login';
            
            if(!in_array($type, array("login","registerSuccess","registerPoster"))){
                echo json_encode(array('status'=>'error','msg'=>'类型错误。'));
                exit;
            }
            
            if (!FileToolkit::isImageFile($file)) {
                echo json_encode(array('status'=>'error','msg'=>'上传图片格式错误，请上传jpg, gif, png格式的文件。'));
                exit;
            }
            
            $fileSize = FileToolkit::getFileSize($file)/1024/1024;
            if($fileSize>2){
                echo json_encode(array('status'=>'error','msg'=>'上传图片超过2M，请上传小于2M的文件。'));
                exit;
            }
            
            //文件名组成
            if($type == "login")
                $filenamePrefix = "login_background_pic";
            if($type == "registerSuccess")
                $filenamePrefix = "register_success_background_pic";
            if($type == "registerPoster")
                $filenamePrefix = "register_poster_background_pic";
            $hash = substr(md5($filenamePrefix.time()), -8);
            $ext = $file->getClientOriginalExtension();
            $filename = $filenamePrefix . $hash . '.' . $ext;

            $directory = getParameter('redcloud.upload.public_directory') . '/tmp';
            $file = $file->move($directory, $filename);

            //根路径
            $pictureFilePath = $directory ."/". $filename;
            try {
                $imagine = new Imagine();
                $image = $imagine->open($pictureFilePath);
            } catch (\Exception $e) {
                @unlink($pictureFilePath);
                echo json_encode(array('status'=>'error','msg'=>'该文件为非图片格式文件，请重新上传。'));
                exit;
            }
            
            //获取宽高
            $widen = 797;
            $heighten = 330;
            if($type == "registerPoster"){
                $widen = 256;
                $heighten = 405;
            }
            $naturalSize = $image->getSize();
            $scaledSize = $naturalSize->widen($widen)->heighten($heighten);
            $natural = explode(' ',$naturalSize.height);
            $natural = explode('x', $natural[0]);
            $scale = explode(' ',$scaledSize.height);
            $scale = explode('x', $scale[0]);
            
            //相对路径
            $picturePic = $pictureUrl = getParameter('redcloud.upload.public_url_path') . '/tmp/' . $filename;

            $frdata = array('pictureUrl'=>$pictureUrl,'picturePic'=>$picturePic,'nwidth'=>$natural[0],'nheight'=>$natural['1'],'swidth'=>$scale[0],'sheight'=>$scale['1']);
            echo json_encode($frdata);
            exit;
        }
        
        return $this->render('System:pictureAuto', array(
            'tab' => $tab,
            'index' => $index,
            'type' => $type
        ));
        
    }
    
    /**
     * 登录背景图裁剪
     * @author fubaosheng  2016-03-04
     */
    public function pictureCropAutoAction(Request $request) {
        $c = $request->request->all();
        $data = array();
        $data['x'] = ($c['x']<0) ? 0 : $c['x'];
        $data['y'] = ($c['y']<0) ? 0 : $c['y'];
        $data['width'] = $c['width'];
        $data['height'] = $c['height'];
        $data['type'] = $c['type'];
        $pictureFilePath = $_SERVER['DOCUMENT_ROOT']. $c['pictureFilePath'];
        
        $pic = $this->changePic($pictureFilePath, $data);
        return $this->createJsonResponse(array('status' => 'ok','data'=>$pic));
    }

    /**
     * 截取图片
     * @author fubaosheng 2016-03-04
     */
    protected function changePic($filePath, array $options){
        $pathinfo = pathinfo($filePath);
        $imagine = new Imagine();
        $rawImage = $imagine->open($filePath);
        
        $newImage = $rawImage->copy();
        $newImage->crop(new Point($options['x'], $options['y']), new Box($options['width'], $options['height']));
         //获取宽高
        $widen = 480;
        $heighten = 270;
        if($options["type"] == "registerPoster"){
            $widen = 276;
            $heighten = 520;
        }
        $newImage->resize(new Box($widen, $heighten));
        $newFilename = $pathinfo['filename'] . "_new." . $pathinfo['extension'];
        $newFilePath = $pathinfo['dirname'] . "/" . $newFilename;
        $newImage->save($newFilePath, array('quality' => 90));
        
        $newFileRecord = $this->getFileService()->uploadFile('user', new File($newFilePath));
        $picture = str_replace("public://", "/Public/dataFolder/", $newFileRecord["uri"]);
        @unlink($filePath);
        
        return $picture;
    }


    public function paymentAction(Request $request)
    {
        $payment = $this->getSettingService()->get('payment', array());
        $default = array(
            'enabled' => 0,
            'disabled_message' => '尚未开启支付模块，无法购买课程。',
            'bank_gateway' => 'none',
            'alipay_enabled' => 0,
            'alipay_key' => '',
            'alipay_secret' => '',
            'alipay_account' => '',
            'alipay_type' => 'direct',
            'tenpay_enabled' => 0,
            'tenpay_key' => '',
            'tenpay_secret' => '',
        );

        $payment = array_merge($default, $payment);
        if ($request->getMethod() == 'POST') {
            $payment = $request->request->all();
            $payment['alipay_key'] = trim($payment['alipay_key']);
            $payment['alipay_secret'] = trim($payment['alipay_secret']);

            $this->getSettingService()->set('payment', $payment);
            $this->getLogService()->info('system', 'update_settings', "更支付方式设置", $payment);
            $this->setFlashMessage('success', '支付方式设置已保存！');
        }

        return $this->render('System:payment', array(
            'payment' => $payment,
        ));
    }

    public function refundAction(Request $request)
    {
        $refundSetting = $this->getSettingService()->get('refund', array());
        $default = array(
            'maxRefundDays' => 0,
            'applyNotification' => '',
            'successNotification' => '',
            'failedNotification' => '',
        );

        $refundSetting = array_merge($default, $refundSetting);

        if ($request->getMethod() == 'POST') {
            $refundSetting = $request->request->all();
            $this->getSettingService()->set('refund', $refundSetting);
            $this->getLogService()->info('system', 'update_settings', "更新退款设置", $refundSetting);
            $this->setFlashMessage('success', '退款设置已保存！');
        }

        return $this->render('System:refund', array(
            'refundSetting' => $refundSetting,
        ));
    }

    public function defaultAction(Request $request)
    {
        $defaultSetting = $this->getSettingService()->get('default', array());
        $path = getParameter('kernel.root_dir') . '/../web/assets/img/default/';

        $default = $this->getDefaultSet();

        $defaultSetting = array_merge($default, $defaultSetting);

        if ($request->getMethod() == 'POST') {
            $defaultSetting = $request->request->all();

            if (isset($defaultSetting['user_name'])) {
                $defaultSetting['user_name'] = $defaultSetting['user_name'];
            } else {
                $defaultSetting['user_name'] = '学员';
            }

            if (isset($defaultSetting['chapter_name'])) {
                $defaultSetting['chapter_name'] = $defaultSetting['chapter_name'];
            } else {
                $defaultSetting['chapter_name'] = '章';
            }

            if (isset($defaultSetting['part_name'])) {
                $defaultSetting['part_name'] = $defaultSetting['part_name'];
            } else {
                $defaultSetting['part_name'] = '节';
            }

            $default = $this->getSettingService()->get('default', array());
            $defaultSetting = array_merge($default, $defaultSetting);

            $this->getSettingService()->set('default', $defaultSetting);
            $this->getLogService()->info('system', 'update_settings', "更新系统默认设置", $defaultSetting);
            $this->setFlashMessage('success', '系统默认设置已保存！');
        }

        return $this->render('System:default', array(
            'defaultSetting' => $defaultSetting,
            'hasOwnCopyright' => false,
        ));
    }

    public function shareAction(Request $request)
    {
        $defaultSetting = $this->getSettingService()->get('default', array());

        $default = $this->getDefaultSet();

        $defaultSetting = array_merge($default, $defaultSetting);

        if ($request->getMethod() == 'POST') {
            $defaultSetting = $request->request->all();
            $default = $this->getSettingService()->get('default', array());
            $defaultSetting = array_merge($default, $defaultSetting);

            $this->getSettingService()->set('default', $defaultSetting);
            $this->getLogService()->info('system', 'update_settings', "更新分享设置", $defaultSetting);
            $this->setFlashMessage('success', '分享设置已保存！');
        }

        return $this->render('System:share', array(
            'defaultSetting' => $defaultSetting,
        ));
    }

    private function getDefaultSet()
    {
        $default = array(
            'defaultAvatar' => 0,
            'defaultCoursePicture' => 0,
            'defaultAvatarFileName' => 'avatar',
            'defaultCoursePictureFileName' => 'coursePicture',
            'articleShareContent' => '我正在看{{articletitle}}，关注{{sitename}}，分享知识，成就未来。',
            'courseShareContent' => '我正在学习{{course}}，收获巨大哦，一起来学习吧！',
            'groupShareContent' => '我在{{groupname}}班级,发表了{{threadname}},很不错哦,一起来看看吧!',
            'classroomShareContent' => '我正在学习{{classroom}}，收获巨大哦，一起来学习吧！',
            'user_name' => '学员',
            'chapter_name' => '章',
            'part_name' => '节',
        );

        return $default;
    }

    public function ipBlacklistAction(Request $request)
    {
        $ips = $this->getSettingService()->get('blacklist_ip', array());

        if (!empty($ips)) {
            $default['ips'] = join("\n", $ips['ips']);
            $ips = array_merge($ips, $default);
        }

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $ips['ips'] = array_filter(explode(' ', str_replace(array("\r\n", "\n", "\r"), " ", $data['ips'])));
            $this->getSettingService()->set('blacklist_ip', $ips);
            $this->getLogService()->info('system', 'update_settings', "更新IP黑名单", $ips);

            $ips['ips'] = join("\n", $ips['ips']);
            $this->setFlashMessage('success', '保存成功！');
        }

        return $this->render('System:ip-blacklist', array(
            'ips' => $ips,
        ));
    }

    public function customerServiceAction(Request $request)
    {
        $customerServiceSetting = $this->getSettingService()->get('customerService', array());

        $default = array(
            'customer_service_mode' => 'closed',
            'customer_of_qq' => '',
            'customer_of_mail' => '',
            'customer_of_phone' => '',
        );

        $customerServiceSetting = array_merge($default, $customerServiceSetting);

        if ($request->getMethod() == 'POST') {
            $customerServiceSetting = $request->request->all();
            $this->getSettingService()->set('customerService', $customerServiceSetting);
            $this->getLogService()->info('system', 'customerServiceSetting', "客服管理设置", $customerServiceSetting);
            $this->setFlashMessage('success', '客服管理设置已保存！');
        }

        return $this->render('System:customer-service', array(
            'customerServiceSetting' => $customerServiceSetting,
        ));
    }

    public function userCenterAction(Request $request)
    {
        $setting = $this->getSettingService()->get('user_partner', array());

        $default = array(
            'mode' => 'default',
            'nickname_enabled' => 0,
            'avatar_alert' => 'none',
            'email_filter' => '',
        );

        $setting = array_merge($default, $setting);

        $configDirectory = getParameter('kernel.root_dir') . '/config/';
        $discuzConfigPath = $configDirectory . 'uc_client_config.php';
        $phpwindConfigPath = $configDirectory . 'windid_client_config.php';

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $data['mode'] = 'default';
            $data['email_filter'] = trim(str_replace(array("\n\r", "\r\n", "\r"), "\n", $data['email_filter']))?:'';
            $setting = array('mode' => $data['mode'],
                'nickname_enabled' => $data['nickname_enabled'],
                'avatar_alert' => $data['avatar_alert'],
                'email_filter' => $data['email_filter'],
            );
            $this->getSettingService()->set('user_partner', $setting);

            $discuzConfig = $data['discuz_config'];
            $phpwindConfig = $data['phpwind_config'];

            if ($setting['mode'] == 'discuz') {
                if (!file_exists($discuzConfigPath) or !is_writeable($discuzConfigPath)) {
                    $this->setFlashMessage('danger', "配置文件{$discuzConfigPath}不可写，请打开此文件，复制Ucenter配置的内容，覆盖原文件的配置。");
                    goto response;
                }
                file_put_contents($discuzConfigPath, $discuzConfig);
            } elseif ($setting['mode'] == 'phpwind') {
                if (!file_exists($phpwindConfigPath) or !is_writeable($phpwindConfigPath)) {
                    $this->setFlashMessage('danger', "配置文件{$phpwindConfigPath}不可写，请打开此文件，复制WindID配置的内容，覆盖原文件的配置。");
                    goto response;
                }
                file_put_contents($phpwindConfigPath, $phpwindConfig);
            }

            $this->getLogService()->info('system', 'setting', "用户中心设置", $setting);
            $this->setFlashMessage('success', '用户中心设置已保存！');
        }

        if (file_exists($discuzConfigPath)) {
            $discuzConfig = file_get_contents($discuzConfigPath);
        } else {
            $discuzConfig = '';
        }

        if (file_exists($phpwindConfigPath)) {
            $phpwindConfig = file_get_contents($phpwindConfigPath);
        } else {
            $phpwindConfig = '';
        }

        response:
        return $this->render('System:user-center', array(
            'setting' => $setting,
            'discuzConfig' => $discuzConfig,
            'phpwindConfig' => $phpwindConfig,
        ));
    }

    public function courseSettingAction(Request $request)
    {

        $courseSetting = $this->getSettingService()->get('course', array());

//        $client = LiveClientFactory::createClient();

//        $capacity = $client->getCapacity();

        $default = array(
            'welcome_message_enabled' => '0',
            'welcome_message_body' => '{{nickname}},欢迎加入课程{{course}}',
            'buy_fill_userinfo' => '0',
            'teacher_modify_price' => '1',
            'teacher_manage_student' => '0',
            'teacher_export_student' => '0',
            'student_download_media' => '0',
            'free_course_nologin_view' => '1',
            'relatedCourses' => '0',
            'coursesPrice' => '0',
            'allowAnonymousPreview' => '1',
            'live_course_enabled' => '0',
            'userinfoFields' => array(),
            "userinfoFieldNameArray" => array(),
            "copy_enabled" => '0',
            "picturePreview_enabled" => '0',
            "chapter_deploy_enabled" => "0",
        );

        $this->getSettingService()->set('course', $courseSetting);
        $courseSetting = array_merge($default, $courseSetting);
        if ($request->getMethod() == 'POST') {
            $courseSetting = $request->request->all();
            if (!isset($courseSetting['userinfoFields'])) {
                $courseSetting['userinfoFields'] = array();
            }

            if (!isset($courseSetting['userinfoFieldNameArray'])) {
                $courseSetting['userinfoFieldNameArray'] = array();
            }

            $courseSetting['live_student_capacity'] = empty($capacity['capacity']) ? 0 : $capacity['capacity'];
            $courseSetting['select_course_cate_sort'] = implode(',',$courseSetting['select_course_cate_sort']);
            $this->getSettingService()->set('course', $courseSetting);
            $this->getLogService()->info('system', 'update_settings', "更新课程设置", $courseSetting);
            $this->setFlashMessage('success', '课程设置已保存！');
        }

        $courseSetting['live_student_capacity'] = empty($capacity['capacity']) ? 0 : $capacity['capacity'];

        $userFields = $this->getUserFieldService()->getAllFieldsOrderBySeqAndEnabled();

        if ($courseSetting['userinfoFieldNameArray']) {
            foreach ($userFields as $key => $fieldValue) {
                if (!in_array($fieldValue['fieldName'], $courseSetting['userinfoFieldNameArray'])) {
                    $courseSetting['userinfoFieldNameArray'][] = $fieldValue['fieldName'];
                }
            }

        }
        
        if(!empty($courseSetting['select_course_cate_sort'])){
            $courseSettingArr  = explode(',',$courseSetting['select_course_cate_sort']);
            foreach($courseSettingArr as $k => $v){
                if($v == "recommendedSeq") $res  = '推荐';
                if($v == "latest")         $res  = '最新';
                if($v == "popular")        $res  = '热门';
                $data[$k]['sortkey'] = $v; 
                $data[$k]['sortname'] = $res; 
            }
        }
        
        
        return $this->render('System:course-setting', array(
            'courseSetting' => $courseSetting,
            'userFields' => $userFields,
            'select_course_cate_sort' => $data,
        ));
    }

    public function questionsSettingAction(Request $request)
    {
        $questionsSetting = $this->getSettingService()->get('questions', array());
        if (empty($questionsSetting)) {
            $default = array(
                'testpaper_answers_show_mode' => 'submitted',
            );
            $questionsSetting = $default;
        }

        if ($request->getMethod() == 'POST') {
            $questionsSetting = $request->request->all();
            $this->getSettingService()->set('questions', $questionsSetting);
            $this->getLogService()->info('system', 'questions_settings', "更新题库设置", $questionsSetting);
            $this->setFlashMessage('success', '题库设置已保存！');
        }

        return $this->render('System:questions-setting');
    }

    public function adminSyncAction(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $setting = $this->getSettingService()->get('user_partner', array());
        if (empty($setting['mode']) or !in_array($setting['mode'], array('phpwind', 'discuz'))) {
            return $this->createMessageResponse('info', '未开启用户中心，不能同步管理员帐号！');
        }
        $bind = $this->getUserService()->getUserBindByTypeAndUserId($setting['mode'], $currentUser['id']);
        if ($bind) {
            goto response;
        } else {
            $bind = null;
        }
            
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $partnerUser = $this->getAuthService()->checkPartnerLoginByNickname($data['nickname'], $data['password']);
            if (empty($partnerUser)) {
                $this->setFlashMessage('danger', '用户名或密码不正确。');
                goto response;
            } else {
                $this->getUserService()->changeEmail($currentUser['id'], $partnerUser['email']);
                $this->getUserService()->changeNickname($currentUser['id'], $partnerUser['nickname']);
                $this->getUserService()->changePassword($currentUser['id'], $data['password']);
                $this->getUserService()->bindUser($setting['mode'], $partnerUser['id'], $currentUser['id'], null);
                $user = $this->getUserService()->getUser($currentUser['id']);
                $this->authenticateUser($user);

                $this->setFlashMessage('success', '管理员帐号同步成功。');

                return $this->redirect($this->generateUrl('admin_setting_user_center'));
            }
        }

        response:
        return $this->render('System:admin-sync', array(
            'mode' => $setting['mode'],
            'bind' => $bind,
        ));
    }

    public function developerAction(Request $request)
    {
        $developerSetting = $this->getSettingService()->get('developer', array());
        $storageSetting = $this->getSettingService()->get('storage', array());

        $default = array(
            'debug' => '0',
            'app_api_url' => '',
            'cloud_api_server' => empty($storageSetting['cloud_api_server']) ? '' : $storageSetting['cloud_api_server'],
            'hls_encrypted' => '1',
        );

        $developerSetting = array_merge($default, $developerSetting);

        if ($request->getMethod() == 'POST') {
            $developerSetting = $request->request->all();
            $storageSetting['cloud_api_server'] = $developerSetting['cloud_api_server'];
            $this->getSettingService()->set('storage', $storageSetting);
            $this->getSettingService()->set('developer', $developerSetting);

            $this->getLogService()->info('system', 'update_settings', "更新开发者设置", $developerSetting);
            $this->setFlashMessage('success', '开发者已保存！');
        }

        return $this->render('System:developer-setting', array(
            'developerSetting' => $developerSetting,
        ));
    }

    public function modifyVersionAction(Request $request)
    {
        $fromVersion = $request->query->get('fromVersion');
        $version = $request->query->get('version');
        $code = $request->query->get('code');

        if (empty($fromVersion) || empty($version) || empty($code)) {
            exit('注意参数为:<br><br>code<br>fromVersion<br>version<br><br>全填，不能为空！');
        }

        $appCount = $this->getAppservice()->findAppCount();
        $apps = $this->getAppservice()->findApps(0, $appCount);
        $appsCodes = ArrayToolkit::column($apps, 'code');

        if (!in_array($code, $appsCodes)) {
            exit('code 填写有问题！请检查!');
        }

        $fromVersionArray['fromVersion'] = $fromVersion;
        $versionArray['version'] = $version;
        $this->getAppservice()->updateAppVersion($code, $fromVersionArray, $versionArray);

        return $this->redirect($this->generateUrl('admin_app_upgrades'));
    }

    public function userFieldsAction()
    {
        
        #edit fubaosheng 2015-08-12 字段自定义关闭
        return $this->createMessageResponse('error', '字段自定义功能已关闭。');
        die;
        
        $textCount = $this->getUserFieldService()->searchFieldCount(array('fieldName' => 'textField'));
        $intCount = $this->getUserFieldService()->searchFieldCount(array('fieldName' => 'intField'));
        $floatCount = $this->getUserFieldService()->searchFieldCount(array('fieldName' => 'floatField'));
        $dateCount = $this->getUserFieldService()->searchFieldCount(array('fieldName' => 'dateField'));
        $varcharCount = $this->getUserFieldService()->searchFieldCount(array('fieldName' => 'varcharField'));

        $fields = $this->getUserFieldService()->getAllFieldsOrderBySeq();
        for ($i = 0; $i < count($fields); $i++) {
            if (strstr($fields[$i]['fieldName'], "textField")) {
                $fields[$i]['fieldName'] = "多行文本";
            }

            if (strstr($fields[$i]['fieldName'], "varcharField")) {
                $fields[$i]['fieldName'] = "文本";
            }

            if (strstr($fields[$i]['fieldName'], "intField")) {
                $fields[$i]['fieldName'] = "整数";
            }

            if (strstr($fields[$i]['fieldName'], "floatField")) {
                $fields[$i]['fieldName'] = "小数";
            }

            if (strstr($fields[$i]['fieldName'], "dateField")) {
                $fields[$i]['fieldName'] = "日期";
            }

        }

        return $this->render('System:user-fields', array(
            'textCount' => $textCount,
            'intCount' => $intCount,
            'floatCount' => $floatCount,
            'dateCount' => $dateCount,
            'varcharCount' => $varcharCount,
            'fields' => $fields,
        ));
    }

    public function editUserFieldsAction(Request $request, $id)
    {
        $field = $this->getUserFieldService()->getField($id);

        if (empty($field)) {
//            throw $this->createNotFoundException();
        }

        if (strstr($field['fieldName'], "textField")) {
            $field['fieldName'] = "多行文本";
        }

        if (strstr($field['fieldName'], "varcharField")) {
            $field['fieldName'] = "文本";
        }

        if (strstr($field['fieldName'], "intField")) {
            $field['fieldName'] = "整数";
        }

        if (strstr($field['fieldName'], "floatField")) {
            $field['fieldName'] = "小数";
        }

        if (strstr($field['fieldName'], "dateField")) {
            $field['fieldName'] = "日期";
        }

        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();

            if (isset($fields['enabled'])) {
                $fields['enabled'] = 1;
            } else {
                $fields['enabled'] = 0;
            }

            $field = $this->getUserFieldService()->updateField($id, $fields);

            return $this->redirect($this->generateUrl('admin_setting_user_fields'));
        }

        return $this->render('System:user-fields.modal.edit', array(
            'field' => $field,
        ));
    }

    public function deleteUserFieldsAction(Request $request, $id)
    {
        $field = $this->getUserFieldService()->getField($id);

        if (empty($field)) {
//            throw $this->createNotFoundException();
        }

        if ($request->getMethod() == 'POST') {

            $auth = $this->getSettingService()->get('auth', array());

            $courseSetting = $this->getSettingService()->get('course', array());

            if (isset($auth['registerFieldNameArray'])) {
                foreach ($auth['registerFieldNameArray'] as $key => $value) {
                    if ($value == $field['fieldName']) {
                        unset($auth['registerFieldNameArray'][$key]);
                    }

                }
            }
            if (isset($courseSetting['userinfoFieldNameArray'])) {
                foreach ($courseSetting['userinfoFieldNameArray'] as $key => $value) {
                    if ($value == $field['fieldName']) {
                        unset($courseSetting['userinfoFieldNameArray'][$key]);
                    }

                }
            }
            $this->getSettingService()->set('auth', $auth);

            $this->getSettingService()->set('course', $courseSetting);

            $this->getUserFieldService()->dropField($id);

            return $this->redirect($this->generateUrl('admin_setting_user_fields'));
        }

        return $this->render('System:user-fields.modal.delete', array(
            'field' => $field,
        ));
    }
    
    public function addUserFieldsAction(Request $request)
    {
        $field = $request->request->all();
        if (isset($field['field_title'])
            && in_array($field['field_title'], array('真实姓名', '手机号码', 'QQ', '所在公司', '身份证号码', '性别', '职业', '微博', '微信'))) {
            throw $this->createAccessDeniedException('请勿添加与默认字段相同的自定义字段！');
        }
//
        $field = $this->getUserFieldService()->addUserField($field);
        if ($field == false) {
            $this->setFlashMessage('danger', '已经没有可以添加的字段了!');
        }

        return $this->redirect($this->generateUrl('admin_setting_user_fields'));
    }
    
    public function generalizeAction(Request $request){
        $generalize = $this->getSettingService()->get('generalize', array());
        $default = array('enabled' =>0);
        $generalize = array_merge($default, $generalize);
        if ($request->getMethod() == 'POST') {
            $generalize = $request->request->all(); 
            if($generalize['enabled'] ==1){
                 createService('Generalize.GeneralizeLevelServiceModel')->Levelsync();  
            }
            $this->getSettingService()->set('generalize', $generalize);
            $this->getLogService()->info('system', 'update_settings', "更新推广设置", $generalize);
            $this->setFlashMessage('success', '推广设置已保存！'); 
          
        }
         
        return $this->render('System:generalize',array('generalize'=>$generalize));
    }


	public function consultSettingAction(Request $request)
    {
        $consult = $this->getSettingService()->get('consult', array());
        $default = array(
            'enabled' => 0,
            'worktime' => '9:00 - 17:00',
            'qq' => array(
                array('name' => '', 'number' => ''),
            ),
            'qqgroup' => array(
                array('name' => '', 'number' => ''),
            ),
            'phone' => array(
                array('name' => '', 'number' => ''),
            ),
            'webchatURI' => '',
            'email' => '',
            'color' => 'default',
        );

        $consult = array_merge($default, $consult);
        if ($request->getMethod() == 'POST') {
            $consult = $request->request->all();
            ksort($consult['qq']);
            ksort($consult['qqgroup']);
            ksort($consult['phone']);
            $this->getSettingService()->set('consult', $consult);
            $this->getLogService()->info('system', 'update_settings', "更新QQ客服设置", $consult);
            $this->setFlashMessage('success', 'QQ客服设置已保存！');
        }
        return $this->render('System:consult-setting', array(
            'consult' => $consult,
        ));
    }

    public function consultUploadAction(Request $request)
    {
        $file = $request->files->get('consult');
        if (!FileToolkit::isImageFile($file)) {
            throw $this->createAccessDeniedException('图片格式不正确！');
        }
        
        $filename =  C('WEBSITE_CODE').'_'.time();
        $filename = $filename.'.'.$file->getClientOriginalExtension();

        $directory = getParameter('redcloud.upload.public_directory')."/xitong";
        $file = $file->move($directory, $filename);

        $consult = $this->getSettingService()->get('consult', array());

        $consult['webchatURI'] = getParameter('redcloud.upload.public_url_path')."/xitong/{$filename}";
        $consult['webchatURI'] = ltrim($consult['webchatURI'], '/');

        $this->getSettingService()->set('consult', $consult);

        $this->getLogService()->info('system', 'update_settings', "更新微信二维码", array('webchatURI' => $consult['webchatURI']));

        $response = array(
            'path' => $consult['webchatURI'],
            'url' => generateUrl($consult['webchatURI']),
        );
        echo json_encode($response);
//        return new Response(json_encode($response));

    }
    
    private function getCourseService()
    {
        return createService('Course.CourseServiceModel');
    }

    protected function getUploadFileService()
    {
        return createService('File.UploadFileService');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }

    protected function getSettingService()
    {
        return createService('System.SettingServiceModel');
    }

    protected function getUserFieldService()
    {
        return createService('User.UserFieldServiceModel');
    }

    protected function getAuthService()
    {
        return createService('User.AuthService');
    }
    
    public  function getFileService() {
        return createService('Content.FileServiceModel');
    }

}
