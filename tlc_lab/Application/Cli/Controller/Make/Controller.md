<?php
namespace {{app}}\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;

class {{module}}Controller extends BaseController {

	public function indexAction(Request $request) {

		$conditions = $request->query->all();

		list($list,$paginator) = $this->get{{module}}Service()->paginate($request, $conditions);

		$this->render('{{module}}/index',compact('list','paginator'));
	}

	public function createAction(){

		if ($_POST) {
			if ($this->get{{module}}Service()->create(I('post.'))) {
				$this->success('添加成功');
			} else {
				$this->error('添加失败');
			}
		}

		$this->render('{{module}}/modal');
	}

	public function updateAction($id){

		$this->render('{{module}}/modal');
	}

	public function deleteAction($id){
		$this->get{{module}}Service()->delete($id);
		$this->success('删除成功');
    }

	private function get{{module}}Service() {
		return createService('{{app}}.{{module}}Service');
	}
}