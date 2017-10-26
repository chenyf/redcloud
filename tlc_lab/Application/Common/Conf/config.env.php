<?php
/**
 * 各网站配置环境调度
 * @author 钱志伟 2015-07-06
 */

#默认webcode 临时处理 by 钱志伟 2015-07-10
$dftWebCode = getenv('WEBCODE') ? getenv('WEBCODE') : (in_array($_SERVER['HOSTNAME'], array('uplook-test1', 'wt1.nazhengshu.com')) ? 'demo' : 'center');
define('DFT_WEBCODE', $dftWebCode);
if(IS_CLI) echo 'DFT_WEBCODE: '.DFT_WEBCODE.PHP_EOL;
define('WEBSITE_CODE', 'tlc');

#echo '<pre>';
#print_r($_SERVER);
#echo WEBSITE_CODE;exit;


$webEnvFile = \Common\Lib\EnvCfgManage::getEnvCfgFile(WEBSITE_CODE);
//配置文件不存在时，如果为允许webcode，可以走公共配置
if(!file_exists($webEnvFile)){
    $webEnvFile = \Common\Lib\EnvCfgManage::getEnvCfgFile('common');
}
include_once $webEnvFile;

#默认环境设置
$isOnline = !in_array($_SERVER['SERVER_ADDR'], array('192.168.3.6'));
define('DEFAULT_ENV_CFG', serialize(array(
    #公共私有库
//    'DB_HOST'           => COMMON_HOST,
//    'DB_NAME'           => 'test_cloud',
//    'DB_USER'           => 'uplooksns',
//    'DB_PWD'            => 'uplook.com.cn',
    #中心库
//    'DB_CENTER_HOST'    => COMMON_HOST,
//    'DB_CENTER_NAME'    => 'center_cloud',
//    'DB_CENTER_USER'    => 'uplooksns',
//    'DB_CENTER_PWD'     => 'uplook.com.cn',
//    'DB_CENTER_TYPE'    => 'mysql',
//    'DB_MONITOR_HOST'   => COMMON_HOST,
//    'DB_MONITOR_NAME'   => 'center_cloud',
//    'DB_MONITOR_USER'   => 'uplooksns',
//    'DB_MONITOR_PWD'    => 'uplook.com.cn',
    'MEMCACHE_HOST'     => COMMON_HOST,
    'MEMCACHE_PORT'     => 11212,
    'REDIS_HOST'        => COMMON_HOST,
    'REDIS_PWD'         => 'tlc',
    'REDIS_PORT'        => 6380,
    'MONGO_HOST'        => COMMON_HOST,
    'MONGO_PWD'         => '123456',
    'COOKIE_DOMAIN'     => C('FULL_DOMAIN'),
    'ONLY_KEPP_COMMON_THEME'=> 0,
    'ANDROID_APIKEY'    => '18SQVCXGxx8rbaNhgV9RGoSU',
    'ANDROID_SECRETKEY' => 'y9GyxQQkT7Ntqyz59ioGDcs0fBGGSYkw',
    'IOS_APIKEY'        => 'VEjv10qlTGkGbQn0rIm0OQcB',
    'IOS_SECRETKEY'     => 'TqLC5vq1wVVjRPX27LjquvsMf8jAsY0m',
    'APP_XHPROF'        => false,
    'APP_XHPROF_AJAX'   => false,
    'QCOS_ENV'          => 'dev',
    'VIRTUAL_LAB_STATUS'=> 0,       #虚拟实验室 qzw
    'CLOUD_OAUTH_STATUS'=>0,//第三方登录认证域名
    'CLOUD_OAUTH_SESSION_EXPIRE' => 43200,//第三方登录认证session 超时时间
    'CLOUD_OAUTH_DOMAIN'        => 'oauth.gkk.com', //第三方登录认证域名
    'SQL_LOG'           => false,   #是否显示sql日志(Logs/sql.log或Logs/sql-xxx.gkk.cn) qzw
    'SQL_LOG_MERGE'     => true,    #common/lib/WebCode.class.php中sql 是否合并，false=>依据域名拆分 qzw
    'SHOW_PAGE_TRACE'   => false,   #显示tp调试信息按钮
    'XHPROF_PATH'       => $isOnline ? '/server/data0/xhprof/' : '/usr/local/nginx-1.1.0/html/xhprof/',
    'XHPROF_URL'        => $isOnline ? 'http://turing.gkk.cn' : 'http://192.168.3.6:84',
    'SHOW_PUBLIC_COURSE'=>true
)));
#未配置变量，进行默认设置
foreach(unserialize(DEFAULT_ENV_CFG) as $cfgName=>$cfgVal){
    defined($cfgName) or define($cfgName, $cfgVal);
}
#默认数据配置模板
define('DEFAULT_DB_CFG', serialize(array(
    'DB_TYPE'              => 'mysql',     // 数据库类型
    'DB_HOST'              => '', // 服务器地址
    'DB_NAME'              => '',          // 数据库名
    'DB_USER'              => '',      // 用户名
    'DB_PWD'               => '',          // 密码
    'DB_PORT'              => '3306',        // 端口
    'DB_PREFIX'            => '',    // 数据库表前缀
    'DB_CENTER_TYPE'       => 'mysql',
)));

