<?php
namespace Home\Controller;

use Common\Lib\Paginator;

class CategoryController extends BaseController {
	public function allAction() {
		$categories = $this->getCategoryService()->findCategories(1);

		$data = array();
		foreach ($categories as $category) {
			$data[$category['id']] = array($category['name'], $category['parentId']);
		}

		return $this->createJsonResponse($data);
	}

	public function topCategoryAction(Request $request) {
		$data        = array();
		$queryString = $request->query->get('q');
		$callback    = $request->query->get('callback');
		$categorys   = $this->getCategoryService()->getCategoryByLikeName($queryString);
		foreach ($categorys as $category) {
			if ($category['isDelete'] == 1) $category['name'] = "【已删除】" . $category['name'];
			$data[] = array('id' => $category['id'], 'name' => $category['name']);
		}

		return $this->createJsonResponse($data);
	}

	public function getCategoryJsonAction() {

		$tree = $this->getCategoryService()->getCategoryDao()->where(array('isDelete' => 0))->field('id,name,parentId,isLeafNode,courseCode')->findAllCategories();

		$tree = $this->getCategoryService()->generateCategory($tree);

		$this->ajaxReturn($tree);

	}
        
	/**
	 * 根据分类id获取此分类下的分类信息
	 * @param \Home\Controller\Request $request
	 * @author LiangFuJian <liangfujian@redcloud.com>
	 * @date 2015-10-24
	 */
	public function getCategoryTreeByIdAction(Request $request){

		$categoryId = $request->query->get('categoryId');

		$tree = $this->getCategoryService()->getCategoryTreeById(intval($categoryId));

		$this->ajaxReturn($tree);

	}
        

	private function getCategoryDao() {
		return createService("Taxonomy.CategoryModel");
//        return $this->createDao("Taxonomy.Category");
	}

	private function getCategoryService() {
		return createService('Taxonomy.CategoryService');
	}

	protected function getCourseService() {
		return createService('Course.CourseService');
	}

}