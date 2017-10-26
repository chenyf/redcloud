<?php
/*
 * 应用初始化时执行
 * @package    
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Behaviors;

use Think\Behavior;

class AppInitBehavior extends Behavior {

	public function run(&$param) {
		if (C('APP_XHPROF')) {
			redcloud_xhprof_enable();
		}

		$this->sessionHandler();
	}

	/**
	 * 初始化session
	 */
	private function sessionHandler() {
		//ini_set("session.save_handler", "memcache");
		//ini_set("session.save_path", 'tcp://127.0.0.1:' . C('MEMCACHE_PORT'));
		ini_set("session.save_handler", "redis");
		ini_set("session.save_path", 'tcp://127.0.0.1:6379');
		ini_set("session.gc_maxlifetime", 43200);
//		ini_set('session.cookie_domain', C('COOKIE_FULLE_DOMAIN'));
		ini_set('session.cookie_lifetime', C('COOKIE_EXPIRE'));
	}

}
