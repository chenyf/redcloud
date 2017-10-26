<?php
namespace BackManage\Controller;

use Home\Controller\BaseController as WebBaseController;
use Common\Model\CloudPlatform\Client\CloudAPI;

class BaseController extends WebBaseController
{

    protected function getDisabledFeatures()
    {
        if (!$this->container->hasParameter('disabled_features')) {
            return array();
        }

        $disableds = $this->container->getParameter('disabled_features');
        if (!is_array($disableds) or empty($disableds)) {
            return array();
        }

        return $disableds;
    }

    protected function refreshCopyright($info = array())
    {
        $settingService = createService('System.SettingServiceModel');

        if (empty($info)) {
            $api = $this->createAPIClient();
            $info = $api->get('/me');
        }

        if (isset($info['copyright'])) {
            if ($info['copyright']) {
                $copyright = $settingService->get('copyright', array());
                if (empty($copyright['owned'])) {
                    $copyright['owned'] = 1;
                    $settingService->set('copyright', $copyright);
                }
            } else {
                $settingService->delete('copyright');
            }
        }
    }

    protected function createAPIClient()
    {
        $settings = createService('System.SettingService')->get('storage', array());
        return new CloudAPI(array(
            'accessKey' => empty($settings['cloud_access_key']) ? '' : $settings['cloud_access_key'],
            'secretKey' => empty($settings['cloud_secret_key']) ? '' : $settings['cloud_secret_key'],
            'apiUrl' => empty($settings['cloud_api_server']) ? '' : $settings['cloud_api_server'],
        ));
    }

}
