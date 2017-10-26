<?php
namespace Common\Lib;
use Common\Lib\HttpRequest;
class Server
{
    private $serverName;
    private $instanceId;
    private $instanceName;
    private $floatingIp;
    private $fixedIp;
    private $serverState;
    private $imageId;
    private $flavorId;
    private $hypervisorName;
    private $taskState;
    private $availabilityZone;
    private $created;
    private $security_groups;
    private $volume_attached;
    private $url;
    private $header;
    private $ch;
    function  init_server($url,$header){
        $this->header=$header;
        $this->url ="$url/servers";
    }

    public function getServerList(){
        //$this->ch = curl_init();
        $urlTemp="$this->url/detail";
        $httpRequest=new HttpRequest();
        $response =  $httpRequest->doGet($urlTemp,$this->header);
        //doLog($response);
        $res=json_decode($response,true);
       // doLog($res->servers);
        /*$num = count($res["servers"]);
        for($i=0;$i<$num;$i++){
            doLog($res->servers[$i]->id);
        }
        foreach($res["servers"] as $server){
            doLog($server["id"]);
        }*/
		
		$resultList = array();
        $serverList=$this->parseServerList($res);
        foreach($serverList as $key => $value){
			$resultList[$key]["serverName"] = $value->getServerName();
            //doLog($value->getServerName());
			$resultList[$key]["instanceId"] = $value->getInstanceId();
           // doLog($value->getInstanceId());
			$resultList[$key]["serverState"] = $value->getServerState();
            //doLog($value->getServerState());
			$resultList[$key]["hypervisorName"] = $value->getHypervisorName();
           // doLog($value->getHypervisorName());
			$resultList[$key]["availabilityZone"] = $value->getAvailabilityZone();
            //doLog($value->getAvailabilityZone());
			$resultList[$key]["created"] = $value->getCreated();
        //    doLog($value->getCreated());
			$resultList[$key]["imageId"] = $value->getImageId();
          //  doLog($value->getImageId());
			$resultList[$key]["flavorId"] = $value->getFlavorId();
            //doLog($value->getFlavorId());            
        } 
        return $resultList;
    }
    private function parseServerList($response){
       // $data=json_decode($response,true);
        $data_servers=$response["servers"];
        //$arrayNum=count($data_servers);
        $serverList =array();
        foreach($data_servers as $value) {
            $server = new Server();
            //doLog($value["id"]);
            $server->setInstanceId($value["id"]);
            $server->setInstanceName($value["OS-EXT-SRV-ATTR:instance_name"]);
            $server->setServerName($value["name"]);
            $server->setServerState($value["status"]);
            $server->setHypervisorName($value["OS-EXT-SRV-ATTR:host"]);
            $server->setAvailabilityZone($value["OS-EXT-AZ:availability_zone"]);
            $server->setCreated($value["created"]);
            $server->setTaskState($value["OS-EXT-STS:task_state"]);
            $server->setImageId($value["image"]["id"]);
        /*    if($value["image"]!=NULL&&empty($value["image"])){
                $image=$value["image"];
                if($image!=NULL){
                    if($image["id"]!=NULL){
                        $server->setImageId($image["id"]);
                    }
                }
            }*/
            $server->setFlavorId($value["flavor"]["id"]);
            $serverList[]=$server;
        }   
        return $serverList; 
    }
    public function startServer($instanceId){
        $httpRequest=new HttpRequest();
        $urlTemp="$this->url/$instanceId/action";
        //$send ="{\"os-start\":null}";
        $send =json_encode(array(
            "os-start"=>null
        ));
        $headerTemp=$this->header;
        $headerTemp[]='Content-Type: application/json';
        $headerTemp[]='Accept: application/json';
        
        $response=$httpRequest->doPost($urlTemp,$headerTemp,$send);
        $ret=false;
        if($response!=True){
            $data=json_decode($response,true);
            if(isset($data["overLimit"])){
                $overLimit = $data["overLimit"];
                 if($overLimit!=null)
                    $ret=true;
                 else
                     $ret=false;
            }
            else
                $ret=false;
        }else
            $ret=True;
        doLog("start server ". "$response");
        return $ret;

    }
    public function stopServer($instanceId){
        $httpRequest=new HttpRequest();
        $urlTemp="$this->url/$instanceId/action";
        $send ="{\"os-stop\":null}";
        $headerTemp=$this->header;
        $headerTemp[]='Content-Type: application/json';
        $headerTemp[]='Accept: application/json';
        
        $response=$httpRequest->doPost($urlTemp,$headerTemp,$send);
        $ret=false;
        if($response!=True){
            $data=json_decode($response,true);
            if(isset($data["overLimit"])){
                $overLimit = $data["overLimit"];
                 if($overLimit!=null)
                    $ret=true;
                 else
                     $ret=false;
            }
            else
                $ret=false;
        }else
            $ret=True;
        doLog("stop server ". "$response");
        return $ret;

    }
    public function rebootServer($instanceId){
        $httpRequest=new HttpRequest();
        $urlTemp="$this->url/$instanceId/action";
        $send ="{\"reboot\":{\"type\":\"SOFT\"}}";
        $headerTemp=$this->header;
        $headerTemp[]='Content-Type: application/json';
        $headerTemp[]='Accept: application/json';
        $response=$httpRequest->doPost($urlTemp,$headerTemp,$send);
        doLog($response);
        $ret=false;
        if($response!=True){
            $data=json_decode($response,true);
        //    doLog($data);
            if(isset($data["overLimit"])){
                $overLimit = $data["overLimit"];
                 if($overLimit!=null)
                    $ret=true;
                 else
                     $ret=false;
            }
            else
                $ret=false;
        }else
            $ret=True;
        doLog("reboot server ". "$response");
        return $ret;

    }
    public function rebootServerHard($instanceId){
        $httpRequest=new HttpRequest();
        $urlTemp="$this->url/$instanceId/action";
        $send ="{\"reboot\":{\"type\":\"HARD\"}}";
        $headerTemp=$this->header;
        $headerTemp[]='Content-Type: application/json';
        $headerTemp[]='Accept: application/json';
        
        $response=$httpRequest->doPost($urlTemp,$headerTemp,$send);
        $ret=false;
        if($response!=True){
            $data=json_decode($response,true);
            if(isset($data["overLimit"])){
                $overLimit = $data["overLimit"];
                 if($overLimit!=null)
                    $ret=true;
                 else
                     $ret=false;
            }
            else
                $ret=false;
        }else
            $ret=True;
        doLog("reboot server hard ". "$response");
        return $ret;

    }
    public function getVncByInstanceId($instanceId){
        $httpRequest=new HttpRequest();
        $urlTemp="$this->url/$instanceId/action";
        $send ="{\"os-getVNCConsole\":{\"type\":\"novnc\"}}";
        $headerTemp=$this->header;
        $headerTemp[]='Content-Type: application/json';
        $headerTemp[]='Accept: application/json';
        $response=$httpRequest->doPost($urlTemp,$headerTemp,$send);
        doLog($response);
        $data=json_decode($response,true);
        $ret="";
        if(isset($data["console"])){
            $ret=$data["console"]["url"];
        }
        return $ret;
    }
    public function boot($serverName,$imageId,$flavorId){
        $httpRequest=new HttpRequest();
        $urlTemp=$this->url;
        $send=json_encode(array(
           "server"=>array(
               "name"=>$serverName,
                "imageRed"=>$imageId,
                "flavorRef"=>$flavorId,
                "max_count"=>1,
                "min_count"=>1,
                "networks"=>[]
            )
        ));
        $response=$httpRequest->doPost($urlTemp,$this->header,$send);
        $data=json_decode($response);
        $overLimit = $data["overLimit"];
        if($overLimit!=null){
            $detail=$overLimit["details"];
            if($detail!=null){
                $ret=true;
            }
        }else{
            $ret =$this->getBootInstanceId($data);
        }
        return $ret;
    }
    private function getBootInstanceId($response){
        $server=$response["server"];
        if($server!=null){
            return $server["id"];
        }
        return false;
    }


