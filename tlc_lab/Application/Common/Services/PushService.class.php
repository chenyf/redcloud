<?php
/**
 * 百度推送
 * @author 钱志伟 2015-05-14
 * @link URL description http://push.baidu.com/doc/php/api
 */
namespace Common\Services;

require_once (APP_PATH . 'Common/Plugins/BaiduPushServerSDKPhp3_0_0/sdk.php');
use \Common\Lib\ArrayToolkit;

class PushService {

    private $apiKey = '';

    private $secretKey = '';

    private $pkgArr = array();

    private $platform = 'android';

    public $sdk = false;

    public $error = array();

    private static $_instance = array();

    public function __construct($platform = 'android', $webCode = '') {
        $webCode = $webCode ? $webCode : C('WEBSITE_CODE');
        $pushCfg = $this->getPushCfg($webCode);
        if (isset($pushCfg[$platform])) {
            $this->apiKey = $pushCfg[$platform]['apiKey'];
            $this->secretKey = $pushCfg[$platform]['secretKey'];
            $this->pkgArr = $pushCfg[$platform]['pkg'];
            $this->platform = $platform;
            $this->initSdk();
        }
    }

    /**
     * 获得推送配置
     * @author 钱志伟 2015-07-24
     */
    private function getPushCfg($webCode) {
        $pushCfg = C('PUSH_CFG');
        if (! C('MULTI_WEB_MODE') || ! C('PUSH_CFG_FROM_DB')) {
            return $pushCfg;
        }
        # 开启多站模式时，覆盖默认配置        
        $pushDbCfg = createService('School.SchoolService')->switchCenterDB()->getSchoolPushCfg(array(
            'siteSelect' => $webCode
        ));
        if (! $pushDbCfg) {

            return $pushCfg;
        }
        $pushCfg['android']['apiKey'] = $pushDbCfg['androidApiKey'];
        $pushCfg['android']['secretKey'] = $pushDbCfg['androidSecretKey'];
        $pushCfg['ios']['apiKey'] = $pushDbCfg['iosApiKey'];
        $pushCfg['ios']['secretKey'] = $pushDbCfg['iosSecretKey'];
        return $pushCfg;
    }

    public static function getInstance($platform = 'android', $cache = false, $webCode = '') {
        $webCode = $webCode ? $webCode : C('WEBSITE_CODE');
        $key = $platform . '_' . $webCode;
        if (! isset(self::$_instance[$key]) || ! (self::$_instance[$key] instanceof self) || ! $cache) {
            self::$_instance[$key] = new self($platform, $webCode);
        }
        return self::$_instance[$key];
    }

    public function initSdk() {
        if (! $this->sdk) {
            $this->sdk = new \PushSDK($this->apiKey, $this->secretKey);
        }
        if (! $this->sdk) {
            die('init baidu push fail! FILE:' . __FILE__);
        }
    }

    /**
     * 错误日志记录
     */
    public function logError($param = array()) {
        $this->error = array(
            'errorNumber' => $this->sdk->getLastErrorCode(),
            'errorMessage' => $this->sdk->getLastErrorMsg(),
            'requestId' => $this->sdk->getRequestId()
        );
        $log = array(
            'platform' => $this->platform,
            'param' => $param,
            'error' => $this->error
        );
        $logfile = sprintf(C('PUSH_LOG_FILE'), $param['type']);
        \Think\Log::write(print_r($log,true), \Think\Log::CRIT, '', $logfile);
    }

