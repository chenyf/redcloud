<?php

namespace My\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

class MyThreadController extends \Home\Controller\BaseController {

    public function _initialize() {
        $user = $this->getCurrentUser();
        if (!$user->isLogin())
            $this->redirect('User/Signin/index');
        if($user->isTeacher()){
            $this->redirect('My/MyTeaching/index');
        }
    }

    public function discussionsAction(Request $request) {
        $user = $this->getCurrentUser();

        $conditions = array(
            'userId' => $user['id'],
            'type' => 'discussion'
        );

        $paginator = new Paginator(
                $request, $this->getThreadService()->searchThreadCount($conditions), 20
        );

        $threads = $this->getThreadService()->searchThreads(
                $conditions, 'createdNotStick', $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );

        $courses = $this->getCourseService()->findCoursesByIds(ArrayToolkit::column($threads, 'courseId'));
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($threads, 'latestPostUserId'));

        return $this->render('MyThread:discussions', array(
                    'courses' => $courses,
                    'users' => $users,
                    'threads' => $threads,
                    'paginator' => $paginator));
    }

    public function questionsAction(Request $request) {

        $user = $this->getCurrentUser();
        $type = $request->query->get('type');

        $typeArr = array('newReply', 'myThread', 'myPost', 'myReply', 'myCollection');
        $type = in_array($type, $typeArr) ? $type : 'newReply';

        $myPostThreads = $this->getThreadService()->searchPostsManage(array('userId' => $user['id']), array('createdTime', 'DESC'), 0, 999999);
        $myThreads = $this->getThreadService()->searchThreads(array('userId' => $user['id']), 'created', 0, 999999);
        $postThreads = array_merge($myPostThreads, $myThreads);

        $courseIds = ArrayToolkit::column($postThreads, 'courseId');
        $courseList = $this->getCourseService()->findCoursesByIds($courseIds);

        $conditions = array();
        $conditions['toUId'] = $user['id'];
        $conditions['type'] = array('in', 'post,reply');
        $threads = $this->getThreadService()->searchThreadsEvent($conditions, array('createdTime', 'DESC'), 0, 999999);
        $replyCounts = count(array_unique(ArrayToolkit::column($threads, 'id')));

        if ($type == 'newReply') {
            #我提问的问题的回答
            $threads = $this->getThreadService()->searchThreadsEvent($conditions, array('createdTime', 'DESC'), 0, 999999);
            $postIds = array_unique(ArrayToolkit::column($threads, 'id'));
            $conditions = array();
            $conditions['ids'] = $postIds;
        }

        #我的提问

        if ($type == 'myThread') {
            $conditions = array();
            $conditions['userId'] = $user['id'];
        }

        #我回答的提问

        if ($type == 'myPost') {
            $conditions = array();
            $conditions['userId'] = $user['id'];
            $conditions['pid'] = 0;
            $posts = $this->getThreadService()->searchPosts($conditions, array('createdTime', 'DESC'), 0, $this->getThreadService()->searchPostCounts($conditions));
            $postIds = array_unique(ArrayToolkit::column($posts, 'threadId'));
            $conditions = array();
            $conditions['ids'] = $postIds;
        }

        #我回复的问题

        if ($type == 'myReply') {
            $conditions = array();
            $conditions['userId'] = $user['id'];
            $conditions['pid'] = array('neq', 0);
            $posts = $this->getThreadService()->searchPosts($conditions, array('createdTime', 'DESC'), 0, $this->getThreadService()->searchPostCounts($conditions));
            $postIds = array_unique(ArrayToolkit::column($posts, 'threadId'));
            $conditions = array();
            $conditions['ids'] = $postIds;
        }

        #我收藏的问题

        if ($type == 'myCollection') {
            $conditions = array();
            $conditions['userId'] = $user['id'];
            $myCollection = $this->getThreadService()->searchThreadCollection($conditions, array('createdTime', 'DESC'), 0, $this->getThreadService()->searchThreadCollectionCounts($conditions));
            $postIds = array_unique(ArrayToolkit::column($myCollection, 'threadId'));
            $conditions = array();
            $conditions['ids'] = $postIds;
        }

        if (!empty($_GET['courseId'])) {
            $conditions['courseId'] = intval($_GET['courseId']);
        }
        if (isset($conditions['ids']) && empty($conditions['ids'])) {
            $threads = array();
        } else {
            $paginator = new Paginator(
                    $request, $this->getThreadService()->searchThreadCount($conditions), 5
            );

            $threads = $this->getThreadService()->searchThreads(
                    $conditions, 'createdNotStick', $paginator->getOffsetCount(), $paginator->getPerPageCount()
            );
            $threads = $this->decorateThread($threads);
        }

        $courses = $this->getCourseService()->findCoursesByIds(ArrayToolkit::column($tmpThreads, 'courseId'));

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($threads, 'latestPostUserId'));

        return $this->render('MyThread:questions', array(
                    'courses' => $courses,
                    'users' => $users,
                    'type' => $type,
                    'threads' => $threads,
                    'replyCounts' => $replyCounts,
                    'paginator' => $paginator,
                    'courseList' => $courseList
        ));
    }

    private function decorateThread($threads) {
        $tree = array();
        if (empty($threads)) {
            return array();
        }
        foreach ($threads as $thread) {
            $course = $this->getCourseService()->getCourseFind($thread['courseId']);
            $thread['courseName'] = $course['title'];
            $user = $this->getUserService()->getUser($thread['userId']);
            $thread['userName'] = $user['nickname'];

            $tree[] = $thread;
        }
        return $tree ? : array();
    }

    protected function getThreadService() {
        return createService('Thread.ThreadService');
    }

    protected function getCourseService() {
        return createService('Course.CourseService');
    }

}