<?php

namespace Common\Model\Testpaper;

use Common\Model\Common\BaseModel;
use Common\Model\Testpaper\Builder\TestpaperBuilderFactory;
use Common\Lib\ArrayToolkit;
use Common\Model\Coures\CouresSerciceModel;
use Common\Model\Quertion\QuertionSerciceModel;
use Common\Model\User\UserSerciceModel;
use Common\Model\Question\Type\QuestionTypeFactory;
use Common\Common\StringToolkit;
use Common\Traits\ServiceTrait;

class TestpaperServiceModel extends BaseModel {

    use ServiceTrait;

    public function getTestpaper($id) {
        return $this->getTestpaperDao()->getTestpaper($id);
    }

    public function getTestpaperResult($id) {
        return $this->getTestpaperResultDao()->getTestpaperResult($id);
    }

    /*
     * 根据testId获取考试结果
     */

    public function getTestpaperResultByTestId($testId) {
        return $this->getTestpaperResultDao()->getTestpaperResultByTestId($testId);
    }

    public function getTestpaperResultsByTestId($testId) {
        return $this->getTestpaperResultDao()->getTestpaperResultsByTestId($testId);
    }

    public function findTestpapersByIds($ids) {
        $testpapers = $this->getTestpaperDao()->findTestpapersByIds($ids);
        return ArrayToolkit::index($testpapers, 'id');
    }

    public function searchTestpapers($conditions, $sort, $start, $limit) {
        return $this->getTestpaperDao()->searchTestpapers($conditions, $sort, $start, $limit);
    }

    public function searchTestpapersItem($conditions, $sort, $start, $limit) {
        return $this->getTestpaperItemDao()->searchTestpapersItem($conditions, $sort, $start, $limit);
    }

    public function searchTestpapersCount($conditions) {
        return $this->getTestpaperDao()->searchTestpapersCount($conditions);
    }

    public function searchTestpaperResultsCount($conditions) {
        return $this->getTestpaperResultDao()->searchTestpaperResultsCount($conditions);
    }

    public function searchTestpapersScore($conditions) {
        return $this->getTestpaperResultDao()->searchTestpapersScore($conditions);
    }

    public function createTestpaper($fields) {
        $testpaper = $this->getTestpaperDao()->addTestpaper($this->filterTestpaperFields($fields, 'create'));
        $items = $this->buildTestpaper($testpaper['id'], $fields);

        return array($testpaper, $items);
    }

    //创建作业 考试
    public function createTestpaperNew($fields) {
        return $testpaper = $this->getTestpaperDao()->addTestpaper($this->filterTestpaperFields($fields, 'create'));
        //$items = $this->buildTestpaper($testpaper['id'], $fields);
        //return array($testpaper, $items);
    }

    //判断是否需要批阅
    public function WhetherMarking($testId) {
        return $this->getTestpaperItemDao()->getTestpapertItemCount($testId);
    }

    public function addEachQuestionScore($map) {
        return $this->getTestpaperItemDao()->addEachQuestionScore($map);
    }

    //根据testId 和 questionId 获取testpaperItem
    public function getTestpaperItemByQuestionId($map) {
        return $this->getTestpaperItemDao()->getTestpaperItemByQuestionId($map);
    }

    //根据testResultId和userId获取testpaperItemResult
    public function getTestpaperItemResultByTestResultId($testResultId,$userId){
        return $this->getTestpaperItemResultDao()->getItemResultByTestpaperIdAndUserId($testResultId,$userId);
    }

    //根据$testId和$userId查找用户是否已经交卷了
    public function findTestPaperResultByUserIdAndTestId($userId,$testId){
        return $this->getTestpaperResultDao()->findTestPaperResultByUserIdAndTestId($userId,$testId);
    }

    public function updateTestpaper($id, $fields) {
        $testpaper = $this->getTestpaperDao()->getTestpaper($id);
        if (empty($testpaper)) {
            E("Testpaper #{$id} is not found, update testpaper failure.");
        }
        $fields = $this->filterTestpaperFields($fields, 'update');

        return $this->getTestpaperDao()->updateTestpaper($id, $fields);
    }

    private function getHomeworkService(){
        return createService('Homework.HomeworkService');
    }

    //上传作业文件
    public function upHomeworkFile($courseId,$homeworkId,$param = array(),$submit = 0){
        if(empty($courseId)){
            return array("status" => false, 'message' => "课程ID不能为空！");
        }

        if(empty($homeworkId)){
            return array("status" => false, 'message' => "作业ID不能为空！");
        }

        $options = array(
            "name" => "",
            "tmp_name" => "",
            "size" => 0,
        );

        $validFileExt = array("doc","docx","pdf","ppt","pptx","xls","xlsx","zip","rar","tar.gz");

        $options = array_merge($options, $param);
        extract($options);
        $info = pathinfo($name);
        $fileType = $info['extension'];
        if (empty($_FILES['file']['name']) || $_FILES['file']['error'] != 0) {
            return array("status" => false, 'message' => "请选择文件");
        }
        if (!in_array($fileType,$validFileExt)) {
            return array("status" => false, 'message' => "文件格式不符合规范");
        }
        if ($size > 1024*1024*100) {
            return array("status" => false, 'message' => "文件的大小不能超过100M");
        }

        $innerPath = $courseId . DIRECTORY_SEPARATOR . $homeworkId . DIRECTORY_SEPARATOR;
        if(empty($submit)){
            $dir = $this->getHomeworkService()->getHomeworkFileDictory();
        }else{
            $dir = $this->getHomeworkService()->getSubmitHomeworkFileDictory();
        }
        /* 设置上传路径 */
        $savePath = $dir . $innerPath;

        if(!is_dir($savePath)){
            if(false === @mkdir($savePath, 0777, true)){
                return array("status" => false, 'message' => "上传文件夹不存在！");
            }
        }

        $user = $this->getCurrentUser();

        /* 以时间来命名上传的文件 */
        $str = date('Ymdhis');

        if(empty($submit)){
            $file_name = $str . "-" . $user['id'] . "." . $fileType;
//            $file_name = $str . "-" . $user['id'] . "-" .basename($name);
        }else{
            $file_name = $user['userNum'] . "-" . date('Y-m-d') . "." . $fileType;
//            $file_name = $str . "." . $fileType;
        }

        $saveFileName = $savePath . $file_name;

        /* 是否上传成功 */
        if (copy($tmp_name,$saveFileName)) {
            $filePath = $innerPath . $file_name;
            return array("status" => true, 'message' => "文件上传成功", 'filePath' => $filePath, 'fileName' => $name);
        } else {
            return array("status" => false, 'message' => "文件上传失败");
        }
    }

