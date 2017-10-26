<?php

namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;
use Think\RedisModel;
use Common\Lib\WebCode;
use Common\Model\Question\QuestionServiceModel as QuestionService;

class CourseQuestionManageController extends \Home\Controller\BaseController {

    public function indexAction(Request $request, $courseId) {

        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($courseId);

        $choices = $this->selectQuestionRanges($courseId);
        $quesSerObj = $this->getQuestionService();
        $conditions = $request->query->all();
        $conditions = $this->buildSearchConditions($conditions, $courseId, 0);
        $orderBy = $conditions['orderBy'];
        doLog("conditions:" . json_encode($conditions));
        $paginator = new Paginator(
                $this->get('request'), $quesSerObj->searchQuestionsCount($conditions), 10
        );

        $questions = $quesSerObj->searchQuestions(
                $conditions, $orderBy, $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($questions, 'userId'));
        #qzw 2015-07-28 报错
//        $targets = $this->get('wyzc.target_helper')->getTargets(ArrayToolkit::column($questions, 'target'));
        return $this->render('CourseQuestionManage:index', array(
                    'course' => $course,
                    'questions' => $questions,
                    'users' => $users,
//            'targets' => $targets,
                    'paginator' => $paginator,
                    'conditions' => $conditions,
                    'targetChoices' => $choices,
        ));
    }

    public function createAction(Request $request, $courseId, $type, $id) {

        $quesSerObj = $this->getQuestionService();
        $courseId = is_numeric($courseId) ? $courseId : substr($courseId, 0, strpos($courseId, "?"));
        $course = $this->getCourseService()->tryManageCourse($courseId);
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            if (isset($data['choices'])) {
                foreach ($data['choices'] as $k => $v) {
                    $arr[$k] = htmlspecialchars($v);
                }
                $data['choices'] = $arr;
            }
            $question = $quesSerObj->createQuestion($data);
            //在modal层中添加试题
            if ($data['tag'] == 2) {
                $type = I('GET.type');

                $resQuestion[$question['id']] = array(
                    'score' => $question['score'],
                    'type' => $question['type'],
                );
                $redisConn = RedisModel::getInstance(C('REDIS_DIST.CLASS_SIGN_IN'));

                $redisData = unserialize($redisConn->get($id));
                $redisData = $redisData ? $redisData : array();
                $questions = $redisData + $resQuestion;
                $redisConn->set($id, serialize($questions));

                $question['tag'] = 2;
                $question['success'] = '题目添加成功';
                $question['resQuestion'] = $resQuestion;
                $this->ajaxReturn($question);
            }
            //在试题库中添加试题
            if ($data['submission'] == 'continue') {
                $urlParams = ArrayToolkit::parts($question, array('target', 'difficulty', 'parentId'));
                $urlParams['type'] = $type;
                $urlParams['courseId'] = $courseId;
                $urlParams['goto'] = $request->query->get('goto', null);
                $urlParams['center'] = (CENTER == 'center') ? 1 : 0;
//                $this->setFlashMessage('success', '题目添加成功，请继续添加。');
//                $this->redirect($this->generateUrl('course_manage_question_create', $urlParams));

                $res = array(
                    'tag' => 0,
                    'success' => '题目添加成功，请继续添加。',
                    'url' => $this->generateUrl('course_manage_question_create', $urlParams),
                );
                $this->ajaxReturn($res);
            } elseif ($data['submission'] == 'continue_sub') {
//                $this->setFlashMessage('success', '题目添加成功，请继续添加子题。');
//                $this->redirect($request->query->get('goto', $this->generateUrl('course_manage_question', array('courseId' => $courseId, 'parentId' => $question['id'], 'center' => (CENTER == 'center') ? 1 : 0))));

                $res = array(
                    'tag' => 0,
                    'success' => '题目添加成功，请继续添加子题。',
                    'url' => ($request->query->get('goto', $this->generateUrl('course_manage_question', array('courseId' => $courseId, 'parentId' => $question['id'], 'center' => (CENTER == 'center') ? 1 : 0)))),
                );
                $this->ajaxReturn($res);
            } else {
//                $this->setFlashMessage('success', '题目添加成功。');
//                $this->redirect($this->generateUrl('course_manage_question', array('courseId' => $courseId, 'center' => (CENTER == 'center') ? 1 : 0)));
                $res = array(
                    'tag' => 0,
                    'success' => '题目添加成功。',
                    'url' => $this->generateUrl('course_manage_question', array('courseId' => $courseId, 'center' => (CENTER == 'center') ? 1 : 0)),
                );
                $this->ajaxReturn($res);
            }
        }

