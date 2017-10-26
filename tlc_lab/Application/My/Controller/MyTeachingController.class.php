<?php

namespace My\Controller;

use Imagine\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

//教师才能访问的控制器
class MyTeachingController extends \Home\Controller\BaseController {

    public function _initialize() {
        $app = $this->getCurrentUser();
        if (!$app->isLogin() && ACTION_NAME != 'FrontDevAction')
            $this->redirect('User/Signin/index');
    }

    public function indexAction(){
        return $this->redirect('My/MyTeaching/courses');
    }

    /**
     * @edit author fubaosheng 2015-11-16 
     * 管理员和老师管理的课程列表
     * 管理员本学校所有的课程(ROLE_SUPER_ADMIN，ROLE_ADMIN)
     * 老师所教的课程(ROLE_TEACHER&&course_member里courseId的Teacher)
     */
    public function coursesAction(Request $request) {
        $status = $request->get('status') ? $request->get('status') : false;

        $user = $this->getCurrentUser();
        if ((!$user->isTeacher()) && (!isGranted("ROLE_ADMIN"))) {
            return $this->createMessageResponse('error', '您不是老师或管理员，不能查看此页面！');
        }

        //管理员和老师不是一套课程列表
        if (isGranted("ROLE_ADMIN")) {
            $count = $this->getCourseService()->getCurrentWebCourseCount();
        } else {
            $count = $this->getCourseService()->findUserTeachCourseCount($user['id'], $status);
        }

        $paginator = new Paginator(
                $this->getRequest(), $count, 12
        );

        $setCode = false; //不忽略webCode
        if (isGranted("ROLE_ADMIN")) {
            $courses = $this->getCourseService()->getCurrentWebAllCourse(
                    $paginator->getOffsetCount(), $paginator->getPerPageCount(), $status
            );
        } else {
            $courses = $this->getCourseService()->findUserTeachCourses(
                    $user['id'], $paginator->getOffsetCount(), $paginator->getPerPageCount(), $status, $setCode
            );
        }

        return $this->render('MyTeaching:teaching', array(
                    'courses' => $courses,
                    'paginator' => $paginator,
                    'my' => $this->getCurrentUser(),
        ));
    }

    /**
     * 查看申请记录
     * @author fubaosheng 2015-11-27
     */
    public function courseApplyRecordAction(Request $request) {
        $user = $this->getCurrentUser();
        if ((!$user->isTeacher()) && (!isGranted("ROLE_ADMIN"))) {
            return $this->createMessageResponse('error', '您不是老师或管理员，不能查看此页面！');
        }

        $status = $request->get('status');
        if (is_numeric($status) && in_array($status, array(0, 1, 2, 3)))
            $status = intval($status);
        else
            $status = -1;

        $paramArr = array();
        $paramArr['status'] = $status;
        if (!isGranted("ROLE_ADMIN"))
            $paramArr['uid'] = $user['id'];

        $count = $this->getCourseService()->courseApplyRecordCount($paramArr);
        $paginator = new Paginator(
                $this->getRequest(), $count, 8
        );
        $paramArr['start'] = $paginator->getOffsetCount();
        $paramArr['limit'] = $paginator->getPerPageCount();
        $records = $this->getCourseService()->courseApplyRecord($paramArr);

        return $this->render('MyTeaching:record-list', array(
                    'records' => $records,
                    'paginator' => $paginator,
                    'status' => $status
        ));
    }

    /**
     * 课程申请记录的信息
     * @author fubaosheng 2015-11-30
     */
    public function courseApplyRecordInfoAction(Request $request) {
        $user = $this->getCurrentUser();
        if ((!$user->isTeacher()) && (!isGranted("ROLE_ADMIN"))) {
            return $this->createMessageResponse('error', '您不是老师或管理员，不能查看此页面！');
        }
        $id = $request->get('applyId') ? intval($request->get('applyId')) : 0;
        $applyRecordInfo = $this->getCourseService()->courseApplyRecordInfo($user['id'], $id);
        if (empty($applyRecordInfo))
            return $this->createMessageResponse('error', '此申请记录不存在！');
        if ($applyRecordInfo["status"] == 1) {
            $failType = C("COUSE_APPLY_FAIL_TYPE");
            $applyRecordInfo["fail"] = $failType[$applyRecordInfo["failType"]];
        }
        return $this->render('MyTeaching:record-info', array(
                    'applyRecordInfo' => $applyRecordInfo
        ));
    }

