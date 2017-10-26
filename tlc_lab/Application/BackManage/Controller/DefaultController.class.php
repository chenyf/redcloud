<?php
namespace BackManage\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Common\Model\Util\CloudClientFactory;

class DefaultController extends BaseController
{
    public function popularCoursesAction(Request $request)
    {
        $dateType = $request->query->get('dateType');

        if($dateType == "today"){
            $startTime = strtotime('today'); 
            $endTime = strtotime('tomorrow');
        }

        if($dateType == "yesterday"){
            $startTime =  strtotime('yesterday');
            $endTime =  strtotime('today');
        }

        if($dateType == "this_week"){
            $startTime = strtotime('Monday this week');
            $endTime = strtotime('Monday next week');
        }

        if($dateType == "last_week"){
            $startTime = strtotime('Monday last week');
            $endTime = strtotime('Monday this week');
        }

        if($dateType == "this_month"){
            $startTime = strtotime('first day of this month midnight');
            $endTime = strtotime('first day of next month midnight');
        }

        if($dateType == "last_month"){
            $startTime = strtotime('first day of last month midnight');
            $endTime = strtotime('first day of this month midnight');
        }

        $members = $this->getCourseService()->countMembersByStartTimeAndEndTime($startTime,$endTime);
        $courseIds = ArrayToolkit::column($members,"courseId");
        
        $courses = $this->getCourseService()->findCoursesByIds($courseIds);
        $courses = ArrayToolkit::index($courses,"id");

        $sortedCourses = array();

        foreach ($members as $key => $value) {
            $course = array();
            $course['title'] = $courses[$value["courseId"]]['title'];
            $course['courseId'] = $courses[$value["courseId"]]['id'];
            $sortedCourses[] = $course;
      }
        return $this->render('Default:popular-courses-table', array(
            'sortedCourses' => $sortedCourses
        ));
        
    }

    public function indexAction(Request $request)
    {
        //        echo U('admin/online/count', array('dd'=>43234));exit;
        ///$this->isAdminOnline();echo 3;exit;
        //print_r(createService('System.SettingService'));exit;
        //print_r(createService('CloudPlatform.AppService'));exit;
        
        return $this->render('Default:index');
    }
    
    public function testAction(){
//        $request = Request::createFromGlobals();
//        print_r($request->query->all()) ;
//        echo $request->request->get('t');exit;
        
//        $r = new Response('<h1>Contact us!</h1>');
//        return $r->sendContent();
         $arr = $this->createNamedFormBuilder('post', array('11'))
            ->add('content', 'textarea')
            ->add('courseId', 'hidden')
            ->add('threadId', 'hidden')
            ->getForm();
          var_dump($arr->createView());die;
//        return $this->render('Default:test');
    }
    
    public function test1Action(){
        return $this->render('Default:test1', array('abc'=>123412431));
    }
    public function test2Action(){
        return $this->render('Default:test2', array('abc'=>123412431));
    }

    public function getCloudNoticesAction(/*Request $request*/)
    {
        $userAgent = 'Open redcloud App Client 1.0';
        $connectTimeout = 10;
        $timeout = 10;
        $url = "http://open.redcloud.com/api/v1/context/notice";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_URL, $url );
        $notices = curl_exec($curl);
        curl_close($curl);
        $notices = json_decode($notices, true);
        
