<?php
namespace Common\Traits;

use Common\Lib\Paginator;
trait ServiceTrait {


	/**
	 * 查询所有记录
	 * @param array      $field
	 * @param array      $condition
	 * @param string     $order
	 * @param bool|false $limit
	 * @return array
	 */
	public function search($field = array('*'), $condition = array(), $order = '', $limit = false) {
		$model = $this->getDao();
		$model->where($condition)->field($field);

		//排序
		if($order){
			$model->order($order);
		}

		//限制返回记录
		if($limit){
			$model->limit($limit);
		}

		$results = $model->select();

		return $results ? $results : array();
	}

	/**
	 * 设置每页记录数
	 * @param $page
	 * @return $this
	 */
	public function setPageRecord($page){
		$this->page = $page;
		return $this;
	}


	/**
	 * 设置排序
	 * @param $order
	 * @return $this
	 */
	public function setSort($order){
		$this->order = $order;
		return $this;
	}

	/**
	 * 分页
	 * @param $request
	 * @param $condition
	 * @param $relation
	 * @return mixed
	 */
	public function paginate($request, $condition = array(),$relation = null) {

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

	/**
	 * 添加数据
	 * @param $data
	 * @return array
	 */
	public function create($data) {
		if ($res = $this->getDao()->create($data)) {
			$res =  $this->returnMessage(true, $this->getDao()->add());
			if(method_exists($this,'create_after')) $this->create_after($this->getLastInsID());
			return $res;
		} else {
			return $this->returnMessage(false, $this->getDao()->getError());
		}
	}

	/**
	 * 修改数据
	 * @param $data
	 * @return array
	 */
	public function update($data) {
		if ($this->getDao()->create($data)) {
			return $this->returnMessage(true, $this->getDao()->save());
		} else {
			return $this->returnMessage(false, $this->getDao()->getError());
		}
	}

	/**
	 * 根据id删除一条数据
	 * @param $id
	 * @return mixed
	 */
	public function delete($id) {
		$res = $this->getDao()->where(compact('id'))->delete();
		return $res;
	}

	/**
	 * 根据指定的条件删除数据
	 * @param $condition
	 * @return mixed
	 */
	public function deleteBy(array $condition) {
		$res = $this->getDao()->where($condition)->delete();
		return $res;
	}

	/**
	 * 根据id查找一条数据
	 * @param $id
	 * @param $field array 指定查找的字段
	 * @return mixed
	 */
	public function find($id,$field = array('*')) {
		return $this->getDao()->where(compact('id'))->field($field)->find();
	}

	/**
	 * 根据指定的条件查找一条数据
	 * @param array $condition
	 * @return mixed
	 */
	public function findBy(array $condition){
		return $this->getDao()->where($condition)->find();
	}

	/**
	 * 根据id获取某个字段值
	 * @param $id
	 * @param $column
	 * @return mixed
	 */
	public function getColumn($id,$column){
		return $this->getDao()->where(['id'=>$id])->getField($column);
	}

	/**
	 * 返回结果
	 * @param        $status
	 * @param string $message
	 * @return array
	 */
	private function returnMessage($status, $message = '') {
		return ['status' => $status, 'message' => $message];
	}

	public function __call($method, $arguments){
		return call_user_func_array([ $this->getDao(),$method],$arguments);
	}

}