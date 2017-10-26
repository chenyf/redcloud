<?php

namespace Common\Model\File;

use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;
use Common\Lib\FileToolkit;

class UploadFileServiceModel extends BaseModel {

	static $implementor = array(
        'local' => 'File.LocalFileImplementorModel',
        'cloud' => 'File.CloudFileImplementorModel',
    );

    public function getFile($id) {
        $file = $this->getUploadFileDao()->getFile($id);

        if (empty($file)){
	        return null;
        }
        $file['mimeType'] = FileToolkit::getMimeTypeByExtension($file['ext']);
        return $this->getFileImplementorByFile($file)->getFile($file);
    }

    public function getFileByHashId($hashId) {
        $file = $this->getUploadFileDao()->getFileByHashId($hashId);
        if (empty($file))
            return null;
        return $this->getFileImplementorByFile($file)->getFile($file);
    }

    public function getFileByConvertHash($hash) {
        return $this->getUploadFileDao()->getFileByConvertHash($hash);
    }

    //根据ID获取文件类型
    public function getFileExtById($id){
        return $this->getUploadFileDao()->getFileExtById($id);
    }

    public function findFilesByIds(array $ids) {
        return $this->getUploadFileDao()->findFilesByIds($ids);
    }
    
    /**
     * 
     * @param type $conditions
     * @param type $sort
     * @param type $start
     * @param type $limit
     * @return type
     * @throws type
     * @author ZhaoZuoWu 2015-03-26
     */
    public function searchFiles($conditions, $sort, $start, $limit) {
        switch ($sort) {
            case 'latestUpdated':
                $orderBy = array('updatedTime', 'DESC');
                break;
            case 'oldestUpdated':
                $orderBy = array('updatedTime', 'ASC');
                break;
            case 'latestCreated':
                $orderBy = array('createdTime', 'DESC');
                break;
            case 'oldestCreated':
                $orderBy = array('createdTime', 'ASC');
                break;
            case 'extAsc':
                $orderBy = array('ext', 'ASC');
                break;
            case 'extDesc':
                $orderBy = array('ext', 'DESC');
                break;
            case 'nameAsc':
                $orderBy = array('filename', 'ASC');
                break;
            case 'nameDesc':
                $orderBy = array('filename', 'DESC');
                break;
            case 'sizeAsc':
                $orderBy = array('size', 'ASC');
                break;
            case 'sizeDesc':
                $orderBy = array('size',
                    'DESC'
                );
                break;
            default :
                throw $this->createServiceException('参数sort不正确。');
        }
        if (array_key_exists('source', $conditions) && $conditions['source'] == 'shared') {
            
            //Find all the users who is sharing with current user.
            $myFriends = $this->getUploadFileShareDao()->findMySharingContacts($conditions ['currentUserId']);
            if (isset($myFriends)) {
                $createdUserIds = "'" . implode("','", ArrayToolkit::column($myFriends, "sourceUserId")) . "'";
            } else {
                //Browsing shared files, but nobody is sharing with current user.
                return array();
            }
        } elseif (isset($conditions['currentUserId'])) {
            $createdUserIds = $conditions['currentUserId'];
        }

        if (isset($createdUserIds)) {
            $conditions['createdUserIds'] = $createdUserIds;
        }

        return $this->getUploadFileDao()->searchFiles($conditions, $orderBy, $start, $limit);
    }

    public function searchFileCount($conditions) {

        if (array_key_exists('source', $conditions) && $conditions['source'] == 'shared') {
            //Find all the users who is sharing with current user.
            $myFriends = $this->getUploadFileShareDao()->findMySharingContacts($conditions['currentUserId']);

            if (isset($myFriends)) {
                $createdUserIds = implode(",", ArrayToolkit::column($myFriends, "sourceUserId"));
            } else {
                //Browsing shared files, but nobody is sharing with current user.
                return 0;
            }
        } elseif (isset($conditions['currentUserId'])) {
            $createdUserIds = $conditions ['currentUserId'];
        }

        if (isset($createdUserIds)) {
            $conditions['createdUserIds'] = $createdUserIds;
        }

        return $this->getUploadFileDao()->searchFileCount($conditions);
    }

