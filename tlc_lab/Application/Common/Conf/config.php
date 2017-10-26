<?php

#$host = $_SERVER['HTTP_X_FORWARD_HOST'] ? $_SERVER['HTTP_X_FORWARD_HOST'] : $_SERVER['HTTP_HOST'];
$host = $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['HTTP_X_FORWARD_HOST'];
if(($port=$_SERVER['SERVER_PORT'])!=80) $host .= ":".$port;
    $domainList = array('www', 'tlc');
if (!empty($host)) {
    $domainList = explode('.', $host, 2);
}
$domainList[1] = '.' . $domainList[1];
#print_r($_SERVER);exit;
#print_r($domainList);exit;

return array(
    //'配置项'=>'配置值'
    'DEFAULT_MODULE' => 'Home',
    'DEFAULT_CONTROLLER' => 'Default',
    'MODULE_ALLOW_LIST' => array('Home', 'BackManage', 'Mobile', 'Cli', 'Center', 'Soap', 'Wclass', 'Content', 'Course', 'Money', 'My', 'Poster', 'System', 'User','Widget',"Chat","Pc","Generalize",'Sell','AccessControl'),
    'MODULE_BACKGROUND' => array('BackManage', 'Center'),  #后台模块 qzw 2016-01-19
    'URL_MODEL' => 2,
    'URL_ROUTER_ON' => true,
    #404页面：qzw 2015-09-22 同时nginx配置里加三项：1, error_page 404  /404.html; 和 2, fastcgi_intercept_errors on; 3, rewrite /404.html xxxxxxxx.html
    'ERROR_PAGE' => '/404.html',
    'SITE_URL' => SITE_URL, #qzw 2015-09-21
    'VAR_MODULE' => 'm', // 默认模块获取变量
    'VAR_CONTROLLER' => 'c', // 默认控制器获取变量
    'VAR_ACTION' => 'a', // 默认操作获取变量
    'TMPL_ENGINE_TYPE' => 'Twig',
    'DEFAULT_ACTION' => 'indexAction', // 默认操作名称
    'URL_HTML_SUFFIX' => '', // URL伪静态后缀设置
    'LANG_SWITCH_ON' => true,// 开启语言包功能
    'LANG_LIST'        => 'zh-cn', // 允许切换的语言列表 用逗号分隔
    'LANG_AUTO_DETECT' => false, // 自动侦测语言 开启多语言功能后有效
    /* 日志 */
    'DEBUG' => true,
    'SHOW_ERROR_MSG' => true,
    'LOG_RECORD' => false, // 开启日志记录
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR,SQL', // 只记录EMERG ALERT CRIT ERR 错误
    //加密秘钥
    'SECURE_CODE' => 'cloud_1024',
    //授权中心站管理员登录分站时使用的token
    'AUTH_TOKEN' => 'lmsk9KxSXLnkJ1obZcoeXKuLId2dEV1aSfXuQ6d62lltyVkBXcRc3BiLXq7GNGQy/xyGZWfc',
    //'DEFAULT_FILTER'        =>  '', // 默认参数过滤方法 用于I函数...
    /* 扩展配置 */
    'LOAD_EXT_CONFIG' => 'config.api,config.param,config.rbac,config.route_home,config.route_admin,config.code,config.mobile,config_mobileTest,config.sms,config.push,config.queue,config.common,config.env,listen,config.level,config.tls,config.pc',
    /* 模板 */
    'TMPL_TEMPLATE_SUFFIX' => '.html.twig',
    /* 导入 */
//    'AUTOLOAD_NAMESPACE' => array(
//        'Lib'     => APP_PATH.'Common/Lib',
//    )
    'secret' => 'dzwkrvm9fsgsgookwkkowc4k80gc4wo', //秘钥
    'VAR_ACTION_SFX' => 'Action', //控制器后缀 U()使用
    'version' => array(
        'asset' => ''
    ),
    'BASE_SITE_DOMAIN' => $domainList[1],
    'DEFAULT_SUB_DOMAIN' => $domainList[0],
    'FULL_DOMAIN' => $domainList[0] . $domainList[1],
    'COOKIE_FULLE_DOMAIN' => $domainList[0] . preg_replace("/[:]+\d+/", "", $domainList[1]),
//    'COOKIE_FULLE_DOMAIN' =>'localhost',
        'alipay_config' => array(
        'partner' => '2088011585980308', //这里是你在成功申请支付宝接口后获取到的PID；
        'key' => 'e1g8om2umvplo7z6aup40keyiel1mpd0', //这里是你在成功申请支付宝接口后获取到的Key
        'seller_email' => 'sunyang@redcloud.com', //这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
        'sign_type' => strtoupper('MD5'),
        'input_charset' => strtolower('utf-8'),
        'cacert' => VENDOR_PATH. 'Alipay/cacert.pem',
        'private_key_path'=> VENDOR_PATH. 'Alipay/rsa_private_key.pem',
        'ali_public_key_path' => VENDOR_PATH. 'Alipay/rsa_public_key.pem',
        'transport' => 'http',
    ),
    'alipay' => array(
        //这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
        'notify_url' => '',
        //这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
        'return_url' => '',
        //支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
        'successpage' => '',
        //支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
        'errorpage' => '',
    ),
    'CALLBACK_URL'=>"http://center.gkk.cn",
    'BLANK_LIST'=>array(
        1=>'中国工商银行',
        2=>'中国农业银行',
        3=>'中国银行',
        4=>'中国建设银行',
        5=>'中国交通银行',
        6=>'中国邮政储蓄银行',
        7=>'中信银行',
        8=>'中国光大银行',
        9=>'华夏银行',
        10=>'中国民生银行',
        11=>'广发银行',
        12=>'招商银行',
        13=>'兴业银行',
        14=>'浦发银行',
        15=>'上海银行',
        16=>'北京银行',
        17=>'汇丰银行',
        18=>'支付宝',
        19=>'微信支付'
    ),
    'TEMPLATE_TPL'=>'Theme',
    'TEMPLATE_DEFAULT_TPL'=>'View'
);