    private function filterTestpaperFields($fields, $mode = 'create') {
        $filtedFields = array();
        $filtedFields['updatedUserId'] = $this->getCurrentUser()->id;
        $filtedFields['updatedTime'] = time();
        if ($mode == 'create') {
            if (!ArrayToolkit::requireds($fields, array('name', 'pattern', 'target'))) {
                E('缺少必要字段！');
            }
            $filtedFields['name'] = $fields['name'];
            $filtedFields['target'] = $fields['target'];
            $filtedFields['pattern'] = $fields['pattern'];
            $filtedFields['description'] = empty($fields['description']) ? '' : $fields['description'];
            $filtedFields['limitedTime'] = empty($fields['limitedTime']) ? 0 : (int) $fields['limitedTime'];
            $filtedFields['metas'] = empty($fields['metas']) ? array() : $fields['metas'];
            $filtedFields['status'] = 'draft';
            $filtedFields['createdUserId'] = $this->getCurrentUser()->id;
            $filtedFields['createdTime'] = time();
            $filtedFields['mode'] = $fields['mode']; //创建试题方式
            $filtedFields['testpaperType'] = $fields['testpaperType'];
            $filtedFields['beginTime'] = empty($fields['beginTime']) ? 0 : istrtotime($fields['beginTime']);
            $filtedFields['endTime'] = empty($fields['endTime']) ? 0 : istrtotime($fields['endTime']);
        } else {
            if (array_key_exists('name', $fields)) {
                $filtedFields['name'] = empty($fields['name']) ? '' : $fields['name'];
            }

            if (array_key_exists('description', $fields)) {
                $filtedFields['description'] = empty($fields['description']) ? '' : $fields['description'];
            }

            if (array_key_exists('limitedTime', $fields)) {
                $filtedFields['limitedTime'] = empty($fields['limitedTime']) ? 0 : (int) $fields['limitedTime'];
            }

            if (array_key_exists('passedScore', $fields)) {
                $filtedFields['passedScore'] = empty($fields['passedScore']) ? 0 : (float) $fields['passedScore'];
            }

            if (array_key_exists('itemCount', $fields)) {
                $filtedFields['itemCount'] = empty($fields['itemCount']) ? 0 : (int) $fields['itemCount'];
            }

            if (array_key_exists('score', $fields)) {
                $filtedFields['score'] = empty($fields['score']) ? 0 : (float) $fields['score'];
            }

            if(array_key_exists('beginTime', $fields)){
                $filtedFields['beginTime'] = empty($fields['beginTime']) ? 0 : istrtotime($fields['beginTime']);
            }

            if(array_key_exists('endTime', $fields)){
                $filtedFields['endTime'] = empty($fields['endTime']) ? 0 : istrtotime($fields['endTime']);
            }

        }

        return $filtedFields;
    }

    public function publishTestpaper($id) {
        $testpaper = $this->getTestpaperDao()->getTestpaper($id);
        if (empty($testpaper)) {
            //throw $this->createNotFoundException();
            return false;
        }
        if (!in_array($testpaper['status'], array('closed', 'draft'))) {
            //  E('试卷状态不合法!');
            return false;
        }
        $testpaper = array(
            'status' => 'open'
        );
        return $this->getTestpaperDao()->updateTestpaper($id, $testpaper);
    }

    public function closeTestpaper($id) {
        $testpaper = $this->getTestpaperDao()->getTestpaper($id);
        if (empty($testpaper)) {
            throw $this->createNotFoundException();
        }
        if (!in_array($testpaper['status'], array('open'))) {
            E('试卷状态不合法!');
        }
        $testpaper = array(
            'status' => 'closed'
        );
        return $this->getTestpaperDao()->updateTestpaper($id, $testpaper);
    }

    public function deleteTestpaper($id) {
        $this->getTestpaperDao()->deleteTestpaper($id);
        $this->getTestpaperResultDao()->deleteTestpaperResultByTestpaperId($id);
        $this->getTestpaperItemDao()->deleteItemsByTestpaperId($id);
        $this->getTestpaperItemResultDao()->deleteItemResultsByTestpaperId($id);
    }

    public function deleteTestpaperByIds(array $ids) {
        
    }

    public function removeHomeWork($id){
        $homework = $this->getCourseClassTestService()->getCourseClassTestById($id);
        if(!empty($homework)){
            $this->getCourseClassTestService()->deleteClassTestById($id);
            $this->deleteTestpaper($homework['testId']);
        }
    }

    /*
     * 根据testId删除item
     */

    public function deleteItemsByTestpaperId($id) {
        $this->getTestpaperItemDao()->deleteItemsByTestpaperId($id);
    }

