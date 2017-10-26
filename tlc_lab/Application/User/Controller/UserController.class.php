<?php
namespace User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;
use Topxia\Common\StringToolkit;

class UserController extends \Home\Controller\BaseController
{

    public function headerBlockAction($user)
    {
        $userProfile = $this->getUserService()->getUserProfile($user['id']);
        $userProfile = $userProfile ? : array();
        $user = array_merge($user, $userProfile);

        return $this->render('User:header-block', array(
            'user' => $user,
        ));
    }

    public function headerUserAction() {
        $user = $this->getCurrentUser();
        return $this->render('Partner:header-user', array(
            'user' => $user,
        ));

    }

    public function getTeacherInfoAction(Request $request){
        if ($request->getMethod() == 'POST'){
            $data = $request->request->all();
            $userNum = isset($data['num']) ? trim($data['num']) : null;
            if(empty($userNum)){
                return $this->createJsonResponse(array('status' => false,'message' => '教师职工号不能为空'));
            }else{
                $user = $this->getUserService()->getUserByUserNum($userNum);
                if(empty($user)){
                    return $this->createJsonResponse(array('status' => false,'message' => '该教师用户不存在，请检查教职工号是否输入有误'));
                }

                return $this->createJsonResponse(array('status' => true,'message' => '','data'=>array('name'=>$user['nickname'],'uid'=>$user['id'])));
            }
        }
    }

    public function showAction(Request $request, $id)
    {   
        $user = $this->getUserService()->getUser($id);
        if (empty($user)) {
            throw $this->createNotFoundException();
        }

        if(isGranted("ROLE_ADMIN") || isGranted("ROLE_TEACHER")) {
            return $this->_teachAction($user);
        }

        return $this->_learnAction($user);
    }

    public function learnAction(Request $request, $id)
    {
        $user = $this->getUserService()->getUser($id);
        if (empty($user)) {
            throw $this->createNotFoundException();
        }

        if(in_array('ROLE_TEACHER',$user['roles'])){
            return $this->_teachAction($user);
        }
        return $this->_learnAction($user);
    }

    public function teachAction(Request $request, $id)
    {
        $user = $this->getUserService()->getUser($id);
        if (empty($user)) {
            throw $this->createNotFoundException();
        }
        if(!in_array('ROLE_TEACHER',$user['roles'])){
            return $this->_learnAction($user);
        }
        return $this->_teachAction($user);
    }

    public function learningAction(Request $request, $id)
    {
        $user = $this->tryGetUser($id);
        $classrooms=array();

        $studentClassrooms=$this->getClassroomService()->searchMembers(array('role'=>'student','userId'=>$user['id']),array('createdTime','desc'),0,9999);
        $auditorClassrooms=$this->getClassroomService()->searchMembers(array('role'=>'auditor','userId'=>$user['id']),array('createdTime','desc'),0,9999);

        $classrooms=array_merge($studentClassrooms,$auditorClassrooms);

        $classroomIds=ArrayToolkit::column($classrooms,'classroomId');

        $classrooms=$this->getClassroomService()->findClassroomsByIds($classroomIds);

        foreach ($classrooms as $key => $classroom) {
            if (empty($classroom['teacherIds'])) {
                $classroomTeacherIds=array();
            }else{
                $classroomTeacherIds=$classroom['teacherIds'];
            }

            $teachers = $this->getUserService()->findUsersByIds($classroomTeacherIds);
            $classrooms[$key]['teachers']=$teachers;
        }

        $members=$this->getClassroomService()->findMembersByUserIdAndClassroomIds($user['id'], $classroomIds);

        return $this->render("User:classroom-learning",array(
            'classrooms'=>$classrooms,
            'members'=>$members,
            'user'=>$user,
        )); 
    }

