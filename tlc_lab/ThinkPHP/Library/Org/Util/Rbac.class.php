<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Org\Util;

use Think\Db;

/**
 * +------------------------------------------------------------------------------
 * 基于角色的数据库方式验证类
 * +------------------------------------------------------------------------------
 */
// 配置文件增加设置
// USER_AUTH_ON 是否需要认证
// USER_AUTH_TYPE 认证类型
// USER_AUTH_KEY 认证识别号
// REQUIRE_AUTH_MODULE  需要认证模块
// NOT_AUTH_MODULE 无需认证模块
// USER_AUTH_GATEWAY 认证网关
// RBAC_DB_DSN  数据库连接DSN
// RBAC_ROLE_TABLE 角色表名称
// RBAC_USER_TABLE 用户表名称
// RBAC_ACCESS_TABLE 权限表名称
// RBAC_NODE_TABLE 节点表名称
/*
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `think_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `think_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `think_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `think_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

class Rbac {
	// 认证方法
	static public function authenticate($map, $model = '') {
		if (empty($model)) $model = C('USER_AUTH_MODEL');
		//使用给定的Map进行认证
		return M($model)->where($map)->find();
	}

	//用于检测用户权限的方法,并保存到Session中
	static function saveAccessList($authId = null) {
		if (null === $authId) $authId = $_SESSION[C('USER_AUTH_KEY')];
		// 如果使用普通权限模式，保存当前用户的访问权限列表
		// 对管理员开发所有权限
		if (C('USER_AUTH_TYPE') != 2 && !$_SESSION[C('ADMIN_AUTH_KEY')]) {
			//保存公共节点列表，公共节点任何角色都可以访问
			$_SESSION['_PUBLIC_ACCESS_LIST'] = self::getPublicAccessList();
			//保存当前用户的公共节点
			$_SESSION['_USER_PUBLIC_ACCESS_LIST'] = self::getUserPublicAccessList($authId);
			//保存用户权限列表
			$_SESSION['_ACCESS_LIST'] = self::getAccessList($authId);
		}
		return;
	}

	// 取得模块的所属记录访问权限列表 返回有权限的记录ID数组
	static function getRecordAccessList($authId = null, $module = '') {
		if (null === $authId) $authId = $_SESSION[C('USER_AUTH_KEY')];
		if (empty($module)) $module = CONTROLLER_NAME;
		//获取权限访问列表
		$accessList = self::getModuleAccessList($authId, $module);
		return $accessList;
	}

	//检查当前操作是否需要认证
	static function checkAccess() {
		//如果项目要求认证，并且当前模块需要认证，则进行权限认证
		if (C('USER_AUTH_ON')) {
			$_module = array();
			$_action = array();
			if ("" != C('REQUIRE_AUTH_MODULE')) {
				//需要认证的模块
				$_module['yes'] = C('REQUIRE_AUTH_MODULE');
				array_walk($_module['yes'], function (&$val) {
					$val = strtoupper($val);
				});
			} else {
				//无需认证的模块
				$_module['no'] = C('NOT_AUTH_MODULE');
				array_walk($_module['no'], function (&$val) {
					$val = strtoupper($val);
				});
			}
			//检查当前模块是否需要认证
			if ((!empty($_module['no']) && !in_array(strtoupper(MODULE_NAME) . '_' . strtoupper(CONTROLLER_NAME), $_module['no'])) || (!empty($_module['yes']) && in_array(strtoupper(MODULE_NAME) . '_' . strtoupper(CONTROLLER_NAME), $_module['yes']))) {
				if ("" != C('REQUIRE_AUTH_ACTION')) {
					//需要认证的操作
					$_action['yes'] = explode(',', strtoupper(C('REQUIRE_AUTH_ACTION')));
				} else {
					//无需认证的操作
					$_action['no'] = C('NOT_AUTH_ACTION');
					array_walk($_action['no'], function (&$val) {
						$val = strtoupper($val);
					});
				}
				//检查当前操作是否需要认证
				if ((!empty($_action['no']) && !in_array(strtoupper(MODULE_NAME) . '_' . strtoupper(CONTROLLER_NAME) . '_' . strtoupper(ACTION_NAME), $_action['no'])) || (!empty($_action['yes']) && in_array(strtoupper(ACTION_NAME), $_action['yes']))) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		return false;
	}

	// 登录检查
	static public function checkLogin() {
		//检查当前操作是否需要认证
		if (self::checkAccess()) {
			//检查认证识别号
			if (!$_SESSION[C('USER_AUTH_KEY')]) {
				if (C('GUEST_AUTH_ON')) {
					// 开启游客授权访问
					if (!isset($_SESSION['_ACCESS_LIST']))
						// 保存游客权限
						self::saveAccessList(C('GUEST_AUTH_ID'));
				} else {
					// 禁止游客访问跳转到认证网关
					redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
				}
			}
		}
		return true;
	}

	//权限认证的过滤器方法
	static public function AccessDecision($appName = MODULE_NAME) {
		//检查是否需要认证
		if (self::checkAccess()) {
			//存在认证识别号，则进行进一步的访问决策
			$accessGuid = md5($appName . CONTROLLER_NAME . ACTION_NAME);
			if (empty($_SESSION[C('ADMIN_AUTH_KEY')])) {
				if (C('USER_AUTH_TYPE') == 2) {

					//加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
					//通过数据库进行访问检查
					$accessList        = self::getAccessList($_SESSION[C('USER_AUTH_KEY')]);
					$public_accessList = self::getPublicAccessList();

					$user_public_accessList = self::getUserPublicAccessList($_SESSION[C('USER_AUTH_KEY')]);

				} else {
					// 如果是管理员或者当前操作已经认证过，无需再次认证
					if ($_SESSION[$accessGuid]) {
						return true;
					}
					//登录验证模式，比较登录后保存的权限访问列表
					$accessList        = $_SESSION['_ACCESS_LIST'];
					$public_accessList = $_SESSION['_PUBLIC_ACCESS_LIST'];
					$user_public_accessList = $_SESSION['_USER_PUBLIC_ACCESS_LIST'];
				}


				//判断是否是公共节点，如果是则认证直接通过
				if (in_array(strtoupper($appName).'_'.strtoupper(CONTROLLER_NAME), $public_accessList['public_controller'])) {
					$_SESSION[$accessGuid] = true;
					return true;
				}
				if (in_array(strtoupper($appName).'_'.strtoupper(CONTROLLER_NAME).'_'.strtoupper(ACTION_NAME), $public_accessList['public_action'])) {
					$_SESSION[$accessGuid] = true;
					return true;
				}

				//判断当前app，是否在当前用户的公共节点中，如果是则认证直接通过
				if (in_array(strtoupper($appName), $user_public_accessList['app'])) {
					$_SESSION[$accessGuid] = true;
					return true;
				}

				//判断当前controller，是否在当前用户的公共节点中，如果是则认证直接通过
				if (in_array(strtoupper($appName).'_'.strtoupper(CONTROLLER_NAME), $user_public_accessList['controller'])) {
					$_SESSION[$accessGuid] = true;
					return true;
				}

				//判断是否为组件化模式，如果是，验证其全模块名
				if (!isset($accessList[strtoupper($appName)][strtoupper(CONTROLLER_NAME)][strtoupper(ACTION_NAME)])) {
					$_SESSION[$accessGuid] = false;
					return false;
				} else {
					$_SESSION[$accessGuid] = true;
				}

			} else {
				//管理员无需认证
				return true;
			}
		}
		return true;
	}

	/**
	 * 获取所有公共节点
	 * @return mixed
	 */
	static public function getPublicAccessList() {
		return self::getAccessService()->getPublicAccessList();
	}

	/**
	 * 获取用户的所有公共节点
	 * @param $userId
	 * @return mixed
	 */
	static public function getUserPublicAccessList($userId){
		return self::getRoleService()->getUserPublicNodes($userId);
	}


	/**
	 * 取得当前认证号的所有权限列表
	 * @param $authId
	 * @return array
	 */
	static public function getAccessList($authId) {
		$access_list = self::getRoleService()->getAccessList($authId);
		$_apps = array_filter($access_list,function($val){
			return $val['level'] == 1;
		});
		$access = array();
		foreach($_apps as $app){
			foreach($access_list as $module){
				if($module['level'] == 2 && $module['pid'] == $app['id']){
					$_action = [];
					foreach($access_list as $action){
						if($action['level'] == 3 && $action['pid'] == $module['id']) {
							$_action[$action['name']] = $action['id'];
						}
					}
					$access[strtoupper($app['name'])][strtoupper($module['name'])] = array_change_key_case($_action, CASE_UPPER);
				}
			}
		}
		return $access;
	}

	// 读取模块所属的记录访问权限
	static public function getModuleAccessList($authId, $module) {
		// Db方式
		$db     = Db::getInstance(C('RBAC_DB_DSN'));
		$table  = array('role' => C('RBAC_ROLE_TABLE'), 'user' => C('RBAC_USER_TABLE'), 'access' => C('RBAC_ACCESS_TABLE'));
		$sql    = "select access.node_id from " .
			$table['role'] . " as role," .
			$table['user'] . " as user," .
			$table['access'] . " as access " .
			"where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and  access.module='{$module}' and access.status=1";
		$rs     = $db->query($sql);
		$access = array();
		foreach ($rs as $node) {
			$access[] = $node['node_id'];
		}
		return $access;
	}

	static private function getAccessService() {
		return createService('Access.AccessService');
	}

	static protected function getRoleService(){
		return createService('Role.RoleService');
	}
}