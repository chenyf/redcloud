<?php

namespace Common\Model\Cash;

use Common\Model\Common\BaseModel;

class CashOrdersServiceModel extends BaseModel {

     public function getCashReceiptCount($condition) {
        $data = array();
        $data=$condition['webCode'];
       
        $count = $this->addOrderFail()->getCount($data);
        return $count;
    }
    
    public function getReceiptInfo($id){
        return $this->addOrderFail()->getReceiptInfo($id);
    }

    public function getCashReceiptList($condition = array(), $start = 0, $end = 15, $order = 'id desc') {
        $data=$condition['webCode'];
         
         $list =  $this->addOrderFail()->getReceiptList($data,$start, $end,$order);
        
         return $list;
    }
    
    
    public function getOrder($id) {
        return $this->getOrderDao()->getOrder($id);
    }

    public function addOrder($order) {
        // $coinSetting=$this->getSettingService()->get('coin',array());

        if (!is_numeric($order['amount'])) {
            E('充值金额必须为整数!');
        }
        $coin = $order['amount'];
        $order['sn'] = "O" . date('YmdHis') . rand(10000, 99999);
        $order['status'] = "created";
        $order['title'] = "充值" . $coin . "元";
        $order['createdTime'] = time();
        $order['type'] = $order['type'];
        return $this->getOrderDao()->addOrder($order);
    }
    
    
    public function addOrderFailure($data){
        return $this->addOrderFail()->addOrderFailure($data);
    }

    public function payOrder($payData) {
        $success = false;
        $order = $this->getOrderDao()->getOrderBySn($payData['sn'], true);
        if (empty($order)) {
            E("订单({$payData['sn']})已被删除，支付失败。");
        }
        if ($payData['status'] == 'success') {
            if ($this->canOrderPay($order)) {
                $this->getOrderDao()->updateOrder($order['id'], array(
                    'status' => 'paid',
                    'paidTime' => $payData['paidTime'],
                    'tradeNo' => $payData['tradeNo'],
                    'payment' => $payData['payment']
                ));
                $this->_createLog($order['id'], 'pay_success', '付款成功', $payData);
                $this->getCashAccountService()->updCashAccount($order['amount'], $order['userId'], $order['type']);
                $success = true;
            } else {
                $this->_createLog($order['id'], 'pay_ignore', '订单已处理', $payData);
            }
        } else {
            $this->_createLog($order['id'], 'pay_unknown', '', $payData);
        }
        $order = $this->getOrderDao()->getOrder($order['id']);

        return array($success, $order);
    }

    //合并数据
    public function updCashOrder($uid, $appUid) {
        $result = $this->getOrderDao()->updCashOrder($uid, $appUid);
        if($result){
           $this->getCashAccountService()->updCashAccountUser($uid, $appUid);
        }
        return $result;
    }

    public function searchOrders($conditions, $orderBy, $start, $limit) {
        $this->closeOrders();

        return $this->getOrderDao()->searchOrders($conditions, $orderBy, $start, $limit);
    }
    public function searchOrdersList($conditions, $orderBy, $start, $limit){
      $this->closeOrders();  
      return $this->getOrderDao()->searchOrdersList($conditions, $orderBy, $start, $limit);
    }

    public function searchOrdersCount($conditions) {
        return $this->getOrderDao()->searchOrdersCount($conditions);
    }

    //取消订单
    public function cancelOrder($id) {
        return $this->getOrderDao()->cancelOrder($id);
    }

    public function closeOrders() {
        $time = time() - 48 * 3600;
        $this->getOrderDao()->closeOrders($time);
    }

    public function analysisAmount($conditions) {
        return $this->getOrderDao()->analysisAmount($conditions);
    }

    private function _createLog($orderId, $type, $message = '', array $data = array()) {
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

    public function getLogsByOrderId($orderId) {
        return $this->getOrderLogDao()->getLogsByOrderId($orderId);
    }

    public function canOrderPay($order) {
        if (empty($order['status'])) {
            E(new \InvalidArgumentException());
        }
        return in_array($order['status'], array('created'));
    }

    protected function getOrderDao() {
        return $this->createDao('Cash.CashOrdersModel');
    }
    
    protected function addOrderFail(){
        return $this->createService("Cash.IosReceiptModel");
    }

    protected function getOrderLogDao() {
        return $this->createDao('Cash.CashOrdersLogModel');
    }

    protected function getSettingService() {

        return $this->createService('System.SettingServiceModel');
    }

    protected function getCashService() {

        return $this->createService('Cash.CashServiceModel');
    }

    protected function getCashAccountService() {
        return $this->createService('Cash.CashAccountService');
    }

}