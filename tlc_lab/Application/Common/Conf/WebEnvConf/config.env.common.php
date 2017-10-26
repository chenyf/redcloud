<?php
/**
 * 本配置文件只放受部署环境影响的配置，修改后告之 钱志伟，线上需要手动修改
 * @author 钱志伟 2015-06-10
 */
define('RUN_ENVIRONMENT', 0); #0=>开发环境 1=>线上环境
define('PUSH_IOS_DEPLOY', 1); #可取值1（开发状态）和2（生产状态）仅iOS推送使用
define('POLYV_DIR_ID', 1433325097070); #保利威视目录id
define('SOHU_LABEL_ID', 10475); #sohu邮件标签id
define('COMMON_HOST', '127.0.0.1');  #公共服务器地址
define('WEB_DOMAIN','http://tlc.cuc.edu.cn'); #域名
define('PAN_URL',rtrim(WEB_DOMAIN,"/") . "/redisk/"); #网盘地址
define('ONLY_KEPP_COMMON_THEME', 1); #只保留通用主题 0=>不是 1=>是
define('DB_HOST',           '127.0.0.1');
define('DB_USER',           'root');
define('DB_PWD',            'redcloud');
define('DB_NAME',           'tlc_lab');
//define('AUTH_URL','http://eteaching.cuc.edu.cn/auth/auth');
//define('AUTH_APPID','IQPR5L');
//define('AUTH_SECRET_KEY','3db98fdb5d8329327ba130f8c8203e0fef09432e');
define('MEMCACHE_PORT',     '11211');
define('REDIS_PORT', 		'6379');
define('REDIS_PWD',			'tlc');
define('STATIC_KEY_BAIDU','6669f96d8fcfdd1d3fa9479b0ca24e1e');
define('APP_XHPROF',        false);
define('APP_XHPROF_AJAX',   false);
define('SHOW_PAGE_TRACE',   false);
define('VIRTUAL_LAB_STATUS',  0);     #虚拟实验室   0=>关闭 1=>开启
define('SHOW_PUBLIC_COURSE',        true);
define('CLOUD_OAUTH_DOMAIN', 'tlc.cuc.edu.cn'); #第三方登录认证域名
define('CLOUD_OAUTH_STATUS', 1); #第三方登录认证状态
define('CLOUD_OAUTH_SESSION_EXPIRE', 43200); #第三方登录认证session 超时时间
define('SQL_LOG', true); #是否显示sql日志(Logs/sql.log或Logs/sql-xxx.gkk.cn) qzw
define('SQL_LOG_MERGE', false); #sql日志拆分，false=>Logs/sql-xxxx.log, true=>Logs/sql.log
