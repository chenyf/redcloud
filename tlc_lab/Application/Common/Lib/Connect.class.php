<?php
/**
 * Created by PhpStorm.
 * User: dingt
 * Date: 2016/8/15
 * Time: 23:00
 */
namespace Common\Lib;
use Common\Lib\Auth;
use Common\Lib\Server;
use Common\Lib\Image;
use Common\Lib\Flavor;
class Connect {
    private $host;
    private $tenantName;
    private $userName;
    private $userPwd;
    private $url;
    private $header;
    private $novaPort="8774";
    function  init_connect($host,$tenantName,$userName,$userPwd)
    {
        $ch = curl_init();
        $this->host=$host;
        $this->tenantName=$tenantName;
        $this->userName=$userName;
        $this->userPwd=$userPwd;
        $auth = new Auth();
        $res= $auth->init_auth($ch,$host,$tenantName,$userName,$userPwd);
        //$auth_temp=$auth->getAuth();
//        doLog($res);
        $data_output = json_decode($res);
        $access = $data_output->access;
        $token = $access->token;
        $tenant = $token->tenant;
        $this->header=$this->getHeader($token->id);
        $this->url=$this->getUrl($tenant->id);
        //doLog($this->url);
    }
    public function getServer(){
        
        $server = new Server();
        $server->init_server($this->url,$this->header);
        return $server;
    }
    public function getImage(){
        $image=new Image();
        $image->init_image($this->url,$this->header);
        return $image;
    }
    public function getFlavor(){
        $flavor=new Flavor();
        $flavor->init_flavor($this->url,$this->header);
        return $flavor;
    }
    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getTenantName()
    {
        return $this->tenantName;
    }

    /**
     * @param mixed $tenantName
     */
    public function setTenantName($tenantName)
    {
        $this->tenantName = $tenantName;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getUserPwd()
    {
        return $this->userPwd;
    }

    /**
     * @param mixed $userPwd
     */
    public function setUserPwd($userPwd)
    {
        $this->userPwd = $userPwd;
    }

    public function getUrl($tenantId){
        return "http://$this->host:$this->novaPort/v2/$tenantId";
    }
    public function setUrl($url){
        $this->url = $url;
    }
    public function getHeader($tokenId){
        $ret= array(
            "x-auth-project-id:admin",
            "x-auth-token:$tokenId",
            "user-agent:python-novaclient"
        );
        return $ret;
    }
    public function setHeader($header){
        $this->header=$header;
    }
}
