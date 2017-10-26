<?php
/**
 * 文件转码队列
 * @author 王磊 2015-06-24
 */
namespace Cli\Queue;
use Common\Lib\MailBat;
class ConvertFileQueue {

    private $switch = 0 ;
    
	/**
	 * 文档转码
	 * @param array $param
	 */
	public function documentConvert($param=array()) {
		$options = array(
			'fileId' =>0
		);
		$options = array_merge($options, $param);
		extract($options);
		//修改文件状态
		self::getUploadFile()->setFileConvert($fileId, 'doing');

		$file = self::getUploadFile()->getFile($fileId);

		if(!$file){
			self::throwError($fileId.' 不存在');
		}

		$filePath =  self::getFilePath($file);

		if (!is_writable($filePath) || !is_file($filePath)) {
			self::getUploadFile()->setFileConvert($fileId, 'error');
			self::throwError($filePath.' 不存在或者没有写入权限',$file);
		}

		if(!file_exists(C('UNOCONV_CMD'))){
			self::getUploadFile()->setFileConvert($fileId, 'error');
			self::throwError(C('UNOCONV_CMD').'找不到命令，请程序确认是否安装 ',$file);
		}

		if($file['storage'] == 'local'){
			$path_part = pathinfo($filePath);
			$fileNewName = $path_part['filename'].'.pdf';
			$newPath = $path_part['dirname'].'/'.$fileNewName;
			$shell  = C('UNOCONV_CMD').' -f pdf '.$filePath;
		}else{
			$fileBaseName = basename($file['hashId']);
			$fileNewName = mb_substr($fileBaseName,0,mb_strrpos($fileBaseName,$file['ext'])) . "pdf";
			$newPath = self::getLocalFileDir($file['targetType'],$file['targetId'],$file['isPublic']) . '/' . $fileNewName;
			$shell  = C('UNOCONV_CMD').' -f pdf -o '.$newPath.' '.$filePath;
		}

		@exec($shell,$err_info,$err_code);

		if(!file_exists($newPath)){
			self::getUploadFile()->setFileConvert($fileId, 'error');
			//@unlink($filePath);
			self::throwError($filePath.' 文件转换失败->错误信息： '.var_export($err_info,true).' -> 错误码：'.$err_code);
		}

		self::getUploadFileModel()->updateFile($fileId,array(
			'hashId' => $file['targetType'] . DIRECTORY_SEPARATOR . $file['targetId'] . DIRECTORY_SEPARATOR . $fileNewName,
			'filename' => $fileNewName,
			'ext' => 'pdf',
			'convertStatus' => 'success'
		));

		if($file['storage'] == 'local') {
			//删除原文件
			@unlink($filePath);
		}
	}


	private function sendMail($content,$file){
            $emailArr = C('SYSTEM_MANAGER');
            $email = implode(";", $emailArr);
            
            $arr = array();
            $site = createService('System.SettingService')->get('site');
            $course = self::getCourseService()->getCourse($file['targetId']);
            $lesson = self::getCourseLessonModel()->findLessons($file['targetId'],array('mediaId'=>$file['id']))[0];
            $arr['siteName'] = $site['name'];
            $arr['courseId'] = $file['targetId'];
            $arr['courseName'] = $course['title'];
            $arr['lessonId'] = $lesson['id'];
            $arr['lessonName'] = $lesson['title'];
            $arr['fileId'] = $file['id'];
            $arr['fileName'] = $file['filename'];
            $arr['fileSize'] = byte_format($file['size']);
            $arr['failTime'] = date("Y-m-d H:i:s",  time());
            $arr['failMsg'] = $content;
            
            $siteSpan = "<span>学校({$arr['webCode']})：{$arr['siteName']}</span><br/>";
            $courseSpan = "<span>课程名({$arr['courseId']})：{$arr['courseName']}</span><br/>";
            $lessonSpan = "<span>课程内容名({$arr['lessonId']})：{$arr['lessonName']}</span><br/>";
            $fileSpan = "<span>文件名({$arr['fileId']})：{$arr['fileName']} ({$arr['fileSize']})</span><br/>";
            $failSpan = "<span>失败时间：{$arr['failTime']}</span><br/><span>失败原因：{$arr['failMsg']}</span><br/>";
            $html = "<div>{$siteSpan}{$courseSpan}{$lessonSpan}{$fileSpan}{$failSpan}</div>";
            
            $param['subject'] = "文档转换格式失败";
            $param['html'] = $html;
            $param['to'] = $email;
            $mailBat = MailBat::getInstance();
            $xml = $mailBat->sendMailBySohu($param);
            //解析返回的xml
            $xmlArr = json_decode(json_encode((array) simplexml_load_string($xml)),true);
            $res = $xmlArr['message'];
	}

	private function recordLog($log) {
		self::getLogService()->error("Queue",'ConvertFileQueue.documentConvert',$log);
	}

	private function getFilePath($file) {
		if($file['storage'] == 'local'){
			return self::getLocalFileDir($file['targetType'],$file['targetId'],$file['isPublic']) . DIRECTORY_SEPARATOR .basename($file['hashId']);
		}else{
			$createdUser = createService('User.UserService')->getUser($file['createdUserId']);
			if(empty($createdUser)){
				return null;
			}

			return pathjoin(getPanPathPrefix($createdUser['userNum']),$file['hashId']);
		}
	}

	private function getLocalFileDir($targetType,$targetId,$isPublic){
		if ($isPublic) {
			$baseDirectory = getParameter('redcloud.upload.public_directory');
		} else {
			$baseDirectory = getParameter('redcloud.disk.local_directory');
		}
		return $baseDirectory . DIRECTORY_SEPARATOR . $targetType . DIRECTORY_SEPARATOR . $targetId;
	}

	private function throwError($error,$file=array()) {
		$msg  = 'ConvertFileQueue队列上传错误:' . $error;
		self::recordLog($msg);
                if(!empty($file) && C('CONVERT_FILE_FAIL')) self::sendMail($msg,$file);
		E($msg);
	}

	private function getLocalFile() {
		return createService('File.LocalFile');
	}

	private function getUploadFile() {
		$service  = createService('File.UploadFileService');
		return $service;
	}

	private function getUploadFileModel() {
		$dao = createService('File.UploadFile');
		return $dao;
	}
        
	private function getCourseService(){
		$service = createService('Course.CourseService');
		return $service;
	}

	private function getCourseLessonModel(){
		$dao = createService('Course.LessonModel');
		return $dao;
	}
        
	private function getLogService(){
		return createService('System.LogService');
	}
}