    public function buildTestpaper($id, $options) {
        $testpaper = $this->getTestpaperDao()->getTestpaper($id);
        if (empty($testpaper)) {
            E("Testpaper #{$id} is not found.");
        }
        $this->getTestpaperItemDao()->deleteItemsByTestpaperId($testpaper['id']);


        $builder = TestpaperBuilderFactory::create($testpaper['pattern']);

        $result = $builder->build($testpaper, $options);
        if ($result['status'] != 'ok') {
            E("Build testpaper #{$id} items error.");
        }

        $items = array();
        $types = array();

        $totalScore = 0;
        $seq = 1;
        foreach ($result['items'] as $item) {
            $questionType = QuestionTypeFactory::create($item['questionType']);

            $item['seq'] = $seq;
            if (!$questionType->canHaveSubQuestion()) {
                $seq++;
                $totalScore += $item['score'];
            }

            $items[] = $this->getTestpaperItemDao()->addItem($item);
            if ($item['parentId'] == 0 && !in_array($item['questionType'], $types)) {
                $types[] = $item['questionType'];
            }
        }

        $metas = empty($testpaper['metas']) ? array() : $testpaper['metas'];
        $metas['question_type_seq'] = $types;
        $metas['missScore'] = $options['missScores'];

        $this->getTestpaperDao()->updateTestpaper($testpaper['id'], array(
            'itemCount' => $seq - 1,
            'score' => $totalScore,
            'metas' => $metas,
        ));

        return $items;
    }

    public function canBuildTestpaper($builder, $options) {
        $builder = TestpaperBuilderFactory::create($builder);
        return $builder->canBuild($options);
    }

    public function findTestpaperResultsByUserId($id, $start, $limit) {
        return $this->getTestpaperResultDao()->findTestpaperResultsByUserId($id, $start, $limit);
    }

    //新版
    public function findTestPaperResultsByUserIdNew($id, $start, $limit, $type, $courseId) {
        return $this->getTestpaperResultDao()->findTestPaperResultsByUserIdNew($id, $start, $limit, $type, $courseId);
    }

    public function findTestpaperResultsCountByUserId($id) {
        return $this->getTestpaperResultDao()->findTestpaperResultsCountByUserId($id);
    }

    //新版我的考试和作业
    public function findTestpaperResultsCountByUserIdNew($id, $type, $courseId) {
        return $this->getTestpaperResultDao()->findTestpaperResultsCountByUserIdNew($id, $type, $courseId);
    }

    public function findTestpaperResultByTestpaperIdAndUserIdAndActive($testpaperId, $userId) {
        return $this->getTestpaperResultDao()->findTestpaperResultByTestpaperIdAndUserIdAndActive($testpaperId, $userId);
    }

    public function findTestpaperResultsByTestIdAndStatusAndUserId($testpaperId, $userId, array $status, $classId, $type) {
        return $this->getTestpaperResultDao()->findTestpaperResultsByTestIdAndStatusAndUserId($testpaperId, $status, $userId, $classId, $type);
    }

    public function findTestpaperResultsByStatusAndTestIds($ids, $status, $start, $limit) {
        return $this->getTestpaperResultDao()->findTestpaperResultsByStatusAndTestIds($ids, $status, $start, $limit);
    }

    public function findTestpaperResult($condition, $field = array('*')) {
        return $this->getTestpaperResultDao()->where($condition)->field($field)->find();
    }

    /**
     * 获取授课班的作业提交结果
     * @param $testId     //试卷id
     * @param $classId   //授课班id
     * @param $status   //作业批阅状态
     *                   未批阅 reviewing 【已做完，等待批阅】
     *                   已批阅 finished 【已批阅】 (doing【正在做】 paused【作业终止】)0分
     * @param $start
     * @param $limit
     * @return mixed
     */
    public function findTestpaperResultsOfClass($testId,$status, $start, $limit) {
        return $this->getTestpaperResultDao()->findTestpaperResultsOfClass($testId,$status, $start, $limit);
    }

    public function findTestpaperResultsCountOfClass($testId, $status) {
        return $this->getTestpaperResultDao()->findTestpaperResultsCountOfClass($testId, $status);
    }

    public function findTestpaperResultCountByStatusAndTestIds($ids, $status) {
        return $this->getTestpaperResultDao()->findTestpaperResultCountByStatusAndTestIds($ids, $status);
    }

    public function findTestpaperResultsByStatusAndTeacherIds($ids, $status, $start, $limit) {
        return $this->getTestpaperResultDao()->findTestpaperResultsByStatusAndTeacherIds($ids, $status, $start, $limit);
    }

    public function findTestpaperResultCountByStatusAndTeacherIds($ids, $status) {
        return $this->getTestpaperResultDao()->findTestpaperResultCountByStatusAndTeacherIds($ids, $status);
    }

    public function findAllTestpapersByTarget($id) {
        $target = 'course-' . $id;
        return $this->getTestpaperDao()->findTestpaperByTargets(array($target));
    }

    public function findAllTestpapersByTargets(array $ids) {
        $targets = array();
        foreach ($ids as $id) {
            $targets[] = 'course-' . $id;
        }
        return $this->getTestpaperDao()->findTestpaperByTargets($targets);
    }