    /**
     * @return mixed
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * @param mixed $serverName
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * @return mixed
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * @param mixed $instanceId
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;
    }

    /**
     * @return mixed
     */
    public function getInstanceName()
    {
        return $this->instanceName;
    }

    /**
     * @param mixed $instanceName
     */
    public function setInstanceName($instanceName)
    {
        $this->instanceName = $instanceName;
    }

    /**
     * @return mixed
     */
    public function getFloatingIp()
    {
        return $this->floatingIp;
    }

    /**
     * @param mixed $floatingIp
     */
    public function setFloatingIp($floatingIp)
    {
        $this->floatingIp = $floatingIp;
    }

    /**
     * @return mixed
     */
    public function getFixedIp()
    {
        return $this->fixedIp;
    }

    /**
     * @param mixed $fixedIp
     */
    public function setFixedIp($fixedIp)
    {
        $this->fixedIp = $fixedIp;
    }

    /**
     * @return mixed
     */
    public function getServerState()
    {
        return $this->serverState;
    }

    /**
     * @param mixed $serverState
     */
    public function setServerState($serverState)
    {
        $this->serverState = $serverState;
    }

    /**
     * @return mixed
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * @param mixed $imageId
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
    }

    /**
     * @return mixed
     */
    public function getFlavorId()
    {
        return $this->flavorId;
    }

    /**
     * @param mixed $flavorId
     */
    public function setFlavorId($flavorId)
    {
        $this->flavorId = $flavorId;
    }

    /**
     * @return mixed
     */
    public function getHypervisorName()
    {
        return $this->hypervisorName;
    }

    /**
     * @param mixed $hypervisorName
     */
    public function setHypervisorName($hypervisorName)
    {
        $this->hypervisorName = $hypervisorName;
    }

    /**
     * @return mixed
     */
    public function getTaskState()
    {
        return $this->taskState;
    }

    /**
     * @param mixed $taskState
     */
    public function setTaskState($taskState)
    {
        $this->taskState = $taskState;
    }

    /**
     * @return mixed
     */
    public function getAvailabilityZone()
    {
        return $this->availabilityZone;
    }

    /**
     * @param mixed $availabilityZone
     */
    public function setAvailabilityZone($availabilityZone)
    {
        $this->availabilityZone = $availabilityZone;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getSecurityGroups()
    {
        return $this->security_groups;
    }

    /**
     * @param mixed $security_groups
     */
    public function setSecurityGroups($security_groups)
    {
        $this->security_groups = $security_groups;
    }

    /**
     * @return mixed
     */
    public function getVolumeAttached()
    {
        return $this->volume_attached;
    }

    /**
     * @param mixed $volume_attached
     */
    public function setVolumeAttached($volume_attached)
    {
        $this->volume_attached = $volume_attached;
    }



}
