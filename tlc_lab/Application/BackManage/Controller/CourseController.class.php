<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

class CourseController extends BaseController
{

    public function indexAction(Request $request)
    {
        $conditions = $request->query->all();
        $conditions['ignoreCid'] = 1;
        $count = $this->getCourseService()->searchCourseCount($conditions);
        $paginator = new Paginator($this->get('request'), $count, 20);
        $courses = $this->getCourseService()->searchCourses($conditions, null, $paginator->getOffsetCount(), $paginator->getPerPageCount());
        $categories = $this->getCategoryService()->findCategoriesByIds(ArrayToolkit::column($courses, 'categoryId'));
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($courses, 'userId'));

        $default = $this->getSettingService()->get('default', array());
        return $this->render('Course:index', array(
            'conditions' => $conditions,
            'courses' => $courses,
            'users' => $users,
            'categories' => $categories,
            'paginator' => $paginator,
            'default' => $default
        ));
    }

    private function searchFuncUsedBySearchActionAndSearchToFillBannerAction(Request $request, $twigToRender)
    {
        $key = $request->request->get("key");

        $conditions = array("title" => $key);
        $conditions['status'] = 'published';
        $conditions['type'] = 'normal';

        $count = $this->getCourseService()->searchCourseCount($conditions);

        $paginator = new Paginator($this->get('request'), $count, 6);

        $courses = $this->getCourseService()->searchCourses($conditions, null, $paginator->getOffsetCount(), $paginator->getPerPageCount());

        $categories = $this->getCategoryService()->findCategoriesByIds(ArrayToolkit::column($courses, 'categoryId'));

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($courses, 'userId'));

        return $this->render($twigToRender, array(
            'key' => $key,
            'courses' => $courses,
            'users' => $users,
            'categories' => $categories,
            'paginator' => $paginator
        ));
    }

    public function searchAction(Request $request)
    {
        return $this->searchFuncUsedBySearchActionAndSearchToFillBannerAction($request, 'Course:search');
    }

    public function searchToFillBannerAction(Request $request)
    {
        return $this->searchFuncUsedBySearchActionAndSearchToFillBannerAction($request, 'Course:search-to-fill-banner');
    }

    public function deleteAction(Request $request, $id)
    {
        $course = $this->getCourseService()->getCourse($id);
        
        $result = $this->getCourseService()->deleteCourse($id);

//        if($result){
//            $user = $this->getUserService()->getUser($course['userId']);
//
//            if(!empty($user) and in_array('ROLE_TEACHER',$user['roles'])){
//                $this->buildTeacherHtml($user);
//            }
//        }

        return $this->createJsonResponse(true);
    }

    public function publishAction(Request $request, $id)
    {
        $this->getCourseService()->publishCourse($id);

        $course = $this->getCourseService()->getCourse($id);
        $user = $this->getUserService()->getUser($course['userId']);

//        if(!empty($user) and in_array('ROLE_TEACHER',$user['roles'])){
//            $this->buildTeacherHtml($user);
//        }

        return $this->renderCourseTr($id);
    }

    public function closeAction(Request $request, $id)
    {
        $this->getCourseService()->closeCourse($id);

        $course = $this->getCourseService()->getCourse($id);
        $user = $this->getUserService()->getUser($course['userId']);

//        if(!empty($user) and in_array('ROLE_TEACHER',$user['roles'])){
//            $this->buildTeacherHtml($user);
//        }

        return $this->renderCourseTr($id);
    }

    /**
     * iOS设备屏蔽
     * @author fubaosheng 2015-06-23
     */
    public function forbidIosAction(Request $request, $id)
    {
        $this->getCourseService()->forbidIos($id);
        return $this->renderCourseTr($id);
    }

    /**
     * iOS设备取消屏蔽
     * @author fubaosheng 2015-06-23
     */
    public function allowIosAction(Request $request, $id)
    {
        $this->getCourseService()->allowIos($id);
        return $this->renderCourseTr($id);
    }

    public function copyAction(Request $request, $id)
    {
        $course = $this->getCourseService()->getCourse($id);

        return $this->render('Course:copy', array(
            'course' => $course,
        ));
    }

    public function copingAction(Request $request, $id)
    {
        $course = $this->getCourseService()->getCourse($id);

        $conditions = $request->request->all();
        $course['title'] = $conditions['title'];

        $newCourse = $this->getCourseCopyService()->copyCourse($course);

        $newTeachers = $this->getCourseCopyService()->copyTeachers($course['id'], $newCourse);

        $newChapters = $this->getCourseCopyService()->copyChapters($course['id'], $newCourse);

        $newLessons = $this->getCourseCopyService()->copyLessons($course['id'], $newCourse, $newChapters);

        $newQuestions = $this->getCourseCopyService()->copyQuestions($course['id'], $newCourse, $newLessons);

        $newTestpapers = $this->getCourseCopyService()->copyTestpapers($course['id'], $newCourse, $newQuestions);

        $this->getCourseCopyService()->convertTestpaperLesson($newLessons, $newTestpapers);

        $newMaterials = $this->getCourseCopyService()->copyMaterials($course['id'], $newCourse, $newLessons);

        $code = 'Homework';
        $homework = $this->getAppService()->findInstallApp($code);
        $isCopyHomework = $homework && version_compare($homework['version'], "1.0.4", ">=");

        if ($isCopyHomework) {
            $newHomeworks = $this->getCourseCopyService()->copyHomeworks($course['id'], $newCourse, $newLessons, $newQuestions);
            $newExercises = $this->getCourseCopyService()->copyExercises($course['id'], $newCourse, $newLessons);
        }

        return $this->redirect($this->generateUrl('admin_course'));
    }

    public function recommendAction(Request $request, $id)
    {
        $course = $this->getCourseService()->getCourse($id);

        $ref = $request->query->get('ref');

        if ($request->getMethod() == 'POST') {
            $number = $request->request->get('number');

            $course = $this->getCourseService()->recommendCourse($id, $number);

            $user = $this->getUserService()->getUser($course['userId']);

            if ($ref == 'recommendList') {
                return $this->render('Course:course-recommend-tr', array(
                    'course' => $course,
                    'user' => $user
                ));
            }


            return $this->renderCourseTr($id);
        }


        return $this->render('Course:course-recommend-modal', array(
            'course' => $course,
            'ref' => $ref
        ));
    }

    public function cancelRecommendAction(Request $request, $id)
    {
        $course = $this->getCourseService()->cancelRecommendCourse($id);
        return $this->renderCourseTr($id);
    }

    public function recommendListAction(Request $request)
    {
        $conditions = array(
            'status' => 'published',
            'recommended' => 1,
            'ignoreCid' => 1
        );

        $paginator = new Paginator(
            $this->get('request'),
            $this->getCourseService()->searchCourseCount($conditions),
            20
        );

        $courses = $this->getCourseService()->searchCourses(
            $conditions,
            'recommendedSeq',
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($courses, 'userId'));

        return $this->render('Course:course-recommend-list', array(
            'courses' => $courses,
            'users' => $users,
            'paginator' => $paginator
        ));
    }

    //课程分类管理
    public function courseCategoryAction(Request $request)
    {
        $categorys = $this->getCourseCategoryService()->selectCategory();
        return $this->render('Course:course-category',array(
            'categorys'  =>  $categorys,
        ));
    }

    //创建、编辑课程分类
    public function categoryManaseBaseAction(Request $request,$id,$create){

        $courses = $this->getCourseService()->selectCoursesByCategory($id);
        $ifHaveCourse = $id != 0 && !empty($courses);

        $parentList = $this->getCategoryService()->findCategoriesByParentId(0);

        if ($request->getMethod() == 'POST'){
            $name = $_POST['name'];
            if(empty($name)){
                return $this->createJsonResponse(array('status'=>false,'message'=>'分类名称不能为空'));
            }

            if(strlen($name) < 1 || strlen($name) > 30){
                return $this->createJsonResponse(array('status'=>false,'message'=>'分类名称必须在1-30个字符之间'));
            }

            $level = $_POST['level'];
            if(empty($level) || !in_array($level,["one","two"])){
                $level = "one";
            }
            $level = ($level == "one") ? 1 : 2;

            if(empty($parentList) && $level == 2){
                return $this->createJsonResponse(array('status'=>false,'message'=>'二级分类必选选择父类'));
            }

            $parentId = $_POST['parent'];

            if($level == 1){
                $parentId = 0;
            }

            $code = $_POST['code'];
            if(strlen($code) > 30){
                return $this->createJsonResponse(array('status'=>false,'message'=>'分类代码长度不能大于30字符'));
            }

            $course_code = $_POST['course_code'];
            if(!$ifHaveCourse && empty($course_code)){
                return $this->createJsonResponse(array('status'=>false,'message'=>'分类课程编号前缀不能为空'));
            }

            if($ifHaveCourse && !empty($course_code)){
                return $this->createJsonResponse(array('status'=>false,'message'=>'该分类下有课程，不能修改分类课程编号前缀'));
            }

            if(!$ifHaveCourse && strlen($course_code) < 1 || strlen($course_code) > 8){
                return $this->createJsonResponse(array('status'=>false,'message'=>'分类课程编号前缀必须在1-8个字符之间'));
            }

            $fields = array(
                'name'  =>  $name,
                'code'  =>  $code,
                'courseCode'    =>  $course_code,
                'level'     =>      $level,
                'parentId'  =>      $parentId
            );
            
            $depli = $this->getCourseCategoryService()->checkDuplicate($id,array_filter($fields));
            if(!empty($depli)){
                return $this->createJsonResponse(array('status'=>false,'message'=>$depli));
            }

            if(empty($create)){ //更新分类
                $ret = $this->getCourseCategoryService()->updateCategory($id,array_filter($fields));
                if($ret){
                    return $this->createJsonResponse(array('status'=>true,'message'=>'修改课程分类成功'));
                }else{
                    return $this->createJsonResponse(array('status'=>false,'message'=>'修改课程分类失败'));
                }

            }else{  //创建分类
                $ret = $this->getCourseCategoryService()->addCategory($fields);
                if($ret){
                    return $this->createJsonResponse(array('status'=>true,'message'=>'创建课程分类成功'));
                }else{
                    return $this->createJsonResponse(array('status'=>false,'message'=>'创建课程分类失败'));
                }
            }

        }

        if(!empty($id)){
            $cate = $this->getCourseCategoryService()->getCategory($id);
        }else{
            $cate = null;
        }

        return $this->render('Course:course-category-base-modal',array(
            'id'    =>  $id,
            'create'   =>  $create,
            'cate'     =>  $cate,
            'parentList' => $parentList,
            'haveCourse'    =>  $ifHaveCourse,
        ));
    }

    //删除课程分类
    public function categoryRemoveAction(Request $request,$id){
        $courses = $this->getCourseService()->selectCoursesByCategory($id);
        if ($request->getMethod() == 'POST') {
            $id = $_POST['cid'];
            if(empty($id)){
                return $this->createJsonResponse(array('status'=>false,'message'=>'id不能为空'));
            }

            if($this->getCourseCategoryService()->removeCategory($id)){
                return $this->createJsonResponse(array('status'=>true,'message'=>'删除分类成功！'));
            }else{
                return $this->createJsonResponse(array('status'=>false,'message'=>'删除分类成功失败！'));
            }
        }

        return $this->render('Course:course-category-remove-modal',array('id'   =>  $id,'count' => count($courses)));
    }


    public function categoryAction(Request $request)
    {
        return $this->forward('BackManage:Category:embed', array(
            'group' => 'course',
        ));
    }

    public function dataAction(Request $request)
    {
        $cond = array('type' => 'normal');

        $conditions = $request->query->all();

        $conditions = array_merge($cond, $conditions);
        $count = $this->getCourseService()->searchCourseCount($conditions);

        $paginator = new Paginator($this->get('request'), $count, 20);

        $courses = $this->getCourseService()->searchCourses($conditions, null, $paginator->getOffsetCount(), $paginator->getPerPageCount());

        foreach ($courses as $key => $course) {
            $isLearnedNum = $this->getCourseService()->searchMemberCount(array('isLearned' => 1, 'courseId' => $course['id']));


            $learnTime = $this->getCourseService()->searchLearnTime(array('courseId' => $course['id']));

            $lessonCount = $this->getCourseService()->searchLessonCount(array('courseId' => $course['id']));

            $courses[$key]['isLearnedNum'] = $isLearnedNum;
            $courses[$key]['learnTime'] = $learnTime;
            $courses[$key]['lessonCount'] = $lessonCount;

        }

        return $this->render('Course:data', array(
            'courses' => $courses,
            'paginator' => $paginator,
        ));
    }

    public function lessonDataAction(Request $request, $id)
    {
        $course = $this->getCourseService()->tryManageCourse($id);

        $lessons = $this->getCourseService()->searchLessons(array('courseId' => $id), array('createdTime', 'ASC'), 0, 1000);

        foreach ($lessons as $key => $value) {
            $lessonLearnedNum = $this->getCourseService()->findLearnsCountByLessonId($value['id']);

            $finishedNum = $this->getCourseService()->searchLearnCount(array('status' => 'finished', 'lessonId' => $value['id']));

            $lessonLearnTime = $this->getCourseService()->searchLearnTime(array('lessonId' => $value['id']));
            $lessonLearnTime = $lessonLearnedNum == 0 ? 0 : intval($lessonLearnTime / $lessonLearnedNum);

            $lessonWatchTime = $this->getCourseService()->searchWatchTime(array('lessonId' => $value['id']));
            $lessonWatchTime = $lessonWatchTime == 0 ? 0 : intval($lessonWatchTime / $lessonLearnedNum);

            $lessons[$key]['LearnedNum'] = $lessonLearnedNum;
            $lessons[$key]['length'] = intval($lessons[$key]['length'] / 60);
            $lessons[$key]['finishedNum'] = $finishedNum;
            $lessons[$key]['learnTime'] = $lessonLearnTime;
            $lessons[$key]['watchTime'] = $lessonWatchTime;

        }

        return $this->render('Course:lesson-data', array(
            'course' => $course,
            'lessons' => $lessons,
        ));
    }

    public function chooserAction(Request $request)
    {
        $conditions = $request->query->all();

        $count = $this->getCourseService()->searchCourseCount($conditions);

        $paginator = new Paginator($this->get('request'), $count, 20);

        $courses = $this->getCourseService()->searchCourses($conditions, null, $paginator->getOffsetCount(), $paginator->getPerPageCount());

        $categories = $this->getCategoryService()->findCategoriesByIds(ArrayToolkit::column($courses, 'categoryId'));

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($courses, 'userId'));

        return $this->render('Course:course-chooser', array(
            'conditions' => $conditions,
            'courses' => $courses,
            'users' => $users,
            'categories' => $categories,
            'paginator' => $paginator
        ));
    }

    private function getSettingService()
    {
        return createService('System.SettingService');
    }

    private function renderCourseTr($courseId)
    {
        $course = $this->getCourseService()->getCourse($courseId);
        $default = $this->getSettingService()->get('default', array());
        return $this->render('Course:tr', array(
            'user' => $this->getUserService()->getUser($course['userId']),
            'category' => $this->getCategoryService()->getCategory($course['categoryId']),
            'course' => $course,
            'default' => $default
        ));
    }

    private function getCourseService()
    {
        return createService('Course.CourseServiceModel');
    }

    private function getCourseCopyService()
    {
        return createService('Course.CourseCopyService');
    }

    private function getCourseCategoryService(){
        return createService('Course.CourseCategoryService');
    }

    private function getCategoryService()
    {
        return createService('Taxonomy.CategoryService');
    }

    private function getNotificationService()
    {
        return createService('User.NotificationService');
    }

    private function getNoteService()
    {
        return createService('Course.NoteService');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }
}