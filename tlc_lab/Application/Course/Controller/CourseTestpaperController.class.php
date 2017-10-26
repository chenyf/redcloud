<?php
namespace Course\Controller;

//use Common\Traits\CoursePermissionTrait;
use Symfony\Component\HttpFoundation\Request;

use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;

class CourseTestpaperController extends \Home\Controller\BaseController {

	//use CoursePermissionTrait;

	public function indexAction(Request $request, $id) {

		$user = $this->getCurrentUser();

		$type = I('type', 0);

		list($course, $member) = $this->getCourseService()->tryTakeCourse($id);


		$condition = [
			'type'     => $type,
			'courseId' => $id,
			'release'  => 1
		];

		list($list, $paginator) = $this->getCourseClassTestService()->paginate($request, $condition);

		foreach ($list as &$v) {
			$TestpaperResult = $this->getTestpaperService()->findTestpaperResult([
				'testId'        => $v['testId'],
				'testpaperType' => $type,
				'userId'        => $user->id,
			]);
			$v['testId']     = $v['testId'] . $type;
			$v['result']     = $TestpaperResult;
		}

		$nav   = $type == 1 ? 'homework' : 'testPaper';
		$title = $type == 1 ? '作业' : '试卷';
                $black = $member["black"]; //是否黑名单

		return $this->render("CourseTestpaper:index", compact('course', 'list', 'paginator', 'nav', 'title', 'black'));
	}

	public function doPracticeAction(Request $request, $testId){
		$targetType = $request->query->get('targetType');
		$targetId = $request->query->get('targetId');

		$testpaper = $this->getTestpaperService()->getTestpaper($testId);

		$targets = $this->get('redcloud.target_helper')->getTargets(array($testpaper['target']));

		if ($targets[$testpaper['target']]['type'] != 'course') {
			return $this->createMessageResponse('info', '练习只能属于课程');
		}

		$course = $this->getCourseService()->getCourse($targets[$testpaper['target']]['id']);

		if (empty($course) || $course['isDeleted']) {
			return $this->createMessageResponse('info', '练习所属课程不存在或已被删除！');
		}

		if (empty($testpaper)) {
			return $this->createMessageResponse('info', '该练习未发布，如有疑问请联系老师！');
		}

		if ($testpaper['status'] == 'draft') {
			return $this->createMessageResponse('info', '该练习未发布，如有疑问请联系老师！');
		}
		if ($testpaper['status'] == 'closed') {
			return $this->createMessageResponse('info', '该练习已关闭，如有疑问请联系老师！');
		}

		$currentUser = $this->getCurrentUser();
		if($currentUser->isLogin() && !$currentUser->isTeacher() && !$currentUser->isAdmin()){
			$testResult = $this->getTestpaperService()->startTestpaper($testId, array('type' => $targetType, 'id' => $targetId, 'testpaperType' => "2"));
			return $this->redirect($this->generateUrl('course_manage_show_test', array('id' => $testResult)));
		}else{
			return $this->redirect($this->generateUrl('course_browse_test', array('id' => $testId)));
		}
	}

	//对应 path => "course_browse_test"，用户未登录的情况下直接浏览
	//$id:testpaperId
	public function browseTestAction(Request $request, $id){
		$testpaper = $this->getTestpaperService()->getTestpaper($id);
		$user = $this->getCurrentUser();
		if (!$testpaper) {
			return $this->createMessageResponse('info', '习题不存在！');
		}

		if($testpaper['pattern'] == 'FileType' and $testpaper['testpaperType'] == 1){
			$testpaper['isFileHomework'] = true;
		}else{
			$testpaper['isFileHomework'] = false;
		}

		//是否已经提交过了
		$testpaperResult = $this->getTestpaperService()->findTestPaperResultByUserIdAndTestId($user['id'],$id);
		if(!empty($testpaperResult) && in_array($testpaperResult['status'],array('submitted','finished'))){
			return $this->redirect($this->generateUrl('course_manage_test_results', array('id' => $testpaperResult['id'])));
		}

		$targets = $this->get('redcloud.target_helper')->getTargets(array($testpaper['target']));

		if($testpaper['isFileHomework']){
			$items = $this->getHomeworkService()->getHomeworkFileList($id);
		}else{
			$result = $this->getTestpaperService()->showTestPaperByTestId($id);
			$items = $result['formatItems'];

			$data = $this->questionTypeSeq($testpaper['metas']['question_type_seq']);
			ksort($data);
			$testpaper['metas']['question_type_seq'] = $data;
		}

		if($testpaper['testpaperType'] == 1 && $user->isLogin() && !$user->isTeacher() && !$user->isAdmin()){ 	//如果是作业而且用户已经登录
			//当前时间在作业时限范围内
			if((time() > $testpaper["beginTime"]) && ($testpaper["endTime"] <= 0 || time() < $testpaper["endTime"])){
				$testResultId = $this->getTestpaperService()->startTestpaper($id, array('type' => "homework", 'id' => 0, 'testpaperType' => $testpaper['testpaperType']));
			}
		}else{
			$testResultId = -1;
		}

		if(!empty($testpaperResult) && in_array($testpaperResult['status'],array('submitted','finished'))){
			$isCommit = true; //已经提交过了
			$fileList = $this->getHomeworkService()->selectSubmitFileByUserIdAndTestId($user['id'],$id);
		}else{
			$isCommit = false; //还没有提交过
			$fileList = array();
		}

		$classPaper = $this->getCourseClassTestService()->getCourseClassTestByTestId($id);
		if(!empty($classPaper)){
			$classPaper['item']	=	$items;
		}

		$layout = $testpaper['isFileHomework'] ? 'file-homework-browse' : 'testpaper-browse';

		$timeLegal = false;
		if(empty($classPaper)){
			//不限时，时间合法
			$timeLegal = ($testpaper["endTime"] - $testpaper["beginTime"] >= 365 * 24 * 3600) || $testpaper["endTime"] == 0 || $testpaper["beginTime"] == 0;
		}else{
			$timeLegal = $classPaper['limit'] != 1 || (time() > $classPaper['startTime'] && time() < $classPaper['endTime']);
		}


		return $this->render('Testpaper:'.$layout, array(
			'isLogin' => $user->isLogin(),
			'isMaster' => $testpaper['createdUserId'] == $user['id'],
			'isCommit' => $isCommit,
			'items' => $items,
			'paper' => $testpaper,
			'testResultId' => $testResultId,
			'courseId' => $targets[$testpaper['target']]['id'],
			'testpaperType' => $testpaper['testpaperType'],
			'classPaper'	=>	$classPaper,
			'fileList'	=> 	$fileList,
			'timeLegal'	=>	$timeLegal,
			'isEarly'	=> !$timeLegal && $testpaper["beginTime"] != 0 && time() < $testpaper["beginTime"],
			'isLater'	=> !$timeLegal && $testpaper["endTime"] != 0 && time() > $testpaper["endTime"],
		));
	}