    /**
     * 广播，向当前应用下所有设备发送一条消息
     * @author 钱志伟 2015-05-14
     *         edit by lvwulong 2016.4.9
     */
    public function pushMsgToAll($param = array()) {
        $options = array(
            'ptype'         => '',           #设备类型
            'module'        => '',           #推送模块
            'operation'     => '',           #操作方法
            'title'         => '云课程',     #消息标题
            'description'   => '有新消息了', #消息显示文本
            'msgTopic'      => '',           #消息的topic，长度限制为 1-128个长度的字母和数字组合
            'unreadedNum'   => 1,            #未读条数
            'data'          => array()       #附加数据
        );
        $options = array_merge($options, $param);
        extract($options);
        if (! $ptype || ! $module || ! $operation || ! $title || ! $data) {
            return false;
        }
        # 消息控制选项。
        $opts = array(
            'msg_type' => 1
        );
        if ($msgTopic) {
            $opts['msg_topic'] = $msgTopic;
        }
        if ($ptype == 'ios') {
            $opts['deploy_status'] = C('PUSH_IOS_DEPLOY');
        }
        $pkg = isset($this->pkgArr[$ptype]) ? $this->pkgArr[$ptype] : '';
        $unreadedNum = $unreadedNum && ($x = intval($unreadedNum)) ? $x : 1;
        $message = $this->getMessage(array(
            'title'       => $title,
            'description' => $description,
            'type'        => $ptype,
            'pkg'         => $pkg,
            'badge'       => $unreadedNum,
            'data'        => array(
                'data'      => $data,
                'module'    => $module,
                'operation' => $operation
            )
        ));
        $ret = $this->sdk->pushMsgToAll($message, $opts);
        if (! $ret) {
            $this->logError(array(
                'input' => $options,
                'message' => $message,
                'function' => 'pushMsgToAll',
                'opts' => $opts
            ));
        }
        return $ret;
    }

    /**
     * 根据tag推送
     * @link http://push.baidu.com/doc/php/api
     * @author 钱志伟 2015-05-13
     *         edit by lvwulong 2016.4.16
     */
    public function pushByTag($param = array()) {
        $options = array(
            'tagName'       => '',           #tagname
            'ptype'         => '',           #设备类型
            'module'        => '',           #推送模块
            'operation'     => '',           #操作方法
            'title'         => '云课程',     #消息标题
            'description'   => '有新消息了', #消息显示文本
            'msgTopic'      => '',           #消息的topic，长度限制为 1-128个长度的字母和数字组合
            'unreadedNum'   => 1,            #未读条数
            'data'          => array()       #附加数据
        );
        $options = array_merge($options, $param);
        extract($options);
        if (! $ptype || ! $tagName || ! $module || ! $operation || ! $title || ! $data) {
            return false;
        }
        # 消息控制选项。
        $opts = array(
            'msg_type' => 1
        );
        if ($msgTopic) {
            $opts['msg_topic'] = $msgTopic;
        }
        if ($ptype == 'ios') {
            $opts['deploy_status'] = C('PUSH_IOS_DEPLOY');
        }
        $pkg = isset($this->pkgArr[$ptype]) ? $this->pkgArr[$ptype] : '';
        $unreadedNum = $unreadedNum && ($x = intval($unreadedNum)) ? $x : 1;
        $message = $this->getMessage(array(
            'title'       => $title,
            'description' => $description,
            'type'        => $ptype,
            'pkg'         => $pkg,
            'badge'       => $unreadedNum,
            'data'        => array(
                'data'      => $data,
                'module'    => $module,
                'operation' => $operation
            )
        ));
        $ret = $this->sdk->pushMsgToAll($message, $opts);
        if (! $ret) {
            $this->logError(array(
                'input' => $options,
                'message' => $message,
                'tagName' => $tagName,
                'opts' => $opts
            ));
        }
        return $ret;
    }

