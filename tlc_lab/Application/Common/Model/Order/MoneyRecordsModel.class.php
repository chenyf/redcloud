<?php
/*
 * 数据层
 * @package    
 * @author     wanglei@wyzc.com
 * @version    $Id$
 */
namespace  Common\Model\Order;

use Common\Model\Common\BaseModel;

//use Topxia\Service\Common\BaseDao;
//use Topxia\Service\Order\Dao\MoneyRecordsDao;
//use PDO;

class MoneyRecordsModel extends BaseModel
{
    protected $tableName = 'money_record';

    public function searchMoneyRecordsCount($conditions)
    {
	    return $this->where($conditions)->count('id');
//        $builder = $this->_createSearchQueryBuilder($conditions)
//            ->select('COUNT(id)');
//        return $builder->execute()->fetchColumn(0);
    }

    public function searchMoneyRecords($conditions, $orderBy, $start, $limit)
    {
    	$this->filterStartLimit($start, $limit);
	    return $this->where($conditions)->order($orderBy[0].' '.$orderBy[1])->limit($start,$limit)->select();
//        $builder = $this->_createSearchQueryBuilder($conditions)
//            ->select('*')
//            ->orderBy($orderBy[0], $orderBy[1])
//            ->setFirstResult($start)
//            ->setMaxResults($limit);
//        return $builder->execute()->fetchAll() ? : array();
    }

    private function _createSearchQueryBuilder($conditions)
    {
//        return $this->createDynamicQueryBuilder($conditions)
//            ->from($this->tableName, 'money_record')
//            ->andWhere('userId = :userId')
//            ->andWhere('type = :type')
//            ->andWhere('status = :status')
//            ->andWhere('transactionNo = :transactionNo');
    }

}