<?php
namespace Common\Model\System;
use \Common\Model\Common\BaseModel;

class SettingServiceModel extends BaseModel
{
    
    const CACHE_NAME = 'settings';
    
    private $cached;
    
//    private function initSet(){
//        $this->setCommCenterWebUseWebCodeRule();
//    }
    /**
     * 修改配置
     * @author 钱志伟 2015-08-31
     */
    public function editSetting($id, $value){
//        $this->initSet();
        return $this->getSettingDao()->editSetting($id, array('value'=>serialize($value)));
    }

    public function set($name, $value,$siteSelect = "local")
    {
//        $this->initSet();
        $this->getSettingDao()->deleteSettingByName($name,$siteSelect);
        if($siteSelect == "local") $webCode = C('WEBSITE_CODE');
        else $webCode = $siteSelect;
        $setting = array(
            'name'  => $name,
            'value' => serialize($value),
            'webCode'=> $webCode
        );
        $info = $this->getSettingDao()->addSetting($setting);
        $this->clearCache();
        return $info;
    }
    
    /*
     * edit fubaosheng 2016-03-10 
     * $cache 是否需要缓存，默认有
     */
    public function get($name, $default = NULL,$siteSelect = "local",$cache=true)
    {
//        $this->initSet();
        static $arr = array();
        if(!isset($arr[$name])){
            $settings = $this->getSettingDao()->findAllSettings($siteSelect,$cache);
            foreach($settings as $setting){
                $arr[$setting['name']] = $setting['value'];
            }
        }
        return isset($arr[$name]) ? unserialize($arr[$name]) : $default;
    }

    public function delete($name , $siteSelect='local')
    {
//        $this->initSet();
        $this->getSettingDao()->deleteSettingByName($name, $siteSelect);
        $this->clearCache();
    }
    
    protected function clearCache()
    {
        $this->getCacheService()->clear(self::CACHE_NAME);
        $this->cached = null;
    }
    
    protected function getSettingDao ()
    {
        return $this->createDao('System.SettingModel');
    }
      protected function getCacheService()
    {
        return $this->createService('System.CacheServiceModel');
    }


}