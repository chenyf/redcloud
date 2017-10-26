<?php
/*
 * 公共课复制队列
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Cli\Queue;

class CourseCopyQueue {


	/**
	 * 复制课程
	 * @param array $param
	 */
	public function run($param = array()) {

		self::getCommonCourseCopyService()->copyAll($param['courseId'], $param['categoryId'], $param['webCode'],$param['coursePrice']);

	}

	private function getCommonCourseCopyService() {
		return createService('Center\Course.CommonCourseCopyService');
	}

}