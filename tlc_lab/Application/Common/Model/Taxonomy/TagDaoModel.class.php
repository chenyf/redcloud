<?php
/*
 * 数据层
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace  Common\Model\Taxonomy;

use Common\Model\Common\BaseModel;

class TagDaoModel extends BaseModel
{
    protected $tableName = 'tag';

	public function getConnection() {
		return $this;
	}

    public function getTag($id)
    {
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        return $this->getConnection()->where(array('id'=>$id))->find();
    }

    public function addTag(array $tag)
    {
        $affected = $this->getConnection()->add($tag);
        if ($affected <= 0) {
            E('Insert tag error.');
        }
        return $this->getTag($affected);
    }

    public function updateTag($id, array $fields)
    {
        $this->getConnection()->where(array('id'=>$id))->save($fields);
        return $this->getTag($id);
    }

    public function deleteTag($id)
    {
        return $this->getConnection()->where(array('id'=>$id))->delete();
    }

    public function findTagsByIds(array $ids)
    {
        if(empty($ids)){ return array(); }
        $marks = implode(',',$ids);
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
	    $where['id'] = array('in',$marks);
        return $this->getConnection()->where($where)->select();
    }

    public function findTagsByNames(array $names)
    {
        if(empty($names)){ return array(); }
        $marks = implode(',',$names);
//        $sql ="SELECT * FROM {$this->tableName} WHERE name IN ({$marks});";
	    $where['name'] = array('in',$marks);
        return $this->getConnection()->where($where)->select();
    }

    public function findAllTags($start, $limit)
    {
        $this->filterStartLimit($start, $limit);
//        $sql = "SELECT * FROM {$this->tableName} ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
        return $this->getConnection()->order('createdTime DESC')->limit($start,$limit)->select();
    }

    public function getTagByName($name)
    {
//        $sql = "SELECT * FROM {$this->tableName} WHERE name = ? LIMIT 1";
        return $this->getConnection()->where(array('name'=>$name))->find();
    }

    public function getTagByLikeName($name)
    {
        $name = "%{$name}%";
//        $sql = "SELECT * FROM {$this->tableName} WHERE name LIKE ?";
	    $where['name'] = array('like',$name);
        return $this->getConnection()->where($where)->select();
    }

    public function findAllTagsCount()
    {
//        $sql = "SELECT COUNT(*) FROM {$this->tableName} ";
        return $this->getConnection()->count();
    }

}