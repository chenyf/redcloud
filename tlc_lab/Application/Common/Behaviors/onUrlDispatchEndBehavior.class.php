<?php
/*
 * 当url调度结束时触发
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Behaviors;

use Think\Behavior;

class onUrlDispatchEndBehavior extends Behavior {

	public function run(&$param) {

		// 记录center,方便切换中心库与本地库 @author 王磊 8/11
		if (!defined('CENTER')) {
			define('CENTER', (intval($_REQUEST['center']) == 1 || intval($_REQUEST['publicCourse']) == 1) ? 'center' : '');
		}
	}

}