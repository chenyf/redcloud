<?php
namespace Common\Model\System;
use Common\Model\Common\BaseModel;
class CacheServiceModel extends BaseModel{
    
    /**
     * 获取缓存数据
     * 缓存无效(不存在或已过期)，则返回NULL
     */
    public function get($name){
        $datas = $this->gets(array($name));
        if (empty($datas)) {
            return null;
        }
        return reset($datas);
    }

    /**
     * 批量获取缓存数据
     * 以数组的形式，返回有效的缓存；缓存的name作为数组的key，缓存的data作为数组的value。
     */
    public function gets (array $names){
        $this->garbageCollection();
        $names = array_filter($names);
        if(empty($names)){
            return array();
        }
        $datas = array();
        $caches = $this->getCacheDao()->findCachesByNames($names);
        $now = time();
        foreach ($caches as $cache) {
            if ($cache['expiredTime'] > 0 && $cache['expiredTime'] < $now ) {
                continue;
            }
            $datas[$cache['name']] = $cache['serialized'] ? unserialize($cache['data']) : $cache['data'];
        }
        return $datas;
    }

    /**
     * 添加缓存
     * expire=0, 永久
     */
    public function set($name, $data, $expiredTime = 0){
        $this->getCacheDao()->deleteCacheByName($name);
        $serialized = is_string($data) ? 0 : 1;
    	$cache = array(
            'name' => $name,
            'data' => $serialized ? serialize($data) : $data,
            'serialized' => $serialized,
            'expiredTime' => $expiredTime,
            'createdTime' => time(),
        );
        return $this->getCacheDao()->addCache($cache);
    }

    /**
     * 清除缓存
     * name为空的话，则清除所有缓存
     */
    public function clear ($name = NULl){
        if (!empty($name)) {
            return $this->getCacheDao()->deleteCacheByName($name);
        } else {
            return $this->getCacheDao()->deleteAllCache();
        }
    }
    
    /**
     * @todo
     */
    protected function garbageCollection(){

    }

    protected function getCacheDao(){
        return $this->createDao("System.Cache");
    }
}
?>
