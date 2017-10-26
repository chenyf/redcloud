<?php
namespace BackManage\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

class AnalysisController extends BaseController
{   

    public function rountByanalysisDateTypeAction(Request $request,$tab)
    {
        $analysisDateType=$request->query->get("analysisDateType");
        $startTime=$request->query->get("startTime");
        $endTime=$request->query->get("endTime");
        return $this->forward('BackManage:Analysis:'.$analysisDateType, array(
          //  'request'=>$request,
           'analysisDateType'=>$analysisDateType,
            'startTime'=>$startTime,
            'endTime'=>$endTime,
            'tab'=>$tab,
        ));
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $tab
     * @return type
     * @author ZhaoZuoWu 2015-04-01
     */
    public function registerAction(Request $request,$tab)
    {    
        $data=array();
        $registerStartDate="";

        $condition=$request->query->all();

        $timeRange=$this->getTimeRange($condition);
        if(!$timeRange) {
              $this->setFlashMessage("danger","输入的日期有误!");
                    return $this->redirect($this->generateUrl('admin_operation_analysis_register', array(
                   'tab' => "trend",
                )));
        }

        $isLocalCenterWeb = false;

        $condition['siteSelect'] = $isLocalCenterWeb ? 'all' : 'local';
        $timeRange['siteSelect'] = $isLocalCenterWeb ? 'all' : 'local';

        $switch = $isLocalCenterWeb ? false : true;
        $timeRange["switch"] = $switch;

        //TODO 处理 $timeRange查询数组
        $timeRange = [];

        $paginator = new Paginator(
                $request,
                $this->getUserService()->searchUserCount($timeRange),
                20
        );
     
        $registerDetail=$this->getUserService()->searchUsers(
            $timeRange,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );

        $registerData="";
        if($tab=="trend"){
            #非中心站获取自己本站数据，中心获取所有数据 qzw 2015-07-10
            $siteSelect = $isLocalCenterWeb ? 'all' : 'local';
            $registerData=$this->getUserService()->analysisRegisterDataByTime($timeRange['startTime'],$timeRange['endTime'], array('siteSelect'=>$siteSelect));
            $data=$this->fillAnalysisData($condition,$registerData);
        }


        $registerStartData=$this->getUserService()->searchUsers(array('switch'=>$switch),0,1);
        if($registerStartData) $registerStartDate=date("Y-m-d",$registerStartData[0]['createdTime']);
     
        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:register",array(
            'registerDetail'=>$registerDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            "registerStartDate"=>$registerStartDate,
            "dataInfo"=>$dataInfo,
          ));
    }

    
     /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $tab
     * @return type
     * @author ZhaoZuoWu 2015-04-01
     */
    public function taskNoticeAction(Request $request,$tab)
    {    
        $data=array();
        $registerStartDate="";
        
        $condition=$request->query->all();
       
        $timeRange=$this->getTimeRange($condition);
        if(!$timeRange) {

              $this->setFlashMessage("danger","输入的日期有误!");
                    return $this->redirect($this->generateUrl('admin_operation_analysis_taskNotice', array(
                   'tab' => "trend",
                )));
        }
        $ffmap = $timeRange;
        $ffmap["startTime"] = date("Y-m-d H:i:s",$timeRange["startTime"]);
        $ffmap["endTime"] = date("Y-m-d H:i:s",$timeRange["endTime"]);
        $paginator = new Paginator(
                $request,
                $this->getMessageService()->searchMsgCount($ffmap),
                20
        );
        
        $taskDetail=$this->getMessageService()->searchMessage(
            $ffmap,
            array('ctime', 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );
          foreach ($taskDetail as $k => $v) {
            $classIdArr = empty($v["class_id"]) ? array() : explode(",", $v["class_id"]);
            $teacherIdArr = empty($v["teacher_id"]) ? array() : explode(",", $v["teacher_id"]);
            $copyIdArr = empty($v["copy_people"]) ? array() : explode(",", $v["copy_people"]);
            $taskDetail[$k]["teacherNum"] = empty($teacherIdArr) ? 0 : count($teacherIdArr);
            $taskDetail[$k]["classNum"] = empty($classIdArr) ? 0 : count($classIdArr);
            $taskDetail[$k]["copyNum"] = empty($copyIdArr) ? 0 : count($copyIdArr);
        }
        $taskData="";
        if($tab=="trend"){
            $taskData=$this->getMessageService()->analysisTaskDataByTime($timeRange["startTime"],$timeRange["endTime"]);
            $data=$this->fillAnalysisData($condition,$taskData);
            }


        $taskStartData=$this->getMessageService()->searchMessage(array(),array('ctime', 'ASC'),0,1);
        if($taskStartData) $taskStartData=date("Y-m-d",$taskStartData[0]['ctime']);
        $msgType = array("1" => "短信", "2" => "邮件", '3' => "客户端推送", '4' => '站内信');
        $sendStatus = array("-1" => "作废", "0" => "未开始", '1' => "进行中", '2' => '完成', '3' => "失败");
        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:task-notice",array(
            'taskDetail'=>$taskDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            "taskStartData"=>$taskStartData,
            "dataInfo"=>$dataInfo,
            "msgtype"=>$msgType,
            "sendStatus"=>$sendStatus,
          ));
    }
    public function userSumAction(Request $request,$tab)
    {      
        $data=array();
        $userSumStartDate="";

        $condition=$request->query->all();
//        $timeRange=$this->getTimeRange($condition);
//        if(!$timeRange) {
//            $this->setFlashMessage("danger","输入的日期有误!");
//            return $this->redirect($this->generateUrl('admin_operation_analysis_user_sum', array(
//               'tab' => "trend",
//            )));
//        }

        $result = array(
            'tab' => $tab,
        );
        if($tab == "trend"){
//            $userSumData = $this->getUserService()->analysisUserSumByTime($timeRange['endTime']);
            $userSumData = $this->getUserService()->analysisUserSumByTime(null);
        
            $data=$this->fillAnalysisUserSum($condition,$userSumData);            
            $result["data"] = $data;
        } else {
            $paginator = new Paginator(
                $request,
//                $this->getUserService()->searchUserCount($timeRange),
                $this->getUserService()->searchUserCount([]),
                20
            );

            $userSumDetail=$this->getUserService()->searchUsers(
//                $timeRange,
                [],
                $paginator->getOffsetCount(),
                $paginator->getPerPageCount()
            );
            $result['userSumDetail'] = $userSumDetail;
            $result['paginator'] = $paginator;
        }

        $userSumStartData = $this->getUserService()->searchUsers(array(),0,1);

        if($userSumStartData) {
//            $userSumStartDate = date("Y-m-d",$userSumStartData[0]['createdTime']);
            $userSumStartDate = "-";
        }

//        $dataInfo=$this->getDataInfo($condition,$timeRange);
        $dataInfo=$this->getDataInfo($condition,null);

        $result['userSumStartDate'] = $userSumStartDate;
        $result['dataInfo'] = $dataInfo;

        return $this->render("OperationAnalysis:user-sum",$result);
    }

    public function courseSumAction(Request $request,$tab)
    {  
        $data=array();
        $courseSumStartDate="";

        $condition=$request->query->all();
        $timeRange=$this->getTimeRange($condition);
    
        if(!$timeRange) {

              $this->setFlashMessage("danger","输入的日期有误!");
                        return $this->redirect($this->generateUrl('admin_operation_analysis_course_sum', array(
                   'tab' => "trend",
                )));
        }

        $paginator = new Paginator(
                $request,
                $this->getCourseService()->findCoursesCountByLessThanCreatedTime($timeRange['endTime']),
             20
        );
       
        $courseSumDetail=$this->getCourseService()->searchCourses(
            $timeRange,
            '',
              $paginator->getOffsetCount(),
              $paginator->getPerPageCount()
         );
        $courseSumData="";

        if($tab=="trend"){
            $courseSumData=$this->getCourseService()->analysisCourseSumByTime($timeRange['endTime']);

            $data=$this->fillAnalysisCourseSum($condition,$courseSumData);          
        }

        $userIds = ArrayToolkit::column($courseSumDetail, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);

        $categories = $this->getCategoryService()->findCategoriesByIds(ArrayToolkit::column($courseSumDetail, 'categoryId'));

        $courseSumStartData=$this->getCourseService()->searchCourses(array(),'createdTimeByAsc',0,1);

        if($courseSumStartData) $courseSumStartDate=date("Y-m-d",$courseSumStartData[0]['createdTime']);

        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:course-sum",array(
            'courseSumDetail'=>$courseSumDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'categories'=>$categories,
            'data'=>$data,
            'users'=>$users,
            'courseSumStartDate'=>$courseSumStartDate,
            'dataInfo'=>$dataInfo,          
         ));
    }

    public function loginAction(Request $request,$tab)
    {    
        $data=array();
        $loginStartDate="";

        $condition=$request->query->all();
        $timeRange=$this->getTimeRange($condition);
        if(!$timeRange) {

              $this->setFlashMessage("danger","输入的日期有误!");
                        return $this->redirect($this->generateUrl('admin_operation_analysis_login', array(
                   'tab' => "trend",
                )));
        }

        $paginator = new Paginator(
                $request,
                $this->getLogService()->searchLogCount(array('action'=>"login_success",'startDateTime'=>date("Y-m-d H:i:s",$timeRange['startTime']),'endDateTime'=>date("Y-m-d H:i:s",$timeRange['endTime']))),
             20
        );
      
        $loginDetail=$this->getLogService()->searchLogs(
            array('action'=>"login_success",'startDateTime'=>date("Y-m-d H:i:s",$timeRange['startTime']),'endDateTime'=>date("Y-m-d H:i:s",$timeRange['endTime'])),
            'created',
              $paginator->getOffsetCount(),
              $paginator->getPerPageCount()
         );
        
        $LoginData="";

        if($tab=="trend"){
            $LoginData=$this->getLogService()->analysisLoginDataByTime($timeRange['startTime'],$timeRange['endTime']);
    
            $data=$this->fillAnalysisData($condition,$LoginData);           
        }

        $userIds = ArrayToolkit::column($loginDetail, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);

        $loginStartData=$this->getLogService()->searchLogs(array('action'=>"login_success"),'createdByAsc',0,1);

        if($loginStartData) $loginStartDate=date("Y-m-d",$loginStartData[0]['createdTime']);

        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:login",array(
            'loginDetail'=>$loginDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'users'=>$users,
            'loginStartDate'=>$loginStartDate,
            'dataInfo'=>$dataInfo,        
        ));
    }
    
    public function courseAction(Request $request,$tab)
    {       
        $data=array();
        $courseStartDate="";

        $condition=$request->query->all();
        $timeRange=$this->getTimeRange($condition);
    
        if(!$timeRange) {

              $this->setFlashMessage("danger","输入的日期有误!");
                        return $this->redirect($this->generateUrl('admin_operation_analysis_course', array(
                   'tab' => "trend",
                )));
        }

        $paginator = new Paginator(
                $request,
                $this->getCourseService()->searchCourseCount($timeRange),
             20
        );

        $courseDetail=$this->getCourseService()->searchCourses(
            $timeRange,
            '',
              $paginator->getOffsetCount(),
              $paginator->getPerPageCount()
         );
        $courseData="";

        if($tab=="trend"){
            $courseData=$this->getCourseService()->analysisCourseDataByTime($timeRange['startTime'],$timeRange['endTime']);
    
            $data=$this->fillAnalysisData($condition,$courseData);          
        }

        $userIds = ArrayToolkit::column($courseDetail, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);
        foreach ($courseDetail as $key => $course) {
            $categoryId = $this->getCourseCategoryRelService()->where(array("courseId"=>$course["id"]))->getField("categoryId");
            $courseDetail[$key]["categoryId"] = $categoryId; 
        }
        $categories = $this->getCategoryService()->findCategoriesByIds(ArrayToolkit::column($courseDetail, 'categoryId'));
        
        $courseStartData=$this->getCourseService()->searchCourses(array(),'createdTimeByAsc',0,1);

        if($courseStartData) $courseStartDate=date("Y-m-d",$courseStartData[0]['createdTime']);
        
        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:course",array(
            'courseDetail'=>$courseDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'categories'=>$categories,
            'data'=>$data,
            'users'=>$users,
            'courseStartDate'=>$courseStartDate,
            'dataInfo'=>$dataInfo,          
         ));
    }


    public function lessonAction(Request $request,$tab)
    {       
        $data=array();
        $lessonStartDate="";

        $condition=$request->query->all();
        $timeRange=$this->getTimeRange($condition);

        if(!$timeRange) {

              $this->setFlashMessage("danger","输入的日期有误!");
                        return $this->redirect($this->generateUrl('admin_operation_analysis_lesson', array(
                   'tab' => "trend",
                )));
        }

        $paginator = new Paginator(
                $request,
                $this->getCourseService()->searchLessonCount($timeRange),
             20
        );
        $lessonDetail=$this->getCourseService()->searchLessons(
            $timeRange,
            array('createdTime',"desc"),
              $paginator->getOffsetCount(),
              $paginator->getPerPageCount()
         );

        $lessonData="";

        if($tab=="trend"){
            $lessonData=$this->getCourseService()->analysisLessonDataByTime($timeRange['startTime'],$timeRange['endTime']);
    
            $data=$this->fillAnalysisData($condition,$lessonData);          
        }

        $courseIds = ArrayToolkit::column($lessonDetail, 'courseId');
       
        $courses=$this->getCourseService()->findCoursesByIds($courseIds);

        $userIds = ArrayToolkit::column($courses, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);

        $lessonStartData=$this->getCourseService()->searchLessons(array(),array('createdTime',"asc"),0,1);

        if($lessonStartData) $lessonStartDate=date("Y-m-d",$lessonStartData[0]['createdTime']);

        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:lesson",array(
            'lessonDetail'=>$lessonDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'courses'=>$courses,
            'users'=>$users,
            'lessonStartDate'=>$lessonStartDate,
            'dataInfo'=>$dataInfo,         
        ));
    }

    public function joinLessonAction(Request $request,$tab)
    {       
        $data=array();
        $joinLessonStartDate="";

        $condition=$request->query->all();
        $timeRange=$this->getTimeRange($condition);
        
        if(!$timeRange) {

              $this->setFlashMessage("danger","输入的日期有误!");
                        return $this->redirect($this->generateUrl('admin_operation_analysis_lesson_join', array(
                   'tab' => "trend",
                )));
        }
        $paginator = new Paginator(
                $request,
                $this->getOrderService()->searchOrderCount(array("paidStartTime"=>$timeRange['startTime'],"paidEndTime"=>$timeRange['endTime'],"status"=>"paid")),
             20
        );
       
        $JoinLessonDetail=$this->getOrderService()->searchOrders(
            array("paidStartTime"=>$timeRange['startTime'],"paidEndTime"=>$timeRange['endTime'],"status"=>"paid"),
            "latest",
              $paginator->getOffsetCount(),
              $paginator->getPerPageCount()
         );

        $JoinLessonData="";

        if($tab=="trend"){
            $JoinLessonData=$this->getOrderService()->analysisCourseOrderDataByTimeAndStatus($timeRange['startTime'],$timeRange['endTime'],"paid");
    
            $data=$this->fillAnalysisData($condition,$JoinLessonData);          
        }

        $courseIds = ArrayToolkit::column($JoinLessonDetail, 'targetId');

        $courses=$this->getCourseService()->findCoursesByIds($courseIds);

        $userIds = ArrayToolkit::column($JoinLessonDetail, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);
 
        $joinLessonStartData=$this->getOrderService()->searchOrders(array("status"=>"paid"),"early",0,1);

        foreach ($joinLessonStartData as $key) {
            $joinLessonStartDate=date("Y-m-d",$key['createdTime']);
        }         

        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:join-lesson",array(
            'JoinLessonDetail'=>$JoinLessonDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'courses'=>$courses,
            'users'=>$users,
            'joinLessonStartDate'=>$joinLessonStartDate,
            'dataInfo'=>$dataInfo,      
        ));
    }

    public function exitLessonAction(Request $request,$tab)
    {       
        $data=array();
        $exitLessonStartDate="";

        $condition=$request->query->all();
        $timeRange=$this->getTimeRange($condition);
        
        if(!$timeRange) {

              $this->setFlashMessage("danger","输入的日期有误!");
                        return $this->redirect($this->generateUrl('admin_operation_analysis_lesson_exit', array(
                   'tab' => "trend",
                )));
        }
        $paginator = new Paginator(
                $request,
                $this->getOrderService()->searchOrderCount(array("paidStartTime"=>$timeRange['startTime'],"paidEndTime"=>$timeRange['endTime'],"statusPaid"=>"paid","statusCreated"=>"created")),
                20
        );

        $exitLessonDetail=$this->getOrderService()->searchOrders(
            array("paidStartTime"=>$timeRange['startTime'],"paidEndTime"=>$timeRange['endTime'],"statusPaid"=>"paid","statusCreated"=>"created"),
            "latest",
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );

        $exitLessonData="";
        if($tab=="trend"){
            $exitLessonData=$this->getOrderService()->analysisExitCourseDataByTimeAndStatus($timeRange['startTime'],$timeRange['endTime']);
  
            $data=$this->fillAnalysisData($condition,$exitLessonData);          
        }

        $courseIds = ArrayToolkit::column($exitLessonDetail, 'targetId');

        $courses=$this->getCourseService()->findCoursesByIds($courseIds);

        $userIds = ArrayToolkit::column($exitLessonDetail, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);

        $cancelledOrders=$this->getOrderService()->findRefundsByIds(ArrayToolkit::column($exitLessonDetail, 'refundId'));

        $cancelledOrders=ArrayToolkit::index($cancelledOrders,'id');

        $exitLessonStartData=$this->getOrderService()->searchOrders(array("statusPaid"=>"paid","statusCreated"=>"created"),"early",0,1);

        foreach ($exitLessonStartData as $key) {
            $exitLessonStartDate=date("Y-m-d",$key['createdTime']);
        }

        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:exit-lesson",array(
            'exitLessonDetail'=>$exitLessonDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'courses'=>$courses,
            'users'=>$users,
            'exitLessonStartDate'=>$exitLessonStartDate,
            'cancelledOrders'=>$cancelledOrders,
            'dataInfo'=>$dataInfo,        
        ));
    }

    public function paidLessonAction(Request $request,$tab)
    {
        $data=array();
        $paidLessonStartDate="";

        $condition=$request->query->all();
        $timeRange=$this->getTimeRange($condition);

        if(!$timeRange) {

              $this->setFlashMessage("danger","输入的日期有误!");
                        return $this->redirect($this->generateUrl('admin_operation_analysis_lesson_paid', array(
                   'tab' => "trend",
                )));
        }
        $paginator = new Paginator(
                $request,
                $this->getOrderService()->searchOrderCount(array("paidStartTime"=>$timeRange['startTime'],"paidEndTime"=>$timeRange['endTime'],"status"=>"paid","amount"=>"0.00")),
             20
        );

        $paidLessonDetail=$this->getOrderService()->searchOrders(
            array("paidStartTime"=>$timeRange['startTime'],"paidEndTime"=>$timeRange['endTime'],"status"=>"paid","amount"=>"0.00"),
            "latest",
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );

        $paidLessonData="";
        if($tab=="trend"){
            $paidLessonData=$this->getOrderService()->analysisPaidCourseOrderDataByTime($timeRange['startTime'],$timeRange['endTime']);
    
            $data=$this->fillAnalysisData($condition,$paidLessonData);          
        }

        $courseIds = ArrayToolkit::column($paidLessonDetail, 'targetId');

        $courses=$this->getCourseService()->findCoursesByIds($courseIds);

        $userIds = ArrayToolkit::column($paidLessonDetail, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);
                
        $paidLessonStartData=$this->getOrderService()->searchOrders(array("status"=>"paid","amount"=>"0.00"),"early",0,1);

        foreach ($paidLessonStartData as $key) {
            $paidLessonStartDate=date("Y-m-d",$key['createdTime']);
        }

        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:paid-lesson",array(
            'paidLessonDetail'=>$paidLessonDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'courses'=>$courses,
            'users'=>$users,
            'paidLessonStartDate'=>$paidLessonStartDate,
            'dataInfo'=>$dataInfo,      
        ));
    }

    public function finishedLessonAction(Request $request,$tab)
    {
        $data=array();
        $finishedLessonStartDate="";

        $condition=$request->query->all();
        $timeRange=$this->getTimeRange($condition);

        if(!$timeRange) {

            $this->setFlashMessage("danger","输入的日期有误!");
                return $this->redirect($this->generateUrl('admin_operation_analysis_lesson_finished', array(
                'tab' => "trend",
            )));
        }
        $paginator = new Paginator(
                $request,
                $this->getCourseService()->searchLearnCount(array("startTime"=>$timeRange['startTime'],"endTime"=>$timeRange['endTime'],"status"=>"finished")),
             20
        );

        $finishedLessonDetail=$this->getCourseService()->searchLearns(
            array("startTime"=>$timeRange['startTime'],"endTime"=>$timeRange['endTime'],"status"=>"finished"),
            array("finishedTime","DESC"),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );

        $finishedLessonData="";
        if($tab=="trend"){
            $finishedLessonData=$this->getCourseService()->analysisLessonFinishedDataByTime($timeRange['startTime'],$timeRange['endTime']);

            $data=$this->fillAnalysisData($condition,$finishedLessonData);          
        }

        $courseIds = ArrayToolkit::column($finishedLessonDetail, 'courseId');

        $courses=$this->getCourseService()->findCoursesByIds($courseIds);

        $lessonIds = ArrayToolkit::column($finishedLessonDetail, 'lessonId');

        $lessons=$this->getCourseService()->findLessonsByIds($lessonIds);

        $userIds = ArrayToolkit::column($finishedLessonDetail, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);

        $finishedLessonStartData=$this->getCourseService()->searchLearns(array("status"=>"finished"),array("finishedTime","ASC"),0,1);

        if($finishedLessonStartData) $finishedLessonStartDate=date("Y-m-d",$finishedLessonStartData[0]['finishedTime']);
        
        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:finished-lesson",array(
            'finishedLessonDetail'=>$finishedLessonDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'courses'=>$courses,
            'lessons'=>$lessons,
            'users'=>$users,
            'finishedLessonStartDate'=>$finishedLessonStartDate,
            'dataInfo'=>$dataInfo,          
        ));
    }

    public function videoViewedAction(Request $request,$tab)
    {
        $data=array();
        $condition=$request->query->all();

        $timeRange=$this->getTimeRange($condition);

        $searchCondition = array(
            "fileType"=>'video',
            "startTime"=>$timeRange['startTime']
            ,"endTime"=>$timeRange['endTime']
        );

        if(!$timeRange) {
          $this->setFlashMessage("danger","输入的日期有误!");
            return $this->redirect($this->generateUrl('admin_operation_analysis_video_viewed', array(
               'tab' => "trend",
            )));
        }

        $paginator = new Paginator(
            $request,
            $this->getCourseService()->searchAnalysisLessonViewCount(
            $searchCondition,
            20
        ));

        $videoViewedDetail=$this->getCourseService()->searchAnalysisLessonView(
            $searchCondition,
            array("createdTime","DESC"),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );
        $videoViewedTrendData="";

        if($tab=="trend"){
            $videoViewedTrendData = $this->getCourseService()->analysisLessonViewDataByTime($timeRange['startTime'],$timeRange['endTime'],array("fileType"=>'video'));
    
            $data=$this->fillAnalysisData($condition,$videoViewedTrendData);            
        }

        $lessonIds = ArrayToolkit::column($videoViewedDetail, 'lessonId');
        $lessons=$this->getCourseService()->findLessonsByIds($lessonIds);
        $lessons=ArrayToolkit::index($lessons,'id');

        $userIds = ArrayToolkit::column($videoViewedDetail, 'userId');
        $users = $this->getUserService()->findUsersByIds($userIds);
        $users = ArrayToolkit::index($users,'id');

        $minCreatedTime = $this->getCourseService()->getAnalysisLessonMinTime('all');

        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:video-view",array(
            'videoViewedDetail'=>$videoViewedDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'lessons'=>$lessons,
            'users'=>$users,
            'dataInfo'=>$dataInfo,
            'minCreatedTime'=>date("Y-m-d",$minCreatedTime['createdTime']),
            'showHelpMessage' => 1
        ));
    }

    public function cloudVideoViewedAction(Request $request,$tab)
    {
        $data=array();
        $condition=$request->query->all();

        $timeRange=$this->getTimeRange($condition);
        
        $searchCondition = array(
            "fileType"=>'video',
            "fileStorage"=>'cloud',
            "startTime"=>$timeRange['startTime']
            ,"endTime"=>$timeRange['endTime']
        );
        
        if(!$timeRange) {
            $this->setFlashMessage("danger","输入的日期有误!");
            return $this->redirect($this->generateUrl('admin_operation_analysis_video_viewed', array(
            'tab' => "trend",
            )));
        }

        $paginator = new Paginator(
            $request,
            $this->getCourseService()->searchAnalysisLessonViewCount(
            $searchCondition,
            20
        ));

        $videoViewedDetail=$this->getCourseService()->searchAnalysisLessonView(
            $searchCondition,
            array("createdTime","DESC"),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );

        $videoViewedTrendData="";

        if($tab=="trend"){
            $videoViewedTrendData = $this->getCourseService()->analysisLessonViewDataByTime($timeRange['startTime'],$timeRange['endTime'],array("fileType"=>'video',"fileStorage"=>'cloud'));
    
            $data=$this->fillAnalysisData($condition,$videoViewedTrendData);            
        }

        $lessonIds = ArrayToolkit::column($videoViewedDetail, 'lessonId');
        $lessons=$this->getCourseService()->findLessonsByIds($lessonIds);
        $lessons=ArrayToolkit::index($lessons,'id');

        $userIds = ArrayToolkit::column($videoViewedDetail, 'userId');
        $users = $this->getUserService()->findUsersByIds($userIds);
        $users = ArrayToolkit::index($users,'id');
        $minCreatedTime = $this->getCourseService()->getAnalysisLessonMinTime('cloud');

        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:cloud-video-view",array(
            'videoViewedDetail'=>$videoViewedDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'lessons'=>$lessons,
            'users'=>$users,
            'dataInfo'=>$dataInfo,
            'minCreatedTime'=>date("Y-m-d",$minCreatedTime['createdTime']),
            'showHelpMessage' => 1
        ));
    }

    public function localVideoViewedAction(Request $request,$tab)
    {
        $data=array();
        $condition=$request->query->all();

        $timeRange=$this->getTimeRange($condition);
        
        $searchCondition = array(
            "fileType"=>'video',
            "fileStorage"=>'local',
            "startTime"=>$timeRange['startTime']
            ,"endTime"=>$timeRange['endTime']
        );
        
        if(!$timeRange) {
          $this->setFlashMessage("danger","输入的日期有误!");
            return $this->redirect($this->generateUrl('admin_operation_analysis_video_viewed', array(
               'tab' => "trend",
            )));
        }

        $paginator = new Paginator(
            $request,
            $this->getCourseService()->searchAnalysisLessonViewCount(
            $searchCondition,
            20
        ));

        $videoViewedDetail=$this->getCourseService()->searchAnalysisLessonView(
            $searchCondition,
            array("createdTime","DESC"),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );

        $videoViewedTrendData="";

        if($tab=="trend"){
            $videoViewedTrendData = $this->getCourseService()->analysisLessonViewDataByTime($timeRange['startTime'],$timeRange['endTime'],array("fileType"=>'video',"fileStorage"=>'local'));
    
            $data=$this->fillAnalysisData($condition,$videoViewedTrendData);            
        }

        $lessonIds = ArrayToolkit::column($videoViewedDetail, 'lessonId');
        $lessons=$this->getCourseService()->findLessonsByIds($lessonIds);
        $lessons=ArrayToolkit::index($lessons,'id');

        $userIds = ArrayToolkit::column($videoViewedDetail, 'userId');
        $users = $this->getUserService()->findUsersByIds($userIds);
        $users = ArrayToolkit::index($users,'id');
        $minCreatedTime = $this->getCourseService()->getAnalysisLessonMinTime('local');
        
        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:local-video-view",array(
            'videoViewedDetail'=>$videoViewedDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'lessons'=>$lessons,
            'users'=>$users,
            'dataInfo'=>$dataInfo,
            'minCreatedTime'=>date("Y-m-d",$minCreatedTime['createdTime']),
            'showHelpMessage' => 1
        ));
    }
    
    public function netVideoViewedAction(Request $request,$tab)
    {
        $data=array();
        $condition=$request->query->all();

        $timeRange=$this->getTimeRange($condition);
        
        $searchCondition = array(
            "fileType"=>'video',
            "fileStorage"=>'net',
            "startTime"=>$timeRange['startTime']
            ,"endTime"=>$timeRange['endTime']
        );
        
        if(!$timeRange) {
            $this->setFlashMessage("danger","输入的日期有误!");
            return $this->redirect($this->generateUrl('admin_operation_analysis_video_viewed', array(
            'tab' => "trend",
            )));
        }

        $paginator = new Paginator(
            $request,
            $this->getCourseService()->searchAnalysisLessonViewCount(
            $searchCondition,
            20
        ));

        $videoViewedDetail=$this->getCourseService()->searchAnalysisLessonView(
            $searchCondition,
            array("createdTime","DESC"),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );

        $videoViewedTrendData="";

        if($tab=="trend"){
            $videoViewedTrendData = $this->getCourseService()->analysisLessonViewDataByTime($timeRange['startTime'],$timeRange['endTime'],array("fileType"=>'video',"fileStorage"=>'net'));
    
            $data=$this->fillAnalysisData($condition,$videoViewedTrendData);            
        }

        $lessonIds = ArrayToolkit::column($videoViewedDetail, 'lessonId');
        $lessons=$this->getCourseService()->findLessonsByIds($lessonIds);
        $lessons=ArrayToolkit::index($lessons,'id');

        $userIds = ArrayToolkit::column($videoViewedDetail, 'userId');
        $users = $this->getUserService()->findUsersByIds($userIds);
        $users = ArrayToolkit::index($users,'id');
        $minCreatedTime = $this->getCourseService()->getAnalysisLessonMinTime('net');
        
        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:net-video-view",array(
            'videoViewedDetail'=>$videoViewedDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'lessons'=>$lessons,
            'users'=>$users,
            'minCreatedTime'=>date("Y-m-d",$minCreatedTime['createdTime']),
            'dataInfo'=>$dataInfo,
            'showHelpMessage' => 1
        ));
    }

    public function incomeAction(Request $request,$tab)
    {
        $data=array();
        $incomeStartDate="";

        $condition=$request->query->all();
        $timeRange=$this->getTimeRange($condition);
        
        if(!$timeRange) {

              $this->setFlashMessage("danger","输入的日期有误!");
                        return $this->redirect($this->generateUrl('admin_operation_analysis_income', array(
                   'tab' => "trend",
                )));
        }
        $paginator = new Paginator(
            $request,
            $this->getOrderService()->searchOrderCount(array("paidStartTime"=>$timeRange['startTime'],"paidEndTime"=>$timeRange['endTime'],"status"=>"paid","amount"=>"0.00")),
            20
        );

        $incomeDetail=$this->getOrderService()->searchOrders(
            array("paidStartTime"=>$timeRange['startTime'],"paidEndTime"=>$timeRange['endTime'],"status"=>"paid","amount"=>"0.00"),
            "latest",
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );

        $incomeData="";

        if($tab=="trend"){
            $incomeData=$this->getOrderService()->analysisAmountDataByTime($timeRange['startTime'],$timeRange['endTime']);
    
            $data=$this->fillAnalysisData($condition,$incomeData);          
        }

        $courseIds = ArrayToolkit::column($incomeDetail, 'targetId');

        $courses=$this->getCourseService()->findCoursesByIds($courseIds);

        $userIds = ArrayToolkit::column($incomeDetail, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);
        
        $incomeStartData=$this->getOrderService()->searchOrders(array("status"=>"paid","amount"=>"0.00"),"early",0,1);
                  
        foreach ($incomeStartData as $key) {
            $incomeStartDate=date("Y-m-d",$key['createdTime']);
        }

        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:income",array(
            'incomeDetail'=>$incomeDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'courses'=>$courses,
            'users'=>$users,
            'incomeStartDate'=>$incomeStartDate,
            'dataInfo'=>$dataInfo,    
        ));
    }

    public function courseIncomeAction(Request $request,$tab)
    {
        $data=array();
        $courseIncomeStartDate="";

        $condition=$request->query->all();
        $timeRange=$this->getTimeRange($condition);

        if(!$timeRange) {

            $this->setFlashMessage("danger","输入的日期有误!");
            return $this->redirect($this->generateUrl('admin_operation_analysis_course_income', array(
            'tab' => "trend",
            )));
        }

        $paginator = new Paginator(
            $request,
            $this->getOrderService()->searchOrderCount(array("paidStartTime"=>$timeRange['startTime'],"paidEndTime"=>$timeRange['endTime'],"status"=>"paid","targetType"=>"course","amount"=>"0.00")),
            20
        );

        $courseIncomeDetail=$this->getOrderService()->searchOrders(
            array("paidStartTime"=>$timeRange['startTime'],"paidEndTime"=>$timeRange['endTime'],"status"=>"paid","targetType"=>"course","amount"=>'0.00'),
            "latest",
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
         );

        $courseIncomeData="";

        if($tab=="trend"){
            $courseIncomeData=$this->getOrderService()->analysisCourseAmountDataByTime($timeRange['startTime'],$timeRange['endTime']);

            $data=$this->fillAnalysisData($condition,$courseIncomeData);            
        }

        $courseIds = ArrayToolkit::column($courseIncomeDetail, 'targetId');

        $courses=$this->getCourseService()->findCoursesByIds($courseIds);

        $userIds = ArrayToolkit::column($courseIncomeDetail, 'userId');

        $users = $this->getUserService()->findUsersByIds($userIds);

        $courseIncomeStartData=$this->getOrderService()->searchOrders(array("status"=>"paid","amount"=>"0.00","targetType"=>"course"),"early",0,1);

        foreach ($courseIncomeStartData as $key) {
            $courseIncomeStartDate=date("Y-m-d",$key['createdTime']);
        }

        $dataInfo=$this->getDataInfo($condition,$timeRange);
        return $this->render("OperationAnalysis:courseIncome",array(
            'courseIncomeDetail'=>$courseIncomeDetail,
            'paginator'=>$paginator,
            'tab'=>$tab,
            'data'=>$data,
            'courses'=>$courses,
            'users'=>$users,
            'courseIncomeStartDate'=>$courseIncomeStartDate,
            'dataInfo'=>$dataInfo,              
        ));
    }

    private function fillAnalysisUserSum($condition,$currentData)
    {
        $dates=$this->getDatesByCondition($condition);
        $currentData=ArrayToolkit::index($currentData,'date');
        $timeRange=$this->getTimeRange($condition);
        $userSumData=array();

        foreach ($dates as $key => $value) {
            $zeroData[] = array("date"=>$value,"count"=>0);
        }

        $userSumData=$this->getUserService()->analysisUserSumByTime($timeRange['endTime']);
      
        if($userSumData){
            $countTmp = $userSumData[0]["count"];
            foreach ($zeroData as $key => $value) {
                $date = $value['date'];
                if (array_key_exists($date,$currentData)){
                    $zeroData[$key]['count'] = $currentData[$date]['count'];
                    $countTmp = $currentData[$date]['count'];
                } else {
                    $zeroData[$key]['count'] = $countTmp;
                }
            }
        }
       
        return json_encode($zeroData);
    }

    private function fillAnalysisCourseSum($condition,$currentData)
    {
        $dates=$this->getDatesByCondition($condition);
        $currentData=ArrayToolkit::index($currentData,'date');
        $timeRange=$this->getTimeRange($condition);
        $zeroData=array();

        foreach ($dates as $key => $value) {
            $zeroData[] = array("date"=>$value,"count"=>0);
        }

        $courseSumData=$this->getCourseService()->analysisCourseSumByTime($timeRange['endTime']);
        if($courseSumData){
            $countTmp = $courseSumData[0]["count"];
            foreach ($zeroData as $key => $value) {
                if($value["date"]<$courseSumData[0]["date"]){
                    $countTmp = 0;
                }
                $date = $value['date'];
                if (array_key_exists($date,$currentData)){
                    $zeroData[$key]['count'] = $currentData[$date]['count'];
                    $countTmp = $currentData[$date]['count'];
                } else {
                    $zeroData[$key]['count'] = $countTmp;
                }
            }
        }
       
        return json_encode($zeroData);
    }

    private function fillAnalysisData($condition,$currentData)
    {
        $dates=$this->getDatesByCondition($condition);

        foreach ($dates as $key => $value) {
            $zeroData[] = array("date"=>$value,"count"=>0);
        }

        $currentData=ArrayToolkit::index($currentData,'date');

        $zeroData=ArrayToolkit::index($zeroData,'date');

        $currentData=array_merge($zeroData,$currentData );

        foreach ($currentData as $key => $value) {
            $data[]=$value;
        }

        return json_encode($data);
    }

    private function getDatesByCondition($condition)
    {   
        $timeRange=$this->getTimeRange($condition);

        $dates=$this->makeDateRange($timeRange['startTime'],$timeRange['endTime']-24*3600);

        return $dates;
    }

    private function getDataInfo($condition,$timeRange)
    {
        $datainfo = array(
            'currentMonthStart'=>date("Y-m-d",strtotime(date("Y-m",time()))),
            'currentMonthEnd'=>date("Y-m-d",strtotime(date("Y-m-d",time()))),
            'lastMonthStart'=>date("Y-m-d",strtotime(date("Y-m", strtotime("-1 month")))),
            'lastMonthEnd'=>date("Y-m-d",strtotime(date("Y-m",time()))-24*3600),
            'lastThreeMonthsStart'=>date("Y-m-d",strtotime(date("Y-m", strtotime("-2 month")))),
            'lastThreeMonthsEnd'=>date("Y-m-d",strtotime(date("Y-m-d",time()))),
            'analysisDateType'=>$condition["analysisDateType"],);

        if(!empty($timeRange) && array_key_exists("startTime",$timeRange)){
            $datainfo['startTime'] = date("Y-m-d",$timeRange['startTime']);
        }
        if(!empty($timeRange) && array_key_exists("endTime",$timeRange)){
            $datainfo['endTime'] = date("Y-m-d",$timeRange['endTime']);
        }

        return $datainfo;
    }

    private function getTimeRange($fields)
    {
        if(isset($fields['startTime'])&&isset($fields['endTime'])&&$fields['startTime']!=""&&$fields['endTime']!="")
        {   
            if($fields['startTime']>$fields['endTime']) return false;
            return array('startTime'=>strtotime($fields['startTime']),'endTime'=>(strtotime($fields['endTime'])+24*3600));
        }

        return array('startTime'=>strtotime(date("Y-m",time())),'endTime'=>strtotime(date("Y-m-d",time()+24*3600)));
    }

    private function makeDateRange($startTime, $endTime)
    {
        $dates = array();

        $currentTime = $startTime;
        while ( true)  {
            if ($currentTime > $endTime) {
                break;
            }
            $currentDate = date('Y-m-d', $currentTime);
            $dates[] = $currentDate;

            $currentTime = $currentTime + 3600 * 24;
        }
        return $dates;
    }

    protected function getLogService()
    {
        return createService('System.LogService');
    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }

    private function getCategoryService()
    {
        return createService('Taxonomy.CategoryService');
     }

    private function getOrderService()
    {
        return createService('Order.OrderService');
    }
      protected function getMessageService() {
        return createService('Message.MsgServiceModel');
    }
    
    protected function getCourseCategoryRelService() {
        return createService('Course.CourseCategoryRelServiceModel');
    }

}