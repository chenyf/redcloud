<?php

/*
 * 业务层
 * @package
 * @author     wanglei@wyzc.com
 * @version    $Id$
 */

namespace Common\Model\Order;

use Common\Model\Common\BaseModel;

//use Topxia\Service\Common\BaseService;
//use Topxia\Service\Order\OrderService;
//use Topxia\Service\Common\ServiceEvent;todo
use Common\Lib\ArrayToolkit;
use Common\Lib\NumberToolkit;

class OrderServiceModel extends BaseModel
{

    protected $currentDb = CENTER;

    public function getOrder($id)
    {
        return $this->getOrderDao()->getOrder($id);
    }

    public function getOrderCondition($condition,$noCancel=0,$noDelete=0){
        return $this->getOrderDao()->getOrderCondition($condition,$noCancel,$noDelete);
    }

    public function selectOrderCondition($condition,$noCancel=0,$noDelete=0){
        return $this->getOrderDao()->selectOrderCondition($condition,$noCancel,$noDelete);
    }

    public function searchOrderList($conditions, $orderBy, $start, $limit) {
        $list = $this->getOrderDao()->searchOrderList($conditions, $orderBy, $start, $limit);
        return $this->handleOrderList($list);
    }

//    public function searchOrderCount($conditions) {
//        return $this->getOrderDao()->searchOrderCount($conditions);
//    }

    public function handleOrderList($list){

    }

    public function searchOrdersList($parm)
    {
        return $this->getOrderDao()->searchOrdersList($parm);
    }

    public function getOrderBySn($sn, $lock = false)
    {
        return $this->getOrderDao()->getOrderBySn($sn, $lock);
    }

    public function findOrdersByIds(array $ids)
    {
        $orders = $this->getOrderDao()->findOrdersByIds($ids);
        return ArrayToolkit::index($orders, 'id');
    }

    public function createOrder($order)
    {
        if (!ArrayToolkit::requireds($order, array('userId', 'title', 'amount', 'targetType', 'targetId', 'payment', 'payType'))) {
            E('创建订单失败：缺少参数。');
        }

        $order = ArrayToolkit::parts($order, array('userId', 'title', 'amount', 'targetType', 'targetId', 'payment', 'note', 'snPrefix', 'data', 'couponCode', 'coinAmount', 'coinRate', 'priceType', 'totalPrice', 'coupon', 'couponDiscount', 'payType', 'sourceWebCode'));

        $orderUser = $this->getUserService()->getUser($order['userId']);
        if (empty($orderUser)) {
            E("订单用户(#{$order['userId']})不存在，不能创建订单。");
        }

        if (!in_array($order['payment'], array('none', 'alipay', 'alipaydouble', 'tenpay', 'coin', 'minebuy'))) {
            E('创建订单失败：payment取值不正确。');
        }

        $order['sn'] = $this->generateOrderSn($order);
        unset($order['snPrefix']);

        if (!empty($order['couponCode'])) {
            $couponInfo = $this->getCouponService()->checkCouponUseable($order['couponCode'], $order['targetType'], $order['targetId'], $order['amount']);
            if ($couponInfo['useable'] != 'yes') {
                E("优惠码不可用");
            }
        }

        unset($order['couponCode']);

        $order['amount'] = number_format($order['amount'], 2, '.', '');
        if (intval($order['amount'] * 100) == 0 && $order['payment'] != 'minebuy') {
            $order['payment'] = 'none';
        }

        $order['status'] = 'created';
        $order['createdTime'] = time();
        $order = $this->getOrderDao()->addOrder($order);

        $this->_createLog($order['id'], 'created', '创建订单');
        return $order;
    }
    public function createPlanOrder($order)
    {
        if (!ArrayToolkit::requireds($order, array('userId', 'title', 'amount', 'targetType', 'targetId', 'payment', 'payType'))) {
            E('创建订单失败：缺少参数。');
        }

        $orderUser = $this->getUserService()->getUser($order['userId']);
        if (empty($orderUser)) {
            E("订单用户(#{$order['userId']})不存在，不能创建订单。");
        }

        if (!in_array($order['payment'], array('none', 'alipay', 'alipaydouble', 'tenpay', 'coin', 'minebuy'))) {
            E('创建订单失败：payment取值不正确。');
        }

        $order['sn'] = $this->generateOrderSn($order);
        unset($order['snPrefix']);

        if (!empty($order['couponCode'])) {
            $couponInfo = $this->getCouponService()->checkCouponUseable($order['couponCode'], $order['targetType'], $order['targetId'], $order['amount']);
            if ($couponInfo['useable'] != 'yes') {
                E("优惠码不可用");
            }
        }

        unset($order['couponCode']);

        $order['amount'] = number_format($order['amount'], 2, '.', '');
        if (intval($order['amount'] * 100) == 0 && $order['payment'] != 'minebuy') {
            $order['payment'] = 'none';
        }
        $order = $this->getOrderDao()->addOrder($order);
        $this->_createLog($order['id'], 'created', '创建订单');
        return $order;
    }

