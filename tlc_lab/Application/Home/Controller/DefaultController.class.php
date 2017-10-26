<?php

namespace Home\Controller;

use Common\Lib\ArrayToolkit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Topxia\System;
use Common\Lib\Paginator;
use \Common\Lib\WebCode;

class DefaultController extends BaseController
{
    public function indexAction()
    {
        $conditions = array('status' => 'published', 'type' => 'normal');

        $courses = $this->getCourseService()->searchCourses($conditions, 'latest', 0, 12);

        return $this->render('Default:index', array(
            'courses' => $courses,
        ));
//        return $this->render('Default:index', array(
//            'courses' => $courses,
//            'treeCate' => $treeCate,
////            'categories' => $categories,
//            'blocks' => $blocks,
//            'blocksImgsize' => $blocksArray['imgsize'],
//            'recentLiveCourses' => $recentLiveCourses,
//            'consultDisplay' => true,
//            'cashRate' => $cashRate,
//            'showCateCourse' => 1,
//            'allowGuide' => $allowGuide,
//            'posters' => $posters,
//        ));
    }

    //下载文件
    public function download(Request $request){
        echo getWebDomain();
    }

    /**
     * new Index
     * @author qzw
     */
    public function newIndexAction()
    {
        $type = isset($_GET['type']) ? $_GET['type'] : 1;
        return $this->render('Index:index_type_' . $type, array());
    }

    private function decorateCategory(&$categories)
    {
//        $sort = "recommendedSeq";
        $sort = "latest";
        $conditions = array(
            'status' => 'published',
            'type' => 'normal',
//            'categoryId' => $category['id'],
            'recommended' => ($sort == 'recommendedSeq') ? 1 : null,
        );

        $res = $this->getCourseService()->searchCourses(
            $conditions, $sort, 1, 10000
        );

        $courseCateArr = array();
        if ($res) {
            foreach ($res as $k => $v) {
                $categoryId = $v['categoryId'];
                if (!$categoryId) continue;
                $courseCateArr[$categoryId][] = $v;
            }
        }
        if ($categories && $courseCateArr) {
            foreach ($categories as &$cate) {
                $cateId = $cate['id'];
                $cate['course'] = isset($courseCateArr[$cateId]) ? $courseCateArr[$cateId] : array();
            }
        }
    }

    public function userlearningAction()
    {
        $user = $this->getCurrentUser();

        $courses = $this->getCourseService()->findUserLearnCourses($user->id, 0, 1);

        if (!empty($courses)) {
            foreach ($courses as $course) {
                $member = $this->getCourseService()->getCourseMember($course['id'], $user->id);

                $teachers = $this->getUserService()->findUsersByIds($course['teacherIds']);
            }

            $nextLearnLesson = $this->getCourseService()->getUserNextLearnLesson($user->id, $course['id']);

            $progress = $this->calculateUserLearnProgress($course, $member);
        } else {
            $course = array();
            $nextLearnLesson = array();
            $progress = array();
            $teachers = array();
        }

        return $this->render('Default:user-learning', array(
            'user' => $user,
            'course' => $course,
            'nextLearnLesson' => $nextLearnLesson,
            'progress' => $progress,
            'teachers' => $teachers
        ));
    }

    /**
     * 前台底部友情链接和关于我们的调用
     * @author @czq 2016-3-8
     */
    public function footPartAction()
    {
        $site = createService('System.SettingServiceModel')->get('site', array());
        $About_us_resource = createService('System.AboutUs')->findAll();
        foreach ($About_us_resource as $k => $v) {
            $About_us_resource[$k]['url'] = trim($v['content']);
        }
        return $this->render('Default:foot-part#Home', array(
            'site' => $site,
            'about_us' => $About_us_resource,
        ));
    }

    private function getRecentLiveCourses()
    {
        $recenntLessonsCondition = array(
            'status' => 'published',
            'endTimeGreaterThan' => time(),
        );

        $recentlessons = $this->getCourseService()->searchLessons(
            $recenntLessonsCondition,
            array('startTime', 'ASC'),
            0,
            20
        );

        $courses = $this->getCourseService()->findCoursesByIds(ArrayToolkit::column($recentlessons, 'courseId'));

        $recentCourses = array();
        foreach ($recentlessons as $lesson) {
            $course = $courses[$lesson['courseId']];
            if ($course['status'] != 'published') {
                continue;
            }
            $course['lesson'] = $lesson;
            $course['teachers'] = $this->getUserService()->findUsersByIds($course['teacherIds']);

            if (count($recentCourses) >= 8) {
                break;
            }

            $recentCourses[] = $course;
        }

        return $recentCourses;
    }