    //添加课程文件：文档、视频等课程内容
    public function addFile($targetType, $targetId, array $fileInfo = array(), $implemtor = 'local',  $originalFile = null) {
        $file = $this->getFileImplementor($implemtor)->addFile($targetType, $targetId, $fileInfo, $originalFile);

        $file = $this->getUploadFileDao()->addFile($file);

        //如果课程内容作为课程可下载的资料
//        if(!empty($file) && C('isCourseContentAsResource')){
//            $arr['courseId'] = $file['targetId'];
//            $arr['createUid'] = $file['createdUserId'];
//            $arr["updateUid"] = $file['createdUserId'];
//            $arr["updateTm"] = time();
//            $arr["createTm"] = time();
//            $arr["title"] = $file['filename'];
//            $arr["url"] = $file['hashId'];
//            $arr["ext"] = $file['ext'];
//            $arr["size"] = $file['size'];
//
//            $arr['isLessonContent'] = 1;//代表是课程内容
//            $r = $this->getCourseResourceService()->addResource($arr);
//        }

        return $file;
    }

    //上传课程资料到磁盘中;只返回文件句柄
    public function addResourceFile($targetType, $targetId, array $fileInfo = array(), $implemtor = 'local',  $originalFile = null)
    {
        $file = $this->getFileImplementor($implemtor)->addFile($targetType, $targetId, $fileInfo, $originalFile);
        return $file;
    }

    //添加课程信息到资源数据库中
    public function saveResourceToDb($uploadFileId,$resourceTitle=null){
        $file = $this->getUploadFileDao()->getFile($uploadFileId);
        if(!empty($file) && C('isCourseContentAsResource')){
            $resource = $this->getCourseResourceDao()->courseResource(array('uploadFileId' => $file['id']));
            //已经保存过该资源了
            if(!empty($resource)){
                return 1;
            }
            $resourceTitle = $resourceTitle == null ? $file['filename'] :$resourceTitle;
            //如果课程内容作为课程可下载的资料
            $arr['courseId'] = $file['targetId'];
            $arr['createUid'] = $file['createdUserId'];
            $arr["updateUid"] = $file['createdUserId'];
            $arr["updateTm"] = time();
            $arr["createTm"] = time();
            $arr["title"] = $resourceTitle;
            $arr["url"] = $file['hashId'];
            $arr["ext"] = $file['ext'];
            $arr["size"] = $file['size'];
            $arr["storage"] = $file['storage'];
            $arr["uploadFileId"] = $file['id'];

            $arr['isLessonContent'] = 1;//代表是课程内容
            $r = $this->getCourseResourceService()->addResource($arr);
            return $r;
        }

        return 0;
    }

    //从课程资料中删除该资料
    public function deleteResourceFromDb($id){
        $this->getCourseResourceService()->removeResource($id);
    }

    private function getCourseResourceService(){
        return createService('Course.CourseResourceService');
    }

    private function getCourseResourceDao(){
        return $this->createDao("Course.CourseResource");
    }

    public function renameFile($id, $newFilename) {
        $this->getUploadFileDao()->updateFile($id, array('filename' => $newFilename));
        return $this->getFile($id);
    }

