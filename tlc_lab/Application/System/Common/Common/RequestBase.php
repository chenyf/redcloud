<?php
//require_once QCLOUDAPI_ROOT_PATH . '/Common/Sign.php';
/**
 * QcloudApi_Common_Request
 */
namespace System\Common\Common;
class QcloudApi_Common_Request_Base
{
    /**
     * $_requestUrl
     * 请求url
     * @var string
     */
    protected static $_requestUrl = '';


    /**
     * $_rawResponse
     * 原始的返回信息
     * @var string
     */
    protected static $_rawResponse = '';

    /**
     * $_version
     * @var string
     */
    protected static $_version = 'SDK_PHP_1.1';
    
    /**
     * $_timeOut
     * 设置连接主机的超时时间
     * @var int 数量级：秒
     * */
    protected static $_timeOut = 10;

    /**
     * getRequestUrl
     * 获取请求url
     */
    public static function getRequestUrl()
    {
        return self::$_requestUrl;
    }

    /**
     * getRawResponse
     * 获取原始的返回信息
     */
    public static function getRawResponse()
    {
        return self::$_rawResponse;
    }

    /**
     * _sendRequest
     * @param  string $url        请求url
     * @param  array  $params     请求参数
     * @param  string $method     请求方法
     * @return
     */
    public static function sendRequest($url, $params, $method = 'POST')
    {

        $ch = curl_init();

        if ($method == 'POST')
        {
            $params = is_array( $params ) ? http_build_query( $params ) : $params;
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        else
        {
            $url .= '?' . http_build_query($paramArray);
        }

        self::$_requestUrl = $url;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT,self::$_timeOut);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (false !== strpos($url, "https")) {
            // 证书
            // curl_setopt($ch,CURLOPT_CAINFO,"ca.crt");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,  false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);
        }
        $resultStr = curl_exec($ch);

        self::$_rawResponse = $resultStr;

        $result = json_decode($resultStr, true);
        if (!$result)
        {
            return $resultStr;
        }
        return $result;
    }

}

