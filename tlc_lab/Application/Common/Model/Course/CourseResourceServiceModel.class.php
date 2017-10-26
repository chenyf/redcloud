<?php
namespace Common\Model\Course;
use Common\Lib\FileToolkit;
use Common\Model\Common\BaseModel;
use Common\Services\QueueService;
class CourseResourceServiceModel extends BaseModel{

    private function getCourseResourceDao(){
        return $this->createDao("Course.CourseResource");
    }

    private function getUserService() {
        return createService('User.UserService');
    }

    private function getNotifiactionService() {
        return createService('User.NotificationService');
    }
    
    private function filterWhere($paramArr){
        $where = array();
        if(array_key_exists('courseId',$paramArr)) {
            $where["courseId"] = $paramArr["courseId"];
        }
        if(array_key_exists('id',$paramArr)) {
            $where["id"] = $paramArr["id"];
        }
        if(array_key_exists('uploadFileId',$paramArr)) {
            $where["uploadFileId"] = $paramArr["uploadFileId"];
        }
        return $where;
    }

    public function findResource($paramArr){
        $paramArr = $this->filterWhere($paramArr);
        $resource = $this->getCourseResourceDao()->courseResource($paramArr);
        return $resource;
    }
    
    public function courseResourceCount($paramArr){
        return $this->getCourseResourceDao()->courseResourceCount($paramArr);
    }
    
    public function courseResourceList($paramArr){
        $resource =  $this->getCourseResourceDao()->courseResourceList($paramArr);
        foreach ($resource as $key => $value){
            $resource[$key]['async'] = $this->isAsyncCloudFile($value['createUid'],$value['isSyncCloud'],$value['syncStatus'],$value['syncCloudPath']) ? 1 : 0;
        }

        return $resource;
    }

    //isResource是否是正常的课程资料
    public function courseResource($paramArr,$isRecource = true){
        $paramArr = $this->filterWhere($paramArr);
        $resource = $this->getCourseResourceDao()->courseResource($paramArr);

        if(!empty($resource)){
//            if($resource["storage"] == 'cloud'){
//                $resource['accessPath'] = $this->getCloudFileAbsolutePath($resource['createUid'],$resource['url']);
//            }else{
//                $resource['accessPath'] = $this->getResourceAccessPath($resource['url']);
//            }
            $resource['accessPath'] = $this->getResourceAccessPath($resource['url']);
        }

        return $resource;
    }

    //根据资源URL获取资源路径
    public function getResourceAccessPath($url){
        return $this->getFilePath($url,true);
    }

    //根据数据库中云盘路径获取云盘文件绝对路径
    public function getCloudFileAbsolutePath($userId,$path){
        $createdUser = $this->getUserService()->getUser($userId);
        if(empty($createdUser)){
            return null;
        }

        return pathjoin(getPanPathPrefix($createdUser['userNum']),$path);
    }

    //判断云盘中是否存在该资料文件
    public function isAsyncCloudFile($userId,$isAsyncCloud,$asyncStatus,$asyncCloudPath){
        $filePath = $this->getCloudFileAbsolutePath($userId,$asyncCloudPath);
        if($isAsyncCloud == 0 || $asyncCloudPath == "" || !is_file($filePath)){
            return false;
        }else{
            return true;
        }
    }

    private function getFilePath($url, $isPublic=true) {
        $url = rtrim($url,DIRECTORY_SEPARATOR);
        if ($isPublic) {
            $baseDirectory = getParameter('redcloud.upload.public_directory');
        } else {
            $baseDirectory = getParameter('redcloud.disk.local_directory');
        }
        return $baseDirectory . DIRECTORY_SEPARATOR . $url;
    }

    public function getCourseResourcePath($resourceId){
        $resource = $this->getCourseResourceDao()->findResource($resourceId);
        $path = $this->getFilePath($resource['url']);
        $path = str_replace(basename($resource['url']),$resource['title'],$path) . "." . $resource['ext'];

        return $path;
    }

