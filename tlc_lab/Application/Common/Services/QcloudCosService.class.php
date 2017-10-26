<?php

/**
 * 腾讯云cos存储服务
 * @author 谈海涛 2016-03-24
 */

namespace Common\Services;

use Qcloud_cos\Auth;
use Qcloud_cos\Cosapi;

class QcloudCosService {
    
    private static $_bucketName = '';
    private static $_APPID = '';
    private static $_SECRET_ID = '';
    private static $_SECRET_KEY = '';



    public function __construct( ) {
        self::$_bucketName = C('QCOS_BUCKETNAME');
        self::$_APPID = C("QCOS_APPID");
        self::$_SECRET_ID = C("QCOS_SECRETID");
        self::$_SECRET_KEY = C("QCOS_SECRETKEY");
    }

    /**
     * 文件上传
     * @author tanhaitao 2016-03-24
     */
    public function uploadFile($srcPath, $dstPath, $bizAttr = null) {
        if (!$srcPath || !$dstPath) return false;
        $bucketName = self::$_bucketName;
        Cosapi::setTimeout(10);
        $uploadRet = Cosapi::upload($srcPath, $bucketName, $dstPath, $bizAttr = null);
        return $uploadRet;
    }

    /**
     * 文件分片上传
     * @param  string  $srcPath     本地文件路径
     * @param  string  $bucketName  上传的bcuket名称
     * @param  string  $dstPath     上传的文件路径
     * @param  $sliceSize 指定分片大小来分片上传
     * @param  $session   (文件唯一id)指定了session，可以实现断点续传
     * @author tanhaitao 2016-03-24
     */
    public function uploadFileSlice($srcPath, $dstPath, $bizAttr = null, $sliceSize = 0, $session = null) {
        if (!$srcPath || !$dstPath)
            return false;
        $bucketName = self::$_bucketName;
        Cosapi::setTimeout(10);
        $sliceUploadRet = Cosapi::upload_slice($srcPath, $bucketName, $dstPath, $bizAttr, $sliceSize, $session);
        return $sliceUploadRet;
    }

    /**
     * 文件删除
     * @author tanhaitao 2016-03-24
     */
    public function delFile($dstPath) {
        if (!$dstPath)
            return false;
        $bucketName = self::$_bucketName;
        $delRet = Cosapi::del($bucketName, $dstPath);
        return $delRet;
    }
    
     /**
     * 改变文件属性
     * @author tanhaitao 2016-03-24
     */
    public function updateFile($dstPath ,$bizAttr) {
        if (!$dstPath)
            return false;
        $bucketName = self::$_bucketName;
        $updateRet = Cosapi::update($bucketName, $dstPath , $bizAttr);
        return $updateRet;
    }
    
    
    /**
     * 创建目录 
     * @author tanhaitao 2016-03-24
     */
    public function createFolder($dstPath) {
        if (!$dstPath)
            return false;
        $bucketName = self::$_bucketName;
        $createFolderRet = Cosapi::createFolder($bucketName, $dstPath);
        return $createFolderRet;
    }
    
    /**
     * 删除目录 
     * @author tanhaitao 2016-03-24
     */
    public function delFolder($dstPath) {
        if (!$dstPath)
            return false;
        $bucketName = self::$_bucketName;
        $delRet = Cosapi::delFolder($bucketName, $dstPath);
        return $delRet;
    }
    
    
    /**
     * 文件查询 
     * @author tanhaitao 2016-03-24
     */
    public function statFile($dstPath) {
        if (!$dstPath)
            return false;
        $bucketName = self::$_bucketName;
        $statRet = Cosapi::stat($bucketName, $dstPath);
        return $statRet;
    }
    
    /**
     * 改变目录属性
     * @author tanhaitao 2016-03-24
     */
    public function updateFolder($dstPath ,$bizAttr) {
        if (!$dstPath)
            return false;
        $bucketName = self::$_bucketName;
        $updateRet = Cosapi::updateFolder($bucketName, $dstPath , $bizAttr);
        return $updateRet;
    }
    
    
    /**
     * 目录文件列表 
     * @author tanhaitao 2016-03-24
     */
    public function listFolder($dstPath) {
        if (!$dstPath)
            return false;
        $bucketName = self::$_bucketName;
        $listRet = Cosapi::listFolder($bucketName, $dstPath);
        return $listRet;
    }
    
    
    /**
     * 前缀搜索
     * @author tanhaitao 2016-03-24
     */
    public function prefixSearchFile($dstPath) {
        if (!$dstPath)
            return false;
        $bucketName = self::$_bucketName;
        $ret = Cosapi::prefixSearch($bucketName, $dstPath );
        return $ret ;
    }
    
    
    
    
}
