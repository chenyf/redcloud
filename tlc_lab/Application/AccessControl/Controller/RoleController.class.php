<?php
/*
 * 角色管理控制器
 * @author     wanglei@redcloud.com
 * @created_at    16/3/29 上午11:38
 */

namespace AccessControl\Controller;

use Symfony\Component\HttpFoundation\Request;
use AccessControl\Service\Rbac;
class RoleController extends BaseController
{

    private $roleService;
    private $nodeService;


    public function __construct()
    {

        parent::__construct();
        $this->roleService = createService('AccessControl.RoleService');
        $this->nodeService = createService('AccessControl.NodeService');
        $this->assign('roleService', $this->roleService);
    }


    /**
     * 浏览角色
     * @param Request $request
     * @return \Home\Controller\Response
     */
    public function indexAction(Request $request)
    {
        list($roles, $paginator) = $this->roleService->paginate($request, I('get.'), true);
        if(empty($roles)) {
            $this->roleService->addDefaultRole();
            list($roles, $paginator) = $this->roleService->paginate($request, I('get.'), true);
        };
        return $this->render('role/index', compact('roles', 'paginator'));
    }

    
    
    /**
     * 创建角色页面
     */
    public function createAction()
    {
        return $this->render('role/create');

    }

    /**
     * 存储角色
     */
    public function storeAction()
    {
        $return = $this->roleService->create(I('data'));
        if ($return['status']) {
            $this->success('保存成功');
        } else {
            $this->error('添加失败: ' . $return['message']);
        }
    }

    /**
     * 修改角色
     * @param $id
     */
    public function editAction($id)
    {
        $data = $this->roleService->find($id);
        return $this->render('role/create', compact('data'));

    }

    /**
     * 更新角色
     */
    public function updateAction()
    {
        $return = $this->roleService->update(I('data'));
        if (empty($return['message'])) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败: ' . $return['message']);
        }
    }

    /**
     * 禁用角色
     * @param $id
     */
    public function closeAction($id)
    {
        $this->roleService->close($id);
        $this->success('操作成功');
    }

    /**
     * 启用角色
     * @param $id
     */
    public function openAction($id)
    {
        $this->roleService->open($id);
        $this->success('操作成功');
    }

    /**
     * 为角色分配权限页面
     * @param  $id
     */
    public function assignAccessAction($id)
    {
        $nodes = $this->nodeService->getNodeArr();
        $node_ids = $this->roleService->getNodeIds($id);
        $roles = $this->roleService->search();
        return $this->render('role/assign-access', compact('nodes', 'node_ids', 'roles'));
    }

    /**
     * 更新权限
     */
    public function updateAccessAction($id)
    {
        $return = $this->roleService->assignAccess($id, I('post.'));
        if ($return['status']) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败：' . $return['message']);
        }

    }

    /**
     * 为用户分配角色
     * @param $uid
     */
    public function assignRoleAction($uid)
    {
        $roles = $this->roleService->getUserRole($uid);
        return $this->render('role/assign-role', compact('roles'));
    }

    /**
     * 浏览角色的所有用户
     * @param $id
     */
    public function userAction($id)
    {
        $roles = $this->roleService->search();
        $users = $this->roleService->getUserByRoleId($id);
        return $this->render('role/role-user',compact('users','roles'));
    }

    /**
     * 删除指定角色中的用户
     * @param $id
     * @param $user_id
     */
    public function deleteUserAction($id, $user_id)
    {
        $this->roleService->deleteUserInRole($user_id, $id);
        $this->success('操作成功');
    }

    /**
     * 添加用户到角色中
     */
    public function createUserAction($id)
    {
        $role = $this->roleService->find($id);
        if($_POST){
            $return = $this->roleService->createUserInRole(I('post.'));
            if ($return['status']) {
                $this->success('保存成功');
            } else {
                $this->error('添加失败: ' . $return['message']);
            }
        }
        return $this->render('role/create-user-role',compact('role'));
    }

}
