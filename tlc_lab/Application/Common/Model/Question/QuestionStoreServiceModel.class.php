<?php

namespace Common\Model\Question;

use Common\Model\Common\BaseModel;
use Think\RedisModel;
use Think\Cache;
use \Common\Lib\ArrayToolkit;
use Common\Lib\WebCode;

class QuestionStoreServiceModel extends BaseModel {

    private $redisConn = false;
    private $cacheMem = false;
    private static $_publicCourse = 0;

    public function __construct() {
        parent::__construct();
        $this->initRedis();
        $this->initMemcache();
    }

    public function initRedis() {
        if ($this->redisConn != false)
            return false;
        $this->redisConn = RedisModel::getInstance(C('REDIS_DIST.CLASS_SIGN_IN'));
    }

    public function initMemcache() {
        if ($this->cacheMem != false)
            return false;
        $this->cacheMem = Cache::getInstance('Memcache');
    }

    /*
     * redis 设置
     */

    public function setQuestion($key, $questions) {
        return $this->redisConn->set($key, serialize($questions));
    }

    public function getQuestion($key) {
        $redisData = unserialize($this->redisConn->get($key));
        return $redisData ? $redisData : array();
    }

    public function exists($key) {
        return $this->redisConn->exists($key);
    }

    public function delQuestion($key) {
        return $this->redisConn->delete($key);
    }

    /*
     * memcache设置
     */

    public function setMemQuestion($key, $ids) {
        return $this->cacheMem->set($key, serialize($ids));
    }

    public function getMemQuestion($key) {
        return unserialize($this->cacheMem->get($key));
    }

    public function isMemExists($key) {
        return !empty($this->cacheMem->get($key));
    }

    public function delMemQuestion($key) {
        return $this->cacheMem->delete($key);
    }

    //zy:作业；lx:联系；ks:考试
    public function setMemKey($type, $homeWorkId) {
        $typeId = $type == '1' ? 'zy_' . $homeWorkId : ($type == '2' ? 'lx_' . $homeWorkId : 'ks_' . $homeWorkId);
        return $typeId;
    }

}