    public function teachingAction(Request $request, $id)
    {
        
        $user = $this->tryGetUser($id);

        $classrooms=array();
        $classroomObj = $this->getClassroomService();
        $teacherClassrooms = $classroomObj->searchMembers(array('role'=>'teacher','userId'=>$user['id']),array('createdTime','desc'),0,9999);
        $headTeacherClassrooms = $classroomObj->searchMembers(array('role'=>'headTeacher','userId'=>$user['id']),array('createdTime','desc'),0,9999);

        $classrooms = array_merge($teacherClassrooms,$headTeacherClassrooms);

        $classroomIds = ArrayToolkit::column($classrooms,'classroomId');

        $classrooms = $classroomObj->findClassroomsByIds($classroomIds);

        $members = $classroomObj->findMembersByUserIdAndClassroomIds($user['id'], $classroomIds);
        
        foreach ($classrooms as $key => $classroom) {
            if (empty($classroom['teacherIds'])) {
                $classroomTeacherIds=array();
            }else{
                $classroomTeacherIds=$classroom['teacherIds'];
            }

            $teachers = $this->getUserService()->findUsersByIds($classroomTeacherIds);
            $classrooms[$key]['teachers']=$teachers;
        }

        return $this->render('User:classroom-teaching', array(
            'classrooms'=>$classrooms,
            'members'=>$members,
            'user'=>$user,
            ));
    }

    public function favoritedAction(Request $request, $id)
    {
        $user = $this->tryGetUser($id);
        $paginator = new Paginator(
            $this->get('request'),
            $this->getCourseService()->findUserFavoritedCourseCount($user['id']),
            12
        );

        $courses = $this->getCourseService()->findUserFavoritedCourses(
            $user['id'],
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return $this->render('User:courses', array(
            'user' => $user,
            'courses' => $courses,
            'paginator' => $paginator,
            'type' => 'favorited',
            'action' => ACTION_NAME
        ));
    }

    public function remindCounterAction(Request $request)
    {
        $user = $this->getCurrentUser();
        $counter = array('newMessageNum' => 0, 'newNotificationNum' => 0);
        if ($user->isLogin()) {
            $counter['newMessageNum'] = $user['newMessageNum'];
            $counter['newNotificationNum'] = $user['newNotificationNum'];
        }
        return $this->createJsonResponse($counter);
    }

    public function checkPasswordAction(Request $request)
    {
        $password = $request->query->get('value');
        $currentUser = $this->getCurrentUser();

        if (!$currentUser->isLogin()) {
            $response = array('success' => false, 'message' => '请先登入');
        }

        if (!$this->getUserService()->verifyPassword($currentUser['id'], $password)) {
            $response = array('success' => false, 'message' => '输入的密码不正确');
        }else{
            $response = array('success' => true, 'message' => '');
        }
        return $this->createJsonResponse($response);
    }

    protected function getUserService()
    {
        return createService('User.UserService');
    }

    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getThreadService()
    {
        return createService('Course.ThreadService');
    }

    protected function getNoteService()
    {
        return createService('Course.NoteService');
    }

    protected function getNotificationService()
    {
        return createService('User.NotificationService');
    }

    private function tryGetUser($id)
    {
        $user = $this->getUserService()->getUser($id);
        if (empty($user)) {
            throw $this->createNotFoundException();
        }
        return $user;
    }

    private function _learnAction($user)
    {
        $courseObj = $this->getCourseService();
        $paginator = new Paginator(
            $this->get('request'),
            $courseObj->findUserLearnCourseCount($user['id']),
            12
        );
        $courses = $courseObj->findUserLearnCourses(
            $user['id'],
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        return $this->render('User:courses', array(
            'user' => $user,
            'courses' => $courses,
            'paginator' => $paginator,
            'type' => 'learn',
            'action' => ACTION_NAME
        ));
    }

    private function _teachAction($user)
    {   
        $courseObj = $this->getCourseService();
        
        //管理员和老师不是一套课程列表
        if(isGranted("ROLE_ADMIN")){
            $count =  $courseObj->getCurrentWebCourseCount();
        }else{
            $count = $courseObj->findUserTeachCourseCount($user['id']);
        }
        
        $paginator = new Paginator(
            $this->get('request'),
            $count,
            12
        );
        
        $setCode = true; //不忽略webCode
        if(isGranted("ROLE_ADMIN")){
            $courses = $courseObj->getCurrentWebAllCourse(
                $paginator->getOffsetCount(),
                $paginator->getPerPageCount(),
                false,
                $setCode
            );
        }else{
            $courses = $courseObj->findUserTeachCourses(
                $user['id'],
                $paginator->getOffsetCount(),
                $paginator->getPerPageCount(),
                false,
                $setCode
            );
        }

        return $this->render('User:courses', array(
            'user' => $user,
            'courses' => $courses,
            'paginator' => $paginator,
            'type' => 'teach',
            'action' => ACTION_NAME
        ));
    }
}