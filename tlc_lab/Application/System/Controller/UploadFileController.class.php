<?php
namespace System\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Topxia\Service\Util\CloudClientFactory;
use Common\Lib\StringToolkit;
use Common\Lib\FileToolkit;
use Common\Model\User\CurrentUserModel;

class UploadFileController extends \Home\Controller\BaseController
{
    public function uploadAction(Request $request,$tokenId)
    {
//        $tokenId = session("upload_token_id");

        $token = $this->getUserService()->getToken('fileupload', $tokenId);
//        session("upload_token_id",null);

        if (empty($token)) {
            throw $this->createAccessDeniedException('上传TOKEN已过期或不存在。');
        }

        $user = $this->getUserService()->getUser($token['userId']);
        if (empty($user)) {
            throw $this->createAccessDeniedException('上传TOKEN非法。');
        }

        $targetType = $request->query->get('targetType');
        $targetId = $request->query->get('targetId');

        $originalFile = $this->get('request')->files->get('file');
        $originalFile = $originalFile ?: $this->get('request')->files->get('Filedata');

        $file = $this->getCourseService()->uploadCourseFile($targetType, $targetId, array(), 'local', $originalFile);

        //需要转码的创建转码队列进行转码
        if($file['convertStatus'] == 'waiting'){
            $this->getLocalFile()->convertFile($file);
            $this->getLocalFile()->convertVideo($file);
            
        }
    	return $this->createJsonResponse($file);
    }

    //资料上传的Action
    public function resourceUploadAction(Request $request,$tokenId){

//        $tokenId = session("upload_token_id");

        $token = $this->getUserService()->getToken('fileupload', $tokenId);
//        session("upload_token_id",null);

        if (empty($token)) {
            throw $this->createAccessDeniedException('上传TOKEN已过期或不存在。');
        }

        $user = $this->getUserService()->getUser($token['userId']);
        if (empty($user)) {
            throw $this->createAccessDeniedException('上传TOKEN非法。');
        }

        $currentUser = new CurrentUserModel();
        $this->setCurrentUser($currentUser->fromArray($user));

        $targetType = $request->query->get('targetType');
        $targetId = $request->query->get('targetId');

        $originalFile = $this->get('request')->files->get('file');
        $originalFile = $originalFile ?: $this->get('request')->files->get('Filedata');

        $file = $this->getCourseService()->uploadCourseResource($targetType, $targetId, array(), 'local', $originalFile);
        $file['url'] = $file['hashId'];
        unset($file['hashId']);
        unset($file['convertHash']);
        doLog(json_encode($file));
        return $this->createJsonResponse($file);
    }
    
    /**
     *文档 重新转码
     * @author fubaosheng 2015-09-16
     */
    public function documentConvertAction(Request $request){
        $conditions = $request->query->all();
        $fileid = $conditions['fileid'] ? : 0;
        if(empty($fileid)){
            return $this->createJsonResponse(array('status'=>false,'info'=>'文件ID为空！'));
        }
        $file = $this->getUploadFileService()->getFile($fileid);
        if(empty($file)){
            return $this->createJsonResponse(array('status'=>false,'info'=>"文件ID为{$fileid}的不存在！"));
        }
        $user = $this->getCurrentUser();
        if($user['id'] != $file['createdUserId']){
            return $this->createJsonResponse(array('status'=>false,'info'=>"非文件创建人！"));
        }
        if(strtolower($file['ext']) == 'pdf'){
            return $this->createJsonResponse(array('status'=>false,'info'=>"文件已经是pdf格式！"));
        }
        if($file['convertStatus'] != 'error'){
            return $this->createJsonResponse(array('status'=>false,'info'=>"文件转换状态不正确！"));
        }
        $this->getLocalFile()->convertFile($file);
        sleep(1);
        $file = $this->getUploadFileService()->getFile($fileid);
        return $this->createJsonResponse(array('status'=>true,'info'=>$file['convertStatus']));
    }

    public function browserAction(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user->isTeacher() && !$user->isAdmin()) {
            throw $this->createAccessDeniedException('您无权查看此页面！');
        }

        $conditions = $request->query->all();
        unset($conditions['center']);
        $materialLibApp = $this->getAppService()->findInstallApp('MaterialLib');

        if(!empty($materialLibApp)){
            $conditions['currentUserId'] = $user['id'];
        }
        
