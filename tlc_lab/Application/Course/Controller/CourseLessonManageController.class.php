<?php

namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Topxia\Service\Util\CloudClientFactory;
use Common\Model\Util\LiveClientFactory;
use Common\Lib\Paginator;

class CourseLessonManageController extends \Home\Controller\BaseController {

    public function indexAction(Request $request, $id,$create) {
        $create = $create ? : 0;
        $courseSerObj = $this->getCourseService();

        $course = $courseSerObj->tryManageCourse($id);
        $courseItems =$courseSerObj->getCourseItems($course['id']);

        $lessonIds = ArrayToolkit::column($courseItems, 'id');
        
        #判断章节是否有子元素
        $data = array('lesson'=>array(),'chapter'=>array());
        $firstItem = array('id'=>0,'type'=>'');
        $showItemIds = array();
        foreach ($courseItems as $value) {
            if($value["seq"] == 1){
                $firstItem["id"] = $value["id"];
                $firstItem["type"] = $value["itemType"];
            }
            if($value["itemType"] == "lesson"){
                if($value['chapterId'])
                    $data['lesson'][$value['id']] = $value['chapterId'];
            }else{
                if($value['parentId'])
                    $data['chapter'][$value['id']] = $value['parentId'];
            }
        }
        #取出seq为1的子元素
        if($firstItem["type"] == "chapter"){
            $showlessonIds = implode(",", array_keys(array_intersect($data["lesson"], array($firstItem["id"]))) );
            $showchapterIds = implode(",", array_keys(array_intersect($data["chapter"], array($firstItem["id"]))) );
            $showItemIds = explode(",", trim($showlessonIds.",".$showchapterIds,",")) ? : array();
        }
        foreach ($courseItems as $showItem) {
            if($showItem["itemType"] == "chapter"){
                if(in_array($showItem["parentId"], $showItemIds))
                    $showItemIds[] = $showItem["id"];
            }else{
                if(in_array($showItem["chapterId"], $showItemIds))
                    $showItemIds[] = $showItem["id"];
            }
        }
        #seq为1的子元素和pid/chapterId等于0显示，其余不显示
        foreach($courseItems as $itemKey => $itemVal){
            $courseItems[$itemKey]["child"] = array();
            if($itemVal["itemType"] == "chapter"){
                foreach ($data['lesson'] as $itemLessonId => $itemLessonPid) {
                    if($itemLessonPid == $itemVal["id"]){
                        $courseItems[$itemKey]["child"][] = $itemLessonId;
                    }
                }
                foreach ($data['chapter'] as $itemChapterId => $itemChapterPid) {
                    if($itemChapterPid == $itemVal["id"]){
                        $courseItems[$itemKey]["child"][] = $itemChapterId;
                    }
                }
                if( !$itemVal["parentId"] || in_array($itemVal["id"],$showItemIds) )
                    $courseItems[$itemKey]["show"] = 1;
                else
                    $courseItems[$itemKey]["show"] = 0;
            }
            if($itemVal["itemType"] == "lesson"){
                if( !$itemVal["chapterId"] || in_array($itemVal["id"],$showItemIds) )
                    $courseItems[$itemKey]["show"] = 1;
                else
                    $courseItems[$itemKey]["show"] = 0;
            }
        }

        $mediaMap = array();
        foreach ($courseItems as $item) {
            if ($item['itemType'] != 'lesson') {
                continue;
            }

            if (empty($item['mediaId'])) {
                continue;
            }

            if (empty($mediaMap[$item['mediaId']])) {
                $mediaMap[$item['mediaId']] = array();
            }
            $mediaMap[$item['mediaId']][] = $item['id'];
        }

        $mediaIds = array_keys($mediaMap);

        $files = $this->getUploadFileService()->findFilesByIds($mediaIds);

        foreach ($files as $file) {
            $lessonIds = $mediaMap[$file['id']];
            foreach ($lessonIds as $lessonId) {
                $courseItems["lesson-{$lessonId}"]['mediaStatus'] = $file['convertStatus'];
            }
        }
        /*
         *  视频状态码
         *   <0	    已删除
         *   60/61	正常
         *   10-26	转码中
         *   51	    视频审核不通过
         *   其他值	未审核
         *
         */
        foreach ($courseItems as $k => $v) {
            if (!empty($v['polyvVid'])) {
                $info = json_decode($v['polyvExtra'], true);
                if (!($info['status'] == 60 || $info['status'] == 61)) {
                    if ($info['status'] < 0) {
                        $mediaStatus = 'del';
                    } elseif ($info['status'] == 51) {
                        $mediaStatus = 'error';
                    } elseif ($info['status'] >= 10 && $info['status'] <= 26) {
                        $mediaStatus = 'doing';
                    } else {
                        $mediaStatus = 'doing';
                    }
                    $courseItems["lesson-{$v['id']}"]['mediaStatus'] = $mediaStatus;
                }
            }
        }
        $default = $this->getSettingService()->get('default', array());
        $courseSetting = $this->getSettingService()->get('course', array());
                
        return $this->render('CourseLessonManage:index', array(
                    'course' => $course,
                    'items' => $courseItems,
                    'files' => ArrayToolkit::index($files, 'id'),
                    'default' => $default,
                    'create' => $create,
                    'deploy' => intval($courseSetting["chapter_deploy_enabled"]),
        ));
    }