return array(
        #运行环境
        'RUN_ENVIRONMENT'      => RUN_ENVIRONMENT, #0=>开发环境 1=>线上环境
        'PUSH_IOS_DEPLOY'      => PUSH_IOS_DEPLOY, #可取值1（开发状态）和2（生产状态）仅iOS推送使用
        'POLYV_DIR_ID'         => POLYV_DIR_ID, #保利威视目录id
        'WEBSITE_CODE'         => WEBSITE_CODE,   #网站代码，不能相同
        'CENTER_CODE'          => 'demo,center,test-center,center-v2', #中心网站代码，不能相同
        'SQL_LOG'              => SQL_LOG, #是否显示sql日志(Logs/sql.log或Logs/sql-xxx.gkk.cn) qzw
        'SQL_LOG_MERGE'        => SQL_LOG_MERGE, #common/lib/WebCode.class.php中sql 是否合并，false=>依据域名拆分 qzw
        'CLOSE_USER_WRITE'     => 0, #关闭用户写 by qzw 2016-1-30
	/* 数据库设置 */
	'DB_TYPE'              => 'mysql',     // 数据库类型
	'DB_HOST'              => DB_HOST, // 服务器地址
	'DB_NAME'              => DB_NAME,          // 数据库名
	'DB_USER'              => DB_USER,      // 用户名
	'DB_PWD'               => DB_PWD,          // 密码
	'DB_PORT'              => '3306',        // 端口
	'DB_PREFIX'            => '',    // 数据库表前缀
    /*中心DB设置*/
//    'DB_CENTER'            => array(
//        'DB_TYPE'              => DB_CENTER_TYPE,     // 数据库类型
//        'DB_HOST'              => DB_CENTER_HOST, // 服务器地址
//        'DB_NAME'              => DB_CENTER_NAME,          // 数据库名
//        'DB_USER'              => DB_CENTER_USER,      // 用户名
//        'DB_PWD'               => DB_CENTER_PWD,       // 密码
//        'DB_PORT'              => '3306',        // 端口
//        'DB_PREFIX'            => '',    // 数据库表前缀
//        'DB_NUM'               => 2,    // 数据库号
//    ),
//    'DB_MONITOR_ACCOUNT' => array(
//        'DB_TYPE'           => 'mysql',
//        'DB_HOST'              => DB_MONITOR_HOST, // 服务器地址
//        'DB_NAME'              => DB_MONITOR_NAME,          // 数据库名
//        'DB_USER'              => DB_MONITOR_USER,      // 用户名
//        'DB_PWD'               => DB_MONITOR_PWD,       // 密码
//        'DB_PORT'              => '3306',        // 端口
//        'DB_PREFIX'            => '',    // 数据库表前缀
//        'DB_NUM'               => 999,    // 数据库号
//    ),
	/* 缓存 */
	'DATA_CACHE_TYPE'      => 'Memcache',
	'MEMCACHE_HOST'        => MEMCACHE_HOST,
	'MEMCACHE_PORT'        => MEMCACHE_PORT,
	'DATA_CACHE_TIME'      => '3600',
        /*Redis设置*/
        'REDIS' => array(
            'taskQueue' => 'redis://user:'.REDIS_PWD.'@'.COMMON_HOST.':'.REDIS_PORT.'/4',
        ),
        'REDIS_HOST'           => REDIS_HOST, //主机
        'REDIS_PORT'           => REDIS_PORT, //端口
        'REDIS_CTYPE'          => 1, //连接类型 1:普通连接 2:长连接
        'REDIS_TIMEOUT'        => 0, //连接超时时间(S) 0:永不超时
        'REDIS_PWD'            => REDIS_PWD, //密码
        /*sohu邮件标签*/
        'SOHU_API_USER'        => 'gkk_mail_bat',
        'SOHU_API_KEY'         => 'FFa9o3rD8p7u16jV',
        'SOHU_FROM_NAME'       => '云课堂',
        'SOHU_FROM'            => 'no-reply@cloudmail.gkk.cn',
        'SOHU_LABEL_ID'        => SOHU_LABEL_ID,
		//Cookie
        'COOKIE_HTTPONLY'      => true,
        'COOKIE_PREFIX'        => 'cloud_',
        'COOKIE_EXPIRE'        => 7 * 24 * 60 * 60,
        'COOKIE_PATH'          => '/',
        'COOKIE_DOMAIN'        => COOKIE_DOMAIN,
        /* 日志设置 */
        'LOG_RECORD'            =>  false,   // 默认不记录日志
        'LOG_TYPE'              =>  'File', // 日志记录类型 默认为文件方式
        'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR',// 允许记录的日志级别
        'LOG_FILE_SIZE'         =>  2097152,	// 日志文件大小限制
        'LOG_EXCEPTION_RECORD'  =>  false,    // 是否记录异常信息日志
        'LOG_FILE_MODULE'       => 'home,admin,mobile,cli',
        /*Mongo数据库配置*/
        'MONGO_HOST'		=> MONGO_HOST, //主机
        'MONGO_PORT'		=>27017, //端口
        'MONGO_USER'            =>'demo',
        'MONGO_PWD'             => MONGO_PWD, //密码
        'MONGO_DBNAME'          =>'cloud',
        /*移动推送*/
        'PUSH_CFG' => array(
            #android
            'android' => array(
                'apiKey'    => ANDROID_APIKEY,
                'secretKey' => ANDROID_SECRETKEY,
                'platform'  => 'android',
                'pkg'       => array(
                    'classPkg'=>'#Intent;action=android.intent.action.MAIN;category=android.intent.category.LAUNCHER;launchFlags=0x10000000;component=com.coder.redcloud.activity/.MyClassActivity;end',
                )
            ),
            #ios
            'ios' => array(
                'apiKey'    => IOS_APIKEY,
                'secretKey' => IOS_SECRETKEY,
                'platform'  => 'ios',
                'pkg'       => array()
            ),
        ),
        'APP_XHPROF'        => APP_XHPROF,
        'APP_XHPROF_AJAX'   => APP_XHPROF_AJAX,
        'XHPROF_PATH'       => XHPROF_PATH,
        'XHPROF_URL'        => XHPROF_URL,
	// 显示页面Trace信息
	'SHOW_PAGE_TRACE'           => SHOW_PAGE_TRACE,
        //是否开启公共课
        'SHOW_PUBLIC_COURSE'        => SHOW_PUBLIC_COURSE,
        //对iOS是否显示二维码图片
        'SUPPORT_IOS_SCAN_PIC'      => 1,
        /* 腾讯云Cos文件上传配置 */
        /* 项目名*/
         'QCOS_BUCKETNAME' => "cloud",
        //COS   项目/{QCOS_ENV}/{webcode}/{业务}/ 
        'QCOS_ENV' =>  QCOS_ENV,
         /* 项目id */
         'QCOS_APPID' => "10011123",
         /* 密钥SecretID */
         'QCOS_SECRETID' => "AKIDaZysHzwQBoe8sWGKCaD4UPuIGqyn536x",
         /* 密钥SecretKey */
         'QCOS_SECRETKEY' => "vnrUxcznD6JxpH2tC0H9dUhgEIenPfsh",
         'QCOS_API_IMAGE_END_POINT' => "http://web.image.myqcloud.com/photos/v1/",
         'QCOS_API_VIDEO_END_POINT' => "http://web.video.myqcloud.com/videos/v1/",
         'QCOS_API_COSAPI_END_POINT' => "http://web.file.myqcloud.com/files/v1/",
        //对Android是否显示二维码图片
        'SUPPORT_ANDROID_SCAN_PIC'  => 1,
        'VIRTUAL_LAB_STATUS'        => VIRTUAL_LAB_STATUS, //虚拟实验室
        'CLOUD_OAUTH_DOMAIN'        => CLOUD_OAUTH_DOMAIN, //第三方登录认证域名
        'CLOUD_OAUTH_STATUS'        => CLOUD_OAUTH_STATUS, //第三方登录认证状态
        'CLOUD_OAUTH_SESSION_EXPIRE'        => CLOUD_OAUTH_SESSION_EXPIRE, //第三方登录认证session 超时时间
        //是否允许redcloud同步至云课堂
        'CLOUD_COURSE_SYNC'         => 1,
        'REDCLOUD_IMPORT_ALLOW_WEBCODE' => array('redcloud', 'test', 'test1', 'test2'),
);
