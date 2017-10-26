<?php
/*
 * 数据层
 * @package
 * @author     wanglei@wyzc.com
 * @version    $Id$
 */
namespace  Common\Model\Question;

use Common\Model\Common\BaseModel;

class CategoryModel extends BaseModel
{
    protected $tableName = 'question_category';

	public function getConnection() {
		return $this;
	}

    public function getCategory($id)
    {
       // $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        return $this->getConnection()->where(array('id'=>$id))->find();
    }

    public function findCategoriesByTarget($target, $start, $limit)
    {
        $this->filterStartLimit($start, $limit);
//        $sql = "SELECT * FROM {$this->tableName} WHERE target = ? ORDER BY seq ASC LIMIT {$start}, {$limit}";
        return $this->getConnection()->where(array('target'=>$target))->order('seq ASC')->limit($start,$limit)->select();
    }

    public function getCategorysCountByTarget($target)
    {
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE target = ?";
        return $this->getConnection()->where(array('target'=>$target))->count();
    }

    public function findCategoriesByIds($ids)
    {
        if(empty($ids)){ return array(); }
        $marks = implode(',',$ids);
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
	    $where['id'] = array('in',$marks);
        return $this->getConnection()->where($where)->select();
    }

    public function addCategory($fields)
    {   
        $affected = $this->getConnection()->add($fields);
        if ($affected <= 0) {
            E('Insert question category error.');
        }
        return $this->getCategory($affected);
    }

    public function updateCategory($id, $fields)
    {
        $this->getConnection()->where(array('id'=>$id))->save($fields);
        return $this->getCategory($id);
    }

    public function deleteCategory($id)
    {
        return $this->getConnection()->where(array('id'=>$id))->delete();
    }

}