<?php

/*
 * 数据层
 * @package    
 * @author     wanglei@wyzc.com
 * @version    $Id$
 */

namespace Common\Model\Order;

use Common\Model\Common\BaseModel;

//use Topxia\Service\Common\BaseDao;
//use Topxia\Service\Order\Dao\OrderDao;
//use PDO;

class OrderModel extends BaseModel {

    protected $tableName = 'orders';
    private $serializeFields = array(
        'data' => 'json',
    );

//	public function getConnection() {
//		return $this;
//	}

    public function getOrder($id) {
        $order = $this->where(array('id' => $id))->find() ? : null;
        return $order ? $this->createSerializer()->unserialize($order, $this->serializeFields) : null;
    }

    public function getOrderCondition($condition,$noCancel=0,$noDelete=0){
        if($noCancel) {
            $condition['isCancel'] = 0;
        }

        if($noDelete) {
            $condition['isCancel'] = 0;
        }

        return $this->where($condition)->find() ?: null;
    }

    public function selectOrderCondition($condition,$noCancel=0,$noDelete=0){
        if($noCancel) {
            $condition['isCancel'] = 0;
        }

        if($noDelete) {
            $condition['isCancel'] = 0;
        }

        return $this->where($condition)->select() ?: array();
    }

    public function searchOrderList($conditions, $orderBy, $start, $limit){
        return $this->where($conditions)->order("$orderBy[0] $orderBy[1]")->limit($start, $limit)->select() ?: array();
    }

//    public function searchOrderCount($conditions){
//        return $this->where($conditions)->count();
//    }

    public function searchOrdersList($parm){
        return $this->where($parm)->order('id desc')->select();
    }
    public function deleteOrderCourse($orderId){
        $data['targetId'] = 0;
        $data['title'] = 0;
        return $this->where("id = $orderId")->save($data);
    }

        public function getOrderBySn($sn) {
        //$sql = "SELECT * FROM {$this->tableName} WHERE sn = ? LIMIT 1";
        $order = $this->where(array('sn' => $sn))->find();
        return $order ? $this->createSerializer()->unserialize($order, $this->serializeFields) : null;
    }

    public function findOrdersByIds(array $ids) {
        if (empty($ids)) {
            return array();
        }
        $ids = implode(',', $ids);
        $orders = $this->where("id in({$ids})")->select();
        return $this->createSerializer()->unserializes($orders, $this->serializeFields);
    }
    public function getOrderInfo($orderId){
       $arr=  $this->where("id = $orderId")->find();
     return $arr;
    }

    public function addOrder($order) {
        $order = $this->createSerializer()->serialize($order, $this->serializeFields);
        $affected = $this->add($order);
        if ($affected <= 0) {
            E('Insert order error.');
        }
        return $this->getOrder($affected);
    }

    public function updateOrder($id, $fields) {
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $this->where(array('id' => $id))->save($fields);
        return $this->getOrder($id);
    }
    public function getCourseOrderEnd($courseId,$uid,$paytype){
        $map['targetId']=$courseId;
        $map['userId']=$uid;
        $map['status']='created';
        $map['payType']=$paytype;
        return $this->where($map)->order('id desc')->find();
    }
    public function searchOrders($conditions, $orderBy, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        $where = $this->_createSearchQueryBuilder($conditions);

        $info = $this
                ->where($where)
                ->order("{$orderBy[0]} {$orderBy[1]}")
                ->limit("{$start},{$limit}")
                ->select();

        return $info;

//        $orders = $this->getConnection->where($conditions)->order("{($orderBy[0]} {$orderBy[1]}")->limit($start,$limit)->select() ? : array();
//        return $this->createSerializer()->unserializes($orders, $this->serializeFields);
    }

    //获取订单提现列表
    public function getOrderList($parm) {
        return $this->where($parm)->select();
    }

    //获取订单流水
    public function getOrderSerial($webCode) {
        $map['webCode'] = $webCode;
        $map['status'] = 'paid';
        return $this->where($map)->field('sum(amount) as orderPrice,count(*) as orderCount')->find();
    }
    public function getOrderSerialUser($webCode){
        $map['webCode'] = $webCode;
        $map['status'] = 'paid';
        return $this->where($map)->field('id')->group('userId')->select();
    }

    //更新订单提现状态
    public function doOrderList($orderId, $id) {
        $data['cashId'] = $id;
        $data['cashStatus'] = 1;
        $map['id'] = array('in', $orderId);
        return $this->where($map)->save($data);
    }
     //更新订单提现状态
    public function doOrderListStatus($id, $status) {
        $map['cashId'] = $id;
        $data['cashStatus'] = $status;
        return $this->where($map)->save($data);
    }
    public function searchBill($conditions, $orderBy, $start, $limit) {
        if (!isset($conditions['startTime']))
            $conditions['startTime'] = 0;
//        $sql = "SELECT * FROM {$this->tableName} WHERE `createdTime`>={$conditions['startTime']} and `createdTime`<{$conditions['endTime']} and `userId` = {$conditions['userId']} and (not(`payment` in ('none','coin'))) and `status` = 'paid' ORDER BY {$orderBy[0]} {$orderBy[1]}  LIMIT {$start}, {$limit}";
        $where['createdTime'] = array('EGT', $conditions['startTime']);
        $where['createdTime'] = array('ELT', $conditions['endTime']);
        $where['userId'] = $conditions['userId'];
        $where['payment'] = array('NOT IN', 'none,coin');
        $where['status'] = 'paid';
        return $this->where($where)->order("{$orderBy[0]} {$orderBy[1]}")->limit($start, $limit)->select();
    }