    /**
     * 单用户推送
     * @author 钱志伟 2015-05-14
     *         edit by lvwulong 2016.4.9
     */
    public function pushByDevice($param = array()) {
        $options = array(
            'ptype'         => '',           #设备类型
            'module'        => '',           #推送模块
            'operation'     => '',           #操作方法
            'title'         => '云课程',     #消息标题
            'description'   => '有新消息了', #消息显示文本
            'channel_id'    => '',           #设备号
            'msgTopic'      => '',           #消息的topic，长度限制为 1-128个长度的字母和数字组合
            'unreadedNum'   => 1,            #未读条数
            'data'          => array()       #附加数据
        );
        $options = array_merge($options, $param);
        extract($options);
        if (! $ptype || ! $module || ! $operation || ! $title || ! $channel_id || ! $data) {
            return false;
        }
        $opts = array(
            'msg_type' => 1
        );
        if ($msgTopic) {
            $opts['msg_topic'] = $msgTopic;
        }
        if ($ptype == 'ios') {
            $opts['deploy_status'] = C('PUSH_IOS_DEPLOY');
        }
        $pkg = $pkgType && isset($this->pkgArr[$ptype]) ? $this->pkgArr[$ptype] : '';
        $unreadedNum = $unreadedNum && ($x = intval($unreadedNum)) ? $x : 1;
        $message = $this->getMessage(array(
            'title'       => $title,
            'description' => $description,
            'type'        => $ptype,
            'pkg'         => $pkg,
            'badge'       => $unreadedNum,
            'data'        => array(
                'data'      => $data,
                'module'    => $module,
                'operation' => $operation
            )
        ));
        $ret = $this->sdk->pushMsgToSingleDevice($channel_id, $message, $opts);
        if (! $ret) {
            $this->logError(array(
                'input' => $options,
                'message' => $message,
                'channel_id' => $channel_id,
                'opts' => $opts
            ));
        }
        return $ret;
    }

