<?php
/**
 * Created by PhpStorm.
 * User: dingt
 * Date: 2016/8/30
 * Time: 20:35
 */
namespace  Common\Lib;
use Common\Lib\HttpRequest;
class Flavor{
    private $flavorId;
    private $flavorName;
    private $memoryMB;
    private $disk;
    private $ephemeral;
    private $swap;
    private $vCpus;
    private $rxtxFactor;
    private $isPublic;
    private $disabled;
    private $url;
    private $header;
    public function init_flavor($url,$header){
        $this->header=$header;
        $this->url="$url/flavors";
    }

    public function getFlavorList(){
        $urlTemp="$this->url/detail";
        $httpRequest =new HttpRequest();
        $response =$httpRequest->doGet($urlTemp,$this->header);
        if($response!=false) {
            $res = json_decode($response, true);
            $flavorList=self::parseFlavorList($res);
            return $flavorList;
        }else
            return false;
    }
    private function parseFlavorList($response){
        $flavorList=array();
        $data_flavor=$response["flavors"];
        foreach ($data_flavor as $value){
            $flavor = self::parseFlavor($value);
            $flavorList[]=$flavor;
        }
        return $flavorList;
    }
    public function getFlavorByFlavorId($flavorId){
        $urlTemp="$this->url/$flavorId";
        $httpRequest =new HttpRequest();
        $response =$httpRequest->doGet($urlTemp,$this->header);
        $jsonObjectArray =json_decode($response,true);
        $flavor=null;
        if(isset($jsonObjectArray["flavor"])) {
            $flavorObject =$jsonObjectArray["flavor"];
            $flavor=self::parseFlavor($flavorObject);
        }
        return $flavor;
    }
    public function parseFlavor($res){
        $flavor =new Flavor();
        $flavor->setFlavorId($res["id"]);
        $flavor->setFlavorName($res["name"]);
        $flavor->setMemoryMB($res["ram"]);
        $flavor->setVCpus($res["vcpus"]);
        $flavor->setSwap($res["swap"]);
        $flavor->setIsPublic($res["os-flavor-access:is_public"]);
        $flavor->setRxtxFactor($res["rxtx_factor"]);
        $flavor->setEphemeral($res["OS-FLV-EXT-DATA:ephemeral"]);
        $flavor->setDisk($res["disk"]);
        $flavor->setDisabled($res["OS-FLV-DISABLED:disabled"]);
        return $flavor;
    }

    public function createFlavor($flavor){
        $ret=true;
        if($flavor->getRxtxFactor()>0.0)
            $rxtx=$flavor->getRxtxFactor();
        else
            $rxtx=1.0;
        $send = json_encode(array(
            "flavor"=>array(
                "vcpus"=>$flavor->getVCpus(),
                "disk"=>$flavor->getDisk(),
                "name"=>$flavor->getFlavorName(),
                "rxtx_factor"=>$rxtx,
                "OS-FLV-EXT-DATA:ephemeral"=>$flavor->getEphemeral(),
                "ram"=>$flavor->getMemoryMB(),
                "id"=>$flavor->getFlavorId()
            )
        ));
        $httpRequest =new HttpRequest();
        $response =$httpRequest->doPost($this->url,$this->header,$send);
        $dataRet=json_decode($response,true);
        $flavorRet = $dataRet["flavor"];
        if($flavorRet==null){
            $ret =false;
        }
        return $ret;


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
    public function getFlavorName()
    {
        return $this->flavorName;
    }

    /**
     * @param mixed $flavorName
     */
    public function setFlavorName($flavorName)
    {
        $this->flavorName = $flavorName;
    }

    /**
     * @return mixed
     */
    public function getMemoryMB()
    {
        return $this->memoryMB;
    }

    /**
     * @param mixed $memoryMB
     */
    public function setMemoryMB($memoryMB)
    {
        $this->memoryMB = $memoryMB;
    }

    /**
     * @return mixed
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * @param mixed $disk
     */
    public function setDisk($disk)
    {
        $this->disk = $disk;
    }

    /**
     * @return mixed
     */
    public function getEphemeral()
    {
        return $this->ephemeral;
    }

    /**
     * @param mixed $ephemeral
     */
    public function setEphemeral($ephemeral)
    {
        $this->ephemeral = $ephemeral;
    }

    /**
     * @return mixed
     */
    public function getSwap()
    {
        return $this->swap;
    }

    /**
     * @param mixed $swap
     */
    public function setSwap($swap)
    {
        $this->swap = $swap;
    }

    /**
     * @return mixed
     */
    public function getVCpus()
    {
        return $this->vCpus;
    }

    /**
     * @param mixed $vCpus
     */
    public function setVCpus($vCpus)
    {
        $this->vCpus = $vCpus;
    }

    /**
     * @return mixed
     */
    public function getRxtxFactor()
    {
        return $this->rxtxFactor;
    }

    /**
     * @param mixed $rxtxFactor
     */
    public function setRxtxFactor($rxtxFactor)
    {
        $this->rxtxFactor = $rxtxFactor;
    }

    /**
     * @return mixed
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param mixed $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }

    /**
     * @return mixed
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param mixed $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

}