    public function payOrder($payData)
    {
        $success = false;
        $order = $this->getOrderDao()->getOrderBySn($payData['sn']);
        $payData['amount'] = $order['amount'];
        if (empty($order)) {
            E("订单({$payData['sn']})已被删除，支付失败。");
        }

        if ($payData['status'] == 'success') {
            // 避免浮点数比较大小可能带来的问题，转成整数再比较。
//            if (intval($payData['amount'] * 100) !== intval($order['amount'] * 100)) {
//                $message = sprintf('订单(%s)的金额(%s)与实际支付的金额(%s)不一致，支付失败。', array($order['sn'], $order['price'], $payData['amount']));
//                $this->_createLog($order['id'], 'pay_error', $message, $payData);
//                E($message)
//            }

            if ($this->canOrderPay($order)) {
                $parm = array(
                    'status' => 'paid',
                    'paidTime' => $payData['paidTime'],
                    'tradeNo' => $payData['tradeNo'],
                );
                if ($payData['payment']) {
                    $parm['payment'] = $payData['payment'];
                }
                $this->getOrderDao()->updateOrder($order['id'], $parm);
                $this->_createLog($order['id'], 'pay_success', '付款成功', $payData);
                $success = true;
            } else {
                $this->_createLog($order['id'], 'pay_ignore', '订单已处理', $payData);
            }
        } else {
            $this->_createLog($order['id'], 'pay_unknown', '', $payData);
        }
        if (intval($payData['amount'] * 100) !== 0) {
            if ($payData['webCode'] != "center" && $payData['webCode']) {
                $parm = array('uid' => $order['userId'], 'orderId' => $order['id'], "courseId" => $order['targetId'], "webCode" => $payData['webCode']);
                hook("generalize_buy", $parm);
            }
            $this->getCourseOrderService()->doSuccessPayOrder($order['id'], $payData['webCode']);
        }
        $order = $this->getOrder($order['id']);

        //note by qzw
        //TODO
//        if ($success) {
//            $this->getDispatcher()->dispatch('order.service.paid', new ServiceEvent($order));
//        }

        return array($success, $order);
    }

    public function findOrderLogs($orderId)
    {
        $order = $this->getOrder($orderId);
        if (empty($order)) {
            E("订单不存在，获取订单日志失败！");
        }
        return $this->getOrderLogDao()->findLogsByOrderId($orderId);
    }

    public function canOrderPay($order)
    {
        if (empty($order['status'])) {
            throw new \InvalidArgumentException();
        }
        return in_array($order['status'], array('created'));
    }

    public function analysisCourseOrderDataByTimeAndStatus($startTime, $endTime, $status)
    {
        return $this->getOrderDao()->analysisCourseOrderDataByTimeAndStatus($startTime, $endTime, $status);
    }

