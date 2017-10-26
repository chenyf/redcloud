<?php
/**
 * 批处理
 * @author 谈海涛 2015-12-15
 */
namespace Common\Services;

use Common\Services\QueueService;
use Think\RedisModel;
use Common\Lib\ArrayToolkit;

class BatProcessService {
    
    private static $redisConn = false;
    private static $code;
    private static $strId;
    private static $microtime;
    
    private static $codeArr = array(
        1000 => "添加成功",
        1001 => "邮箱或者手机号不存在",
        1002 => "成员角色不在列表范围内",
        1003 => "不能给自己设置角色",
        1004 => "班级不存在",
        1005 => "该帐号已经加入本班",
        1006 => "添加失败",
        1007 => "该帐号已经加入其他班级",
        1008 => "任务添加失败",
        1009 => "请选择文件",
        1010 => "文件上传成功",
        1011 => "文件上传失败",
        1012 => "请选择文件",
        1013 => "不是Excel文件，请重新选择文件上传",
        1014 => "文件的大小不符合要求",
        1015 => "文件路径不正确 ",
        1016 => "文件名为空",
        1017 => "文件不存在",
        1018 => "不是Excel文件，请重新选择文件上传",
    );

    public function __construct() {
        self::initRedis();
    }

    public static function initRedis() {
        if (self::$redisConn != false)
            return false;
        self::$redisConn = RedisModel::getInstance(C('REDIS_DIST.BAT_TASK'));
    }

    #任务列表
    public static function getTaskListKey($code, $strId) {
        $str = "bat_process_task_list_" . C('WEBSITE_CODE') . "_{$code}_{$strId}";
        return strtolower($str);
    }

    #任务信息
    public static function getTaskInfoKey($code, $strId, $microtime) {
        $str = "bat_process_task_info_" . C('WEBSITE_CODE') . "_{$code}_{$strId}_{$microtime}";
        return strtolower($str);
    }

    #任务子项列表
    public static function getTaskItemKey($code, $strId, $microtime) {
        $str = "bat_process_task_item_" . C('WEBSITE_CODE') . "_{$code}_{$strId}_{$microtime}";
        return strtolower($str);
    }

    #任务子项处理状态
    public static function getTaskItemInfoKey($code, $strId, $itemMicrotime) {
        $str = "bat_process_task_item_info_" . C('WEBSITE_CODE') . "_{$code}_{$strId}_{$itemMicrotime}";
        return strtolower($str);
    }

    public static function getUid(){
        return createService('User.UserService')->getCurrentUser()->id;
    }
    
    /**
     * 添加任务
     * @param  string $code, string $strId
     * @author 谈海涛 2015-12-15
     */
    public static function addTaskList($code, $strId) {
        if (!$code || !$strId)
            return false;
        $taskListKey = self::getTaskListKey($code, $strId);
        $microtime = getMicroTm();
        $r = self::$redisConn->zAdd($taskListKey, getMicroTm(), $microtime);
        return $r ? $microtime : false;
    }

    /**
     * 获得任务父项列表
     * @param  string $code, string $strId
     * @author 谈海涛 2015-12-16
     */
     public static function getTaskList($param = array()) {
        $options = array(
            'code' => '',
            'strId' => '',
            'sMicroTm' => '-inf',
            'eMicroTm' => '+inf',
            'status' => 'all',
            'start' => 0,
            'length' => 5,
        );
        $options = array_merge($options, $param);
        extract($options);
        
        $taskListKey = self::getTaskListKey($code, $strId);
        $list = self::$redisConn->zRevrangeByScore($taskListKey, $eMicroTm, $sMicroTm);
        
        $data = array();
        $arr = array();
        foreach ($list as $value) {
            $info = self::getTaskInfo($code, $strId, $value);
            $info["microtime"] = $value;
            if ($status != 'all' && $info['status'] == $status) {
                $data[] = $info;
            } else {
                $arr[] = $info;
            }
        }

        $result = ($status != 'all') ? $data : $arr;
        if(!empty($result)){
            $result = array_slice($result,$start,$length);
            foreach ($result as $key => $task) {
                $result[$key]["extData"] = json_decode($task["extData"],true);
            }
            return $result;
        }
        return array();
    }
    
