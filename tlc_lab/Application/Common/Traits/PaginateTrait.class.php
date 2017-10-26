<?php
/*
 * 分页trait
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Traits;

use Common\Lib\Paginator;
use Symfony\Component\HttpFoundation\Request;

trait PaginateTrait {

	/**
	 * 分页
	 * @param $request
	 * @param $condition
	 * @param $relation
	 * @return mixed
	 */
	public function paginate(Request $request, $condition = array(),$relation = null) {

		//过滤搜索条件
		if (method_exists($this, 'SearchCondition')) {
			$condition = $this->SearchCondition($condition);
		}
		//总记录数
		$count = $this->getDao()->where($condition)->count();
		//每页记录数
		$pageRecord =  $this->page ? $this->page : 15;
		$paginator = new Paginator($request, $count, $pageRecord);
		//关联查询
		if($relation){
			$this->getDao()->relation($relation);
		}
		$list =  $this->getDao()->order($this->order)->where($condition)->limit($paginator->getOffsetCount(), $paginator->getPerPageCount())->select();
		//装饰返回数据
		if (method_exists($this, 'decorateList') && is_array($list) && !empty($list)) {
			foreach($list as &$val){
				$this->decorateList($val);
			}
		}
		//总页数
		$totalPage = ceil($count/$pageRecord);
		$list = is_array($list) ? $list : array();
		//返回  [ 数据列表  分页对象   总页数 ]
		return [$list, $paginator,$totalPage];
	}
}