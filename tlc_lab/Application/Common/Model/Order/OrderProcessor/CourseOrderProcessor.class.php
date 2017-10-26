<?php
namespace Common\Model\Order\OrderProcessor;

use Common\Model\Common\ServiceKernel;
use Common\Lib\NumberToolkit;
use Exception;

class CourseOrderProcessor extends BaseProcessor implements OrderProcessor
{
	protected $router = "course_show";

	public function getRouter() {
		return $this->router;
	}

	public function getOrderInfo($targetId, $fields)
	{
            $db = (isset($fields['center']) && intval($fields['center']) == 1) ? 'center' : '';
            $course = $this->getCourseService()->switchDB($db)->getCourse($targetId);
            if(empty($course)) {
                throw new Exception("找不到要购买课程!");
            }

            $users = $this->getUserService()->switchDB($db)->findUsersByIds($course['teacherIds']);
            list($coinEnable, $priceType, $cashRate) = $this->getCoinSetting();
        
            $totalPrice = 0;

            if(!$coinEnable) {
                $totalPrice = $course["price"];
                return array(
                    'totalPrice' => $totalPrice,
                    'targetId' => $targetId,
                    'targetType' => "course",
                    'courses' => empty($course) ? null : array($course),
                    'users' => $users,
                );
            }

            if ($priceType == "Coin") {
                $totalPrice = $course["coinPrice"];
            } else if($priceType == "RMB") {
                $totalPrice = $course["price"];
            }

            list($totalPrice, $coinPayAmount, $account, $hasPayPassword) = $this->calculateCoinAmount($totalPrice, $priceType, $cashRate);

            return array(
                'courses' => empty($course) ? null : array($course),
                'users' => empty($users) ? null : $users,
                'totalPrice' => $totalPrice,
                'targetId' => $targetId,
                'targetType' => "course",
                'cashRate' => $cashRate,
                'priceType' => $priceType,
                'account' => $account,
                'hasPayPassword' => $hasPayPassword,
                'coinPayAmount' => $coinPayAmount,
            );
	}

	public function shouldPayAmount($targetId, $priceType, $cashRate, $coinEnabled, $fields)
	{
        $totalPrice = $this->getTotalPrice($targetId, $priceType);

        $amount = $totalPrice;

        //优惠码优惠价格
        $couponApp = $this->getAppService()->findInstallApp("Coupon");
        $couponSetting = $this->getSettingService()->get("coupon");
        if(!empty($couponApp) && isset($couponSetting["enabled"]) && $couponSetting["enabled"] == 1 && $fields["couponCode"] && trim($fields["couponCode"]) != "") {
            $couponResult = $this->afterCouponPay(
                $fields["couponCode"], 
                'course',
                $targetId, 
                $totalPrice, 
                $priceType, 
                $cashRate
            );
            if(isset($couponResult["useable"]) && $couponResult["useable"]=="yes" && isset($couponResult["afterAmount"])){
                $amount = $couponResult["afterAmount"];
            } else {
                unset($couponResult);
            }
        }

        //虚拟币优惠价格
        if(array_key_exists("coinPayAmount", $fields)) {
            $amount = $this->afterCoinPay(
                $coinEnabled, 
                $priceType, 
                $cashRate, 
                $amount,
                $fields['coinPayAmount'], 
                $fields["payPassword"]
            );
        }

        if ($priceType == "Coin") {
            $amount = $amount/$cashRate;
        }
        if($amount<0){
            $amount = 0;
        }
        
        $totalPrice = NumberToolkit::roundUp($totalPrice);
        $amount = NumberToolkit::roundUp($amount);

        return array(
        	$amount, 
        	$totalPrice, 
        	empty($couponResult) ? null : $couponResult,
        );
	}

    public function createOrder($orderInfo, $fields) 
    {
        return $this->getCourseOrderService()->createOrder($orderInfo);
    }

    private function getTotalPrice($targetId, $priceType)
    {
        $totalPrice = 0;
        $course = $this->getCourseService()->getCourse($targetId);
        if($priceType == "RMB") {
            $totalPrice = $course["price"];
        } else if ($priceType == "Coin") {
            $totalPrice = $course["coinPrice"];
        }
        $totalPrice = (float)$totalPrice;
        return $totalPrice;
    }

    public function doPaySuccess($success, $order) {
        if (!$success) {
            return ;
        }

        $this->getCourseOrderService()->doSuccessPayOrder($order['id']);

        return ;
    }

    protected function getCouponService()
    {
        return createService('Coupon.CouponServiceModel');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppServiceModel');
    }

    protected function getCourseService()
    {
    	return createService('Course.CourseServiceModel');
    }

    protected function getSettingService()
    {
        return createService('System.SettingServiceModel');
    }

    protected function getUserService()
    {
        return createService('User.UserServiceModel');
    }

    protected function getCourseOrderService() {
        return createService("Course.CourseOrderServiceModel");
    }
}