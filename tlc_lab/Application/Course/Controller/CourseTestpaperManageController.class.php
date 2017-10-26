<?php

namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;
use Think\Cache;
use Think\RedisModel;
use Common\Lib\WebCode;
use Common\Model\Question\Type\QuestionTypeFactory;

class CourseTestpaperManageController extends \Home\Controller\BaseController {

    public function indexAction(Request $request, $courseId) {

        $course = $this->getCourseService()->tryManageCourse($courseId);

        $conditions = array();
        $conditions['target'] = "course-{$course['id']}";
        $paginator = new Paginator(
                $this->get('request'), $this->getTestpaperService()->searchTestpapersCount($conditions), 10
        );

        $testpapers = $this->getTestpaperService()->searchTestpapers(
                $conditions, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($testpapers, 'updatedUserId'));

        return $this->render('CourseTestpaperManage:index', array(
                    'course' => $course,
                    'testpapers' => $testpapers,
                    'users' => $users,
                    'paginator' => $paginator,
        ));
    }

    public function createAction(Request $request, $courseId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);

        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();
            $fields['ranges'] = empty($fields['ranges']) ? array() : explode(',', $fields['ranges']);
            $fields['target'] = "course-{$course['id']}";
            $fields['pattern'] = 'QuestionType';
            list($testpaper, $items) = $this->getTestpaperService()->createTestpaper($fields);
            return $this->redirect($this->generateUrl('course_manage_testpaper_items', array('courseId' => $course['id'], 'testpaperId' => $testpaper['id'], 'center' => (CENTER == 'center') ? 1 : 0)));
        }

        $typeNames = $this->get('redcloud.twig.web_extension')->getDict('questionType');
        $types = array();

        foreach ($typeNames as $type => $name) {
            $typeObj = QuestionTypeFactory::create($type);
            $types[] = array(
                'key' => $type,
                'name' => $name,
                'hasMissScore' => $typeObj->hasMissScore(),
            );
        }
        $conditions["types"] = ArrayToolkit::column($types, "key");
        $conditions["courseId"] = $course["id"];
        $questionNums = $this->getQuestionService()->getQuestionCountGroupByTypes($conditions);
        $questionNums = ArrayToolkit::index($questionNums, "type");

        return $this->render('CourseTestpaperManage:create', array(
                    'course' => $course,
                    'ranges' => $this->getQuestionRanges($course),
                    'types' => $types,
                    'questionNums' => $questionNums
        ));
    }

    public function getQuestionCountGroupByTypesAction(Request $request, $courseId) {
        $params = $request->query->all();
        $course = $this->getCourseService()->tryManageCourse($courseId);
        if (empty($course)) {
            return $this->createJsonResponse(array());
        }

        $typeNames = $this->get('redcloud.twig.web_extension')->getDict('questionType');
        $types = array();

        foreach ($typeNames as $type => $name) {
            $typeObj = QuestionTypeFactory::create($type);
            $types[] = array(
                'key' => $type,
                'name' => $name,
                'hasMissScore' => $typeObj->hasMissScore(),
            );
        }

        $conditions["types"] = ArrayToolkit::column($types, "key");
        if ($params["range"] == "course") {
            $conditions["courseId"] = $course["id"];
        } else if ($params["range"] == "lesson") {
            $targets = $params["targets"];
            $targets = explode(',', $targets);
            $conditions["targets"] = $targets;
        }

        $questionNums = $this->getQuestionService()->getQuestionCountGroupByTypes($conditions);
        $questionNums = ArrayToolkit::index($questionNums, "type");
        return $this->createJsonResponse($questionNums);
    }

    public function buildCheckAction(Request $request, $courseId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);

        $data = $request->request->all();
        $data['target'] = "course-{$course['id']}";
        $data['ranges'] = empty($data['ranges']) ? array() : explode(',', $data['ranges']);
        $result = $this->getTestpaperService()->canBuildTestpaper('QuestionType', $data);
        return $this->createJsonResponse($result);
    }

    public function updateAction(Request $request, $courseId, $id) {
        $course = $this->getCourseService()->tryManageCourse($courseId);

        $testpaper = $this->getTestpaperService()->getTestpaper($id);
        if (empty($testpaper)) {
            return $this->createMessageResponse('error','试卷不存在');
        }

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $testpaper = $this->getTestpaperService()->updateTestpaper($id, $data);
            $this->setFlashMessage('success', '试卷信息保存成功！');
            return $this->redirect($this->generateUrl('course_manage_testpaper', array('courseId' => $course['id'])));
        }

        return $this->render('CourseTestpaperManage:update', array(
                    'course' => $course,
                    'testpaper' => $testpaper,
        ));
    }

    public function deleteAction(Request $request, $courseId, $testpaperId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $testpaper = $this->getTestpaperWithException($course, $testpaperId);
        $this->getTestpaperService()->deleteTestpaper($testpaper['id']);

        return $this->createJsonResponse(true);
    }

    public function deletesAction(Request $request, $courseId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);

        $ids = $request->request->get('ids');

        foreach (is_array($ids) ? $ids : array() as $id) {
            $testpaper = $this->getTestpaperWithException($course, $id);
            $this->getTestpaperService()->deleteTestpaper($id);
        }

        return $this->createJsonResponse(true);
    }

    public function removeHomeWorkAction(Request $request,$courseId){
        $course = $this->getCourseService()->tryManageCourse($courseId);
        if(empty($course)){
            return $this->createJsonResponse(false);
        }
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            if(!empty($data['vid'])){
                $this->getTestpaperService()->removeHomeWork($data['vid']);
            }
            return $this->createJsonResponse(true);
        }
        return $this->createJsonResponse(false);
    }

    public function publishAction(Request $request, $courseId, $id) {
        $course = $this->getCourseService()->tryManageCourse($courseId);

        $testpaper = $this->getTestpaperWithException($course, $id);

        $testpaper = $this->getTestpaperService()->publishTestpaper($id);

        $user = $this->getUserService()->getUser($testpaper['updatedUserId']);

        return $this->render('CourseTestpaperManage:tr', array(
                    'testpaper' => $testpaper,
                    'user' => $user,
                    'course' => $course,
        ));
    }

    public function closeAction(Request $request, $courseId, $id) {
        $course = $this->getCourseService()->tryManageCourse($courseId);

        $testpaper = $this->getTestpaperWithException($course, $id);

        $testpaper = $this->getTestpaperService()->closeTestpaper($id);

        $user = $this->getUserService()->getUser($testpaper['updatedUserId']);

        return $this->render('CourseTestpaperManage:tr', array(
                    'testpaper' => $testpaper,
                    'user' => $user,
                    'course' => $course,
        ));
    }

    private function getTestpaperWithException($course, $testpaperId) {
        $testpaper = $this->getTestpaperService()->getTestpaper($testpaperId);
        if (empty($testpaper)) {
            throw $this->createNotFoundException();
        }

        if ($testpaper['target'] != "course-{$course['id']}") {
            throw $this->createAccessDeniedException();
        }
        return $testpaper;
    }

    public function itemsAction(Request $request, $courseId, $testpaperId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);

        $testpaper = $this->getTestpaperService()->getTestpaper($testpaperId);
        if (empty($testpaper)) {
            return $this->createMessageResponse('error','试卷不存在');
        }

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            if (empty($data['questionId']) or empty($data['scores'])) {
                return $this->createMessageResponse('error', '试卷题目不能为空！');
            }
            if (count($data['questionId']) != count($data['scores'])) {
                return $this->createMessageResponse('error', '试卷题目数据不正确');
            }

            $data['questionId'] = array_values($data['questionId']);
            $data['scores'] = array_values($data['scores']);

            $items = array();
            foreach ($data['questionId'] as $index => $questionId) {
                $items[] = array('questionId' => $questionId, 'score' => $data['scores'][$index]);
            }

            $this->getTestpaperService()->updateTestpaperItems($testpaper['id'], $items);

            $this->setFlashMessage('success', '试卷题目保存成功！');
            return $this->redirect($this->generateUrl('course_manage_testpaper', array('courseId' => $courseId)));
        }

        $items = $this->getTestpaperService()->getTestpaperItems($testpaper['id']);
        $questions = $this->getQuestionService()->findQuestionsByIds(ArrayToolkit::column($items, 'questionId'));

