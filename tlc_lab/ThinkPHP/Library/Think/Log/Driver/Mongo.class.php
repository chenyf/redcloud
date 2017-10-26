<?php

// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Think\Log\Driver;

class Mongo {
    
    protected $config = array(
        'log_time_format' => ' c ',
        'log_file_size' => 2097152,
        'log_path' => '',
    );
    // 当前连接ID
    protected $_linkID    = null;
    protected $_dbConn    = null;
    protected $_mongoConfig = null;
    // 实例化并传入参数
    public function __construct($config = array()) {
        if ( !class_exists('mongoClient') ) {
            E(L('_NOT_SUPPERT_').':mongoClient');
        }
      
       
        $this->config = array_merge($this->config, $config);
        $this->getMongoConfig();
    }
    
    /**
     * 获取mongo的连接配置
     * @return \Think\Log\Driver\Mongo
     */
    public function getMongoConfig(){
        $mongoConfig['hostname'] = C('MONGO_HOST');
        $mongoConfig['database']= C('MONGO_DBNAME');
        $mongoConfig['username']= C('MONGO_USER');
        $mongoConfig['password'] =C('MONGO_PWD');
        $mongoConfig["hostport"] = C("MONGO_PORT");
        $this->_mongoConfig = $mongoConfig;
    }
    /**
     * 初始化数据库连接
     * @access protected
     * @return void
     */
    protected function initConnect() {
        // 默认单数据库
        if (!$this->_linkID)
            $this->_linkID = $this->connect();
        
        return $this->_linkID;
    } 
      /**
     * 选择数据库
     * @param type $name
     */
    public function selectMongoDb($name=""){
        if($this->_dbConn != false) return fasle;
        $dbname = $name ? $name:C('MONGO_DBNAME');
        $this->_dbConn =  $this->_linkID->selectDB($dbname);
       
    }
      /**
     * 选择集合表
     * @author ZhaoZuoWu 2015-01-13
     */
    public function selectCollection($name){
         $collection= $this->_dbConn->selectCollection($name);
         return $collection;
    }
    /**
     * 连接数据库方法
     * @access public
     */
    public function connect($config = '') {
        if (!isset($this->linkID)) {
            if (empty($config))
             $config =$this->_mongoConfig;
            $host = 'mongodb://' . ($config['username'] ? "{$config['username']}" : '') . ($config['password'] ? ":{$config['password']}@" : '') . $config['hostname'] . ($config['hostport'] ? ":{$config['hostport']}" : '') . '/' . ($config['database'] ? "{$config['database']}" : '');
             $this->linkID = new \mongoClient($host);
            try {
                $this->linkID = new \mongoClient($host);
            } catch (\MongoConnectionException $e) {
                E($e->getmessage());
            }
        }
        return $this->linkID;
    }

    /**
     * 日志写入接口
     * @access public
     * @param string $log 日志信息
     * @param string $destination  写入目标
     * @return void
     */
     public  function write($log, $destination = '',$level= "") {
        $this->initConnect(false); //初始化连接Mongo数据库
        $this->selectMongoDb(); //初始化连接Mongo数据库
        $collectionName ="log_".date("ymd");
        $collection = $this->selectCollection($collectionName);
        $now = date($this->config['log_time_format']);
        $destPath = pathinfo($destination, PATHINFO_DIRNAME);
        $moduleName = str_replace(LOG_PATH, "" , $destPath);
        $moduleName = strtolower($moduleName);
        $logModule = C("LOG_FILE_MODULE");
        $logLevel = C("LOG_LEVEL");
        $logModuleList = explode(",",$logModule);
        $logLevelList = explode(",",$logLevel);
        if(!in_array($moduleName, $logModuleList)){
            $moduleName = "other";
        }
         if(!in_array($level, $logLevelList)){
            $level = "other";
        }
        $url = $_SERVER['REQUEST_URI'] ;
        $content =  "[{$now}] " . $_SERVER['REMOTE_ADDR'] . ' ' . $url . "\r\n{$log}\r\n";
        $destination = empty($destination) ?  "":$destination;
        $map = array(
            'moduleName' =>$moduleName,
            'date' =>date("Y-m-d H:i:s",time()),
            'time' =>time(),
            'error_type' =>$level,
            'content' =>$content,
            'destination' =>$destination,
            'url'    => $url,
        );
       $rs = $collection->insert($map);
       
    }

}
