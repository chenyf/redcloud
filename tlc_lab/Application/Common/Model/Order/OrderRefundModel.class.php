<?php
/*
 * 数据层
 * @package
 * @author     wanglei@wyzc.com
 * @version    $Id$
 */
namespace  Common\Model\Order;

use Common\Model\Common\BaseModel;

class OrderRefundModel extends BaseModel
{
    protected $tableName = 'order_refund';

//	public function getConnection() {
//		return $this;
//	}

    public function getRefund($id)
    {
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        return $this->where(array('id'=>$id))->find() ? : null;
    }
    public function getRefundUser($orderId,$userId)
    {
        $map['orderId']=$orderId;
        $map['userId']=$userId;
        $map['status']='created';
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        return $this->where($map)->find();
    }
    public function findRefundCountByUserId($userId)
    {
//        $sql = "SELECT COUNT(id) FROM {$this->tableName} WHERE userId = ?";
        return $this->where(array('userId'=>$userId))->count('id');
    }

    public function findRefundsByUserId($userId, $start, $limit)
    {
        $this->filterStartLimit($start, $limit);
//        $sql = "SELECT * FROM {$this->tableName} WHERE userId = ? ORDER BY createdTime DESC LIMIT {$start}, {$limit}";
        return $this->where(array('userId'=>$userId))->order('createdTime DESC')->limit($start,$limit)->select() ? : array();
    }

    public function searchRefunds($conditions, $orderBy, $start, $limit)
    {
        $this->filterStartLimit($start, $limit);
//        $builder = $this->_createSearchQueryBuilder($conditions)
//            ->select('*')
//            ->orderBy($orderBy[0], $orderBy[1])
//            ->setFirstResult($start)
//            ->setMaxResults($limit);
//        return $builder->execute()->fetchAll() ? : array();
	    return $this->where($conditions)->order("{$orderBy[0]} {$orderBy[1]}")->limit($start,$limit)->select();
    }


    public function searchRefundCount($conditions)
    {
//        $builder = $this->_createSearchQueryBuilder($conditions)
//            ->select('COUNT(id)');
//        return $builder->execute()->fetchColumn(0);
	    return $this->where($conditions)->count('id');
    }

    public function addRefund($refund)
    {
        $affected = $this->add($refund);
        if ($affected <= 0) {
           E('Insert order refund error.');
        }
        return $this->getRefund($affected);
    }

    public function updateRefund($id, $refund)
    {
        $this->where(array('id'=>$id))->save($refund);
        return $this->getRefund($id);
    }

    private function _createSearchQueryBuilder($conditions)
    {
//        $builder = $this->createDynamicQueryBuilder($conditions)
//                    ->from($this->tableName, $this->tableName)
//                    ->andWhere('status = :status')
//                    ->andWhere('userId = :userId')
//                    ->andWhere('targetType = :targetType')
//                    ->andWhere('orderId = :orderId');
//                    // ->andWhere('courseId = :courseId');
//
//
//        if (isset($conditions['courseIds']) && count($conditions['courseIds'])>0 && isset($conditions['targetType']) ){
//            $courseIdsRange = '('.implode(', ',$conditions['courseIds']).')';
//            $builder = $builder->andWhere('targetType = :targetType')->andStaticWhere("targetId IN {$courseIdsRange}");
//        }
//
//        if (isset($conditions['targetIds'])) {
//            $targetIds = '('.implode(', ',$conditions['targetIds']).')';
//            $builder->andStaticWhere("targetId IN ($targetIds)");
//        }
//
//        return $builder;
    }
    public function findRefundsByIds(array $ids)
    {
        if(empty($ids)) {
            return array();
        }
        $marks = implode(',',$ids);
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
	    $where['id'] = array('in',$marks);
        return  $this->where($where)->select();
    }

}