    /**
     * 取消申请
     * @author fubaosheng 2015-12-01
     */
    public function courseRemoveApplyAction(Request $request) {
        $user = $this->getCurrentUser();
        if ((!$user->isTeacher()) && (!isGranted("ROLE_ADMIN"))) {
            $this->ajaxReturn(array("data" => "NO_POWER", "info" => "您不是老师或管理员，不能取消申请！", "status" => 0));
        }
        $id = $request->get('applyId') ? intval($request->get('applyId')) : 0;
        $applyRecordInfo = $this->getCourseService()->courseApplyRecordInfo($user['id'], $id);
        if (empty($applyRecordInfo))
            $this->ajaxReturn(array("data" => "NO_EXISTS", "info" => "该条申请记录不存在！", "status" => 0));
        if (intval($applyRecordInfo["status"]) != 0)
            $this->ajaxReturn(array("data" => "ERROR_STATUS", "info" => "该条申请记录的状态已经改变！", "status" => 0));
        $r = $this->getCourseService()->courseRemoveApply($id, $applyRecordInfo["courseId"], $user["id"]);
        if ($r)
            $this->ajaxReturn(array("data" => "SUCCESS", "info" => "取消申请成功！", "status" => 1));
        else
            $this->ajaxReturn(array("data" => "FAIL", "info" => "取消申请失败！", "status" => 0));
    }

    public function coursesTestAction(Request $request) {

        return $this->render('MyTeachingTest:teaching', array(
                    'courses' => array(),
                    'paginator' => array(),
                    'live_course_enabled' => 0
        ));
        die;

        $status = $request->get('status') ? $request->get('status') : false;

        $user = $this->getCurrentUser();
        if (!$user->isTeacher()) {
            return $this->createMessageResponse('error', '您不是老师，不能查看此页面！');
        }

        $paginator = new Paginator(
                $this->getRequest(), $this->getCourseService()->findUserTeachCourseCount($user['id'], $status), 12
        );
        $courses = $this->getCourseService()->findUserTeachCourses(
                $user['id'], $paginator->getOffsetCount(), $paginator->getPerPageCount(), $status
        );

        $courseSetting = $this->getSettingService()->get('course', array());

        return $this->render('MyTeachingTest:teaching', array(
                    'courses' => $courses,
                    'paginator' => $paginator,
                    'live_course_enabled' => empty($courseSetting['live_course_enabled']) ? 0 : $courseSetting['live_course_enabled']
        ));
    }

    /**
     * 前端页面开发
     */
    public function frontDevAction() {
        $pageName = isset($_GET['pageName']) && $_GET['pageName'] ? trim($_GET['pageName']) : '';

        if ($pageName == '')
            die("lost param:     ?pageName=xxxx");

        return $this->render('FrontDev:' . $pageName, array(
                    'pageName' => $pageName
        ));
    }

