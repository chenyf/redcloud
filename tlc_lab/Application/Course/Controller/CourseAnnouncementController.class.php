<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\ArrayToolkit;


class CourseAnnouncementController extends \Home\Controller\BaseController
{

    public function showAction(Request $request, $courseId, $id)
    {
        $courseObj = $this->getCourseService();
        list($course, $member) = $courseObj->tryTakeCourse($courseId);
        $announcement = $courseObj->getCourseAnnouncement($courseId, $id);
        return $this->render('Course:announcement-show-modal', array(
            'announcement' => $announcement,
            'course' => $course,
            'canManage' => $courseObj->canManageCourse($course['id']),
        ));
    }

    public function showAllAction(Request $request, $courseId)
    {
        $courseObj = $this->getCourseService();
        $announcements = $courseObj->findAnnouncements($courseId, 0, 10000);
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($announcements, 'userId'));
        return $this->render('Course:announcement-show-all-modal', array(
            'announcements'=>$announcements,
            'users'=>$users
        ));
    }

    public function createAction(Request $request, $courseId)
    {
        $courseObj = $this->getCourseService();
        $course = $courseObj->tryManageCourse($courseId);

        if($request->getMethod() == 'POST'){
            $announcement = $courseObj->createAnnouncement($courseId, $request->request->all());

            return $this->createJsonResponse(true);
        }

        return $this->render('Course:announcement-write-modal', array(
            'announcement' => array('id' => '', 'content' => ''),
            'course'=>$course
        ));
    }
    
    public function updateAction(Request $request, $courseId, $id)
    {    
        $courseObj = $this->getCourseService();
        $course = $courseObj->tryManageCourse($courseId);

        $announcement = $courseObj->getCourseAnnouncement($courseId, $id);
        if (empty($announcement)) {
            return $this->createNotFoundException("课程公告(#{$id})不存在。");
        }

        if($request->getMethod() == 'POST') {
            $courseObj->updateAnnouncement($courseId, $id, $request->request->all());
            return $this->createJsonResponse(true);
        }

        return $this->render('Course:announcement-write-modal', array(
            'course' => $course,
            'announcement'=>$announcement
        ));
    }

    public function deleteAction(Request $request, $courseId, $id)
    {
        $courseObj = $this->getCourseService();
        $course = $courseObj->tryManageCourse($courseId);
        $courseObj->deleteCourseAnnouncement($courseId, $id);
        return $this->createJsonResponse(true);
    }

    
    /**
     * 不需要加Request $request否则参数获取不到
     * @param array $course
     * @return type
     * @author ZhaoZuoWu 2015-04-08
     */
    public function blockAction($course)
    {
        $courseObj = $this->getCourseService();
        $announcements = $courseObj->findAnnouncements($course['id'], 0, 10);
        return $this->render('Course:announcement-block', array(
            'course' => $course,
            'announcements' => $announcements,
            'canManage' => $courseObj->canManageCourse($course['id']),
            'canTake' => $courseObj->canTakeCourse($course)
        ));
    }

    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getUserService()
    {
        return createService('User.UserService');
    }

    protected function getNotificationService()
    {
        return createService('User.NotificationService');
    }

}