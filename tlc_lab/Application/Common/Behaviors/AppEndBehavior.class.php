<?php
/*
 * 应用结束时执行
 * @package    
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Behaviors;

use Think\Behavior;

class AppEndBehavior extends Behavior {

	public function run(&$param) {
		if (C('APP_XHPROF')) {
			redcloud_xhprof_disable();
		}
	}
}