    public function countUserBillNum($conditions) {
        if (!isset($conditions['startTime']))
            $conditions['startTime'] = 0;
//        $sql = "SELECT count(*) FROM {$this->tableName} WHERE `createdTime`>={$conditions['startTime']} and `createdTime`<{$conditions['endTime']} and `userId` = {$conditions['userId']} and (not(`payment` in ('none','coin'))) and `status` = 'paid' ";
        $where['createdTime'] = array('EGT', $conditions['startTime']);
        $where['createdTime'] = array('ELT', $conditions['endTime']);
        $where['userId'] = $conditions['userId'];
        $where['payment'] = array('NOT IN', 'none,coin');
        $where['status'] = 'paid';
        return $this->where($where)->count();
    }

    public function searchOrderCount($conditions) {
        $where = $this->_createSearchQueryBuilder($conditions);
        $builder = $this;
        if (isset($conditions['siteSelect']))
            $builder = processSqlObj(array('sqlObj' => $builder, 'siteSelect' => $conditions['siteSelect']));
        $count = $builder->where($where)->count('id');
        return $count;
//	    return $this->where($conditions)->count('id');
    }

    public function sumOrderAmounts($startTime, $endTime, array $courseId) {
        if (empty($courseId)) {
            return array();
        }

        $marks = implode(',', $courseId);

        $sql = "SELECT  targetId,sum(amount) as  amount from {$this->tableName} WHERE  createdTime > {$startTime} AND createdTime < {$endTime} AND targetId IN ({$marks}) AND targetType = 'course' AND status = 'paid' group by targetId";
        return $this->query($sql);
    }

    private function _createSearchQueryBuilder($conditions) {
        $where = "1 AND 1";
        if (isset($conditions["title"])) {
            $conditions["title"] = '%' . $conditions["title"] . "%";
            $where.=" AND title like '%" . $conditions['title'] . "%' ";
        }
        if (isset($conditions["sn"])) {
            $where.=" AND sn ='" . $conditions['sn'] . "'";
        }
        if (isset($conditions["targetType"])) {
            $where.=" AND targetType ='" . $conditions['targetType'] . "'";
        }
        if (isset($conditions["targetId"])) {
            $where.=" AND targetId ='" . $conditions['targetId'] . "'";
        }
        if (isset($conditions["userId"])) {
            $where.=" AND userId ='" . $conditions['userId'] . "'";
        }
        if (isset($conditions["amount"])) {
            $where.=" AND amount >'" . $conditions['amount'] . "'";
        }
        if (isset($conditions["status"])) {
            $where.=" AND status ='" . $conditions['status'] . "'";
        }
        if (isset($conditions["statusPaid"])) {
            $where.=" AND status <> '" . $conditions['statusPaid'] . "'";
        }
        if (isset($conditions["statusCreated"])) {
            $where.=" AND status <> '" . $conditions['statusCreated'] . "'";
        }
        if (isset($conditions["payment"])) {
            $where.=" AND payment = '" . $conditions['payment'] . "'";
        }
        if (isset($conditions["createdTimeGreaterThan"])) {
            $where.=" AND createdTime>= '" . $conditions['createdTimeGreaterThan'] . "'";
        }
        if (isset($conditions["paidStartTime"])) {
            $where.=" AND paidTime>='" . $conditions['paidStartTime'] . "'";
        }
        if (isset($conditions["paidEndTime"])) {
            $where.=" AND paidTime<'" . $conditions['paidEndTime'] . "'";
        }
        if (isset($conditions["startTime"])) {
            $where.=" AND createdTime>='" . $conditions['startTime'] . "'";
        }
        if (isset($conditions["endTime"])) {
            $where.=" AND createdTime<'" . $conditions['endTime'] . "'";
        }
        if (isset($conditions["webCode"])) {
            $where.=" AND webCode = '" . $conditions['webCode'] . "'";
        }
       if (isset($conditions["sourceWebCode"])) {
            $where.=" AND sourceWebCode = '" . $conditions['sourceWebCode'] . "'";
        }
        return $where;
//        return $this->createDynamicQueryBuilder($conditions)
//            ->from($this->tableName, 'course_order')
//            ->andWhere('sn = :sn')
//            ->andWhere('targetType = :targetType')
//            ->andWhere('targetId = :targetId')
//            ->andWhere('userId = :userId')
//            ->andWhere('amount > :amount')
//            ->andWhere('status = :status')
//            ->andWhere('status <> :statusPaid')
//            ->andWhere('status <> :statusCreated')
//            ->andWhere('payment = :payment')
//            ->andWhere('createdTime >= :createdTimeGreaterThan')
//            ->andWhere('paidTime >= :paidStartTime')
//            ->andWhere('paidTime < :paidEndTime')
//            ->andWhere('createdTime >= :startTime')
//            ->andWhere('createdTime < :endTime')
//            ->andWhere('title LIKE :title');
    }