    public function viewDraftAction(Request $request) {
        $params = $request->query->all();

        $courseId = $params['courseId'];
        if (array_key_exists('lessonId', $params)) {
            $lessonId = $params['lessonId'];
        } else {
            $lessonId = 0;
        }

        $user = $this->getCurrentUser();
        $userId = $user['id'];
        $drafts = $this->getCourseService()->findCourseDraft($courseId, $lessonId, $userId);
        $listdrafts = array("title" => $drafts['title'], "summary" => $drafts['summary'], "content" => $drafts['content']);
        return $this->createJsonResponse($listdrafts);
    }

    public function draftCreateAction(Request $request) {
        $formData = $request->request->all();
        $user = $this->getCurrentUser();
        $userId = $user['id'];
        $courseId = $formData['courseId'];
        if (array_key_exists('lessonId', $formData)) {
            $lessonId = $formData['lessonId'];
        } else {
            $lessonId = 0;
            $formData['lessonId'] = 0;
        }

        $content = $formData['content'];

        $drafts = $this->getCourseService()->findCourseDraft($courseId, $lessonId, $userId);
        if ($drafts) {
            $draft = $this->getCourseService()->updateCourseDraft($courseId, $lessonId, $userId, $formData);
        } else {
            $draft = $this->getCourseService()->createCourseDraft($formData);
        }
        return $this->createJsonResponse(true);
    }

    // @todo refactor it.
    public function createAction(Request $request, $id) {

        $course = $this->getCourseService()->tryManageCourse($id);
        $parentId = $request->query->get('parentId');
        if ($request->getMethod() == 'POST') {
            $lesson = $request->request->all();

            $lesson['courseId'] = $course['id'];

            if ($lesson['media']) {
                $lesson['media'] = json_decode($lesson['media'], true);
            }

            if (is_numeric($lesson['second'])) {
                $lesson['length'] = $this->textToSeconds($lesson['minute'], $lesson['second']);
                unset($lesson['minute']);
                unset($lesson['second']);
            }

            if(!empty($lesson['chapter-id'])){
                $tmpArr = explode('-',$lesson['chapter-id']);
                if(count($tmpArr) == 2) {
                    $lesson['chapterId'] = intval($tmpArr[1]);
                }
            }
            unset($lesson['chapter-id']);

            $lesson = $this->getCourseService()->createLesson($lesson);

            $file = false;
            if ($lesson['mediaId'] > 0 && ($lesson['type'] != 'testpaper')) {

                $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
                if ($file['type'] == "document" && $file['convertStatus'] == "none") {

                    $convertHash = $this->getUploadFileService()->reconvertFile(
                            $file['id'], $this->generateUrl('uploadfile_cloud_convert_callback2', array(), true)
                    );
                }

                $lesson['mediaStatus'] = $file['convertStatus'];
            }

            $lessonId = 0;
            $this->getCourseService()->deleteCourseDrafts($id, $lessonId, $this->getCurrentUser()->id);
            $courseSetting = $this->getSettingService()->get('course', array());
            $lesson["show"] = 1;

            $chapter = $this->getCourseService()->getChapter($id,$lesson['chapterId']);
            $lesson['lessonLevel'] = $chapter?$chapter['type']:'chapter';
            return $this->render('CourseLessonManage:list-item', array(
                        'course' => $course,
                        'lesson' => $lesson,
                        'file' => $file,
                        'deploy' => intval($courseSetting["chapter_deploy_enabled"]),
            ));
        }

        $user = $this->getCurrentUser();
        $userId = $user['id'];
        $randString = substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 0, 12);
        $filePath = "courselesson/{$course['id']}";
        $fileKey = "{$filePath}/" . $randString;
        $convertKey = $randString;

