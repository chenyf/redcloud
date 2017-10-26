<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Home\Controller;

use Common\Lib\EncryptTool;
use Common\Lib\FileToolkit;
use Common\Model\File\UploadFileModel;
use Common\Twig\Web\WebExtension;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;

use Common\Model\Util\DictFilterUtil;

class HelloController extends BaseController{

   public function extendTest(){
       
       $this->render("Test:subTpl");
   }

    public function hello(Request $request){
        echo C('PHP_BIN_PATH');
    }

    public function hello2(){
        $sync_cli_script = getParameter("sync.cloud.cli_script");
        $php_bin = C("PHP_BIN_PATH");
        echo "{$php_bin} {$sync_cli_script}";
    }

    public function play(){
        return $this->render('Test:player2');
    }

    public function stream(Request $request){
        $filename="2016818035842-4j9fm0.mp4";
        $location="D:/files/tlc/data/courselesson/21/2016818035842-4j9fm0.mp4";

        $mimeType = \Common\Lib\FileToolkit::getMimeTypeByExtension("mp4");

        if (!file_exists($location))
        {
            header ("HTTP/1.1 404 Not Found");
            return;
        }

        $size  = filesize($location);
        $time  = date('r', filemtime($location));

        $fm = @fopen($location, 'rb');
        if (!$fm)
        {
            header ("HTTP/1.1 505 Internal server error");
            return;
        }

        $begin  = 0;
        $end  = $size - 1;

        if (isset($_SERVER['HTTP_RANGE']))
        {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
            {
                $begin  = intval($matches[1]);
                if (!empty($matches[2]))
                {
                    $end  = intval($matches[2]);
                }
            }
        }
        if (isset($_SERVER['HTTP_RANGE']))
        {
            header('HTTP/1.1 206 Partial Content');
        }
        else
        {
            header('HTTP/1.1 200 OK');
        }

        header("Content-Type: $mimeType");
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length:' . (($end - $begin) + 1));
        if (isset($_SERVER['HTTP_RANGE']))
        {

            header("Content-Range: bytes $begin-$end/$size");
        }
        $type = "attachment";

        $agent = $request->headers->get('User-Agent');
        if(preg_match('/(MSIE)|(Trident)/',$agent)
            || preg_match('#Android.*Chrome/[.0-9]*#',$agent)
            || preg_match('#^Mozilla/5\.0$#',$agent)){
            header( 'Content-Disposition: ' . rawurlencode($type) . '; filename="' . rawurlencode( $filename ) . '"' );
        } else {
            header( 'Content-Disposition: ' . rawurlencode($type) . '; filename*=UTF-8\'\'' . rawurlencode( $filename )
                . '; filename="' . rawurlencode( $filename ) . '"' );
        }

        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: $time");

        $cur  = $begin;
        fseek($fm, $begin, 0);

        while(!feof($fm) && $cur <= $end && (connection_status() == 0))
        {
            print fread($fm, min(1024 * 16, ($end - $cur) + 1));
            $cur += 1024 * 16;
        }
    }

    public function course(){
        if($this->getCourseService()->resetUnitNumber(9)){
            echo "重置成功";
        }else{
            echo "重置失败";
        }
    }

    public function testAction(){
        $file = $this->getUploadFileService()->getFile(6);
        $this->getLocalFile()->convertFile($file);
    }

    public function getChapterDao() {
        return createService("Course.CourseChapter");
    }

    protected function getLocalFile()
    {
        return createService('File.LocalFile');
    }

    private function getUploadFileService()
    {
        return createService('File.UploadFileService');
    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }
   
}

class Test{

    public static $name = "";

    public static function hello(){
        echo "你好，" . self::$name . "\r\n";
    }

}
?>
