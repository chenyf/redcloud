<?php
namespace Common\Model\Order\OrderProcessor;

use Common\Model\Order\OrderProcessor\OrderProcessor;

class OrderProcessorFactory
{

	public static function create($target)
    {
    	if(empty($target)) {
    		throw new Exception("订单类型不存在");
    	}

    	$class = __NAMESPACE__ . '\\' . ucfirst($target). 'OrderProcessor';

    	return new $class();
    }

}