    //删除课程内容上传的文件，包括数据库和磁盘文件，并删除和upload_files同步的课程资料resource中的对应记录
    public function deleteFile($id)
    {
        $file = $this->getUploadFileDao()->getFile($id);
        if (empty($file)) {
            throw $this->createServiceException("文件(#{$id})不存在，删除失败");
        }

        $deleteSubFile = true;
        if (!empty($file['etag'])) {
            $etagCount = $this->getUploadFileDao()->findFilesCountByEtag($file['etag']);
            if ($etagCount > 1) {
                $deleteSubFile = false;
            }
        }

        if (!empty($file['convertParams']['convertor']) && $file['convertParams']['convertor'] == 'HLSEncryptedVideo') {
            $deleteSubFile = true;
        }

        if (!empty($file['convertParams']['convertor']) && $file['convertParams']['convertor'] == 'ppt') {
            $deleteSubFile = true;
        }

        //本地文件从磁盘中删除
        if ($file['storage'] == 'local') {
            $this->getFileImplementorByFile($file)->deleteFile($file, $deleteSubFile);
        }

        //删除对应upload_files的resource
        $this->getCourseResourceDao()->deleteResource(array(
            'uploadFileId' => $file['id'],
            'isLessonContent' => 1,
        ));

        //数据库中删除upload_files
        return $this->getUploadFileDao()->deleteFile($id);
    }

    public function deleteFiles(array $ids) {
        foreach ($ids as $id) {
            $this->deleteFile($id);
        }
    }

    public function saveConvertResult($id, array $result = array()) {
        $file = $this->getFile($id);
        if (empty($file)) {
            throw $this->createServiceException("文件(#{$id})不存在，转换失败");
        }

        $file = $this->getFileImplementorByFile($file)->saveConvertResult($file, $result);

        $this->getUploadFileDao()->updateFile($id, array(
            'convertStatus' => $file['convertStatus'],
            'metas2' => json_encode($file['metas2']),
            'updatedTime' => time(),
        ));

        return $this->getFile($id);
    }

    public function saveConvertResult3($id, array $result = array()) {
        $file = $this->getFile($id);
        if (empty($file)) {
            throw $this->createServiceException("文件(#{$id})不存在，转换失败");
        }
        $file['convertParams']['convertor'] = 'HLSEncryptedVideo';

        $fileNeedUpdateFields = array();

        $file = $this->getFileImplementorByFile($file)->saveConvertResult($file, $result);

        if ($file['convertStatus'] == 'success') {
            $fileNeedUpdateFields['convertParams'] = json_encode($file['convertParams']);
            $fileNeedUpdateFields['metas2'] = json_encode($file['metas2']);
            $fileNeedUpdateFields['updatedTime'] = time();
            $this->getUploadFileDao()->updateFile($id, $fileNeedUpdateFields);
        }

        return $this->getFile($id);
    }

    public function convertFile($id, $status, array $result = array(), $callback = null) {
        $statuses = array('none', 'waiting', 'doing', 'success', 'error');
        if (!in_array($status, $statuses)) {
            throw $this->createServiceException('状态不正确，变更文件转换状态失败！');
        }

        $file = $this->getFile($id);
        if (empty($file)) {
            throw $this->createServiceException("文件(#{$id})不存在，转换失败");
        }

        $file = $this->getFileImplementorByFile($file)->convertFile($file, $status, $result, $callback);

        $this->getUploadFileDao()->updateFile($id, array(
            'convertStatus' => $file['convertStatus'],
            'metas2' => $file['metas2'],
            'updatedTime' => time(),
        ));

        return $this->getFile($id);
    }

    public function setFileConverting($id, $convertHash) {
        $file = $this->getFile($id);
        if (empty($file)) {
            throw $this->createServiceException('file not exist.');
        }

        // $status = $file['convertStatus'] == 'success' ? 'success' : 'waiting';

        $fields = array(
            'convertStatus' => 'waiting',
            'convertHash' => $convertHash,
            'updatedTime' => time(),
        );
        $this->getUploadFileDao()->updateFile($id, $fields);

        return $this->getFile($id);
    }

	public function setFileConvert($id, $status) {
		$file = $this->getFile($id);
		if (empty($file)) {
			throw $this->createServiceException('file not exist.');
		}

		$fields = array(
			'convertStatus' => $status,
			'updatedTime' => time(),
		);
		$this->getUploadFileDao()->updateFile($id, $fields);

		return $this->getFile($id);
	}

