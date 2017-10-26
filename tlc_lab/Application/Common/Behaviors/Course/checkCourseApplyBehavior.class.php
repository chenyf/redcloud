<?php
/*
 * 审核公共课后触发
 * @package    
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Behaviors\Course;

use Think\Behavior;
use Common\Services\QueueService;
class checkCourseApplyBehavior extends Behavior {

	public function run(&$param) {
		$apply = $param['apply'];
		//记录日志
		$this->Logger('Course', 'checkCourseApply', "审核".C("PUBLIC_COURSE_NAME")."成功", $param);

		if ($apply['status'] == 2) {
			//审核通过，复制课程加入队列
			QueueService::addJob(array(
				'jobName' => 'CourseCopy',
				'param'   => $apply,
			));

		}else{
			//审核失败
			 $this->getCourseDao()->updateCourse($apply['courseId'], [
				'relCommCourseId' => -1,
			]);
			$fails = C('COUSE_APPLY_FAIL_TYPE');
			$this->getNotificationService()->notify($apply['applyUid'],'','您的课程《'.$apply['courseName'].'》申请'.C("PUBLIC_COURSE_NAME").'失败，原因: '.$fails[$apply['failType']]);
		}
	}

	private function Logger() {
		return createService('System.LogService');
	}

	private function getCommonCourseCopyService() {
		return createService('Center\Course.CommonCourseCopyService');
	}

	private function getCourseDao() {
		return createService('Course.CourseModel');
	}

	private function getNotificationService(){
		return createService('User.NotificationService');
	}
}