    public function analysisPaidCourseOrderDataByTime($startTime, $endTime)
    {
        return $this->getOrderDao()->analysisPaidCourseOrderDataByTime($startTime, $endTime);
    }

    public function analysisExitCourseDataByTimeAndStatus($startTime, $endTime)
    {
        return $this->getOrderDao()->analysisExitCourseOrderDataByTime($startTime, $endTime);
    }

    public function analysisAmount($conditions)
    {
        $conditions = $this->_prepareSearchConditions($conditions);
        return $this->getOrderDao()->analysisAmount($conditions);
    }

    public function analysisAmountDataByTime($startTime, $endTime)
    {
        return $this->getOrderDao()->analysisAmountDataByTime($startTime, $endTime);
    }

    public function analysisCourseAmountDataByTime($startTime, $endTime)
    {
        return $this->getOrderDao()->analysisCourseAmountDataByTime($startTime, $endTime);
    }

    public function generateOrderSn($order)
    {
        $prefix = empty($order['snPrefix']) ? 'E' : (string)$order['snPrefix'];
        $sn = $prefix . date('YmdHis', time()) . mt_rand(10000, 99999);
        return $sn;
    }

    private function _createLog($orderId, $type, $message = '', array $data = array())
    {
        $user = $this->getCurrentUser();

        $log = array(
            'orderId' => $orderId,
            'type' => $type,
            'message' => $message,
            'data' => json_encode($data),
            'userId' => $user->id,
            'ip' => $user->currentIp,
            'createdTime' => time()
        );

        return $this->getOrderLogDao()->addLog($log);
    }

    public function cancelOrder($id, $message = '')
    {
        $order = $this->getOrder($id);
        if (empty($order)) {
            throw $this->$this->createServiceException('订单不存在，取消订单失败！');
        }

        if (!in_array($order['status'], array('created',))) {
            throw $this->$this->createServiceException('当前订单状态不能取消订单！');
        }

        $order = $this->getOrderDao()->updateOrder($order['id'], array('status' => 'cancelled','remark' => $message));

        $this->_createLog($order['id'], 'cancelled', $message);

        return $order;
    }

    public function sumOrderPriceByTarget($targetType, $targetId)
    {
        return $this->getOrderDao()->sumOrderPriceByTargetAndStatuses($targetType, $targetId, array('paid'));
    }

    public function sumCouponDiscountByOrderIds($orderIds)
    {
        return $this->getOrderDao()->sumCouponDiscountByOrderIds($orderIds);
    }

    public function findUserRefundCount($userId)
    {
        return $this->getOrderRefundDao()->findRefundCountByUserId($userId);
    }

    public function findRefundsByIds(array $ids)
    {
        return $this->getOrderRefundDao()->findRefundsByIds($ids);
    }

    public function findUserRefunds($userId, $start, $limit)
    {
        return $this->getOrderRefundDao()->findRefundsByUserId($userId, $start, $limit);
    }

    public function searchRefunds($conditions, $sort = 'latest', $start, $limit)
    {
        $conditions = array_filter($conditions);
        if (empty($conditions)) {
            $conditions['isCenter'] = 0;
        }
        $orderBy = array('createdTime', 'DESC');
        return $this->getOrderRefundDao()->searchRefunds($conditions, $orderBy, $start, $limit);
    }

    public function searchRefundCount($conditions)
    {
        $conditions = array_filter($conditions);
        if (!$conditions['isCenter']) {
            $conditions['isCenter'] = 0;
        }
        return $this->getOrderRefundDao()->searchRefundCount($conditions);
    }

