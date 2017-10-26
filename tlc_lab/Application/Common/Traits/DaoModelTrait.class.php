<?php
namespace Common\Traits;

use Common\Lib\Paginator;

Trait DaoModelTrait {


	protected $condition = array();

	/**
	 * 查询条件
	 * @param $condition
	 * @return \Think\Model
	 */
	public function condition($condition) {
		$this->condition = array_merge($this->condition, $condition);
		return $this;
	}


	/**
	 * 统计记录
	 * @param string $field
	 * @return mixed
	 */
	public function searchCount($field = 'id') {
		if ($this->condition) {
			$this->where($this->condition);
		}
		return $this->count($field);
	}


	/**
	 * 查询所有记录
	 * @param array  $field
	 * @param string $order
	 * @return mixed
	 */
	public function search($field = array('*'), $order = '') {
		if ($this->condition) {
			$this->where($this->condition);
		}
		$order = $order ? $order : $this->order;
		if ($order) {
			$this->order($order);
		}
		return $this->order($order)->field($field)->select();
	}

	/**
	 * 分页
	 * @param int    $page
	 * @param        $request
	 * @param string $order
	 * @return array
	 */
	public function paginate($page = 15, $request, $order = '') {

		$count = $this->searchCount();

		$paginator = new Paginator($request, $count, $page);

		if ($this->condition) {
			$this->where($this->condition);
		}

		$order = $order ? $order : $this->order;

		if ($order) {
			$this->order($order);
		}

		$list = $this->limit($paginator->getOffsetCount(), $paginator->getPerPageCount())->select();

		return [$list, $paginator];
	}

	/**
	 * 添加一条数据
	 * @param array $data
	 * @return mixed
	 */
	public function store(array $data = array()) {
		if (!empty($data)) {
			$this->data($data);
		}
		return $this->add();
	}

	/**
	 * 批量添加数据
	 * @param array $dataList
	 * @example
	 * -------------------------------------------------------------------
	 * $dataList[] = array('name'=>'thinkphp','email'=>'thinkphp@gamil.com');
	 * $dataList[] = array('name'=>'onethink','email'=>'onethink@gamil.com');
	 * ---------------------------------------------------------------------
	 * @return mixed
	 */
	public function storeAll(array $dataList) {
		return $this->addAll($dataList);
	}

	/**
	 * 更新一条记录
	 * @param array $data
	 * @param       $id
	 * @return bool
	 */
	public function update(array $data = array(), $id = 0) {
		if (!empty($data) && $id) {
			$this->where(array('id' => $id))->data($data);
		}
		$this->save();
		return $this->first($id);
	}


	/**
	 * 根据字段更新一条记录
	 * @param array $data
	 * @param       $field
	 * @param       $value
	 * @return bool
	 */
	public function updateBy(array $data, $field, $value) {
		return $this->where(array($field => $value))->data($data)->save();
	}

	/**
	 * 根据id删除一条记录
	 * @param array|mixed $id
	 * @return mixed
	 */
	public function destroy($id) {
		return $this->where(array('id' => $id))->delete();
	}

	/**
	 * 根据字段删除一条记录
	 * @param $field
	 * @param $value
	 * @return mixed
	 */
	public function destroyBy($field, $value) {
		return $this->where(array($field => $value))->delete();
	}

	/**
	 * 根据id查找一条记录
	 * @param array|mixed $id
	 * @param array       $field
	 * @return mixed
	 */
	public function first($id, $field = array('*')) {
		return $this->where(array('id' => $id))->field($field)->find();
	}

	/**
	 * 根据字段查找一条记录
	 * @param       $field
	 * @param       $value
	 * @param array $columns
	 * @return mixed
	 */
	public function firstBy($field, $value, $columns = array('*')) {
		return $this->where(array($field => $value))->field($columns)->find();
	}

}