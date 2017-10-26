<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Common\Lib\FileToolkit;
use Common\Model\Util\CloudClientFactory;

class CourseLessonController extends \Home\Controller\BaseController
{
  
    /**
     * @author ZhaoZuoWu 2015-03-31
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $courseId
     * @param type $lessonId
     * @return type
     * @throws type
     */
    public function previewAction(Request $request, $courseId, $lessonId)
    {
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->getCourse($courseId);
        $lesson = $courseSerObj->getCourseLesson($courseId, $lessonId);
        $user = $this->getCurrentUser();

        if (!$user->isLogin()) {
            $this->jsonResponse(403, 'Unlogin');
        }
        if (empty($lesson)) {
            #by LiangFuJian 2015-08-07
            $this->jsonResponse(404, 'lesson not found');
        }

        if (!empty($course['status']) && $course['status'] == 'closed') {
            return $this->render('CourseLesson:preview-notice-modal', array('course' => $course));
        }
        //@author 褚兆前
        $hasGoldBuy = $this->getCourceLessonLearnServiceModel()->checkUserGoldBuyThisLesson($user->id,$lesson['courseId'],$lessonId);
        
        $courseObj = createService('Course.CourseService');
        $member = $user ? $courseObj->getCourseMember($course['id'], $user->id) : null;
        $userIsAdd = false;
        if ($member && empty($member['locked'])) {
            $userIsAdd = true;
        }

        $allowAnonymousPreview = $this->setting('course.allowAnonymousPreview', 1);
        if (empty($allowAnonymousPreview) && !$user->isLogin()) {
            throw $this->createAccessDeniedException();
        }

        if ($lesson['type'] == 'video' and $lesson['mediaSource'] == 'self') {
            $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
            if (!empty($file['metas2']) && !empty($file['metas2']['sd']['key'])) {
                $factory = new CloudClientFactory();
                $client = $factory->createClient();
                $hls = $client->generateHLSQualitiyListUrl($file['metas2'], 3600);

                if (isset($file['convertParams']['convertor']) && ($file['convertParams']['convertor'] == 'HLSEncryptedVideo')) {

                    $token = $this->getTokenService()->makeToken('hls.playlist', array('data' => $file['id'], 'times' => 3, 'duration' => 3600));
                    $hls = array(
                        'url' => $this->generateUrl('hls_playlist', array(
                            'id' => $file['id'], 
                            'token' => $token['token'],
                            'line' => $request->query->get('line')
                        ), true)
                    );

                } else {
                    $hls = $client->generateHLSQualitiyListUrl($file['metas2'], 3600);
                }

            }

        } else if ($lesson['mediaSource'] == 'youku') {
            $matched = preg_match('/\/sid\/(.*?)\/v\.swf/s', $lesson['mediaUri'], $matches);
            if ($matched) {
                $lesson['mediaUri'] = "http://player.youku.com/embed/{$matches[1]}";
                $lesson['mediaSource'] = 'iframe';
            } else {
                $lesson['mediaUri'] = $lesson['mediaUri'];
            }
        } else if ($lesson['mediaSource'] == 'tudou'){
            $matched = preg_match('/\/v\/(.*?)\/v\.swf/s', $lesson['mediaUri'], $matches);
            if ($matched) {
                $lesson['mediaUri'] = "http://www.tudou.com/programs/view/html5embed.action?code={$matches[1]}";
                $lesson['mediaSource'] = 'iframe';
            } else {
                $lesson['mediaUri'] = $lesson['mediaUri'];
            }
        }
        
        return $this->render('CourseLesson:preview-modal', array(
            'user' => $user,
            'course' => $course,
            'lesson' => $lesson,
            'userIsAdd' =>$userIsAdd,
            'hasGoldBuy'=>$hasGoldBuy,
            'hlsUrl' => (isset($hls) and is_array($hls) and !empty($hls['url'])) ? $hls['url'] : '',
        ));
    }
    
