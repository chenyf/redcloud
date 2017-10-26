<?php

namespace BackManage\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;

use Common\Model\Util\LiveClientFactory;
use Common\Lib\ArrayToolkit;
use Common\Lib\FileToolkit;
use Common\Lib\Paginator;
use Common\Model\Util\PluginUtil;
use Common\Model\Util\CloudClientFactory;
use Common\Model\CloudPlatform\KeyApplier;
use Common\Model\CloudPlatform\Client\CloudAPI;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Gd\Image;
class CloudSettingController extends BaseController
{
    public function keyAction(Request $request)
    {
        $settings = $this->getSettingService()->get('storage', array());
        if (empty($settings['cloud_access_key']) or empty($settings['cloud_secret_key'])) {
            return $this->redirect($this->generateUrl('admin_setting_cloud_key_update'));
        }
        return $this->render('CloudSetting:key', array(
        ));
    }

    public function keyInfoAction(Request $request)
    {
        $api = $this->createAPIClient();
        $info = $api->get('/me');

        if (!empty($info['accessKey'])) {
            $settings = $this->getSettingService()->get('storage', array());
            if (empty($settings['cloud_key_applied'])) {
                $settings['cloud_key_applied'] = 1;
                $this->getSettingService()->set('storage', $settings);
            }
         
            
            $this->refreshCopyright($info);

        } else {
            $settings = $this->getSettingService()->get('storage', array());
            $settings['cloud_key_applied'] = 0;
            $this->getSettingService()->set('storage', $settings);
        }

        $currentHost = $request->server->get('HTTP_HOST');

        return $this->render('CloudSetting:key-license-info', array(
            'info' => $info,
            'currentHost' => $currentHost,
            'isLocalAddress' => $this->isLocalAddress($currentHost),
        ));
    }

    public function keyBindAction(Request $request)
    {
        $api = $this->createAPIClient();
        $currentHost = $request->server->get('HTTP_HOST');
        $result = $api->post('/me/license-domain', array('domain' => $currentHost));

        if (!empty($result['licenseDomains'])) {
            $this->setFlashMessage('success', '授权域名绑定成功！');
        } else {
            $this->setFlashMessage('danger', '授权域名绑定失败，请重试！');
        }

        return $this->createJsonResponse($result);
    }


    
    public function keyUpdateAction(Request $request)
    {
        $settings = $this->getSettingService()->get('storage', array());

        if ($request->getMethod() == 'POST') {
            $options = $request->request->all();

            if (!empty($settings['cloud_api_server'])) {
                $options['apiUrl'] = $settings['cloud_api_server'];
            }
         
            $api = new CloudAPI($options);

            $result = $api->post(sprintf('/keys/%s/verification', $options['accessKey']));

            if (isset($result['error'])) {
                $this->setFlashMessage('danger', 'AccessKey / SecretKey　不正确！');
                goto render;
            }
            
            $user = $api->get('/me');
            if ($user['edition'] != 'opensource') {
                $this->setFlashMessage('danger', 'AccessKey / SecretKey　不正确！！');
                goto render;
            }

            $settings['cloud_access_key'] = $options['accessKey'];
            $settings['cloud_secret_key'] = $options['secretKey'];
            $settings['cloud_key_applied'] = 1;

            $this->getSettingService()->set('storage', $settings);

            $this->setFlashMessage('success', '授权码保存成功！');
            return $this->redirect($this->generateUrl('admin_setting_cloud_key'));
        }

        render:
        return $this->render('CloudSetting:key-update', array(
        ));
    }

    public function keyApplyAction(Request $request)
    {
        $applier = new KeyApplier();
        $keys = $applier->applyKey($this->getCurrentUser());
        
        if (empty($keys['accessKey']) or empty($keys['secretKey'])) {
            return $this->createJsonResponse(array('error' => 'Key生成失败，请检查服务器网络后，重试！'));
        }

        $settings = $this->getSettingService()->get('storage', array());

        $settings['cloud_access_key'] = $keys['accessKey'];
        $settings['cloud_secret_key'] = $keys['secretKey'];
        $settings['cloud_key_applied'] = 1;

        $this->getSettingService()->set('storage', $settings);
        
        return $this->createJsonResponse(array('status' => 'ok'));
    }

    public function keyCopyrightAction(Request $request)
    {

        $api = $this->createAPIClient();
        $info = $api->get('/me');

        if (empty($info['copyright'])) {
            throw $this->createAccessDeniedException('您无权操作!');
        }

        $name = $request->request->get('name');

        $this->getSettingService()->set('copyright', array(
            'owned' => 1,
            'name' => $request->request->get('name', ''),
        ));

        return $this->createJsonResponse(array('status' => 'ok'));
    }