    public function makeUploadParams($params) {
        return $this->getFileImplementor($params['storage'])->makeUploadParams($params);
    }

    public function reconvertFile($id, $convertCallback) {
        $file = $this->getFile($id);
        if (empty($file)) {
            throw $this->createServiceException('file not exist.');
        }
        $convertHash = $this->getFileImplementorByFile($file)->reconvertFile($file, $convertCallback);

        $this->setFileConverting($file['id'], $convertHash);

        return $convertHash;
    }

    public function reconvertOldFile($id, $convertCallback, $pipeline) {
        $result = array();

        $file = $this->getFile($id);
        if (empty($file)) {
            return array('error' => 'file_not_found', 'message' => "文件(#{$id})不存在");
        }

        if ($file['storage'] != 'cloud') {
            return array('error' => 'not_cloud_file', 'message' => "文件(#{$id})，不是云文件。");
        }

        if ($file['type'] != 'video') {
            return array('error' => 'not_video_file', 'message' => "文件(#{$id})，不是视频文件。");
        }

        if ($file['targetType'] != 'courselesson') {
            return array('error' => 'not_course_file', 'message' => "文件(#{$id})，不是课程内容文件。");
        }

        $target = createService('Course.CourseService')->getCourse($file['targetId']);
        if (empty($target)) {
            return array('error' => 'course_not_exist', 'message' => "文件(#{$id})所属的课程已删除。");
        }

        if (!empty($file['convertParams']['convertor']) && $file['convertParams']['convertor'] == 'HLSEncryptedVideo') {
            return array('error' => 'already_converted', 'message' => "文件(#{$id})已转换");
        }

        $fileNeedUpdateFields = array();

        if (!empty($file['convertParams']['convertor']) && $file['convertParams']['convertor'] == 'HLSVideo') {
            $file['convertParams']['hlsKeyUrl'] = 'http://hlskey.redcloud.net/placeholder';
            $file['convertParams']['hlsKey'] = $this->generateKey(16);
            if ($file['convertParams']['videoQuality'] == 'low') {
                $file['convertParams']['videoQuality'] = 'normal';
                $file['convertParams']['video'] = array('440k', '640k', '1000K');
            }

            $fileNeedUpdateFields['convertParams'] = json_encode($file['convertParams']);
            $file['convertParams']['convertor'] = 'HLSEncryptedVideo';
        }

        if (empty($file['convertParams'])) {
            $convertParams = array(
                'convertor' => 'HLSEncryptedVideo',
                'segtime' => 10,
                'videoQuality' => 'normal',
                'audioQuality' => 'normal',
                'video' => array('440k', '640k', '1000K'),
                'audio' => array('48k', '64k', '96k'),
                'hlsKeyUrl' => 'http://hlskey.redcloud.net/placeholder',
                'hlsKey' => $this->generateKey(16),
            );

            $file['convertParams'] = $convertParams;

            $convertParams['convertor'] = 'HLSVideo';
            $fileNeedUpdateFields['convertParams'] = json_encode($convertParams);
        }

        $convertHash = $this->getFileImplementorByFile($file)->reconvertOldFile($file, $convertCallback, $pipeline);
        if (empty($convertHash)) {
            return array('error' => 'convert_request_failed', 'message' => "文件(#{$id})转换请求失败！");
        }

        $fileNeedUpdateFields['convertHash'] = $convertHash;
        $fileNeedUpdateFields['updatedTime'] = time();

        $this->getUploadFileDao()->updateFile($file['id'], $fileNeedUpdateFields);


        $subTarget = createService('Course.CourseService')->findLessonsByTypeAndMediaId('video', $file['id']) ? : array();
        if (!empty($subTarget)) {
            $subTarget = $subTarget[0];
        }

        return array(
            'convertHash' => $convertHash,
            'courseId' => empty($subTarget['courseId']) ? $target['targetId'] : $subTarget['courseId'],
            'lessonId' => empty($subTarget['id']) ? 0 : $subTarget['id'],
        );
    }

