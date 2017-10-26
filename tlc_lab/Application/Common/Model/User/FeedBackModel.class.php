<?php
/**
 * 用户意见反馈
 * @author fubaosheng 2015-05-08
 */
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class FeedBackModel extends BaseModel{
    protected $tableName = 'feedback';
    
    public function getFeedBack($id){
        return $this->where("id = {$id}")->find() ? : array();
    }
    
    public function addFeedBack($feedBack){
        $r = $this->add($feedBack);
        if(!$r) E("Insert FeedBack error.");
        return  $this->getFeedBack($r);
    }
    
    public function searchFeedBackCount($conditions){
        $builder = $this->createFeedBackQueryBuilder($conditions)->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }
    
    public function searchFeedBacks($conditions, $orderBy, $start, $limit){
        $builder = $this->createFeedBackQueryBuilder($conditions)
            ->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }
    
    private function createFeedBackQueryBuilder($conditions){
        $conditions = array_filter($conditions,function($v){
            if($v === 0){
                return true;
            }
            if(empty($v)){
                return false;
            }
            return true;
        });

        return  $this->createDynamicQueryBuilder($conditions)
                ->from($this->tableName, 'feedback')
                ->andWhere('type = :type')
                ->andWhere('status = :status')
                ->andWhere("`from` = :from");
    }
}
?>