        $targetType = 'courselesson';
        $targetId = $course['id'];
        $draft = $this->getCourseService()->findCourseDraft($targetId, 0, $userId);
        $setting = $this->setting('storage');

        $setting['upload_mode'] = 'local';

        $features = getParameter('enabled_features') ? getParameter('enabled_features') : array();

        return $this->render('CourseLessonManage:lesson-modal', array(
                    'course' => $course,
                    'targetType' => $targetType,
                    'targetId' => $targetId,
                    'filePath' => $filePath,
                    'fileKey' => $fileKey,
                    'convertKey' => $convertKey,
                    'storageSetting' => $setting,
                    'features' => $features,
                    'parentId' => $parentId,
                    'draft' => $draft,
        ));
    }


    public function createVideoAction(Request $request, $id){
        $course = $this->getCourseService()->tryManageCourse($id);
        $setting = $this->setting('storage');
        $setting['cloud_url'] = getScheme() == 'http' ?  'http://v.polyv.net:1080/files/' :'https://v.polyv.net:1081/files/';
        $setting['poly_catid'] = C('POLYV_DIR_ID');
        return $this->render('CourseLessonManage:lesson-modal-video', array(
            'course' => $course,
            'storageSetting' => $setting,
        ));
    }

    // @todo refactor it.
    public function editAction(Request $request, $courseId, $lessonId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $lesson = $this->getCourseService()->getCourseLesson($course['id'], $lessonId);
        if (empty($lesson)) {
            return $this->createMessageResponse('error',"课程内容(#{$lessonId})不存在！");
        }
        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();
            unset($lesson['chapter-id']);
            if ($fields['media']) {
                $fields['media'] = json_decode($fields['media'], true);
                if ($fields['media']['source'] == 'polyv') {
                    $fields['polyvExtra'] = json_encode($fields['media']);
                }
            }

            if ($fields['second']) {
                $fields['length'] = $this->textToSeconds($fields['minute'], $fields['second']);
                unset($fields['minute']);
                unset($fields['second']);
            }

            $lesson = $this->getCourseService()->updateLesson($course['id'], $lesson['id'], $fields);
            $this->getCourseService()->deleteCourseDrafts($course['id'], $lesson['id'], $this->getCurrentUser()->id);

            $file = false;
            if ($lesson['mediaId'] > 0 && ($lesson['type'] != 'testpaper')) {
                $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
                $lesson['mediaStatus'] = $file['convertStatus'];
                if ($file['type'] == "document" && $file['convertStatus'] == "none") {

                    $convertHash = $this->getUploadFileService()->reconvertFile(
                            $file['id'], $this->generateUrl('uploadfile_cloud_convert_callback2', array(), true)
                    );
                }
            }

            $courseSetting = $this->getSettingService()->get('course', array());
            $lesson["show"] = 1;
            return $this->render('CourseLessonManage:list-item', array(
                        'course' => $course,
                        'lesson' => $lesson,
                        'file' => $file,
                        'deploy' => intval($courseSetting["chapter_deploy_enabled"]),
            ));
        }

        $file = null;

        if ($lesson['mediaId']) {
            $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
            if (!empty($file)) {
                $lesson['media'] = array(
                    'id' => $file['id'],
                    'status' => $file['convertStatus'],
                    'source' => 'self',
                    'name' => $file['filename'],
                    'uri' => '',
                );
            } else {
                $lesson['media'] = array('id' => 0, 'status' => 'none', 'source' => '', 'name' => '文件已删除', 'uri' => '');
            }
        } else {
            $lesson['media'] = array(
                'id' => 0,
                'status' => 'none',
                'source' => $lesson['mediaSource'],
                'name' => $lesson['mediaName'],
                'uri' => $lesson['mediaUri'],
            );
        }

        if($lesson['mediaSource']=='polyv' && !empty($lesson['polyvExtra'])){
            $lesson['media'] =  json_decode($lesson['polyvExtra']);
        }
        
        list($lesson['minute'], $lesson['second']) = $this->secondsToText($lesson['length']);

        $user = $this->getCurrentUser();
        $userId = $user['id'];
        $randString = substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 0, 12);
        $filePath = "courselesson/{$course['id']}";
        $fileKey = "{$filePath}/" . $randString;
        $convertKey = $randString;

        $targetType = 'courselesson';
        $targetId = $course['id'];
        $draft = $this->getCourseService()->findCourseDraft($courseId, $lessonId, $userId);
        $setting['upload_mode'] = 'local';
        $lesson['title'] = str_replace(array('"', "'"), array('&#34;', '&#39;'), $lesson['title']);

        $features = getParameter('enabled_features') ? getParameter('enabled_features') : array();

        return $this->render('CourseLessonManage:lesson-modal', array(
                    'course' => $course,
                    'lesson' => $lesson,
                    'file' => $file,
                    'targetType' => $targetType,
                    'targetId' => $targetId,
                    'filePath' => $filePath,
                    'fileKey' => $fileKey,
                    'convertKey' => $convertKey,
                    'storageSetting' => $setting,
                    'features' => $features,
                    'draft' => $draft
        ));
    }

    public function publishAction(Request $request, $courseId, $lessonId) {
        $this->getCourseService()->publishLesson($courseId, $lessonId);
        $course = $this->getCourseService()->getCourse($courseId);
        $lesson = $this->getCourseService()->getCourseLesson($courseId, $lessonId);

        $file = false;
        if ($lesson['mediaId'] > 0 && ($lesson['type'] != 'testpaper')) {
            $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
            $lesson['mediaStatus'] = $file['convertStatus'];
            
        }

        //将课程内容同步到资料中
        if(!empty($lesson['mediaId'])){
            $resource = $this->getCourseResourceService()->findResource(array(
                'courseId'  =>  $courseId,
                'uploadFileId'  => $lesson['mediaId']
            ));

            if(empty($resource)) {
                $this->getUploadFileService()->saveResourceToDb($lesson['mediaId'], $lesson['title']);
            }
        }

        return $this->render('CourseLessonManage:list-item', array(
                    'course' => $course,
                    'lesson' => $lesson,
                    'file' => $file
        ));
    }

    public function unpublishAction(Request $request, $courseId, $lessonId) {
        $this->getCourseService()->unpublishLesson($courseId, $lessonId);

        $course = $this->getCourseService()->getCourse($courseId);
        $lesson = $this->getCourseService()->getCourseLesson($courseId, $lessonId);
        $file = false;
        if ($lesson['mediaId'] > 0 && ($lesson['type'] != 'testpaper')) {
            $file = $this->getUploadFileService()->getFile($lesson['mediaId']);
            $lesson['mediaStatus'] = $file['convertStatus'];
        }

        //将与课程内容一致的课程资料删除
        if(!empty($lesson['mediaId'])){
            $resource = $this->getCourseResourceService()->findResource(array(
                'courseId'  =>  $courseId,
                'uploadFileId'  => $lesson['mediaId']
            ));

            if(!empty($resource)) {
                $this->getUploadFileService()->deleteResourceFromDb($resource['id']);
            }
        }

        return $this->render('CourseLessonManage:list-item', array(
                    'course' => $course,
                    'lesson' => $lesson,
                    'file' => $file
        ));
    }

    public function sortAction(Request $request, $id) {
        $ids = $request->request->get('ids');
        if (!empty($ids)) {
//            $course = $this->getCourseService()->tryManageCourse($id);
//            $this->getCourseService()->sortCourseItems($course['id'], $request->request->get('ids'));
        }
        $data = array();
        $courseItems = $this->getCourseService()->getCourseItems($id);
        foreach ($courseItems as $key => $value) {
            if($value["itemType"] == "lesson"){
                $data[$key] = $value["chapterId"];
            }else{
                $data[$key] = $value["parentId"];
            }
        }
        $courseSetting = $this->getSettingService()->get('course', array());
        $info = intval($courseSetting["chapter_deploy_enabled"]) ? 0 : 1;  //modify @author Czq 默认开启取反值
        $this->ajaxReturn(array('data'=>$data,'info'=>$info,'status'=>1,));
    }

    public function deleteAction(Request $request, $courseId, $lessonId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $lesson = $this->getCourseService()->getCourseLesson($courseId, $lessonId);

        $this->getCourseService()->deleteLesson($course['id'], $lessonId);
//        $this->getCourseMaterialService()->deleteMaterialsByLessonId($lessonId);

        return $this->createJsonResponse(true);
    }

    private function secondsToText($value) {
        $minutes = intval($value / 60);
        $seconds = $value - $minutes * 60;
        return array($minutes, $seconds);
    }

    /**
     * 保利威视回调接口
     */
    public function polyvCallbackAction() {
        $sign = I('sign'); //系统签名
        $type = I('type'); //回调类型 upload:已上传 encode:已编码 pass:通过  nopass:未通过 del:删除
        $vid = I('vid'); //视频id
        $format = I('format'); //编码后的视频格式
        $df = I('df'); //视频清淅度版本，1为流畅、2为高清、3为超清
        $setting = $this->setting('storage');
        $secret_key = $setting['secret_key'];
        $logger = createService('System.LogService');
        $notify = createService('User.NotificationService');

        if ($type == 'encode') {//转码完成回调
            $isign = md5($type . $format . $vid . $df . $secret_key);
            if ($sign !== $isign) {
                $logger->error('CourseLessonManage', 'polyvCallback_error', '签名验证失败');
                return false;
            }
            $count = M('course_lesson')->where(array('polyvVid' => $vid))->count();
            if ($count <= 0) {
                $logger->error('CourseLessonManage', 'polyvCallback_error', '不存在的Vid');
                return false;
            }
            $lessons = M('course_lesson')->where(array('polyvVid' => $vid))->select();
            if (is_array($lessons)) {
                foreach ($lessons as $k => $v) {
                    $info = json_decode($v['polyvExtra'], true);
                    $info['status'] = 71;
                    $info = json_encode($info);
                    M('course_lesson')->where(array('id' => $v['id']))->save(array('polyvExtra' => $info));
                }
            }
        } elseif ($type == 'pass' || $type == 'nopass' || $type == 'del') {//审核操作回调
            $isign = md5('manage' . $type . $vid . $secret_key);
            if ($sign !== $isign) {
                $logger->error('CourseLessonManage', 'polyvCallback_error', '签名验证失败');
                return false;
            }
            $count = M('course_lesson')->where(array('polyvVid' => $vid))->count();
            if ($count <= 0) {
                $logger->error('CourseLessonManage', 'polyvCallback_error', '不存在的Vid');
                return false;
            }
            $lessons = M('course_lesson')->where(array('polyvVid' => $vid))->select();
            if (is_array($lessons)) {
                foreach ($lessons as $k => $v) {
                    $info = json_decode($v['polyvExtra'], true);
                    switch ($type) {
                        case 'pass':
                            $info['status'] = 60;
                            $info = json_encode($info);
                            M('course_lesson')->where(array('id' => $v['id']))->save(array('polyvExtra' => $info));
                            //通知用户
                            $notify->notify($v['userId'], '', '您上传的课程视频:【' . $v['title'] . '】转码成功');
                            //记录系统日志
                            $logger->info('CourseLessonManage', 'polyvCallback_success', '用户ID:' . $v['userId'] . ' 发布的课程视频:【' . $v['title'] . '】转码成功');
                            break;
                        case 'nopass':
                            $info['status'] = 51;
                            $info = json_encode($info);
                            M('course_lesson')->where(array('id' => $v['id']))->save(array('polyvExtra' => $info));
                            $notify->notify($v['userId'], '', '您上传的课程视频:【' . $v['title'] . '】审核失败');
                            $logger->info('CourseLessonManage', 'polyvCallback_error', '用户ID:' . $v['userId'] . ' 发布的课程视频:【' . $v['title'] . '】审核失败');
                            break;
                        case 'del':
                            $info['status'] = -1;
                            $info = json_encode($info);
                            M('course_lesson')->where(array('id' => $v['id']))->save(array('polyvExtra' => $info));
                            $notify->notify($v['userId'], '', '您上传的课程视频:【' . $v['title'] . '】审核失败');
                            $logger->info('CourseLessonManage', 'polyvCallback_error', '用户ID:' . $v['userId'] . ' 发布的课程视频:【' . $v['title'] . '】审核失败');
                        default:
                    }
                }
            }
        }
    }

    private function textToSeconds($minutes, $seconds) {
        return intval($minutes) * 60 + intval($seconds);
    }

    private function getCourseService() {
        return createService('Course.CourseService');
    }

    private function getAppService() {
        return createService('CloudPlatform.AppService');
    }

    private function getCourseMaterialService() {
        return createService('Course.MaterialService');
    }

    private function getDiskService() {
        return createService('User.DiskService');
    }

    private function getUploadFileService() {
        return createService('File.UploadFileService');
    }

    private function getQuestionService() {
        return createService('Question.QuestionService');
    }

    private function getSettingService() {
        return createService('System.SettingService');
    }

    protected function getClassroomService() {
        return createService('Classroom.ClassroomService');
    }

    private function getCourseResourceService()
    {
        return createService('Course.CourseResourceService');
    }
    
     private function getCategoryService()
    {
        return createService('Taxonomy.CategoryService');
    }

}