    public function videoAction(Request $request)
    {
        $siteSelectArr = $request->query->all();
        $siteSelect = isset($siteSelectArr['siteSelect']) ? $siteSelectArr['siteSelect'] : C('WEBSITE_CODE');
        $storageSetting = $this->getSettingService()->get('storage', array(), $siteSelect);
//        $default = array(
//            'upload_mode' => 'local',
//            'cloud_bucket' => '',
//            'video_quality' => 'low',
//            'video_audio_quality' => 'low',
//            'video_watermark' => 0,
//            'video_watermark_image' => '',
//            'video_embed_watermark_image' => '',
//            'video_watermark_position' => 'topright',
//            'video_fingerprint' => 0,
//            'video_header' => null,
//        );

        if ($request->getMethod() == 'POST') {
            $storageSetting = array_merge($storageSetting, $request->request->all());
            $this->getSettingService()->set('storage', $storageSetting,$siteSelect);
            $this->setFlashMessage('success', '云视频设置已保存！');
        } else {
            $storageSetting = array_merge($storageSetting);
        }

        $headLeader = array();
        if(!empty($storageSetting) && array_key_exists("video_header", $storageSetting) && $storageSetting["video_header"]){
            $headLeader = $this->getUploadFileService()->getFileByTargetType('headLeader');
        }

        return $this->render('CloudSetting:video', array(
            'storageSetting'=>$storageSetting,
            'headLeader'=>$headLeader,
            'siteSelect'=>$siteSelect
        ));
    }

    public function videoWatermarkUploadAction(Request $request)
    {
        $file = $request->files->get('watermark');
        if (!FileToolkit::isImageFile($file)) {
            throw $this->createAccessDeniedException('图片格式不正确！');
        }

        $filename = 'watermark_' . time() . '.' . $file->getClientOriginalExtension();
        
        $directory = getParameter('redcloud.upload.public_directory')."/xitong";
        $file = $file->move($directory, $filename);
        $path = "system/{$filename}";

        $response = array(
            'path' => $path,
            'url' =>  $this->get('redcloud.twig.web_extension')->getFileUrl($path),
        );

        echo (json_encode($response));
    }


   public function videoEmbedWatermarkUploadAction(Request $request)
    {
            $file = $request->files->get('watermark');
            if (!FileToolkit::isImageFile($file)) {
                throw $this->createAccessDeniedException('图片格式不正确！');
            }

            $filename = 'watermarkembed_' . time() . '.' . $file->getClientOriginalExtension();

            $directory = getParameter('redcloud.upload.public_directory')."/xitong";

            $file = $file->move($directory, $filename);
            $path = "system/{$filename}";
            $originFileInfo = getimagesize($file);
            $filePath = getParameter('redcloud.upload.public_directory')."/".$path;
            $imagine = new Imagine();
            $rawImage = $imagine->open($filePath);

            $pathinfo = pathinfo($filePath);
            $specification['240'] = 20;
            $specification['360'] = 30;
            $specification['480'] = 40;
            $specification['720'] = 60;
            $specification['1080'] = 90;

            foreach ($specification as $key => $value) {
                $width= ($originFileInfo[0]*$value/$originFileInfo[1]);
            $specialImage = $rawImage->copy();
            $specialImage->resize(new Box($width, $value));
            $filePath = "{$pathinfo['dirname']}/{$pathinfo['filename']}-{$key}.{$pathinfo['extension']}";
            $specialImage->save($filePath);
            }

            $response = array(
                'path' => $path,
                'url' =>  $this->get('redcloud.twig.web_extension')->getFileUrl($path),
            );
          
           echo (json_encode($response));
      
    }


    public function videoWatermarkRemoveAction(Request $request)
    {
        return $this->createJsonResponse(true);
    }

    private function isLocalAddress($address)
    {
        if (in_array($address, array('localhost', '127.0.0.1'))) {
            return true;
        }

        if (strpos($address, '192.168.') === 0) {
            return true;
        }

        if (strpos($address, '10.') === 0) {
            return true;
        }

        return false;
    }

    protected function getSettingService()
    {
        return createService('System.SettingServiceModel');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppServiceModel');
    }

    protected function getUploadFileService()
    {
        return createService('File.UploadFileServiceModel');
    }
}