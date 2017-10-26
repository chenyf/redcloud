<?php
namespace User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Common\Form\UserProfileType;
use Common\Form\TeacherProfileType;
use Common\Component\OAuthClient\OAuthClientFactory;
use Common\Lib\FileToolkit;
use Common\Lib\ArrayToolkit;
use Common\Lib\SmsToolkit;
use Common\Lib\MailBat;
use Common\Lib\Paginator;
use Common\Lib\WebCode;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;

class SettingsController extends \Home\Controller\BaseController
{
    
    /**
     * 判断登录
     * @author fubaosheng 2015-06-01
     */
    public function _initialize(){
        $app = $this->getCurrentUser();
        if(!$app->isLogin())
            $this->redirect('User/Signin/index');
    }
    
    /**
     * 个人基本信息
     * @author fubaosheng 2015-04-28
     */
    public function profileAction(Request $request)
    {
        $user = $this->getCurrentUser();

        $profile = $this->getUserService()->getUserProfile($user['id']);

        if ($request->getMethod() == 'POST') {
            $profile = $request->request->get('profile');
            $userAvatar = array(
                'smallAvatar'  =>  $profile['avatar0'],
                'mediumAvatar' =>  $profile['avatar1'],
                'largeAvatar'  =>  $profile['avatar2'],
            );

            unset($profile['avatar0']);
            unset($profile['avatar1']);
            unset($profile['avatar2']);

            $result = $this->getUserService()->updateUserProfile($user['id'], $profile);
            if($result->code != 20){
                $this->setFlashMessage('error', $result->msg);
            }else{
                $this->getUserService()->updateUser($user['id'],$userAvatar);
            }

            $this->setFlashMessage('success', '基础信息保存成功。');
            #add qzw by 2015-10-23
            clearUserCache($user['id']);
//            if (!((strlen($user['verifiedMobile']) > 0) && (isset($mobile)))) {
//                $this->getUserService()->updateUserProfile($user['id'], $profile);
//                $this->setFlashMessage('success', '基础信息保存成功。');
//            } else {
//                $this->setFlashMessage('danger', '不能修改已绑定的手机。');
//            }

            return $this->redirect($this->generateUrl('settings'));

        }

        //$fields = $this->getUserFieldService()->getAllFieldsOrderBySeqAndEnabled();
        $fields = array();
        for($i=0;$i<count($fields);$i++){
            if(strstr($fields[$i]['fieldName'], "textField")) $fields[$i]['type']="text";
            if(strstr($fields[$i]['fieldName'], "varcharField")) $fields[$i]['type']="varchar";
            if(strstr($fields[$i]['fieldName'], "intField")) $fields[$i]['type']="int";
            if(strstr($fields[$i]['fieldName'], "floatField")) $fields[$i]['type']="float";
            if(strstr($fields[$i]['fieldName'], "dateField")) $fields[$i]['type']="date";
        }
        
        if (array_key_exists('idcard',$profile) && $profile['idcard']=="0") {
            $profile['idcard'] = "";
        }

//        $fromCourse = $request->query->get('fromCourse');
        $data['studUid'] = $this->getCurrentUser()->id;

        return $this->render('Settings:profile', array(
            'profile' => $profile,
            'fields'=>$fields,
//            'fromCourse' => $fromCourse,
            'user' => $user,
        ));
    }

    public function approvalSubmitAction(Request $request)
    {
        $user = $this->getCurrentUser();
        if ($request->getMethod() == 'POST') {
            $faceImg = $request->files->get('faceImg');
            $backImg = $request->files->get('backImg');
            
            if (!FileToolkit::isImageFile($backImg) || !FileToolkit::isImageFile($faceImg) ) {
                return $this->createMessageResponse('error', '上传图片格式错误，请上传jpg, bmp,gif, png格式的文件。');
            }
            $arr['truename'] = $_POST['truename'];
            $arr["idcard"] = $_POST['idcard'];
            $directory = getParameter('redcloud.upload.private_directory') . '/approval';
            $this->getUserService()->applyUserApproval($user['id'], $arr, $faceImg, $backImg, $directory);
//            $this->getUserService()->applyUserApproval($user['id'], $request->request->all(), $faceImg, $backImg, $directory);
            $this->setFlashMessage('success', '实名认证提交成功！');
            return $this->redirect($this->generateUrl('settings'));
        }
        return $this->render('Settings:approval', array(
        ));
    }

    public function nicknameAction(Request $request)
    {
        $user = $this->getCurrentUser();
        
        $is_nickname = $this->getSettingService()->get('user_partner');

        if($is_nickname['nickname_enabled'] == 0){
            return $this->redirect($this->generateUrl('settings'));
        }

        if ($request->getMethod() == 'POST') {

            $nickname = $request->request->get('nickname');
            $this->getAuthService()->changeNickname($user['id'], $nickname);
            $this->setFlashMessage('success', '姓名修改成功！');
            return $this->redirect($this->generateUrl('settings'));
        }
        return $this->render('Settings:nickname', array(
        ));
    }
    
    /**
     * 个人信息学号验证
     * @author 谈海涛 2015-09-29
     */
    public function studNumAction(Request $request)
    {
        $uid = $this->getCurrentUser()->id;
        if ($request->getMethod() == 'POST') {

            $nickname = $request->request->get('nickname');
            $studNum = $request->request->get('studNum');
            
            
            $data['nickname'] = empty($nickname) ? '' : trim($nickname);
            $data['studNum'] = empty($studNum) ? '' : trim($studNum);
            
            if($data['nickname'] == '')
                return $this->createJsonResponse(array('status' => 'error','info'=>'请输入姓名'));
            if($data['studNum'] == '')
                return $this->createJsonResponse(array('status' => 'error','info'=>'请输入学号'));
            $user = $this->getUserService()->getUserByStudNum($data['studNum']);
            
            if($user['id'] && $user['id'] != $uid ) 
                return $this->createJsonResponse(array('status' => 'error','info'=>'对不起，此学号已被其他用户绑定，请检查信息是否输入正确或请老师验证解决！'));
           //未绑定学号，验证临时表是否有数据
            $field['studName'] = $data['nickname'];
            $field['studNum'] = $data['studNum'];
            $exitsStud = $this->getUserService()->existTmpStudInfo($field);
            
            if(empty($exitsStud)) 
                return $this->createJsonResponse(array('status' => 'error','info'=>'对不起，此学号不存在，请确认信息是否输入正确！'));
            
            $r = $this->getUserService()->updateUser($uid,$data);
            if($r) return $this->createJsonResponse(array('status' => 'success','info'=>'恭喜，你已经通过学号验证！'));
        }
        return $this->render('Settings:studnum', array(
            'uid'=>$uid
        ));
    }
    