    /**
     * 获得任务父项列表总数
     * @param  string $code, string $strId
     * @author 谈海涛 2015-12-16
     */
    public static function getTaskListCount($param = array()) {
        $options = array(
            'code' => '',
            'strId' => '',
            'sMicroTm' => '-inf',
            'eMicroTm' => '+inf',
            'status' => 'all',
        );
        $options = array_merge($options, $param);
        extract($options);

        $taskListKey = self::getTaskListKey($code, $strId);
        $list = self::$redisConn->zRevrangeByScore($taskListKey, $eMicroTm, $sMicroTm);

        $data = array();
        $arr = array();
        foreach ($list as $key => $value) {
            $info = self::getTaskInfo($code, $strId, $value);
            if ($status != 'all' && $info['status'] == $status) {
                $data[] = $info;
            } else {
                $arr[] = $info;
            }
        }
        if ($status != 'all') {
            $num = count($data);
        } else {
            $num = count($arr);
        }

        return $num ? $num : 0;
    }
    
    /**
     * 获得任务键值
     * @param  string $code, string $strId
     * @author 谈海涛 2015-12-16
     */
    public static function getTaskKeyList($param = array()) {
        $options = array(
            'code' => '',
            'strId' => '',
            'sMicroTm' => '-inf',
            'eMicroTm' => '+inf',
            'limit' => array(),
        );
        $options = array_merge($options, $param);
        extract($options);

        if ($limit) {
            $param['limit'] = $limit;
        }
        $taskListKey = self::getTaskListKey($code, $strId);
        $list = self::$redisConn->zRevrangeByScore($taskListKey, $eMicroTm, $sMicroTm, $param);

        return $list ? $list : array();
    }

    /**
     * 添加任务信息
     * @param  string $code, string $strId ,string $microtime ,array $data
     * @author 谈海涛 2015-12-16
     */
    public static function addTaskInfo($param) {
        if (empty($param))
            return false;

        $taskInfoKey = self::getTaskInfoKey(self::$code, self::$strId, self::$microtime);
        $uid = self::getUid();
        $count = count($param['rowData']);
        $extData = $param["extData"] ? : array();
        $extData = json_encode($extData);
        $arr = array('uid'=>$uid,'createTm'=>time(),'webCode'=>C('WEBSITE_CODE'),'code'=>self::$code,'status'=>'nostart','lastModifyTm'=>0,'count'=>$count,'successNum'=>0,'errorNum'=>0,'extData'=>$extData);
        $r = self::$redisConn->hashSet($taskInfoKey, $arr);
        return $r ? $r : false;
    }

    /**
     * 获得任务信息
     * @param  string $code, string $strId ,string $microtime ,array $data
     * @author 谈海涛 2015-12-16
     */
    public static function getTaskInfo($code, $strId, $microtime) {
        $taskInfoKey = self::getTaskInfoKey($code, $strId, $microtime);
        return self::$redisConn->hGetAll($taskInfoKey);
    }

    /**
     * 获得某任务状态
     * @param  string $code, string $strId ,string $microtime 
     * @author 谈海涛 2015-12-16
     */
    public static function getTaskStatus($code, $strId, $microtime) {
        $taskInfoKey = self::getTaskInfoKey($code, $strId, $microtime);
        $arr = self::$redisConn->hGetAll($taskInfoKey);
        return $arr['status'] ? $arr['status'] : '';
    }

    /**
     * 获得某任务学员总数
     * @param  string $code, string $strId ,string $microtime 
     * @author 谈海涛 2015-12-16
     */
    public static function getTaskCount($code, $strId, $microtime) {
        $taskInfoKey = self::getTaskInfoKey($code, $strId, $microtime);
        $arr = self::$redisConn->hGetAll($taskInfoKey);
        return $arr['count'] ? $arr['count'] : 0;
    }

    /**
     * 添加任务子项列表
     * @param  string $code, string $strId ,string $microtime
     * @author 谈海涛 2015-12-15
     */
    public static function addTaskItem() {
        $taskItemKey = self::getTaskItemKey(self::$code, self::$strId, self::$microtime);
        $itemMicrotime = getMicroTm();
        $r = self::$redisConn->zAdd($taskItemKey, getMicroTm(), $itemMicrotime);
        return $r ? $itemMicrotime : false;
    }