    //public function applyRefundOrder($id, $expectedAmount = null, $reason = array()) {
    public function applyRefundOrder($id, $data)
    {
        $order = $this->getOrder($id);
        $refundInfo = $this->getOrderRefundDao()->getRefundUser($id, $order['userId']);

        if ($refundInfo) {
            return $refundInfo;
        }
        if (empty($order)) {
            return false;
        }
        if ($order['status'] != 'paid') {
            return false;
        }
        // 订单金额为０时，不能退款
        if (intval($order['amount'] * 100) == 0) {
            return false;
        }
        // 超出退款期限，不能退款
        if ((time() - $order['paidTime']) > (86400 * 7)) {
            $expectedAmount = 0;
        }
        $status = 'created';
        if ($order['payment'] == 'none') {
            $order['payment'] = 'coin';
        }
        $refundData = array(
            'orderId' => $order['id'],
            'userId' => $order['userId'],
            'targetType' => $order['targetType'],
            'targetId' => $order['targetId'],
            'status' => $status,
            'expectedAmount' => 0,
            'actualAmount' => $order['amount'],
            'reasonType' => empty($reason['type']) ? 'other' : $reason['type'],
            'reasonNote' => empty($reason['note']) ? '' : $reason['note'],
            'updatedTime' => time(),
            'createdTime' => time(),
            'refundType' => $order['payment'],
            'isIos' => $order['payType']
        );
        if ($data['coin'] == '1') {
            $refundData['refundStatus'] = 1;
            $refundData['userName'] = $data['name'];
            $refundData['phone'] = $data['phone'];
            $refundData['blank'] = $data['blank'];
            $refundData['blankSn'] = $data['blankSn'];
        }
        $refund = $this->getOrderRefundDao()->addRefund($refundData);
        $this->getOrderDao()->updateOrder($order['id'], array(
            'status' => 'refunding',
            'refundId' => $refund['id'],
        ));
        $this->_createLog($order['id'], 'refund_apply', '订单申请退款');
        return $refund;
    }

    public function auditRefundOrder($id, $pass)
    {
        $order = $this->getOrder($id);
        if (empty($order)) {
            E("订单(#{$id})不存在，退款确认失败");
        }

        $user = $this->getCurrentUser();
        if (!$user->isAdmin()) {
            E("订单(#{$id})，你无权进行退款确认操作");
        }

        if ($order['status'] != 'refunding') {
            E("当前订单(#{$order['id']})状态下，不能进行确认退款操作");
        }

        $refund = $this->getOrderRefundDao()->getRefund($order['refundId']);
        if (empty($refund)) {
            E("当前订单(#{$order['id']})退款记录不存在，不能进行确认退款操作");
        }

        if ($refund['status'] != 'created') {
            E("当前订单(#{$order['id']})退款记录状态下，不能进行确认退款操作款");
        }


        if ($pass == true) {
            $this->getOrderRefundDao()->updateRefund($refund['id'], array(
                'status' => 'success',
                'actualAmount' => $order['amount'],
                'updatedTime' => time(),
            ));

            $this->getOrderDao()->updateOrder($order['id'], array(
                'status' => 'refunded',
            ));

            $this->_createLog($order['id'], 'refund_success', "退款申请(ID:{$refund['id']})已审核通过：{$note}");
        } else {
            $this->getOrderRefundDao()->updateRefund($refund['id'], array(
                'status' => 'failed',
                'updatedTime' => time(),
            ));

            $this->getOrderDao()->updateOrder($order['id'], array(
                'status' => 'paid',
            ));

            $this->_createLog($order['id'], 'refund_failed', "退款申请(ID:{$refund['id']})已审核未通过：{$note}");
        }

        $this->getLogService()->info('course_order', 'andit_refund', "审核退款申请#{$refund['id']}");

        return $pass;
    }

    public function cancelRefundOrder($id)
    {
        $order = $this->getOrder($id);
        if (empty($order)) {
            E("订单(#{$id})不存在，取消退款失败");
        }

        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            E("用户未登录，订单(#{$id})取消退款失败");
        }

        if ($order['userId'] != $user['id'] and !$user->isAdmin()) {
            E("订单(#{$id})，你无权限取消退款");
        }

