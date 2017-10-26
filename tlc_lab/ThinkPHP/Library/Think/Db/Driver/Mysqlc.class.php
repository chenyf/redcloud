<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think\Db\Driver;
use Think\Db;
use Common\Services\SoapClientService;
defined('THINK_PATH') or exit();

/**
 * Mysqlc数据库驱动类:连接中心库
 * @author 钱志伟 2015-11-21
 */
class Mysqlc extends Db{

    
    public function __call($method, $arguments){
       // echo $method,':',print_r($arguments, true),'<br/>';
            //if($method!='mysql_query') continue;
//            $result = call_user_method_array($method, SoapClientService::getInstance()->getSoapClient(), $arguments);
            $result = SoapClientService::remoteCall($method, $arguments);
            return $result;
    }
}