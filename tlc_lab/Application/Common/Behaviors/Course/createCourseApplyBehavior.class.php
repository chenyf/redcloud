<?php
/*
 * 申请公共课后触发
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Behaviors\Course;

use Think\Behavior;

class createCourseApplyBehavior extends Behavior {

	public function run(&$param) {

		//修改课程状态为已申请
		$courseId = $param['courseId'];
		$this->getCourseDao()->updateCourse($courseId, [
			'relCommCourseId' => 0,
			'relCommCourseTm' => time()
		]);

		//记录日志
		$course = $this->getCourseDao()->getCourse($courseId);
		$this->Logger()->info('course', 'createCourseApply', "申请 《".$course['title']."》 为".C("PUBLIC_COURSE_NAME"));
	}

	private function Logger() {
		return createService('System.LogService');
	}

	private function getCourseDao() {
		return createService('Course.CourseModel');
	}

}