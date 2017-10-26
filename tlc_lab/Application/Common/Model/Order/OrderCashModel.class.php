<?php

/*
 * 数据层
 * @package    
 * @author     郭俊强
 * @version    $Id$
 */

namespace Common\Model\Order;

use Think\Model;
use Common\Model\Common\BaseModel;

class OrderCashModel extends BaseModel {

    protected $tableName = 'order_cash';

    public function addOrderCash($data) {
        return $this->add($data);
    }

    public function getListCash($parm) {
        return $this->table('order_cash a')->join('orders b on a.orderId=b.id')->where($parm)->field('b.*,a.price')->select();
    }

    public function searchOrderCashCount($parm) {
        return $this->where($parm)->count();
    }

    public function searchOrderCashs($conditions, $orderBy, $start, $limit) {
        $this->filterStartLimit($start, $limit);
        $info = $this
                ->table('order_cash a')->join('orders b on a.orderId=b.id')->where($conditions)
                ->order("{$orderBy[0]} {$orderBy[1]}")
                ->field('b.*,a.price')->limit("{$start},{$limit}")
                ->select();
        return $info;
    }

    public function doCashList($orderId, $id) {
        $data['cashId'] = $id;
        $data['status'] = 1;
        $map['status'] = 0;
        $map['type'] = 0;
        $map['orderId'] = array('in', $orderId);
        return $this->where($map)->save($data);
    }
    public function doCashListStatus($id,$status){
        $map['cashId'] = $id;
        $data['status'] = $status;
        return $this->where($map)->save($data);
    }

}