    /**
     * 获得消息
     * @author 钱志伟 2015-05-13
     */
    private function getMessage($param = array()) {
        $options = array(
            'title' => '',
            'description' => '',
            'pkg' => '',
            'type' => '',
            'badge' => 1,
            'data' => array()
        );
        $options = array_merge($options, $param);
        extract($options);
        if ($this->platform == 'android') {
            # 通知类型的内容必须按指定内容发送，示例如下：
            $message = '{
                            "title": "' . $title . '",
                            "description": "' . $description . '",
                            "notification_builder_id": 0,
                            "notification_basic_style":7
                            ' . ($pkg ? (',"pkg_content":"' . $pkg . '"') : '') . '
                        }';
            $arr = json_decode($message, true);
            $arr['custom_content'] = $data;
            $message = json_encode($arr);
        } else {
            if ($type) {
                $data['type'] = $type;
            }
            $message = '{
                "aps": {
                     "alert":"' . $description . '",
                     "sound":"",
                     "badge":' . $badge . '
                }
            }';
            $arr = json_decode($message, true);
            if ($data) {
                $arr = array_merge($data, $arr);
            }
            $message = json_encode($arr);
        }
        return $message;
    }

    /**
     * 获得用户的tag
     */
    public function getUserTags($cache = true) {
        static $tags = array();
        if (! $tags || ! $cache) {
            $ret = $this->sdk->queryTags();
            if ($ret === false) {
                $this->logError(array(
                    'function' => 'queryTags'
                ));
                return array();
            }
            if ($ret['total_num'] > 0) {
                $tags = $ret['result'];
                $tags = ArrayToolkit::index($tags, 'tag');
            }
        }
        return $tags;
    }

    /**
     * 创建tag
     * @author 钱志伟 2015-05-13
     */
    public function createTag($tagName = '') {
        $ret = $this->sdk->createTag($tagName);
        if ($ret === false) {
            $this->logError(array(
                'input' => $tagName,
                'function' => 'createTag'
            ));
            return false;
        } else {
            return $ret['result'] == 0 ? true : false;
        }
    }

    /**
     * 删除tag
     * @author 钱志伟 2015-05-14
     */
    public function deleteTag($tagName = '') {
        $ret = $this->sdk->deleteTag($tagName);
        if ($ret === false) {
            $this->logError(array(
                'input' => $tagName,
                'function' => 'deleteTag'
            ));
            return false;
        } else {
            return $ret['result'] == 0 ? true : false;
        }
    }

    /**
     * 查询tag下设备数
     * @author 钱志伟 2015-05-13
     */
    public function queryDeviceNumInTag($tagName = '') {
        $rs = $this->sdk->queryDeviceNumInTag($tagName);
        return isset($rs['device_num']) ? $rs['device_num'] : 0;
    }

    /**
     * 增加tag或增加用户关注的tag, 每次只能加入设置，分批加入
     * @author 钱志伟 2014-09-28
     */
    public function addTag($tagName, $channel_ids = array()) {
        $retArr = array();
        if (! $channel_ids) {
            return false;
        }
        if (! is_array($channel_ids)) {
            $channel_ids = array(
                $channel_ids
            );
        }
        $channel_ids = array_unique($channel_ids);
        $this->createTag($tagName);
        $singleGrpNum = 10;
        $groupNum = ceil(count($channel_ids) / $singleGrpNum);
        for ($i = 0; $i < $groupNum; $i ++) {
            $idArr = array_slice($channel_ids, $i * $singleGrpNum, $singleGrpNum);
            $ret = $this->sdk->addDevicesToTag($tagName, $idArr);
            if ($ret === false) {
                $this->logError(array(
                    'input' => $tagName,
                    'channel_ids' => $idArr,
                    'function' => 'addDevicesToTag'
                ));
            } else {
                $retArr[] = $ret;
            }
        }
        return $retArr;
    }

    /**
     * 删除tag或解除用户关注的tag
     * @author 钱志伟 2014-09-28
     */
    public function delDevicesFromTag($tagName, $channel_ids = array()) {
        if (! $channel_ids) {
            return false;
        }
        $ret = $this->sdk->delDevicesFromTag($tagName, $channel_ids);
        if (! $ret) {
            $this->logError(array(
                'input' => $tagName,
                'channel_ids' => $channel_ids,
                'function' => 'delDevicesFromTag'
            ));
            return false;
        }
        return isset($ret['result']) && $ret['result'] == 0 ? true : false;
    }

    /**
     * 删除用户所有tag
     */
    public function deleteDeviceAllTag($param = array()) {
        $option = array(
            'channel_ids' => array(),
            'channel_id' => '',
            'onlyClass' => 1
        );
        $option = array_merge($option, $param);
        extract($option);
        $channel_ids = array_merge($channel_ids, array(
            $channel_id
        ));
        //$isBind = $this->bindVerify($user_id, array('deviceType'=>3));
        //if(!$isBind) return true;
        $tags = $this->getUserTags();
        if ($tags) {
            foreach ($tags as $tag) {
                $pfx = isProductEnv() ? C('PUSH_CLASS_TAG_KEY_PFX_PRO') : C('PUSH_CLASS_TAG_KEY_PFX_DEV');
                if ($onlyClass && strpos($tag['tag'], $pfx !== false))
                    continue;
                $r = $this->delDevicesFromTag($tag['tag'], $channel_ids);
            }
        }
        return true;
    }

    /**
     * 通过主题查询推送记录
     * @author 钱志伟 2015-05-14
     */
    public function getTopicRecords($param = array()) {
        $options = array(
            'topicId' => ''
        );
        $options = array_merge($options, $param);
        extract($options);
        $list = $this->sdk->queryTopicRecords($topicId);
        print_r($list);
        exit();
    }

    /**
     * 获得批量单播所使用过的分类主题及推送和到达的数据量
     */
    public function getTopicList($param = array()) {
        $options = array();
        $options = array_merge($options, $param);
        extract($options);
        $result = array();
        $ret = $this->sdk->queryTopicList();
        if ($ret === false) {
            $this->logError(array(
                'input' => $options,
                'function' => 'queryTopicList'
            ));
            return array();
        } else {
            if ($ret['total_num'] > 0) {
                foreach ($ret['result'] as $item) {
                    # 查询推送记录
                    $item['list'] = $this->sdk->queryTopicRecords($item['topic_id'], array(
                        'limit' => 10
                    ));
                    $result[$item['topic_id']] = $item;
                }
            }
        }
        return $result;
    }

    /**
     * 获得消息历史记录
     * @author 钱志伟 2015-05-15
     */
    public function getMessageHistory($param = array()) {
        $options = array();
        $options = array_merge($options, $param);
        extract($options);
        $ret = $this->sdk->queryMessageHistory();
        if (! $ret) {
            $this->logError(array(
                'options' => $options,
                'function' => 'queryMessageHistory'
            ));
            return array();
        }
        return array();
    }
}