    public function getMediaInfo($key, $type) {
        return $this->getFileImplementor('cloud')->getMediaInfo($key, $type);
    }

    public function getFileByTargetType($targetType) {
        return $this->getUploadFileDao()->getFileByTargetType($targetType);
    }

    public function findMySharingContacts($targetUserId) {
        $userIds = $this->getUploadFileShareDao()->findMySharingContacts($targetUserId);

        if (!empty($userIds)) {
            return $this->getUserService()->findUsersByIds(ArrayToolkit::column($userIds, 'sourceUserId'));
        } else {
            return null;
        }
    }

    public function findShareHistory($sourceUserId) {
        $shareHistories = $this->getUploadFileShareDao()->findShareHistoryByUserId($sourceUserId);

        return $shareHistories;
    }

    public function shareFiles($sourceUserId, $targetUserIds) {
        foreach ($targetUserIds as $targetUserId) {
            //Ignore sharing request if the sourceUserId equasls to targetUserId
            if ($targetUserId != $sourceUserId) {
                $shareHistory = $this->getUploadFileShareDao()->findShareHistory($sourceUserId, $targetUserId);

                if (isset($shareHistory)) {
                    //File sharing record exists, update the existing record
                    $fileShareFields = array(
                        'isActive' => 1,
                        'updatedTime' => time()
                    );

                    $this->getUploadFileShareDao()->updateShare($shareHistory['id'], $fileShareFields);
                } else {
                    //Add new file sharing record
                    $fileShareFields = array(
                        'sourceUserId' => $sourceUserId,
                        'targetUserId' => $targetUserId,
                        'isActive' => 1,
                        'createdTime' => time(),
                        'updatedTime' => time()
                    );

                    $this->getUploadFileShareDao()->addShare($fileShareFields);
                }
            }
        }
    }

    public function cancelShareFile($sourceUserId, $targetUserId) {
        $shareHistory = $this->getUploadFileShareDao()->findShareHistory($sourceUserId, $targetUserId);

        if (!empty($shareHistory)) {
            $fileShareFields = array(
                'isActive' => 0,
                'updatedTime' => time()
            );

            $this->getUploadFileShareDao()->updateShare($shareHistory ['id'], $fileShareFields);
        }
    }

    public function increaseFileUsedCount($fileIds) {
        $this->getUploadFileDao()->updateFileUsedCount($fileIds, 1);
    }

    public function decreaseFileUsedCount($fileIds) {
        $this->getUploadFileDao()->updateFileUsedCount($fileIds, -1);
    }

    private function generateKey($length = 0) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $key = '';
        for ($i = 0; $i < 16; $i++) {
            $key .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $key;
    }

    private function getFileImplementorByFile($file) {
        return $this->getFileImplementor('local');
//        return $this->getFileImplementor($file['storage']);
    }
    
    /**
     * 
     * @return obj
     * @author ZhaoZuoWu 2015-03-06
     */
    private function getUploadFileDao() {
        return $this->createService('File.UploadFileModel');
    }
    
    /**
     * @author ZhaoZuoWu 2015-03-26
     * @return type
     */
    private function getUploadFileShareDao() {
        return $this->createService('File.UploadFileShareModel');
    }

    private function getUserService() {
        return $this->createService('User.UserServiceModel');
    }
    
    /**
     *
     * @param string $key
     * @return array
     * @author ZhaoZuoWu 2015-03-26
     */
    private function getFileImplementor($key) {
        return $this->createService(self::$implementor["local"]);
//        if (!array_key_exists($key, self::$implementor)) {
//            throw $this->createServiceException(sprintf("`%s` File Implementor is not allowed.", $key));
//        }
//        return $this->createService(self::$implementor[$key]);
    }

    private function getLogService() {
        return $this->createService("System.LogService");
    }

}