//        $targets = $this->get('redcloud.target_helper')->getTargets(ArrayToolkit::column($questions, 'target'));

        $subItems = array();
        foreach ($items as $key => $item) {
            if ($item['parentId'] > 0) {
                $subItems[$item['parentId']][] = $item;
                unset($items[$key]);
            }
        }


        return $this->render('CourseTestpaperManage:items', array(
                    'course' => $course,
                    'testpaper' => $testpaper,
                    'items' => ArrayToolkit::group($items, 'questionType'),
                    'subItems' => $subItems,
                    'questions' => $questions,
//            'targets' => $targets,
        ));
    }

    public function itemsResetAction(Request $request, $courseId, $testpaperId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);

        $testpaper = $this->getTestpaperService()->getTestpaper($testpaperId);
        if (empty($testpaper)) {
            return $this->createMessageResponse('error','试卷不存在');
        }

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $data['target'] = "course-{$course['id']}";
            $data['ranges'] = explode(',', $data['ranges']);
            $this->getTestpaperService()->buildTestpaper($testpaper['id'], $data);
            return $this->redirect($this->generateUrl('course_manage_testpaper_items', array('courseId' => $courseId, 'testpaperId' => $testpaperId)));
        }

        $typeNames = $this->get('redcloud.twig.web_extension')->getDict('questionType');
        $types = array();
        foreach ($typeNames as $type => $name) {
            $typeObj = QuestionTypeFactory::create($type);
            $types[] = array(
                'key' => $type,
                'name' => $name,
                'hasMissScore' => $typeObj->hasMissScore(),
            );
        }


        return $this->render('CourseTestpaperManage:items-reset', array(
                    'course' => $course,
                    'testpaper' => $testpaper,
                    'ranges' => $this->getQuestionRanges($course),
                    'types' => $types,
        ));
    }

    public function itemPickerAction(Request $request, $courseId, $testpaperId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $testpaper = $this->getTestpaperService()->getTestpaper($testpaperId);
        if (empty($testpaper)) {
            throw $this->createNotFoundException();
        }

        $conditions = $request->query->all();

        if (empty($conditions['target'])) {
            $conditions['targetPrefix'] = "course-{$course['id']}";
        }

        $conditions['parentId'] = 0;
        $conditions['excludeIds'] = empty($conditions['excludeIds']) ? array() : explode(',', $conditions['excludeIds']);

        if (!empty($conditions['keyword'])) {
            $conditions['stem'] = $conditions['keyword'];
        }


        $replace = empty($conditions['replace']) ? '' : $conditions['replace'];

        $paginator = new Paginator(
                $request, $this->getQuestionService()->searchQuestionsCount($conditions), 7
        );

        $questions = $this->getQuestionService()->searchQuestions(
                $conditions, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );

        $targets = $this->get('redcloud.target_helper')->getTargets(ArrayToolkit::column($questions, 'target'));
        return $this->render('CourseTestpaperManage:item-picker-modal', array(
                    'course' => $course,
                    'testpaper' => $testpaper,
                    'questions' => $questions,
                    'replace' => $replace,
                    'paginator' => $paginator,
                    'targetChoices' => $this->getQuestionRanges($course, true),
                    'targets' => $targets,
                    'conditions' => $conditions,
        ));
    }

    public function itemPickedAction(Request $request, $courseId, $testpaperId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $testpaper = $this->getTestpaperService()->getTestpaper($testpaperId);
        if (empty($testpaper)) {
            throw $this->createNotFoundException();
        }

        $question = $this->getQuestionService()->getQuestion($request->query->get('questionId'));
        if (empty($question)) {
            throw $this->createNotFoundException();
        }

        if ($question['subCount'] > 0) {
            $subQuestions = $this->getQuestionService()->findQuestionsByParentId($question['id']);
        } else {
            $subQuestions = array();
        }

        $targets = $this->get('redcloud.target_helper')->getTargets(array($question['target']));

        return $this->render('CourseTestpaperManage:item-picked', array(
                    'course' => $course,
                    'testpaper' => $testpaper,
                    'question' => $question,
                    'subQuestions' => $subQuestions,
                    'targets' => $targets,
                    'type' => $question['type']
        ));
    }

    /**
     * 作业/考试管理
     * 王磊 205-10-13
     */
    public function courseTestAction(Request $request, $courseId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $conditions = $request->query->all();
        
        list($list, $paginator) = $this->getCourseClassTestService()->paginate($request, $conditions, 'course_class');
        foreach ($list as &$arr) {
            $info = $this->getTestpaperService()->getTestpaper($arr['testId']);
            $arr['itemCount'] = $info['itemCount'];
        }
        foreach ($list as &$v) {
            $v['finish_num'] = $this->getTestpaperService()->searchTestpaperResultsCount([
                'testId' => $v['testId'],
                'status' => 'finished'
            ]);
            //试卷总人数，如果传入classId，则查询该班级的总人数，否则为课程中所有班级的总人数
            $v['classMemberNum'] = $this->getClassService()->getCourseStudentMemberCount($courseId,$conditions['classId']);
//            $v['classMemberNum'] = $this->getCourseMemberService()->searchMemberCount([
//                'courseId' => $courseId
//            ]);
        }

        $qesConditions['targetPrefix'] = "course-{$course['id']}";
        $qesConditions['isRecycle'] = 0;
        $questionsCount = $this->getQuestionService()->searchQuestionsCount($qesConditions); //试题数量
        $testpaperCount = $this->getTestpaperService()->searchTestpapersCount($conditions);
        return $this->render('CourseTestpaperManage:course-test', compact('course', 'list', 'paginator', 'questionsCount'));
    }

    /**
     * 创建练习
     * 郭俊强 2015-09-18
     */
    public function createCourseLessonAction(Request $request, $courseId, $chapterId) {

        $type = 2;
        $tyepText = "练习";
        $parentId = $request->query->get('parentId');
        $quesSerObj = $this->getQuestionService();
        $conditions['targetPrefix'] = "course-{$courseId}";
        $qesCount = $quesSerObj->searchQuestionsCount($conditions);
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $ranges = $this->getQuestionRanges($course);
        $classIdArray = $this->getCourseService()->getTestpaperId(array("courseId" => $courseId));
        $conditions = array();
        $conditions['target'] = "course-{$courseId}";
        $conditions['status'] = 'open';
        //$conditions['testpaperType'] = $type;
        if ($classIdArray) {
            $conditions['id'] = array("not in", $classIdArray);
        }
        $testpapers = $this->getTestpaperService()->searchTestpapers(
                $conditions, array('createdTime', 'DESC'), 0, 1000
        );
        $testCount = count($testpapers);
        if ($request->getMethod() == 'POST') {
            $modeArray = array('manually', 'existing', 'random');
            $fields = $request->request->all();
            $fields['name'] = $fields['name'];
            $fields['mode'] = empty($fields['mode']) || !in_array($fields['mode'], $modeArray) ? 'manually' : $fields['mode'];
            $fields['pattern'] = 'QuestionType';
            $fields['target'] = "course-{$courseId}";
            $fields['courseId'] = $courseId;
            $fields['ranges'] = empty($fields['ranges']) ? array() : explode(',', $fields['ranges']);
            $fields['scores'] = array("single_choice" => "2", "choice" => "2", "uncertain_choice" => "2", "fill" => "2", "determine" => "2", "essay" => "2");
            $fields["missScores"] = array("choice" => "0", "uncertain_choice" => "0");
            $fields["percentages"] = array("simple" => "", "normal" => "", "difficulty" => "");
            $testpaper = $this->getTestpaperService()->createTestpaperNew($fields);
            $lesson['title'] = $fields['name'];
            $lesson['type'] = 'practice';
            $lesson['courseId'] = $courseId;
            $lesson['mediaId'] = $testpaper['id'];
            $lesson['testCount'] = $fields['testCount'];
            $items = $this->getCourseService()->getCourseItems($courseId);
            $lesson = $this->getCourseService()->createLesson($lesson);
            $res = $this->sortLesson($items, $lesson['id'], $parentId, 'lesson', $courseId);
            if ($testpaper)# && $res)
                return $this->redirect(U('Course/CourseTestpaperManage/' . $fields['mode'] . 'CreateCourseTestQuestions', array('courseId' => $courseId, 'type' => $type, 'id' => $testpaper['id'], 'lessonId' => $lesson['id'])));
        }
        return $this->render('CourseTestpaperManage:create-course-test', array(
                    'course' => $course,
                    'paperOptions' => $paperOptions,
                    'courseId' => $courseId,
                    'parentId' => $parentId,
                    'type' => $type,
                    'ranges' => $ranges,
                    'questionsCount' => $qesCount,
                    'tyepText' => $tyepText,
                    'testpaperCount' => $testCount
        ));
    }

    public function sortLesson($items, $lessonId, $parentId, $type, $courseId) {
        if ($parentId == "") {
            return true;
        }
        $sort = array();
        $lastParentId = 0;
        $sortType = "";

        foreach ($items as $key => $value) {
            if ($key == $parentId && $value['type'] == 'chapter') {
                $lastParentId = 1;
                $sortType = "chapter";
            }
            if ($key == $parentId && $value['type'] == 'unit') {
                $lastParentId = 1;
                $sortType = "unit";
            }
            if ($lastParentId == 1 && $value['type'] == 'unit' && $sortType == 'chapter' && $key != $parentId) {
                $sort[] = $type . '-' . $lessonId;
                $lastParentId = 2;
            }
            if ($lastParentId == 1 && $value['type'] == $sortType && $key != $parentId) {
                $sort[] = $type . '-' . $lessonId;
                $lastParentId = 2;
            }
            if ($lastParentId == 1 && $sortType == "unit" && $value['type'] == 'chapter' && $key != $parentId) {
                $sort[] = $type . '-' . $lessonId;
                $lastParentId = 2;
            }
            $sort[] = $key;
        }
        if ($lastParentId == 0 || $lastParentId == 1) {
            $sort[] = $type . '-' . $lessonId;
        }

        $this->getCourseService()->sortCourseItems($courseId, $sort);
    }

    /**
     * 添加练习题目
     */
    public function createCourseLessonQuestionsAction(Request $request) {
        $id = I('id');  // 练习课时id
        $type = 2;
        $tyepText = "练习";
        $lesson = $this->getCourseService()->findLesson($id);
        $courseId = $lesson['courseId'];
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $testId = $lesson['mediaId'];
        $testpaperInfo = $this->getTestpaperService()->getTestpaper($testId);
        return $this->redirect(U('Course/CourseTestpaperManage/' . $testpaperInfo['mode'] . 'CreateCourseTestQuestions', array('courseId' => $courseId, 'type' => $type, 'id' => $testId, 'lessonId' => $lesson['id'], 'center' => (CENTER == 'center') ? 1 : 0)));
    }

    /**
     * 选择练习
     * 郭俊强 2015-09-21
     */
    public function getTestpaperLessonAction(Request $request, $courseId) {
        $type = isset($_GET['type']) ? intval($_GET['type']) : '0';
        $tyepText = "练习";
        $parentId = $request->query->get('parentId');
        $classIdArray = $this->getCourseService()->getTestpaperId(array("courseId" => $courseId));
        $conditions = array();
        $conditions['target'] = "course-{$courseId}";
        $conditions['status'] = 'open';
        //$conditions['testpaperType'] = $type;
        if ($classIdArray) {
            $conditions['id'] = array("not in", $classIdArray);
        }
        $testpapers = $this->getTestpaperService()->searchTestpapers(
                $conditions, array('createdTime', 'DESC'), 0, 1000
        );
        $paperOptions = array();
        foreach ($testpapers as $testpaper) {
            $paperOptions[$testpaper['id']] = $testpaper['name'];
        }
        if ($request->getMethod() == 'POST') {

            $data = $request->request->all();
            $items = $this->getCourseService()->getCourseItems($courseId);
            $lesson['title'] = $data['title'];
            $lesson['type'] = 'practice';
            $lesson['courseId'] = $courseId;
            $lesson['mediaId'] = $data['testId'];
            $lesson['testCount'] = $this->getTestpaperService()->getFieldById($data['testId'], 'itemCount'); //已添加题目数量
            $lesson = $this->getCourseService()->createLesson($lesson);
            $this->sortLesson($items, $lesson['id'], $parentId, 'lesson', $courseId);
            if ($lesson['id']) {
                $url = $this->generateUrl('course_manage_lesson', array('id' => $courseId, 'center' => (CENTER == 'center') ? 1 : 0));
                $message['info'] = '保存成功';
                $message['url'] = $url;
                $this->success($message, 1);
            } else {
                $this->error('保存失败：' . $return['message']);
            }
        }
        return $this->render('CourseTestpaperManage:get-testpaper-lesson', array(
                    'paperOptions' => $paperOptions,
                    'courseId' => $courseId,
                    'parentId' => $parentId,
                    'type' => $type,
                    'tyepText' => $tyepText
        ));
    }

    public function getModalQuestionsListAction(Request $request, $courseId, $id) {

        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($courseId);

        $courseItems = $courseSerObj->getCourseItems($course['id']);
        $courseClassTestInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id));
        foreach ($courseItems as $k => $v) {
            $choices["course-{$course['id']}/{$k}"] = $v;
        }
        $quesSerObj = $this->getQuestionService();

        $conditions = $request->query->all();
        $data = $request->request->all();
        $conditions = array_merge($conditions, $data);

        $conditions['notids'] = $data['notids']; //去除试卷中已有试题

        $conditions = $this->buildSearchConditions($conditions, $courseId);

        $orderBy = $conditions['orderBy'];
        $paginator = new Paginator(
                $this->get('request'), $quesSerObj->searchQuestionsCount($conditions), 10
        );
        $questions = $quesSerObj->searchQuestions(
                $conditions, $orderBy, $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );
        return $this->render('CourseQuestionManage:question-modal-list', array(
                    'course' => $course,
                    'questions' => $questions,
                    'users' => $users,
                    'paginator' => $paginator,
                    'parentQuestion' => $parentQuestion,
                    'conditions' => $conditions,
        ));
    }

    /*
     * 获取试题的列表
     */

    public function getQuestionsListAction(Request $request, $courseId, $id) {
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($courseId);
        $courseItems = $courseSerObj->getCourseItems($course['id']);
        foreach ($courseItems as $k => $v) {
            $choices["course-{$course['id']}/{$k}"] = $v;
        }
        $quesSerObj = $this->getQuestionService();
        $conditions = $request->query->all();
        $conditions = $this->buildSearchConditions($conditions, $courseId);

        $user = $this->getCurrentUser();
        if(!$user->isLogin()){
            return $this->ajaxReturn(array('list'=>"",'page'=>""));
        }

        $conditions['userId']   =  $user['id'];

        $type = $conditions['type'];
        unset($conditions['type']);
        $center = $conditions['center'];
        $id = $conditions['id'];
        if ($type != '2') {
            $courseClassTestInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id));
            $id = $courseClassTestInfo['testId'];
        }
        $testpaperInfo = $this->getTestpaperService()->getTestpaper($id);
        $key = $this->getQuestionStoreService()->setMemKey($center, $type, $id);

        /*
         * @dataRange 选择的试题范围 selected
         */
        if (!empty($conditions['dataRange'])) {
            if ($conditions['dataRange'] == 'selected') {
                $dataRang = true;
            } elseif ($conditions['dataRange'] == 'selectall') {
                $dataRang = false;
            }
            unset($conditions['dataRange']);
        }
        if ($testpaperInfo['itemCount'] > 0 && (isset($dataRang) ? $dataRang : 0)) {
            //已保存试题
            $testpaperItems = $this->getTestpaperService()->getTestpaperItems($testpaperInfo['id']);
            foreach ($testpaperItems as &$testpaper) {
                $testpaper['type'] = $testpaper['questionType'];
                $testpaper['id'] = $testpaper['questionId'];
            }
            $lists = $testpaperItems;
            foreach ($testpaperItems as $testpaperItem) {
                $ids[] = $testpaperItem['questionId'];
            }
            $manualList = $this->getManualAddQuestions($key);
            foreach ($manualList as $ques) {
                $notids[] = $ques['id'];
            }
            $conditions['ids'] = $ids;
            $conditions['notids'] = $notids;
            $paginator = new Paginator(
                    $this->get('request'), $quesSerObj->searchQuestionsCount($conditions), 10
            );
            $questions = $quesSerObj->searchQuestions(
                    $conditions, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount(), true
            );
            $res['lists'] = $lists;
        } else {
            $data = $request->request->all();
            $ids = array();
            $manualList = $this->getManualAddQuestions($key);
            foreach ($manualList as $ques) {
                $ids[] = $ques['id'];
            }
            $conditions['notids'] = $ids;
            $orderBy = $conditions['orderBy'];
            $paginator = new Paginator(
                    $this->get('request'), $quesSerObj->searchQuestionsCount($conditions), 10
            );

            doLog("question list get:" . json_encode($conditions));
            $questions = $quesSerObj->searchQuestions(
                    $conditions, $orderBy, $paginator->getOffsetCount(), $paginator->getPerPageCount()
            );
        }
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($questions, 'userId'));
        if ($paginator->getOffsetCount() == 0) {
            doLog("merge!!");
            $questions = array_merge($this->getManualAddQuestions($key), $questions ? $questions : array());
        }
        $res['list'] = $this->render('CourseQuestionManage:question-list', array(
            'course' => $course,
            'questions' => $questions,
                ), TRUE);
        $res['page'] = $this->render('CourseTestpaperManage:question-page', array(
            'paginator' => $paginator,
                ), TRUE);
        $this->ajaxReturn($res);
    }

    /*
     * 随机获取试题列表
     * 手动添加的试题保存在redis中,随机的试题保存在memcache中
     * @courseId 课程Id
     * @id       试卷Id
     */

    public function randGetQuestionListAction(Request $request, $courseId, $id) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $quesSerObj = $this->getQuestionService();
        $qesStoreSerObj = $this->getQuestionStoreService();
        $center = empty(I('GET.center')) ? 0 : 1;
        $type = I('GET.type');

        if ($type == '2') {
            $testId = $id;
        } else {
            $courseClassTestInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id));
            $testId = $courseClassTestInfo['testId'];
        }
        $key1 = $qesStoreSerObj->setMemKey($center, $type, $testId);
        $testpaperInfo = $this->getTestpaperService()->getTestpaper($testId);
        if ($testpaperInfo['itemCount'] > 0) {
            $testpaperItems = $this->getTestpaperService()->getTestpaperItems($testpaperInfo['id']);

            //已经添加的试题
            foreach ($testpaperItems as &$testpaper) {
                $testpaper['type'] = $testpaper['questionType'];
                $testpaper['id'] = $testpaper['questionId'];
            }

            foreach ($testpaperItems as $testpaperItem) {
                $ids[] = $testpaperItem['questionId'];
            }
            $map['ids'] = $ids;

            $lists = $quesSerObj->searchQuestions(
                    $map, array('createdTime', 'DESC'), 0, count($ids)
            );

            //排除手动添加的试题
            $ids = array();
            $manualAddQuestions = $this->getManualAddQuestions($key1);
            foreach ($manualAddQuestions as $manulqes) {
                $ids[] = $manulqes['id'];
            }
            $map['notids'] = $ids;

            $lists = $testpaperItems;
            $paginator = new Paginator(
                    $this->get('request'), $quesSerObj->searchQuestionsCount($map), 10
            );
            $questions = $quesSerObj->searchQuestions(
                    $map, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount(), true
            );
            if ($paginator->getOffsetCount() == 0) {
                $questions = array_merge($this->getManualAddQuestions($key1), $questions ? $questions : array());
            }
            $res['list'] = $this->render('CourseQuestionManage:question-list', array(
                'course' => $course,
                'questions' => $questions,
                    ), TRUE);
            $res['page'] = $this->render('CourseTestpaperManage:question-page', array(
                'paginator' => $paginator,
                    ), TRUE);

            $res['lists'] = $lists;

            $this->ajaxReturn($res);
        }

        if ($request->getMethod() == 'POST') {
            $conditions = $request->request->all();
            $testCount = intval($conditions['testCount']);
            $conditions = $this->buildSearchConditions($conditions, $courseId);

            $manualAddQuestions = $this->getManualAddQuestions($key1);

            foreach ($manualAddQuestions as $manulqes) {
                $ids[] = $manulqes['id'];
            }
            $conditions['notids'] = $ids;
            $orderBy = $conditions['orderBy'];
            $total = $quesSerObj->searchQuestionsCount($conditions);
            $questions = $this->getQuestionService()->searchQuestions($conditions, array('createdTime', 'DESC'), 0, $total);

            shuffle($questions);
            if ($testCount > $total) {
                $testCount = $total;
            }

            //执行记录分页数据
            $key = $qesStoreSerObj->setMemKey($center, $type, $testId);
            if (empty($qesStoreSerObj->getMemQuestion($key)) || $conditions['reset']) {
                $list = array_rand($questions, $testCount);
                $ids = array();
                if (is_numeric($list)) {
                    $ids[] = $questions[$list]['id'];
                } else {
                    foreach ($list as $k => $v) {
                        $ids[] = $questions[$v]['id'];
                    }
                }
                $qesStoreSerObj->setMemQuestion($key, $ids);
            } else {
                $ids = $qesStoreSerObj->getMemQuestion($key);
            }
            $paginator = new Paginator(
                    $this->get('request'), $testCount, 10
            );


            $map['ids'] = $ids;
            $allMap['ids'] = $ids;

            $paginator = new Paginator(
                    $request, $this->getQuestionService()->searchQuestionsCount($map), 10
            );
            $questions = $this->getQuestionService()->searchQuestions(
                    $map, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount(), TRUE
            );

            //全部的试题 id
            $allQuestions = $quesSerObj->searchQuestions(
                    $allMap, $orderBy, 0, $testCount
            );

            $allQuestions = array_merge($this->getManualAddQuestions($key1), $allQuestions);
            $res['lists'] = $allQuestions;

            if ($paginator->getOffsetCount() == 0) {
                $questions = array_merge($this->getManualAddQuestions($key1), $questions);
            }

            $res['list'] = $this->render('CourseQuestionManage:question-list', array(
                'course' => $course,
                'questions' => $questions,
                    ), TRUE);

            $res['page'] = $this->render('CourseTestpaperManage:question-page', array(
                'paginator' => $paginator,
                    ), TRUE);
            $this->ajaxReturn($res);
        }
    }

    /**
     * 上传文件作为作业
     */
    public function fileCreateCourseTestQuestionsAction(Request $request, $courseId, $id){
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($courseId);

        if(empty($course)){
            return $this->createMessageResponse('info',"该课程不存在，请重试！");
        }

        $courseClassTest = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id));
        $id = $courseClassTest['testId'];

        $testpaperInfo = $this->getTestpaperService()->getTestpaper($id);

        if(empty($testpaperInfo)){
            return $this->createMessageResponse('info',"作业不存在，请检查地址！");
        }

        $user = $this->getCurrentUser();
        if(!$user->isLogin() or !$user->isTeacher()){
            return $this->createMessageResponse('info',"您没有操作权限！");
        }

        $conditions = $request->query->all();
        if ($request->getMethod() == 'POST'){

            $media = isset($_POST['media']) ? json_decode($_POST['media']) : null;
            $remark = isset($_POST['remark']) ? remove_xss(trim($_POST['remark'])) : "";
            $score = isset($_POST['score']) ? intval($_POST['score']) : 0;

            if(empty($media) || empty($media->paths) || count($media->paths) == 0){
                return $this->createJsonResponse(array(
                    'status'    =>  false,
                    'message'   =>  "必须上传文件才能进行操作！",
                ));
            }

            if(count($media->paths) > 3){
                return $this->createJsonResponse(array(
                    'status'    =>  false,
                    'message'   =>  "最多只能上传3个文件",
                ));
            }
            
            $res1 = $this->getHomeworkService()->addHomeworkFileList($user['id'],$courseId,$id,$media->paths);
            $res2 = $this->getHomeworkService()->updateTestpaper($id,$remark,$score);
            if($res1 && $res2){
                return $this->createJsonResponse(array(
                    'status'    =>  true,
                    'message'   =>  "保存作业成功！",
                ));
            }else{
                return $this->createJsonResponse(array(
                    'status'    =>  false,
                    'message'   =>  "保存作业失败！",
                ));
            }
        }

        if (isset($conditions['type']) && $conditions['type'] != '1') {
            return $this->createMessageResponse('info',"抱歉，目前只有作业支持文件上传",'',5);
        }


        $fileList = $this->getHomeworkService()->getHomeworkFileList($id);
        $fileListUri = ArrayToolkit::column($fileList,'fileuri');

        $fileListJson = !empty($fileListUri) ? json_encode(array('paths' => $fileListUri)) : "";

        return $this->render('CourseTestpaperManage:create-file-homework',array(
            'name' => $testpaperInfo['name'],
            'remark'    =>  $testpaperInfo['description'],
            'score' => $testpaperInfo['score'],
            'id'    =>  $id,
            'courseClassId' =>  $courseClassTest['id'],
            'course' => $course,
            'courseId'  => $courseId,
            'fileList'  =>  $fileList,
            'fileListJson'  =>  $fileListJson
        ));
    }

    //上传作业文件
    public function upHomeworkFileAction(Request $request, $courseId, $id,$submit){
        if(empty($courseId)){
            return $this->createJsonResponse(array("status" => false, 'message' => "课程ID不能为空！"));
        }

        if(empty($id)){
            return $this->createJsonResponse(array("status" => false, 'message' => "作业ID不能为空！"));
        }

        if(!empty($submit)){
            $user = $this->getCurrentUser();

            if(!$user->isLogin()) {
                $this->redirect('User/Signin/index');
            }

            if($user->isTeacher() or $user->isAdmin()){
                return $this->createJsonResponse(array("status" => false, 'message' => "教师不能提交作业"));
            }
        }

        $res = $this->getTestpaperService()->upHomeworkFile($courseId, $id,$_FILES['file'],$submit);
        return $this->createJsonResponse($res);
    }

    //删除作业文件
    public function deleteHomeworkFileAction(Request $request,$submit){
        if ($request->getMethod() == 'POST'){
            $user = $this->getCurrentUser();

            if(!$user->isLogin()) {
                $this->redirect('User/Signin/index');
            }

            //提交作业后，学生不能删除文件
            if(!empty($id)) {
                if (!$user->isTeacher() and !$user->isAdmin()) {
                    return $this->createJsonResponse(array("status" => false, 'message' => "你无权删除文件"));
                }
            }

            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $path = isset($_POST['path']) ? trim($_POST['path']) : "";

            if(empty($submit)){
                $dir = $this->getHomeworkService()->getHomeworkFileDictory();
            }else{
                $dir = $this->getHomeworkService()->getSubmitHomeworkFileDictory();
            }

            $accessPath = $dir . $path;

            @unlink($accessPath);

            if(empty($submit) && !empty($id)){    //根据数据库ID删除数据库记录
                if(!$this->getHomeworkService()->deleteHomeworkFile($id)){ //删除失败
                   return $this->createJsonResponse(array('status' => false));
                }
            }

            return $this->createJsonResponse(array('status' => true));
        }
        return $this->createJsonResponse(array('status' => false));
    }

    //下载作业文件
    //$submit用来标识是否是学生提交的作业文件
    public function downloadHomeworkFileAction(Request $request, $id,$submit){

        if(empty($submit)){
            $file = $this->getHomeworkService()->getHomeworkFile($id);
            $dir = $this->getHomeworkService()->getHomeworkFileDictory();
        }else{
            $file = $this->getHomeworkService()->findSubmitFileById($id);
            $dir = $this->getHomeworkService()->getSubmitHomeworkFileDictory();
        }

        if(empty($file)){
            return $this->createMessageResponse("info","不存在该文件记录，请检查重试！");
        }

        $accessPath = $dir . $file['fileuri'];
        if(!is_file($accessPath)){
            return $this->createMessageResponse("info","文件不存在，请重试");
        }

        //打开文件
        $file = fopen ( $accessPath, "r" );
        //输入文件标签
        Header ( "Content-type: application/octet-stream" );
        Header ( "Accept-Ranges: bytes" );
        Header ( "Accept-Length: " . filesize ( $accessPath ) );
        $file_name = basename($accessPath);
        Header ( "Content-Disposition: attachment; filename=" . $file_name );
        //输出文件内容
        //读取文件内容并直接输出到浏览器
        echo fread ( $file, filesize ( $accessPath ) );
        fclose ( $file );
        exit ();
    }

    /*
     * 手动创建试题
     * @id 作业/考试为courseClassTestId 练习为testId
     */

    public function manuallyCreateCourseTestQuestionsAction(Request $request, $courseId, $id) {
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($courseId);
        $conditions = $request->query->all();

        $user = $this->getCurrentUser();
        if(!$user->isLogin()) {
            $this->redirect('User/Signin/index');
        }

        $conditions['userId']   =   $user['id'];

        if ($conditions['type'] != '2') {
            $courseClassTestInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id));
            $id = $courseClassTestInfo['testId'];
        }
        $testpaperInfo = $this->getTestpaperService()->getTestpaper($id);
        $mode = $testpaperInfo['mode'];
        /*
         * @craeteStep 区分是完善作业
         */
        if ($testpaperInfo['itemCount'] > 0)
            $createStep = "twoStep"; //完善作业
        else {
            $createStep = 'oneStep'; //创建作业
            $key = $this->getQuestionStoreService()->setMemKey($conditions['type'], $id);
            $res = $this->getQuestionStoreService()->delQuestion($key); //清除手动添加的试题
        }
        $choices = $this->selectQuestionRanges($courseId);
        $quesSerObj = $this->getQuestionService();
        $conditions = $this->buildSearchConditions($conditions, $courseId);
        $paginator = new Paginator(
                $this->get('request'), $quesSerObj->searchQuestionsCount($conditions), 10
        );
        $questions = $quesSerObj->searchQuestions(
                $conditions, $conditions['orderBy'], $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($questions, 'userId'));

        return $this->render('CourseTestpaperManage:manually-create-course-test-questions', array(
                    'name' => $testpaperInfo['name'],
                    'course' => $course,
                    'questions' => $questions,
                    'users' => $users,
                    'paginator' => $paginator,
                    'parentQuestion' => $parentQuestion,
                    'conditions' => $conditions,
                    'targetChoices' => $choices,
                    'mode' => $mode,
                    'createStep' => $createStep
        ));
    }

    /*
     * 随机创建试题
     */

    public function randomCreateCourseTestQuestionsAction(Request $request, $courseId, $id) {
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($courseId);
        $qesStoreSerObj = $this->getQuestionStoreService();
        $courseItems = $courseSerObj->getCourseItems($course['id']);
        $choices = $this->selectQuestionRanges($courseId);
        $questionsCount = $this->getQuestionService()->searchQuestionsCount($qesConditions); //试题数量
        $data = $request->query->all();
        extract($data);
        if ($type != '2') {
            $courseClassTestInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id));
            $id = $courseClassTestInfo['testId'];
        }
        $testpaperInfo = $this->getTestpaperService()->getTestpaper($id);
        if ($testpaperInfo['itemCount'] > 0)
            $perfectWork = true;
        else {
            $key = $qesStoreSerObj->setMemKey($center, $type, $id);
            $res = $qesStoreSerObj->delQuestion($key);
        }
        if ($request->getMethod() == 'POST') {
            $conditions = $request->request->all();
            $conditions['testCount'] = 2;
            $map['ranges'] = empty($conditions['ranges']) ? array() : explode(',', $conditions['ranges']);
            $map['target'] = "course-{$courseId}";
            $map['isRecycle'] = 0;
            $total = $this->getQuestionService()->searchQuestionsCount($map);
            $questions = $this->getQuestionService()->searchQuestions($map, array('createdTime', 'DESC'), 0, $total);
            shuffle($questions);
            $list = array_rand($questions, $conditions['testCount']);
            if (is_numeric($list)) {
                $qesData[] = $questions[$list];
            } else {
                $qesData = array();
                foreach ($list as $val) {
                    $qesData[] = $questions[$val];
                }
            }
            return $this->render('CourseQuestionManage:question-list', array(
                        'course' => $course,
                        'questions' => $questions,
                        'users' => $users,
                        'paginator' => $paginator,
                        'parentQuestion' => $parentQuestion,
                        'conditions' => $conditions,
                        'targetChoices' => $choices,
            ));
        }
        return $this->render('CourseTestpaperManage:random-create-course-test-questions', array(
                    'name' => $testpaperInfo['name'],
                    'course' => $course,
                    'targetChoices' => $choices,
                    'perfect' => $perfectWork
        ));
    }

    /*
     * 选择已有的试卷
     */

    public function existingCreateCourseTestQuestionsAction(Request $request, $courseId, $id) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $qesStoreSerObj = $this->getQuestionStoreService();
        $center = I('GET.center');
        $type = I('GET.type');
        if ($type != '2') {
            $courseClassTestInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id));
            $id = $courseClassTestInfo['testId'];
        }
        $testpaperInfo = $this->getTestpaperService()->getTestpaper($id);

        if ($testpaperInfo['itemCount'] > 0)
            $perfectWork = 'true';
        else {
            $key = $qesStoreSerObj->setMemKey($center, $type, $id);
            $qesStoreSerObj->delQuestion($key);
            $qesStoreSerObj->delMemQuestion($key);
        }
        //获取历史试卷选项
        if ($request->getMethod() == 'POST') {
            $key = 'history_' . $qesStoreSerObj->setMemKey($center, $type, $id);
            $historyTid = $qesStoreSerObj->getQuestion($key);
            $conditions = $request->request->all();
            $option = array(
                'classId' => $conditions['classId'], //组id
                'courseId' => $courseId, //课程id
                'type' => $conditions['type'], //0：试卷  1：作业
            );
            $list = $this->getCourseClassTestService()->getCourseClassTestList($option, '', 1);
            return $this->render('CourseTestpaperManage:get-history-homework', array(
                        'course' => $course,
                        'list' => $list,
                        'perfect' => $perfectWork,
                        'historyTid' => $historyTid
            ));
        }
        //选择授课班
        $courseClass = $this->getCourseClassService()->search(['id', 'className'], ['courseId' => $courseId]);
        array_unshift($courseClass, ['id' => 0, 'className' => "默认授课班"]);
        return $this->render('CourseTestpaperManage:existing-create-course-test-questions', array(
                    'name' => $testpaperInfo['name'],
                    'course' => $course,
                    'courseClass' => $courseClass,
                    'perfect' => $perfectWork,
        ));
    }

