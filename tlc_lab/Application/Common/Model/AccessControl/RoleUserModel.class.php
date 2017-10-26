<?php
/*
 * 角色—用户关联模块
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Model\AccessControl;


use Common\Model\Common\BaseModel;
use Common\Traits\DaoModelTrait;

class RoleUserModel extends BaseModel {

	use DaoModelTrait;

	protected $tableName = 'admin_role_user';

	//自动完成
	protected $_auto = array(
		array('updated_time', 'time', self::MODEL_BOTH, 'function'),
		array('operator', 'getCurrentUid', self::MODEL_BOTH, 'callback'),
	);

	


}