        $files = $this->getUploadFileService()->searchFiles($conditions, 'latestUpdated', 0, 10000);
        
        return $this->createFilesJsonResponse($files);
    }

     public function browsersAction(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user->isTeacher() && !$user->isAdmin()) {
            throw $this->createAccessDeniedException('您无权查看此页面！');
        }

        $conditions = $request->query->all();
        unset($conditions['center']);
        $files = $this->getUploadFileService()->searchFiles($conditions, 'latestUpdated', 0, 10000);
        
        return $this->createFilesJsonResponse($files);
    }
    
    public function paramsAction(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            throw $this->createAccessDeniedException();
        }

        $params = $request->query->all();

        $params['user'] = $user->id;
        $params['defaultUploadUrl'] = $this->generateUrl('uploadfile_upload', array('targetType' => $params['targetType'], 'targetId' => $params['targetId'] ?: '0' ));

        $params['resourceUploadUrl'] = $this->generateUrl('resource_upload',array('targetType' => $params['targetType'], 'targetId' => $params['targetId'] ?: '0' ));

        //现在存储都使用本地的
        $params['storage'] = 'local';

        if (empty($params['lazyConvert'])) {
            $params['convertCallback'] = $this->generateUrl('uploadfile_cloud_convert_callback2', array(), true);
        } else {
            $params['convertCallback'] = null;
        }

        $params = $this->getUploadFileService()->makeUploadParams($params);
        return $this->createJsonResponse($params);
    }

    private function cloudCallBack(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            throw $this->createAccessDeniedException();
        }

        $fileInfo = $request->request->all();
      
        $targetType = $request->query->get('targetType');
        $targetId = $request->query->get('targetId');
        $lazyConvert = $request->query->get('lazyConvert') ? true : false;
        $fileInfo['lazyConvert'] = $lazyConvert;

        if($targetType == 'headLeader'){
            $storage = $this->getSettingService()->get('storage');
            unset($storage['headLeader']);
            $this->getSettingService()->set('storage', $storage);

            $file = $this->getUploadFileService()->getFileByTargetType($targetType);
            if(!empty($file) && array_key_exists('id', $file)){
                $this->getUploadFileService()->deleteFile($file['id']);
            }
        }

        $file = $this->getUploadFileService()->addFile($targetType, $targetId, $fileInfo, 'cloud');

        if ($lazyConvert && $file['type']!="document") {
        
            $convertHash = $this->getUploadFileService()->reconvertFile(
                $file['id'],
                $this->generateUrl('uploadfile_cloud_convert_callback2', array(), true)
            );
        }

        return $file;
    }

    public function cloudCallbackAction(Request $request)
    {
        $file = $this->cloudCallBack($request);
        return $this->createJsonResponse($file);
    }

    public function cloudConvertCallback2Action(Request $request)
    {
        $file = $this->cloudConvertCallback2($request);    

        return $this->createJsonResponse($file['metas2']);
    }

    private function cloudConvertCallback2(Request $request)
    {
        $result = $request->getContent();
        $result = preg_replace_callback(
          "(\\\\x([0-9a-f]{2}))i",
          function($a) {return chr(hexdec($a[1]));},
          $result
        );

        $this->getLogService()->info('uploadfile', 'cloud_convert_callback', "文件云处理回调", array('result' => $result));
        $result = json_decode($result, true);
        $result = array_merge($request->query->all(), $result);
        if (empty($result['id'])) {
            throw new \RuntimeException('数据中id不能为空');
        }

        if (!empty($result['convertHash'])) {
            $file = $this->getUploadFileService()->getFileByConvertHash($result['convertHash']);
        } else {
            $file = $this->getUploadFileService()->getFileByConvertHash($result['id']);
            if ($file && $file['type'] == 'ppt') {
                $result['nextConvertCallbackUrl'] = $this->generateUrl('uploadfile_cloud_convert_callback2', array('convertHash' => $result['id']), true);
            }
        }

        if (empty($file)) {
            throw new \RuntimeException('文件不存在');
        }
        $file = $this->getUploadFileService()->saveConvertResult($file['id'], $result);

        if (in_array($file['convertStatus'], array('success', 'error'))) {
            $this->getNotificationService()->notify($file['createdUserId'], 'cloud-file-converted', array(
                'file' => $file,
            ));
        }
        return $file;
    }

    public function cloudConvertCallback3Action(Request $request)
    {
        $result = $request->getContent();

        $result = preg_replace_callback(
          "(\\\\x([0-9a-f]{2}))i",
          function($a) {return chr(hexdec($a[1]));},
          $result
        );

        $this->getLogService()->info('uploadfile', 'cloud_convert_callback3', "文件云处理回调", array('result' => $result));
        $result = json_decode($result, true);
        $result = array_merge($request->query->all(), $result);
        if (empty($result['id'])) {
            throw new \RuntimeException('数据中id不能为空');
        }

        if ($result['code'] != 0) {
            $this->getLogService()->error('uploadfile', 'cloud_convert_error', "文件云处理失败", array('result' => $result));
            return $this->createJsonResponse(true);
        }

        $file = $this->getUploadFileService()->getFileByConvertHash($result['id']);
        if (empty($file)) {
            $this->getLogService()->error('uploadfile', 'cloud_convert_error', "文件云处理失败，文件记录不存在", array('result' => $result));
            throw new \RuntimeException('文件不存在');
        }

        $file = $this->getUploadFileService()->saveConvertResult3($file['id'], $result);

        return $this->createJsonResponse($file['metas2']);
    }    

    public function cloudConvertCallbackAction(Request $request)
    {
        $data = $request->getContent();

        $this->getLogService()->info('uploadfile', 'cloud_convert_callback', "文件云处理回调", array('content' => $data));

        $key = $request->query->get('key');
        $fullKey = $request->query->get('fullKey');
        if (empty($key)) {
            throw new \RuntimeException('key不能为空');
        }

        $data = json_decode($data, true);

        if (empty($data['id'])) {
            throw new \RuntimeException('数据中id不能为空');
        }

        if ($fullKey) {
            $hash = $fullKey;
        } else {
            $hash = "{$data['id']}:{$key}";
        }

        $file = $this->getUploadFileService()->getFileByConvertHash($hash);
        if (empty($file)) {
            throw new \RuntimeException('文件不存在');
        }

        if ($data['code'] != 0) {
            $this->getUploadFileService()->convertFile($file['id'], 'error');
            throw new \RuntimeException('转换失败');
        }

        $items = (empty($data['items']) or !is_array($data['items'])) ? array() : $data['items'];

        $status = $request->query->get('twoStep', false) ? 'doing' : 'success';

        if ($status == 'doing') {
            $callback = $this->generateUrl('uploadfile_cloud_convert_callback', array('key' => $key, 'fullKey' => $hash), true);
            $file = $this->getUploadFileService()->convertFile($file['id'], $status, $data['items'], $callback);
        } else {
            $file = $this->getUploadFileService()->convertFile($file['id'], $status, $data['items']);
        }

        if (in_array($file['convertStatus'], array('success', 'error'))) {
            $this->getNotificationService()->notify($file['createdUserId'], 'cloud-file-converted', array(
                'file' => $file,
            ));
        }

        return $this->createJsonResponse($file['metas2']);
    }

    public function getHeadLeaderHlsKeyAction(Request $request)
    {
        $file = $this->getUploadFileService()->getFileByTargetType('headLeader');
        $convertParams = json_decode($file['convertParams'], true);
        return new Response($convertParams['hlsKey']);
    }

    public function getMediaInfoAction(Request $request, $type)
    {
        $key = $request->query->get('key');
        $info = $this->getUploadFileService()->getMediaInfo($key, $type);
        return $this->createJsonResponse($info['format']['duration']);
    }

    protected function getSettingService()
    {
        return createService('System.SettingService');
    }

	protected function getLocalFile()
	{
		return createService('File.LocalFile');
	}

    private function getUploadFileService()
    {
        return createService('File.UploadFileService');
    }

    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getNotificationService()
    {
        return createService('User.NotificationService');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }

    private function createFilesJsonResponse($files)
    {
        foreach ($files as &$file) {
            $file['updatedTime'] = date('Y-m-d H:i', $file['updatedTime']);
            $file['size'] = FileToolkit::formatFileSize($file['size']);

            // Delete some file attributes to redunce the json response size
            unset($file['hashId']);
            unset($file['convertHash']);
            unset($file['etag']);
            unset($file['convertParams']);

            unset($file);
        }
        return $this->createJsonResponse($files);
    }

}