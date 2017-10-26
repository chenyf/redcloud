<?php
namespace Common\Model\File;

use Common\Model\Common\BaseModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

//use Topxia\Service\Common\BaseService;
//use Topxia\Service\File\FileImplementor;
use Common\Lib\FileToolkit;
use Common\Lib\ArrayToolkit;
use Common\Services\QueueService;

class LocalFileModel extends BaseModel {
	public function getFile($file) {
		$file['fullpath'] = $this->getFileFullPath($file);
		$file['webpath']  = $this->getFileWebPath($file);
		return $file;
	}

	public function addFile($targetType, $targetId, array $fileInfo = array(), UploadedFile $originalFile = null) {
		$errors = FileToolkit::validateFileExtension($originalFile);
		if ($errors) {
			@unlink($originalFile->getRealPath());
			E("该文件格式，不允许上传。");
		}

		$uploadFile = array();

		$uploadFile['storage']    = 'local';
		$uploadFile['targetId']   = $targetId;
		$uploadFile['targetType'] = $targetType;

		$uploadFile['filename'] = $originalFile->getClientOriginalName();

		$uploadFile['ext'] = FileToolkit::getFileExtension($originalFile);;
		$uploadFile['size'] = $originalFile->getSize();

		$filename = FileToolkit::generateFilename($uploadFile['ext']);

		$uploadFile['hashId'] = "{$uploadFile['targetType']}/{$uploadFile['targetId']}/{$filename}";

		$uploadFile['convertHash']   = "ch-{$uploadFile['hashId']}";
		$uploadFile['convertStatus'] = 'none';

		$uploadFile['type'] = FileToolkit::getFileTypeByExtension($uploadFile['ext']);

//		$uploadFile['isPublic']    = empty($fileInfo['isPublic']) ? 0 : 1;
//		$uploadFile['canDownload'] = empty($uploadFile['canDownload']) ? 0 : 1;
		$uploadFile['isPublic']    = 1;
		$uploadFile['canDownload'] = 1;

		$uploadFile['updatedUserId'] = $uploadFile['createdUserId'] = $this->getCurrentUser()->id;
		$uploadFile['updatedTime']   = $uploadFile['createdTime'] = time();

		$targetPath = $this->getFilePath($targetType, $targetId, $uploadFile['isPublic']);

		$originalFile->move($targetPath, $filename);

		return $uploadFile;
	}

	public function saveConvertResult($file, array $result = array()) {

	}

	/**
	 * 文件转码，将文件加入转码队列
	 * @param $file
	 * @return bool
	 * @author 王磊
	 */
	public function convertFile($file) {

		$convertType['document'] = array('doc', 'docx', 'ppt', 'pptx');

		$user = $this->getCurrentUser();

		//检测权限
		if ($file['createdUserId'] != $user['id'] || $file['convertStatus'] == 'success') {
			return false;
		}

		//pdf文件不需要转码
		if (strtolower($file['ext']) == 'pdf') {
//			$this->getUploadFile()->setFileConvert($file['id'], 'success');
			return true;
		}

		//修改文件状态
//		$this->getUploadFile()->setFileConvert($file['id'], 'waiting');
                
		//加入转码队列
		foreach ($convertType as $k => $v) {
			if (in_array($file['ext'], $v)) {
				$queueName = $k . 'Convert';
				QueueService::addJob(array(
					'jobName' => $queueName,
					'param'   => array('fileId' => $file['id']),
				));
			}
		}

	}

	public function convertVideo($file) {

		//需要转格式的视频文件后缀
		$convertType['video'] = array('avi', 'mov', 'flv', '3gp','rmvb', 'wmv');

		$user = $this->getCurrentUser();

		//检测权限
		if ($file['createdUserId'] != $user['id'] || $file['convertStatus'] == 'success') {
			return false;
		}

		$ok = ['mp4', 'ogg', 'webm'];
		//$ok中的文件不需要转码
		if (in_array(strtolower($file['ext']), $ok)) {
//			$this->getUploadFile()->setFileConvert($file['id'], 'success');
			return true;
		}

		//修改文件状态
//		$this->getUploadFile()->setFileConvert($file['id'], 'waiting');

		//加入转码队列
		foreach ($convertType as $k => $v) {
			if (in_array($file['ext'], $v)) {
				$queueName = $k . 'Convert';
				QueueService::addJob(array(
					'jobName' => $queueName,
					'param'   => array('fileId' => $file['id']),
				));
			}
		}
	}

	public function deleteFile($file, $deleteSubFile = true) {
		$filename = $this->getFileFullPath($file);
		@unlink($filename);
	}

	public function makeUploadParams($params) {
		$uploadParams = array();

		$uploadParams['storage']             = 'local';
		$uploadParams['url']                 = $params['defaultUploadUrl'];
		$uploadParams['postParams']          = array();
		$uploadParams['postParams']['token'] = $this->getUserService()->makeToken('fileupload', $params['user'], strtotime('+ 2 hours'))["token"];

		return $uploadParams;
	}

	public function getMediaInfo($key, $mediaType) {

	}

	public function reconvertFile($file, $convertCallback, $pipeline = null) {

	}

	private function getFileFullPath($file) {
		if (empty($file['isPublic'])) {
			$baseDirectory = getParameter('redcloud.disk.local_directory');
		} else {
			$baseDirectory = getParameter('redcloud.upload.public_directory');
		}

		return $baseDirectory . DIRECTORY_SEPARATOR . $file['hashId'];
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
		return $this->createService("User.UserService");
	}


	private function getUploadFile() {
		return $this->createService('File.UploadFileService');
	}

}
