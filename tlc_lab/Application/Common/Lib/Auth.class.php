<?php

/**
 * Created by PhpStorm.
 * User: dingt
 * Date: 2016/8/20
 * Time: 17:58
 */
namespace Common\Lib;
use Common\Lib\HttpRequest;
class Auth
{
    private  $keystonePort ="5000";
    private  $keystoneVersion="v2.0";
    private $projectId;
    private $tokenId;
    private $response;
    public function init_auth($ch,$host,$tenantName,$userName,$userPwd)
    {
        $auth_data = json_encode(array(
            "auth" => array(
                "tenantName" => $tenantName,
                "passwordCredentials" => array(
                    "username" => $userName,
                    "password" => $userPwd
                )
            )
        ));
        $url = "http://$host:$this->keystonePort/$this->keystoneVersion/tokens";
        $httpRequest =new HttpRequest();
        $header =array (
            "Content-Type:application/json",
            "Accept:application/json",
            "User-Agent:python-novaclient");
        $res = $httpRequest->doPost($url,$header,$auth_data);
        //doLog($res);
        $this->response =$res;
        return $res;
        /*curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $auth_data);
        $res = curl_exec($ch);
        if ($res == FALSE) {
            die(curl_error($ch));
        }
        $this->response = $res;
        */
    }

    public function getAuth(){
        $data_output = json_decode($this->response);
        $access = $data_output->access;
        $token = $access->token;
        $tenant = $token->tenant;
        $auth = new Auth();
        $auth->setTokenId($token->id);
        $auth->setProjectId($tenant->id);
        return $auth;
       // $this->setTokenId($token->id);
        //$this->setProjectId($tenant->id);
    }
    /**
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param mixed $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return mixed
     */
    public function getTokenId()
    {
        return $this->tokenId;
    }

    /**
     * @param mixed $tokenId
     */
    public function setTokenId($tokenId)
    {
        $this->tokenId = $tokenId;
    }

}
