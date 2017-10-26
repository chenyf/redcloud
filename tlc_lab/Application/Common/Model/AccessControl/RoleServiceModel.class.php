<?php
namespace Common\Model\AccessControl;

use AccessControl\Service\RbacConfig;
use Common\Model\Common\BaseModel;
use Common\Traits\ServiceTrait;
use Common\Lib\ArrayToolkit;

class RoleServiceModel extends BaseModel
{

    use ServiceTrait;

    const SALES_SPECIALIST = "SALES_SPECIALIST";//销售专员
    const SALES_SUPERVISOR = "SALES_SUPERVISOR";//销售主管


    const OPEN = 1; //启用
    const CLOSE = 2;//禁用

    //默认角色
    private $defaultRole = [
        array(
            'name' => "销售专员",
            'code' => self::SALES_SPECIALIST
        ),
        array(
            'name' => '销售主管',
            'code' => self::SALES_SUPERVISOR
        ),
    ];


    private $dao = null;


    private function decorateList(&$val)
    {
        $val['count'] = $this->getRoleUserDao()->where(array('role_id' => $val['id']))->count();
    }

    /**
     * 添加默认角色
     */
    public function addDefaultRole()
    {
        $roles = $this->defaultRole;
        if (!empty($roles)) {
            $user = $this->getCurrentUser();
            foreach ($roles as &$role) {
                $role['code'] = strtoupper($role['code']);
                $role['operator'] = $user->id;
                $role['updated_time'] = time();
            }
            $this->getDao()->addAll($roles);
        }
    }

    /**
     * 启用角色
     * @param $id
     */
    public function open($id)
    {
        $this->getDao()->where(array('id' => $id))->setField('status', self::OPEN);
    }

    /**
     * 关闭角色
     * @param $id
     */
    public function close($id)
    {
        $this->getDao()->where(array('id' => $id))->setField('status', self::CLOSE);
    }

    /**
     * 获取分配给角色的节点id数组
     * @param $role_id
     * @return mixed
     */
    public function getNodeIds($role_id)
    {
        return $this->getRoleNodeDao()->where(['role_id' => $role_id])->getField('node_id', true);
    }

    /**
     * 为角色分配权限
     * @param $role_id
     * @param $data
     * @return array
     */
    public function assignAccess($role_id, $data)
    {
        $node_ids = $data['node_ids'];
        $lists = [];
        //组合数据
        if (!empty($node_ids)) {
            foreach ($node_ids as $v) {
                $node_tmp = explode('_', $v);
                $node_id = $node_tmp[0];
                $level = $node_tmp[1];
                $tmp['role_id'] = $role_id;
                $tmp['node_id'] = $node_id;
                $tmp['level'] = $level;
                $lists[] = $tmp;

            }

        }
        $this->startTrans();
        //删除旧节点
        $this->getRoleNodeDao()->where(['role_id' => $role_id])->delete();
        //添加新节点
        $res = $this->getRoleNodeDao()->storeAll($lists);
        $this->commit();

        if ($res) {
            return $this->returnMessage(true, $res);
        } else {
            return $this->returnMessage(false, $this->getRoleNodeDao()->getError());
        }
    }

    /**
     * 根据id获取指定角色下的所有用户
     * @param $role_id
     * @return mixed
     */
    public function getUserByRoleId($role_id)
    {
        $users = $this->getRoleUserDao()->where(compact('role_id'))->order('updated_time desc')->select();
        $this->getUserService()->decorationOfUserByUid($users, array(
            'uid' => 'user_id',
            'filter_column' => array('user_name' => 'nickname', 'mobile' => 'verifiedMobile', 'email' => 'email')
        ));
        $this->getUserService()->decorationOfUserByUid($users, array(
            'uid' => 'operator',
            'filter_column' => array('operator_name' => 'nickname')
        ));
        return $users;
    }

    /**
     * 根据code获取指定角色下的所有用户
     * @param $role_code
     * @return mixed
     */
    public function getUserByRoleCode($role_code)
    {
        $role_id = $this->getDao()->where(array('code' => $role_code))->getField('id');
        return $this->getUserByRoleId($role_id);

    }

    /**
     * 为指定角色增加用户
     * @param $data
     * @return array
     */
    public function createUserInRole($data)
    {
        if ($this->isExistUserInRole($data['user_id'], $data['role_id'])) {
            return $this->returnMessage(false, "用户已存在");
        }
        if ($this->getRoleUserDao()->where(array('role_id' => $data['rp'])))
            if ($res = $this->getRoleUserDao()->create($data)) {
                return $this->returnMessage(true, $this->getRoleUserDao()->store());
            } else {
                return $this->returnMessage(false, $this->getRoleUserDao()->getError());
            }

    }

    /**
     * 从指定角色中删除用户
     * @param $user_id
     * @param $role_id
     * @return mixed
     */
    public function deleteUserInRole($user_id, $role_id)
    {
        return $this->getRoleUserDao()->where(compact('user_id', 'role_id'))->delete();
    }

    /**
     * 获取用户所有节点
     * @param $userId
     * @return mixed
     */
    public function getAccessList($userId)
    {
        $node_ids = $this->getNodeIdByRoleIdForUser($userId);
        return $this->getNodeDao()->where([['id' => array('IN', $node_ids)]])->field('id,name,level,pid')->select();
    }


    /**
     * 获取用户所属角色的所有节点ID
     * @param $userId
     * @param $access
     * @return array
     */
    public function getNodeIdByRoleIdForUser($userId, $access = false)
    {
        $res = $this->getRoleNodeDao()->getNodeIdByRoleIdForUser($userId, $access);
        $node_ids = ArrayToolkit::column($res, 'node_id');
        $node_ids = array_unique($node_ids);
        return $node_ids;
    }


    /**
     * 用户是否拥有某个角色
     * @param $user_id
     * @param $role_id
     * @return bool
     */
    private function isExistUserInRole($user_id, $role_id)
    {
        return $this->getRoleUserDao()->where(compact('user_id', 'role_id'))->count() > 0;
    }


    private function getDao()
    {
        if (!$this->dao) {
            $this->dao = $this->createService('AccessControl.RoleModel');
        }
        return $this->dao;
    }

    private function getRoleNodeDao()
    {
        return $this->createService('AccessControl.RoleNodeModel');
    }


    private function getRoleUserDao()
    {
        return $this->createService('AccessControl.RoleUserModel');
    }

    private function getNodeDao()
    {
        return $this->createService('AccessControl.NodeModel');
    }

    private function getUserService()
    {
        return $this->createService('User.UserService');
    }

}