    public function classroomsAction(Request $request) {
        $user = $this->getCurrentUser();

        if (!$user->isTeacher()) {
            return $this->createMessageResponse('error', '您不是老师，不能查看此页面！');
        }

        $classrooms = array();
        $teacherClassrooms = $this->getClassroomService()->searchMembers(array('role' => 'teacher', 'userId' => $user->id), array('createdTime', 'desc'), 0, 9999);
        $headTeacherClassrooms = $this->getClassroomService()->searchMembers(array('role' => 'headTeacher', 'userId' => $user->id), array('createdTime', 'desc'), 0, 9999);

        $classrooms = array_merge($teacherClassrooms, $headTeacherClassrooms);

        $classroomIds = ArrayToolkit::column($classrooms, 'classroomId');

        $classrooms = $this->getClassroomService()->findClassroomsByIds($classroomIds);

        $members = $this->getClassroomService()->findMembersByUserIdAndClassroomIds($user->id, $classroomIds);

        foreach ($classrooms as $key => $classroom) {

            $courses = $this->getClassroomService()->findCoursesByClassroomId($classroom['id']);
            $courseIds = ArrayToolkit::column($courses, 'courseId');

            $coursesCount = count($courses);

            $classrooms[$key]['coursesCount'] = $coursesCount;

            $studentCount = $this->getClassroomService()->searchMemberCount(array('role' => 'student', 'classroomId' => $classroom['id'], 'startTimeGreaterThan' => strtotime(date('Y-m-d'))));
            $auditorCount = $this->getClassroomService()->searchMemberCount(array('role' => 'auditor', 'classroomId' => $classroom['id'], 'startTimeGreaterThan' => strtotime(date('Y-m-d'))));


            $allCount = $studentCount + $auditorCount;

            $classrooms[$key]['allCount'] = $allCount;

            $todayTimeStart = strtotime(date("Y-m-d", time()));
            $todayTimeEnd = strtotime(date("Y-m-d", time() + 24 * 3600));
            $todayFinishedLessonNum = $this->getCourseService()->searchLearnCount(array("targetType" => "classroom", "courseIds" => $courseIds, "startTime" => $todayTimeStart, "endTime" => $todayTimeEnd, "status" => "finished"));

            $threadCount = $this->getThreadService()->searchThreadCount(array('targetType' => 'classroom', 'targetId' => $classroom['id'], 'type' => 'discussion', "startTime" => $todayTimeStart, "endTime" => $todayTimeEnd, "status" => "open"));

            $classrooms[$key]['threadCount'] = $threadCount;

            $classrooms[$key]['todayFinishedLessonNum'] = $todayFinishedLessonNum;
        }

        return $this->render('MyTeaching:classroom', array(
                    'classrooms' => $classrooms,
                    'members' => $members,
        ));
    }

    public function stuThreadMenuAction(Request $request) {
        $threadSerObj = $this->getThreadService();
        $user = $this->getCurrentUser();
        $courseId = $request->query->get('courseId');

        $counts = array();
        $conditions['fromUId'] = $user['id'];
        $conditions['type'] = 'lock';
        $counts['lockedCounts'] = $threadSerObj->searchThreadEventCount($conditions);

        $conditions = array();
        $conditions['toUId'] = $user['id'];
        $conditions['type'] = 'append';
        $counts['appendCounts'] = $threadSerObj->searchThreadEventCount($conditions);

        $conditions = array();
        $conditions['toUId'] = $user['id'];
        $conditions['type'] = 'reply';
        $counts['replyCounts'] = $threadSerObj->searchThreadEventCount($conditions);

        $conditions = array();
        $myCourseIds = $this->getCourseService()->getUserCourseId($user['id']);

        $conditions = array();
        $teacherCourseIds = $this->getThreadService()->getTeacherCourseId($user['id']);
        $courseIds = array_unique(array_merge($myCourseIds, $teacherCourseIds));
        $conditions['courseIds'] = $courseIds;
        $conditions['isClosed'] = 0;
        $conditions['teacherPostNum'] = array('eq', 0);
        $counts['unansweredCounts'] = $this->getThreadService()->searchThreadCountInCourseIds($conditions);

        return $this->render('MyTeaching:stu-thread-menu', array(
                    'counts' => $counts,
        ));
    }

