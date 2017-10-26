<?php
namespace My\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Topxia\WebBundle\Util\AvatarAlert;

class MyCourseController extends \Home\Controller\BaseController
{
    public function _initialize(){
        $user = $this->getCurrentUser();
        if(!$user->isLogin()) {
            $this->redirect('User/Signin/index');
        }
        if($user->isTeacher()){
            $this->redirect('My/MyTeaching/index');
        }
    }
    
    private function powerRedirect(){
        $user = $this->getCurrentUser();
        if($user->isTeacher() || isGranted("ROLE_ADMIN")){
            return true;
        }
        return false;
    }

    /**
     * 老师、超管、管理员跳在教课程，其余跳在学课程
     * @author edit fubaosheng 2015-12-25
     */
    public function indexAction (Request $request)
    {
        if ($this->powerRedirect()) {
            return $this->redirect($this->generateUrl('my_teaching_courses'));
        } else {
            return $this->redirect($this->generateUrl('my_courses_learning'));
        }
    }

    public function learningAction(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $paginator = new Paginator(
            $this->get('request'),
            $this->getCourseService()->findUserLeaningCourseCount($currentUser['id'],array()),
            12
        );
        
        $courses = $this->getCourseService()->findUserLeaningCourses(
            $currentUser['id'],
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount(),
            array()
        );

        return $this->render('MyCourse:learning', array(
            'courses'=>$courses,
            'paginator' => $paginator,
            'my' => $this->getCurrentUser(),
        ));
    }

    public function learnedAction(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $paginator = new Paginator(
            $this->get('request'),
            $this->getCourseService()->findUserLeanedCourseCount($currentUser['id'],array()),
            12
        );

        $courses = $this->getCourseService()->findUserLeanedCourses(
            $currentUser['id'],
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount(),
            array()
        );

        $userIds = array();
        foreach ($courses as $key => $course) {
            $userIds = array_merge($userIds, $course['teacherIds']);
            $learnTime=$this->getCourseService()->searchLearnTime(array('courseId'=>$course['id'],'userId'=>$currentUser['id']));
            $courses[$key]['learnTime']=intval($learnTime/60)."小时".($learnTime%60)."分钟";
        }
        $users = $this->getUserService()->findUsersByIds($userIds);

        return $this->render('MyCourse:learned', array(
            'courses'=>$courses,
            'users'=>$users,
            'paginator' => $paginator
        ));
    }

    public function favoritedAction(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $paginator = new Paginator(
            $this->get('request'),
            $this->getCourseService()->findUserFavoritedCourseCount($currentUser['id']),
            12
        );

        $courses = $this->getCourseService()->findUserFavoritedCourses(
            $currentUser['id'],
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $userIds = array();
        foreach ($courses as $favoriteCourse) {
            $userIds = array_merge($userIds, $favoriteCourse['teacherIds']);
        }
        $users = $this->getUserService()->findUsersByIds($userIds);

        return $this->render('MyCourse:favorited', array(
            'courses'=>$courses,
            'users'=>$users,
            'paginator' => $paginator
        ));
    }

    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getSettingService()
    {
        return createService('System.SettingService');
    }

    //@author 褚兆前 2016-03-18
    private function getCourceLessonLearnService() {
        return createService('Course.CourceLessonLearnServiceModel');
    }
}