    //用户登录的情况下，查看用户是否做过该练习，做过的话直接取test_result，否则插入一条记录
    //没登录的，或者是老师管理员的不用记录
    public function startTestpaper($id, $target) {
        $user = $this->getCurrentUser();
        if(!$user->isLogin() || $user->isTeacher() || $user->isAdmin()){
            return;
        }

        $exitTestpaperResult = $this->getTestpaperResultDao()->findTestPaperResultByUserIdAndTestId($this->getCurrentUser()->id,$id);
        if(empty($exitTestpaperResult)){
            $testpaper = $this->getTestpaperDao()->getTestpaper($id);

            $testpaperResult = array(
                'paperName' => $testpaper['name'],
                'testId' => $id,
                'testpaperType' => $target['testpaperType'],
                'userId' => $this->getCurrentUser()->id,
                'limitedTime' => $testpaper['limitedTime'],
                'beginTime' => time(), //开始作业或联系的时间
                'status' => 'doing',
                'target' => empty($target['type']) ? '' : $testpaper['target'] . "/" . $target['type'] . "-" . $target['id']
            );

            return $this->getTestpaperResultDao()->addTestpaperResult($testpaperResult);
        }

        return $exitTestpaperResult['id'];
    }

    private function completeQuestion($items, $questions) {
        foreach ($items as $item) {
            if (!in_array($item['questionId'], ArrayToolkit::column($questions, 'id'))) {
                $questions[$item['questionId']] = array(
                    'isDeleted' => true,
                    'stem' => '此题已删除',
                    'score' => 0,
                    'answer' => ''
                );
            }
        }
        return $questions;
    }

    public function previewTestpaper($testpaperId) {
        $items = $this->getTestpaperItems($testpaperId);
        $items = ArrayToolkit::index($items, 'questionId');
        $questions = $this->getQuestionService()->findQuestionsByIds(ArrayToolkit::column($items, 'questionId'));
        $questions = ArrayToolkit::index($questions, 'id');

        $questions = $this->completeQuestion($items, $questions);

        $formatItems = array();
        foreach ($items as $questionId => $item) {
            $items[$questionId]['question'] = $questions[$questionId];

            if ($item['parentId'] != 0) {
                if (!array_key_exists('items', $items[$item['parentId']])) {
                    $items[$item['parentId']]['items'] = array();
                }
                $items[$item['parentId']]['items'][$questionId] = $items[$questionId];
                $formatItems['material'][$item['parentId']]['items'][$item['seq']] = $items[$questionId];
                unset($items[$questionId]);
            } else {
                $formatItems[$item['questionType']][$item['questionId']] = $items[$questionId];
            }
        }

        ksort($formatItems);
        return $formatItems;
    }

    public function showTestpaper($testpaperResultId, $isAccuracy = null) {
        $itemResults = $this->getTestpaperItemResultDao()->findTestResultsByTestpaperResultId($testpaperResultId);
        $itemResults = ArrayToolkit::index($itemResults, 'questionId');

        $testpaperResult = $this->getTestpaperResultDao()->getTestpaperResult($testpaperResultId);

        $items = $this->getTestpaperItems($testpaperResult['testId']);
        $items = ArrayToolkit::index($items, 'questionId');

        $questions = $this->getQuestionService()->findQuestionsByIds(ArrayToolkit::column($items, 'questionId'));
        $questions = ArrayToolkit::index($questions, 'id');

        $questions = $this->completeQuestion($items, $questions);

        $formatItems = array();
        foreach ($items as $questionId => $item) {

            if (array_key_exists($questionId, $itemResults)) {
                $questions[$questionId]['testResult'] = $itemResults[$questionId];
            }
            $questions[$questionId]['score'] = $item['score'];
            $items[$questionId]['question'] = $questions[$questionId];

            if ($item['parentId'] != 0) {
                if (!array_key_exists('items', $items[$item['parentId']])) {
                    $items[$item['parentId']]['items'] = array();
                }
                $items[$item['parentId']]['items'][$questionId] = $items[$questionId];
                $formatItems['material'][$item['parentId']]['items'][$item['seq']] = $items[$questionId];
                unset($items[$questionId]);
            } else {
                $formatItems[$item['questionType']][$item['questionId']] = $items[$questionId];
            }
        }

        if ($isAccuracy) {
            $accuracy = $this->makeAccuracy($items);
        }

        ksort($formatItems);
        return array(
            'formatItems' => $formatItems,
            'accuracy' => $isAccuracy ? $accuracy : null
        );
    }

    public function showTestPaperByTestId($testId){

        $items = $this->getTestpaperItems($testId);
        $items = ArrayToolkit::index($items, 'questionId');

        $questions = $this->getQuestionService()->findQuestionsByIds(ArrayToolkit::column($items, 'questionId'));
        $questions = ArrayToolkit::index($questions, 'id');

        $questions = $this->completeQuestion($items, $questions);

        $formatItems = array();
        foreach ($items as $questionId => $item) {

            $questions[$questionId]['score'] = $item['score'];
            $items[$questionId]['question'] = $questions[$questionId];

            if ($item['parentId'] != 0) {
                if (!array_key_exists('items', $items[$item['parentId']])) {
                    $items[$item['parentId']]['items'] = array();
                }
                $items[$item['parentId']]['items'][$questionId] = $items[$questionId];
                $formatItems['material'][$item['parentId']]['items'][$item['seq']] = $items[$questionId];
                unset($items[$questionId]);
            } else {
                $formatItems[$item['questionType']][$item['questionId']] = $items[$questionId];
            }
        }

        ksort($formatItems);
        return array(
            'formatItems' => $formatItems
        );
    }