    public function promotedTeacherBlockAction()
    {
        $teacher = $this->getUserService()->findLatestPromotedTeacher(0, 8);
        if ($teacher) {
            foreach ($teacher as $k => $v) {
                $teacher[$k] = array_merge(
                    $v,
                    $this->getUserService()->getUserProfile($v['id'])
                );
                if (isset($teacher[$k]['locked']) && $teacher[$k]['locked'] !== '0')
                    $teacher[$k] = null;
            }
        }

        return $this->render('Default:promoted-teacher-block', array(
            'teacher' => $teacher,
        ));
    }

    public function latestReviewsBlockAction($number)
    {
        $reviews = $this->getReviewService()->searchReviews(array(), 'latest', 0, $number);
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($reviews, 'userId'));
        $courses = $this->getCourseService()->findCoursesByIds(ArrayToolkit::column($reviews, 'courseId'));
        return $this->render('Default:latest-reviews-block', array(
            'reviews' => $reviews,
            'users' => $users,
            'courses' => $courses,
        ));
    }

    public function topNavigationAction($siteNav = null)
    {
        $navigations = $this->getNavigationService()->getNavigationsTreeByType('top');

        return $this->render('Default:top-navigation#Home', array(
            'navigations' => $navigations,
            'siteNav' => $siteNav
        ));
    }

    public function getSubTree($navigations, $id)
    {
        static $list = array();
        if (isset($list[$id])) return $list[$id];
        foreach ($navigations as $key => $navigation) {
            if ($navigation["parentId"] == $id) {
                $list[$id][] = $navigation;
            }
        }

        return $list[$id];
    }

    public function footNavigationAction()
    {
        if (CONTROLLER_NAME == 'Course' && ACTION_NAME == 'learnAction') {
            return false;
        }
        $count = $this->getNavigationService()->getNavigationsCountByType("foot");
        $navigations = $this->getNavigationService()->findNavigationsByType('foot', 0, $count);
        static $list = array();
        foreach ($navigations as $key => $navigation) {
            if ($navigation['parentId'] == 0) {
                $navigation["subTree"] = $this->getSubTree($navigations, $navigation["id"]);
                $list[] = $navigation;
            }
        }

        return $this->render('Default:foot-navigation', array(
            'navigations' => $list,
        ));
    }

    public function customerServiceAction()
    {
        $customerServiceSetting = $this->getSettingService()->get('customerService', array());

        return $this->render('Default:customer-service-online', array(
            'customerServiceSetting' => $customerServiceSetting,
        ));

    }

    public function jumpAction(Request $request)
    {
        $courseId = intval($request->query->get('id'));
        if ($this->getCourseService()->isCourseTeacher($courseId, $this->getCurrentUser()->id)) {
            $url = $this->generateUrl('live_course_manage_replay', array('id' => $courseId));
        } else {
            $url = $this->generateUrl('course_show', array('id' => $courseId));
        }
        echo "<script type=\"text/javascript\">
        if (top.location !== self.location) {
        top.location = \"{$url}\";
        }
        </script>";
        exit();
    }

    private function calculateUserLearnProgress($course, $member)
    {
        if ($course['lessonNum'] == 0) {
            return array('percent' => '0%', 'number' => 0, 'total' => 0);
        }

        $percent = intval($member['learnedNum'] / $course['lessonNum'] * 100) . '%';

        return array(
            'percent' => $percent,
            'number' => $member['learnedNum'],
            'total' => $course['lessonNum']
        );
    }

    protected function getSettingService()
    {
        return createService('System.SettingService');
    }

    protected function getNavigationService()
    {
        return createService('Content.NavigationService');
    }

    protected function getBlockService()
    {
        return createService('Content.BlockService');
    }

    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getReviewService()
    {
        return createService('Course.ReviewService');
    }

    protected function getCategoryService()
    {
        return createService('Taxonomy.CategoryService');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }

    public function testPolv()
    {
        return $this->render('Default:testPolv');
    }

    public function queueAction()
    {
        $arr = array(
            'id' => 'aaaa'
        );
        $id = \Common\Services\QueueService::addJob(array(
            'jobName' => 'queuename_chuzhaoqian',
            'param' => $arr
        ));
        header("Content-type: text/html; charset=utf-8");
        echo '队列测试（/Home/Default/indexAction首页）：', $id;
        exit;
    }

}
