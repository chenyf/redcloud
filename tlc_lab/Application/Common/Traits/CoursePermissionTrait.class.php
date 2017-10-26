<?php
/*
 * 判断课程权限
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Traits;

trait CoursePermissionTrait {

	protected $courseId;

	public function __construct() {
		parent::__construct();
		$this->courseId = I('id');

		//是否登录
		$user = $this->getCurrentUser();
		if (!$user->isLogin()) {
			return $this->createMessageResponse('info', '你好像忘了登录哦？', null, 3000, $this->generateUrl('login'));
		}

		//课程是否存在
		$course = $this->getCourseService()->getCourse($this->courseId);
		if (empty($course)) {
			return $this->createMessageResponse('error',"课程不存在，或已删除。");
		}
	}


	private function getCourseService() {
		return createService('Course.CourseService');
	}

}