    public function threadsAction(Request $request, $type) {
        $user = $this->getCurrentUser();
        $courseId = $request->query->get('courseId');

        #判断是否是创建课程老师
        if (!$this->getThreadService()->getTeacher($user['id']) && !$user->isTeacher() && !$user->isAdmin()) {
            return $this->createMessageResponse('error', '您不是老师，不能查看此页面！');
        } else {
            $privilege = TRUE;
        }

        #删除过期数据
        $lockConditions = array(
            'fromUId' => $user['id'],
            'type' => 'lock'
        );
        $lockThreads = $this->getThreadService()->searchThreadsEvent(
                $lockConditions, array('createdTime', 'DESC'), $this->getThreadService()->searchThreadEventCount($lockConditions)
        );
        foreach ($lockThreads as $thread) {
            if (time() >= $thread['unlockTime']) {
                $conditions['fromUId'] = $user['id'];
                $conditions['threadId'] = $thread['id'];
                $conditions['type'] = 'lock';
                $this->getThreadService()->deleteThreadEvent($conditions);
            }
        }

        #所属课程
        if($user->isAdmin()){
            $courseIds = $myCourse = $this->getCourseService()->getAllCourseId();   //管理员查询所有的课程
        }else {
            $courseIds = $myCourse = $this->getCourseService()->getUserCourseId($user['id']); //我创建的课程
        }

//        $myTeachingCourseCount = $this->getCourseService()->findUserTeachCourseCount($user['id'], true);
        $typeArr = array('unanswered', 'closed');
        if (!in_array($type, $typeArr)) {
            $type = 'unanswered';
        }

        if ($type == 'unanswered') {
            $answerTeaCourseIds = $this->getThreadService()->getTeacherCourseId($user['id']); //答疑老师课程
            $courseIds = array_unique(array_merge($courseIds, $answerTeaCourseIds));
            $conditions['teacherPostNum'] = array('eq', 0);
        }

        if ($type == 'closed') {
            $conditions['isClosed'] = 1;
        }

        $courseList = $this->getCourseService()->findCoursesByIds($courseIds);

        if (!empty($courseId)) {
            $courseIds = explode(',', $courseId);
        }

        if (empty($courseIds)) {
            $threads = array();
        } else {
            $conditions['courseIds'] = $courseIds;
            $paginator = new Paginator(
                    $request, $this->getThreadService()->searchThreadCountInCourseIds($conditions), 5
            );
            $threads = $this->getThreadService()->searchThreadInCourseIds(
                    $conditions, 'createdNotStick', $paginator->getOffsetCount(), $paginator->getPerPageCount()
            );
        }

        $threads = $this->decorateThread($threads, $myCourse);
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($threads, 'latestPostUserId'));
        $courses = $this->getCourseService()->findCoursesByIds(ArrayToolkit::column($threads, 'courseId'));
        $lessons = $this->getCourseService()->findLessonsByIds(ArrayToolkit::column($threads, 'lessonId'));