        $question = array(
            'id' => 0,
            'type' => $type,
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

        return $this->render("CourseQuestionManage:question-form-{$type}", array(
                    'course' => $course,
                    'question' => $question,
                    'parentQuestion' => $parentQuestion,
                    'targetsChoices' => $this->getQuestionTargetChoices($course),
                    'categoryChoices' => $this->getQuestionCategoryChoices($course),
                    'enabledAudioQuestion' => $enabledAudioQuestion
        ));
    }

    /*
     * 编辑已经被使用的试题
     * @courseId 课程id
     * @testId   试卷id
     * @type     试题类型
     * @htestId  历史作业id
     * @id       试题id  
     */

    public function editHasAddedQuestionAction(Request $request, $courseId, $testId, $id, $htestId) {
        
        $type = I('GET.type');
        $center = empty(I('GET.center')) ? 0 : 1;
        if ($type != '2') {
            $courseClassTestInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $testId));
            $testId = $courseClassTestInfo['testId'];
        }
        $testpaperInfo = $this->getTestpaperService()->getTestpaper($testId);
        $option = array(
            'testId' => $testpaperInfo['id'],
            'questionId' => $id
        );
        $testpaperItem = $this->getTestpaperService()->getTestpaperItemByQuestionId($option);
        $qesStoreSerObj = $this->getQuestionStoreService();
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            if (isset($data['choices'])) {
                foreach ($data['choices'] as $k => $v) {
                    $arr[$k] = htmlspecialchars($v);
                }
                $data['choices'] = $arr;
            }
            $question = $this->getQuestionService()->createQuestion($data);

            /*
             * 获取redis中手动添加的试题
             * 修改其中和本试题相同的数据
             */
            $key = $qesStoreSerObj->setMemKey($center, $type, $testId);
            $manualQuestions = $qesStoreSerObj->getQuestion($key);
            foreach ($manualQuestions as $qid => $manualQuestion) {
                if ($qid == $id) {
                    unset($manualQuestions[$qid]);
                    $arr1 = array($question['type'], $question['score']);
                    $arr2[$question['id']] = $arr1;
                    $arr3 = $manualQuestions + $arr2;
                    $qesStoreSerObj->setQuestion($key, $arr3);
                }
            }

            if ($testpaperItem) {
                $fields = array(
                    'questionId' => $question['id'],
                    'questionType' => $question['type'],
                );
                $re = $this->getTestpaperService()->updateItem($testpaperItem['id'], $fields);
            }

            if ($testpaperInfo['mode'] == 'random') {
                $memQuestion = $qesStoreSerObj->getMemQuestion($key);
                foreach ($memQuestion as $k => $memQes) {
                    if ($memQes == $id) {
                        $memQuestion[$k] = $question['id'];
                    }
                }
                $qesStoreSerObj->setMemQuestion($key, $memQuestion);
            }

            if ($testpaperInfo['mode'] == 'existing') {
                //历史试卷
                $historyCourseClassTestInfo = $this->getCourseClassTestService()->getCourseClassTestInfo(array('id' => $htestId));
                $homeWorkId = $historyCourseClassTestInfo['testId'];
                $key = $qesStoreSerObj->setMemKey($center, $type, $homeWorkId);
                $memQuestion = $qesStoreSerObj->getMemQuestion($key);
                foreach ($memQuestion as $k => $memQes) {
                    if ($memQes == $id) {
                        $memQuestion[$k] = $question['id'];
                    }
                }
                $qesStoreSerObj->setMemQuestion($key, $memQuestion);
            }
            $resQuestion[$question['id']] = array(
                'score' => $question['score'],
                'type' => $question['type'],
            );
            $question['tag'] = 1;
            $question['preId'] = $id;
            $question['success'] = '题目编辑成功';
            $question['resQuestion'] = $resQuestion;
            $this->ajaxReturn($question);
        }
    }

    /*
     * 更新试题
     * @updSource 更新来源 0试题库编辑 1试卷试题编辑
     */

    public function updateAction(Request $request, $courseId, $id, $updSource = 0) {

        $courseId = is_numeric($courseId) ? $courseId : substr($courseId, 0, strpos($courseId, "?"));
        $course = $this->getCourseService()->tryManageCourse($courseId);
        if ($request->getMethod() == 'POST') {
            $question = $request->request->all();
            $center = I('GET.center');
            $question = $this->getQuestionService()->updateQuestion($id, $question);
            if ($updSource) {
                $res = array(
                    'tag' => 1,
                    'success' => '题目修改成功!',
                );
                return $this->ajaxReturn($res);
            }
            $res = array(
                'tag' => 0,
                'success' => '题目修改成功!',
                'url' => $this->generateUrl('course_manage_question', array('courseId' => $courseId, 'center' => $center, 'parentId' => $question['parentId']))
            );
            return $this->ajaxReturn($res);
            $this->setFlashMessage('success', '题目修改成功！');
            return $this->redirect($this->generateUrl('course_manage_question', array('courseId' => $courseId, 'center' => $center, 'parentId' => $question['parentId'])));
//            return $this->redirect($request->query->get('goto', $this->generateUrl('course_manage_question',array('courseId' => $courseId,'parentId' => $question['parentId']))));
        }
        $question = $this->getQuestionService()->getQuestion($id);
        if ($question['parentId'] > 0) {
            $parentQuestion = $this->getQuestionService()->getQuestion($question['parentId']);
        } else {
            $parentQuestion = null;
        }

        if ($updSource) {
            $question['qestype'] = $question['type'];
            return $this->render("CourseQuestionManage:question-edit-modal", array(
                        'course' => $course,
                        'question' => $question,
                        'parentQuestion' => $parentQuestion,
                        'targetsChoices' => $this->getQuestionTargetChoices($course),
                        'categoryChoices' => $this->getQuestionCategoryChoices($course),
                        'type' => $question['type'],
                        'updateTag' => false,
            ));
        } else {
            return $this->render("CourseQuestionManage:question-form-{$question['type']}", array(
                        'course' => $course,
                        'question' => $question,
                        'parentQuestion' => $parentQuestion,
                        'targetsChoices' => $this->getQuestionTargetChoices($course),
                        'categoryChoices' => $this->getQuestionCategoryChoices($course),
            ));
        }
    }

    /*
     * 放入回收站 
     */

    public function deleteAction(Request $request, $courseId, $id) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $question = $this->getQuestionService()->getQuestion($id);
        //$this->getQuestionService()->deleteQuestion($id);
        $res = $this->getQuestionService()->putRecycle($id);
        return $this->createJsonResponse($res);
    }

    public function deletesAction(Request $request, $courseId) {
        $course = $this->getCourseService()->tryManageCourse($courseId);

        $ids = $request->request->get('ids');
        foreach ($ids ? : array() as $id) {
            $this->getQuestionService()->deleteQuestion($id);
        }

        return $this->createJsonResponse(true);
    }

    public function uploadFileAction(Request $request, $courseId, $type) {
        $course = $this->getCourseService()->tryManageCourse($courseId);

        if ($request->getMethod() == 'POST') {
            $originalFile = $this->get('request')->files->get('file');
            $file = $this->getUploadFileService()->addFile('quizquestion', 0, array('isPublic' => 1), 'local', $originalFile);
            return $this->createJsonResponse($file);
        }
    }

    /**
     * @todo refact it, to xxvholic.
     */
    public function previewAction(Request $request, $courseId, $id) {
        $isNewWindow = $request->query->get('isNew');
        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($courseId);

        $question = $this->getQuestionService()->getQuestion($id);
        if (empty($question)) {
            return $this->createMessageResponse('error','题目不存在！');
        }
        $item = array(
            'questionId' => $question['id'],
            'questionType' => $question['type'],
            'question' => $question
        );
        if ($question['subCount'] > 0) {
            $questions = $this->getQuestionService()->findQuestionsByParentId($id);
            foreach ($questions as $value) {
                $items[] = array(
                    'questionId' => $value['id'],
                    'questionType' => $value['type'],
                    'question' => $value
                );
            }
            $item['items'] = $items;
        }
        $type = in_array($question['type'], array('single_choice', 'uncertain_choice')) ? 'choice' : $question['type'];
        $questionPreview = true;
        $isHide = true; //在试题库中查看时隐藏分数
        if ($isNewWindow) {
            return $this->render('QuizQuestionTest:question-preview', array(
                        'item' => $item,
                        'type' => $type,
                        'questionPreview' => $questionPreview
            ));
        }
        return $this->render('QuizQuestionTest:question-preview-modal', array(
                    'item' => $item,
                    'type' => $type,
                    'questionPreview' => $questionPreview,
                    'isHide' => $isHide
        ));
    }

    /**
     * 获取回收站列表
     * @return array
     */
    public function getRecycleQuestionsAction(Request $request, $courseId) {

        $courseSerObj = $this->getCourseService();
        $course = $courseSerObj->tryManageCourse($courseId);
        $courseItems = $courseSerObj->getCourseItems($course['id']);

        $choices = $this->selectQuestionRanges($courseId);
        $quesSerObj = $this->getQuestionService();
        $conditions = $request->query->all();
        $conditions = $this->buildSearchConditions($conditions, $courseId, 1);
        $orderBy = $conditions['orderBy'];

        $paginator = new Paginator(
                $this->get('request'), $quesSerObj->searchQuestionsCount($conditions), 10
        );

        $questions = $quesSerObj->searchQuestions(
                $conditions, $orderBy, $paginator->getOffsetCount(), $paginator->getPerPageCount()
        );

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($questions, 'userId'));
        #qzw 2015-07-28 报错
