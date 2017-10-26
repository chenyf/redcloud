<?php
/*
 * 分类组件
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */

namespace Widget\Controller;
use Home\Controller\BaseController;

class CategoryController extends BaseController {

	private $categoryService;


	public function __construct(){
		parent::__construct();
		$this->categoryService = createService('Taxonomy.CategoryService');
	}

	/**
	 * 分类侧边栏
	 * @param $param
	 * @return \Home\Controller\Response
	 */
	public function sideCategoryAction($param) {
		$categories = $this->categoryService->getCategoryTree();
		$treeCate     = array();
		$topCateIdArr = array();
		$CateTreeById = array(); //所有二级分类数组
		if ($categories) {
			foreach ($categories as $cate) {
				$parentId = $cate['parentId'];
				$id       = $cate['id'];
				if ($cate['isDelete'] == 1) continue;
				if (!$parentId) {
					$topCateIdArr[]         = $id;
					$treeCate[$id]          = $cate;
					$treeCate[$id]['child'] = array();
				} elseif (in_array($parentId, $topCateIdArr)) {
					$CateTreeById[$id]                               = $cate;
					$treeCate[$parentId]['child'][$id]               = $cate;
					$treeCate[$parentId]['child'][$id]['threeChild'] = array();
				} else {
					$TopCateId   = $CateTreeById[$cate['parentId']]['parentId'];
					$treeCate[$TopCateId]['child'][$cate['parentId']]['threeChild'][$id] = $cate;
				}
			}
		}
		return $this->render('category:side-category#Widget', array(
			'treeCate' => $treeCate,
			'param'    => $param,
		));
	}


}

?>