        return $this->render('MyTeaching:threads', array(
                    'courseId' => $courseId,
                    'paginator' => $paginator,
                    'threads' => $threads,
                    'users' => $users,
                    'courses' => $courses,
                    'lessons' => $lessons,
                    'type' => $type,
                    'courseList' => $courseList,
                    'privilege' => $privilege
        ));
    }

    public function postedThreadAction(Request $request, $type) {
        $user = $this->getCurrentUser();
        $courseId = $request->query->get('courseId');
        $data = $request->query->all();

        if (!empty($data['courseId'])) {
            $conditions['courseId'] = intval($data['courseId']);
        }
        if (!empty($data['startTime']) && !empty($data['endTime'])) {
            $conditions['startTime'] = strtotime($data['startTime']);
            $conditions['endTime'] = strtotime($data['endTime']);
        }

        $conditions['userId'] = $user['id'];

        $paginator = new Paginator(
                $request, $this->getThreadService()->searchPostManageCounts($conditions), 5
        );

        $threads = $this->getThreadService()->searchPostsManage($conditions, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount());
        $allThread = $this->getThreadService()->searchPostsManage($conditions, array('createdTime', 'DESC'), 0, $this->getThreadService()->searchPostManageCounts($conditions));
        $threadIds = ArrayToolkit::column($allThread, 'id');

        $dataStatistics = $this->getThreadService()->getDataStatisticsByTeacher($user['id'], $threadIds);
//        print_r($dataStatistics);die;
        //$this->getDataStatistics(array('courseId' => $courseId, 'userId' => $user['id'], 'threadIds' => ArrayToolkit::column($allThread, 'id')));
        #增加评分项
        $conditions = array();
        foreach ($threads as $k => $thread) {
            $conditions['courseId'] = $thread['courseId'];
            $conditions['threadId'] = $thread['id'];
            $conditions['teacherId'] = $user['id'];
            $comment = $this->getThreadService()->findThreadComment($conditions);
            $threads[$k]['satisficing'] = $comment['satisficing'] ? : 0;
        }
        $threads = $this->decorateThread($threads);
        $courseList = $this->getCourseByThread($user['id']);

        return $this->render('MyTeaching:threads', array(
                    'courseId' => $courseId,
                    'paginator' => $paginator,
                    'threads' => $threads,
                    'users' => $users,
                    'courses' => $courses,
                    'lessons' => $lessons,
                    'type' => $type,
                    'courseList' => $courseList,
                    'privilege' => $privilege,
                    'data' => $data,
                    'dataStatistics' => $dataStatistics
        ));
    }

    protected function getCourseByThread($userId) {
        #所属课程
        $myCourseIds = $this->getCourseService()->getUserCourseId($userId);
        $teacherCourseIds = $this->getThreadService()->getTeacherCourseId($userId);
        $courseIds = array_unique(array_merge($myCourseIds, $teacherCourseIds));
        $courseList = $this->getCourseService()->findCoursesByIds($courseIds);
        return $courseList;
    }

    public function relatedThreadAction(Request $request, $type) {
        $threadSerObj = $this->getThreadService();
        $user = $this->getCurrentUser();
        $courseId = $request->query->get('courseId');

        if (!empty($courseId)) {
            $conditions['courseId'] = intval($courseId);
        }
        $typeArr = array('locked', 'append', 'reply');
        if (!in_array($type, $typeArr)) {
            E($type . '类型不存在！');
        }
        switch ($type) {
            case 'reply':
                $conditions['toUId'] = $user['id'];
                $conditions['type'] = 'reply';
                break;
            case 'locked':
                $conditions['fromUId'] = $user['id'];
                $conditions['type'] = 'lock';
                break;
            case 'append':
                $conditions['toUId'] = $user['id'];
                $conditions['type'] = 'append';
                break;
        }

        $paginator = new Paginator(
                $request, $threadSerObj->searchThreadEventCount($conditions), 5
        );

        $threads = $threadSerObj->searchThreadsEvent($conditions, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount());

        #装饰锁定剩余时间
        if ($type == 'locked') {
            foreach ($threads as $key => &$thread) {
                $thread['timeRemaining'] = $thread['unlockTime'] - time();
                if ($thread['timeRemaining'] < 0) {
                    unset($thread);
                }
            }
        }


        $threads = $this->decorateThread($threads);
        $courseList = $this->getCourseByThread($user['id']);
        return $this->render('MyTeaching:threads', array(
                    'courseId' => $courseId,
                    'paginator' => $paginator,
                    'threads' => $threads,
                    'users' => $users,
                    'courses' => $courses,
                    'lessons' => $lessons,
                    'type' => $type,
                    'courseList' => $courseList,
                    'privilege' => $privilege
        ));
    }

    #放弃抢答

    public function giveupGrabAnswerAction(Request $request) {
        $user = $this->getCurrentUser();
        $courseId = $request->query->get('courseId');
        $threadId = $request->query->get('id');
        $fields['lockedUId'] = 0;
        $fields['unlockTime'] = 0;
        $this->getThreadService()->updateLockedThread($courseId, $threadId, $fields, FALSE);

        $fields = array();
        $fields['courseId'] = $courseId;
        $fields['threadId'] = $threadId;
        $fields['teacherId'] = $user['id'];
        $this->getThreadService()->addThreadGiveup($fields);
    }

    private function decorateThread($threads, $couserIds = false) {
        $tree = array();
        if (empty($threads)) {
            return array();
        }
        foreach ($threads as $thread) {
            if (is_array($couserIds)) {
                $thread['isMyCourse'] = in_array($thread['courseId'], $couserIds) ? true : false;
            }
            $course = $this->getCourseService()->getCourseFind($thread['courseId']);
            $thread['courseName'] = $course['title'];
            $user = $this->getUserService()->getUser($thread['userId']);
            $thread['userName'] = $user['nickname'];

            $tree[] = $thread;
        }
        return $tree ? : array();
    }

    protected function getCourseThreadService() {
        return createService('Course.ThreadService');
    }

    protected function getThreadService() {
        return createService('Thread.ThreadService');
    }

    protected function getUserService() {
        return createService('User.UserService');
    }

    protected function getCourseService() {
        return createService('Course.CourseServiceModel');
    }

    protected function getSettingService() {
        return createService('System.SettingService');
    }

    protected function getClassroomService() {
        return createService('Classroom.ClassroomService');
    }

}