    /**
     * 获得任务子项列表
     * @param  string $code, string $strId ,string $microtime
     * @author 谈海涛 2015-12-16
     */
    public static function getTaskDetailList($paramArr = array()) {
        $options = array(
            'code' => '',
            'strId' => '',
            'microtime' => '',
            'sMicroTm' => '-inf',
            'eMicroTm' => '+inf',
            'limit' => array(),
        );
        $options = array_merge($options, $paramArr);
        extract($options);
        
        $param = array();
        if ($limit)
            $param['limit'] = $limit;
        
        $taskItemKey = self::getTaskItemKey($code, $strId, $microtime);
        $list = self::$redisConn->zRevrangeByScore($taskItemKey, $eMicroTm, $sMicroTm, $param);
        
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = self::getTaskItemInfo($code, $strId, $value);
        }
        return $data ?  : array();
    }

    /**
     * 获得任务子项列表总数
     * @param  string $code, string $strId ,string $microtime
     * @author 谈海涛 2015-12-16
     */
    public static function getTaskItemCount($param = array()) {
        $options = array(
            'code' => '',
            'strId' => '',
            'microtime' => '',
            'sMicroTm' => '-inf',
            'eMicroTm' => '+inf',
            'status' => 'all',
        );
        $options = array_merge($options, $param);
        extract($options);
        
        $taskItemKey = self::getTaskItemKey($code, $strId, $microtime);
        $list = self::$redisConn->zRevrangeByScore($taskItemKey, $eMicroTm, $sMicroTm);

        $data = array();
        $arr = array();
        foreach ($list as $key => $value) {
            $itemInfo = self::getTaskItemInfo($code, $strId, $value);
            if ($status != 'all' && $itemInfo['status'] == $status) {
                $data[] = $itemInfo;
            } else {
                $arr[] = $itemInfo;
            }
        }
        if ($status != 'all') {
            $num = count($data);
        } else {
            $num = count($arr);
        }

        return $num ? $num : 0;
    }
    
    /**
     * 获得任务子项列表
     * @param  string $code, string $strId ,string $microtime
     * @author 谈海涛 2015-12-16
     */
    public static function getTaskItemList($param = array()) {
        $options = array(
            'code' => '',
            'strId' => '',
            'microtime' => '',
            'sMicroTm' => '-inf',
            'eMicroTm' => '+inf',
            'status' => 'all',
            'start' => 0,
            'length' => 5,
        );
        $options = array_merge($options, $param);

        extract($options);
        $taskItemKey = self::getTaskItemKey($code, $strId, $microtime);
        $list = self::$redisConn->zRevrangeByScore($taskItemKey, $eMicroTm, $sMicroTm);

        $data = array();
        $arr = array();
        foreach ($list as $key => $value) {
            $itemInfo = self::getTaskItemInfo($code, $strId, $value);
            if ($status != 'all' && $itemInfo['status'] == $status) {
                $data[] = $itemInfo;
            } else {
                $arr[] = $itemInfo;
            }
        }
        if ($status != 'all') {
            return $data ? self::decorateData(array_slice($data,$start,$length)) : array();
        } else {
            return $arr  ? self::decorateData(array_slice($arr,$start,$length)) : array();
        }
    }
    
    /**
     * 处理子项数据
     * @param type $data
     * @return type
     */
    private function decorateData($data){
        foreach ($data as $key => $item) {
            if(!is_not_json($item["itemData"]))
                $data[$key]["itemData"] = json_decode($item["itemData"],true);
            if($item["processTm"])
                $data[$key]["processTm"] = date("Y-m-d H:i:s",$item["processTm"]);
        }
        return $data;
    }

    /**
     * 获得任务子项轮训列表
     * @param  string $code, string $strId ,string $microtime
     * @author 谈海涛 2015-12-16
     */
    public static function getPollTaskList($param = array()) {
        $options = array(
            'code' => '',
            'strId' => '',
            'microtime' => '',
            'pollTm' => 0,
            'sMicroTm' => '-inf',
            'eMicroTm' => '+inf',
        );
        $options = array_merge($options, $param);
        extract($options);

        $taskItemKey = self::getTaskItemKey($code, $strId, $microtime);
        $list = self::$redisConn->zRangeByScore($taskItemKey, $sMicroTm, $eMicroTm, array());

        $data = array();
        foreach ($list as $key => $value) {
            $itemInfo = self::getTaskItemInfo($code, $strId, $value);
            if ( ($itemInfo['status'] != 'nostart') && ($itemInfo['processTm'] > $pollTm) ) {
                $data[] = $itemInfo;
            }
        }
        return !empty($data) ? self::decorateData($data) : array();
    }

    public static function modifyTaskInfo($param = array()) {
        $options = array(
            'code' => '',
            'strId' => '',
            'microtime' => '',
            'sMicroTm' => '-inf',
            'eMicroTm' => '+inf'
        );
        $options = array_merge($options, $param);
        extract($options);

        $taskItemKey = self::getTaskItemKey($code, $strId, $microtime);
        $list = self::$redisConn->zRevrangeByScore($taskItemKey, $eMicroTm, $sMicroTm);
        $failure = array();
        $success = array();
        foreach ($list as $key => $value) {
            $ItemInfo = self::getTaskItemInfo($code, $strId, $value);
            if ($ItemInfo['status'] == 'failure')
                $failure[] = $ItemInfo;
            if ($ItemInfo['status'] == 'success')
                $success[] = $ItemInfo;
        }
        $data['errorNum'] = count($failure);
        $data['successNum'] = count($success);
        $count = self::getTaskCount($code, $strId, $microtime);
        $num = $data['errorNum'] + $data['successNum'];
        if (intval($count) == intval($num))
            $data['status'] = 'finish';
        $data['lastModifyTm'] = time();
        self::updateTaskInfo($code, $strId, $microtime, $data);
    }

    /**
     * 添加任务子项处理状态
     * @param  string $code, string $strId ,string $microtime ,array $data
     * @author 谈海涛 2015-12-15
     */
    public static function addTaskItemInfo($itemMicrotime, $param, $num) {
        if (!$itemMicrotime || empty($param))
            return false;
        $taskItemInfoKey = self::getTaskItemInfoKey(self::$code, self::$strId, $itemMicrotime);

        if (is_array($param))
            $itemData = json_encode($param);
        else
            $itemData = $param;
        
        $arr = array('itemMicrotime'=>$itemMicrotime,'itemData'=>$itemData,'status'=>'nostart','processTm'=>0,'remark' => '','num'=>$num);
        $r = self::$redisConn->hashSet($taskItemInfoKey, $arr);
        return $r ? $r : false;
    }

    /**
     * 获得任务子项处理
     * @param  string $code, string $strId ,string $itemMicrotime
     * @author 谈海涛 2015-12-16
     */
    public static function getTaskItemInfo($code, $strId, $itemMicrotime) {
        $taskItemInfoKey = self::getTaskItemInfoKey($code, $strId, $itemMicrotime);
        return self::$redisConn->hGetAll($taskItemInfoKey);
    }

    /**
     * 获得任务子项处理状态
     * @param  string $code, string $strId ,string $itemMicrotime
     * @author 谈海涛 2015-12-16
     */
    public static function getTaskItemStatus($code, $strId, $itemMicrotime) {
        $taskItemInfoKey = self::getTaskItemInfoKey($code, $strId, $itemMicrotime);
        $taskItemInfo = self::$redisConn->hGetAll($taskItemInfoKey);
        return $taskItemInfo['status'] ? $taskItemInfo['status'] : '';
    }

    /**
     * 添加任务信息
     * @param  string $code, string $strId ,string $microtime ,array $data
     * @author 谈海涛 2015-12-15
     */
   public static function updateTaskInfo($code, $strId, $microtime, $fields) {
        if (!$code || !$strId || !$microtime || empty($fields))
            return false;
      
        $taskInfoKey = self::getTaskInfoKey($code, $strId, $microtime);
        $r = self::$redisConn->hashSet($taskInfoKey, $fields);
        return $r ? $r : false;
    }

    /**
     * 是否存在任务信息
     * @author 谈海涛 2015-12-16
     */
    public static function isExistsTaskInfo($code, $strId, $microtime) {
        $taskInfoKey = self::getTaskInfoKey($code, $strId, $microtime);
        return self::$redisConn->exists($taskInfoKey);
    }

    /**
     * 更新任务子项处理状态
     * @param  string $code, string $strId ,string $microtime ,array $data
     * @author 谈海涛 2015-12-15
     */
    public static function updateTaskItemInfo($code, $strId, $itemMicrotime, $fields) {
        if (!$code || !$strId || !$itemMicrotime || empty($fields))
            return false;
        
        $taskItemInfoKey = self::getTaskItemInfoKey($code, $strId, $itemMicrotime);
        $fields['processTm'] = time();
        $r = self::$redisConn->hashSet($taskItemInfoKey, $fields);
        return $r ? $r : false;
    }

    /**
     * 是否存在任务子项
     * @author 谈海涛 2015-12-16
     */
    public static function isExistsItemInfo($code, $strId, $itemMicrotime) {
        $taskItemInfoKey = self::getTaskItemInfoKey($code, $strId, $itemMicrotime);
        return self::$redisConn->exists($taskItemInfoKey);
    }

    /**
     * 解析帐号串
     * string $accounts  string  type: mobile|email|mix   mix代表手机号和邮箱都可以  
     * return array 
     * @author 谈海涛 2015-12-16
     */
    public static function parseAccountString($accounts, $type = "mix") {
        if (!$accounts)
            return false;
        $accountsStr = trim($accounts);
        $accountsStr = str_ireplace([",",";","，","；"],";",$accountsStr);
        $accounts = explode(';', $accountsStr);
        $accounts = array_unique($accounts);
        foreach ($accounts as $k => $v) {
            if(empty($v)){
               unset($accounts[$k]);
               continue; 
            }

//            if (!isValidEmail($v) && !isValidMobile($v)) {
//                return array("status" => false, 'message' => $v . '不合法，请修正');
//            }

            if(!isValidStudNum($v)){
                return array("status" => false, 'message' => $v . '存在不合法的学号，请修正');
            }

            if ($type == 'email' && !isValidEmail($v)) {
                unset($accounts[$k]);
                continue;
            }

            if ($type == 'mobile' && !isValidMobile($v)) {
                unset($accounts[$k]);
                continue;
            }
        }
        $accounts = array_values($accounts);
        return array("status" => true, 'message' => '', "data" => $accounts);
    }

    /**
     * 解析excel
     * return array 
     * @author 谈海涛 2015-12-16
     */
    public static function parseExcel($filePath, $headItems) {
        $filePath = str_replace(realpath(__ROOT__), "", $filePath);
        if (empty($filePath))
            return array("status" => false, 'message' => self::$codeArr['1015']);
        $filePath = realpath(__ROOT__) . $filePath;
        if (!file_exists($filePath))
            return array("status" => false, 'message' => self::$codeArr['1017']);

        $currentSheet = self::getPhpExcel($filePath);
        $allRows = $currentSheet->getHighestRow();

        $excelHead = array('A' => 'account');
        $headItems = $headItems ? $headItems : $excelHead;
        
        $successUser = array();
        for ($currentRow = 2; $currentRow <= $allRows; $currentRow++) {
            $field = array();
            foreach ($headItems as $k => $v) {
                $field[$v] = filterExcelTrim((string) $currentSheet->getCellByColumnAndRow(ord($k) - 65, $currentRow)->getValue());
                if (!$field[$v]) {
                    continue;
                }
                if ($v == 'account' && !isValidEmail($field[$v]) && !isValidMobile($field[$v])) {
                    $memberRows = array("status" => false, 'message' =>'第'.$currentRow.'行'. $field[$v] . '不合法，请修正');
                    break;
                }
            }
            if($field[$v]) $successUser[] = $field;
        }
        if ($memberRows)
            return $memberRows;
        if(!$successUser)
            return array("status" => false, 'message' => '请从第二行起开始填写帐号',);
        return array("status" => true, 'message' => '', 'data' => $successUser);
    }

    /*
     * 获取excel表对象
     * @author 谈海涛 2015-12-16
     */
    public static function getPhpExcel($filePath) {
        if ($filePath) {
            require_once("./Vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php");
            //获取excel文件
            $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
            $currentSheet = $objPHPExcel->getSheet(0);
            return $currentSheet;
        }
    }

    /*
     * 上传excel文件
     * @author 谈海涛 2015-12-16
     */
    public static function uploadExcel($param = array()) {
        $options = array(
            "name" => "",
            "tmp_name" => "",
            "size" => 0,
        );
        $options = array_merge($options, $param);
        extract($options);
        $info = pathinfo($name);
        $fileType = $info['extension'];
        if (empty($_FILES['file']['name']) || $_FILES['file']['error'] != 0) {
            return array("status" => false, 'message' => self::$codeArr['1012']);
        }
        if (strtolower($fileType) != "xls" && strtolower($fileType) != "xlsx") {
            return array("status" => false, 'message' => self::$codeArr['1013']);
        }
        if ($size > 10485760) {
            return array("status" => false, 'message' => self::$codeArr['1014']);
        }
        /* 设置上传路径 */
        $savePath = getParameter('redcloud/upload/public_directory') . "/tmp/";
        /* 以时间来命名上传的文件 */
        $str = date('Ymdhis');
        $file_name = $str . "." . $fileType;
        /* 是否上传成功 */
        if (copy($tmp_name, $savePath . $file_name)) {
            $filePath = str_replace(realpath(__ROOT__), "", $savePath . $file_name);
            return array("status" => true, 'message' => self::$codeArr['1010'], 'filePath' => $filePath, 'fileName' => $name);
        } else {
            return array("status" => false, 'message' => self::$codeArr['1011']);
        }
    }

    /**
     * 创建任务
     * string $code, string $strId ,array $data
     * return bool
     * @author 谈海涛 2015-12-16
     */
    public static function createTask($code, $strId, $data) {
        if (!$code || !$strId || empty($data))
            return array("status" => false, "message" => "参数错误");
        
        #添加父项到列表
        $microtime = self::addTaskList($code, $strId);
        if (!$microtime)
            return array("status" => false, "message" => "创建任务失败");
        
        self::$code = $code;
        self::$strId = $strId;
        self::$microtime = $microtime;

        #添加父项信息
        self::addTaskInfo($data);

        #循环添加子项和子项信息
        foreach ($data["rowData"] as $k => $v) {
            $itemMicrotime = self::addTaskItem();
            self::addTaskItemInfo($itemMicrotime, $v, $k+1);
        }
        
        $r = array("status"=>true,'message'=>'添加成功','data'=>array('code'=>$code,'strId'=>$strId,'microtime'=>$microtime));
        $batTask = C("BAT_TASK");
        #是否自动开始任务
        if ($batTask[$code]['autoBegin'] == 1) {
            $r = self::startTask(self::$code,self::$strId,self::$microtime);
        }
        return $r;
    }

    /**
     * 中止任务
     * string $code, string $strId ,string $microtime
     * return bool 
     * @author 谈海涛 2015-12-16
     */
    public static function stopTask($code, $strId, $microtime) {
        if (!$code || !$strId || !$microtime)
            return array("status" => false, "message" => "参数错误");
        
        $info['status'] = 'stop';
        $info["lastModifyTm"] = time();
        self::updateTaskInfo($code, $strId, $microtime, $info);
        return array("status"=>true,'message'=>'操作成功','data'=>array('code'=>$code,'strId'=>$strId,'microtime'=>$microtime));
    }

    /**
     * 开启任务
     * string $code, string $strId ,string $microtime
     * return bool 
     * @author 谈海涛 2015-12-16
     */
    public static function startTask($code, $strId, $microtime) {
        if (!$code || !$strId || !$microtime)
            return array("status" => false, "message" => "参数错误");

        #修改父项的状态和时间
        $fields["status"] = "doing";
        $fields["lastModifyTm"] = time();
        self::updateTaskInfo($code,$strId,$microtime,$fields);

        $param = array();
        $param["code"] = $code;
        $param["strId"] = $strId;
        $param["microtime"] = $microtime;
        
        #获取子项列表
        $itemArr = self::getTaskDetailList($param);
        $itemArr = array_reverse($itemArr);
       
        #循环子项添加至队列
        foreach ($itemArr as $item) {
            #判断 父项中途停止 子项未开始
            $status = self::getTaskStatus($code, $strId, $microtime);
            if( ($status == "doing") && ($item["status"] == "nostart") ){
                $param['itemMicrotime'] = $item["itemMicrotime"];
                QueueService::addJob(array('jobName' => 'execSubTask', 'param' => $param));
            }
        }
        return array("status"=>true,'message'=>'添加成功','data'=>array('code'=>$code,'strId'=>$strId,'microtime'=>$microtime));
    }

}