//        $targets = $this->get('wyzc.target_helper')->getTargets(ArrayToolkit::column($questions, 'target'));
        return $this->render('CourseQuestionManage:question-recycle', array(
                    'course' => $course,
                    'questions' => $questions,
                    'users' => $users,
                    //            'targets' => $targets,
                    'paginator' => $paginator,
                    'parentQuestion' => $parentQuestion,
                    'conditions' => $conditions,
                    'targetChoices' => $choices,
        ));
    }

    /**
     * 恢复选中试题
     * @param
     */
    public function recoveryQuestionAction(Request $request, $courseId, $id) {
        $course = $this->getCourseService()->tryManageCourse($courseId);
        $ids = $request->request->get('ids');
        $map['id'] = array('in', $ids);
        $question = $this->getQuestionService()->getQuestion($id);
        //$this->getQuestionService()->deleteQuestion($id);
        $re = $this->getQuestionService()->recoveryRecycle($map);
        return $this->createJsonResponse($re);
    }

    public function createNewQuestionAction(Request $request, $courseId, $id) {
        $quesSerObj = $this->getQuestionService();
        $courseId = is_numeric($courseId) ? $courseId : substr($courseId, 0, strpos($courseId, "?"));
        $course = $this->getCourseService()->tryManageCourse($courseId);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $question = $quesSerObj->createQuestion($data);
            $res = array(
                'tag' => 0,
                'success' => '题目修改成功!',
                'url' => $this->generateUrl('course_manage_question', array('courseId' => $courseId, 'center' => (CENTER == 'center') ? 1 : 0))
            );
            return $this->ajaxReturn($res);
            if ($data['submission'] == 'continue') {
                $urlParams = ArrayToolkit::parts($question, array('target', 'difficulty', 'parentId'));
                $urlParams['type'] = $type;
                $urlParams['courseId'] = $courseId;
                $urlParams['goto'] = $request->query->get('goto', null);
                $urlParams['center'] = (CENTER == 'center') ? 1 : 0;
                $this->setFlashMessage('success', '题目添加成功，请继续添加。');
                $this->redirect($this->generateUrl('course_manage_question_create', $urlParams));
            } elseif ($data['submission'] == 'continue_sub') {
                $this->setFlashMessage('success', '题目添加成功，请继续添加子题。');
                $this->redirect($request->query->get('goto', $this->generateUrl('course_manage_question', array('courseId' => $courseId, 'parentId' => $question['id'], 'center' => (CENTER == 'center') ? 1 : 0))));
            } else {
                $this->setFlashMessage('success', '题目添加成功。');
                $this->redirect($this->generateUrl('course_manage_question', array('courseId' => $courseId, 'center' => (CENTER == 'center') ? 1 : 0)));
            }
        }

        $question = $this->getQuestionService()->getQuestion($id);
        if ($question['parentId'] > 0) {
            $parentQuestion = $this->getQuestionService()->getQuestion($question['parentId']);
        } else {
            $parentQuestion = null;
        }

        return $this->render("CourseQuestionManage:question-form-{$question['type']}", array(
                    'course' => $course,
                    'question' => $question,
                    'parentQuestion' => $parentQuestion,
                    'targetsChoices' => $this->getQuestionTargetChoices($course),
                    'categoryChoices' => $this->getQuestionCategoryChoices($course),
                    'updateTag' => true,
        ));
    }

    /*
     * 删除图片文件
     */

    public function delImgAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $url = getParameter("wyzc/upload/public_directory");
            $url .= '/' . $data['url'];
            if (!is_dir($url)) {
                if (unlink($url)) {
                    return $this->ajaxReturn(array('status' => 'ok', 'msg' => '删除文件成功！'));
                } else {
                    return $this->ajaxReturn(array('status' => 'error', 'msg' => '删除文件失败！'));
                }
            }
            return $this->ajaxReturn(array('status' => 'error', 'msg' => '不是有效文件！'));
        }
    }

    public function courseExpload($str) {
        $map = array();
        $arr = explode('/', $str);
        if (strstr($arr[1], 'chapter')) {
            $course = explode('-', $arr[0]);
            $chapter = explode('-', $arr[1]);
            $map = array(
                'courseId' => $course[1],
                'id' => $chapter[1],
            );
            return $map;
        } elseif (strstr($arr[1], 'lesson')) {
            $course = explode('-', $arr[0]);
            $lesson = explode('-', $arr[1]);
            $map = array(
                'courseId' => $course[1],
                'id' => $lesson[1],
            );
            return $map;
        }
    }

    private function buildSearchConditions($conditions, $courseId, $isRecycle) {
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
        $conditions['isRecycle'] = $isRecycle;
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

    private function getQuestionCategoryChoices($course) {
        $categories = $this->getQuestionService()->findCategoriesByTarget("course-{$course['id']}", 0, QuestionService::MAX_CATEGORY_QUERY_COUNT);
        $choices = array();
        foreach ($categories as $category) {
            $choices[$category['id']] = $category['name'];
        }
        return $choices;
    }

    private function getCourseService() {
        return createService('Course.CourseService');
    }

    private function getQuestionService() {
        return createService('Question.QuestionService');
    }

    private function getUploadFileService() {
        return createService('File.UploadFileService');
    }

    private function getCategoryService() {
        return createService('Taxonomy.CategoryService');
    }

    private function getCourseClassTestService() {
        return createService('Course.CourseClassTestServiceModel');
    }

    private function getTestpaperService() {
        return createService('Testpaper.TestpaperService');
    }

    private function getQuestionStoreService() {
        return createService('Question.QuestionStoreService');
    }

}