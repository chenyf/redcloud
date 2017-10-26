<?php
/*
 * 节点管理
 * @author     wanglei@redcloud.com
 * @created_at    16/3/30 上午11:29
 */
namespace Common\Model\AccessControl;

use Common\Model\Common\BaseModel;
use Common\Traits\ServiceTrait;

class NodeServiceModel extends BaseModel {

	use ServiceTrait;


	/**
	 * 选择节点列表 
	 * @param int $pid
	 * @param array $data
	 * @return array
	 */
	public function getNodeList($pid = 0, $data = array()) {
		if (empty($data)) {
			$data = $this->search();
		}
		$lists = array();
		foreach ($data as $k => $v) {
			if ($v['pid'] == $pid) {
				$lists[] = $v;
				$lists   = array_merge($lists, $this->getNodeList($v['id'], $data));
			}
		}
		return $lists;
	}

	/**
	 * 节点层级数组
	 * @param int $pid
	 * @param array $data
	 * @return array
	 */
	public function getNodeArr($pid = 0, $data = array()) {
		if (empty($data)) {
			$data = $this->search(['id','name','title','pid','level']);
		}
		$lists = array();
		foreach ($data as $k => $v) {
			if ($v['pid'] == $pid) {
				$v['children']   = $this->getNodeArr($v['id'], $data);
				$lists[$v['id']] = $v;
			}
		}
		return $lists;
	}

	/**
	 * 添加数据
	 * @param $data
	 * @return array
	 */
	public function create($data) {
		$level         = $this->getDao()->where(['id' => $data['pid']])->getField('level');
		$data['level'] = intval($level) + 1;
		if ($this->getDao()->create($data)) {
			return $this->returnMessage(true, $this->getDao()->store());
		} else {
			return $this->returnMessage(false, $this->getDao()->getError());
		}
	}


	private function getDao() {
		return $this->createService('AccessControl.NodeModel');
	}


}