<?php
/*
 * 角色—节点关联模块
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Model\AccessControl;


use Common\Model\Common\BaseModel;
use Common\Traits\DaoModelTrait;

class RoleNodeModel extends BaseModel {

	use DaoModelTrait;

	protected $tableName = 'admin_role_node';

	/**
	 * 获取用户所属角色的所有节点id
	 * @param $userId
	 * @return mixed
	 */
	public function  getNodeIdByRoleIdForUser($userId) {
		$where = 'user.id=' . $userId;
		return $this
			->join('admin_role ON admin_role_node.role_id = admin_role.id')
			->join('admin_role_user ON admin_role_user.role_id = admin_role.id')
			->join('user ON admin_role_user.user_id = user.id')
			->where($where)
			->field('admin_role_node.node_id')
			->select();
	}
	
}