//    /*
//     * 获取已添加的试题
//     */
//
//    public function getHasAddedQuestionsAction(Request $request, $courseId, $id) {
//        $courseSerObj = $this->getCourseService();
//        $course = $courseSerObj->tryManageCourse($courseId);
//        $courseClassTestInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id));
//        $testpaperInfo = $this->getTestpaperService()->getTestpaper($courseClassTestInfo['testId']);
//
//        $quesSerObj = $this->getQuestionService();
//        if ($testpaperInfo['itemCount'] > 0) {
//            $testpaperItems = $this->getTestpaperService()->getTestpaperItems($testpaperInfo['id']);
//            foreach ($testpaperItems as $testpaperItem) {
//                $ids[] = $testpaperItem['questionId'];
//            }
//            $map['ids'] = $ids;
//            $lists = $quesSerObj->searchQuestions(
//                    $map, array('createdTime', 'DESC'), 0, count($ids)
//            );
//
//            $paginator = new Paginator(
//                    $this->get('request'), $quesSerObj->searchQuestionsCount($map), 10
//            );
//            $questions = $quesSerObj->searchQuestions(
//                    $map, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
//            );
//            if ($paginator->getOffsetCount() == 0) {
//                $questions = array_merge($this->getManualAddQuestions($id), $questions ? $questions : array());
//            }
//
//            $res['list'] = $this->render('CourseQuestionManage:question-list', array(
//                'course' => $course,
//                'questions' => $questions,
//                    ), TRUE);
//            $res['page'] = $this->render('CourseTestpaperManage:question-page', array(
//                'paginator' => $paginator,
//                    ), TRUE);
//
//            $res['lists'] = $lists;
//
//            $this->ajaxReturn($res);
//        }
//        $res['list'] = array();
//        $res['page'] = '';
//        $res['lists'] = array();
//        $this->ajaxReturn($res);
//    }

    public function getQuestionCountAction(Request $request, $courseId) {
        $quesSerObj = $this->getQuestionService();
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($courseId);
        if ($request->getMethod() == 'POST') {
            $conditions = $request->request->all();
            $conditions = $this->buildSearchConditions($conditions, $courseId);
            $questionCount = $quesSerObj->searchQuestionsCount($conditions);
            $this->ajaxReturn($questionCount);
        }
    }

    /*
     * 获取选择的统计
     */

    public function getselectedStatisticAction(Request $request) {
        $type = intval(I('GET.type'));
        return $this->render('CourseTestpaperManage:question-type:get-selected-statistic', array(
                    'type' => $type
        ));
    }

    /*
     * @testId  course_class_test Id 历史作业的id
     * @id 该作业id
     */

    public function getTestpaperQuestionListAction(Request $request) {
        $queryData = $request->query->all();
        extract($queryData);

        $quesSerObj = $this->getQuestionService();
        $courseSerObj = $this->getCourseService();
        $qesStoreSerObj = $this->getQuestionStoreService();
        $course = $courseSerObj->tryManageCourse($courseId);

        if ($type != '2') {
            $courseClassTestInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id));
            $id = $courseClassTestInfo['testId']; //作业或考试
        }
        $testpaperInfo = $this->getTestpaperService()->getTestpaper($id);
        $thisKey = $qesStoreSerObj->setMemKey($center, $type, $id); //@thisKey 本试卷key
        if ($testpaperInfo['itemCount'] > 0) {
            //echo "already save data!";die();
            $testpaperItems = $this->getTestpaperService()->getTestpaperItems($testpaperInfo['id']);

            foreach ($testpaperItems as $testpaperItem) {
                $ids[] = $testpaperItem['questionId'];
            }
            //已保存试题
            foreach ($testpaperItems as &$testpaper) {
                $testpaper['type'] = $testpaper['questionType'];
                $testpaper['id'] = $testpaper['questionId'];
            }
            $map['ids'] = $ids;
            $lists = $testpaperItems;

            //排除手动添加的试题
            $ids = array();
            $manualAddQuestions = $this->getManualAddQuestions($thisKey);
            foreach ($manualAddQuestions as $manulqes) {
                $ids[] = $manulqes['id'];
            }
            $map['notids'] = $ids;

            $paginator = new Paginator(
                    $this->get('request'), $quesSerObj->searchQuestionsCount($map), 10
            );
            $questions = $quesSerObj->searchQuestions(
                    $map, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount(), true
            );

            if ($paginator->getOffsetCount() == 0) {
                $questions = array_merge($this->getManualAddQuestions($thisKey), $questions ? $questions : array());
            }

            $res['list'] = $this->render('CourseQuestionManage:question-list', array(
                'course' => $course,
                'questions' => $questions,
                'type' => $type
                    ), TRUE);
            $res['page'] = $this->render('CourseTestpaperManage:question-page', array(
                'paginator' => $paginator,
                    ), TRUE);
            $res['lists'] = $lists;
            $this->ajaxReturn($res);
        }

        if ($request->getMethod() == 'POST') {
            $requestData = $request->request->all();
            extract($requestData);
            $courseClassTestId = $testId;

            $courseClassTest = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $courseClassTestId));
            $historyTestpaperId = $courseClassTest['testId'];   //历史试卷Id
            $historyKey = $qesStoreSerObj->setMemKey($center, $type, $historyTestpaperId); //历史试卷Key
            //存储需要分页的数据
            if ($qesStoreSerObj->isMemExists($historyKey)) {
                $questionIds = $qesStoreSerObj->getMemQuestion($historyKey);
            } else {
                $list = $this->getTestpaperService()->getTestpaperItems($historyTestpaperId);
                $questionIds = array();
                foreach ($list as $value) {
                    $questionIds[] = $value['questionId'];
                }
                $qesStoreSerObj->setMemQuestion($historyKey, $questionIds);
            }

            $map['ids'] = $questionIds;
            $orderBy = array('createdTime', 'ASC');
            $lists = $quesSerObj->searchQuestions(
                    $map, $orderBy, 0, count($questionIds)
            );

            $paginator = new Paginator(
                    $this->get('request'), count($questionIds), 10
            );

            $questions = $quesSerObj->searchQuestions(
                    $map, $orderBy, $paginator->getOffsetCount(), $paginator->getPerPageCount(), true
            );

            if ($paginator->getOffsetCount() == 0) {
                $questions = array_merge($this->getManualAddQuestions($thisKey), $questions ? $questions : array());
            }
            $res['list'] = $this->render('CourseQuestionManage:question-list', array(
                'course' => $course,
                'questions' => $questions,
                'testId' => $testId,
                'type' => $type
                    ), TRUE);
            $res['page'] = $this->render('CourseTestpaperManage:question-page', array(
                'paginator' => $paginator,
                    ), TRUE);

            $res['lists'] = $lists;

            $this->ajaxReturn($res);
        }
    }

    /**
     * 获取已经添加的试题列表
     */
    public function getSavedQuestionsListAction(Request $request) {

        if ($testpaperInfo['itemCount'] > 0) {
            //echo "already save data!";die();
            $testpaperItems = $this->getTestpaperService()->getTestpaperItems($testpaperInfo['id']);

            foreach ($testpaperItems as $testpaperItem) {
                $ids[] = $testpaperItem['questionId'];
            }
            //已保存试题
            foreach ($testpaperItems as &$testpaper) {
                $testpaper['type'] = $testpaper['questionType'];
                $testpaper['id'] = $testpaper['questionId'];
            }
            $map['ids'] = $ids;
            $lists = $testpaperItems;

            //排除手动添加的试题
            $ids = array();
            $manualAddQuestions = $this->getManualAddQuestions($thisKey);
            foreach ($manualAddQuestions as $manulqes) {
                $ids[] = $manulqes['id'];
            }
            $map['notids'] = $ids;

            $paginator = new Paginator(
                    $this->get('request'), $quesSerObj->searchQuestionsCount($map), 10
            );
            $questions = $quesSerObj->searchQuestions(
                    $map, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount(), true
            );

            if ($paginator->getOffsetCount() == 0) {
                $questions = array_merge($this->getManualAddQuestions($thisKey), $questions ? $questions : array());
            }

            $res['list'] = $this->render('CourseQuestionManage:question-list', array(
                'course' => $course,
                'questions' => $questions,
                'type' => $type
                    ), TRUE);
            $res['page'] = $this->render('CourseTestpaperManage:question-page', array(
                'paginator' => $paginator,
                    ), TRUE);
            $res['lists'] = $lists;
            $this->ajaxReturn($res);
        }
    }

    /*
     * 获取手动添加试题弹层
     * @id 试卷id
     */

    public function getModalAddQuestionAction(Request $request, $courseId, $id) {
        $qesStoreSerObj = $this->getQuestionStoreService();
        $choices = $this->selectQuestionRanges($courseId);
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($courseId);
        if ($request->getMethod() == 'POST') {
            $data = $request->query->all();
            $questions = $request->request->all();
            extract($data);
            if ($type != '2') {
                $testpaperInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id,));
                $id = $testpaperInfo['testId'];
            }
            $key = $qesStoreSerObj->setMemKey($center, $type, $id);
            $redisData = $qesStoreSerObj->getQuestion($key);
            $questions = $redisData + $questions;
            $qesStoreSerObj->setQuestion($key, $questions);
            $this->ajaxReturn(array('id' => $id, 'courseId' => $courseId));
        }
        return $this->render('CourseTestpaperManage:modal-add-question', array(
                    'mode' => I('get.mode'),
                    'targetChoices' => $choices,
        ));
    }

    /*
     * 在手动添加试题弹层中添加新题
     */

    public function createNewQuestionAction(Request $request, $courseId, $qestype) {
        $quesSerObj = $this->getQuestionService();
        $courseId = is_numeric($courseId) ? $courseId : substr($courseId, 0, strpos($courseId, "?"));
        $course = $this->getCourseService()->tryManageCourse($courseId);
        if ($request->getMethod() == 'POST') {
            $type = I('GET.type');
            $center = I('GET.center');
            $testId = I('GET.id');
            if ($type != '2') {
                $testpaperInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $testId,));
                $testId = $testpaperInfo['testId'];
            }
            $data = $request->request->all();
            if (isset($data['choices'])) {
                foreach ($data['choices'] as $k => $v) {
                    $arr[$k] = htmlspecialchars($v);
                }
                $data['choices'] = $arr;
            }
            $question = $quesSerObj->createQuestion($data);
            $resQuestion[$question['id']] = array(
                'score' => $question['score'],
                'type' => $question['type'],
            );

            $qesStoreSerObj = $this->getQuestionStoreService();
            $key = $qesStoreSerObj->setMemKey($center, $type, $testId);

            $qesData = $qesStoreSerObj->getQuestion($key);
            $qesData = $qesData ? $qesData : array();
            $questions = $qesData + $resQuestion;