    private function makeAccuracy($items) {
        $accuracyResult = array(
            'right' => 0,
            'partRight' => 0,
            'wrong' => 0,
            'noAnswer' => 0,
            'all' => 0,
            'score' => 0,
            'totalScore' => 0
        );
        $accuracy = array(
            'single_choice' => $accuracyResult,
            'choice' => $accuracyResult,
            'uncertain_choice' => $accuracyResult,
            'determine' => $accuracyResult,
            'fill' => $accuracyResult,
            'essay' => $accuracyResult,
            'material' => $accuracyResult
        );

        foreach ($items as $item) {

            if ($item['questionType'] == 'material') {
                if (!array_key_exists('items', $item)) {
                    continue;
                }
                foreach ($item['items'] as $key => $v) {

                    if ($v['questionType'] == 'essay') {
                        $accuracy['material']['hasEssay'] = true;
                    }

                    $accuracy['material']['score'] += $v['question']['testResult']['score'];
                    $accuracy['material']['totalScore'] += $v['score'];

                    $accuracy['material']['all']++;
                    if ($v['question']['testResult']['status'] == 'right') {
                        $accuracy['material']['right']++;
                    }
                    if ($v['question']['testResult']['status'] == 'partRight') {
                        $accuracy['material']['partRight']++;
                    }
                    if ($v['question']['testResult']['status'] == 'wrong') {
                        $accuracy['material']['wrong']++;
                    }
                    if ($v['question']['testResult']['status'] == 'noAnswer') {
                        $accuracy['material']['noAnswer']++;
                    }
                }
            } else {

                $accuracy[$item['questionType']]['score'] += $item['question']['testResult']['score'];
                $accuracy[$item['questionType']]['totalScore'] += $item['score'];

                $accuracy[$item['questionType']]['all']++;
                if ($item['question']['testResult']['status'] == 'right') {
                    $accuracy[$item['questionType']]['right']++;
                }
                if ($item['question']['testResult']['status'] == 'partRight') {
                    $accuracy[$item['questionType']]['partRight']++;
                }
                if ($item['question']['testResult']['status'] == 'wrong') {
                    $accuracy[$item['questionType']]['wrong']++;
                }
                if ($item['question']['testResult']['status'] == 'noAnswer') {
                    $accuracy[$item['questionType']]['noAnswer']++;
                }
            }
        }

        return $accuracy;
    }

    public function makeTestpaperResultFinish($id) {
        $userId = $this->getCurrentUser()->id;
        if (empty($userId)) {
            E("当前用户不存在!");
        }

        $testpaperResult = $this->getTestpaperResultDao()->getTestpaperResult($id);

        if ($testpaperResult['userId'] != $userId) {
            throw $this->createAccessDeniedException('无权修改其他学员的试卷！');
        }

        if (in_array($testpaperResult['status'], array('submitted', 'finished'))) {
            throw $this->createServiceException("已经交卷的试卷不能更改答案!");
        }

        $items = $this->getTestpaperItems($testpaperResult['testId']);
        $items = ArrayToolkit::index($items, 'questionId');

        $questions = $this->getQuestionService()->findQuestionsByIds(ArrayToolkit::column($items, 'questionId'));

        //得到当前用户答案
        $answers = $this->getTestpaperItemResultDao()->findTestResultsByTestpaperResultId($testpaperResult['id']);
        $answers = ArrayToolkit::index($answers, 'questionId');

        $answers = $this->formatAnswers($answers, $items);

        $answers = $this->getQuestionService()->judgeQuestions($answers, true);

        $answers = $this->makeScores($answers, $items);

        $questions = $this->completeQuestion($items, $questions);

        foreach ($answers as $questionId => $answer) {
            if ($answer['status'] == 'noAnswer') {
                $answer['answer'] = array_pad(array(), count($questions[$questionId]['answer']), '');

                $answer['testId'] = $testpaperResult['testId'];
                $answer['testPaperResultId'] = $testpaperResult['id'];
                $answer['userId'] = $userId;
                $answer['questionId'] = $questionId;
                $this->getTestpaperItemResultDao()->addItemResult($answer);
            }
        }

        //记分
        $this->getTestpaperItemResultDao()->updateItemResults($answers, $testpaperResult['id']);
        return $this->getTestpaperItemResultDao()->findTestResultsByTestpaperResultId($testpaperResult['id']);
    }

    private function formatAnswers($answers, $items) {
        $results = array();
        foreach ($items as $item) {
            if (!array_key_exists($item['questionId'], $answers)) {
                $results[$item['questionId']] = array();
            } else {
                $results[$item['questionId']] = $answers[$item['questionId']]['answer'];
            }
        }
        return $results;
    }

    public function makeScores($answers, $items) {
        foreach ($answers as $questionId => $answer) {
            if ($answer['status'] == 'right') {
                $answers[$questionId]['score'] = $items[$questionId]['score'];
            } elseif ($answer['status'] == 'partRight') {

                if ($items[$questionId]['questionType'] == 'fill') {
                    $answers[$questionId]['score'] = ($items[$questionId]['score'] * $answer['percentage']) / 100;
                    $answers[$questionId]['score'] = number_format($answers[$questionId]['score'], 1, '.', '');
                } else {
                    $answers[$questionId]['score'] = $items[$questionId]['missScore'];
                }
            } else {
                $answers[$questionId]['score'] = 0;
            }
        }
        return $answers;
    }

