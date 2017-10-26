<?php
namespace Common\Lib;

use Common\Lib\HttpRequest;

class ApiService
{

    public static function postData($url,$dataArray){
        $dataArray = array_merge($dataArray,array(
            'appId' =>  C('api_appid'),
            'skey'  =>  C('api_secret_key'),
        ));

        return self::doPost($url,array(
                'Content-Type: application/json',
        ),$dataArray);
    }

    private static function doPost($url,$header,$data,$timeout=8){
        if(!function_exists('curl_init')){
            return E('do not support curl!!!');
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_POST, true);

        $postData = json_encode($data);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        $header = array_merge($header,array('Content-Length: ' . strlen($postData)));
        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);

        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result);
    }

}