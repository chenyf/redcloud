<?php
namespace Common\Model\User;
use Think\Model;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Org\Util\Rbac;

class CurrentUserModel extends Model implements AdvancedUserInterface, \ArrayAccess {

	protected $data;

	public function setData(array $data) {
		$this->data = $data;
	}

	public function __set($name, $value) {
		if (array_key_exists($name, $this->data)) {
			$this->data[$name] = $value;
		}
		E("{$name} is not exist in CurrentUser.");
	}

	public function __get($name) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}

		E("{$name} is not exist in CurrentUser.");
	}

	public function __isset($name) {
		return isset($this->data[$name]);
	}

	public function __unset($name) {
		unset($this->data[$name]);
	}

	public function offsetExists($offset) {
		return $this->__isset($offset);
	}

	public function offsetGet($offset) {
		return $this->__get($offset);
	}

	public function offsetSet($offset, $value) {
		return $this->__set($offset, $value);
	}

	public function offsetUnset($offset) {
		return $this->__unset($offset);
	}

	public function getRoles() {
		return $this->roles;
	}

	public function getPassword() {
		return $this->password;
	}

	public function getSalt() {
		return $this->salt;
	}

	public function getUsername() {
		return $this->email;
	}

	public function getId() {
		return $this->id;
	}

	public function eraseCredentials() {

	}

	public function isAccountNonExpired() {
		return true;
	}

	public function isAccountNonLocked() {
		return !$this->locked;
	}

	public function isCredentialsNonExpired() {
		return true;
	}

	public function isEnabled() {
		return true;
	}

	public function isEqualTo(UserInterface $user) {
		//UserInterface
		if ($this->email !== $user->getUsername()) {
			return false;
		}
		if (array_diff($this->roles, $user->getRoles())) {
			return false;
		}
		if (array_diff($user->getRoles(), $this->roles)) {
			return false;
		}
		return true;
	}

	public function isLogin() {
		return empty($this->id) ? false : true;
	}

	public function isAdmin() {
		if (count(array_intersect($this->getRoles(), array('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'))) > 0) {
			return true;
		}
		return false;
	}

	public function isTeacher() {
		return in_array('ROLE_TEACHER', $this->getRoles());
	}

	public function fromArray(array $user) {
		$this->data = $user;
		return $this;
	}

	public function toArray() {
		return $this->data;
	}

	/**
	 * 获取用户老师身份的分类
	 * @author fubaosheng 2015-05-04
	 */
	public function getTeacherCategoryId() {
		return $this->teacherCategoryId ?: 0;
	}

	/**
	 * 获取用户管理员身份的分类
	 * @author fubaosheng 2015-05-04
	 */
	public function getAdminCategoryIds() {
		if (empty($this->adminCategoryIds)) {
			return array();
		} else {
			return explode(",", trim($this->adminCategoryIds, ","));
		}
	}

	/**
	 * 获取用户自定义的角色
	 * @author fubaosheng 2015-05-04
	 */
	public function getDefineRoles() {
		if (empty($this->defineRoles)) {
			return array();
		} else {
			return json_decode($this->defineRoles, true);
		}
	}

	public function is_super_admin(){
		return  boolval($this->super_admin);
	}

	//是否加入班级
	public function hasClass(){
		$classId = createService('Group.GroupService')->getGroupIdsByUid($this->getId());
		return empty($classId) ? false : true;
	}

    //判断是否本校学生
    public function isSchoolStudent(){
        if($this->hasClass() || $this->studNum){
            return true;
        }else{
            return false;
        }
    }

	/**
	 * 判断用户是否有操作权限
	 * ----------------------
	 * $user = $this->getCurrentUser();
	 * 例:
	 * if(!$user->can('Home','Course','createAction')){
	 *      E('没有权限')
	 * }
	 * 或者:
	 * if(!$user->can('Admin')){
	 *      E('没有权限访问')
	 * }
	 * --------------------------
	 * @param null $app
	 * @param null $controller
	 * @param null $action
	 * @return bool
	 */
	public function can($app = null, $controller = null, $action = null) {
		if (!$this->is_super_admin()) { //超级管理员无需认证

			$userId     = $this->id;
			$action = rtrim(strtoupper($action),'ACTION').'ACTION';
			$controller = trim(strtoupper($controller));
			$app = trim(strtoupper($app));

			//公共节点
			$public_access_list = Rbac::getPublicAccessList();
			if (in_array($app.'_'.$controller, $public_access_list['public_controller'])) {
				return true;
			}
			if (in_array($app.'_'.$controller.'_'.$action, $public_access_list['public_action'])) {
				return true;
			}

			//用户所属角色公共节点
			$user_public_access_list = Rbac::getUserPublicAccessList($userId);
			if (in_array($app, $user_public_access_list['app'])) {
				return true;
			}
			if (in_array($app.'_'.$controller, $user_public_access_list['controller'])) {
				return true;
			}

			//用户权限节点
			$accessList = Rbac::getAccessList($userId);

			if ($app) {
				if (!isset($accessList[$app])) {
					return false;
				}
			}
			if ($controller) {
				if (!isset($accessList[$app][$controller])) {
					return false;
				}
			}
			if ($action) {
				if (!isset($accessList[$app][$controller][$action])) {
					return false;
				}
			}
		}
		return true;
	}
}

?>
