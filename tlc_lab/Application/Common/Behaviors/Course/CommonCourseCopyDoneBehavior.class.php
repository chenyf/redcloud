<?php
/*
 * 复制公共课后触发
 * @package    
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Behaviors\Course;

use Think\Behavior;

class CommonCourseCopyDoneBehavior extends Behavior {

	public function run(&$param) {
		$copy_error     = $param['copy_error'];
		$localCourse  = $param['localCourse'];
		$commonCourseId = $param['commonCourseId'];
		if (empty($copy_error)) {//成功

			//记录日志到中心站
			$this->logger('Course', 'CommonCourseCopy', '课程 ' . $commonCourseId . ' 的课程内容文件全部复制成功');

			//指定分站课程对应的公共课id
			$this->getCourseModel()->updateCourse($localCourse['id'], [
				'relCommCourseId' => $commonCourseId,
				'relCommCourseTm' => time(),
			]);

			//开启公共课
			$this->getCourseModel()->updateCourse($commonCourseId, [
				'status' => 'published',
			]);

			//向申请人发送通知
			$this->getNotificationService()->notify($localCourse['userId'],'','您申请的'.C("PUBLIC_COURSE_NAME").' <a href="'.U('Course/CourseManage/index',['id'=>$commonCourseId,'center'=>1]).'">'.$localCourse['title'].'</a> 开通成功');

		} else {//失败
			$this->logger('Course', 'CommonCourseCopy', '复制课程内容文件失败', $param);
		}
	}

	private function Logger() {
		return createService('System.LogService');
	}

	private function getCourseModel() {
		return createService('Course.Course');
	}

	private function getNotificationService(){
		return createService('User.NotificationService');
	}
}