    //队列同步资料文件到云盘中
    public function syncResource($resourceId){
        $resource = $this->getCourseResourceDao()->findResource($resourceId);
        if(empty($resource)){
            return false;
        }

        $user = $this->getUserService()->getUser($resource["createUid"]);
        if(empty($user)){
            return false;
        }

        $path = $this->getFilePath($resource['url']);

        $resource['title'] = str_replace(" ","",$resource['title']);
//        $path = str_replace(basename($resource['url']),$resource['title'],$path) . "." . $resource['ext'];

        $name = str_replace(array(" ","/","\\","."),'_',$resource['title']) . "." . $resource['ext'];
        QueueService::addJob(array(
            'jobName' => 'syncFileToCloud',
            'param'   => array('id'=>$resourceId,'userNum' => $user['userNum'],"path" => $path,'name'=>$name),
        ));

        $this->changeResourceSyncStatus($resourceId,'waiting',"");
        $this->getCourseResourceDao()->saveResource(array("id" => $resourceId),array('syncStartTime' => time()));
        return true;
    }

    //改变resource表中同步的状态
    public function changeResourceSyncStatus($id,$status,$path){
        $fields['syncStatus'] = $status;
        $fields['syncCloudPath'] = $path;
        $fields['syncFinishTime'] = time();
        $resource = $this->getCourseResourceDao()->findResource($id);

        $resource_manage_url = '/Course/CourseResource/indexAction/id/' . $resource['courseId'];
//        $resource_manage_url = U('course_manage',array('id' => $resource['courseId']));
        $redisk_url = PAN_URL;

        if($fields['syncStatus'] == 'success'){
            $fields['isSyncCloud'] = 1;
            $this->getNotifiactionService()->notify($resource["createUid"], 'default', "恭喜！您同步到云盘的资料文件{$resource['title']}同步成功！ <a href='{$resource_manage_url}'>查看资料列表</a> 或者 <a href='{$redisk_url}'>进入网盘查看</a> ");
        }else{
            $fields['isSyncCloud'] = 0;
            if($fields['syncStatus'] == 'failure'){
                $this->getNotifiactionService()->notify($resource["createUid"], 'default', "抱歉！您同步到云盘的资料文件{$resource['title']}同步失败￣へ￣ <a href='{$resource_manage_url}'>查看资料列表</a>");
            }
        }
        $this->getCourseResourceDao()->saveResource(array("id" => $id),$fields);
    }
    
    //删除资料
    public function deleteResource($paramArr){
        $paramArr = $this->filterWhere($paramArr);
        return $this->getCourseResourceDao()->deleteResource($paramArr);
    }

    public function removeResource($resourceId){
        return $this->getCourseResourceDao()->deleteResource(array("id" => $resourceId));
    }
    
    public function updateResourceDownloadNum($paramArr){
        $paramArr = $this->filterWhere($paramArr);
        return $this->getCourseResourceDao()->updateResourceDownloadNum($paramArr);
    }
    
    public function getResourceDownloadNum($paramArr){
        $paramArr = $this->filterWhere($paramArr);
        return $this->getCourseResourceDao()->getResourceDownloadNum($paramArr);
    }
    
    public function addResource($arr){
        if(empty($arr['createUid']) || empty($arr['updateUid'])) {
            $user = $this->getCurrentUser();
            $arr['updateUid'] = $user['id'];
            $arr['createUid'] = $user['id'];
        }
        return $this->getCourseResourceDao()->addResource($arr);
    }
    
    public function saveResource($paramArr,$arr){
        $paramArr = $this->filterWhere($paramArr);
        return $this->getCourseResourceDao()->saveResource($paramArr,$arr);
    }
    
    public function exchangeResource($arr){
        return $this->table("resource_exchange")->add($arr);
    }
    
    public function isExchangeResource($paramArr){
        return $this->table("resource_exchange")->where($paramArr)->find() ? : array();
    }
    public function exchangeResourceRecord($paramArr){
        $where = array();
        $where["uid"] = $paramArr["uid"];
        $where["courseId"] = $paramArr["courseId"];
        $records = $this->table("resource_exchange")->where($where)->field("resourceId")->select() ? : array();
        $resourceIds = array();
        foreach ($records as $key => $value) {
            $resourceIds[$key] = $value["resourceId"];
        }
        return array_values($resourceIds);
    }
  

}
?>