<?php

/*
 * 业务层
 * @package
 * @author     郭俊强
 * @version    $Id$
 */

namespace Common\Model\Order;

use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class OrderCashServiceModel extends BaseModel {

    protected $currentDb = CENTER;

    public function doOrderCash($order, $courseId, $webCode) {
        if ($order['amount'] == 0)
            return false;

        $couserInfo = $this->getCourseService()->getCourse($courseId);
        $level = $this->getSchoolService()->getSchoolLevel($order['webCode']);
        if ($order['webCode'] == $couserInfo['webCode']) {
            $wyzcLevel = $level['privateWyzcLevel'];
        } else {
            $level = $this->getSchoolService()->getSchoolLevel($couserInfo['webCode']);
            
            $wyzcLevel = $level['privateWyzcLevel'];
        }
        if ($order['payType'] == 1) {
            $order['amount'] = $order['amount'] * 70 / 100;
        }
        $data = array();
        $data['price'] =$couserInfo['coursePrice'] * $wyzcLevel/ 100;
        $data['orderId'] = $order['id'];
        $data['type'] = 1;
        $data['createTime'] = time();
        $data['cashTime'] = time();
        $this->getOrderCashDao()->addOrderCash($data);
        $data = array();
        $data['price'] = $order['amount'] - $couserInfo['coursePrice'] * $wyzcLevel/ 100;
        $data['orderId'] = $order['id'];
        $data['type'] = 0;
        $data['createTime'] = time();
        $data['cashTime'] = time();
        $this->getOrderCashDao()->addOrderCash($data);
    }

    //获取提现订单 并计算提现金额
    public function getCashOrder() {
       // $dataTime = strtotime(date("Y-m-d")) - 30 * 24 * 3600;
       // $map['a.createTime'] = array('elt', $dataTime);
        $map['a.status'] = 0;
        $map['b.status'] = 'paid';
        $map['a.type'] = 0;
        $list = $this->getOrderCashDao()->getListCash($map);
       
        $amount = ArrayToolkit::column($list, 'amount');
        $price = ArrayToolkit::column($list, 'price');
        $amount=array_sum($amount);
        $price=array_sum($price);
        $data['orderList']=$list;
        $data['amountCount']=$amount;
        $data['amount']=$price;
        return $data;
    }
    //生成提现单
    public function doCashOrder(){
        $list=$this->getCashOrder();
        $data=array();
        $data['title']=date("YmdHis")."提现单";
        $data['type'] = 0;
        $data['amount']=$list['amount'];
        $data['orderPrice']=$list['amountCount'];
        $data['createdTime']=time();
        $id = $this->getSchoolOrdersService()->addOrders($data);
        $orderId=ArrayToolkit::column($list['orderList'], 'id');
        $orderId=  implode(",",$orderId);
        $this->getOrderCashDao()->doCashList($orderId,$id);
        $this->getOrderService()->doOrderList($orderId,$id);
        return true;
    }
    //更改状态
    public function doCashListStatus($id,$status){
        return $this->getOrderCashDao()->doCashListStatus($id,$status);
    }
    //获取老师结算详情
    public function getTeacherOrder($parm){
        $list = $this->getOrderCashDao()->getListCash($parm);
        $amount = ArrayToolkit::column($list, 'amount');
        $price = ArrayToolkit::column($list, 'price');
        $amount=array_sum($amount);
        $price=array_sum($price);
        $data['orderList']=$list;
        $data['amountCount']=$amount;
        $data['amount']=$price;
        return $data;
    }
    //生成结算单
    public function doTeacherOrder($parm,$type){
        $list=$this->getTeacherOrder($parm);
        $data['cashId']=$parm['a.cashId'];
        $user = $this->getCurrentUser();
        $data['title']=$user['nickname'].date("YmdHis")."结算单";
        $data['userId']=$user['id'];
        $data['type']=$type;
        $data['status']=0;
        $data['amount']=$list['amount'];
        $data['schPrice']=$list['amountCount'];
        $data['createdTime']=time();
        $data['updTime']=time();
        return $this->getCashTeacherDao()->switchDB(C('WEBSITE_CODE'))->addCashTeaher($data);
    }
    public function searchOrderCashCount($parm){
        return $this->getOrderCashDao()->searchOrderCashCount($parm);
    }
    public function searchOrderCashs($conditions, $sort = 'latest', $start, $limit){
        $orderBy = array();
        if ($sort == 'latest') {
            $orderBy = array('a.createTime', 'DESC');
        } elseif ($sort == 'early') {
            $orderBy = array('a.createTime', 'ASC');
        } else {
            $orderBy = array('a.createTime', 'DESC');
        }
        return $this->getOrderCashDao()->searchOrderCashs($conditions, $orderBy, $start, $limit);
    }
    private function getCourseService() {
        return $this->createService('Course.CourseServiceModel');
    }
    private function getSchoolOrdersService() {
        return $this->createService('Center.SchoolOrdersServiceModel');
    }

    private function getOrderService() {
        return $this->createService('Order.OrderServiceModel');
    }

    private function getOrderCashDao() {
        return $this->createService('Order.OrderCashModel');
    }
    private function getCashTeacherDao() {
        return $this->createService('Cash.CashTeacherModel');
    }
    private function getSchoolService() {
        return $this->createService('Center\School.SchoolService');
    }

}