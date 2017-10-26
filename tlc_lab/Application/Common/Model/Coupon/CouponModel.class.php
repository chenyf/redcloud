<?php

namespace Common\Model\Coupon;

use Think\Model;

class CouponModel extends \Common\Model\Common\BaseModel
{

    protected $tableName = 'coupon';


    public function addData($arr)
    {

        return $this->addAll($arr);
    }

    public function getCouponCount($map)
    {
        if ($map) {
            if (isset($map['sufferUid'])) {
                $count = $this->table('coupon c')->join('user u ON u.id = c.sufferUid')->where($map)->getField('count(*) as count');
            } else {
                //$count = $this->where($map)->getField('count(*) as count');
                $count = $this->table('coupon c')->join('user u ON u.id = c.createUid')->where($map)->getField('count(*) as count');
            }
        } else {
            $count = $this->getField('count(*) as count');
        }
        return $count;
    }

    public function getcouponsNum($where)
    {
        return $this->where($where)->count();

    }

    public function getCouponData($map, $start, $end)
    {
        if ($map) {
            if (isset($map['sufferUid'])) {
                $data = $this->table('coupon c')->join('user u ON u.id = c.sufferUid')->where($map)->field('u.nickname,c.createTime,c.couponNum,c.title,c.couponAmount,c.startTime,c.endTime,c.sufferUid,c.type,c.status,c.createUid,c.id')->order('createTime desc')->limit($start, $end)->select();
            } else {
                //$data = $this->where($map)->order('createTime desc')->limit($start, $end)->select();
                $data = $this->table('coupon c')->join('user u ON u.id = c.createUid')->where($map)->field('u.nickname,c.createTime,c.couponNum,c.title,c.couponAmount,c.startTime,c.endTime,c.sufferUid,c.type,c.status,c.createUid,c.id')->order('createTime desc')->limit($start, $end)->select();
            }
        } else {
            $data = $this->order('createTime desc')->limit($start, $end)->select();
        }

        return $data;
    }

    public function getOneData($id)
    {
        $map['id'] = $id;
        $data = $this->where($map)->find();
        return $data;
    }

    public function delCoupon($couponId)
    {
        $map['id'] = $couponId;
        return $this->where($map)->delete();
    }

    public function editCoupon($uid, $donor, $couponId)
    {
        $map['id'] = $couponId;
        $arr = array(
            'sufferUid' => $uid,
            'type' => 1,
            'donor' => $donor
        );
        return $this->where($map)->save($arr);
    }


    public function getcouponsData($where, $start, $end)
    {
        return $this->where($where)->limit($start, $end)->select();
    }

    public function updateCouponStatus($where, $date)
    {
        $this->where($where)->save($date);
    }

    //获取首页优惠券要显示的所有信息
    public function getCouponNum($map)
    {
        $map1 = $map;
        $map1['c.status'] = 1;
        $map0 = $map;
        $map0['c.status'] = 0;
        $map3 = $map;
        $map3['c.status'] = 3;
        if (isset($map['sufferUid'])) {
            $arr = array(
                //'total' => $this->where($map)->getField('count(*) as count'),
                'already' => $this->table('coupon c')->join('user u ON u.id = c.sufferUid')->where($map1)->getField('count(*) as count'),
                'noUse' => $this->table('coupon c')->join('user u ON u.id = c.sufferUid')->where($map0)->getField('count(*) as count'),
                'overdue' => $this->table('coupon c')->join('user u ON u.id = c.sufferUid')->where($map3)->getField('count(*) as count'),
            );
        } else {
            $arr = array(
                //'total' => $this->where($map)->getField('count(*) as count'),
                'already' => $this->table('coupon c')->join('user u ON u.id = c.createUid')->where($map1)->getField('count(*) as count'),
                'noUse' => $this->table('coupon c')->join('user u ON u.id = c.createUid')->where($map0)->getField('count(*) as count'),
                'overdue' => $this->table('coupon c')->join('user u ON u.id = c.createUid')->where($map3)->getField('count(*) as count'),
            );
        }

        return $arr;
    }
}

?>
