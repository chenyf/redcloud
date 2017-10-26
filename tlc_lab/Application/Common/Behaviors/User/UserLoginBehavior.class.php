<?php
/*
 * 用户登录后
 * @author     wanglei@redcloud.com
 * @created_at    16/3/31 上午10:20
 */
namespace  Common\Behaviors\User;

use Think\Behavior;
use AccessControl\Service\Rbac;
use AccessControl\Service\RbacConfig;

class UserLoginBehavior extends Behavior
{

	public function run(&$user)
	{
		$_SESSION[RbacConfig::getConfig("USER_AUTH_KEY")] = $user->id;

		//管理员无需认证
		if ($user->isAdmin()) {
			session(RbacConfig::getConfig("ADMIN_AUTH_KEY"), true);
		}
		//保存访问权限列表
		Rbac::saveAccessList();
	}


}