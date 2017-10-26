<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;
use Topxia\Service\Question\QuestionService;

class CourseQuestionCategoryManageController extends \Home\Controller\BaseController
{

    public function indexAction(Request $request, $courseId)
    {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $categories = $this->getQuestionService()->findCategoriesByTarget("course-{$course['id']}", 0, QuestionService::MAX_CATEGORY_QUERY_COUNT);

        return $this->render('CourseQuestionCategoryManage:index', array(
            'course' => $course,
            'categories' => $categories,
        ));
    }

    public function createAction(Request $request, $courseId)
    {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        if ($request->getMethod() == 'POST') {

            $data =$request->request->all();
            $data['target'] = "course-{$course['id']}";
            $category = $this->getQuestionService()->createCategory($data);

            return $this->render('CourseQuestionCategoryManage:tr', array(
                'category' => $category,
                'course' => $course
            ));
        }
        return $this->render('CourseQuestionCategoryManage:modal', array(
            'course' => $course,
        ));
    }

    public function updateAction(Request $request, $courseId, $id)
    {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $category = $this->getQuestionService()->getCategory($id);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $category = $this->getQuestionService()->updateCategory($id, $data);

            return $this->render('CourseQuestionCategoryManage:tr', array(
                'category' => $category,
                'course' => $course,
            ));
        }

        return $this->render('CourseQuestionCategoryManage:modal', array(
            'category' => $category,
            'course' => $course,
        ));
    }

    public function deleteAction(Request $request, $courseId, $id)
    {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $category = $this->getQuestionService()->getCategory($id);
        $this->getQuestionService()->deleteCategory($id);
        return $this->createJsonResponse(true);
    }

    public function sortAction(Request $request, $courseId)
    {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        
        $this->getQuestionService()->sortCategories("course-".$course['id'], $request->request->get('ids'));
        return $this->createJsonResponse(true);
    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }

    private function getQuestionService()
    {
        return createService('Question.QuestionService');
    }
}