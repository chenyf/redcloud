<?php
/*
 * 创建课程后
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Behaviors\Course;

use Think\Behavior;

class createdCourseBehavior extends Behavior {

	public function run(&$param) {
		//创建课程聊天室
		$courseId = $param['id'];
		$groupId =  $this->getChatRoomService()->create($param['title'],$param['userId']);
		$this->getCourseDao()->updateCourse($courseId, [
			'chatRoomStatus'=>1,
			'chatRoomId'=>$groupId,
		]);
	}

	private function getChatRoomService(){
		return createService('ChatRoom.ChatRoomService');
	}

	private function getCourseDao() {
		return createService('Course.CourseModel');
	}

}