//            print_r($questions);die;
            $qesStoreSerObj->setQuestion($key, $questions);

            $question['tag'] = 2;
            $question['success'] = '题目添加成功';
            $question['resQuestion'] = $resQuestion;
            $this->ajaxReturn($question);
        }

        $question = array(
            'id' => 0,
            'qestype' => $qestype,
            'target' => $request->query->get('target'),
            'difficulty' => $request->query->get('difficulty', 'normal'),
            'parentId' => $request->query->get('parentId', 0),
        );

        if ($question['parentId'] > 0) {
            $parentQuestion = $quesSerObj->getQuestion($question['parentId']);
            if (empty($parentQuestion)) {
                return $this->createMessageResponse('error', '父题不存在，不能创建子题！');
            }
        } else {
            $parentQuestion = null;
        }

        if ($this->container->hasParameter('enabled_features')) {
            $features = $this->container->getParameter('enabled_features');
        } else {
            $features = array();
        }
        $enabledAudioQuestion = in_array('audio_question', $features);
        return $this->render("CourseTestpaperManage:question-type:question-form-{$qestype}", array(
                    'course' => $course,
                    'question' => $question,
                    'parentQuestion' => $parentQuestion,
                    'targetsChoices' => $this->getQuestionTargetChoices($course),
                    'enabledAudioQuestion' => $enabledAudioQuestion
        ));
    }

    /*
     * 生成作业
     * @courseId 课程id
     * @param id 作业id //如果是练习为 testId 否则是 courseClassTestId
     * @historyTid 完善作业时显示选择的历史试卷
     */

    public function buildTestpaperAction(Request $request, $courseId, $id) {
        $id = intval(I('GET.id'));
        $type = intval(I('GET.type'));
        $courseId = intval(I('GET.courseId'));
        if ($type == '2') {
            $testId = $id;
            $lessonId = intval(I('GET.lessonId'));
        } else {
            $testpaperInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $id,));
            $testId = $testpaperInfo['testId'];
        }
        $historyTid = I('GET.historyTid');
        if (!empty($historyTid)) {
            $qesStoreSerObj = $this->getQuestionStoreService();
            $key = 'history_' . $qesStoreSerObj->setMemKey($type, $testId);
            $qesStoreSerObj->setQuestion($key, $historyTid);
        }

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            //更新数据
            $updatefiled = array(
                'itemCount' => 0,
                'score' => 0
            );
            $testCount = count($data);
            $res = $this->getTestpaperService()->updateTestpaper($testId, $updatefiled);
            if (!empty($lessonId))
                $res = $this->getCourseService()->updateLesson($courseId, $lessonId, array('mediaId' => $testId, 'testCount' => $testCount));
            $this->getTestpaperService()->deleteItemsByTestpaperId($testId);
            foreach ($data as $k => $v) {
                $v['id'] = $k;
                $v['parentId'] = 0;
                $this->getTestpaperService()->addItem($testId, $v);
            }
            if ($type == '2') {
                $this->getTestpaperService()->updTestpaperSeq($testId);
                $this->ajaxReturn(array('type' => 'lx', 'url' => $this->generateUrl('course_manage_lesson', array('id' => $courseId))));
            }
            else
                $this->ajaxReturn(array('testId' => $testId)); //返回试卷id
        }
    }

    /**
     * 创建作业、考试、练习
     * 郭俊强 2015-09-18
     */
    public function createCourseTestAction(Request $request, $courseId) {

        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        $typeText = $type == 0 ? "考试" : ( $type == 1 ? "作业" : "练习");
        if ($request->getMethod() == 'POST') {
            $modeArray = array('manually', 'existing', 'random','file');
            $fields = $request->request->all();
            $fields['name'] = $fields['name'];
            $fields['mode'] = empty($fields['mode']) || !in_array($fields['mode'], $modeArray) ? 'manually' : $fields['mode'];
            $fields['pattern'] = $fields['mode'] == 'file' ? "FileType" : 'QuestionType';
            $fields['target'] = "course-{$courseId}";
            $fields['testpaperType'] = $type;
            $testpaper = $this->getTestpaperService()->createTestpaperNew($fields);
            $data['type'] = $type;
            $data['courseId'] = $courseId;
            $data['classId'] = $fields['classId'];
            $data['userId'] = $this->getCurrentUser()->id;
            $data['title'] = $fields['name'];
            $data['mode'] = $fields['mode'];
            $data['testId'] = $testpaper['id'];
            $data['createdTime'] = time();
            $data['updatedTime'] = time();
            $data['correct'] = !empty($fields['correct']) ? 1 : 0;   //默认作业文件需要批阅
            $result = $this->getCourseClassTestService()->creatClassTestNew($data);
            if ($testpaper && $result)
                return $this->redirect(U('Course/CourseTestpaperManage/' . $fields['mode'] . 'CreateCourseTestQuestions', array('courseId' => $courseId, 'type' => $type, 'id' => $result)));
        }

        $quesSerObj = $this->getQuestionService();
        $conditions['targetPrefix'] = "course-{$courseId}";
        $qesCount = $quesSerObj->searchQuestionsCount($conditions);
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $ranges = $this->getQuestionRanges($course);

        $option = array(
            'courseId' => $courseId,
            'release' => 1
        );
        $testpaperCount = $this->getCourseClassTestService()->getCourseClassTestCount($option);
        return $this->render('CourseTestpaperManage:create-course-test', array(
                    'course' => $course,
                    //'paperOptions' => $paperOptions,
                    'courseId' => $courseId,
                    'type' => $type,
                    'ranges' => $ranges,
                    'tyepText' => $typeText,
                    'questionsCount' => $qesCount,
                    'testpaperCount' => $testpaperCount
        ));
    }

    /**
     * 获取试题
     * 郭俊强 2015-09-19
     */
    public function getCourseQesAction(Request $request, $courseId) {

        $data = $request->request->all();
        $map['ranges'] = empty($data['ranges']) ? array() : explode(', ', $data['ranges']);
        $map['target'] = "course-{$courseId}";


        $conditions = array();
        $conditions['type'] = $data['type'];
//        $conditions = I('GET.type');
        if (!empty($map['ranges'])) {
            $conditions['targets'] = $map['ranges'];
        } else {
            $conditions['targetPrefix'] = $map['target'];
        }
        $conditions['parentId'] = 0;
        $paginator = new Paginator(
                $this->get('request'), $this->getQuestionService()->searchQuestionsCount($conditions), 10
        );
        $questions = $this->getQuestionService()->searchQuestions(
                $conditions, array('createdTime', 'DESC'), $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );
        if ($map['type']) {
            //return $questions;
        } else {
            $questions = ArrayToolkit::group($questions, 'type');
        }
        $text = $this->render('CourseTestpaperManage:course-question', array(
            //'questions' => $result,
            'paginator' => $paginator,
            'aa' => 343,
            'questions' => $questions,
            'questionsType' => $conditions['type']
                ), true);
        // var_dump($text);die;
        return $this->createJsonResponse($text);
    }

    /**
     * 选择试卷和考试
     * 郭俊强 2015-09-21
     */
    public function getTestpaperAction(Request $request, $courseId, $classId) {
        $type = isset($_GET['type']) ? intval($_GET['type']) : '0';
        $tyepText = $type == 0 ? "考试" : "作业";

        $classIdArray = $this->getCourseClassTestService()->getTestpaperId(array("classId" => $classId, "courseId" => $courseId, 'type' => $type));
        $conditions = array();
        $conditions['target'] = "course-{$courseId}";
        $conditions['status'] = 'open';
        //$conditions['testpaperType'] = $type;
        if ($classIdArray) {
            $conditions['id'] = array("not in", $classIdArray);
        }
        $testpapers = $this->getTestpaperService()->searchTestpapers(
                $conditions, array('createdTime', 'DESC'), 0, 1000
        );
        $paperOptions = array();
        foreach ($testpapers as $testpaper) {
            $paperOptions[$testpaper['id']] = $testpaper['name'];
        }
        if ($request->getMethod() == 'POST') {

            $data = $request->request->all();
            $data['type'] = $type;
            $data['courseId'] = $courseId;
            $data['classId'] = $classId;
            $data['userId'] = $this->getCurrentUser()->id;
            $return = $this->getCourseClassTestService()->create($data);
            if ($return['status']) {
                $url = U('Course/CourseTestpaperManage/courseTest', array('courseId' => $courseId, 'classId' => $classId, 'type' => $type, 'center' => (CENTER == 'center') ? 1 : 0));
                $message['info'] = '保存成功';
                $message['url'] = $url;
                $this->success($message, 1);
            } else {
                $this->error('保存失败：' . $return['message']);
            }
        }
        return $this->render('CourseTestpaperManage:get-testpaper', array(
                    'paperOptions' => $paperOptions,
                    'courseId' => $courseId,
                    'classId' => $classId,
                    'type' => $type,
                    'tyepText' => $tyepText
        ));
    }

    /**
     * 添加作业/考试题目
     */
    public function createCourseTestQuestionsAction(Request $request) {

        $id = I('id');  // 作业/考试id
        $course_test = $this->getCourseClassTestService()->find($id);
        $courseId = $course_test['courseId'];
        $classId = $course_test['classId'];
        $testId = $course_test['testId'];
        $type = $course_test['type'];
        $testCount = $course_test['testCount']; //所需题目数量
        $itemCount = $this->getTestpaperService()->getFieldById($testId, 'itemCount'); //已添加题目数量
        $needCount = $testCount - $itemCount; //还需要添加题目数量

        if ($needCount <= 0) {
            $needCount = false;
        }

        $course = $this->getCourseService()->tryManageCourse($courseId);
        $tyepText = $type == 0 ? "考试" : "作业";
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $itemId = $data['isModify'];
            $data['type'] = $data['question_type'];
            unset($data['question_type']);
            if ($itemId != "") {
                $map = array(
                    'testId' => $testId,
                    'questionId' => $itemId
                );
                $question = $this->getQuestionService()->updateQuestion($itemId, $data);
                $questions['questionType'] = $question['type'];
                $questions['score'] = $question['score'];
                $questions['parentId'] = $question['parentId'];
                $re = $this->getTestpaperService()->updateItem1($map, $questions);
            } else {
                $question = $this->getQuestionService()->createQuestion($data);
                $this->getTestpaperService()->addItem($testId, $question);
            }
            $itemCount = $this->getTestpaperService()->getFieldById($testId, 'itemCount'); //已添加题目数量
            $needCount = $testCount - $itemCount; //还需要添加题目数量
            $this->ajaxReturn([
                'id' => $question['id'],
                'testCount' => $testCount,
                'itemCount' => $itemCount,
                'needCount' => $needCount,
                'type' => $type
            ]);
        }

        $question = array(
            'id' => 0,
            'type' => I('question_type'),
            'target' => $request->query->get('target'),
        );

        return $this->render('CourseTestpaperManage:create-course-test-questions', array(
                    'question' => $question,
                    'title' => $course_test['title'],
                    'course' => $course,
                    'courseId' => $courseId,
                    'id' => $id,
                    'classId' => $classId,
                    'type' => $type,
                    'needCount' => $needCount,
                    'testCount' => $testCount,
                    'itemCount' => $itemCount,
                    'tyepText' => $tyepText,
                    'targetsChoices' => $this->getQuestionTargetChoices($course),
                        //'categoryChoices' => $this->getQuestionCategoryChoices($course),
        ));
    }

    /**
     * 完成作业/考试
     */
    public function finishCourseTestAction(Request $request, $id) {

        $id = I('id');  // 作业/考试id
        $course_test = $this->getCourseClassTestService()->find($id);
        $edit = I('edit', 0);
        $courseId = $course_test['courseId'];

        $testId = $course_test['testId'];

        $type = $course_test['type'];

        $tyepText = $type == 0 ? "考试" : "作业";

        $course = $this->getCourseService()->tryManageCourse($courseId);

        //获取试题数量和分数
        $items = $this->getTestpaperService()->getItemCount($testId);
        foreach ($items as $v) {
            $item = $v;
        }

        $itemCount = $item['itemCount']; //试题总数量
        $itemScore = $item['score']; //试题总分数
        //获取题型
        $questionsType = $this->getTestpaperService()->getTestpaperItemQuestions($testId);

        //获取每种类型试题的总数与分数
        $Topics = $this->getTestpaperService()->getTestpaperItemAllQuestions($testId);
        foreach ($Topics as $k => $v) {
            $arr['number'][$v['questionType']] = $v['itemcount'];
            $arr['fen'][$v['questionType']] = $v['typescore'];
        }
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            if ($data['timeLimit'] == 1) {  //不限制时间
                $year = 365 * 24 * 3600;
                $data['startTime'] = date('Y-m-d H:i:s', time());
                $data['endTime'] = date('Y-m-d H:i:s', time() + $year);
                $data['limit'] = 0;
            }else { //限时
                $data['limit'] = 1;
            }
            unset($data['timeLimit']);

            //更新使用次数
            $testpaperItems = $this->getTestpaperService()->getTestpaperItems($course_test['testId']);
            $res = $this->filterField($testpaperItems);
            $this->getQuestionService()->setQuestionsCallNumber($res, 'callNumber');
            $return = $this->getCourseClassTestService()->update($data);
            $this->getTestpaperService()->updateTestpaper($testId,array("beginTime" => $data['startTime'],"endTime" => $data['endTime'],'limitedTime' => $data['limit']));
            if ($return['status']) {
                $this->getTestpaperService()->updTestpaperSeq($testId);
                $this->success('保存成功');
            } else {
                $this->error('保存失败：' . $return['message']);
            }
        }
        $piyue = $this->getTestpaperService()->WhetherMarking($testId);
        return $this->render('CourseTestpaperManage:finish-course-test', array(
                    'id' => $id,
                    'edit' => $edit,
                    'testId' => $testId,
                    'course' => $course,
                    'course_test' => $course_test,
                    'courseId' => $courseId,
                    'type' => $type,
                    'tyepText' => $tyepText,
                    'piyue' => $piyue,
                    'Questions' => $questionsType,
                    'Topics' => $Topics,
                    'itemCount' => $itemCount,
                    'arr' => $arr,
                    'itemScore' => $itemScore,
                    'mode'  =>  $course_test['mode'],
        ));
    }

    //公布答案
    public function unpublishCourseTestAction(Request $request, $id) {
        $data['show'] = 1;
        $restult = $this->getCourseClassTestService()->updCourseTestShow($id, $data);
        if ($restult) {
            $this->success("公布成功");
        } else {
            $this->error("公布失败");
        }
    }

    /**
     * 发布作业或考试
     * 郭俊强 2015-09-21
     */
    public function publishClassTestAction(Request $request, $courseId, $id, $testId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $data['release'] = 1;
        $restult = $this->getCourseClassTestService()->updCourseTestShow($id, $data);
        $testpaperItems = $this->getTestpaperService()->getTestpaperItems($testId);
        $res = $this->filterField($testpaperItems);
        $this->getQuestionService()->setQuestionsCallNumber($res, 'releaseNumber');
        if ($restult) {
            $this->getTestpaperService()->updTestpaperSeq($testId);
            $testpaper = $this->getTestpaperService()->publishTestpaper($testId);
            $this->success("发布成功");
        } else {
            $this->error("发布失败");
        }
    }

    /**
     * 编辑作业或考试
     * 郭俊强 205-08-19
     */
    public function editCourseTestAction(Request $request, $id) {
        $map['id'] = $id;
        $classInfo = $this->getCourseClassTestService()->getCourseClassTestInfo($map);
        $tyepText = $classInfo['type'] == 0 ? "考试" : "作业";
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $restult = $this->getCourseClassTestService()->updCourseClassTest($data);
            if ($restult) {
                $this->success("更新成功");
            } else {
                $this->error("更新失败");
            }
        }
        return $this->render('CourseTestpaperManage:edit-course-test', array(
                    'classInfo' => $classInfo,
                    'tyepText' => $tyepText
        ));
    }

    /*
     * 获取手动添加的试题
     */

    private function getManualAddQuestions($key) {

        $redisSerObj = $this->getQuestionStoreService();
        $quesSerObj = $this->getQuestionService();
        if ($redisSerObj->exists($key)) {
            $questions = $redisSerObj->getQuestion($key);
            foreach ($questions as $k => $v) {
                $ids[] = $k;
            }
            $map['ids'] = $ids;
            $questions = $quesSerObj->searchQuestions(
                    $map, array('createdTime', 'DESC'), 0, $quesSerObj->searchQuestionsCount($map)
            );
            foreach ($questions as &$v) {
                $v['isManual'] = TRUE;
            }
            return $questions;
        }
        return array();
    }

    private function buildSearchConditions($conditions, $courseId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        if ($conditions['startTarget'] != '0') {
            $seqs = array($conditions['startTarget'], $conditions['endTarget']);
            $map['seq'] = array('between', $seqs);
            $map['courseId'] = array('eq', $courseId);
            $lesson_res = $this->getCourseService()->accordingSeqGetLesson($map);
            $chapter_res = $this->getCourseService()->accordingSeqGetChapter($map);
            foreach ($lesson_res as $v) {
                $course_lesson[] = "course-{$courseId}/lesson-{$v['id']}";
            }
            foreach ($chapter_res as $v) {
                $course_lesson[] = "course-{$courseId}/chapter-{$v['id']}";
            }
            $conditions['targets'] = $course_lesson;
        }
        if (empty($conditions['target'])) {
            $conditions['targetPrefix'] = "course-{$course['id']}";
        }
        if (!empty($conditions['qestype'])) {
            $conditions['types'] = explode(',', $conditions['qestype']);
            unset($conditions['qestype']);
        }
        if (!empty($conditions['difficulty'])) {
            $conditions['difficulty'] = explode(',', $conditions['difficulty']);
        }
        if (!empty($conditions['keyword'])) {
            $conditions['stem'] = trim($conditions['keyword']);
        }

        if (!empty($conditions['parentId'])) {

            $parentQuestion = $quesSerObj->getQuestion($conditions['parentId']);
            if (empty($parentQuestion)) {
                return $this->redirect($this->generateUrl('course_manage_question', array('courseId' => $courseId)));
            }

            $orderBy = array('createdTime', 'ASC');
        } else {
            $conditions['parentId'] = 0;
            $parentQuestion = null;
            $orderBy = array('createdTime', 'DESC');
        }

        $conditions['isRecycle'] = 0;
        $conditions['orderBy'] = $orderBy;
        return $conditions;
    }

    private function selectQuestionRanges($courseId) {
        $courseItems = $this->getCourseService()->getCourseItems($courseId);
        foreach ($courseItems as $k => $v) {
            if ($v['type'] == 'chapter') {
                $n++;
                $m = 0;
                $j = 0;
                $v['sort'] = "第{$n}章";
            } else if ($v['type'] == 'unit') {
                $m++;
                $j = 0;
                $v['sort'] = "第{$m}节";
            } else {
                $j++;
                $v['sort'] = "{$n}.{$m}.{$j}";
            }
            $choices["course-{$course['id']}/{$k}"] = $v;
        }
        return $choices;
    }

    private function getQuestionTargetChoices($course) {
        $lessons = $this->getCourseService()->getCourseLessons($course['id']);
        $choices = array();
        $choices["course-{$course['id']}"] = '本课程';
        foreach ($lessons as $lesson) {
            if ($lesson['type'] == 'testpaper' || $lesson['type'] == 'testtask' || $lesson['type'] == 'practice') {
                continue;
            }
            $choices["course-{$course['id']}/lesson-{$lesson['id']}"] = "课程内容{$lesson['number']}：{$lesson['title']}";
        }
        return $choices;
    }

    private function getQuestionRanges($course, $includeCourse = false) {
        $lessons = $this->getCourseService()->getCourseLessons($course['id']);
        $ranges = array();

        if ($includeCourse == true) {
            $ranges["course-{$course['id']}"] = '本课程';
        }

        foreach ($lessons as $lesson) {
            if ($lesson['type'] == 'testpaper' || $lesson['type'] == 'testtask' || $lesson['type'] == 'practice') {
                continue;
            }
            $ranges["course-{$lesson['courseId']}/lesson-{$lesson['id']}"] = "课程内容{$lesson['number']}： {$lesson['title']}";
        }

        return $ranges;
    }

    public function filterField($items) {
        foreach ($items as $item) {
            $arr[] = $item['questionId'];
        }
        return $arr;
    }

    private function getCourseClassTestService() {
        return createService('Course.CourseClassTestServiceModel');
    }

    private function getClassService() {
        return createService('Group.ClassServiceModel');
    }

    private function getCourseMemberService() {
        return createService('Course.CourseMemberService');
    }

    private function getCourseClassService() {
        return createService('Course.CourseClassServiceModel');
    }

    private function getCourseService() {
        return createService('Course.CourseServiceModel');
    }

    private function getTestpaperService() {
        return createService('Testpaper.TestpaperService');
    }

    private function getHomeworkService(){
        return createService('Homework.HomeworkService');
    }

    private function getQuestionService() {
        return createService('Question.QuestionServiceModel');
    }

    private function getCategoryService() {
        return createService('Taxonomy.CategoryService');
    }

    private function getQuestionStoreService() {
        return createService('Question.QuestionStoreService');
    }

}