    public function sumOrderPriceByTargetAndStatuses($targetType, $targetId, array $statuses) {
        if (empty($statuses)) {
            return array();
        }

        $marks = implode(',', $statuses);
        // $sql = "SELECT sum(amount) FROM {$this->tableName} WHERE targetType =? AND targetId = ? AND status in ({$marks})";
        $where['targetType'] = $targetType;
        $where['targetId'] = $targetId;
        $where['status'] = array('in', $marks);
        return $this->where($where)->sum('amount');
    }

    public function sumCouponDiscountByOrderIds($orderIds) {
        if (empty($orderIds)) {
            return array();
        }
        $marks = implode(',', $orderIds);
//        $sql = "SELECT sum(couponDiscount) FROM {$this->tableName} WHERE id in ({$marks})";
        $where['id'] = array('in', $marks);
        return $this->where($where)->sum("couponDiscount");
    }

    public function analysisCourseOrderDataByTimeAndStatus($startTime, $endTime, $status) {
        $sql = "SELECT count(id) as count, from_unixtime(createdTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE`createdTime`>={$startTime} and `createdTime`<={$endTime} and `status`='{$status}' and targetType='course' group by date_format(from_unixtime(`createdTime`),'%Y-%m-%d') order by date ASC ";

        return $this->query($sql);
    }

    public function analysisPaidCourseOrderDataByTime($startTime, $endTime) {
        $sql = "SELECT count(id) as count, from_unixtime(createdTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE`createdTime`>={$startTime} and `createdTime`<={$endTime} and `status`='paid' and targetType='course'  and `amount`>0 group by date_format(from_unixtime(`createdTime`),'%Y-%m-%d') order by date ASC ";

        return $this->query($sql);
    }

    public function analysisAmount($conditions) {
        $where = $this->_createSearchQueryBuilder($conditions);
//            ->select('sum(amount)');
//        return $builder->execute()->fetchColumn(0);
        $builder = $this;
        if (isset($conditions['siteSelect']))
            $builder = processSqlObj(array('sqlObj' => $builder, 'siteSelect' => $conditions['siteSelect']));
        $info = $builder->where($where)->sum('amount');
        return $info;
    }

    public function analysisAmountDataByTime($startTime, $endTime) {
        $sql = "SELECT sum(amount) as count, from_unixtime(paidTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE`paidTime`>={$startTime} and `paidTime`<={$endTime} and `status`='paid'  group by from_unixtime(`paidTime`,'%Y-%m-%d') order by date ASC ";

        return $this->query($sql);
    }

    public function analysisCourseAmountDataByTime($startTime, $endTime) {
        $sql = "SELECT sum(amount) as count, from_unixtime(paidTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE`paidTime`>={$startTime} and `paidTime`<={$endTime} and `status`='paid' and targetType='course'   group by from_unixtime(`paidTime`,'%Y-%m-%d') order by date ASC ";

        return $this->query($sql);
    }

    public function analysisExitCourseOrderDataByTime($startTime, $endTime) {
        $sql = "SELECT count(id) as count, from_unixtime(createdTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE`createdTime`>={$startTime} and `createdTime`<={$endTime} and `status`<>'paid' and `status`<>'created' and targetType='course' group by from_unixtime(`createdTime`,'%Y-%m-%d') order by date ASC ";

        return $this->query($sql);
    }

    public function getConnection() {
        return $this;
    }
    public function OrderInfo($orderList){
        return $this->where($orderList)->select();
    }
    /**
     * 销售订单管理  朱旭
     */
     public function searchOrderCountInfo($orderList){
            $data =  $this->where($orderList)->count();
            return $data;
    }
    public function searchOrdersInfo($conditions, $start, $limit){
            $where = array();
                if($conditions){
                    $where[] = $conditions;
                }
                $where['status'] = paid;
            $data = $this->where($where)->select();
            $orderInfo = $this->where($conditions)->select();
            $result = $this->where($conditions)->limit($start, $limit)->select();
//            echo $this->getlastsql();
            return array($orderInfo,$result,$data);
    }

    public function getSllUserInfo($id){
        $data = $this->table("user u")
                    ->join("orders o ON u.id = o.userId")
                    ->field('o.sn,u.nickname,u.verifiedMobile,u.email')
                    ->where("userId = {$id}")->find();
        return $data;
    }
    public function allotListInfo($id){
        $data = $this->table("orders o")
                    ->join("user u ON u.id = o.userId")
                    ->field('o.sn,u.nickname,o.title,o.createdTime,o.amount,o.id')
                    ->where("o.id = {$id}")->find();
        return $data;
    }
    //修改销售人员
    public function UpdateAllot($orderId,$data){
        $data = $this->where("id={$orderId}")->save($data);
        return $data;
    }
  
}

