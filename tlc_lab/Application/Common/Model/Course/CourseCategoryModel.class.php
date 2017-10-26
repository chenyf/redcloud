<?php
/*
 * 课程分类关联
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Model\Course;


use Common\Model\Common\BaseModel;

class CourseCategoryModel extends BaseModel {

//	protected $order = 'ctm desc';

	protected $tableName = 'category';

	public function selectCategory(){
		return $this->select() ?: array();
	}

	public function getCategory($id){
		$map['id'] = $id;
		return $this->where($map)->find() ?: null;
	}

	public function getCategoryByCondition($condition){
		return $this->where($condition)->find() ?: null;
	}

	public function updateCategory($id,$data){
		return $this->where(array('id'=>$id))->save($data);
	}

	public function addCategory($data){
		return $this->add($data);
	}

	public function deleteCategory($id){
		$map['id'] = $id;
		return $this->where($map)->delete();
	}

}