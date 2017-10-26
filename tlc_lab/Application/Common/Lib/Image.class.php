<?php
/**
 * Created by PhpStorm.
 * User: dingt
 * Date: 2016/8/30
 * Time: 20:02
 */
namespace Common\Lib;
use  Common\Lib\HttpRequest;
class Image{
    private $imageName;
    private $imagePath;
    private $imageSize;
    private $imageDesc;
    private $imageState;
    private $imageId;
    private $imageUpdated;
    private $imageMeta;
    private $imageType;
    private $imageServer;
    private $url;
    private $header;
    public function init_image($url,$header){
        $this->url="$url/images";
        $this->header=$header;
    }
    public function getImageList(){

        $urlTemp="$this->url/detail";
        $httpRequest =new HttpRequest();
        $response =$httpRequest->doGet($urlTemp,$this->header);
        if($response!=false) {
            $res = json_decode($response, true);
            $imageList=self::parseImageList($res);
            return $imageList;
        }else
            return false;
    }
    private function parseImageList($response){
        $imageList=array();
        $data_images=$response["images"];
        foreach ($data_images as $value){
            $image = self::parseImage($value);
            $imageList[]=$image;
        }
        return $imageList;
    }
    public function getImageByImageId($imageId){
        $urlTemp="$this->url/$imageId";
        $httpRequest =new HttpRequest();
        $response =$httpRequest->doGet($urlTemp,$this->header);

        $jsonObjectArray =json_decode($response,true);
        $image=null;
        if(isset($jsonObjectArray["image"])) {
            $imageObject =$jsonObjectArray["image"];
            $image=self::parseImage($imageObject);
            
        }
        return $image;
    }
    private function parseImage($data_image){
        $image=new Image();
        $image->setImageId($data_image["id"]);
        $image->setImageName($data_image["name"]);
        $image->setImageState($data_image["status"]);
        $image->setImageSize($data_image["OS-EXT-IMG-SIZE:size"]);
        $image->setImageUpdated($data_image["updated"]);
        $image->setImageMeta($data_image["metadata"]);
        if(isset($data_image["server"]))
            $image->setImageServer($data_image["server"]);
        else
            $image->setImageServer(null);
        return $image;
    }

    /**
     * @return mixed
     */
    public function getImageType()
    {
        return $this->imageType;
    }

    /**
     * @param mixed $imageType
     */
    public function setImageType($imageType)
    {
        $this->imageType = $imageType;
    }

    /**
     * @return mixed
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @param mixed $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return mixed
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @param mixed $imagePath
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /**
     * @return mixed
     */
    public function getImageSize()
    {
        return $this->imageSize;
    }

    /**
     * @param mixed $imageSize
     */
    public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;
    }

    /**
     * @return mixed
     */
    public function getImageDesc()
    {
        return $this->imageDesc;
    }

    /**
     * @param mixed $imageDesc
     */
    public function setImageDesc($imageDesc)
    {
        $this->imageDesc = $imageDesc;
    }

    /**
     * @return mixed
     */
    public function getImageState()
    {
        return $this->imageState;
    }

    /**
     * @param mixed $iamgeState
     */
    public function setImageState($imageState)
    {
        $this->imageState = $imageState;
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
    public function getImageUpdated()
    {
        return $this->imageUpdated;
    }

    /**
     * @param mixed $imageUpdated
     */
    public function setImageUpdated($imageUpdated)
    {
        $this->imageUpdated = $imageUpdated;
    }

    /**
     * @return mixed
     */
    public function getImageMeta()
    {
        return $this->imageMeta;
    }

    /**
     * @param mixed $imageMeta
     */
    public function setImageMeta($imageMeta)
    {
        $this->imageMeta = $imageMeta;
    }

    /**
     * @return mixed
     */
    public function getImageServer()
    {
        return $this->imageServer;
    }

    /**
     * @param mixed $imageServer
     */
    public function setImageServer($imageServer)
    {
        $this->imageServer = $imageServer;
    }


}