    /**
     * @author ZhaoZuoWu 2015-03-31
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $courseId
     * @param type $lessonId
     * @return type
     */
    public function showAction(Request $request, $courseId, $lessonId)
    {
        $courseSerObj = $this->getCourseService();
        list($course, $member) = $courseSerObj->tryTakeCourse($courseId);
        $lesson = $courseSerObj->getCourseLesson($courseId, $lessonId);
        $json = array();
        $json['number'] = $lesson['number'];

        $chapter = empty($lesson['chapterId']) ? null : $courseSerObj->getChapter($course['id'], $lesson['chapterId']);
        if ($chapter['type'] == 'unit') {
            $unit = $chapter;
            $json['unitNumber'] = $unit['number'];
            
            $chapter = $courseSerObj->getChapter($course['id'], $unit['parentId']);
            $json['chapterNumber'] = empty($chapter) ? 0 : $chapter['number'];

        } else {
            $json['chapterNumber'] = empty($chapter) ? 0 : $chapter['number'];
            $json['unitNumber'] = 0;
        }

        $json['title'] = $lesson['title'];
        $json['summary'] = $lesson['summary'];
        $json['type'] = $lesson['type'];
        $json['content'] = $lesson['content'];
        $json['status'] = $lesson['status'];
        $json['quizNum'] = $lesson['quizNum'];
        $json['materialNum'] = $lesson['materialNum'];
        $json['mediaId'] = $lesson['mediaId'];
        $json['mediaSource'] = $lesson['mediaSource'];
        $json['polyvVid'] = $lesson['polyvVid'];
        $json['startTimeFormat'] = date("m-d H:i",$lesson['startTime']);
        $json['endTimeFormat'] = date("H:i",$lesson['endTime']);
        $json['startTime'] = $lesson['startTime'];
        $json['endTime'] = $lesson['endTime'];
        $json['id'] = $lesson['id'];
        $json['courseId'] = $lesson['courseId'];
        $json['videoWatermarkEmbedded'] = 0;

        $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
        $json['mimeType'] = $file ? FileToolkit::getMimeTypeByExtension($file['ext']) : "";

        $currentUser = $this->getCurrentUser();
        if($currentUser->isLogin() && $currentUser->id == $lesson['userId']){
            $json['previlege'] = 1;
        }

        $app = $this->getAppService()->findInstallApp('Homework');
        if(!empty($app)){
            $homework = $this->getHomeworkService()->getHomeworkByLessonId($lesson['id']);
            $exercise = $this->getExerciseService()->getExerciseByLessonId($lesson['id']);
            $json['homeworkOrExerciseNum'] = $homework['itemCount'] + $exercise['itemCount'];
        }else{ 
            $json['homeworkOrExerciseNum'] = 0;
        }

        $json['isTeacher'] = $courseSerObj->isCourseTeacher($courseId, $this->getCurrentUser()->id);
        if($lesson['type'] == 'live' && $lesson['replayStatus'] == 'generated') {
            $json['replays'] = $courseSerObj->getCourseLessonReplayByLessonId($lesson['id']);
            if(!empty($json['replays'])) {
                foreach ($json['replays'] as $key => $value) {
                    $url = $this->generateUrl('live_course_lesson_replay_entry', array(
                        'courseId' => $lesson['courseId'], 
                        'lessonId' => $lesson['id'], 
                        'courseLessonReplayId' => $value['id']
                    ), true);
                    $json['replays'][$key]["url"] = $url;
                }
            }
        }

        if ($json['mediaSource'] == 'self' || $json['mediaSource'] == 'cloud') {
            $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
            if (!empty($file)) {
                //网盘导入的文件
                $json['mediaUri'] = $this->generateUrl('course_lesson_media', array('courseId'=>$course['id'], 'lessonId' => $lesson['id']));
            } else {
                $json['mediaUri'] = '';
                if ($lesson['type'] == 'video') {
                    $json['mediaError'] = '抱歉，视频文件不存在，暂时无法学习。';
                } else if ($lesson['type'] == 'audio') {
                    $json['mediaError'] = '抱歉，音频文件不存在，暂时无法学习。';
                } else if ($lesson['type'] == 'ppt') {
                    $json['mediaError'] = '抱歉，PPT文件不存在，暂时无法学习。';
                }
            }
        } else if ($json['mediaSource'] == 'youku' && $this->isMobile()) {
            $matched = preg_match('/\/sid\/(.*?)\/v\.swf/s', $lesson['mediaUri'], $matches);
            if ($matched) {
                $json['mediaUri'] = "http://player.youku.com/embed/{$matches[1]}";
                $json['mediaSource'] = 'iframe';
            } else {
                $json['mediaUri'] = $lesson['mediaUri'];
            }

        } else if ($json['mediaSource'] == 'tudou'){
            $matched = preg_match('/\/v\/(.*?)\/v\.swf/s', $lesson['mediaUri'], $matches);
            if ($matched) {
                $json['mediaUri'] = "http://www.tudou.com/programs/view/html5embed.action?code={$matches[1]}";
                $json['mediaSource'] = 'iframe';
            } else {
                $json['mediaUri'] = $lesson['mediaUri'];
            }
        } else {
            $json['mediaUri'] = $lesson['mediaUri'];
        }

        $json['canLearn'] = $courseSerObj->canLearnLesson($lesson['courseId'], $lesson['id']);

        return $this->createJsonResponse($json);
    }


