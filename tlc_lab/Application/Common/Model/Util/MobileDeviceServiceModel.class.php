<?php
namespace Common\Model\Util;
use Common\Model\Common\BaseModel;
class MobileDeviceServiceModel extends BaseModel
{
	function addMobileDevice($parames)
	{
		if ($this->getMobileDeviceDao()->findMobileDeviceByIMEI($parames["imei"])) {
			return false;
		}
		$mobileDevice = $this->getMobileDeviceDao()->addMobileDevice($parames);
		return !empty($mobileDevice);
	}


    	function findMobileDeviceByIMEI($imei)
    	{
    		return $this->getMobileDeviceDao()->findMobileDeviceByIMEI($imei);
    	}

    	protected function getMobileDeviceDao ()
	{
	    return $this->createDao('Util.MobileDeviceModel');
	}
}