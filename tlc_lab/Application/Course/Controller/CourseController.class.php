<?php

namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Common\Lib\Paginator;
use Common\Form\CourseType;
use Common\Lib\ArrayToolkit;
use Common\Model\Util\LiveClientFactory;
use Common\Model\Course\CourseService;
use Common\Lib\WebCode;
require_once 'Application/System/Common/Common/RequestBase.php';
use \System\Common\Common\QcloudApi_Common_Request_Base;

class CourseController extends \Home\Controller\BaseController
{

    public function exploreAction(Request $request, $category,$number)
    {

        $courseObj = $this->getCourseService();
        $categoryObj = $this->getCategoryService();
        if (!empty($category)) {
            if (ctype_digit((string) $category)) {
                $category = $categoryObj->getCategory($category);
            } else {
                $category = $categoryObj->getCategoryByCode($category);
            }
            if (empty($category)) {
                return $this->createMessageResponse('error', '非法类别');
            }
        } else {
            $category = array('id' => null);
        }

       // print_r($category);

        $settings  = $this->getSettingService()->get('course', array());

        if(!empty($settings['select_course_cate_sort'])){
            $settings  = explode(',',$settings['select_course_cate_sort']);
            foreach($settings as $k => $v){
                if($v == "recommendedSeq") $res  = '推荐';
                if($v == "latest")         $res  = '最新';
                if($v == "popular")        $res  = '热门';
                $data[$k]['sortkey'] = $v;
                $data[$k]['sortname'] = $res;
            }
        }

        if($settings[0]){
           $sort = $request->query->get('sort', $settings[0]);
        }else{
           $sort = $request->query->get('sort', 'latest');
        }


        $conditions = array(
            'status' => 'published',
            'type' => 'normal',
            'categoryId' => $category['id'],
            'recommended' => ($sort == 'recommendedSeq') ? 1 : null,
        );

        if($number){
            $conditions['number'] = $number;
        }

        $paginator = new Paginator(
            $this->get('request'),
            $courseObj->searchCourseCount($conditions)
            , 9
        );

        $courses = $courseObj->searchCourses(
            $conditions, $sort,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return $this->render('Course:explore', array(
            'courses' => $courses,  //总数据
            'category' => $category,
            'courseNumber' => $number,
            'sort' => $sort,
            'paginator' => $paginator,
//            'categories' => $categories,
            'select_course_cate_sort' => $data,
            'consultDisplay' => true
        ));
    }

    #by YangJinlong 2015-05-21
    private function decorateCategoryTree($categoryTree){
        static $idsArr = array();
        foreach($categoryTree as $k => $v){
            if($v['isLeafNode'] == 1 ){
                array_push($idsArr, $v['id']);
            }
            if(!empty($v['children'])){
                $this->decorateCategoryTree($v['children']);
            }
        }
        return $idsArr;
    }

    public function archiveAction(Request $request)
    {
        $conditions = array(
            'status' => 'published'
        );

        $paginator = new Paginator(
            $this->get('request'),
            $this->getCourseService()->searchCourseCount($conditions)
            , 30
        );

        $courses = $this->getCourseService()->searchCourses(
            $conditions, 'latest',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $userIds = array();
        foreach ($courses as &$course) {
            $course['tags'] = $this->getTagService()->findTagsByIds($course['tags']);
            $userIds = array_merge($userIds, $course['teacherIds']);
        }

        $users = $this->getUserService()->findUsersByIds($userIds);

        return $this->render('Course:archive', array(
            'courses' => $courses,
            'paginator' => $paginator,
            'users' => $users
        ));
    }


    public function archiveCourseAction(Request $request, $id)
    {
        $course = $this->getCourseService()->getCourse($id);
        $lessons = $this->getCourseService()->searchLessons(array('courseId' => $course['id'],'status' => 'published'), array('createdTime', 'ASC'), 0, 1000);
        $tags = $this->getTagService()->findTagsByIds($course['tags']);
        $category = $this->getCategoryService()->getCategory($course['categoryId']);

        return $this->render('Course:archiveCourse', array(
            'course' => $course,
            'lessons' => $lessons,
            'tags' => $tags,
            'category' => $category
        ));
    }

    public function archiveLessonAction(Request $request, $id, $lessonId)
    {

        $course = $this->getCourseService()->getCourse($id);

        $lessons = $this->getCourseService()->searchLessons(array('courseId' => $course['id'],'status' => 'published'), array('createdTime', 'ASC'), 0, 1000);

        $tags = $this->getTagService()->findTagsByIds($course['tags']);

        if ($lessonId == '' && $lessons != null ) {
            $currentLesson = $lessons[0];
        } else {
            $currentLesson = $this->getCourseService()->getCourseLesson($course['id'], $lessonId);
        }

        return $this->render('Course:archiveLesson', array(
            'course' => $course,
            'lessons' => $lessons,
            'currentLesson' => $currentLesson,
            'tags' => $tags
        ));
    }

    public function infoAction(Request $request, $id)
    {
        $course = $this->getCourseService()->getCourse($id);
        $category = $this->getCategoryService()->getCategory($course['categoryId']);
        $tags = $this->getTagService()->findTagsByIds($course['tags']);
        return $this->render('Course:info', array(
            'course' => $course,
            'category' => $category,
            'tags' => $tags,
        ));
    }

    public function teacherInfoAction(Request $request, $courseId, $id)
    {
        
        $currentUser = $this->getCurrentUser();
        #edit 未登录跳登录 fubaosheng 2015-06-01
        if(!$currentUser->isLogin())
            $this->redirect('User/Signin/index');

        $userSerObj = $this->getUserService();
        $course = $this->getCourseService()->getCourse($courseId);
        $user = $userSerObj->getUser($id);
        $profile = $userSerObj->getUserProfile($id);

        $isFollowing = $userSerObj->isFollowed($currentUser->id, $user['id']);

        return $this->render('Course:teacher-info-modal', array(
            'user' => $user,
            'profile' => $profile,
            'isFollowing' => $isFollowing,
        ));
    }

    /**
     * 如果用户已购买了此课程，或者用户是该课程的教师，则显示课程的Dashboard界面。
     * 如果用户未购买该课程，那么显示课程的营销界面。
     */
    public function showAction(Request $request, $id)
    {
        $previewAs = $request->query->get('previewAs');
        $type = $request->query->get('type')?:"index";

        $page = $request->query->get('page');

        if(!empty($page) && is_numeric($page)){
            $type = 'resource';
        }
        
        #是否显示虚拟实验室
        $isShowVirtualLab = isShowVirtualLab() ? 1: 0;

        $settingObj = $this->getSettingService();
        $courseObj = $this->getCourseService();
        $appSerObj = $this->getAppService();
        $uploadObj = $this->getUploadFileService();
        $categoryObj = $this->getCategoryService();
        $tagObj = $this->getTagService();

        $reviewObj = $this->getReviewService();
        #课程信息
        
        $course = $courseObj->getCourse($id);

        if (empty($course) || $course['isDeleted']) {
            return $this->createMessageResponse('error', "课程{$id}不存在");
        }
        
//        $teachers = $courseObj->findCourseTeachers($id);
//        doLog(json_encode($teachers));
//        $teacher = $courseObj->getCourseTeacher($id);

        $defaultSetting = $settingObj->get('default', array());
        if (isset($defaultSetting['courseShareContent'])){
            $courseShareContent = $defaultSetting['courseShareContent'];
        } else {
            $courseShareContent = "";
        }

        $valuesToBeReplace = array('{{course}}');
        $valuesToReplace = array($course['title']);
        $courseShareContent = str_replace($valuesToBeReplace, $valuesToReplace, $courseShareContent);

        $weeks = array("日","一","二","三","四","五","六");
        $currentTime = time();

        $user = $this->getCurrentUser();

        $items = $courseObj->getCourseItems($course['id'],'published');
               
        #判断章节是否有子元素
        $data = array('lesson'=>array(),'chapter'=>array());
        $firstItem = array('id'=>0,'type'=>'');
        $firstItemKey = array_keys($items)[0];
        $showItemIds = array();
        foreach ($items as $dataKey => $dataVal) {
            if($dataKey == $firstItemKey){
                $firstItem["id"] = $dataVal["id"];
                $firstItem["type"] = $dataVal["itemType"];
            }
            if($dataVal["itemType"] == "lesson"){
                if($dataVal['chapterId'])
                    $data['lesson'][$dataVal['id']] = $dataVal['chapterId'];
            }else{
                if($dataVal['parentId'])
                    $data['chapter'][$dataVal['id']] = $dataVal['parentId'];
            }
        }
        #取出seq为1的子元素
        if($firstItem["type"] == "chapter"){
            $showlessonIds = implode(",", array_keys(array_intersect($data["lesson"], array($firstItem["id"]))) );
            $showchapterIds = implode(",", array_keys(array_intersect($data["chapter"], array($firstItem["id"]))) );
            $showItemIds = explode(",", trim($showlessonIds.",".$showchapterIds,",")) ? : array();
        }
        foreach ($items as $showItem) {
            if($showItem["itemType"] == "chapter"){
                if(in_array($showItem["parentId"], $showItemIds))
                    $showItemIds[] = $showItem["id"];
            }else{
                if(in_array($showItem["chapterId"], $showItemIds))
                    $showItemIds[] = $showItem["id"];
            }
        }
        #seq为1的子元素和pid/chapterId等于0显示，其余不显示
        foreach($items as $itemKey => $itemVal){
            $items[$itemKey]["child"] = array();
            if($itemVal["itemType"] == "chapter"){
                foreach ($data['lesson'] as $itemLessonId => $itemLessonPid) {
                    if($itemLessonPid == $itemVal["id"]){
                        $items[$itemKey]["child"][] = $itemLessonId;
                    }
                }
                foreach ($data['chapter'] as $itemChapterId => $itemChapterPid) {
                    if($itemChapterPid == $itemVal["id"]){
                        $items[$itemKey]["child"][] = $itemChapterId;
                    }
                }
                if( !$itemVal["parentId"] || in_array($itemVal["id"],$showItemIds) )
                    $items[$itemKey]["show"] = 1;
                else
                    $items[$itemKey]["show"] = 0;
            }
            if($itemVal["itemType"] == "lesson"){
                if( !$itemVal["chapterId"] || in_array($itemVal["id"],$showItemIds) )
                    $items[$itemKey]["show"] = 1;
                else
                    $items[$itemKey]["show"] = 0;
            }
        }
        
        $mediaMap = array();
        foreach ($items as $item) {
            if (empty($item['mediaId'])) {
                continue;
            }

            if (empty($mediaMap[$item['mediaId']])) {
                $mediaMap[$item['mediaId']] = array();
            }
            $mediaMap[$item['mediaId']][] = $item['id'];
        }

        $mediaIds = array_keys($mediaMap);
        $files = $uploadObj->findFilesByIds($mediaIds);
        #edit 郭俊强 修改用户课程权限
        if($user->isLogin()){
             $isPower=$courseObj->getCourseMemberPower($course['id'], $user->id);
        }

        if($this->isPluginInstalled("Homework")) {
            $lessons = $courseObj->getCourseLessons($course['id']);
            $lessonIds = ArrayToolkit::column($lessons, 'id');
            $homeworks = $this->getHomeworkService()->findHomeworksByCourseIdAndLessonIds($course['id'], $lessonIds); //no exists
            $exercises = $this->getExerciseService()->findExercisesByLessonIds($lessonIds); //no exists
        }

        $groupedItems = $this->groupCourseItems($items);
        $hasFavorited = $courseObj->hasFavoritedCourse($course['id']);
        
        $category = $categoryObj->getCategory($course['categoryId']);
        $tags = $tagObj->findTagsByIds($course['tags']);

        $courseReviews = $reviewObj->findCourseReviews($course['id'],'0','1');

        $freeLesson=$courseObj->searchLessons(array('courseId'=>$id,'type'=>'video','status'=>'published'),array('createdTime','ASC'),0,1);
        if($freeLesson)$freeLesson=$freeLesson[0];

//        return $this->render("Course:show", array(
        $firstLessonId = 0;
        foreach($groupedItems as $gitem){
            if($gitem['type'] == 'chapter'){

            }else{
                $firstLessonId = $gitem['data'][0]['id'];
            }
        }
        return $this->render("Course:show", array(
            'course' => $course,
            'firstLessonId' => $firstLessonId,
            'canManage' => $courseObj->canManageCourse($course['id']),
            'freeLesson'=>$freeLesson,
            'groupedItems' => $groupedItems,
            'hasFavorited' => $hasFavorited,
            'category' => $category,
            'previewAs' => $previewAs,
            'tags' => $tags,
            'currentTime' => $currentTime,
            'courseReviews' => $courseReviews,
            'weeks' => $weeks,
            'courseShareContent'=>$courseShareContent,
            'consultDisplay' => true,
            'isShowVirtualLab'=>$isShowVirtualLab,
//            'teachers'=>$teachers,
            'firstItemKey' => $firstItemKey,
            'type' => $type,
        ));

    }
    
    private function canShowCourse($course, $user)
    {
        return ($course['status'] == 'published') or
            $user->isAdmin() or
            $this->getCourseService()->isCourseTeacher($course['id'],$user->id) ;
    }

    private function previewAsMember($as, $member, $course)
    {
        $user = $this->getCurrentUser();
        if (empty($user->id)) {
            return null;
        }

        if (in_array($as, array('member', 'guest'))) {
            if (isGranted('ROLE_ADMIN')) {
                $member = array(
                    'id' => 0,
                    'courseId' => $course['id'],
                    'userId' => $user->id,
                    'levelId' => 0,
                    'learnedNum' => 0,
                    'isLearned' => 0,
                    'seq' => 0,
                    'isVisible' => 0,
                    'role' => 'teacher',
                    'locked' => 0,
                    'createdTime' => time(),
                    'deadline' => 0
                );
            }

            if (empty($member) or $member['role'] != 'teacher') {
                return $member;
            }

            if ($as == 'member') {
                $member['role'] = 'student';
            } else {
                $member = null;
            }
        }

        return $member;
    }

    private function groupCourseItems($items)
    {
        $grouped = array();

        $list = array();
        foreach ($items as $id => $item) {
            if ($item['itemType'] == 'chapter') {
                if (!empty($list)) {
                    $grouped[] = array('type' => 'list', 'data' => $list);
                    $list = array();
                }
                $grouped[] = array('type' => 'chapter', 'data' => $item);
            } else {
                $list[] = $item;
            }
        }

        if (!empty($list)) {
            $grouped[] = array('type' => 'list', 'data' => $list);
        }

        return $grouped;
    }

    //将节放到章下面
    private function filterGroupCourseItems($items){
        $group = $this->groupCourseItems($items);
        $unitArr = [];
        foreach ($group as $k => $g){
            $chapter = $g['data'];
            if($g['type'] == 'chapter'){
                if($chapter['type'] == 'unit'){   //节
                    $unitArr[strval($chapter['parentId'])][] = $chapter;
                    unset($group[$k]);
                }
            }
        }

        foreach ($group as $k => $g){
            $chapter = $g['data'];
            if($g['type'] == 'chapter'){
                if($chapter['type'] != 'unit'){   //章
                    $chapterId = $chapter['id'];
                    if(!empty($unitArr[strval($chapterId)])){
                        $group[$k]['data']['units'] = $unitArr[strval($chapterId)];
                    }
                }
            }
        }

        return $group;
    }

    private function calculateUserLearnProgress($course, $member)
    {
        if ($course['lessonNum'] == 0) {
            return array('percent' => '0%', 'number' => 0, 'total' => 0);
        }

        $percent = intval($member['learnedNum'] / $course['lessonNum'] * 100) . '%';

        return array (
            'percent' => $percent,
            'number' => $member['learnedNum'],
            'total' => $course['lessonNum']
        );
    }

    public function favoriteAction(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if(!$user->id) $this->jsonResponse(403, 'Unlogin');
        $this->getCourseService()->favoriteCourse($id);
        return $this->createJsonResponse(true);
    }

    public function unfavoriteAction(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if(!$user->id) $this->jsonResponse(403, 'Unlogin');
        
        $this->getCourseService()->unfavoriteCourse($id);
        return $this->createJsonResponse(true);
    }

    public function createAction(Request $request)
    {
        $user = $this->getUserService()->getCurrentUser();

        #edit 未登录跳登录 fubaosheng 2015-06-01
        if(!$user->isLogin())
            $this->redirect('User/Signin/index');

        $userProfile = $this->getUserService()->getUserProfile($user->id);

        $isLive = $request->query->get('flag');
        $type = ($isLive == "isLive") ? 'live' : 'normal';

        if ($type == 'live') {
            $courseSetting = $this->setting('course', array());
            if (!empty($courseSetting['live_course_enabled'])) {
                $client = LiveClientFactory::createClient();
                $capacity = $client->getCapacity();
            } else {
                $capacity = array();
            }
        }

        if (!isGranted('ROLE_TEACHER')) {
            throw $this->createAccessDeniedException();
        }

        if ($request->getMethod() == 'POST') {
            $course = $request->request->all();
            $course = $this->getCourseService()->createCourse($course);
            return $this->redirect($this->generateUrl('course_manage', array('id' => $course['id'])));
        }

        return $this->render('Course:create', array(
            'userProfile'=>$userProfile,
            'type'=>$type
        ));
    }

    //课程列表使用的模板是CourseLesson/item-list.html.twig
    public function learnAction(Request $request, $id)
    {   
        $user = $this->getCurrentUser();
        $courseSerObj = $this->getCourseService();
        if (!$user->isLogin()) {

        }else{
            //如果不是该课程的学生，添加课程学生course_member
            if(!$this->getCourseService()->isCourseStudent($id,$user['id'])){
                $this->getCourseService()->addCourseStudent($id,$user['id']);
            }
        }
        $course = $courseSerObj->getCourse($id);

        if (empty($course) || $course['isDeleted']) {
            return $this->createMessageResponse('error',"课程不存在，或已被删除。");
        }

        if (!$courseSerObj->canTakeCourse($id)) {
        }

        $this->getCourseService()->increaseCourseViewCount($id);

        //请求启动docker容器
        $startDockerUrl = "http://10.58.85.136:8888/envs/start";
        $dockerWindowUrl = "http://10.58.85.136:6080/vnc_auto.html?token={token}&password=123456";
        $addWindowUrl = "/";
        $delWindowUrl = "/";
        $startDockerParams = array(
            "type"  => "container",
            "image" => "chenyf/box",
            "cmd"   => "/etc/box/init.sh"
        );
        $startDockerParams = json_encode($startDockerParams);
        $result = QcloudApi_Common_Request_Base::sendRequest($startDockerUrl, $startDockerParams, 'POST');
        if (isset($result['errno']) && $result['errno']==0) {
            $envname = $result['data']['envname'];
            //$port = $result['data']['port'];
            $dockerWindowUrl = str_replace('{token}', $envname, $dockerWindowUrl);
        }

        $windowUrl = array(
            'init' => $dockerWindowUrl,
            'add'  => $addWindowUrl,
            'del'  => $delWindowUrl
        );
        return $this->render('Course:learn', array(
            'course'    => $course,
            'windowUrl' => $windowUrl
        ));
    }


    /**
     * 用户学习间隔上报
     * @author 谈海涛 2015-09-15
     */
    public function studyStatisticsReportAction() {
        $data['courseId'] = isset($_POST['courseId']) ? intval($_POST['courseId']) : 0;
        $data['lessonId'] = isset($_POST['lessonId']) ? intval($_POST['lessonId']) : 0;
        $data['position'] = isset($_POST['position']) ? abs(intval($_POST['position'])) : 0;
        $data['duration'] = isset($_POST['duration']) ? intval($_POST['duration']) : 0;
        $data['status'] = isset($_POST['status']) ? $_POST['status'] : '';
        $data['brower'] = isset($_POST['brower']) ? $_POST['brower'] : "";
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data["intervalTm"] = isset($_POST['intervalTm']) ? intval($_POST['intervalTm']) : 0;
        $current_time = time();
        $data['report_time'] = intval($current_time);
        $data['report_date'] = date("Y-m-d H:i:s", $current_time);
        $data['uid'] = intval($this->getCurrentUser()->id);
        
        $lastPlayPos = $this->getCourseReportService()->getLastPlayPos($data['courseId'],$data['lessonId'],$data['uid']);
        if(!empty($lastPlayPos)  && ($data['position'] < $lastPlayPos)){
            $data['lastPlayPos'] = $lastPlayPos;
        }else{
            $data['lastPlayPos'] = $data['position'];
        }
        
        if ($this->getCurrentUser()->id == 0) {
            return $this->createJsonResponse(array('status' => '0','info'=>'NOLOGIN'));
        }
        $rs = $this->getCourseReportService()->reportStudyData($data['courseId'],$data['lessonId'],$data['uid'],$data);
        if ($rs) {
            return $this->createJsonResponse(array('status' => '1'));
        }
        return $this->createJsonResponse(array('status' => '0'));
    }
    /**
     * 获得视频最后播放位置
     * @author 谈海涛 2015-09-15
     */
    public function getReportPositionAction(){
        $courseId = isset($_GET['courseId']) ? intval($_GET['courseId']) : 0;
        $lessonId = isset($_GET['lessonId']) ? intval($_GET['lessonId']) : 0;
        $uid      = intval($this->getCurrentUser()->id);
        $position = $this->getCourseReportService()->getReportPosition($courseId,$lessonId,$uid);
        if ($position) {
            return $this->createJsonResponse(array('status' => '1','data'=>$position));
        }
        return $this->createJsonResponse(array('status' => '0'));
        
    }

    public function recordLearningTimeAction(Request $request,$lessonId,$time)
    {
        $user = $this->getCurrentUser();

        $this->getCourseService()->waveLearningTime($lessonId,$user->id,$time);

        return $this->createJsonResponse(true);
    }


    public function detailDataAction(Request $request,$id)
    {

        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($id);

        $count = $courseSerObj->getCourseStudentCount($id);
        $paginator = new Paginator($this->get('request'), $count, 20);

        $students = $courseSerObj->findCourseStudents($id, $paginator->getOffsetCount(),  $paginator->getPerPageCount());

        foreach ($students as $key => $student) {

            $user=$this->getUserService()->getUser($student['userId']);
            $students[$key]['nickname']=$user['nickname'];

            $questionCount=$this->getThreadService()->searchThreadCount(array('courseId'=>$id,'type'=>'question','userId'=>$user['id']));
            $students[$key]['questionCount']=$questionCount;

            if( $student['learnedNum']>=$course['lessonNum'] && $course['lessonNum']>0){
                $finishLearn=$courseSerObj->searchLearns(array('courseId'=>$id,'userId'=>$user['id'],'sttaus'=>'finished'),array('finishedTime','DESC'),0,1);
                $students[$key]['fininshTime']=$finishLearn[0]['finishedTime'];

                $students[$key]['fininshDay']=intval(($finishLearn[0]['finishedTime']-$student['createdTime'])/(60*60*24));
            }else{
                $students[$key]['fininshDay']=intval((time()-$student['createdTime'])/(60*60*24));
            }

            $learnTime=$courseSerObj->searchLearnTime(array('userId'=>$user['id'],'courseId'=>$id));
            $students[$key]['learnTime']=$learnTime;
        }

        return $this->render('Course:course-data-modal', array(
            'course'=>$course,
            'paginator'=>$paginator,
            'students'=>$students,
            ));
    }

    public function recordWatchingTimeAction(Request $request,$lessonId,$time)
    {
        $user = $this->getCurrentUser();

        $this->getCourseService()->waveWatchingTime($user->id,$lessonId,$time);

        return $this->createJsonResponse(true);
    }

    public function watchPlayAction(Request $request,$lessonId)
    {
        $user = $this->getCurrentUser();

        $this->getCourseService()->watchPlay($user->id,$lessonId);

        return $this->createJsonResponse(true);
    }

    public function watchPausedAction(Request $request,$lessonId)
    {
        $user = $this->getCurrentUser();

        $this->getCourseService()->watchPaused($user->id,$lessonId);

        return $this->createJsonResponse(true);
    }

    public function addMemberExpiryDaysAction(Request $request, $courseId, $userId){
        $user = $this->getUserService()->getUser($userId);
        $course = $this->getCourseService()->getCourse($courseId);
        $member = $this->getCourseService()->getCourseMember($courseId,$userId);
        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();
            $day = isset($fields['expiryDay']) ? intval($fields['expiryDay']) : 0;
            $courseMember = $this->getCourseService()->getCourseMember($courseId,$userId);
            if($courseMember["deadline"]){
                if($courseMember["deadline"] - time()>0){
                    $cuntDeadline = $member["deadline"] + (intval($day)*24*3600);
                    if( ($cuntDeadline-time())/86400 > 2000){
                        return $this->createJsonResponse(false);
                    }else{
                        $this->getCourseService()->addMemberExpiryDays($courseId, $userId, $day);
                        return $this->createJsonResponse(true);
                    }
                }else{
                    if($day > 2000){
                        return $this->createJsonResponse(false);
                    }else{
                        $this->getCourseService()->addMemberExpiryDays($courseId, $userId, $day);
                        return $this->createJsonResponse(true);
                    }
                }
            }else{
                return $this->createJsonResponse(true);
            }
        }
        $default = $this->getSettingService()->get('default', array());
        return $this->render('CourseStudentManage:set-expiryday-modal', array(
            'course' => $course,
            'user' => $user,
            'default'=> $default,
            'member'=>$member
        ));
    }

    public function memberExpiryDaysAction(Request $request, $courseId, $userId, $day){
        $member = $this->getCourseService()->getCourseMember($courseId,$userId);
        if($member["deadline"]){
            if($member["deadline"] - time()>0){
                $countDeadline = $member["deadline"] + (intval($day)*24*3600);
                $deadline = $this->getWebExtension()->remainTimeFilter($countDeadline);
                return $this->success($deadline);
            }else{
                return $this->success($day."天");
            }
        }else{
            return $this->success('无限制');
        }
    }

    /**
     * Block Actions
     */

    public function headerAction($course, $manage = false)
    {

        $user = $this->getCurrentUser();
        $courseSerObj = $this->getCourseService();
        $member = $courseSerObj->getCourseMember($course['id'], $user->id);

        $nextLearnLesson = $this->getCourseService()->getUserNextLearnLesson($user->id, $course['id']);

        $progress = $this->calculateUserLearnProgress($course, $member);

        $users = empty($course['teacherIds']) ? array() : $this->getUserService()->findUsersByIds($course['teacherIds']);

        $defaultSetting = $this->getSettingService()->get('default', array());

        if (isset($defaultSetting['courseShareContent'])){
            $courseShareContent = $defaultSetting['courseShareContent'];
        } else {
            $courseShareContent = "";
        }

        $valuesToBeReplace = array('{{course}}');
        $valuesToReplace = array($course['title']);
        $courseShareContent = str_replace($valuesToBeReplace, $valuesToReplace, $courseShareContent);

        $teacher = $this->getCourseService()->getCourseTeacher($course['id']);

        return $this->render('Course:header', array(
            'course' => $course,
            'teacher' => $teacher,
            'nextLearnLesson' => $nextLearnLesson,
            'progress' => $progress,
            'users' => $users,
            'manage' => $manage,
            'courseShareContent' => $courseShareContent,
            #by qzw 'isAdmin' => isGranted('ROLE_SUPER_ADMIN')
            //'isAdmin' => isGranted('ROLE_SUPER_ADMIN')
            'canManage' => $courseSerObj->canManageCourse($course['id']),
        ));
    }

    public function teachersBlockAction($course)
    {
        $userSerObj = $this->getUserService();
        $users = $userSerObj->findUsersByIds($course['teacherIds']);
        $profiles = $userSerObj->findUserProfilesByIds($course['teacherIds']);

        return $this->render('Course:teachers-block', array(
            'course' => $course,
            'users' => $users,
            'profiles' => $profiles,
        ));
    }

    public function progressBlockAction($course)
    {
        $user = $this->getCurrentUser();

        $member = $this->getCourseService()->getCourseMember($course['id'], $user->id);
        $nextLearnLesson = $this->getCourseService()->getUserNextLearnLesson($user->id, $course['id']);

        $progress = $this->calculateUserLearnProgress($course, $member);
        return $this->render('Course:progress-block', array(
            'course' => $course,
            'member' => $member,
            'nextLearnLesson' => $nextLearnLesson,
            'progress'  => $progress,
        ));
    }

    public function latestMembersBlockAction($course, $count = 10)
    {
        $students = $this->getCourseService()->findCourseStudents($course['id'], 0, 12);
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($students, 'userId'));
        return $this->render('Course:latest-members-block', array(
            'students' => $students,
            'users' => $users,
        ));
    }