    /**
     * 学号绑定申请
     * @author 谈海涛 2015-09-30
     */
    public function studApplyAction(Request $request)
    {
          
        $all = $request->request->all();
        $data['studNum'] = empty($all['studNum']) ? '' : trim($all['studNum']);
        $data['studName'] = empty($all['nickname']) ? '' : trim($all['nickname']);
        $data['classId'] = empty($all['classId']) ? '' : intval($all['classId']);
        $data['studRemark'] = empty($all['studRemark']) ? '' : trim($all['studRemark']);
        $data['pictrue'] = empty($all['userpic']) ? '' : trim($all['userpic']);
        $data['studUid'] = $this->getCurrentUser()->id;
        
        if(!$data['studUid'] || !$data['studNum'] || !$data['studName']) {
           $datajson = json_encode(array('status' => 'error','info'=>'姓名或学号不合法')); 
           echo $datajson;
           //echo "<script>parent.upload_search_callback('" . $datajson . "');</script>";
            exit;
        }
        $apply = $this->getUserService()->getApplyBystudUid($data['studUid']);
        
        if(!empty($apply)){
            $data['applyTime'] = time();
            $r = $this->getUserService()->updateTmpStudApply($apply[id],$data);
        }else{
            $r = $this->getUserService()->addTmpStudApply($data);
        }
        if(!$r){
            $datajson = $this->json_encode(array('status' => 'error','info'=>'提交失败'));
        }else{
            $datajson = json_encode(array('status' => 'OK','info'=>'提交成功，请耐心等待老师审核！'));
        }
        echo $datajson;
        //echo "<script>parent.upload_search_callback('" . $datajson . "');</script>";
        exit;
    }

    /**
     * 取消学号绑定申请
     * @author 谈海涛 2015-10-09
     */
    public function delStudApplyAction(Request $request)
    {
        $parm = $request->request->all();
        if($parm['value'] == 'delete'){
            $uid = $this->getCurrentUser()->id;
            $r   = $this->getUserService()->delTmpStudApply($uid);
        }
        if($r) return $this->createJsonResponse(array('status' => 'success', 'info' => '绑定学号申请，取消成功！'));
        return $this->createJsonResponse(array('status' => 'error', 'info' => '绑定学号申请，取消失败！'));
    }
    /**
     * 学号绑定之证件照上传
     * @author 谈海涛 2015-10-08
     */
    public function applyPictureAction(Request $request, $id)
    {   
        if($request->getMethod() == 'POST'){
            $file = $request->files->get('picture');
            
            $record = $this->getFileService()->uploadFile('default', $file);
            
            $record['url'] =getFilePath($record['uri']);
            $src = SITE_PATH.$record['url'];
            $size = getimagesize($src);
            
            $width  = $size[0];
            $height = $size[1];
            
            if($width > $height){
                if($width > 200){
                    $height = (200/$width)*$height;
                    $width = 200;
                }
                    
            }else{
                if($height > 200){
                    $width = (200/$height)*$width;
                    $height = 200;
                }
            }
            
            $frdata = array( 'status' => 'success','pictureUrl' => $record['url'],'width'=>$width,'height'=>$height);
            $datajson = json_encode($frdata);
            echo $datajson;
            //echo "<script>parent.upload_search_callback('" . $datajson . "');</script>";
            exit;
            
        }

    }

    /**
     * 学号绑定之班级搜索
     * @author 谈海涛 2015-10-08
     */
    public function getStudClassAction(Request $request)
    {   
        $q = $request->query->all();
        $className = $request->query->get('className');
        $calss = $this->getGroupService()->getStudGroupByTitle($className);
        return $this->createJsonResponse(array('more' => 'false', 'results' => $calss));

    }
    
    public function nicknameCheckAction(Request $request)
    {
        $nickname = $request->query->get('value');
        $currentUser = $this->getUserService()->getCurrentUser();

        if ($currentUser['nickname'] == $nickname){
            return $this->createJsonResponse(array('success' => true, 'message' => ''));
        }

        list ($result, $message) = $this->getAuthService()->checkUsername($nickname);
        if ($result == 'success'){
            $response = array('success' => true, 'message' => '');
        } else {
            $response = array('success' => false, 'message' => '姓名已存在');
        }
    
        return $this->createJsonResponse($response);
    }

    public function avatarAction2(Request $request)
    {
        $user = $this->getCurrentUser();

        $form = $this->createFormBuilder()
            ->add('avatar', 'file')
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
//            if ($form->isValid()) {
            if (1) {
                $data = $form->getData();
                $file = $data['avatar'];

                if (!FileToolkit::isImageFile($file)) {
                    $arr = array('error'=>true,'message' => '上传图片格式错误，请上传jpg, gif, png格式的文件。');
                    echo json_encode($arr);exit();
//                    return $this->createMessageResponse('error', '上传图片格式错误，请上传jpg, gif, png格式的文件。');
                }

                $filenamePrefix = "user_{$user['id']}_";
                $hash = substr(md5($filenamePrefix . time()), -8);
                $ext = $file->getClientOriginalExtension();
                $filename = $filenamePrefix . $hash . '.' . $ext;

                $directory = getParameter('redcloud.upload.public_directory') . '/tmp';
                $file = $file->move($directory, $filename);

                $fileName = str_replace('.', '!', $file->getFilename());
                
                $arr = array('file' => $fileName);
                echo json_encode($arr);exit();
//                return $this->redirect($this->generateUrl('settings_avatar_crop', array(
//                    'file' => $fileName)
//                ));
            }
        }

        $hasPartnerAuth = $this->getAuthService()->hasPartnerAuth();
        if ($hasPartnerAuth) {
            $partnerAvatar = $this->getAuthService()->getPartnerAvatar($user['id'], 'big');
        } else {
            $partnerAvatar = null;
        }

//        $fromCourse = $request->query->get('fromCourse');
        
