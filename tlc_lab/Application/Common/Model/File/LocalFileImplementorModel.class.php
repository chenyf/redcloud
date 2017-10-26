<?php

namespace Common\Model\File;

use Common\Model\Common\BaseModel;
use Common\Lib\FileToolkit;

class LocalFileImplementorModel extends BaseModel {

    public function getFile($file) {

        //现在云盘导入的文件都复制到了本地，所以统一从本地文件获取
        $file['fullpath'] = $this->getFileFullPath($file);
        $file['webpath'] = $this->getFileWebPath($file);

        //本地文件或者需要转格式的文档文件从本地文件（tlc）中获取
//        if($file['storage'] == 'local' || $file['ifConvert'] == 1) {
//            $file['fullpath'] = $this->getFileFullPath($file);
//            $file['webpath'] = $this->getFileWebPath($file);
//        }else if($file['storage'] == 'cloud'){
//            $file['fullpath'] = $this->getCloudFilePath($file);
//            $file['webpath'] = $this->getCloudWebPath($file);
//        }
        return $file;
    }

    public function addFile($targetType, $targetId, array $fileInfo = array(),  $originalFile = null) {

        $user = $this->getCurrentUser();

//        if(!$user->isLogin()){
//            doLog("未获取到用户登录信息");
//            throw $this->createServiceException("上传失败，未获取到用户登录信息。");
//        }

        if(!FileToolkit::validateIsFileType($originalFile)){
            doLog("上传失败，未获取到上传内容。");
            throw $this->createServiceException("上传失败，未获取到上传内容。");
        }

        $errors = FileToolkit::validateFileExtension($originalFile);
        if ($errors) {
            @unlink($originalFile->getRealPath());
            throw $this->createServiceException("该文件格式，不允许上传。");
        }

        $uploadFile = array();

        $uploadFile['storage'] = 'local';
        $uploadFile['targetId'] = $targetId;
        $uploadFile['targetType'] = $targetType;

        $uploadFile['filename'] = $originalFile->getClientOriginalName();

        $uploadFile['ext'] = FileToolkit::getFileExtension($originalFile);
        $uploadFile['ext'] = strtolower($uploadFile['ext']);

        $uploadFile['size'] = $originalFile->getSize();

        $filename = FileToolkit::generateFilename($uploadFile['ext']);

        $uploadFile['hashId'] = "{$uploadFile['targetType']}/{$uploadFile['targetId']}/{$filename}";

        $uploadFile['convertHash'] = "ch-{$uploadFile['hashId']}";

        $uploadFile['type'] = FileToolkit::getFileTypeByExtension($uploadFile['ext']);

        //只有doc和PPT才需要转码
        if (($uploadFile['type'] == 'document' and strtolower($uploadFile['ext']) != 'pdf') or $uploadFile['type'] == 'ppt'){
            $uploadFile['convertStatus'] = 'waiting';
            $uploadFile['ifConvert'] = 1;
        } else if (FileToolkit::isVideoNeedToBeConvert($uploadFile['ext'])){
            $uploadFile['convertStatus'] = 'waiting';
            $uploadFile['ifConvert'] = 1;
        } else {
            $uploadFile['convertStatus'] = 'success';
            $uploadFile['ifConvert'] = 0;
        }

        $uploadFile['isPublic'] = 1;
        $uploadFile['canDownload'] = 1;

        $uploadFile['createdUserId'] = $user->id;
        $uploadFile['updatedUserId'] = $user->id;
        $uploadFile['updatedTime'] = $uploadFile['createdTime'] = time();

        $targetPath = $this->getFilePath($targetType, $targetId, $uploadFile['isPublic']);

        $originalFile->move($targetPath, $filename);

        return $uploadFile;
    }

    public function saveConvertResult($file, array $result = array()) {
        
    }

    public function convertFile($file, $status, $result = null, $callback = null) {
        throw $this->createServiceException('本地文件暂不支持转换');
    }

    public function deleteFile($file, $deleteSubFile = true) {
        $filename = $this->getFileFullPath($file);
        @unlink($filename);
    }

    public function makeUploadParams($params) {
        $uploadParams = array();

        $uploadParams['storage'] = 'local';

        $token = $this->getUserService()->makeToken('fileupload', $params['user'], strtotime('+ 2 hours'));

        if($params['targetType'] == "resource"){    //上传课程资料
            $uploadParams['url'] = rtrim($params['resourceUploadUrl'],"/") . "/tokenId/" . $token["id"];
        }else{
            $uploadParams['url'] = rtrim($params['defaultUploadUrl'],"/") . "/tokenId/" . $token["id"];
        }
        $uploadParams['postParams'] = array();
        $uploadParams['postParams']['token'] = $token["token"];

//        session("upload_token_id",$token["id"]);

        return $uploadParams;
    }

    public function getMediaInfo($key, $mediaType) {
        
    }

    public function reconvertFile($file, $convertCallback, $pipeline = null) {
        
    }
    
    /**
     * 
     * @param type $file
     * @return string
     * @uathor ZhaoZuoWu 2015-03-26
     * @有bug,后面需要修改
     */
    private function getFileFullPath($file) {
        if (empty($file['isPublic'])) {
            $baseDirectory = getParameter('redcloud.disk.local_directory');
        } else {
            $baseDirectory = getParameter('redcloud.upload.public_directory');
        }
        return $baseDirectory . DIRECTORY_SEPARATOR . $file['hashId'];
    }

    /**
     * 获取云盘文件完整路径
     * @param $file
     */
    private function getCloudFilePath($file){
        $createdUser = $this->getUserService()->getUser($file['createdUserId']);
        if(empty($createdUser)){
            return null;
        }

        return pathjoin(getPanPathPrefix($createdUser['userNum']),$file['hashId']);
    }

    /**
     * @param $file
     * @return string
     * 获取云盘文件URL路径
     */
    private function getCloudWebPath($file){
        return "/Course/CourseLesson/documentCloudAction/id/" . $file['id'];
    }


    private function getFileWebPath($file) {
        if (empty($file['isPublic'])) {
            return '';
        }

        return getParameter('redcloud.upload.public_url_path') . '/' . $file['hashId'];
    }

    private function getFilePath($targetType, $targetId, $isPublic) {
        if ($isPublic) {
            $baseDirectory = getParameter('redcloud.upload.public_directory');
        } else {
            $baseDirectory = getParameter('redcloud.disk.local_directory');
        }
        return $baseDirectory . DIRECTORY_SEPARATOR . $targetType . DIRECTORY_SEPARATOR . $targetId;
    }

    private function getUserService() {
        return createService('User.UserService');
    }

}
