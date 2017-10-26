<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
ini_set("log_errors", "On");
ini_set("error_log", "/data/tlc_lab/Logs/php_error.log");

// 应用入口文件
error_reporting(E_ALL);
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

//加载本地配置环境
$local_env = array();
if(file_exists('./env.php')){
    $local_env = include('./env.php');
}
//加载版本信息
if (file_exists('./version.php')) {
	$version = include('./version.php');
	define('WEB_VERSION', $version['version'] ? $version['version'] : '');
}

define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
if(IS_CLI) define('APP_DEBUG', True);

else {
    define('APP_DEBUG', isset($local_env['APP_DEBUG']) ? $local_env['APP_DEBUG'] : false);
}

define('LOCAL_MODEL',True);

//授权h5访问 qzw 2016-4-20
if(APP_DEBUG && isset($local_env['ALLOW_ORGIN']) && $local_env['ALLOW_ORGIN']) {
    header("Access-Control-Allow-Origin: ". $local_env['ALLOW_ORGIN']);
}

// 定义应用目录
define('APP_PATH','./Application/');

define('TEMPLATE_PATH', './Public/Template/');
$_root  =   rtrim(realpath('.'),'/');
define('__ROOT__',  (($_root=='/' || $_root=='\\') ? '' : $_root));
define('SITE_PATH', __ROOT__);

//定义运行缓存目录
$runtimeDirName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'cli_env'; #各站点各使用自己的 qzw 2015-09-21
if(!empty($local_env['RUNTIME_PATH'])){
    define('RUNTIME_PATH', rtrim($local_env['RUNTIME_PATH'],DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $runtimeDirName . DIRECTORY_SEPARATOR);
}else {
    define('RUNTIME_PATH', './Runtime/' . $runtimeDirName . DIRECTORY_SEPARATOR);
}

//定义日志存放目录
if(!empty($local_env['LOG_PATH'])){
    define('LOG_PATH', rtrim($local_env['LOG_PATH'],DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
}else {
    define('LOG_PATH', './Logs/');
}

//定义静态HTML文件存放目录
if(!empty($local_env['STATIC_HTML_PATH'])){
    define('STATIC_HTML_PATH', rtrim($local_env['STATIC_HTML_PATH'],DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
}else {
    define('STATIC_HTML_PATH', realpath(__ROOT__) . DIRECTORY_SEPARATOR . 'Html/');
}

//定义数据目录，即文件存放目录
if(!empty($local_env['DATA_PATH'])){
    define('DATA_PATH', rtrim($local_env['DATA_PATH'],DIRECTORY_SEPARATOR));
}else {
    define('DATA_PATH', "%kernel.root_dir%");
}

//定义网盘存储目录，即网盘文件存放目录
if(!empty($local_env['REDISK_DATA_PATH'])){
    define('REDISK_DATA_PATH', rtrim($local_env['REDISK_DATA_PATH'],DIRECTORY_SEPARATOR));
}else {
    define('REDISK_DATA_PATH', realpath(__ROOT__) . DIRECTORY_SEPARATOR . "redisk/data/");
}

//定义静态文件获取URL前缀
if(!empty($local_env['DATA_FETCH_URL_PREFIX'])){
    define('DATA_FETCH_URL_PREFIX', trim($local_env['DATA_FETCH_URL_PREFIX'],DIRECTORY_SEPARATOR));
}else {
    define('DATA_FETCH_URL_PREFIX', "resource");
}

//定义图片文件存放顶级目录
if(!empty($local_env['PICTURE_PARENT_PATH'])){
    define('PICTURE_PARENT_PATH', rtrim($local_env['PICTURE_PARENT_PATH'],DIRECTORY_SEPARATOR));
}else{
    define('PICTURE_PARENT_PATH', "");
}


#qzw 2015-2-16
if(0 && in_array($_SERVER['HTTP_HOST'], array('test.gkk.cn', 'test-center.gkk.cn')) 
        && !(preg_match('/^\/Mobile\//', $_SERVER['REQUEST_URI']) 
        || preg_match('/^\/app_down\//', $_SERVER['REQUEST_URI'])
        || preg_match('/^\/Pc\/Index/', $_SERVER['REQUEST_URI'])
        || preg_match('/^\/Pc\/PcTest/', $_SERVER['REQUEST_URI'])
        || preg_match('/^\/Chat/', $_SERVER['REQUEST_URI']))) {
    if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!='admin' || $_SERVER['PHP_AUTH_PW']!='redcloud612!#') {
        authenticate();
    }
}

#try{
//自动加载vendor目录下的第三方类库
require  './Vendor/autoload.php';
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';
#}catch(\Think\Exception $e){
#    echo 'error:';
#    die($e->getMessage());
#}

// 亲^_^ 后面不需要任何代码了 就是如此简单
function authenticate() {
  header('WWW-Authenticate: Basic realm="ask qzw"');
  header('HTTP/1.0 401 Unauthorized');
  echo "You must enter a valid login ID and password to access this resource\n";
  exit;
}

