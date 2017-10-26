<?php

namespace Common\Model\Util;
use Common\Model\Common\BaseModel;
class MobileDeviceModel extends BaseModel
{
	protected $tableName = 'mobile_device';

	public function getMobileDeviceById($id)
	{
                return $this->where("id=".$id)->find();
	}

	public function addMobileDevice(array $parames)
	{
		$affected =$this->add($parames);
              
		if ($affected <= 0) {
		        throw E('Insert MediaParse error.');
		}
		return $this->getMobileDeviceById($affected);
	}

    	public function findMobileDeviceByIMEI($imei)
    	{    
                $data  = $this->where("imei='".$imei."'")->find();
                return $data;
    		
    	}
}