<?php

namespace System\Controller;

use Common\Services\QcloudCosService;

class DemoController extends \Home\Controller\BaseController {
    
    
    function downfile($fileurl)
    {
     ob_start(); 
     $filename=$fileurl;
     $date=date("Ymd-H:i:m");
     header( "Content-type:  application/octet-stream "); 
     header( "Accept-Ranges:  bytes "); 
     header( "Content-Disposition:  attachment;  filename= {$date}.jpg"); 
     $size=readfile($filename); 
      header( "Accept-Length: " .$size);
    }
     

    public function indexAction() {
        $url="http://cloud-10011123.file.myqcloud.com/js/11111.jpg";
        $this ->downfile($url);
        
        $this->render("Demo:index", array(
        ));
    }
    
    public function uploadAction() {
        
        $this->render("Demo:upload", array(
        ));
    }

    public function createAction() {
        
       
      

//        $srcPath = '/home/ubuntu/cos/sdk/63MB_test.exe';
//        $dstPath = '/63MB_test.exe';
//        $srcPath = '/home/ubuntu/cos/sdk/php-sdk/cos-php-sdk/test.mp4';
//        $dstPath = '/test.mp4';
        $srcPath = $_FILES['file']['tmp_name'];
        $dstPath = "/php/".$_FILES['file']['name'];
        
        $QcloudCosService = new QcloudCosService();
        $resule = $QcloudCosService::uploadFileSlice($srcPath,$dstPath);
        
        var_dump($resule);die('sdd');
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
//        $delRet = Cosapi::delFolder($bucketName, "/public/file/mp4/");
//        var_dump($delRet);die("delFolder");
//        
        $delRet = Cosapi::del($bucketName, $dstPath);
        var_dump($delRet);die('del');
    
//        $result = Cosapi::stat($bucketName, $dstPath);
//        var_dump($result);die('1lll');
        Cosapi::setTimeout(10);
        
         #上传文件
        if($_FILES['error'] == 0 )
            $uploadRet = Cosapi::upload($srcPath, $bucketName, $dstPath);
        var_dump($uploadRet);die("444");

        //分片上传
        $sliceUploadRet = Cosapi::upload_slice(
                $srcPath, $bucketName, $dstPath);
var_dump($sliceUploadRet);die('123');
        //用户指定分片大小来分片上传
        $sliceUploadRet = Cosapi::upload_slice(
                $srcPath, $bucketName, $dstPath, null, 3*1024*1024);
        //指定了session，可以实现断点续传
        //$sliceUploadRet = Cosapi::upload_slice(
        //        $srcPath, $bucketName, $dstPath, null, 2000000, '48d44422-3188-4c6c-b122-6f780742f125+CpzDLtEHAA==');
        //var_dump($sliceUploadRet);

        //创建目录
        //$createFolderRet = Cosapi::createFolder($bucketName, "/test/");
        //var_dump($createFolderRet);

        //listFolder
        $listRet = Cosapi::listFolder($bucketName, "/");
        var_dump($listRet);

       

        //updateFolder
        //$updateRet = Cosapi::updateFolder($bucketName, '/test/', '{json:0}');
        //var_dump($updateRet);

        //update
        //$updateRet = Cosapi::update($bucketName, $dstPath, '{json:1}');
        //var_dump($updateRet);

        //statFolder
        //$statRet = Cosapi::statFolder($bucketName, "/test/");
        //var_dump($statRet);

        //stat
        //$statRet = Cosapi::stat($bucketName, $dstPath);
        //var_dump($statRet);

        //delFolder
        

        //del
        

    }

}