        return $this->render('Settings:avatar', array(
            'form' => $form->createView(),
            'user' => $this->getUserService()->getUser($user['id']),
            'partnerAvatar' => $partnerAvatar,
//            'fromCourse' => $fromCourse,
        ));
    }

    //上传用户头像
    public function avatarAction(Request $request){

        $user = $this->getCurrentUser();
        $file = $request->files->get('avatar');

        if (!FileToolkit::isImageFile($file)) {
            $arr = array('error'=>true,'message' => '上传图片格式错误，请上传jpg, gif, png格式的文件。');
            echo json_encode($arr);exit();
//                    return $this->createMessageResponse('error', '上传图片格式错误，请上传jpg, gif, png格式的文件。');
        }

        $filenamePrefix = "user_{$user['id']}_";
        $hash = substr(md5($filenamePrefix . time()), -8);
        $ext = $file->getClientOriginalExtension();
        $filename = $filenamePrefix . $hash . '.' . $ext;

        $directory = getParameter('redcloud.upload.public_directory') . '/tmp';
        $file = $file->move($directory, $filename);

        $fileName = str_replace('.', '!', $file->getFilename());

        $arr = array('file' => $fileName);
        echo json_encode($arr);exit();
    }

    //模态框裁剪用户头像
    public function avatarCropModalAction(Request $request){
        $currentUser = $this->getCurrentUser();
        $filename = $request->query->get('file');
        $filename = str_replace('!', '.', $filename);
        $filename = str_replace(array('..' , '/', '\\'), '', $filename);

        $pictureFilePath = getParameter('redcloud.upload.public_directory') . '/tmp/' . $filename;

        if($request->getMethod() == 'POST') {
            $options = $request->request->all();

            $pictureFilePath = getParameter('redcloud.upload.public_directory') . '/tmp/' . $options['filename'];

            $fileRecords = $this->getUserService()->changeAvatar($currentUser['id'], $pictureFilePath, $options);


            $pic_url = getParameter('redcloud.upload.public_url_path') . '/' . str_replace('public://', '', $fileRecords[2]);

            $response = array(
                'uri0'  =>  $fileRecords[0],
                'uri1'  =>  $fileRecords[1],
                'uri2'  =>  $fileRecords[2],
                'url'   =>  $pic_url
            );

            return $this->createJsonResponse($response);
        }

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

        return $this->render('Settings:avatar-crop-modal', array(
            'pictureUrl' => $pictureUrl,
            'naturalSize' => $naturalSize,
            'scaledSize' => $scaledSize,
            'filename'  =>  $filename
        ));
    }

    public function avatarCropAction(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $filename = $request->query->get('file');
        $filename = str_replace('!', '.', $filename);
        $filename = str_replace(array('..' , '/', '\\'), '', $filename);

        $pictureFilePath = getParameter('redcloud.upload.public_directory') . '/tmp/' . $filename;

        if($request->getMethod() == 'POST') {
            $options = $request->request->all();
            $this->getUserService()->changeAvatar($currentUser['id'], $pictureFilePath, $options);
            if($currentUser->isTeacher()) {
                $this->buildTeacherHtml($currentUser);
            }
            return $this->redirect($this->generateUrl('settings_avatar'));
        }

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

        return $this->render('Settings:avatar-crop', array(
            'pictureUrl' => $pictureUrl,
            'naturalSize' => $naturalSize,
            'scaledSize' => $scaledSize,
        ));
    }

    public function avatarFetchPartnerAction(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        if (!$this->getAuthService()->hasPartnerAuth()) {
            throw $this->createNotFoundException();
        }

        $url = $this->getAuthService()->getPartnerAvatar($currentUser['id'], 'big');
        if (empty($url)) {
            $this->setFlashMessage('danger', '获取论坛头像地址失败！');
            return $this->createJsonResponse(true);
        }

        $avatar = $this->fetchAvatar($url);
        if (empty($avatar)) {
            $this->setFlashMessage('danger', '获取论坛头像失败或超时，请重试！');
            return $this->createJsonResponse(true);
        }

        $avatarPath = getParameter('redcloud.upload.public_directory') . '/tmp/' . $currentUser['id'] . '_' . time() . '.jpg';

        file_put_contents($avatarPath, $avatar);

        $this->getUserService()->changeAvatar($currentUser['id'], $avatarPath, array('x'=>0, 'y'=>0, 'width'=>200, 'height' => 200));

        if($currentUser->isTeacher()) {
            $this->buildTeacherHtml($currentUser);
        }

        return $this->createJsonResponse(true);
    }

    public function securityAction(Request $request) 
    { 
        $user = $this->getCurrentUser(); 
        if (empty($user['setup'])) {
            return $this->redirect($this->generateUrl('settings_setup'));
        }

        $hasLoginPassword = strlen($user['password']) > 0;
        $hasPayPassword = strlen($user['payPassword']) > 0;
        $userSecureQuestions = $this->getUserService()->getUserSecureQuestionsByUserId($user['id']);
        $hasFindPayPasswordQuestion = (isset($userSecureQuestions)) && (count($userSecureQuestions) > 0);
        $hasVerifiedMobile = (isset($user['verifiedMobile'])&&(strlen($user['verifiedMobile'])>0));

        $cloudSmsSetting = $this->getSettingService()->get('cloud_sms');
        $showBindMobile = (isset($cloudSmsSetting['sms_enabled'])) && ($cloudSmsSetting['sms_enabled'] == '1') 
                            && (isset($cloudSmsSetting['sms_bind'])) && ($cloudSmsSetting['sms_bind'] == 'on');

        $itemScore = floor(100.0/(3.0 + ($showBindMobile?1.0:0)));
        $progressScore = 1 + ($hasLoginPassword? $itemScore:0 ) + ($hasPayPassword? $itemScore:0 ) + ($hasFindPayPasswordQuestion? $itemScore:0 ) + ($showBindMobile && $hasVerifiedMobile ? $itemScore:0 );
        if ($progressScore <= 1 ) {
            $progressScore = 0;
        }

        return $this->render('Settings:security', array( 
            'progressScore' => $progressScore,
            'hasLoginPassword' => $hasLoginPassword,
            'hasPayPassword' => $hasPayPassword,
            'hasFindPayPasswordQuestion' => $hasFindPayPasswordQuestion,
            'hasVerifiedMobile' => $hasVerifiedMobile,
        )); 
    } 

    public function payPasswordAction(Request $request) 
    { 
        $user = $this->getCurrentUser(); 

        $hasPayPassword = strlen($user['payPassword']) > 0;

        if ($hasPayPassword){
            $this->setFlashMessage('danger', '不能直接设置新支付密码。');
            return $this->redirect($this->generateUrl('settings_reset_pay_password'));
        }

        $form = $this->createFormBuilder()
            ->add('currentUserLoginPassword', 'password')
            ->add('newPayPassword', 'password')
            ->add('confirmPayPassword', 'password')
            ->getForm();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
//            if ($form->isValid()) {
            if(1){
                $passwords = $form->getData();
//                dump($passwords);die;
                if (!$this->getAuthService()->checkPassword($user['id'], $passwords['currentUserLoginPassword'])) {
                            return $this->render('Settings:pay-password', array( 
                                    'form' => $form->createView(),
                                    'message' => '当前用户登录密码不正确，请重试！',
                                    'res' =>'danger'
                            )); 
//                                $this->setFlashMessage('danger', '当前用户登录密码不正确，请重试！');
//                                  return $this->redirect($this->generateUrl('settings_pay_password'));
			} else {
                            $this->getAuthService()->changePayPassword($user['id'], $passwords['currentUserLoginPassword'], $passwords['newPayPassword']);
                            return $this->render('Settings:reset-pay-password', array( 
                                    'form' => $form->createView(),
                                    'message' => '新支付密码设置成功，您可以在此重设密码。',
                                    'res' =>'success'
                            )); 
//				$this->setFlashMessage('success', '新支付密码设置成功，您可以在此重设密码。');
			}

//			return $this->redirect($this->generateUrl('settings_reset_pay_password'));
		}
	}
		return $this->render('Settings:pay-password', array( 
			'form' => $form->createView(),
		)); 
	} 

	public function setPayPasswordAction(Request $request) 
	{ 
		$user = $this->getCurrentUser(); 

		$hasPayPassword = strlen($user['payPassword']) > 0;

		if ($hasPayPassword){
			return $this->createJsonResponse('不能直接设置新支付密码。');
		}

		$form = $this->createFormBuilder()
			->add('currentUserLoginPassword', 'password')
			->add('newPayPassword', 'password')
			->add('confirmPayPassword', 'password')
			->getForm();

		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if (1) {
				$passwords = $form->getData();
				if (!$this->getAuthService()->checkPassword($user['id'], $passwords['currentUserLoginPassword'])) {
					return $this->createJsonResponse(array('ACK' => 'fail', 'message' => '当前用户登录密码不正确，请重试！'));
				} else {
					$this->getAuthService()->changePayPassword($user['id'], $passwords['currentUserLoginPassword'], $passwords['newPayPassword']);
					return $this->createJsonResponse(array('ACK' => 'success', 'message' => '新支付密码设置成功！'));
				}
			}
		}

		return $this->render('Settings:pay-password-modal', array( 
			'form' => $form->createView()
		)); 
	}

	public function resetPayPasswordAction(Request $request) 
	{ 
		$user = $this->getCurrentUser(); 

		$form = $this->createFormBuilder()
			// ->add('currentUserLoginPassword','password')
			->add('oldPayPassword', 'password')
			->add('newPayPassword', 'password')
			->add('confirmPayPassword', 'password')
			->getForm();

		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if (1) {
				$passwords = $form->getData();
		
				if ( !($this->getUserService()->verifyPayPassword($user['id'], $passwords['oldPayPassword']) ) ) 
				{       
                                        return $this->render('Settings:reset-pay-password', array( 
                                                'form' => $form->createView(),
                                                'message' => '支付密码不正确，请重试！',
                                                'res' => 'danger'
                                        ));
//					$this->setFlashMessage('danger', '支付密码不正确，请重试！');
                                        
				}				
				else 
				{
					$this->getAuthService()->changePayPasswordWithoutLoginPassword($user['id'], $passwords['newPayPassword']);
                                        return $this->render('Settings:reset-pay-password', array( 
                                                'form' => $form->createView(),
                                                'message' => '重置支付密码成功',
                                                'res' => 'success'
                                        ));
//					$this->setFlashMessage('success', '重置支付密码成功。');
				}

//				return $this->redirect($this->generateUrl('settings_reset_pay_password'));
			}
		}

		return $this->render('Settings:reset-pay-password', array( 
			'form' => $form->createView()
		)); 
	} 

	private function setPayPasswordPage($request, $userId, $returnRes)
	{       
		$token = $this->getUserService()->makeToken('pay-password-reset',$userId,strtotime('+1 day'))["token"];
		$request->request->set('token',$token);
                return $this->updatePayPasswordReturn('', $token,$returnRes);
//		return $this->forward('Home:Settings:updatePayPassword', array(
//                 'request' => $request,
//                 'returnRes' =>serialize($returnRes)
//                ));
	}

	private function updatePayPasswordReturn($form, $token,$returnRes)
	{
            return $this->render('Settings:update-pay-password-from-email-or-secure-questions', array(
//                    'form' => $form->createView(),
                    'token' => $token?:null,
                    'returnRes'=> $returnRes
            ));
	}
	public function updatePayPasswordAction(Request $request)
	{
            $token = $this->getUserService()->getToken('pay-password-reset', $request->query->get('token')?:$request->request->get('token'));
//		if (empty($token)){
//			throw new \RuntimeException('Bad Token!');
//		}
        $form = $this->createFormBuilder()
            ->add('payPassword', 'password')
            ->add('confirmPayPassword', 'password')
            ->add('currentUserLoginPassword', 'password')
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if (1) {
                $data = $form->getData();
                if ($data['payPassword'] != $data['confirmPayPassword']){
//                	$this->setFlashMessage('danger', '两次输入的支付密码不一致。');
                        $returnRes = array("res"=>'danger', 'message'=>'两次输入的支付密码不一致。' );
			return $this->updatePayPasswordReturn($form, $token, $returnRes);
                }
                $userId= $this->getCurrentUser()->id;
                    if ($this->getAuthService()->checkPassword($userId, $data['currentUserLoginPassword'])){
                    $this->getAuthService()->changePayPassword($userId, $data['currentUserLoginPassword'], $data['payPassword']);
//                if ($this->getAuthService()->checkPassword($token['userId'], $data['currentUserLoginPassword'])){
//                        $this->getAuthService()->changePayPassword($token['userId'], $data['currentUserLoginPassword'], $data['payPassword']);
//	                $this->getUserService()->deleteToken('pay-password-reset',$token['token']);
	                return $this->render('Settings:pay-password-success');
	            }else{
//	            	$this->setFlashMessage('danger', '用户登录密码错误。');
                        $returnRes = array("res"=>'danger', 'message'=>'用户登录密码错误。' );
                        return $this->updatePayPasswordReturn($form, $token,$returnRes);
	            }
            }
        }
            return $this->updatePayPasswordReturn($form, $token);
	}

	private function findPayPasswordActionReturn($userSecureQuestions, $returnRes, $hasSecurityQuestions, $hasVerifiedMobile)
	{
		$questionNum = rand(0,2);
		$question = $userSecureQuestions[$questionNum]['securityQuestionCode'];
		return $this->render('Settings:find-pay-password', array( 
			'question' => $question,
			'questionNum' => $questionNum,
			'hasSecurityQuestions' => $hasSecurityQuestions,
			'hasVerifiedMobile' => $hasVerifiedMobile,
                        'returnRes' => $returnRes,
		)); 		
	}

	public function findPayPasswordAction(Request $request) 
	{ 
		$user = $this->getCurrentUser(); 
		$userSecureQuestions = $this->getUserService()->getUserSecureQuestionsByUserId($user['id']);
        $hasSecurityQuestions = (isset($userSecureQuestions)) && (count($userSecureQuestions) > 0);
        $verifiedMobile = $user['verifiedMobile'];
        $hasVerifiedMobile = (isset($verifiedMobile ))&&(strlen($verifiedMobile)>0);
        $canSmsFind = ($hasVerifiedMobile) && 
        			  ($this->getEduCloudService()->getCloudSmsKey('sms_enabled') == '1') &&
        			  ($this->getEduCloudService()->getCloudSmsKey('sms_forget_pay_password') == 'on');

		if ((!$hasSecurityQuestions)&&($canSmsFind)) {
			return $this->redirect($this->generateUrl('settings_find_pay_password_by_sms', array()));
		}

		if (!$hasSecurityQuestions) {
			$this->setFlashMessage('danger', '您还没有安全问题，请先设置。');
			return $this->forward('User:Settings:securityQuestions');
		}

		if ($request->getMethod() == 'POST') {

 			$questionNum = $request->request->get('questionNum');
 			$answer = $request->request->get('answer');
                               
			$userSecureQuestion = $userSecureQuestions[$questionNum];

 			$isAnswerRight = $this->getUserService()->verifyInSaltOut(
                                          $answer, $userSecureQuestion['securityAnswerSalt'] , $userSecureQuestion['securityAnswer'] ); 

 			if (!$isAnswerRight){
                                $returnRes =array("res"=> 'danger' , "message" => '回答错误'); 
// 				$this->setFlashMessage('danger', '回答错误。');
 				return $this->findPayPasswordActionReturn($userSecureQuestions,$returnRes);
 			}
                        $returnRes = array('res' =>'success', 'message' =>'回答正确，你可以开始更新支付密码。'); 
// 			$this->setFlashMessage('success', '回答正确，你可以开始更新支付密码。');
 			return $this->setPayPasswordPage($request, $user['id'], $returnRes);

		}
		return $this->findPayPasswordActionReturn($userSecureQuestions, $hasSecurityQuestions, $hasVerifiedMobile);
	}

	public function findPayPasswordBySmsAction(Request $request)
	{
		$eduCloudService = $this->getEduCloudService();
		$scenario = "sms_forget_pay_password";
		if ($eduCloudService->getCloudSmsKey('sms_enabled') != '1'  || $eduCloudService->getCloudSmsKey($scenario) != 'on') {
			return $this->render('Settings:edu-cloud-error', array()); 
        }		

		$currentUser = $this->getCurrentUser();
		
		$userSecureQuestions = $this->getUserService()->getUserSecureQuestionsByUserId($currentUser['id']);
        $hasSecurityQuestions = (isset($userSecureQuestions)) && (count($userSecureQuestions) > 0);
        $verifiedMobile = $currentUser['verifiedMobile'];
        $hasVerifiedMobile = (isset($verifiedMobile ))&&(strlen($verifiedMobile)>0);

		if (!$hasVerifiedMobile){
			$this->setFlashMessage('danger', '您还没有绑定手机，请先绑定。');
			return $this->redirect($this->generateUrl('settings_bind_mobile', array(
            )));
		}

		if ($request->getMethod() == 'POST'){
			if ($currentUser['verifiedMobile'] != $request->request->get('mobile')){
				$this->setFlashMessage('danger', '您输入的手机号，不是已绑定的手机');
				SmsToolkit::clearSmsSession($request, $scenario);
				goto response;
			}			
			list($result, $sessionField, $requestField) = SmsToolkit::smsCheck($request, $scenario);
			if ($result) {
				$this->setFlashMessage('success', '验证通过，你可以开始更新支付密码。');
 				return $this->setPayPasswordPage($request, $currentUser['id']);
			}else{
				$this->setFlashMessage('danger', '验证错误。');
			}
			
		}
		response:
		return $this->render('Settings:find-pay-password-by-sms', array(
			'hasSecurityQuestions' => $hasSecurityQuestions,
			'hasVerifiedMobile' => $hasVerifiedMobile,
			'verifiedMobile' => $verifiedMobile
		));
	}

	private function securityQuestionsActionReturn($hasSecurityQuestions, $userSecureQuestions)
	{
		$question1 = null;$question2 = null;$question3 = null;
		if ($hasSecurityQuestions){
			$question1 = $userSecureQuestions[0]['securityQuestionCode'];
			$question2 = $userSecureQuestions[1]['securityQuestionCode'];
			$question3 = $userSecureQuestions[2]['securityQuestionCode'];
		}

		return $this->render('Settings:security-questions', array( 
			'hasSecurityQuestions' => $hasSecurityQuestions,
			'question1' => $question1,
			'question2' => $question2,
			'question3' => $question3,
		)); 		
	}

	public function securityQuestionsAction(Request $request)
	{
		$user = $this->getCurrentUser(); 
		$userSecureQuestions = $this->getUserService()->getUserSecureQuestionsByUserId($user['id']);
		$hasSecurityQuestions = (isset($userSecureQuestions)) && (count($userSecureQuestions) > 0);

		if ($request->getMethod() == 'POST') {
			if (!$this->getAuthService()->checkPassword($user['id'],$request->request->get('userLoginPassword')) ){
				$this->setFlashMessage('danger', '您的登录密码错误，不能设置安全问题。');
				return $this->securityQuestionsActionReturn($hasSecurityQuestions, $userSecureQuestions);
			}

			if ($hasSecurityQuestions){
				throw new \RuntimeException('您已经设置过安全问题，不可再次修改。');
			}

			if ($request->request->get('question-1') == $request->request->get('question-2')
			 	|| $request->request->get('question-1') == $request->request->get('question-3')
			 	|| $request->request->get('question-2') == $request->request->get('question-3')){
				throw new \RuntimeException('2个问题不能一样。');
			}
			$fields = array(  
					'securityQuestion1'  => $request->request->get('question-1'),
					'securityAnswer1' => $request->request->get('answer-1'),
					'securityQuestion2'  => $request->request->get('question-2'),
					'securityAnswer2' => $request->request->get('answer-2'),
					'securityQuestion3'  => $request->request->get('question-3'),
					'securityAnswer3' => $request->request->get('answer-3'),
				);							
			$this->getUserService()->addUserSecureQuestionsWithUnHashedAnswers($user['id'],$fields);
			$this->setFlashMessage('success', '安全问题设置成功。');
			$hasSecurityQuestions = true;
			$userSecureQuestions = $this->getUserService()->getUserSecureQuestionsByUserId($user['id']);
		}		

		return $this->securityQuestionsActionReturn($hasSecurityQuestions, $userSecureQuestions);
	}

	private function bindMobileReturn($hasVerifiedMobile, $setMobileResult, $verifiedMobile)
	{
		return $this->render('Settings:bind-mobile', array(
			'hasVerifiedMobile' => $hasVerifiedMobile,
			'setMobileResult' => $setMobileResult,
			'verifiedMobile' => $verifiedMobile
		)); 
	}

	public function bindMobileAction(Request $request)
	{
		$eduCloudService = $this->getEduCloudService();
		$currentUser = $this->getCurrentUser()->toArray();
		$verifiedMobile = '';
		$hasVerifiedMobile = (isset($currentUser['verifiedMobile'])&&(strlen($currentUser['verifiedMobile'])>0));
		if ($hasVerifiedMobile) {
			$verifiedMobile = $currentUser['verifiedMobile'];
		}
		$setMobileResult = 'none';

		$scenario = "sms_bind";
		if ($eduCloudService->getCloudSmsKey('sms_enabled') != '1'  || $eduCloudService->getCloudSmsKey($scenario) != 'on') {
			return $this->render('Settings:edu-cloud-error', array()); 
        }

        if ($request->getMethod() == 'POST') {
        	$password = $request->request->get('password');
        	if (!$this->getAuthService()->checkPassword($currentUser['id'], $password)) {
				$this->setFlashMessage('danger', '您的登录密码错误');
				SmsToolkit::clearSmsSession($request, $scenario);
				return $this->bindMobileReturn($hasVerifiedMobile, $setMobileResult, $verifiedMobile);
			}

			list($result, $sessionField, $requestField) = SmsToolkit::smsCheck($request, $scenario);
			if ($result) {
				$verifiedMobile = $sessionField['to'];
				$this->getUserService()->changeMobile($currentUser['id'], $verifiedMobile);

				$setMobileResult = 'success';
				$this->setFlashMessage('success', '绑定成功');
			}else{
				$setMobileResult = 'fail';
				$this->setFlashMessage('danger', '绑定失败，原短信失效');
			}						
		}

		return $this->bindMobileReturn($hasVerifiedMobile, $setMobileResult, $verifiedMobile);
	}

	public function passwordCheckAction(Request $request)
	{
		$currentUser = $this->getCurrentUser();
		$password = $request->request->get('value');
		if (strlen($password) > 0) {
			$passwordRight = $this->getUserService()->verifyPassword($currentUser['id'], $password);
			if ($passwordRight) {
				$response = array('success' => true, 'message' => '密码正确');
			} else {
				$response = array('success' => false, 'message' => '密码错误');
			}
		} else {			
			$response = array('success' => false, 'message' => '密码不能为空');
		}
      
        return $this->createJsonResponse($response);
	}

	public function passwordAction(Request $request)
	{
		$user = $this->getCurrentUser();

		if (empty($user['setup'])) {
			return $this->redirect($this->generateUrl('settings_setup'));
		}

		$form = $this->createFormBuilder()
			->add('currentPassword', 'password')
			->add('newPassword', 'password')
			->add('confirmPassword', 'password')
			->getForm();

		if ($request->getMethod() == 'POST') {
			$form->bind($request);
//			if ($form->isValid()) {
			if (1) {
				$passwords = $form->getData();
				if (!$this->getAuthService()->checkPassword($user['id'], $passwords['currentPassword'])) {
                                        return $this->render('Settings:password', array(
                                                'message' => '当前密码不正确，请重试！',
                                                'res' => 'danger'
                                        ));
//					$this->setFlashMessage('danger', '当前密码不正确，请重试！');
				} else {
                                        #验证 fubaosheng 2015-08-13
                                        if(empty($passwords['newPassword']) || $passwords['newPassword']!=$passwords['confirmPassword']){
                                            return $this->render('Settings:password', array(
                                                    'message' => '新密码和确认密码不一致',
                                                    'res' => 'danger'
                                            ));
                                        }
                                    
					$this->getAuthService()->changePassword($user['id'], $passwords['currentPassword'], $passwords['newPassword']);                 
                                        return $this->render('Settings:password', array(
                                                'message' => '密码修改成功',
                                                'res' => 'success'
                                        ));
//					$this->setFlashMessage('success', '密码修改成功。');
				}

//				return $this->redirect($this->generateUrl('settings_password'));
			}
		}
		return $this->render('Settings:password', array(
			'form' => $form->createView()
		));
	}

	public function emailAction(Request $request)
	{
		$user = $this->getCurrentUser();
		$mailer = $this->getSettingService()->get('mailer', array());
		if (empty($user['setup'])) {
			return $this->redirect($this->generateUrl('settings_setup'));
		}
		$form = $this->createFormBuilder()
			->add('password', 'password')
			->add('email', 'text')
			->getForm();
               
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			if (1) {
				$data = $form->getData();
				$isPasswordOk = $this->getUserService()->verifyPassword($user['id'], $data['password']);
				if (!$isPasswordOk) {
                                    return $this->render("Settings:email", array(
                                            'form' => $form->createView(),
                                            'mailer' =>$mailer,
                                            'message' => '密码不正确，请重试。',
                                            'res' => 'danger'
                                    ));
				}
                                
                                if(!isValidEmail($data['email'])){
                                    return $this->render("Settings:email", array(
                                           'form' => $form->createView(),
                                           'mailer' =>$mailer,
                                           'message' =>'新登录邮箱格式不正确。',
                                           'res' => 'danger'
                                   )); 
                                }
                               
                                if(!empty($user['email'])){
                                    //已绑定邮箱
                                    $userOfNewEmail = $this->getUserService()->getUserByEmail($data['email']);
                                    if ($userOfNewEmail && $userOfNewEmail['id'] == $user['id']) {
                                        return $this->render("Settings:email", array(
                                                'form' => $form->createView(),
                                                'mailer' =>$mailer,
                                                'message' =>'新邮箱，不能跟当前邮箱一样。',
                                                'res' => 'danger'
                                        )); 
                                    }

                                    if ($userOfNewEmail && $userOfNewEmail['id'] != $user['id']) {
                                        return $this->render("Settings:email", array(
                                                'form' => $form->createView(),
                                                'mailer' =>$mailer,
                                                'message' =>'新邮箱已经被注册，请换一个试试。',
                                                'res' => 'danger'
                                        )); 
                                    }
                                
                                }else{
                                    //未绑定邮箱
                                    $userOfNewEmail = $this->getUserService()->getUserByEmail($data['email']);
                                    if($userOfNewEmail){
                                        return $this->render("Settings:email", array(
                                               'form' => $form->createView(),
                                               'mailer' =>$mailer,
                                               'message' =>'新邮箱已经被注册，请换一个试试。',
                                               'res' => 'danger'
                                       ));
                                    }
                                }
                                
				$token = $this->getUserService()->makeToken('email-verify', $user['id'], strtotime('+1 day'), $data['email'])["token"];
				#edit fubaosheng 2015-08-13
                                $param['to'] = $data['email'];
                                $param['subject'] = "重设{$user['nickname']}在" . $this->setting('site.name', 'CLOUD') . "的电子邮箱";
                                $param['html'] =  $this->render('Settings:email-change', array('user' => $user,'token' => $token,'siteurl' => C('SITE_URL')),true);

                                $mailBat = MailBat::getInstance();
                                $xml = $mailBat->sendMailBySohu($param);
                                $xmlArr = json_decode(json_encode((array) simplexml_load_string($xml)),true);

                                if($xmlArr['message'] != "success"){
                                    $this->getLogService()->error('setting', 'email_change', "邮箱变更确认邮件发送失败[{$user['id']}]");
                                    return $this->render("Settings:email", array(
                                        'form' => $form->createView(),
                                        'mailer' =>$mailer,
                                        'message' =>"邮箱变更确认邮件发送失败，请联系管理员。",
                                        'res' => 'danger'
                                   ));
                                }

                                $this->getLogService()->info('setting', 'email_change', "{$user['id']}发送了邮箱变更确认邮件。");
                                return $this->render("Settings:email", array(
                                        'form' => $form->createView(),
                                        'mailer' =>$mailer,
                                        'message' =>"请到邮箱{$data['email']}中接收确认邮件，并点击确认邮件中的链接完成修改。",
                                        'res' => "success"
                                ));
				return $this->redirect($this->generateUrl('settings_email'));
			}
		}

		return $this->render("Settings:email", array(
			'form' => $form->createView(),
			'mailer' =>$mailer
		));
	}

	public function emailVerifyAction(Request $request)
	{
		$user = $this->getCurrentUser();
		$token = $this->getUserService()->makeToken('email-verify', $user['id'], strtotime('+1 day'), $user['email'])["token"];

                #edit 发送密码重置邮件 fubaosheng 2015-08-13
                $param['to'] = $user['email'];
                $param['subject'] = "验证{$user['nickname']}在{$this->setting('site.name')}的电子邮箱";
                $html = $this->render('Settings:email-verify', array('user' => $user,'token' => $token,'siteurl' => C('SITE_URL')),true);
                $param['html'] =  $html;
                $mailBat = MailBat::getInstance();
                $xml = $mailBat->sendMailBySohu($param);
                $xmlArr = json_decode(json_encode((array) simplexml_load_string($xml)),true);

                if($xmlArr['message'] != "success"){
                    $this->getLogService()->error('setting', 'email-verify', "邮箱验证邮件发送失败[{$user['id']}]");
                    return $this->createJsonResponse(false);
                }

                $this->getLogService()->info('setting', 'email-verify', "{$user['email']}发送了邮箱验证邮件。");

		return $this->createJsonResponse(true);
	}

	public function bindsAction(Request $request)
	{
		$user = $this->getCurrentUser();
		$clients = OAuthClientFactory::clients();
		$userBinds = $this->getUserService()->findBindsByUserId($user->id) ?  : array();
		foreach($userBinds as $userBind) {
			$clients[$userBind['type']]['status'] = 'bind';
		}
		return $this->render('Settings:binds', array(
			'clients' => $clients,
		));
	}

	public function unBindAction(Request $request, $type)
	{
		$user = $this->getCurrentUser();
		$this->checkBindsName($type);
		$userBinds = $this->getUserService()->unBindUserByTypeAndToId($type, $user->id);
		return $this->redirect($this->generateUrl('settings_binds'));
	}

	public function bindAction(Request $request, $type)
	{
		$this->checkBindsName($type);
		$callback = $this->generateUrl('settings_binds_bind_callback', array('type' => $type), true);
		$settings = $this->setting('login_bind');
		$config = array('key' => $settings[$type.'_key'], 'secret' => $settings[$type.'_secret']);
		$client = OAuthClientFactory::create($type, $config);
		return $this->redirect($client->getAuthorizeUrl($callback));
	}

	public function bindCallbackAction (Request $request, $type)
	{
		$this->checkBindsName($type);
		$user = $this->getCurrentUser();
		if (empty($user)) {
			return $this->redirect($this->generateUrl('login'));
		}

		$bind = $this->getUserService()->getUserBindByTypeAndUserId($type, $user->id);
		if (! empty($bind)) {
			$this->setFlashMessage('danger', '您已经绑定了该第三方网站的帐号，不能重复绑定!');
			goto response;
		}

		$code = $request->query->get('code');
		if (empty($code)) {
			$this->setFlashMessage('danger', '您取消了授权/授权失败，请重试绑定!');
			goto response;
		}

		$callbackUrl = $this->generateUrl('settings_binds_bind_callback', array('type' => $type), true);
		try {
			$token = $this->createOAuthClient($type)->getAccessToken($code, $callbackUrl);
		} catch (\Exception $e) {
			$this->setFlashMessage('danger', '授权失败，请重试绑定!');
			goto response;
		}

		$bind = $this->getUserService()->getUserBindByTypeAndFromId($type, $token['userId']);
		if (!empty($bind)) {
			$this->setFlashMessage('danger', '该第三方帐号已经被其他帐号绑定，不能重复绑定!');
			goto response;
		}

		$this->getUserService()->bindUser($type, $token['userId'], $user['id'], $token);
		$this->setFlashMessage('success', '帐号绑定成功!');

		response:
		return $this->redirect($this->generateUrl('settings_binds'));

	}

	public function setupAction(Request $request)
	{
		$user = $this->getCurrentUser();

		if ($request->getMethod() == 'POST') {
			$data = $request->request->all();
                        
                        #验证 edit fubaosheng 2015-08-13
                        if(empty($user['id']) || empty($data['email']) || empty($data['nickname']))
                            return $this->createJsonResponse(false);
                        
			$this->getAuthService()->changeEmail($user['id'], null, $data['email']);
			$this->getAuthService()->changeNickname($user['id'], $data['nickname']);
			$user = $this->getUserService()->setupAccount($user['id']);
//			$this->authenticateUser($user);
			return $this->createJsonResponse(true);
		}

		return $this->render('Settings:setup');
	}

	public function setupCheckNicknameAction(Request $request)
	{
		$user = $this->getCurrentUser();

		$nickname = $request->query->get('value');

		if ($nickname == $user['nickname']) {
			$response = array('success' => true);
		} else {
			list($result, $message) = $this->getAuthService()->checkUsername($nickname);
			if ($result == 'success') {
				$response = array('success' => true);
			} else {
				$response = array('success' => false, 'message' => $message);
			}
		}

		return $this->createJsonResponse($response);
	}
        
        /*
         * @author 褚兆前 2016-04-07
         * 高校云互联  根据状态获取应用列表
         */
        public function getOpenUserListAction($state=''){
            $isCenter = WebCode::isLocalCenterWeb();
            if($isCenter){
                E('中心站不能申请高校云应用');
            }
            $currentUser = $this->getCurrentUser();
            $openUserIsOk = $this->getOpenUserService()->openUserIsOk();
            if(!$openUserIsOk) E('未开启');
            if(empty($currentUser->id))E('没有登录');
            $stateInList = $this->getOpenUserService()->getOpenUserstate($state);
            $userId = $currentUser->id;
            //$openUserList = $this->getOpenUserService()->getUserOpenUserList($userId,$stateInList);
            $paginator = new Paginator(
            $this->get('request'),
            $this->getOpenUserService()->getUserOpenUserCount($userId,$stateInList),
            12
            );
            
            $openUserList = $this->getOpenUserService()->getUserOpenUserPage(
                $userId,
                $stateInList,
                $paginator->getOffsetCount(),
                $paginator->getPerPageCount()
            );
            
            return $this->render('Settings:open-user-manager',array(
                'state'=>$state,
                'paginator' => $paginator,
                'resouces'=> $openUserList,
                'side_nav'=>'openUser',
            ));
        }
        
        /*
         * @author 褚兆前
         * 调用个人中心 高校云互联的菜单
         */
        public function getOpenUserNavAction(){
            $isCenter = WebCode::isLocalCenterWeb();
            if($isCenter){
               return '';
            }
            
            if($this->getOpenUserService()->openUserIsOk()){
                return $this->render('Settings:get-open-user-nav#User');
            }
            
            return '';
        }
        
        /*
         * @author 褚兆前
         * 增加 高校云互联 应用
         */
        public function addOpenUserAction(){
            $_codeArr = array(
                1000 => '申请成功',
                1001 => '申请失败',
            );
            $openUserIsOk = $this->getOpenUserService()->openUserIsOk();
            if(!$openUserIsOk) E('未开启');
            $isCenter = WebCode::isLocalCenterWeb();
            if($isCenter){
                E('中心站不能申请高校云应用');
            }
            $currentUser = $this->getCurrentUser();
            $userId = $currentUser->id;
            if( !IS_POST )E('请求方式不正确');
            if(empty($currentUser->id)) E('没有登录');
            if(!filter_var(I('post.url'),FILTER_VALIDATE_URL)) E('非法链接');
            if(!filter_var(I('post.backUrl'),FILTER_VALIDATE_URL)) E('非法链接');
            
            $metaId = $this->getOpenUserService()->setMetaIdSession($userId);
            
            $resouces = $this->getOpenUserService()->addOpenUser( I('post.'),$userId,$metaId);
            
            if(empty($resouces))$this->ajaxReturn(array('code'=>1001,'messge'=>$_codeArr[1001]));
            session('metaId',null);
            $this->getLogService()->info('Settings', 'addOpenUserAction', '添加了高校云应用');
            $this->ajaxReturn(array('code'=>1000,'messge'=>$_codeArr[1000]));
        }
        
        public function editOpenUserAction(){
            
            $openUserIsOk = $this->getOpenUserService()->openUserIsOk();
            if(!$openUserIsOk) E('未开启');
            $_codeArr = array(
                1000 => '修改成功',
                1001 => '修改失败',
            );
            
            $currentUser = $this->getCurrentUser();
            $userId = $currentUser->id;
            if( !IS_POST )E('请求方式不正确');
            if(empty($currentUser->id)) E('没有登录');
            if(!I('post.id')) E('应用id错误');
            if(!filter_var(I('post.url'),FILTER_VALIDATE_URL)) E('非法链接');
            if(!filter_var(I('post.backUrl'),FILTER_VALIDATE_URL)) E('非法链接');
            $isCenter = webCode::isLocalcenterWeb();
            $opType = 0;
            $opUserId = 0;
            $type='';
            
            if(!empty(goBackEnd()) && $isCenter){
                $type = in_array(I('post.modaType'), array('success','stop','default')) ? I('post.modaType') : '';
                
                $opUserId = $userId;
                $userId = '';
                $admin = 1;
                $opType = empty(I('opst.state')) ? 0: I('opst.state');
            }
            
            $resouces = $this->getOpenUserService()->editOpenUser( I('post.'),$userId,$type,$opUserId);
            
            if(empty($resouces))$this->ajaxReturn(array('code'=>1001,'messge'=>$_codeArr[1001]));
            $this->getLogService()->info('Settings', 'editOpenUserAction', '编辑了高校云ID为'.I('post.id').'的应用');
            $this->ajaxReturn(array('code'=>1000,'messge'=>$_codeArr[1000]));
            
        }
        
        public function editOpenUserStateAction(){
            
            $_codeArr = array(
                1000 => '修改成功',
                1001 => '修改失败',
            );
            $openUserIsOk = $this->getOpenUserService()->openUserIsOk();
            if(!$openUserIsOk) E('未开启');
            $currentUser = $this->getCurrentUser();
            $userId = $currentUser->id;
            if( !IS_POST )E('请求方式不正确');
            if(empty($currentUser->id)) E('没有登录');
            if(!I('post.id')) E('应用id错误');
            
            $type = in_array(I('post.modaType'), array('delete','stop')) ? I('post.modaType') : '';
            
            $isCenter = webCode::isLocalcenterWeb();
            $opType = 0;
            $opUserId = 0;
            if(!empty(goBackEnd()) && $isCenter){
                $opUserId = $userId;
                $userId = '';
            }
            
            $resouces = $this->getOpenUserService()->editOpenUserState( I('post.'),$userId,$type,$opUserId);
            
            if(empty($resouces))$this->ajaxReturn(array('code'=>1001,'messge'=>$_codeArr[1001]));
            $this->getLogService()->info('Settings', 'editOpenUserAction', '编辑了高校云ID为'.I('post.id').'的应用状态');
            $this->ajaxReturn(array('code'=>1000,'messge'=>$_codeArr[1000]));
            
        }
        
        /*
         * @author 褚兆前
         * 高校云互联的弹窗
         */
        public function openUserModalAction($id='',$modalType=""){
            
            $openUserIsOk = $this->getOpenUserService()->openUserIsOk();
            if(!$openUserIsOk) E('未开启');
            $admin = 0;
            $currentUser = $this->getCurrentUser();
            if(empty($currentUser->id)){
                E('没有登录');
            }
            $userId = $currentUser->id;
            $isCenter = webCode::isLocalcenterWeb();
            if(!empty(goBackEnd()) && $isCenter){
                $userId = '';
                $admin = 1;
            }
            if($modalType == 'delete'){
                return $this->render('Settings:open-user-othor-modal',array('id'=>$id,'modaType'=>'delete','admin' => $admin));
            }
            
            if($modalType == 'stop'){
                return $this->render('Settings:open-user-othor-modal',array('id'=>$id,'modaType'=>'stop','admin' => $admin));
            }
            
            if(!$id){
                $metaId = $this->getOpenUserService()->setMetaIdSession($userId);
            }
            if($id){
                
                $item = $this->getOpenUserService()->getOpenUserById($userId,$id);
                $metaId  = $item['metaId'];
            }
            
            $type = $id ? 'edit' : '';
            return $this->render('Settings:open-user-modal',array(
                'type'=>$type, 
                'id'=>$id,
                'openUser'=> $openUser,
                'item'  => $item,
                'metaId'=>$metaId,
                'admin' => $admin,
            ));
        }
        
        /*
         * @author 褚兆前
         * 检测应用 url 是否添加高校云的 meta
         */
        public function checkAddUrlMetaAction($url,$id=''){
            $openUserIsOk = $this->getOpenUserService()->openUserIsOk();
            if(!$openUserIsOk) E('未开启该功能');
            $_codeArr = array(
                1000 => '检测成功',
                1001 => '检测失败',
                1002 => '检测超时'
            );
            if(!filter_var($url,FILTER_VALIDATE_URL)) E('非法链接');
            
            $currentUser = $this->getCurrentUser();
            $userId = $currentUser->id;
            if(empty($currentUser->id)) E('没有登录');
            
            if(!$id){
                $metaId = $this->getOpenUserService()->setMetaIdSession($userId);
            }
            
            if($id){
                $isCenter = webCode::isLocalcenterWeb();
                if(!empty(goBackEnd()) && $isCenter){
                    $userId = '';
                    $admin = 1;
                }
                $item = $this->getOpenUserService()->getOpenUserById($userId,$id);
                $metaId  = $item['metaId'];
            }
            
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10); 
            curl_setopt($ch, CURLOPT_TIMEOUT,20000);
            $urlContent = curl_exec($ch);
            $curl_errno = curl_errno($ch);  
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            if($curl_errno >0) $this->ajaxReturn(array('code'=>1002,'messge'=>$_codeArr[1002]));
            
            $meta = '<meta property="openUser:admins" content="'.$metaId.'" />';
            if(strpos($urlContent, $meta) !== false){
                $this->ajaxReturn(array('code'=>1000,'messge'=>$_codeArr[1000]));
            }
            $this->ajaxReturn(array('code'=>1001,'messge'=>$_codeArr[1001]));
        }
        
        public function openUserDocumentAction(){
            $url = C('SITE_URL');
            return $this->render('Settings:open-user-document-modal',array('siteUrl'=>$url));
        }
        
	private function checkBindsName($type) 
	{   
		$types = array_keys(OAuthClientFactory::clients());
		if (!in_array($type, $types)) {
			throw new NotFoundHttpException();
		}
	}

	private function getFileService()
	{
		return createService('Content.FileServiceModel');
	}

	public function fetchAvatar($url)
	{

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);

		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 0);

		curl_setopt($curl, CURLOPT_URL, $url );

		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

		$response = curl_exec($curl);

		curl_close($curl);

		return $response;
	}

	private function createOAuthClient($type)
	{
		$settings = $this->setting('login_bind');        

		if (empty($settings)) {
			throw new \RuntimeException('第三方登录系统参数尚未配置，请先配置。');
		}

		if (empty($settings) or !isset($settings[$type.'_enabled']) or empty($settings[$type.'_key']) or empty($settings[$type.'_secret'])) {
			throw new \RuntimeException("第三方登录({$type})系统参数尚未配置，请先配置。");
		}

		if (!$settings[$type.'_enabled']) {
			throw new \RuntimeException("第三方登录({$type})未开启");
		}

		$config = array('key' => $settings[$type.'_key'], 'secret' => $settings[$type.'_secret']);
		$client = OAuthClientFactory::create($type, $config);

		return $client;
	}

    protected function getTeacherInfoService()
    {
        return createService('User.TeacherInfoService');
    }

	private function getAuthService()
	{
		return createService('User.AuthServiceModel');
	}

	protected function getSettingService()
	{
		return createService('System.SettingServiceModel');
	}

	protected function getUserFieldService()
	{
		return createService('User.UserFieldServiceModel');
	}

        protected function getEduCloudService()
        {
            return createService('EduCloud.EduCloudServiceModel');
        }	

        protected function getMobileCodeService()
        {
            return createService('Service.MobileCodeServiceModel');
        }
        
        protected function getUserService()
        {
            return createService('User.UserServiceModel');
        }
        
        private function getGroupService()
        {
            return createService('Group.GroupServiceModel');
        }

	private function getStudNumOfAuditService()
	{
		return createService('Group.StudNumOfAuditService');
	}
        
        private function getOpenUserService(){
            return createService('System.OpenUserService');
        }
        
}