<?php
/*
 * 业务层
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Model\Taxonomy;

use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;
use Common\Common\Url;

class CategoryServiceModel extends BaseModel {

	public function findCategoriesByParentId($parentId) {
		return $this->getCategoryDao()->getCategoriesByParentId($parentId);
	}

	/**
	 * 获取分类名根据ids
	 * @author fubaosheng 2015-04-30
	 */
	public function getNameByIds($ids) {
		$categoryName = $this->getCategoryDao()->getNameByIds($ids);
		return $categoryName ?: array();
	}

	/**
	 * 根据ID获取分类名
	 */
	public function getNameById($id){
		$categoryName = $this->getCategoryDao()->getNameById($id);
		return $categoryName ?: "";
	}

	/**
	 * 根据ID获取课程码
	 */
	public function getCourseCodeById($id){
		$courseCode = $this->getCategoryDao()->getCourseCodeById($id);
		return $courseCode ?: "";
	}

	/**
	 * 获取顶级分类根据name
	 * @param type $name
	 * @return type
	 */
	public function getCategoryByLikeName($name) {
		return $this->getCategoryDao()->getCategoryByLikeName($name);
	}

	public function deleteCategoryData($category) {
		unset($category['icon']);
		unset($category['path']);
		unset($category['description']);
		unset($category['ctm']);
		unset($category['isSupper']);
		unset($category['groupId']);
		return $category;
	}

	/**
	 * 获取顶级分类
	 * @param array $ids
	 * @return mixed
	 */
	public function getTopCategory($ids = array()) {
		if (is_array($ids) && !empty($ids)) {
			$topCategory = $this->getCategoryDao()->findCategoriesByIds($ids);
		} else {
			$topCategory = $this->getCategoryDao()->findAllCategoriesByParentId(0);
		}
		if ($topCategory) {
			foreach ($topCategory as $key => $value) {
				$topCategory[$key] = $this->deleteCategoryData($value);
			}
		}
		$topCategory = $this->filterDelTopCategory($topCategory);
		return $topCategory;
	}

	/**
	 * 标记删除的顶级分类
	 * @author fubaosheng 2015-05-15
	 */
	public function filterDelTopCategory($topCategory) {
		if ($topCategory) {
			foreach ($topCategory as $key => $value) {
				if ($value['isDelete'] == 1) {
					$topCategory[$key]['name'] = "【已删除】" . $value['name'];
				}
			}
		}
		return $topCategory ? array_values($topCategory) : array();
	}

	/**
	 * 获取单个分类下的信息
	 * @param type $id
	 * @return type
	 */
	public function getCategoryById($id) {
		$category = $this->getCategoryDao()->getCategory($id);
		$category = $this->deleteCategoryData($category);
		return $category ? $category : array();
	}

	/**
	 * 获取用户分类下的组织机构
         * type(0=>展示中，1=>已删除,2=>全部)
	 * @author fubaosheng 2015-05-07
	 */
	public function findUserCategoryTree($uid,$type=0) {
            $groupService     = createService("Group.GroupService");
            $userCategoryTree = $groupService->getUserCategoryTree(array('uid' => $uid));
            $userCategoryTree = $this->filterUserCategoryTree($userCategoryTree,$type);
            return $userCategoryTree;
	}

	/**
	 * 筛选用户分类下的组织机构
         * type(0=>展示中，1=>已删除,2=>全部)
	 * @author fubaosheng 2015-05-07
	 */
	public function filterUserCategoryTree($userCategoryTree,$type=2) {
            static $arr = array();
            if ($userCategoryTree) {
                foreach ($userCategoryTree as $key => $value) {
                    if($type == 0){
                        if(intval($value['isDelete']) == 1) continue;
                    }
                    if($type == 1){
                        if(intval($value['isDelete']) == 0) continue;
                    }
                    $children = $value['children'];
                    unset($value['children']);
                    $arr[$value['id']] = $value;
                    if (is_array($children) && !empty($children)) $this->filterUserCategoryTree($children,$type);
                }
            }
            return array_values($arr);
	}

	/**
	 * 筛选已删除的分类
	 * @author fubaosheng 2015-06-23
	 */
	public function filterDelCategoryTree($category) {
		if ($category) {
			foreach ($category as $key => $value) {
				if ($value['isDelete'] == 1) {
					unset($category[$key]);
				} else {
					if (!empty($value['children'])) $category[$key]['children'] = $this->filterDelCategoryTree($value['children']);
				}
			}
		}
		return array_values($category);
	}

	/**
	 * 获取分类下的子类
	 * @param type $id
	 * @return type
	 */
	public function getCategoryTreeById($id, $filter = 1) {
		$id       = $id ? $id : 0;
		$category = array();
		$category = $this->getCategoryDao()->getCategory($id);
//		if ($category) $category['iconUrl'] = Url::getCategoryIconUrl(array('iconFromType' => $category['iconFromType'], 'icon' => $category['icon']));
		if ($filter) $category = $this->deleteCategoryData($category);

		if ($id == 0)
			$categoryChildren = $this->getCategoryDao()->findAllCategoriesByParentId(0);
		else
			$categoryChildren = $this->getCategoryDao()->findAllCategoriesByParentId($category['id']);
		if ($categoryChildren) {
			foreach ($categoryChildren as $key => $value) {
				if ($id == 0) {
					$category[] = $this->getCategoryTreeById($value['id'], $filter);
				} else {
					$category['children'][] = $this->getCategoryTreeById($value['id'], $filter);
				}
			}
		} else {
			if ($category) $category['children'] = array();
		}
		return $category;
	}
        
        /**
         * 获取此分类下所有子节点的id
         * @param int $id
         * @return array
         */
        public function getLeafNodeId($id) {
            
            $return = array();
            $category = $this->getCategoryDao()->findAllCategoriesByParentId($id);

            foreach ($category as $v) {
                if ($v['isLeafNode'] == 1) {
                    $return[] = $v['id'];
                } else {
                    $leafNode = $this->getLeafNodeId($v['id']);
                    foreach($leafNode as $vo)
                        array_push($return, $vo);
                }
            }
            
            return $return;
	}

	public function getCategory($id) {
		if (empty($id)) {
			return null;
		}
		$category                 = $this->getCategoryDao()->getCategory($id);
		$category['exisLeafNode'] = $this->getLeafNode($category['id']);
		return $category;
	}

	private function getLeafNode($id) {
		$category = $this->getCategoryDao()->getCategoryByPid($id);
		if (empty($category)) {
			return 0;
		} else {
			if ($category['isLeafNode'] == 1) {
				return 1;
			} else {
				return $this->getLeafNode($category['id']);
			}
		}
	}

	/**
	 * 推荐专业
	 * @param $id
	 * @param $status
	 */
	public function recommend($id, $status) {
		$status = intval($status);
		if ($status !== 0) {
			$status = 1;
		}
		$category = $this->table('category')->where(array('id' => $id))->find();
		if (!$category || $category['isLeafNode'] != 1) {
			E('分类不存在，或不是专业');
		}
		$this->table('category')->where(array('id' => $id))->setField('isRecommend', $status);
	}

	public function getCategoryByCode($code) {
		return $this->getCategoryDao()->findCategoryByCode($code);
	}

	public function getCategoryTree() {

		$prepare = function ($categories) {
			$prepared = array();
			foreach ($categories as $category) {
				$category['uname'] = getUserName($category['createUid']);
				if (!isset($prepared[$category['parentId']])) {
					$prepared[$category['parentId']] = array();
				}
				$prepared[$category['parentId']][] = $category;
			}
			return $prepared;
		};

		$categories = $prepare($this->findCategories());

		$tree = array();
		$this->makeCategoryTree($tree, $categories, 0);

		return $tree;
	}

	public function findCategories() {
		return $this->getCategoryDao()->findCategories();
	}

	public function findAllCategoriesByParentId($parentId) {
		return $this->getCategoryDao()->findAllCategoriesByParentId($parentId);
	}

	public function findGroupRootCategories() {
		return $this->getCategoryDao()->getCategoriesByParentId(0);
	}

	public function findCategoryChildren($id){
		return $this->getCategoryDao()->getChildrenCateByPid($id);
	}

	public function findCategoryBrother($id,$include=false){
		return $this->getCategoryDao()->getBrotherCateByPid($id,$include);
	}

	//现在分类只按照学院分类：信息科学与工程学院，理学院，信息与工程学院

	/**
	 * 根据分类的ID获取旗下所有的课程编号
	 * @param $id
	 */
	public function findCourseCodeByCategoryId($id){
		if(empty($id)){
			return $this->getCourseDao()->getAllCourseNumber();
		}
		$category = $this->getCategory($id);
		if(empty($category)){
			return array();
		}

		$courseCodePrefix = $category['courseCode'];
		return $this->getCourseDao()->getAllCourseNumber($courseCodePrefix);
	}

	public function findCategoryChildrenIds($id) {
		$category = $this->getCategory($id);
		if (empty($category)) {
			return array();
		}
		$tree = $this->getCategoryTree();

		$childrenIds = array();
		$depth       = 0;
		foreach ($tree as $node) {
			if ($node['id'] == $category['id']) {
				$depth = $node['depth'];
				continue;
			}
			if ($depth > 0 && $depth < $node['depth']) {
				$childrenIds[] = $node['id'];
			}

			if ($depth > 0 && $depth >= $node['depth']) {
				break;
			}

		}

		return $childrenIds;
	}

	public function findCategoriesByIds(array $ids) {
		return ArrayToolkit::index($this->getCategoryDao()->findCategoriesByIds($ids), 'id');
	}

	public function findAllCategories() {
		return $this->getCategoryDao()->findAllCategories();
	}

	public function findCategoryParentsById($id) {

		$res = $this->getCategoryDao()->findCategoryParentsById($id);

		return array_reverse($res);
	}

	public function isCategoryCodeAvaliable($code, $exclude = null) {
		if (empty($code)) {
			return false;
		}

		if ($code == $exclude) {
			return true;
		}

		$category = $this->getCategoryDao()->findCategoryByCode($code);

		return $category ? false : true;
	}

	public function createCategory(array $category) {

		$category = ArrayToolkit::parts($category, array('description', 'name', 'code', 'weight', 'parentId', 'icon', 'isLeafNode', 'iconFromType'));
		if (!ArrayToolkit::requireds($category, array('name', 'code', 'weight', 'parentId'))) {
			throw $this->createServiceException("缺少必要参数，，添加分类失败");
		}

		$category['level']     = $this->getLevelBuyId($category['parentId']);
		$uid                   = $this->getCurrentUser()->id;
		$category['isSupper']  = isGranted('ROLE_SUPER_ADMIN') ? 1 : 0;
		$category['createUid'] = $uid;
		$category['ctm']       = time();

		$this->filterCategoryFields($category);
		$category = $this->getCategoryDao()->addCategory($category);

		$this->getLogService()->info('category', 'create', "添加分类 {$category['name']}(#{$category['id']})", $category);

		return $category;
	}

	public function updateCategory($id, array $fields) {
		$category = $this->getCategory($id);
		if (empty($category)) {
			E("分类(#{$id})不存在，更新分类失败！");
		}

		$fields = ArrayToolkit::parts($fields, array('description', 'name', 'code', 'courseCode','weight', 'parentId', 'icon', 'isLeafNode', 'iconFromType'));
		if (empty($fields)) {
			throw $this->createServiceException('参数不正确，更新分类失败！');
		}

		$this->filterCategoryFields($fields, $category);

		$this->getLogService()->info('category', 'update', "编辑分类 {$fields['name']}(#{$id})", $fields);

		return $this->getCategoryDao()->updateCategory($id, $fields);
	}

	/**
	 * 删除分类 不进行物理删除
	 * @author fubaosheng 2015-05-14
	 */
	public function deleteCategory($cid, $deleSelf = '1') {
		$category = $this->getCategory($cid);
		if (empty($category)) {
			E('Not found');
		}

		$ids = $this->findCategoryChildrenIds($cid);
		if ($deleSelf == '1') $ids[] = $cid;
		foreach ($ids as $id) {
			$this->getCategoryDao()->updateCategory($id, array('isDelete' => 1));
//            $this->getCategoryDao()->deleteCategory($id);
		}

		$this->getLogService()->info('category', 'delete', "删除分类{$category['name']}(#{$cid})");
	}

	/**
	 * 恢复删除的分类
	 * @author fubaosheng 2015-05-14
	 */
	public function recoverCategory($cid) {
		$category = $this->getCategory($cid);
		if (empty($category)) {
			E('Not found');
		}

		$topId = $this->getDelTopPid($cid);
		$ids   = $this->findCategoryChildrenIds($topId);
		array_unshift($ids, $topId);

		foreach ($ids as $id) {
			$this->getCategoryDao()->updateCategory($id, array('isDelete' => 0));
		}

		$this->getLogService()->info('category', 'recover', "恢复分类{$category['name']}(#{$cid})");
	}

	/**
	 * 获取顶级父类(已删除)
	 * @author fubaosheng 2015-06-24
	 */
	public function getDelTopPid($id) {
		$pid = $this->createDao("Taxonomy.Category")->getPidBuyId($id);
		if ($pid == 0) return $id;
		$cate = $this->createDao("Taxonomy.Category")->getParentNameByParentId($pid);
		if (!empty($cate) && $cate['isDelete'])
			return $this->getDelTopPid($pid);
		else
			return $id;
	}


	/**
	 * 获取最顶级父id
	 * @author fubaosheng 2015-05-14
	 */
	public function getTopPidById($id) {
		$pid = $this->createDao("Taxonomy.Category")->getPidBuyId($id);
		if ($pid)
			return $this->getTopPidById($pid);
		return $id;
	}

	/**
	 * 获取所有上级分类(id,name)
	 * for 面包屑
	 * @param $id
	 * @param $arr
	 * @return array
	 */
	public function getParentsById($id, $arr = array()) {
		$info  = $this->getCategoryDao()->where(array('id' => $id,'isDelete'=>0))->field('id,name,parentId')->find();
		$arr[] = $info;
		if ($info['parentId'] == 0)
			return $arr;
		$arr = $this->getParentsById($info['parentId'], $arr);
		return $arr;
	}

	/**
	 * 获取顶级分类
	 * @param $id
	 */
	public function getTopId($id){

	}


	/**
	 * 获取推荐班级
	 * @return array
	 */
	public function getRecommend() {
		$data = $this->table('category')
			->where(array('isRecommend' => 1, 'isDelete' => 0))
			->select();

		foreach ($data as &$v) {
			$v['description'] = is_null($v['description']) ? '' : $v['description'];
			$v['iconUrl']     = Url::getCategoryIconUrl(array('iconFromType' => $v['iconFromType'], 'icon' => $v['icon']));
		}

		return $data ? $data : array();
	}

	public function generateCategory($tree, $parentId = 0) {
		$list = array();
		foreach ($tree as $k => $v) {
			if($v['parentId'] == $parentId){
				$v['child'] = $this->generateCategory($tree,$v['id']);
				$list[$v['id']] = $v;
			}
		}
		return $list;

	}
        
	private function getLevelBuyId($id) {
		static $level = 1;
		if ($id != 0) {
			$pid = $this->getCategoryDao()->getPidBuyId($id);
			$level++;
			if ($pid != 0) $this->getLevelBuyId($pid);
		}
		return $level;
	}

	private function makeCategoryTree(&$tree, &$categories, $parentId) {
		static $depth = 0;
		static $leaf = false;
		if (isset($categories[$parentId]) && is_array($categories[$parentId])) {
			foreach ($categories[$parentId] as $category) {
				$depth++;
				$category['depth'] = $depth;
				$tree[]            = $category;
				$this->makeCategoryTree($tree, $categories, $category['id']);
				$depth--;
			}
		}
		return $tree;
	}

	private function filterCategoryFields(&$category, $releatedCategory = null) {
		foreach (array_keys($category) as $key) {
			switch ($key) {
				case 'name':
					$category['name'] = (string)$category['name'];
					if (empty($category['name'])) {
						throw $this->createServiceException("名称不能为空，保存分类失败");
					}
					break;
				case 'code':
					if (empty($category['code'])) {
						throw $this->createServiceException("编码不能为空，保存分类失败");
					} else {
						if (!preg_match("/^[a-zA-Z0-9_]+$/i", $category['code'])) {
							throw $this->createServiceException("编码({$category['code']})含有非法字符，保存分类失败");
						}
						if (ctype_digit($category['code'])) {
							throw $this->createServiceException("编码({$category['code']})不能全为数字，保存分类失败");
						}
						$exclude = empty($releatedCategory['code']) ? null : $releatedCategory['code'];
						if (!$this->isCategoryCodeAvaliable($category['code'], $exclude)) {
							throw $this->createServiceException("编码({$category['code']})不可用，保存分类失败");
						}
					}
					break;
				case 'groupId':
					break;
				case 'parentId':
					$category['parentId'] = (int)$category['parentId'];
					if ($category['parentId'] > 0) {
						$parentCategory = $this->getCategory($category['parentId']);
						if (empty($parentCategory) or $parentCategory['groupId'] != $category['groupId']) {
							throw $this->createServiceException("父分类(ID:{$category['groupId']})不存在，保存分类失败");
						}
					}
					break;
				case 'level' :
					$category['level'] = (int)$category['level'];
					if ($category['level'] + 1 >= C('GROUP_DEPTH')) {
						$category['isLeafNode'] = 1;
					}
					break;
			}
		}

		return $category;
	}

	public function getCategoryName($categoryId){
		return $this->getCategoryDao()->getNameById($categoryId);
	}
        
	public function getCateParentName($categoryId,$name = ""){
		if(!$categoryId){
			return $name;
		}else{
			$cate = $this->getCategoryDao()->getCategory($categoryId);
			if($cate){
				$name = $cate["name"]."—".$name;
				return $this->getCateParentName($cate["parentId"],$name);
			}else{
				return $name;
			}
		}
	}

	public function getCategoryDao() {
		return $this->createService("Taxonomy.CategoryModel");
//        return $this->createDao("Taxonomy.Category");
	}

	public function getCourseDao() {
		return $this->createService('Course.CourseModel');
	}

	private function getLogService() {
		return $this->createService("System.LogService");
	}

}