<?php
/**
 * Created by PhpStorm.
 * User: sike
 * Date: 2016/7/29
 * Time: 13:41
 */

namespace Home\Controller;


class DownloadController extends BaseController
{
    //下载文件
    public function downloadAction(Request $request){
        $type = $request->query->get("type");
        $id = $request->query->get("id");

        if(empty($type) || empty($id)){
            return $this->createJsonResponse(array('exit'=>false,'message'=>'参数有误'));
        }
        $accessPath = "";

        if($type == 'resource'){
            $recource = $this->getCourseResourceService()->courseResource(array('id'=>$id));
            $accessPath = empty($recource) ? "" : $recource['accessPath'];
        }

        if(!is_file($accessPath)){
            return $this->createJsonResponse(array('exit'=>false,'message'=>'文件不存在'));
        }
        //打开文件
        $file = fopen ( $accessPath, "r" );
        //输入文件标签
        Header ( "Content-type: application/octet-stream" );
        Header ( "Accept-Ranges: bytes" );
        Header ( "Accept-Length: " . filesize ( $accessPath ) );
        $file_name = basename($accessPath);
        Header ( "Content-Disposition: attachment; filename=" . $file_name );
        //输出文件内容
        //读取文件内容并直接输出到浏览器
        echo fread ( $file, filesize ( $accessPath ) );
        fclose ( $file );
        exit ();
    }

    private function getFilePath($url, $isPublic=true) {
        if ($isPublic) {
            $baseDirectory = getParameter('redcloud.upload.public_directory');
        } else {
            $baseDirectory = getParameter('redcloud.disk.local_directory');
        }
        $url = ltrim($url,DIRECTORY_SEPARATOR);
        return $baseDirectory . DIRECTORY_SEPARATOR . $url;
    }

    private function getCourseResourceService(){
        return createService('Course.CourseResourceService');
    }

    protected function getCourseService(){
        return createService('Course.CourseService');
    }
}