    public function finishTest($id, $userId) {
        $itemResults = $this->getTestpaperItemResultDao()->findTestResultsByTestpaperResultId($id);

        $testpaperResult = $this->getTestpaperResultDao()->getTestpaperResult($id);

        $testpaper = $this->getTestpaperDao()->getTestpaper($testpaperResult['testId']);
//        $correct 表示是否要批改
        if($testpaper['pattern'] == 'FileType' and $testpaper['testpaperType'] == 1){   //FileHomeWork
            $correct = true;
        }else{
            $correct = $this->isExistsEssay($itemResults);
        }

        if ($testpaperResult['testpaperType'] != 2) {
            $target = explode('-', $testpaper['target']);
            $courseId = $target[1];
            $classTest = $this->getCourseClassTestService()->getCourseClassTest(array('testId' => $testpaperResult['testId'], 'courseId' => $courseId));
            if ($classTest['correct'] == 1 && $correct == true) {
                $correct = true;
            } else {
                $correct = false;
            }
        } else {
            $correct = false;
        }

        $fields['status'] = $correct ? 'submitted' : 'finished';

        //文件作业
        if($testpaper['pattern'] == 'FileType' and $testpaper['testpaperType'] == 1){
            $fields['objectiveScore'] = 0;
            $fields['rightItemCount'] = 0;
        }else{
            $accuracy = $this->sumScore($itemResults);
            $fields['objectiveScore'] = $accuracy['sumScore'];
            $fields['rightItemCount'] = $accuracy['rightItemCount'];
        }

        if (!$correct) {
            $fields['score'] = $fields['objectiveScore'];
        }

        $fields['submitTime'] = time();

        $testpaperResult = $this->getTestpaperResultDao()->updateTestpaperResult($id, $fields);

        //发布动态。。。
//        $this->getStatusService()->publishStatus(array(
//            'type' => 'finished_testpaper',
//            'objectType' => 'testpaper',
//            'objectId' => $testpaper['id'],
//            'properties' => array(
//                'testpaper' => $this->simplifyTestpaper($testpaper),
//                'result' => $this->simplifyTestpaperResult($testpaperResult),
//            )
//        ));

        return $testpaperResult;
    }

    public function isExistsEssay($itemResults) {
        $questions = $this->getQuestionService()->findQuestionsByIds(ArrayToolkit::column($itemResults, 'questionId'));
        foreach ($questions as $value) {
            if ($value['type'] == 'essay') {
                return true;
            }
        }
        return false;
    }

    private function sumScore($itemResults) {
        $score = 0;
        $rightItemCount = 0;
        foreach ($itemResults as $itemResult) {
            $score += $itemResult['score'];
            if ($itemResult['status'] == 'right') {
                $rightItemCount++;
            }
        }
        return array(
            'sumScore' => $score,
            'rightItemCount' => $rightItemCount
        );
    }

    public function makeTeacherFinishTest($id, $paperId, $teacherId, $field) {
        $testResults = array();

        $teacherSay = $field['teacherSay'];
        unset($field['teacherSay']);


        $items = $this->getTestpaperItemDao()->findItemsByTestpaperId($paperId);
        $items = ArrayToolkit::index($items, 'questionId');

        $userAnswers = $this->getTestpaperItemResultDao()->findTestResultsByTestpaperResultId($id);
        $userAnswers = ArrayToolkit::index($userAnswers, 'questionId');

        foreach ($field as $key => $value) {
            $keys = explode('_', $key);

            if (!is_numeric($keys[1])) {
                E('得分必须为数字！');
            }

            $testResults[$keys[1]][$keys[0]] = $value;
            $userAnswer = $userAnswers[$keys[1]]['answer'];
            if ($keys[0] == 'score') {
                if ($value == $items[$keys[1]]['score']) {
                    $testResults[$keys[1]]['status'] = 'right';
                } elseif ($userAnswer[0] == '') {
                    $testResults[$keys[1]]['status'] = 'noAnswer';
                } else {
                    $testResults[$keys[1]]['status'] = 'wrong';
                }
            }
        }
        //是否要加入教师阅卷的锁
        $this->getTestpaperItemResultDao()->updateItemEssays($testResults, $id);

        $this->getQuestionService()->statQuestionTimes($testResults);

        $testpaperResult = $this->getTestpaperResultDao()->getTestpaperResult($id);

        $subjectiveScore = array_sum(ArrayToolkit::column($testResults, 'score'));

        $totalScore = $subjectiveScore + $testpaperResult['objectiveScore'];

        return $this->getTestpaperResultDao()->updateTestpaperResult($id, array(
                    'score' => $totalScore,
                    'subjectiveScore' => $subjectiveScore,
                    'status' => 'finished',
                    'checkTeacherId' => $teacherId,
                    'checkedTime' => time(),
                    'teacherSay' => $teacherSay
        ));
    }


    //批阅完成文件作业
    public function makeFileHomeworkFinishTest($id, $paperId, $teacherId, $field){
        doLog(json_encode($field));
        $score = isset($field['score_'.$id]) ? floatval($field['score_'.$id]) : 0;
        $teacherSay = isset($field['teacherSay']) ? trim($field['teacherSay']) : "";
        $subjectiveScore = $score;

        return $this->getTestpaperResultDao()->updateTestpaperResult($id, array(
            'score' => $score,
            'subjectiveScore' => $subjectiveScore,
            'status' => 'finished',
            'checkTeacherId' => $teacherId,
            'checkedTime' => time(),
            'teacherSay' => $teacherSay
        ));
    }

    public function finishTestpaper($resultId) {
        
    }

    public function submitTestpaperAnswer($id, $answers) {
        if (empty($answers)) {
            return array();
        }

        $user = $this->getCurrentUser();

        $testpaperResult = $this->getTestpaperResultDao()->getTestpaperResult($id);

        if ($testpaperResult['userId'] != $user['id']) {
            E('无权修改其他学员的试卷！');
        }

        if (in_array($testpaperResult['status'], array('submitted', 'finished'))) {
            E("已经交卷的试卷不能更改答案!");
        }

        //已经有记录的
        $itemResults = $this->filterTestAnswers($testpaperResult['id'], $answers);
        $itemIdsOld = ArrayToolkit::index($itemResults, 'questionId');

        $answersOld = ArrayToolkit::parts($answers, array_keys($itemIdsOld));

        if (!empty($answersOld)) {
            $this->getTestpaperItemResultDao()->updateItemAnswers($testpaperResult['id'], $answersOld);
        }
        //还没记录的
        $itemIdsNew = array_diff(array_keys($answers), array_keys($itemIdsOld));

        $answersNew = ArrayToolkit::parts($answers, $itemIdsNew);

        if (!empty($answersNew)) {
            $this->getTestpaperItemResultDao()->addItemAnswers($testpaperResult['id'], $answersNew, $testpaperResult['testId'], $user['id']);
        }

        //测试数据
        return $this->filterTestAnswers($testpaperResult['id'], $answers);
    }

