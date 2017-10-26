<?php
/*
 * 监听 ThinkPHP 系统标签位（钩子）
 * ----------------------------------------------
 * app_init         应用初始化标签位
 * path_info        PATH_INFO检测标签位
 * app_begin        应用开始标签位
 * url_dispatch     URL调度结束标签
 * action_name      操作方法名标签位
 * action_begin     控制器开始标签位
 * view_begin       视图输出开始标签位
 * view_parse       视图解析标签位
 * template_filter  模板内容解析标签位
 * view_filter      视图输出过滤标签位
 * view_end         视图输出结束标签位
 * action_end       控制器结束标签位
 * app_end          应用结束标签位
 * ----------------------------------------------
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */


return array(

	'app_init'     => array(
		'Common\\Behaviors\\AppInitBehavior',
	),

	'app_begin'    => array(
		'Common\\Behaviors\\RegisterAppHookBehavior',
		'Behavior\CheckLangBehavior',
	),

	'url_dispatch' => array(
		'Common\\Behaviors\\onUrlDispatchEndBehavior',
	),

	'app_end'      => array(
		'Common\\Behaviors\\AppEndBehavior',
	),

);

