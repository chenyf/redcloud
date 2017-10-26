<?php
/*
 * 节点管理
 * @author     wanglei@redcloud.com
 * @created_at    16/3/30 上午11:32
 */
namespace AccessControl\Controller;

use Symfony\Component\HttpFoundation\Request;

class NodeController extends BaseController
{

    private $nodeService;

    public function __construct()
    {
        parent::__construct();
        if(!\Common\Lib\WebCode::isLocalCenterWeb()) E(L('no_permission_access'));
        $this->nodeService = createService('AccessControl.NodeService');
    }

    /**
     * 浏览节点
     */
    public function indexAction()
    {
        $condition['pid'] = I('pid', 0);
        $lists = $this->nodeService->search(['*'], $condition);
        $parent = $this->nodeService->find(I('pid'));
        $this->render('node/index', compact('lists', 'parent'));
    }


    /**
     * 创建节点
     */
    public function createAction()
    {
        $nodes = $this->nodeService->getNodeList();
        if ($_POST) {
            $return = $this->nodeService->create(I('post.'));
            if ($return['status']) {
                $this->success('添加成功', U('index', ['pid' => I('pid')]));
            } else {
                $this->error('添加失败：' . $return['message']);
            }
        }
        $this->render('node/edit', compact('nodes'));
    }

    /**
     * 修改节点
     * @param $id
     */
    public function editAction($id)
    {
        $data = $this->nodeService->find($id);

        if ($_POST) {
            $return = $this->nodeService->update(I('post.'));
            if ($return['status']) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败：' . $return['message']);
            }
        }

        $this->render('node/edit', compact('data'));
    }

}