    private function filterTestAnswers($testpaperResultId, $answers) {
        return $this->getTestpaperItemResultDao()->findTestResultsByItemIdAndTestId(array_keys($answers), $testpaperResultId);
    }

    public function reviewTestpaper($resultId, $items, $remark = null) {
        
    }

    public function updateTestpaperResult($id) {
        $testpaperResult = $this->getTestpaperResultDao()->getTestpaperResult($id);

        $fields['updateTime'] = time();

        $fields['submitTime'] = time();

        $this->getTestpaperResultDao()->updateTestpaperResultActive($testpaperResult['testId'], $testpaperResult['userId']);

        return $this->getTestpaperResultDao()->updateTestpaperResult($id, $fields);
    }

    public function getTestpaperItems($testpaperId) {
        return $this->getTestpaperItemDao()->findItemsByTestPaperId($testpaperId);
    }

    public function addItem($testpaperId, $question) {
        $testpaper = $this->getTestpaper($testpaperId);
        $item['questionType'] = $question['type'];
        $item['parentId'] = $question['parentId'];
        $item['questionId'] = $question['id'];
        $metas = empty($testpaper['metas']) ? array() : $testpaper['metas'];
        $metas['question_type_seq'] = is_array($metas['question_type_seq']) ? $metas['question_type_seq'] : array();
        array_push($metas['question_type_seq'], $item['questionType']);
        $metas['question_type_seq'] = array_unique($metas['question_type_seq']);
        sort($metas['question_type_seq']);

        if (empty($metas['missScore'])) {
            $metas['missScore'] = array("choice" => "0", "uncertain_choice" => "0");
        }
        $totalScore = $testpaper['score'] + $question['score'];
        $item['testId'] = $testpaperId;
        $item['score'] = $question['score'];

        $this->getTestpaperItemDao()->addItem($item);
        $this->getTestpaperDao()->updateTestpaper($testpaper['id'], array(
            'itemCount' => $testpaper['itemCount'] + 1,
            'score' => $totalScore,
            'metas' => $metas,
        ));
    }

    private function questionTypeSeq($data) {
        foreach ($data as $v) {
            if ($v == 'single_choice') {
                $datalist[0] = $v;
            }
            if ($v == 'choice') {
                $datalist[1] = $v;
            }
            if ($v == 'determine') {
                $datalist[2] = $v;
            }
            if ($v == 'fill') {
                $datalist[3] = $v;
            }
            if ($v == 'essay') {
                $datalist[4] = $v;
            }
        }
        return $datalist;
    }

    public function updateTestpaperItems($testpaperId, $items) {

        $testpaper = $this->getTestpaper($testpaperId);
        if (empty($testpaperId)) {
            throw $this->createServiceException();
        }
        $existItems = $this->getTestpaperItems($testpaperId);

        $existItems = ArrayToolkit::index($existItems, 'questionId');

        $questions = $this->getQuestionService()->findQuestionsByIds(ArrayToolkit::column($items, 'questionId'));

        if (count($items) != count($questions)) {
            E('数据缺失');
        }

        $types = array();
        $totalScore = 0;
        $seq = 1;

        $items = ArrayToolkit::index($items, 'questionId');

        foreach ($items as $questionId => $item) {
            if ($questions[$questionId]['type'] == 'material') {
                $items[$questionId]['score'] = 0;
            }
        }
        foreach ($items as $questionId => $item) {
            if ($questions[$questionId]['parentId'] > 0) {
                $items[$questions[$questionId]['parentId']]['score'] += $item['score'];
            }
        }


        foreach ($items as $item) {
            $question = $questions[$item['questionId']];
            $item['seq'] = $seq;
            if ($question['subCount'] == 0) {
                $seq++;
                $totalScore += $item['score'];
            }

            if (empty($existItems[$item['questionId']])) {
                $item['questionType'] = $question['type'];
                $item['parentId'] = $question['parentId'];
                // @todo, wellming.

                if (array_key_exists('missScore', $testpaper['metas']) and array_key_exists($question['type'], $testpaper['metas']['missScore'])) {
                    $item['missScore'] = $testpaper['metas']['missScore'][$question['type']];
                } else {
                    $item['missScore'] = 0;
                }

                $item['testId'] = $testpaperId;
                $item = $this->getTestpaperItemDao()->addItem($item);
            } else {

                $existItem = $existItems[$item['questionId']];

                if ($item['seq'] != $existItem['seq'] or $item['score'] != $existItem['score']) {
                    $existItem['seq'] = $item['seq'];
                    $existItem['score'] = $item['score'];
                    $item = $this->getTestpaperItemDao()->updateItem($existItem['id'], $existItem);
                } else {
                    $item = $existItem;
                }
                unset($existItems[$item['questionId']]);
            }

            if ($item['parentId'] == 0 && !in_array($item['questionType'], $types)) {
                $types[] = $item['questionType'];
            }
        }

        foreach ($existItems as $existItem) {
            $this->getTestpaperItemDao()->deleteItem($existItem['id']);
        }

        $metas = empty($testpaper['metas']) ? array() : $testpaper['metas'];
        $metas['question_type_seq'] = $types;


        $this->getTestpaperDao()->updateTestpaper($testpaper['id'], array(
            'itemCount' => $seq - 1,
            'score' => $totalScore,
            'metas' => $metas,
        ));
    }

