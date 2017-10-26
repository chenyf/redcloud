<?php
namespace Common\Model\Coupon;
use Think\Model;

class CouponServiceModel extends \Common\Model\Common\BaseModel
{

    public function checkCouponUseable()
    {
        return '';
    }

    public function getMemberByUserId()
    {
        return '';
    }

    public function addData($title, $startTime, $endTime, $amount, $num, $imgUrl)
    {
        $user = $this->getCurrentUser();
        $temp = array();
        for ($i = 0; $i < $num; $i++) {
            $arr = array(
                'createUid' => $user['id'],
                'createTime' => time(),
                'couponNum' => uniqid() . $this->mtrandStr(),
                'couponAmount' => $amount,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'cardUrl' => $imgUrl,
                'title' => $title
            );
            $temp[] = $arr;
        }
        return $this->getCouponDao()->addData($temp);
    }

    public function mtrandStr()
    {
        $numArr = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm');
        for ($i = 0; $i < 5; $i++) {
            $str .= $numArr[mt_rand(0, count($numArr) - 1)];
        }
        return $str;
    }


    public function getcouponsNum($conditions)
    {

        $key = $conditions['val'];
        if ($conditions['sel'] == "price") {
            $where = "couponAmount = $key and `status` = 0 and sufferUid =0 and donor=0";
        } elseif ($conditions['sel'] == "title") {

            $where = "couponNum like '%$key%' and `status` = 0 and sufferUid =0 and donor=0";
        } else {
            $where = "`status` = 0 and sufferUid =0 and donor=0";
        }

        $num = $this->getCouponDao()->getcouponsNum($where);
        echo $num;
        return $num;
    }

    public function getcouponsData($conditions = array(), $start = 0, $end = 15, $order = '')
    {
        $key = $conditions['val'];
        if ($conditions['sel'] == "price") {
            $where = "couponAmount = $key and `status` = 0 and sufferUid =0 and donor=0";
        } elseif ($conditions['sel'] == "title") {
            $where = "couponNum like '%$key%' and `status` = 0 and sufferUid =0 and donor=0";
        } else {
            $where = "`status` = 0 and sufferUid =0 and donor=0";
        }
        $res = $this->getCouponDao()->getcouponsData($where, $start, $end);
        return $res;
    }


    public function updateCouponStatus($uid, $id, $sufferUid)
    {
        $date['donor'] = $uid;
        $date['sufferUid'] = $sufferUid;
        $where = "id in ($id)";
        $this->getCouponDao()->updateCouponStatus($where, $date);
    }

    public function getCouponCount($map)
    {
        return $this->getCouponDao()->getCouponCount($map);
    }

    public function getCouponData($map, $start, $end)
    {
        $data = $this->getCouponDao()->getCouponData($map, $start, $end);
        $this->getUserService()->decorationOfUserByUid($data, array(
            'uid' => 'createUid',
            'filter_column' => array('creator' => 'nickname')
        ));
        $this->getUserService()->decorationOfUserByUid($data, array(
            'uid' => 'sufferUid',
            'filter_column' => array('receiver' => 'nickname')
        ));
        return $data;
    }

    //获取优惠券相关统计信息
    public function getCouponNum($map)
    {

        return $this->getCouponDao()->getCouponNum($map);
    }

    public function editCoupon($uid, $donor, $couponId)
    {

        return $this->getCouponDao()->editCoupon($uid, $donor, $couponId);
    }

    public function delCoupon($couponId)
    {
        return $this->getCouponDao()->delCoupon($couponId);
    }

    public function getOneCoupon($id)
    {
        return $this->getCouponDao()->getOneData($id);
    }

    public function getCouponDao()
    {
        return $this->createDao("Coupon.Coupon");
    }


    private function getUserService()
    {
        return $this->createService('User.UserService');
    }


}

?>