    public function coursesBlockAction($courses, $view = 'list', $mode = 'default',$md=4)
    {
        $userIds = array();
        foreach ($courses as $key => $course) {
            $userIds = array_merge($userIds, $course['teacherIds']);
            $classrooms = array();
            if ($this->isPluginInstalled("Classroom")) {
                $classrooms=$this->getClassroomService()->findClassroomsByCourseId($course['id']); // no exista

                $classroomIds=ArrayToolkit::column($classrooms,'classroomId');

                $courses[$key]['classroomCount']=count($classroomIds);

                if(count($classroomIds)>0){

                    $classroom=$this->getClassroomService()->getClassroom($classroomIds[0]); //no exists
                    $courses[$key]['classroom']=$classroom;
                }
            }
        }

        $users = $this->getUserService()->findUsersByIds($userIds);
        return $this->render("Course:courses-block-{$view}#Course", array(
            'courses' => $courses,
            'users' => $users,
            'classrooms'=>$classrooms,
            'mode' => $mode,
            'md' => $md,
        ));
    }

    public function selectAction(Request $request)
    {
        $url="";
        $type="";
        $classroomId=0;

        if($request->query->get('url')){

            $url=$request->query->get('url');
        }

        if($request->query->get('type')){

            $type=$request->query->get('type');
        }

        if($request->query->get('classroomId')){

            $classroomId=$request->query->get('classroomId');
        }


        $conditions = array(
            'status' => 'published'
        );

        $paginator = new Paginator(
            $this->get('request'),
            $this->getCourseService()->searchCourseCount($conditions)
            , 5
        );

        $courses = $this->getCourseService()->searchCourses(
            $conditions, 'latest',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $courseIds=ArrayToolkit::column($courses, 'id');
        $unEnabledCourseIds =$this->getClassroomCourseIds($request,$courseIds);

        $userIds = array();
        foreach ($courses as &$course) {
            $course['tags'] = $this->getTagService()->findTagsByIds($course['tags']);
            $userIds = array_merge($userIds, $course['teacherIds']);
        }

        $users = $this->getUserService()->findUsersByIds($userIds);

        return $this->render("Course:course-select", array(
            'users'=>$users,
            'url'=>$url,
            'courses'=>$courses,
            'type'=>$type,
            'unEnabledCourseIds'=>$unEnabledCourseIds,
            'classroomId'=>$classroomId,
            'paginator'=>$paginator
        ));
    }


	public function chatRoomAction($id){
		list($course,$member) = $this->getCourseService()->tryTakeCourse($id);
		return $this->render('Course:chatRoom',compact('course'));
	}


    private function getClassroomCourseIds($request,$courseIds)
    {
        $unEnabledCourseIds=array();
        if($request->query->get('type') !="classroom")
            return $unEnabledCourseIds;

        if ($this->isPluginInstalled("Classroom")) {

            $classroomId=$request->query->get('classroomId');

            foreach ($courseIds as $key => $value) {
                $course=$this->getCourseService()->getCourse($value);
                $classrooms = $this->getClassroomService()->findClassroomsByCourseId($value);// no exists
                if($course && count($classrooms)==0){
                    unset($courseIds[$key]);
                }

            }
        }
        $unEnabledCourseIds = $courseIds;

        return $unEnabledCourseIds;
    }

    public function searchAction(Request $request)
    {
        $key = $request->request->get("key");
        $classroomId=0;

        $conditions = array( "title"=>$key );
        $conditions['status'] = 'published';

        if($request->query->get('classroomId')){

            $classroomId=$request->query->get('classroomId');
        }

        $courses = $this->getCourseService()->searchCourses(
            $conditions, 'latest',
            0,
            5
        );

        $courseIds=ArrayToolkit::column($courses, 'id');
        $unEnabledCourseIds =$this->getClassroomCourseIds($request,$courseIds);

        $userIds = array();
        foreach ($courses as &$course) {
            $course['tags'] = $this->getTagService()->findTagsByIds($course['tags']);
            $userIds = array_merge($userIds, $course['teacherIds']);
        }

        $users = $this->getUserService()->findUsersByIds($userIds);

        return $this->render('Course:course-select-list', array(
            'users'=>$users,
            'courses'=>$courses,
            'unEnabledCourseIds'=>$unEnabledCourseIds
        ));
    }

    public function relatedCoursesBlockAction($course)
    {

        $courses = $this->getCourseService()->findCoursesByAnyTagIdsAndStatus($course['tags'], 'published', array('Rating' , 'DESC'), 0, 4);

        return $this->render("Course:related-courses-block", array(
            'courses' => $courses,
            'currentCourse' => $course
        ));
    }

    public function rebuyAction(Request $request,$courseId)
    {
        $user = $this->getCurrentUser();

        $this->getCourseService()->removeStudent($courseId, $user->id);

        return $this->redirect($this->generateUrl('course_show',array('id' => $courseId)));
    }

    private function getClassroomMembersByCourseId($id) {

        if ($this->isPluginInstalled("Classroom")) {
            $classrooms = $this->getClassroomService()->findClassroomsByCourseId($id); //no exists
            $classroomIds = ArrayToolkit::column($classrooms, "classroomId");
            $user=$this->getCurrentUser();

            $members = $this->getClassroomService()->findMembersByUserIdAndClassroomIds($user->id, $classroomIds); //no exists
            return $members;
        }
        return array();
    }

	/**
	 * 课程分类选择
	 * @author 王磊 2015-05-21
	 */
	public function getCategorySelectAction($param) {
            
            $categoryObj = $this->getCategoryService();
            $categories = $categoryObj->getCategoryTree();
            $treeCate     = array();
            $topCateIdArr = array();
            $CateTreeById = array(); //所有二级分类数组
            if ($categories) {
                foreach ($categories as $cate) {
                    $parentId = $cate['parentId'];
                    $id       = $cate['id'];
                    if ($cate['isDelete'] == 1) continue;
                    if (!$parentId) {
                        $topCateIdArr[]         = $id;
                        $treeCate[$id]          = $cate;
                        $treeCate[$id]['child'] = array();
                    } elseif (in_array($parentId, $topCateIdArr)) {
                        $CateTreeById[$id]                               = $cate;
                        $treeCate[$parentId]['child'][$id]               = $cate;
                        $treeCate[$parentId]['child'][$id]['threeChild'] = array();
                    } else {
                        $TopCateId                                                           = $CateTreeById[$cate['parentId']]['parentId'];
                        $treeCate[$TopCateId]['child'][$cate['parentId']]['threeChild'][$id] = $cate;
                    }
                }
            }
            return $this->render('Course:select-category', array(
                    'treeCate' => $treeCate,
                    'param' => $param,
            ));
    }

    private function createCourseForm()
    {
        return $this->createNamedFormBuilder('course')
            ->add('title', 'text')
            ->getForm();
    }

    protected function getUserService()
    {
        return createService('User.UserService');
    }

    protected function getCourseMemberService()
    {
        return createService('Course.CourseMemberService');
    }

    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    private function getCategoryService()
    {
        return createService('Taxonomy.CategoryService');
    }

    private function getTagService()
    {
        return createService('Taxonomy.TagService');
    }

    private function getReviewService()
    {
        return createService('Course.ReviewService');
    }

    private function getHomeworkService()
    {
            return createService('Homework.HomeworkService');
    }

    private function getExerciseService()
    {
            return createService('Homework.ExerciseService');
    }

    private function getSettingService()
    {
        return createService('System.SettingServiceModel');
    }

    private function getThreadService()
    {
        return createService('Thread.ThreadService');
    }

    private function getUploadFileService()
    {
    return createService('File.UploadFileService');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }

    private function getCourseReportService() 
    {
        return createService('Course.CourseReportServiceModel');
    }
    
    //@author 褚兆前 2016-03-18
    private function getCourceLessonLearnService() {
        return createService('Course.CourceLessonLearnServiceModel');
    }
    
    private function getWebExtension(){
        return $this->container->get('redcloud.twig.web_extension');
    }

}