    public function canTeacherCheck($id) {
        $paper = $this->getTestpaperDao()->getTestpaper($id);
        if (!$paper) {
            E('试卷不存在');
        }

        $user = $this->getCurrentUser();
        if ($user->isAdmin()) {
            return $user['id'];
        }

        $target = explode('-', $paper['target']);

        if ($target[0] == 'course') {
            $targetId = explode('/', $target[1]);
            $member = $this->getCourseService()->getCourseMember($targetId[0], $user['id']);

            // @todo: 这个是有问题的。
            if ($member['role'] == 'teacher') {
                return $user['id'];
            }
        }
        return false;
    }

    public function findTeacherTestpapersByTeacherId($teacherId) {
        $members = $this->getMemberDao()->findAllMemberByUserIdAndRole($teacherId, 'teacher');

        $targets = array_map(function($member) {
                    return "course-" . $member['courseId'];
                }, $members);

        return $this->getTestpaperDao()->findTestpaperByTargets($targets);
    }

    private function getTestpaperDao() {
        return $this->createService('Testpaper.TestpaperModel');
    }

    private function getDao() {
        return $this->createService('Testpaper.TestpaperModel');
    }

    private function getTestpaperResultDao() {
        return $this->createService('Testpaper.TestpaperResultModel');
    }

    private function getTestpaperItemDao() {
        return $this->createService('Testpaper.TestpaperItemModel');
    }

    private function getTestpaperItemResultDao() {
        return $this->createService('Testpaper.TestpaperItemResultModel');
    }

    private function getCourseService() {
        return $this->createService('Course.CourseServiceModel');
    }

    private function getQuestionService() {
        return $this->createService('Question.QuestionServiceModel');
    }

    private function getMemberDao() {
        return $this->createService('Course.CourseMemberModel');
    }

    private function getCourseMemberService() {
        return $this->createService('Course.CourseMemberServiceModel');
    }

    private function getCourseClassTestService() {
        return $this->createService('Course.CourseClassTestServiceModel');
    }

    private function simplifyTestpaper($testpaper) {
        return array(
            'id' => $testpaper['id'],
            'name' => $testpaper['name'],
            'description' => StringToolkit::plain($testpaper['description'], 100),
            'score' => $testpaper['score'],
            'passedScore' => $testpaper['passedScore'],
            'itemCount' => $testpaper['itemCount'],
        );
    }

    private function simplifyTestpaperResult($testpaperResult) {
        return array(
            'id' => $testpaperResult['id'],
            'score' => $testpaperResult['score'],
            'objectiveScore' => $testpaperResult['objectiveScore'],
            'subjectiveScore' => $testpaperResult['subjectiveScore'],
            'teacherSay' => StringToolkit::plain($testpaperResult['teacherSay'], 100),
        );
    }

    private function getStatusService() {
        return $this->createService('User.StatusServiceModel');
    }

    //获取试题
    public function getCourseQuestions($options) {
        $conditions = array();

        if (!empty($options['ranges'])) {
            $conditions['targets'] = $options['ranges'];
        } else {
            $conditions['targetPrefix'] = $options['target'];
        }

        $conditions['parentId'] = 0;

        $total = $this->getQuestionService()->searchQuestionsCount($conditions);

        $questions = $this->getQuestionService()->searchQuestions($conditions, array('createdTime', 'DESC'), 0, $total);
        if ($options['type']) {
            return $questions;
        } else {
            return ArrayToolkit::group($questions, 'type');
        }
    }

    //试卷试题排序
    public function updTestpaperSeq($id) {
        $testpaper = $this->getTestpaperDao()->getTestpaper($id);
        $questions = $this->getTestpaperItemDao()->getTestpaperItem($id);
        $quest = ArrayToolkit::group($questions, 'questionType');

        $data = $this->questionTypeSeq($testpaper['metas']['question_type_seq']);
        ksort($data);
        $testpaper['metas']['question_type_seq'] = $data;
        $question_type_seq = $testpaper['metas']['question_type_seq'];

        $list = array();
        foreach ($question_type_seq as $value) {
            $list[$value] = $quest[$value];
        }
        $seq = 1;
        foreach ($list as $val) {
            foreach ($val as $v) {
                $questions = $this->getTestpaperItemDao()->updateItem($v['id'], array('seq' => $seq));
                $seq++;
            }
        }
    }

    //xf
    public function getItemCount($testId) {
        return $this->getTestpaperDao()->getItemCount($testId);
    }

    public function getTestpaperItemQuestions($testId) {
        return $this->getTestpaperItemDao()->getTestpaperItemQuestions($testId);
    }

    public function getTestpaperItemAllQuestions($testId) {
        return $this->getTestpaperItemDao()->getTestpaperItemAllQuestions($testId);
    }

    public function updateTestpaperScore($map) {
        return $this->getTestpaperDao()->updateTestpaperScore($map);
    }

    public function updateTestpaperEveryItemScore($map) {
        return $this->getTestpaperItemDao()->updateTestpaperEveryItemScore($map);
    }

    public function updateItem1($map, $fields) {
        return $this->getTestpaperItemDao()->updateItem1($map, $fields);
    }

    public function updateItem($id, $fields) {
        return $this->getTestpaperItemDao()->updateItem($id, $fields);
    }

    public function searchTestpapersItemCount($conditions) {
        return $this->getTestpaperDao()->searchTestpapersItemCount($conditions);
    }

    public function getItemsCountByTestId($testId) {
        return $this->getTestpaperItemDao()->getItemsCountByTestId($testId);
    }

    public function getTestpaperItemQuestionIds($testId) {
        return $this->getTestpaperItemDao()->getTestpaperItemQuestionIds($testId);
    }

}