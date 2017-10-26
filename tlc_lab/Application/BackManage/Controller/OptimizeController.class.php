<?php

namespace BackManage\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Common\Model\Util\FileUtil;
use Common\Model\Util\SystemUtil;
use Common\Lib\WebCode;

class OptimizeController extends BaseController
{
    public function indexAction()
    {
        $isCenter   =  WebCode::isLocalCenterWeb();
       if($isCenter){
          return $this->render('System:optimize', array(
              'isCenter' => $isCenter
          ));
       }else{
           E("无权限");
       }
    }
    

    public function removeCacheAction()
    {
        if(WebCode::isLocalCenterWeb()){
             FileUtil::emptyDir(SystemUtil::getCachePath());
             return $this->createJsonResponse(false);
        }
    }
    public function removeTempAction()
    {
        if(WebCode::isLocalCenterWeb()){
            if(!$this->isDisabledUpgrade()){
                FileUtil::emptyDir(SystemUtil::getDownloadPath());
            }
             FileUtil::emptyDir(SystemUtil::getUploadTmpPath());
            return $this->createJsonResponse(true);
        }
    }    

    public function removeBackupAction()
    {
        if(WebCode::isLocalCenterWeb()){
            if(!$this->isDisabledUpgrade()){
                 FileUtil::emptyDir(SystemUtil::getBackUpPath());
             }
            return $this->createJsonResponse(true);
        }
    }
    public function backupdbAction()
    {
        if(WebCode::isLocalCenterWeb()){
            $db = SystemUtil::backupdb();
            $dir = getParameter('redcloud.upload.backup_url_path');
            $downloadFile = $dir.DIRECTORY_SEPARATOR.basename($db);
            \Symfony\Component\HttpFoundation\JsonResponse::create(array('status' => 'ok', 'result'=>$downloadFile))->send();

            return $this->createJsonResponse(array('status' => 'ok', 'result'=>$downloadFile));
        }
    }

    public function removeUnusedFilesAction()
    {
        if(WebCode::isLocalCenterWeb()){
            $this->getSystemUtilService()->removeUnusedUploadFiles();
            return $this->createJsonResponse(true);       
        }
    }

    private function isDisabledUpgrade()
    {
        if (!$this->container->hasParameter('disabled_features')) {
            return false;
        }

        $disableds = $this->container->getParameter('disabled_features');
        if (!is_array($disableds) or empty($disableds)) {
            return false;
        }

        return in_array('upgrade', $disableds);
    }

    private function getSystemUtilService()
    {
        return createService('Util.SystemUtilService');
    }


}