	public function showTextAction(){

		$courseId = I('courseId',0);
		$lessonId = I('lessonId',0);

		$lesson = $this->getCourseService()->getCourseLesson($courseId, $lessonId);

		$this->render('CourseLesson:show-text',array(
			'lesson'=>$lesson,
		));

	}

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $courseId
     * @param type $lessonId
     * @return type
     * @throws type
     * @author ZhaoZuoWu 2015-03-31
     */
    public function mediaAction(Request $request, $courseId, $lessonId)
    {
        $lesson = $this->getCourseService()->getCourseLesson($courseId, $lessonId);  
        if (empty($lesson) || empty($lesson['mediaId'])) {
            throw $this->createNotFoundException();
        }

        return $this->fileAction($request, $lesson['mediaId']);
    }

    /**
     * 
     * @param type $courseId
     * @param type $lessonId
     * @return type
     * @author ZhaozuoWu 2015-04-01
     */
    public function detailDataAction(Request $request,$courseId,$lessonId)
    {   
        $students=array();
        $lesson = $this->getCourseService()->getCourseLesson($courseId,$lessonId);
        $count = $this->getCourseService()->searchLearnCount(array('courseId'=>$courseId,'lessonId'=>$lessonId));
        $paginator = new Paginator($this->get('request'), $count, 20);

        $learns = $this->getCourseService()->searchLearns(array('courseId'=>$courseId,'lessonId'=>$lessonId),array('startTime','ASC'), $paginator->getOffsetCount(),  $paginator->getPerPageCount());
        
        foreach ($learns as $key => $learn) {
            
            $user=$this->getUserService()->getUser($learn['userId']);
            $students[$key]['nickname']=$user['nickname'];
            $students[$key]['startTime']=$learn['startTime'];
            $students[$key]['finishedTime']=$learn['finishedTime'];
            $students[$key]['learnTime']=$learn['learnTime'];
            $students[$key]['watchTime']=$learn['watchTime'];
 
        }
        $lesson['length']=intval($lesson['length']/60);
        
        return $this->render('CourseLesson:lesson-data-modal', array(
            'lesson'=>$lesson,
            'paginator'=>$paginator,
            'students'=>$students,
            ));
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $courseId
     * @param type $lessonId
     * @return type
     * @throws type
     * @author ZhaozuoWu 2015-04-01
     */
    public function mediaDownloadAction(Request $request, $courseId, $lessonId)
    {
        if (!$this->setting('course.student_download_media')) {
            return $this->createMessageResponse('未开启课程内容音视频下载。');
        }
        $lesson = $this->getCourseService()->getCourseLesson($courseId, $lessonId);  
        if (empty($lesson) || empty($lesson['mediaId']) || ($lesson['mediaSource'] != 'self') ) {
            throw $this->createNotFoundException();
        }

        $this->getCourseService()->tryTakeCourse($courseId);

        return $this->fileAction($request, $lesson['mediaId'], true);
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $courseId
     * @param type $lessonId
     * @return type
     * @throws type
     */
    public function pptAction(Request $request, $courseId, $lessonId)
    {
        $lesson = $this->getCourseService()->getCourseLesson($courseId, $lessonId);

        if (empty($lesson)) {
            throw $this->createNotFoundException();
        }

        if (!$lesson['free']) {
            $this->getCourseService()->tryTakeCourse($courseId);
        }

        if ($lesson['type'] != 'ppt' or empty($lesson['mediaId'])) {
            throw $this->createNotFoundException();
        }

        $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
        if (empty($file)) {
            throw $this->createNotFoundException();
        }

        if ($file['convertStatus'] != 'success') {
            if ($file['convertStatus'] == 'error') {
                $url = $this->generateUrl('course_manage_files', array('id' => $courseId));
                $message = sprintf('PPT文档转换失败，请到课程<a href="%s" target="_blank">文件管理</a>中，重新转换。', $url);
                return $this->createJsonResponse(array(
                    'error' => array('code' => 'error', 'message' => $message),
                ));
            } else {
                return $this->createJsonResponse(array(
                    'error' => array('code' => 'processing', 'message' => 'PPT文档还在转换中，还不能查看，请稍等。'),
                ));
            }
        }

        $factory = new CloudClientFactory();
        $client = $factory->createClient();

        $result = $client->pptImages($file['metas2']['imagePrefix'], $file['metas2']['length']. '');

        return $this->createJsonResponse($result);
    }

    public function documentAction(Request $request, $courseId, $lessonId)
    {
        $lesson = $this->getCourseService()->getCourseLesson($courseId, $lessonId);

        if (empty($lesson)) {
            throw $this->createNotFoundException();
        }

        if ($lesson['type'] != 'document' or empty($lesson['mediaId'])) {
            throw $this->createNotFoundException();
        }

        $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
        if (empty($file)) {
            throw $this->createNotFoundException();
        }

	    if ( !in_array($file['convertStatus'],array('success','none')) ) {
		    if ($file['convertStatus'] == 'error') {
			    $url = $this->generateUrl('course_manage_files', array('id' => $courseId));
			    $message = sprintf('文档转换失败，请到课程<a href="%s" target="_blank">文件管理</a>中，重新转换。', $url);
			    $this->createJsonResponse(array(
				    'error' => array('code' => 'error', 'message' => $message),
			    ));
		    } else {
			 $this->createJsonResponse(array(
				    'error' => array('code' => 'processing', 'message' => '文档还在转换中，还不能查看，请稍等。'),
			    ));
		    }
	    }

        $result['pdfUri'] = C('SITE_URL') . $file['webpath'];

        return $this->createJsonResponse($result);
    }

    //网盘文档文件获取
    public function documentCloudAction(Request $request, $id){
        $file = $this->getUploadFileService()->getFile($id);

        if(empty($file)){
//            createLocalMediaResponse2($request, $file, false);
            return $this->createMessageResponse("info","不存在该文件！");
        }else{
            createLocalMediaResponse($request, $file, false);
        }
    }

    public function fileAction(Request $request, $fileId, $isDownload = false)
    {
        $file = $this->getUploadFileService()->getFile($fileId);
        if (empty($file)) {
            throw $this->createNotFoundException();
        }

        return $this->createLocalMediaResponse($request, $file, $isDownload);
    }

    public function learnStatusAction(Request $request, $courseId, $lessonId)
    {
        $user = $this->getCurrentUser();
        $status = $this->getCourseService()->getUserLearnLessonStatus($user->id, $courseId, $lessonId);
        return $this->createJsonResponse(array('status' => $status ? : 'unstart'));
    }

    public function learnStartAction(Request $request, $courseId, $lessonId)
    {
        $result = $this->getCourseService()->startLearnLesson($courseId, $lessonId);
        return $this->createJsonResponse($result);
    }

    public function learnFinishAction(Request $request, $courseId, $lessonId)
    {
        $user = $this->getCurrentUser();

        if(!$user->isLogin()){
            return $this->createJsonResponse(array('error'=>'请先登录再操作'));
        }

        if($user->isTeacher()){
            return $this->createJsonResponse(array('error'=>'您是老师，不用此操作'));
        }

        $this->getCourseService()->finishLearnLesson($courseId, $lessonId);

        $member = $this->getCourseService()->getCourseMember($courseId, $user->id);

        $response = array(
            'learnedNum' => empty($member['learnedNum']) ? 0 : $member['learnedNum'],
            'isLearned' => empty($member['isLearned']) ? 0 : $member['isLearned'],
        );

        return $this->createJsonResponse($response);
    }

    public function learnCancelAction(Request $request, $courseId, $lessonId)
    {
        $user = $this->getCurrentUser();

        if(!$user->isLogin()){
            return $this->createJsonResponse(array('error'=>'请先登录再操作'));
        }

        if($user->isTeacher()){
            return $this->createJsonResponse(array('error'=>'您是老师，不用此操作'));
        }
        $this->getCourseService()->cancelLearnLesson($courseId, $lessonId);
        return $this->createJsonResponse(true);
    }

    //导入云课程——加载网盘文档文件
    public function listDocumentAction(Request $request){
        if($request->getMethod() == 'POST'){
            $data = $request->request->all();

            $directory = empty($data['dir']) ? "/" : $data['dir'];
            $currentUser = $this->getCurrentUser();
            if(!$currentUser->isLogin()){
                return $this->createJsonResponse(array(
                    'code'=>0,
                    'data'=>array(),
                    'message'=>"请登录后操作"
                ));
            }

            if(!empty($directory)){
                $fileList = $this->getCourseService()->loadFilesUnderDir("document",$directory);
                return $this->createJsonResponse(array(
                    'code'=>1,
                    'data'=>$fileList
                ));
            }else{
                return $this->createJsonResponse(array(
                    'code'=>0,
                    'data'=>array(),
                    'message'=>"已到根目录"
                ));
            }
        }else{
            return $this->createJsonResponse([]);
        }
    }

    //导入云课程——加载网盘视频文件
    public function listVideoAction(Request $request){
        if($request->getMethod() == 'POST'){
            $data = $request->request->all();

            $directory = empty($data['dir']) ? "/" : $data['dir'];
            $currentUser = $this->getCurrentUser();
            if(!$currentUser->isLogin()){
                return $this->createJsonResponse(array(
                    'code'=>0,
                    'data'=>array(),
                    'message'=>"请登录后操作"
                ));
            }

            if(!empty($directory)){
                $fileList = $this->getCourseService()->loadFilesUnderDir("video",$directory);
                return $this->createJsonResponse(array(
                    'code'=>1,
                    'data'=>$fileList
                ));
            }else{
                return $this->createJsonResponse(array(
                    'code'=>0,
                    'data'=>array(),
                    'message'=>"已到根目录"
                ));
            }
        }else{
            return $this->createJsonResponse([]);
        }
    }

    private function createLocalMediaResponse(Request $request, $file, $isDownload = false)
    {
        createLocalMediaResponse2($request, $file, $isDownload);
//        $response = BinaryFileResponse::create($file['fullpath'], 200, array(), false);
//        $response->trustXSendfileTypeHeader();
//
//        if($isDownload) {
//            $file['filename'] = urlencode($file['filename']);
//            if (preg_match("/MSIE/i", $request->headers->get('User-Agent'))) {
//                $response->headers->set('Content-Disposition', 'attachment; filename="'.$file['filename'].'"');
//            } else {
//                $response->headers->set('Content-Disposition', "attachment; filename*=UTF-8''".$file['filename']);
//            }
//        }
//
//        $mimeType = FileToolkit::getMimeTypeByExtension($file['ext']);
//        if ($mimeType) {
//            $response->headers->set('Content-Type', $mimeType);
//        }
//        return $response;
    }

    private function isMobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            //找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        //判断手机发送的客户端标志,兼容性有待提高
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp',
                    'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu',
                    'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi',
                    'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        //协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    private function getSettingService()
    {
        return createService('System.SettingServiceModel');
    }

    private function getCourseService()
    {
        return createService('Course.CourseServiceModel');
    }

    private function getDiskService()
    {
        return createService('User.DiskServiceModel');
    }

    private function getTokenService()
    {
        return createService('User.TokenServiceModel');
    }

    private function getFileService()
    {
        return createService('Content.FileServiceModel');
    }

    private function getUploadFileService()
    {
        return createService('File.UploadFileServiceModel');
    }

    private function getExerciseService()
    {
        return createService('Homework.ExerciseServiceModel');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppServiceModel');
    }
    
    protected function getCourceLessonLearnServiceModel()
    {
        return createService('Course.CourceLessonLearnServiceModel');
    }
}