	//对应 path => "course_manage_show_test"，用户登录的情况下查看
	//$id:testpaper_result_id
	public function showTestAction(Request $request, $id) {
		$testpaperResult = $this->getTestpaperService()->getTestpaperResult($id);
		$user = $this->getCurrentUser();
		if (!$testpaperResult) {
			return $this->createMessageResponse('info', '习题不存在！');
		}

		if (in_array($testpaperResult['status'], array('submitted', 'finished')) && $user->isLogin()) {
			return $this->redirect($this->generateUrl('course_manage_test_results', array('id' => $testpaperResult['id'])));
		}

		$testpaper = $this->getTestpaperService()->getTestpaper($testpaperResult['testId']);
		$targets = $this->get('redcloud.target_helper')->getTargets(array($testpaper['target']));
		//判断组的入场时间和交卷时间 不同条件  1.是否可以考试 2.是否
		$result = $this->getTestpaperService()->showTestpaper($id);
		$items = $result['formatItems'];
		$total = $this->makeTestpaperTotal($testpaper, $items);

		$data = $this->questionTypeSeq($testpaper['metas']['question_type_seq']);
		ksort($data);
		$testpaper['metas']['question_type_seq'] = $data;

		$favorites = $this->getQuestionService()->findAllFavoriteQuestionsByUserId($testpaperResult['userId'], $testpaperResult['testId']);

		return $this->render('Testpaper:testpaper-show', array(
			'isLogin' => $user->isLogin(),
			'items' => $items,
			'paper' => $testpaper,
			'paperResult' => $testpaperResult,
			'favorites' => ArrayToolkit::column($favorites, 'questionId'),
			'id' => $id,
			'total' => $total,
			'courseId' => $targets[$testpaper['target']]['id'],
			'testpaperType' => $testpaperResult['testpaperType']
		));
	}

	public function latestBlockAction(Request $request){
		$courseId = I('id') ? : 0;
		$conditions = array(
			'target' => "course-".$courseId,
			'testpaperType' => 1,
			'status' => 'open'
		);

//		list($list, $paginator) = $this->getCourseClassTestService()->paginate($request, $conditions, 'course_class');
		list($list, $paginator) = $this->getTestpaperService()->paginate($request, $conditions, 'course_class');

		foreach ($list as $key=>$item) {
			$fileList = $this->getHomeworkService()->getHomeworkFileList($item['id']);
			$list[$key]['fileCount'] = ( empty($fileList) || !is_array($fileList) ) ? 0 : count($fileList);
		}

		return $this->render('CourseTestpaper:lastest-block', compact('list', 'paginator'));
	}

	protected function getCourseService()
	{
		return createService('Course.CourseService');
	}

	private function getCourseClassTestService() {
		return createService('Course.CourseClassTestService');
	}

	protected function getTestpaperService() {
		return createService('Testpaper.TestpaperService');
	}

	protected function getCourseMemberService() {
		return createService('Course.CourseMemberService');
	}

	private function getQuestionService() {
		return createService('Question.QuestionService');
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

	private function makeTestpaperTotal($testpaper, $items) {
		$total = array();
		foreach ($testpaper['metas']['question_type_seq'] as $type) {
			if (empty($items[$type])) {
				$total[$type]['score'] = 0;
				$total[$type]['number'] = 0;
				$total[$type]['missScore'] = 0;
			} else {
				$total[$type]['score'] = array_sum(ArrayToolkit::column($items[$type], 'score'));
				$total[$type]['number'] = count($items[$type]);
				if (array_key_exists('missScore', $testpaper['metas']) and array_key_exists($type, $testpaper["metas"]["missScore"])) {
					$total[$type]['missScore'] = $testpaper["metas"]["missScore"][$type];
				} else {
					$total[$type]['missScore'] = 0;
				}
			}
		}

		return $total;
	}

	private function getHomeworkService(){
		return createService('Homework.HomeworkService');
	}

}