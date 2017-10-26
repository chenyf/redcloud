<?php
/**
 * soap客户端服务类
 * @author 钱志伟 2015-11-13
 */
  
namespace Common\Services;
class SoapClientService {
    public $clientKey = 'fdipzone';
    public $clientSecret = '123456';
    public $centerSiteDoor = 'http://qzw-center.cloud.com:98/';
    public $errorMsg = '';
    public static $_instance = false;
    private $remoteSqlUri = 'Soap/Default/';

    public function __construct() {
        ini_set('display_errors', 1);
        error_reporting(1);
    }
    
    public function getSoapClient(){
        try{
            $uri = $location = $this->centerSiteDoor . $this->remoteSqlUri;
            $soap = new \SoapClient(null,array(
                'location'=> $location,
                'uri'=> $uri,
                'login' => $this->clientKey, // HTTP auth login  
                'password' => $this->clientSecret // HTTP auth password 
           ));
        }catch(SoapFault $e){
            $this->errorMsg = '获得soap失败! 请检查网络是否正常. error msg: '.$e->getMessage();
        }
        $soap = self::setCookie($soap);
        return $soap;
    }
    #设置cookie
    public function setCookie($soap){
        if($_COOKIE){
            foreach($_COOKIE as $key=>$val){
                $soap->__setCookie($key, $val);
            }
        }
        $soap->__setCookie('aaa', 44);
        return $soap;
    }
    
    public static function remoteCall($method, $arguments){
        $soap = self::getInstance()->getSoapClient();
        return $soap->call(array(
            'method'    => $method, 
            'arguments' => $arguments,
        ));
    }

    public static function getInstance(){
        if(!self::$_instance) self::$_instance = new self();
        return self::$_instance;
    }
}