        return $this->render('Default:cloud-notice',array(
            "notices"=>$notices,
        ));
    }

    public function officialMessagesAction()
    {
        $message=$this->getAppService()->getMessages();
        
        return $this->render('Default:official.messages',array(
            "message"=>$message,
        ));
    }

    public function systemStatusAction()
    {   
        $apps=array();
        $systemVersion="";
        $error="";
        $apps = $this->getAppService()->checkAppUpgrades();

        $appsAll = $this->getAppService()->getCenterApps();

        $codes = ArrayToolkit::column($appsAll, 'code');

        $installedApps = $this->getAppService()->findAppsByCodes($codes);

        $unInstallAppCount=count($appsAll)-count($installedApps);

        $app_count=count($apps);
        if(isset($apps['error'])) $error="error";

        $mainAppUpgrade = null;
        foreach ($apps as $key => $value) {
            if(isset($value['code']) && $value['code']=="MAIN") {
                $mainAppUpgrade = $value;
            }
        }
       
        #note by qzw 2015-08-25
        //$liveCourseStatus = $this->getEduCloudService()->getLiveCourseStatus();

        return $this->render('Default:system.status',array(
            "apps"=>$apps,
            "error"=>$error,
            "mainAppUpgrade"=>$mainAppUpgrade,
            "app_count"=>$app_count,
            "unInstallAppCount"=>$unInstallAppCount,
            "liveCourseStatus" => $liveCourseStatus
        ));
    }

    public function latestUsersBlockAction(/*Request $request*/)
    {
        $users = $this->getUserService()->searchUsers(array(), 0, 5);
        return $this->render('Default:latest-users-block', array(
            'users'=>$users,
        ));
    }
    
    public function operationAnalysisDashbordBlockAction(Request $request)
    {
        
        $request = Request::createFromGlobals();
        $siteSelect = 'local';
        $switch = true;
        
        $todayTimeStart=strtotime(date("Y-m-d",time()));
        $todayTimeEnd=strtotime(date("Y-m-d",time()+24*3600));

        $yesterdayTimeStart=strtotime(date("Y-m-d",time()-24*3600));
        $yesterdayTimeEnd=strtotime(date("Y-m-d",time()));

        #总用户数
        $todayUserSum=$this->getUserService()->findUsersCountByLessThanCreatedTime(strtotime(date("Y-m-d",time()+24*3600)));
        $yesterdayUserSum=$this->getUserService()->findUsersCountByLessThanCreatedTime(strtotime(date("Y-m-d",time())));
        
        #用户登录数
        $todayLoginNum=$this->getLogService()->analysisLoginNumByTime(strtotime(date("Y-m-d",time())),strtotime(date("Y-m-d",time()+24*3600)), $siteSelect);
        $yesterdayLoginNum=$this->getLogService()->analysisLoginNumByTime(strtotime(date("Y-m-d",time()-24*3600)),strtotime(date("Y-m-d",time())), $siteSelect);
        
        #新增课程数
        $todayCourseNum=$this->getCourseService()->searchCourseCount(array("startTime"=>$todayTimeStart,"endTime"=>$todayTimeEnd, 'siteSelect' =>$siteSelect));    
        $yesterdayCourseNum=$this->getCourseService()->searchCourseCount(array("startTime"=>$yesterdayTimeStart,"endTime"=>$yesterdayTimeEnd, 'siteSelect' =>$siteSelect));
        
        #课程总数
        $todayCourseSum=$this->getCourseService()->findCoursesCountByLessThanCreatedTime(strtotime(date("Y-m-d",time()+24*3600)), $siteSelect);
        $yesterdayCourseSum=$this->getCourseService()->findCoursesCountByLessThanCreatedTime(strtotime(date("Y-m-d",time())),$siteSelect);
        
        #新增课程内容数
        $todayLessonNum=$this->getCourseService()->searchLessonCount(array("startTime"=>$todayTimeStart,"endTime"=>$todayTimeEnd, 'siteSelect' => $siteSelect));
        $yesterdayLessonNum=$this->getCourseService()->searchLessonCount(array("startTime"=>$yesterdayTimeStart,"endTime"=>$yesterdayTimeEnd, 'siteSelect' => $siteSelect));

        #完成课程内容学习数
        $todayFinishedLessonNum=$this->getCourseService()->searchLearnCount(array("startTime"=>$todayTimeStart,"endTime"=>$todayTimeEnd,"status"=>"finished", 'siteSelect' => $siteSelect));
        $yesterdayFinishedLessonNum=$this->getCourseService()->searchLearnCount(array("startTime"=>$yesterdayTimeStart,"endTime"=>$yesterdayTimeEnd,"status"=>"finished", 'siteSelect' => $siteSelect));

        #视频观看数
        $todayAllVideoViewedNum=$this->getCourseService()->searchAnalysisLessonViewCount(array('startTime'=>strtotime(date("Y-m-d",time())),'endTime'=>strtotime(date("Y-m-d",time()+24*3600)),"fileType"=>'video', 'siteSelect' => $siteSelect));
        $yesterdayAllVideoViewedNum=$this->getCourseService()->searchAnalysisLessonViewCount(array('startTime'=>strtotime(date("Y-m-d",time()-24*3600)),'endTime'=>strtotime(date("Y-m-d",time())),"fileType"=>'video', 'siteSelect' => $siteSelect));        

        #本地视频观看数
        $todayLocalVideoViewedNum=$this->getCourseService()->searchAnalysisLessonViewCount(array('startTime'=>strtotime(date("Y-m-d",time())),'endTime'=>strtotime(date("Y-m-d",time()+24*3600)),"fileType"=>'video','fileStorage'=>'local', 'siteSelect' => $siteSelect));
        $yesterdayLocalVideoViewedNum=$this->getCourseService()->searchAnalysisLessonViewCount(array('startTime'=>strtotime(date("Y-m-d",time()-24*3600)),'endTime'=>strtotime(date("Y-m-d",time())),"fileType"=>'video','fileStorage'=>'local', 'siteSelect' => $siteSelect));

        #网络视频观看数
        $todayNetVideoViewedNum=$this->getCourseService()->searchAnalysisLessonViewCount(array('startTime'=>strtotime(date("Y-m-d",time())),'endTime'=>strtotime(date("Y-m-d",time()+24*3600)),"fileType"=>'video','fileStorage'=>'net', 'siteSelect' => $siteSelect));
        $yesterdayNetVideoViewedNum=$this->getCourseService()->searchAnalysisLessonViewCount(array('startTime'=>strtotime(date("Y-m-d",time()-24*3600)),'endTime'=>strtotime(date("Y-m-d",time())),"fileType"=>'video','fileStorage'=>'net', 'siteSelect' => $siteSelect));


        $storageSetting = $this->getSettingService()->get('storage');

        if (!empty($storageSetting['cloud_access_key']) and !empty($storageSetting['cloud_secret_key'])) {
            $factory = new CloudClientFactory();
            $client = $factory->createClient($storageSetting);
            $keyCheckResult = '';// $client->checkKey(); 注释 by qzw 2015-11-25
        } else {
            $keyCheckResult = array('error' => 'error');
        }
        return $this->render('Default:operation-analysis-dashbord', array(
            'todayUserSum' => $todayUserSum,
            'yesterdayUserSum' => $yesterdayUserSum,
            'todayCourseSum' => $todayCourseSum,
            'yesterdayCourseSum' => $yesterdayCourseSum,
            'todayLoginNum'=>$todayLoginNum,
            'yesterdayLoginNum'=>$yesterdayLoginNum,
            'todayCourseNum'=>$todayCourseNum,
            'yesterdayCourseNum'=>$yesterdayCourseNum,
            'todayLessonNum'=>$todayLessonNum,
            'yesterdayLessonNum'=>$yesterdayLessonNum,
            'todayFinishedLessonNum'=>$todayFinishedLessonNum,
            'yesterdayFinishedLessonNum'=>$yesterdayFinishedLessonNum,

            'todayAllVideoViewedNum'=>$todayAllVideoViewedNum,
            'yesterdayAllVideoViewedNum'=>$yesterdayAllVideoViewedNum,

            'todayLocalVideoViewedNum'=>$todayLocalVideoViewedNum,
            'yesterdayLocalVideoViewedNum'=>$yesterdayLocalVideoViewedNum,

            'todayNetVideoViewedNum'=>$todayNetVideoViewedNum,
            'yesterdayNetVideoViewedNum'=>$yesterdayNetVideoViewedNum,
            'keyCheckResult'=>$keyCheckResult,
        ));        
    }

    public function onlineCountAction(/*Request $request*/)
    {
        $onlineCount =  $this->getStatisticsService()->getOnlineCount(15*60);
        return $this->createJsonResponse(array('onlineCount' => $onlineCount, 'message' => 'ok'));
    }

    public function loginCountAction(/*Request $request*/)
    {
        $loginCount = $this->getStatisticsService()->getloginCount(15*60);
        return $this->createJsonResponse(array('loginCount' => $loginCount, 'message' => 'ok'));
    }

    public function unsolvedQuestionsBlockAction(/*Request $request*/)
    {
        $questions = $this->getThreadService()->searchThreads(
            array('type' => 'question'),
            'createdNotStick',
            0,5
        );
    
        $unPostedQuestion = array();
        foreach ($questions as $key => $value) {
            if ($value['postNum'] == 0) {
                $unPostedQuestion[] = $value;
            }else{
                $threadPostsNum = $this->getThreadService()->getThreadPostCountByThreadId($value['id']);
                $userPostsNum = $this->getThreadService()->getPostCountByuserIdAndThreadId($value['userId'],$value['id']);
                    if($userPostsNum == $threadPostsNum){
                        $unPostedQuestion[] = $value;
                    }
            }
        }

        $questions = $unPostedQuestion;


        $courses = $this->getCourseService()->findCoursesByIds(ArrayToolkit::column($questions, 'courseId'));
        $askers = $this->getUserService()->findUsersByIds(ArrayToolkit::column($questions, 'userId'));

        $teacherIds = array();
        foreach (ArrayToolkit::column($courses, 'teacherIds') as $teacherId) {
             $teacherIds = array_merge($teacherIds,$teacherId);
        }
        $teachers = $this->getUserService()->findUsersByIds($teacherIds);        

        return $this->render('Default:unsolved-questions-block', array(
            'questions'=>$questions,
            'courses'=>$courses,
            'askers'=>$askers,
            'teachers'=>$teachers
        ));
    }

    public function questionRemindTeachersAction(Request $request, $courseId, $questionId)
    {
        $course = $this->getCourseService()->getCourse($courseId);
        $question = $this->getThreadService()->getThread($courseId, $questionId);
        $questionUrl = $this->generateUrl('course_thread_show', array('courseId'=>$course['id'], 'id'=> $question['id']), true);
        $questionTitle = strip_tags($question['title']);
        foreach ($course['teacherIds'] as $receiverId) {
            $result = $this->getNotificationService()->notify($receiverId, 'default',
                "课程《{$course['title']}》有新问题 <a href='{$questionUrl}' target='_blank'>{$questionTitle}</a>，请及时回答。");
        }

        return $this->createJsonResponse(array('success' => true, 'message' => 'ok'));
    }

    protected function getEduCloudService()
    {
        return createService('EduCloud.EduCloudService');
    }

    protected function getSettingService()
    {
        return createService('System.SettingService');
    }

    protected function getStatisticsService()
    {
        return createService('System.StatisticsServiceModel');
    }

    protected function getThreadService()
    {
        return createService('Course.ThreadService');
    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getNotificationService()
    {
        return createService('User.NotificationService');
    }

    protected function getLogService()
    {
        return createService('System.LogService');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }

}
