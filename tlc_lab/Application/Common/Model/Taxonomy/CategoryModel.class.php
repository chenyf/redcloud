<?php
/*
 * 数据层
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace  Common\Model\Taxonomy;

use Common\Model\Common\BaseModel;

class CategoryModel extends BaseModel
{
	protected $tableName = 'category';

    public function getNameByIds($ids){
        if(empty($ids)) return array();
        return  $this->where("id in({$ids}) ")->field("name,isDelete,id")->select() ? : array();
    }

	public function getNameById($id){
		return  $this->where(array('id'=>$id))->getField('name');
	}

    public function getCourseCodeById($id){
        return  $this->where(array('id'=>$id))->getField('courseCode');
    }

        public function getCategoryByLikeName($name){
            $name = "%{$name}%";
            $where['name'] = array('like',$name);
            $where['parentId'] = 0;
            return $this->where($where)->order("weight ASC")->select() ? : array();
        }
        
        public function getCateByName($wherArr){
            return $this->where($wherArr)->getField("id") ? : 0;
        }
        
        public function getParentNameByParentId($parentId){
            return  $this->field("id,name,parentId,isDelete")->where("id = {$parentId}")->find() ? : array();
        }

        public function getCategoryByPid($id){
            return  $this->field("id,isLeafNode,name")->where("parentId = {$id}")->find() ? : array();
        }

        public function getChildrenCateByPid($id){
            return  $this->field("id,isLeafNode,name")->where("parentId = {$id}")->select() ? : array();
        }

        public function getBrotherCateByPid($id,$include=false){
            $parentId = $this->getPidBuyId($id);
            $where = "id != {$id} and parentId = {$parentId}";
            if($include){
                $where = "parentId = {$parentId}";
            }
            return  $this->field("id,isLeafNode,name")->where($where)->order('id ASC')->select() ? : array();
        }

        public function getPidBuyId($id){
            return $this->where("id = {$id}")->getField('parentId') ? : 0;
        }
        
	public function addCategory($category)
        {
            $affected = $this->add($category);
            if ($affected <= 0) {
                E('Insert category error.');
            }
            return $this->getCategory($affected);
	}
        
    public function addCategoryByExcel($category){
        $affected = $this->add($category);
        if ($affected <= 0) return 0;
        return $affected;
    }
        
	public function deleteCategory($id)
    {
        return $this->where(array('id'=>$id))->delete();
	}

	public function getCategory($id)
    {
//		$sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        return $this->where(array('id'=>$id))->find() ? : array();
	}

	public function findCategoryByCode($code)
    {
//        $sql = "SELECT * FROM {$this->tableName} WHERE code = ? LIMIT 1";
        return $this->where(array('code'=>$code))->find() ? : array();
	}

	public function updateCategory($id, $category)
    {
        $this->where(array('id'=>$id))->save($category);
        return $this->getCategory($id);
	}

	public function findCategories()
    {
        $sql = "SELECT * FROM {$this->tableName} ORDER BY weight ASC";
        return $this->query($sql) ? : array();
    }

	public function findCategoriesByParentId($parentId, $orderBy = null, $start, $limit)
    {
        $this->filterStartLimit($start, $limit);
        $sql = "SELECT * FROM {$this->tableName} WHERE parentId = {$parentId} ORDER BY {$orderBy} DESC LIMIT {$start}, {$limit}";
        return $this->query($sql) ? : array();
	}

    public function findAllCategoriesByParentId($parentId)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE parentId = {$parentId} ORDER BY weight ASC";
        return $this->query($sql) ? : array();
    }

    public function getCategoriesByParentId($parentId)
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE parentId = {$parentId} ORDER BY weight ASC";
        return $this->query($sql) ? : array();
    }

	public function findCategoryParentsById($id){
		$info = $this->getConnection($id)->field('id,level,parentId')->find($id);
		$res  = array();
		if($info['level'] == 1 ){
			return  $res;
		}
		$res = array('L'.$info['level']=>$info['id']);
		$res = array_merge($res,$this->findCategoryParentsById($info['parentId']));
 		return $res;
	}

	public function findCategoriesCountByParentId($parentId)
    {
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE  parentId = ?";
        return $this->where("parentId=".$parentId)->count();
	}

	public function findCategoriesByIds(array $ids)
    {
        if(empty($ids)){ return array(); }
        $marks = implode(',',$ids);
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
		$where['id'] = array('in',$marks);
        $info =  $this->where($where)->select() ? : array();
        return $info;
    }

    public function findAllCategories()
    {
//        $sql = "SELECT * FROM {$this->tableName}";
        return $this->select() ? : array();
    }
    
    public function getNoDeleteCateById($id){
        if(empty($id)){ return array(); }
        $where['id'] = array('in',$id);
        $where['isDelete'] = 0;
        return $this->where($where)->select() ? : array();
    }
    

}