        if ($order['status'] != 'refunding') {
            E("当前订单(#{$order['id']})状态下，不能取消退款");
        }

        $refund = $this->getOrderRefundDao()->getRefund($order['refundId']);
        if (empty($refund)) {
            E("当前订单(#{$order['id']})退款记录不存在，不能取消退款");
        }

        if ($refund['status'] != 'created') {
            E("当前订单(#{$order['id']})退款记录状态下，不能取消退款");
        }

        $this->getOrderRefundDao()->updateRefund($refund['id'], array(
            'status' => 'cancelled',
            'updatedTime' => time(),
        ));

        $this->getOrderDao()->updateOrder($order['id'], array(
            'status' => 'paid',
        ));

        $this->_createLog($order['id'], 'refund_cancel', "取消退款申请(ID:{$refund['id']})");
    }

    public function searchOrders($conditions, $sort = 'latest', $start, $limit)
    {
        $orderBy = array();
        if ($sort == 'latest') {
            $orderBy = array('createdTime', 'DESC');
        } elseif ($sort == 'early') {
            $orderBy = array('createdTime', 'ASC');
        } else {
            $orderBy = array('createdTime', 'DESC');
        }

        $conditions = $this->_prepareSearchConditions($conditions);
        $orders = $this->getOrderDao()->searchOrders($conditions, $orderBy, $start, $limit);

        return ArrayToolkit::index($orders, 'id');
    }

    //获取退款订单
    public function getRefund($refundId)
    {
        return $this->getOrderRefundDao()->getRefund($refundId);
    }

    //获取提现订单列表
    public function getOrderList($parm)
    {
        return $this->getOrderDao()->getOrderList($parm);
    }

    //获取订单流水
    public function getOrderSerial($webCode)
    {
        return $this->getOrderDao()->getOrderSerial($webCode);
    }

    public function getOrderSerialUser($webCode)
    {
        $list = $this->getOrderDao()->getOrderSerialUser($webCode);
        return count($list);
    }

    //更新订单
    public function doOrderList($orderId, $id)
    {
        return $this->getOrderDao()->doOrderList($orderId, $id);
    }

    public function doOrderListStatus($id, $status)
    {
        return $this->getOrderDao()->doOrderListStatus($id, $status);
    }

    //获取订单
    public function OrderInfo($orderList)
    {
        return $this->getOrderDao()->OrderInfo($orderList);
    }

    public function searchBill($conditions, $sort = 'latest', $start, $limit)
    {
        $orderBy = array();
        if ($sort == 'latest') {
            $orderBy = array('createdTime', 'DESC');
        } elseif ($sort == 'early') {
            $orderBy = array('createdTime', 'ASC');
        } else {
            $orderBy = array('createdTime', 'DESC');
        }

        $conditions = $this->_prepareSearchConditions($conditions);
        $orders = $this->getOrderDao()->searchBill($conditions, $orderBy, $start, $limit);

        return ArrayToolkit::index($orders, 'id');
    }

    public function countUserBillNum($conditions)
    {
        $conditions = $this->_prepareSearchConditions($conditions);
        return $this->getOrderDao()->countUserBillNum($conditions);
    }

    public function sumOrderAmounts($startTime, $endTime, array $courseId)
    {
        return $this->getOrderDao()->sumOrderAmounts($startTime, $endTime, $courseId);
    }

    public function searchOrderCount($conditions)
    {
        $conditions = $this->_prepareSearchConditions($conditions);
        return $this->getOrderDao()->searchOrderCount($conditions);
    }

    private function _prepareSearchConditions($conditions)
    {
        $conditions = array_filter($conditions);

        if (isset($conditions['date'])) {
            $dates = array(
                'yesterday' => array(
                    strtotime('yesterday'),
                    strtotime('today'),
                ),
                'today' => array(
                    strtotime('today'),
                    strtotime('tomorrow'),
                ),
                'this_week' => array(
                    strtotime('Monday this week'),
                    strtotime('Monday next week'),
                ),
                'last_week' => array(
                    strtotime('Monday last week'),
                    strtotime('Monday this week'),
                ),
                'next_week' => array(
                    strtotime('Monday next week'),
                    strtotime('Monday next week', strtotime('Monday next week')),
                ),
                'this_month' => array(
                    strtotime('first day of this month midnight'),
                    strtotime('first day of next month midnight'),
                ),
                'last_month' => array(
                    strtotime('first day of last month midnight'),
                    strtotime('first day of this month midnight'),
                ),
                'next_month' => array(
                    strtotime('first day of next month midnight'),
                    strtotime('first day of next month midnight', strtotime('first day of next month midnight')),
                ),
            );

            if (array_key_exists($conditions['date'], $dates)) {
                $conditions['paidStartTime'] = $dates[$conditions['date']][0];
                $conditions['paidEndTime'] = $dates[$conditions['date']][1];
                unset($conditions['date']);
            }
        }

        if (isset($conditions['keywordType']) && isset($conditions['keyword'])) {
            $conditions[$conditions['keywordType']] = $conditions['keyword'];
        }
        unset($conditions['keywordType']);
        unset($conditions['keyword']);

        if (isset($conditions['buyer'])) {
            $user = $this->getUserService()->getUserByName($conditions['buyer'], $conditions['webCode']);
            $conditions['userId'] = $user ? $user['id'] : -1;
        }

        return $conditions;
    }

    public function updateOrderCashSn($id, $cashSn)
    {
        $order = $this->getOrder($id);
        if (empty($order)) {
            E('更新订单失败：订单不存在。');
        }
        if (empty($cashSn)) {
            E('更新订单失败：支付流水号不存在。');
        }
        $this->getOrderDao()->updateOrder($id, array("cashSn" => $cashSn));
    }

    public function updateOrder($id, $orderFileds)
    {
        return $this->getOrderDao()->updateOrder($id, $orderFileds);
    }

    //获取课程生成的最后一个未支付的订单
    public function getCourseOrderEnd($courseId, $uid, $paytype = 0)
    {
        return $this->getOrderDao()->getCourseOrderEnd($courseId, $uid, $paytype);
    }
    
    /**
     * 销售订单管理
     */
      public function searchOrderCountInfo($orderList){
        $orderList = $this->highSearchCondition($orderList);
        return $this->getOrderDao()->searchOrderCountInfo($orderList);
    }
    public function searchOrdersInfo($conditions, $start, $limit) {
        $conditions = $this->highSearchCondition($conditions);
        return $this->getOrderDao()->searchOrdersInfo($conditions, $start, $limit);
    }
    
    public function getSllUserInfo($id){
        return $this->getOrderDao()->getSllUserInfo($id);
    }
     public function allotListInfo($id){
        return $this->getOrderDao()->allotListInfo($id);
    }
    //修改销售人员
    public function UpdateAllot($orderId,$data){
        return $this->getOrderDao()->UpdateAllot($orderId,$data);
    }
 
    private function highSearchCondition($conditions){
         if($conditions['startDateTime'] >0 || $conditions['endDateTime'] >0){
//              $data['createdTime'] = array(array("GT",strtotime($conditions['startDateTime'])),array("LT",strtotime($conditions['endDateTime'])));
              $data['createdTime'] = array('between',array(strtotime($conditions['startDateTime']),strtotime($conditions['endDateTime'])));
         }
         if($conditions['paystartTime'] >0 || $conditions['payendTime'] >0){
             $data['paidTime'] = array('between',array(strtotime($conditions['paystartTime']),strtotime($conditions['payendTime'])));
         }
         if($conditions['zero'] ==0){
             $data['amount'] = array(array("GT",$conditions['zero']));
         }
         if($conditions['zero'] ==""){
             $data['amount'] = array(array("EGT",$conditions['zero']));
         }
         if($conditions['moneyStart'] !='' ||  $conditions['moneyEnd'] !=""){
             $data['amount'] = array(array("EGT",$conditions['moneyStart']),array("ELT",$conditions['moneyEnd']));
         }
       
        //是否退款
         if($conditions['ifcheck'] !="" && $conditions['ifcheck'] !='all'){
             if($conditions['ifcheck'] == 'refunded'){
                 $data['status'] = array("eq",'refunded');
             }else{
                 $data['status'] = array("neq",'refunded');
             }
         }
         if($conditions['ifcheck'] !="" && $conditions['ifcheck'] =='all'){
             $data['status'] = array('in','installment,paid,created,cancelled,refunding,refunded');
         }
         //支付状态
         if($conditions['statuscheck'] !="" && $conditions['statuscheck'] !="all"){
             $data['status'] = $conditions['statuscheck'];
         }
         if($conditions['statuscheck'] !="" && $conditions['statuscheck'] =='all'){
            //$data['status'] = array("in",'paid,created,installment,cancelled');
             $data['status'] = array('in','installment,paid,created,cancelled,refunding,refunded');
         }
        //支付方式
         if($conditions['paycheck'] !="" && $conditions['paycheck'] !='all'){
             $data['payment'] = $conditions['paycheck'];
         }
         if($conditions['paycheck'] !="" && $conditions['paycheck'] =='all'){
             $data['payment'] = array('in','wxpay,alipay,minebuy,Remittance,coin,none');
        }
        //订单来源
         if($conditions['checkname'] !="" && $conditions['checkname'] !='all'){
             $data['sellSource'] = $conditions['checkname'];
         }
         if($conditions['checkname'] !="" && $conditions['checkname'] =="all"){
              $data['sellSource'] = array('in','SEM,SEO,市场推广');
         }
        //销售分配
         if($conditions['sellcheck'] != "" && $conditions['sellcheck']!='all'){
             if($conditions['sellcheck'] == 0){
                 $data['sellUid'] = array("EQ",0);
             }else{
                 $data['sellUid'] = array("EGT",1);
             }
         }
         if($conditions['sellcheck'] != "" && $conditions['sellcheck'] == 'all'){
             $data['sellUid'] = array("EGT",0);
         }
         
         if($conditions['sn']!=""){
             $data['sn'] = $conditions['sn'];
         }
         if($conditions['userId']!=""){
              $data['userId'] = $conditions['userId'];
         }
         if($conditions['title']!=""){
              $data['title'] = $conditions['title'];
         }
         if($conditions['title']!=""){
              $data['title'] = $conditions['title'];
         }
         return $data;
    }

    private function getLogService()
    {
        return $this->createService('System.LogServiceModel');
    }

    private function getSettingService()
    {
        return $this->createService('System.SettingServiceModel');
    }

    private function getUserService()
    {
        return $this->createService('User.UserServiceModel');
    }

    private function getOrderRefundDao()
    {
        return $this->createService('Order.OrderRefundModel');
    }

    private function getOrderDao()
    {
        return $this->createService('Order.OrderModel');
    }

    private function getOrderLogDao()
    {
        return $this->createService('Order.OrderLogModel');
    }

    //todo
    private function getCouponService()
    {
        return $this->createService('Coupon:Coupon.CouponService');
    }

    private function getCourseService()
    {
        return $this->createService('Course.CourseServiceModel');
    }

    private function getCashAccountService()
    {
        return $this->createService('Cash.CashAccountService');
    }

    private function getAuthService()
    {
        return $this->createService('User.AuthService');
    }

    private function getAppService()
    {
        return $this->createService('CloudPlatform.AppServiceModel');
    }

    private function getCourseOrderService()
    {
        return $this->createService('Course.CourseOrderServiceModel');
    }


}