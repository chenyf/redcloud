<?php

use Common\Lib\SendMessage;

//调试LOG
function doLog($content=''){
    $logFile = C('test_log_path');
    if(!is_dir(dirname($logFile))){
        mkdir(dirname($logFile));
    }
    if(!is_file($logFile)){
        fclose(fopen($logFile,'wb'));
    }
    date_default_timezone_set("Asia/Shanghai");
    $time = "[ ".date("Y-m-d H:i:s",time()) ." ] ";
    file_put_contents($logFile,$time.$content."\r\n",FILE_APPEND);
}

//数组转对象
function array2object($array) {
    if (is_array($array)) {
        $obj = new StdClass();
        foreach ($array as $key => $val){
            if(is_array($val)){
                $obj->$key = array2object($val);
            }else {
                $obj->$key = $val;
            }
        }
    }
    else { $obj = $array; }
    return $obj;
}

//获取服务器IP地址
function getServerIp(){
    $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');

    return $host;
}

//获得配置
function getSetting($name) {
    static $settingArr = array();

    if (!$settingArr) {
        $tmpData = createService('System.Setting')->findAllSettings();
        foreach ($tmpData as $row) {
            $settingArr[$row['name']] = unserialize($row['value']);
        }
    }

    $rs = isset($settingArr[$name]) ? $settingArr[$name] : array();
    return $rs;
}

//参数替换
function paramReplace($param) {
    $param = str_replace('%kernel.root_dir%', __ROOT__, $param);
    return $param;
}

/**
 * 装饰用户信息
 * @author 钱志伟 2015-08-19
 */
function decorateUserInfo($uid, &$userInfo) {
    $uBaseInfo = getUserBaseInfo($uid);
    #添加用户性别
    $gender = getUserGender($uid);
    if ($uBaseInfo) {
        $userInfo['userName'] = (string) $uBaseInfo['nickname'];
        $userInfo['studNum'] = (string) $uBaseInfo['studNum'];
        $userInfo['userFace'] = C('SITE_URL') . \Common\Common\Url::getInstance()->getUserFaceUrl(array('uid' => $uid));
        $userInfo['email'] = (string) $uBaseInfo['email'];
        $userInfo['mobile'] = (string) $uBaseInfo['verifiedMobile'];
        $userInfo['gender'] = (string) $gender;
        $userInfo['about'] = strip_tags((string) $uBaseInfo['about']);
        return $userInfo;
    }
}

/**
/**
 * 获得系统配置参数（路径等）
 * @author  钱志伟 2015-03-26
 * @example getParameter('redcloud.disk.local_directory');
 */
function getParameter($param) {
    $param = str_replace('.', '/', $param);
    $value = C($param);
    $value = str_replace('%kernel.root_dir%', realpath(__ROOT__), $value);
    return $value;
}

function getRelativePath($param){
    $param = str_replace('.', '/', $param);
    $value = C($param);
    $value = str_replace(DATA_PATH, "/" . DATA_FETCH_URL_PREFIX, $value);
    return $value;
}

/**
 * 获取文件上传物理路径
 * @param $folder //父目录
 * -------------------------------------------------
 * $folder可选参数：public_directory,private_directory
 * -----------------------------------------------
 * @param $path //子目录
 * @return string
 */
function getUploadPath($folder, $path) {
    $path = getParameter('redcloud.upload.' . $folder) . '/' . $path;
    if (!file_exists($path)) {
        if (!mk_dir($path)) {
            E('文件夹创建失败，请检查是否有写入权限：' . $path);
        }
    }
    return $path;
}

/**
 * 获取文件访问路径
 * @param $folder //父目录
 * -------------------------------------------------
 * $folder可选参数：public_url_path,private_url_path
 * -----------------------------------------------
 * @param $path //子目录
 * @param $fileName //文件名
 * @return string
 */
function getUploadUrl($folder, $path, $fileName) {
    $url = getParameter('redcloud.upload.' . $folder) . '/' . $path . '/' . $fileName;
    return $url;
}

/**
 * 过滤Excel空格(左右和中间的空格,全角和半角空格)
 * @author fubaosheng 2015-11-13
 */
function filterExcelTrim($str) {
    #qzw edit 2015-11-19
    return str_replace(array(' ', '　'), '', $str);
    //return preg_replace("/[(\xc2\xa0)|\s|\t|\r|\n| |　]+/", "", $str);
}

/**
 * 创建ServerModel
 * @author 赵作武 2015-03-26
 */
function createService($name, $linknum = 0) {
    //static $service = array();

    list($module, $className) = explode('.', $name);
    $class = '\\Common\\Model\\' . $module . '\\' . $className;
    if (!class_exists($class))
        $class .= "Model";
    #切换数据库有影响不能缓存
//	if (!isset($service[$name . '_' . $linknum])) {
//		$service[$name . '_' . $linknum] = new $class($linknum);
//	}
//	return $service[$name . '_' . $linknum];

    return new $class($linknum);
}

/**
 * 生成秘钥
 */
function generateCsrfToken($intention) {
    return sha1(C("secret") . $intention . getSessionId());
}

/**
 * 判断秘钥是否相等（$token）
 */
function isCsrfTokenValid($intention, $token) {
    return $token === generateCsrfToken($intention);
}

/**
 * 获取sessionId
 */
function getSessionId() {
    if (version_compare(PHP_VERSION, '5.4', '>=')) {
        if (PHP_SESSION_NONE === session_status()) {
            session_start();
        }
    } elseif (!session_id()) {
        session_start();
    }
    return session_id();
}

/**
 * 判断超级管理员是否是当前站的
 * @author fubaosheng 2015-07-23
 */
function currentSuperAdmin() {
    $currentUser = D('User\CurrentUser');
    if (!$currentUser['id'])
        return false;

    $roles = $currentUser->getRoles();
    if (in_array("ROLE_SUPER_ADMIN", $roles)) {
        return true;
    }
    return false;
}

/**
 * 判断管理员是否是当前站的
 * @author fubaosheng 2015-07-23
 */
function currentAdmin() {
    $currentUser = D('User\CurrentUser');
    if (!$currentUser['id'])
        return false;

    $roles = $currentUser->getRoles();
    if (in_array("ROLE_ADMIN", $roles) && !empty($currentUser['adminCategoryIds'])) {
        $adminCate = createService('Taxonomy.Category')->getNoDeleteCateById($currentUser['adminCategoryIds']);
        if (!empty($adminCate))
            return true;
    }
    return false;
}

/**
 * 同步部分数据(中心=>私有)
 * @author fubaosheng 2015-08-04
 */
function synchroData($paramArr) {
    $options = array(
        'db' => '',
        'sql' => '',
        'pk' => array(),
        'id' => 0
    );
    $options = array_merge($options, $paramArr);
    extract($options);

    if (!$db || !$sql)
        return false;

    $sql = trim($sql);
    $type = strtolower(substr($sql, 0, strpos($sql, " ")));
    $typeArr = array('insert', 'update', 'delete', 'truncate');
    if (!in_array($type, $typeArr))
        return false;

    if ($type == "insert" && !empty($pk)) {
        if (empty($id)) {
            return false;
        } else {
            $sqlArr = explode("VALUES", $sql);
            $field = explode(",", str_replace("`", "", rtrim(trim(substr($sqlArr[0], strpos($sqlArr[0], '(') + 1)), ")")));
            $increment = array_keys($pk)[0];
            if (!in_array($increment, $field)) {
                $sqlArr[0] = str_replace('(', "(`{$increment}`,", $sqlArr[0]);
                $sqlArr[1] = str_replace('(', "({$id},", $sqlArr[1]);
                $sql = implode("VALUES", $sqlArr);
            }
        }
    }

    $status = 0; //0=>同步成功，1=>同步失败
    $failMsg = '';
//    $db = strtoupper($db);
//    if($db == "PRIVATE"){
//        $host = C("DB_HOST");
//        $port = C("DB_PORT");
//        $user = C("DB_USER");
//        $pwd = C("DB_PWD");
//        $dbName = C("DB_NAME");
//    }
//    $link = mysql_connect($host.":".$port,$user,$pwd);
//    if(!$link){
//        $status = 1;
//        $failMsg = "连接失败";
//    }
//    $select = mysql_select_db($dbName,$link);
//    if(!$select){
//        $status = 1;
//        $failMsg = "选择{$dbName}数据库失败";
//    }
    #$query = mysql_query($sql,$link);
    #qzw 2015-12-17
    $query = M('user')->query($sql);
    if (!$query) {
        $status = 1;
        $failMsg = M('user')->getDbError();
    }

    return array('status' => $status, 'failMsg' => $failMsg, 'data' => $sql);
}

//目录拼接
function pathjoin(){
    if(func_num_args() <= 0){
        return "";
    }

    $path = "";
    foreach (func_get_args() as $arg){
        $arg = str_replace("\\",DIRECTORY_SEPARATOR,$arg);
        $path = rtrim($path,DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($arg,DIRECTORY_SEPARATOR);
    }

    $path = str_replace("\\",DIRECTORY_SEPARATOR,$path);

    if(strpos(SITE_PATH,"/") === 0){
        return DIRECTORY_SEPARATOR.trim($path,DIRECTORY_SEPARATOR);
    }else{
        return trim($path,DIRECTORY_SEPARATOR);
    }

}

/**
 * 判断自定义角色是否是当前站的
 * @author fubaosheng 2015-07-23
 */
function currentDefineRole() {
    $currentUser = D('User\CurrentUser');
    if (!$currentUser['id'])
        return false;

    if (!empty($currentUser['defineRoles'])) {
        $defineRoles = json_decode($currentUser['defineRoles'], true);
        $defineRole = "";
        foreach ($defineRoles as $defineRoleVal) {
            $defineRole .= $defineRoleVal[0] . ",";
        }
        $defineRole = rtrim($defineRole, ",");
        $defineCateIds = createService('Role.RoleService')->getRoleCategorysById($defineRole);
        $defineCateIds = implode($defineCateIds, ",");
        $defineCate = createService('Taxonomy.Category')->getNoDeleteCateById($defineCateIds);
        if (!empty($defineCate))
            return true;
    }
    return false;
}

/**
 * 进后台
 * @author fubaosheng 2015-07-23
 */
function goBackEnd() {
    $currentUser = D('User\CurrentUser');
    if (!$currentUser['id'])
        return false;

    if (isGranted("ROLE_SUPER_ADMIN"))
        return true;

    #判断管理员是否是当前站的
    if (currentAdmin())
        return true;
    #判断自定义角色是否是当前站的
    if (currentDefineRole())
        return true;
    return false;
}

/**
 * 权限判断
 * @param        $role
 * @param string $siteSelect (local=>当前站, all=>all)
 * @return bool
 */
function isGranted($role, $siteSelect = 'local') {
    $currentUser = D('User\CurrentUser');
    if (!$currentUser['id'])
        return false;

    $roles = $currentUser->getRoles();
    if (in_array('ROLE_SUPER_ADMIN', $roles)) {
        return true;
    }

    if (in_array($role, $roles)) {
        return true;
    }
    return false;
}

/**
 * 获得版本
 * @author 钱志伟 2015-3-31
 * @param string $packetName
 * @return string
 */
function getVersion($packetName = '') {
    return C("version.{$packetName}");
}

/**
 * 获得插件
 */
function getPlugins() {
    return array();
}

/**
 * 获得请求
 */
function getRequest() {
    static $request = null;
    static $lock = 0;
    if (!$lock) {
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $lock = 1;
    }
    return $request;
}

/**
 * 获取云盘文件存储前缀路径
 */
function getPanPathPrefix($userNum){
    return pathjoin(getParameter('owncloud.path.prefix'),$userNum,"files");
}

function createLocalMediaResponse2(Request $request, $file, $isDownload = false){
    $filename = $file['filename'];
    $location = $file['fullpath'];

    $mimeType = \Common\Lib\FileToolkit::getMimeTypeByExtension($file['ext']);

    if (!file_exists($location))
    {
        header ("HTTP/1.1 404 Not Found");
        return;
    }
    $size  = $file['size'];
    $time  = date('r', filemtime($location));

    $fm = @fopen($location, 'rb');
    if (!$fm)
    {
        header ("HTTP/1.1 505 Internal server error");
        return;
    }

    $begin  = 0;
    $end  = $size - 1;

    if (isset($_SERVER['HTTP_RANGE']))
    {
        if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
        {
            $begin  = intval($matches[1]);
            if (!empty($matches[2]))
            {
                $end  = intval($matches[2]);
            }
        }
    }
    if (isset($_SERVER['HTTP_RANGE']))
    {
        header('HTTP/1.1 206 Partial Content');
    }
    else
    {
        header('HTTP/1.1 200 OK');
    }

    header("Content-Type: $mimeType");
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Accept-Ranges: bytes');
    header('Content-Length:' . (($end - $begin) + 1));
    if (isset($_SERVER['HTTP_RANGE']))
    {

        header("Content-Range: bytes $begin-$end/$size");
    }
    $type = "attachment";

    $agent = $request->headers->get('User-Agent');
    if(preg_match('/(MSIE)|(Trident)/',$agent)
        || preg_match('#Android.*Chrome/[.0-9]*#',$agent)
        || preg_match('#^Mozilla/5\.0$#',$agent)){
        header( 'Content-Disposition: ' . rawurlencode($type) . '; filename="' . rawurlencode( $filename ) . '"' );
    } else {
        header( 'Content-Disposition: ' . rawurlencode($type) . '; filename*=UTF-8\'\'' . rawurlencode( $filename )
            . '; filename="' . rawurlencode( $filename ) . '"' );
    }

    header("Content-Transfer-Encoding: binary");
    header("Last-Modified: $time");

    $cur  = $begin;
    fseek($fm, $begin, 0);

    while(!feof($fm) && $cur <= $end && (connection_status() == 0))
    {
        print fread($fm, min(1024 * 16, ($end - $cur) + 1));
        $cur += 1024 * 16;
    }
}

/**
 * 音频文件响应
 * @author 钱志伟 2015-4-3
 */
function createLocalMediaResponse(Request $request, $file, $isDownload = false) {
    $mimeType = Common\Lib\FileToolkit::getMimeTypeByExtension($file['ext']);

    header('Content-type: ' . $mimeType);
    if ($isDownload) {
        $file['filename'] = urlencode($file['filename']);
        if (preg_match("/MSIE/i", $request->headers->get('User-Agent'))) {
            header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
        } else {
            #$response->headers->set('Content-Disposition', "attachment; filename*=UTF-8''".$file['filename']);
            header("Content-Disposition: attachment; filename=\"".$file['filename']."\";");
        }
    }
    header("Content-Length: ".$file['size']);
    readfile($file['fullpath']);
    exit;
}

/**
 * 获得文件路径
 * @author 钱志伟
 */
function getFilePath($uri, $default = '', $absolute = false) {
    #$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    #$assets = new \Symfony\Component\Templating\Helper\CoreAssetsHelper(new \Symfony\Bundle\FrameworkBundle\Templating\Asset\PathPackage($request, '5.1.4', '%s?%s'), array());
    $url = '';
    if (empty($uri)) {
        //$url = $assets->getUrl('assets/img/default/' . $default);
        // $url = $request->getBaseUrl() . '/assets/img/default/' . $default;
        if ($absolute) {
//                    $url = $request->getSchemeAndHttpHost() . $url;
        }
        return $url;
    }
    $uri = D('Content\FileService')->parseFileUri($uri); # $this->parseFileUri($uri);
    if ($uri['access'] == 'public') {
        $url = rtrim(getParameter('redcloud.upload.public_url_path'), ' /') . '/' . $uri['path'];
//        $url = ltrim($url, ' /');
//        $url = $assets->getUrl($url);

        if ($absolute) {
//            $url = $request->getSchemeAndHttpHost() . $url;
        }
    } elseif ($uri['access'] == 'Data') {
        $url = '/Data/private_files/' . $uri['path'];
    } else {

    }
    return $url;
}

//加密函数
function encrypt($txt, $key = null) {
    if (empty($key))
        $key = C('SECURE_CODE');
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
    $nh = rand(0, 64);
    $ch = $chars [$nh];
    $mdKey = md5($key . $ch);
    $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
    $txt = base64_encode($txt);
    $tmp = '';
    $i = 0;
    $j = 0;
    $k = 0;
    for ($i = 0; $i < strlen($txt); $i++) {
        $k = $k == strlen($mdKey) ? 0 : $k;
        $j = ($nh + strpos($chars, $txt [$i]) + ord($mdKey [$k++])) % 64;
        $tmp .= $chars [$j];
    }
    return $ch . $tmp;
}

//解密函数
function decrypt($txt, $key = null) {
    if (empty($key))
        $key = C('SECURE_CODE');
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
    $ch = $txt [0];
    $nh = strpos($chars, $ch);
    $mdKey = md5($key . $ch);
    $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
    $txt = substr($txt, 1);
    $tmp = '';
    $i = 0;
    $j = 0;
    $k = 0;
    for ($i = 0; $i < strlen($txt); $i++) {
        $k = $k == strlen($mdKey) ? 0 : $k;
        $j = strpos($chars, $txt [$i]) - $nh - ord($mdKey [$k++]);
        while ($j < 0)
            $j += 64;
        $tmp .= $chars [$j];
    }
    return base64_decode($tmp);
}

/**
 * 获取用户浏览器型号。新加浏览器，修改代码，增加特征字符串.把IE加到12.0 可以使用5-10年了.
 */
function getBrowser() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Maxthon')) {
        $browser = 'Maxthon';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 12.0')) {
        $browser = 'IE12.0';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 11.0')) {
        $browser = 'IE11.0';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 10.0')) {
        $browser = 'IE10.0';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0')) {
        $browser = 'IE9.0';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0')) {
        $browser = 'IE8.0';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0')) {
        $browser = 'IE7.0';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
        $browser = 'IE6.0';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'NetCaptor')) {
        $browser = 'NetCaptor';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape')) {
        $browser = 'Netscape';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx')) {
        $browser = 'Lynx';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
        $browser = 'Opera';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
        $browser = 'Google';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
        $browser = 'Firefox';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
        $browser = 'Safari';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iphone') || strpos($_SERVER['HTTP_USER_AGENT'], 'ipod')) {
        $browser = 'iphone';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
        $browser = 'iphone';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'android')) {
        $browser = 'android';
    } else {
        $browser = 'other';
    }
    return $browser;
}

function generateUrl($route, $parameters = array()) {
    $routeHomeArr = C("route_home");
    //var_dump($routeHomeArr["course_show"],$name);exit();
    $routeAdminArr = C("route_admin");
    $map = "";
    if (isset($routeHomeArr[$route])) {
        $map = $routeHomeArr[$route];
    } else if (isset($routeAdminArr[$route])) {
        $map = $routeAdminArr[$route];
    }
    $url = U($map, $parameters);
    return $url;
}

/**
 * +----------------------------------------------------------
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 * +----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function byte_format($size, $dec = 2) {
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size, $dec) . " " . $a[$pos];
}

/**
 * 输出utf8编码头
 * @author zzw 2015-04-15
 */
function utf8Header($charset = 'utf-8') {
    header("Content-Type: text/html;charset={$charset}");
}

/**
 * 获取图片真实路径
 * @param        $category
 * @param string $uri
 * @param string $size
 * @param bool $absolute
 * @return string
 */
function getDefaultPath($category, $uri = "", $size = '', $absolute = false) {
//        $assets = $this->container->get('templating.helper.assets');
//        $request = $this->container->get('request');
    $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $assets = new \Symfony\Component\Templating\Helper\CoreAssetsHelper(new \Symfony\Bundle\FrameworkBundle\Templating\Asset\PathPackage($request, '5.1.4', '%s?%s'), array());

    $cdn = createService('System.SettingService')->get('cdn', array());
    $cdnUrl = (empty($cdn['enabled'])) ? '' : rtrim($cdn['url'], " \/");
    #tanhaitao 2015-09-21 add
    if (!empty($uri) && in_array(intval($uri), C('COURSE_DEFAULT_PIC_IDX'), TRUE)) {
        $course_pic = C('COURSE_DEFAULT_PIC');
        $url = $assets->getUrl($course_pic[intval($uri)]);
        return $url;
    }
    if (empty($uri)) {
        $publicUrlpath = '/Public/assets/img/default/';
        $url = $assets->getUrl($publicUrlpath . $size . $category);

        $defaultSetting = createService('System.SettingService')->get('default', array());

        $key = 'default' . ucfirst($category);
        $fileName = $key . 'FileName';
        if (array_key_exists($key, $defaultSetting) && array_key_exists($fileName, $defaultSetting)) {
            if ($defaultSetting[$key] == 1) {
                $url = $assets->getUrl($publicUrlpath . $size . $defaultSetting[$fileName]);
                return $url;
            } else {
                if ($absolute) {
                    $url = $request->getSchemeAndHttpHost() . $url;
                }
                return $url;
            }
        } else {
            return $url;
        }
    }

    $uri = D('Content\FileService')->parseFileUri($uri);
    if ($uri['access'] == 'public') {

        $url = rtrim(getParameter('redcloud.upload.public_url_path'), ' /') . '/' . $uri['path'];
        $url = ltrim($url, ' /');
        $url = $assets->getUrl($url);

        if ($cdnUrl) {
            $url = $cdnUrl . $url;
        } else {
            if ($absolute) {
                $url = $request->getSchemeAndHttpHost() . $url;
            }
        }

        return $url;
    } else {

    }
}

/**
 * 获取保利威视视频连接地址
 * 0=>无(默认)，1=>流畅，2=>高清，3=>超清
 * @author fubaosheng 2015-03-19
 */
function getVideoUrl($vid, $bit = 0) {
    $urlOne = substr($vid, 0, 10);
    $urlArr = explode('_', $vid);
    $urlTow = substr($urlArr [0], -1);
    $urlTree = substr($urlArr [0], -32);
    $bittype = '';
    switch ($bit) {
        case 1 :
        case 2 :
        case 3 :
            $bittype = '_' . $bit;
            break;
        case 0 :
        default :
            $bittype = '';
            break;
    }
    $newUrl = "http://hls.videocc.net/{$urlOne}/{$urlTow}/{$urlTree}{$bittype}.m3u8";
    return $newUrl;
}

/**
 * 根据polyvId获取视频信息
 * @author fubaosheng 2015-06-03
 */
function getPolyvVideoInfoByPolyvId($polyvId) {
    $info = array();
    if ($polyvId) {
        $setting = createService('System.SettingService')->get('storage', array());
        $readToken = $setting['read_token'];
        if (empty($readToken))
            return $info;

        $url = "http://v.polyv.net/uc/services/rest?method=getById&readtoken={$readToken}&vid={$polyvId}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($data, true);
        if ($data['error'] == "0")
            return $data['data'][0];
        else
            return $info;
    }
    return $info;
}

/**
 * 根据polyvId获取钱视频大小
 * @author fubaosheng 2015-06-03
 */
function getPolyvVideoSizeByPolyvId($polyvId) {
    $polyvVideoSize = 0;

    $setting = createService('System.SettingService')->get('storage', array());
    $readToken = $setting['read_token'];
    if (empty($readToken) || empty($polyvId))
        return $polyvVideoSize;

    $url = "http://v.polyv.net/uc/services/rest?method=getById&readtoken={$readToken}&vid={$polyvId}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($data, true);
    if ($data['error'] == "0") {
        $duration = $data['data'][0]['duration'];
        $durationArr = explode(":", $duration);
        $h = intval($durationArr[0]);
        $m = intval($durationArr[1]);
        $s = intval($durationArr[2]);
        $polyvVideoSize = $h * 3600 + $m * 60 + $s;
    }
    return $polyvVideoSize;
}

/**
 * 获取用户姓名 edit fbs 2014-4-9
 * edit by qzw 2014-05-20
 */
function getUserName($uid, $lang = 'zh') {
    static $cacheArr = array();
    $key = $uid . '_' . $lang;

    if (isset($cacheArr[$key]))
        return $cacheArr[$key];
//file_put_contents('/tmp/a.txt', $key.PHP_EOL, FILE_APPEND);
    //获取当前登录的用户
//    global $ts;
//    if ($uid == $ts['user']['uid'] || $uid == $ts['user']['uname'])
//    return $ts['user']['uname'];
    $userInfo = getUserBaseInfo($uid);
    $cacheArr[$key] = isset($userInfo['nickname']) ? $userInfo['nickname'] : '';
    return $cacheArr[$key];
}

/**
 * 获得缓存key前缀
 * @author 钱志伟 2015-10-23
 * @example getCacheKey('CACHE_UID_UINFO', 3308) => cache_uid_uinfo_3308
 */
function getCacheKey($type = '', $tail = '') {
    return C('CACHE_KEY_PFX.' . $type) . '_' . $tail;
}

/**
 * 获取用户基本信息
 * @author fbs 2014-06-16
 */
function getUserBaseInfo($uid, $lang = 'zh') {
    static $cacheArr = array();
    $key = $uid . '_' . $lang;

    if (isset($cacheArr[$key]))
        return $cacheArr[$key];

    $cacheMem = \Think\Cache::getInstance('Memcache');
    $uinfo = $cacheMem->get(getCacheKey('CACHE_UID_UINFO', $uid));
    if (!$uinfo) {
//        $userInfo = M('user a')->join('user_profile b on a.id=b.id')->where("a.id={$uid}")->field('a.*, b.gender, b.idcard, b.qq ,b.about')->find();
//        $userInfo['email'] = (string) $userInfo['email'];
//        $userInfo['mobile'] = (string) $userInfo['mobile'];
//        $userInfo['userName'] = (string) $userInfo['userName'];
//        $userInfo['studNum'] = (string) $userInfo['studNum'];
//        $cacheMem->set(getCacheKey('CACHE_UID_UINFO', $uid), $userInfo, 1800);
        $userInfo = M('user a')->where("a.id={$uid}")->find();
        $uinfo = $cacheArr[$key] = $userInfo;
    }
    return $uinfo;
}

/**
 * 清除用户缓存
 * @author 钱志伟 2015-2015-10-23
 */
function clearUserCache($uid = 0) {
    if (!$uid)
        return false;
    $cacheKeyPfxArr = array(
        'CACHE_UID_UINFO',
    );
    $cacheMem = \Think\Cache::getInstance('Memcache');
    if ($cacheKeyPfxArr) {
        foreach ($cacheKeyPfxArr as $cacheKeyPfx) {
            $cacheMem->rm(getCacheKey($cacheKeyPfx, $uid));
        }
    }
}

/**
 * 获取用户头像
 * $type : s 小头像, m:中头像, l:大头像
 * @param        $uid
 * @param string $type
 * @return mixed
 */
function getUserFace($uid, $type = 's') {
    #edit 谈海涛 2015-09-25
    switch ($type) {
        case 'm':
            //$size = 'mediumAvatar';
            $types = 'middle';
            break;
        case 'l':
            //$size = 'largeAvatar';
            $types = 'big';
            break;
        case 's':
        default:
            //$size = 'smallAvatar';
            $types = 'small';
    }

    //$face = M('user')->where(array('id' => $uid))->getField($size);
    //return C('SITE_URL') . getDefaultPath('avatar', $face);
    return C('SITE_URL') . \Common\Common\Url::getInstance()->getUserFaceUrl(array('type' => $types, 'uid' => $uid));
}

/**
 * 获取用户性别
 * @param $uid
 * @return mixed
 */
function getUserGender($uid) {
    $userInfo = getUserBaseInfo($uid);
    return isset($userInfo['gender']) ? (string) $userInfo['gender'] : '';
}

/**
 * 获取分类名称
 * @param $id
 * @return mixed
 */
function getCategoryName($id) {
    $name = M('category')->where(array('id' => $id))->getField('name');
    return $name;
}

/**
 * 检查学号是否合法
 * @return boolean
 */
function isValidStudNum($studNum) {
    return preg_match("/^[123456789]\d{0,19}$/", $studNum) !== 0;
}

/**
 * 检查密码是否合法
 * @return boolean
 */
function isValidPassword($password) {
    if (preg_match("/\s/", $password))
        return false;
    if (preg_match("/[\x7f-\xff]/", $password))
        return false;
    $len = strlen($password) ? : 0;
    if ($len < 5 || $len > 20)
        return false;
    return true;
}

/**
 * 检查手机是否合法
 * @return boolean
 */
function isValidMobile($mobile) {
    $mobile = str_replace(" ", "", $mobile);
    return preg_match("/^1[3|4|5|7|8]\d{9}$/", $mobile) !== 0;
}

/**
 * 检查学号是否合法
 */
function isValidStudentNumber($number){
    return preg_match("/^[0-9]*$/", $number) !== 0;
}

/**
 * 检查Email地址是否合法
 * @return boolean
 */
function isValidEmail($email) {
    $check = preg_match("/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", trim($email)) !== 0;
    if ($check) {
        $len = intval(strlen(strstr($email, "@"))) - 1;
        if ($len > 15) {
            return false;
        } else {
            if (strlen(substr($email, 0, strpos($email, "@"))) > 44)
                return false;
            return true;
        }
    }
    return $check;
}

/**
 * 检测内容是否含有关键字
 */
function checkKeyWord($content) {
    $content = strip_tags($content);
    $audit = createService('Service.XdataModel')->lget('audit');
    if ($audit['open'] && $audit['keywords']) {
        $arr_keyword = explode('|', $audit['keywords']);
        foreach ($arr_keyword as $k => $v) {
            if (false !== stripos($content, $v)) {
                return true;
            }
        }
    }
    return false;
}

/**
 * 检查给定的用户名是否合法
 * 合法的用户名由4-20位的中英文/数字/下划线/减号组成
 * @param string $username
 * @return boolean
 */
function isLegalUsername($username) {
    // GB2312: preg_match("/^[".chr(0xa1)."-".chr(0xff)."A-Za-z0-9_-]+$/", $username)
//    return preg_match("/^[\x{2e80}-\x{9fff}A-Za-z0-9_-]+$/u", $username) &&
//            mb_strlen($username, 'UTF-8') >= 4 &&
//            mb_strlen($username, 'UTF-8') <= 20;
    $lenth = absLength($username);
    if ($lenth < 4 || $lenth > 20)
        return false;

    return true;
}

function isLegalNickname($username) {
    $username = str_replace(" ", "", $username);
//	$lenth    = absLength($username);
    $lenth = mb_strlen($username, 'utf-8');
    if ($lenth < 2 || $lenth > 20)
        return false;
    if (preg_match("/^[0-9a-zA-Z_\.\x{4e00}-\x{9fa5}]+$/u", $username))
        return true;

    return false;
}

function isstudNum($studNum) {
    $studNum = str_replace(" ", "", $studNum);
    return preg_match("/^[\w]{6,20}$/", $studNum) !== 0;
}

/**
 * 获得视频有好时间
 * @author 钱志伟 2015-05-08
 */
function getVideoFriendTm($video_time) {
    $video_time = floor($video_time);
    $sec = $video_time % 60;
    $hour = floor($video_time / 60 / 60);
    $min = floor(($video_time - $hour * 60 * 60) / 60);
    $min = $min < 10 ? '0' . $min : $min;
    $sec = $sec < 10 ? '0' . $sec : $sec;
    if ($video_time > 60 * 60) {
        $friendTm = $hour . ':' . $min . ':' . $sec;
    } else {
        $friendTm = $min . ':' . $sec;
    }
    return $friendTm;
}

/**
 * 优化消息
 * @author 钱志伟 2014-09-10
 */
function optimizeHtml($param = array()) {
    $options = array(
        'html' => '',
        'allowTags' => '<img><p>'
    );
    $options = array_merge($options, $param);
    extract($options);
    // at替换:input换成文字
    $regex = "/\<input value=\"(@[^\"']*)\".*(class=\"editAt\")?.*\/\>/U";
    $html = preg_replace($regex, '\\1 ', $html);
    // 过滤标签
    $html = strip_tags($html, $allowTags);

    return $html;
}

/**
 * html转line
 * @author 钱志伟 2014-09-10
 */
function html2line($param = array()) {
    $options = array(
        'html' => '',
        'imgMinWidth' => 0, #qzw 2016-3-18 app展现时，0尺寸转化为固定尺寸
        'imgMinHeight' => 0, #qzw 2016-3-18
    );
    $options = array_merge($options, $param);
    extract($options);

    $lineArr = array();

    $html = preg_replace("/<p><\/p>$/", "", $html);
    $html = str_replace(array(
        '<p>',
        '</p>'
    ), array(
        '',
        "\n"
    ), $html);
    $html = rtrim($html, "\n");

    $prefix = 'src="http://' . $_SERVER ['HTTP_HOST'] . '/Public';
    $html = str_replace('src="/Public', $prefix, $html);

    $r = preg_match_all("/<img([^>]*)\s*src=[^>]*(\/)?>/", $html, $match);

    if ($r) {
        if ($imgArr = $match [0]) {
            foreach ($imgArr as $imgstr) {
                // 表情算文本 钱志伟 2014-11-21
                if (strpos($imgstr, '/xheditor_emot/') > 0)
                    continue;

                $pos = strpos($html, $imgstr);
                if ($pos > 0) {
                    $lineArr [] = array(
                        'type' => 'text',
                        'content' => substr($html, 0, $pos)
                    );
                }
                $html = substr($html, $pos + strlen($imgstr));
                // 为图片添加宽高，不存在返回错误图片
                preg_match("/src=['\"][^'\"]*/", $imgstr, $srcMatch);
                //edit fubaosheng 2015-05-28 外网图片直接返回
                $imgUrl = substr($srcMatch[0], 5);
                $filePath = SITE_PATH;
                $filePath .= preg_replace("/src=['\"]http:\/\/[^\/]*/", "", $srcMatch [0]);
                if (file_exists($filePath)) {
                    $hPosition = strpos($imgstr, "data-height");
                    $wPosition = strpos($imgstr, "data-width");
                    if ($hPosition === false || $wPosition === false) {
                        $fileSize = getimagesize($filePath);
                        #qzw 2016-3-18 照顾app，防止0尺寸
                        $width = $fileSize[0] ? $fileSize[0] : $imgMinWidth;
                        $height = $fileSize[1] ? $fileSize[1] : $imgMinHeight;
                        $fileStr = "<img  data-width='{$width}' data-height='{$height}' ";
                        $imgstr = str_replace("<img", $fileStr, $imgstr);
                    }
                } else if (!empty($imgUrl)) {
                    #by qzw 2015-06-15
                    //$imgSize = getimagesize($imgUrl);
                    $imgstr = "<img src='{$imgUrl}' data-width='{$imgMinWidth}' data-height='{$imgMinHeight}' />";
                } else {
                    $errorSrc = "/Public/assets/img/default/error.png";
                    $errorPath = SITE_PATH . $errorSrc;
                    $imgstr = "";
                    if (file_exists($errorPath)) {
                        $errorSize = getimagesize($errorPath);
                        $errorSrc = "http://" . $_SERVER ['HTTP_HOST'] . $errorSrc;
                        #qzw 2016-3-18 照顾app，防止0尺寸
                        $width = $errorSize[0] ? $errorSize[0] : $imgMinWidth;
                        $height = $errorSize[1] ? $errorSize[1] : $imgMinHeight;
                        $imgstr = "<img src='{$errorSrc}' error=1 data-width='{$width}' data-height='{$height}' />";
                    }
                }

                $lineArr [] = array(
                    'type' => 'img',
                    'content' => $imgstr,
                );
            }
        }
    }
    if ($html)
        $lineArr [] = array(
            'type' => 'text',
            'content' => htmlspecialchars_decode($html)
        );

    return $lineArr;
}

/**
 * 计算字符串长度,中文长度为2,英文为1
 * @param string $str
 * @return int
 * @author LiangFuJian
 * @date   2015-03-13
 */
function absLength($str) {
    $n = 0;
    preg_match_all("/./us", $str, $matchs);
    foreach ($matchs[0] as $p) {
        $n += preg_match('#^[' . chr(0x1) . '-' . chr(0xff) . ']$#', $p) ? 1 : 2;
    }
    return $n;
}

/**
 * 遍历目录
 * @author 钱志伟 2013-11-15
 * @param string $pathname 路径
 * @param string $pattern 模式
 * @param int|bool $absPath 是否绝对路径
 */
function myScanDir($pathname, $fileRegex = '*', $absPath = 1) {
    $fileArr = array();
    foreach (glob($pathname) as $filename) {
        if (is_dir($filename)) {
            $fileArr = array_merge($fileArr, myScanDir($filename . '/*', $fileRegex, $absPath));
        } else {
            $file = basename($filename);
            if (preg_match("/{$fileRegex}/", $file)) {
                $fileArr [] = $absPath ? $filename : $file;
            }
        }
    }
    return $fileArr;
}

function is_base64($str){
    if($str==base64_encode(base64_decode($str))){
        return true;
    }else{
        return false;
    }
}

function is_mhtml_xss($params){
    return strstr($params,"Content-Location") && strstr($params,"Content-Transfer-Encoding") && strstr($params,"base64");
}

/**
 * xss过滤
 * @param $val
 * @return mixed
 */
function remove_xss($val) {

//    doLog($val);
//
//    $val = urlencode($val);
//    $val = str_ireplace('0D%0A', '', $val);
//
//    doLog($val);
//    $val = urldecode($val);

    if(is_mhtml_xss($val)){
        return "";
    }

//    $val = str_replace(PHP_EOL, '', $val);

    $xss = new \Common\Util\xsshtml($val);
    return $xss->getHtml();

    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
//	$val = preg_replace('/([\x00-\x08]|[\x0b-\x0c]|[\x0e-\x19])/', '', $val);
//
//	// straight replacements, the user should never need these since they're normal characters
//	// this prevents like <IMG SRC=@avascript:alert('XSS')>
//	$search = 'abcdefghijklmnopqrstuvwxyz';
//	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
//	$search .= '1234567890!@#$%^&*()';
//	$search .= '~`";:?+/={}[]-_|\'\\';
//	for ($i = 0; $i < strlen($search); $i++) {
//		// ;? matches the ;, which is optional
//		// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
//		// @ @ search for the hex values
//		$val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
//		// @ @ 0{0,7} matches '0' zero to seven times
//		$val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
//	}
//
//	// now the only remaining whitespace attacks are \t, \n, and \r
//	$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
//        if(!$ignoreStyle) $ra1[] = 'style';
//	$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
//	$ra = array_merge($ra1, $ra2);
//
//	$found = true; // keep replacing as long as the previous round replaced something
//	while ($found == true) {
//		$val_before = $val;
//		for ($i = 0; $i < sizeof($ra); $i++) {
//			$pattern = '/';
//			for ($j = 0; $j < strlen($ra[$i]); $j++) {
//				if ($j > 0) {
//					$pattern .= '(';
//					$pattern .= '(&#[xX]0{0,8}([9ab]);)';
//					$pattern .= '|';
//					$pattern .= '|(&#0{0,8}([9|10|13]);)';
//					$pattern .= ')*';
//				}
//				$pattern .= $ra[$i][$j];
//			}
//			$pattern .= '/i';
//			$replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
//			$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
//			if ($val_before == $val) {
//				// no replacements were made, so exit the loop
//				$found = false;
//			}
//		}
//	}
//	return $val;
}

/**
 * 获得用户设备类型信息
 * @author 杨金龙 2015-04-27
 */
function getUserDeviceInfo($param = array()) {
    $options = array(
        'uid' => 0, #int | array
        'deviceType' => '', #android, ios
    );
    $options = array_merge($options, $param);
    extract($options);

    if (!$uid)
        return array();

    $key = md5(json_encode($options));

    static $cacheArr = array();
    if (!isset($cacheArr[$key])) {
        if ($deviceType)
            $userDevice = createService("User.UserDeviceService")->getUserDeviceByType($uid, $deviceType);
        else
            $userDevice = createService("User.UserDeviceService")->getUserAllDevice($uid);
        $cacheArr[$key] = $userDevice;
    }
    return $cacheArr[$key];
}

/**
 * 获得所有用户设备信息
 * @author 钱志伟 2015-05-13
 */
function getAllUserDeviceInfo($param = array()) {
    $options = array(
        'type' => 'all', #all, android, ios
    );
    $options = array_merge($options, $param);
    extract($options);

    $key = md5(json_encode($options));

    static $cacheArr = array();
    if (!isset($cacheArr[$key])) {
        $allUserDevice = createService("User.UserDeviceService")->getAllUserDevice($type);
        $cacheArr[$key] = $allUserDevice;
    }
    return $cacheArr[$key];
}

/**
 * 检查命名及长度
 * 合法：汉字、字母、数字、下划线
 * @param $str
 * @param $size
 * @return bool
 */
function checkName($str, $size) {

    if (isUtf8($str)) {
        if (mb_strlen($str, 'UTF8') > $size) {
            return false;
        }
        if (!preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $str)) {
            return false;
        }
    } else {
        if (mb_strlen($str, 'GB2312') > $size) {
            return false;
        }
        if (!preg_match('/^(?!_|\s\')[A-Za-z0-9_' . chr(0xa1) . '-' . chr(0xff) . '\s\']+$/', $str)) {
            return false;
        }
    }

    return true;
}

/**
 * 检查字符串是否是UTF8编码
 * @param string $string 字符串
 * @return Boolean
 */
function isUtf8($str) {
    $c = 0;
    $b = 0;
    $bits = 0;
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        $c = ord($str[$i]);
        if ($c > 128) {
            if (($c >= 254))
                return false;
            elseif ($c >= 252)
                $bits = 6;
            elseif ($c >= 248)
                $bits = 5;
            elseif ($c >= 240)
                $bits = 4;
            elseif ($c >= 224)
                $bits = 3;
            elseif ($c >= 192)
                $bits = 2;
            else
                return false;
            if (($i + $bits) > $len)
                return false;
            while ($bits > 1) {
                $i++;
                $b = ord($str[$i]);
                if ($b < 128 || $b > 191)
                    return false;
                $bits--;
            }
        }
    }
    return true;
}

/**
 * 开启xhprof
 * @author qzw 2015-04-10
 */
function redcloud_xhprof_enable() {
    if (!extension_loaded('xhprof')) {
        die("未安装xhprof，请安装xhprof或者关闭APP_XHPROF");
    }
    xhprof_enable(XHPROF_FLAGS_CPU);
}

/**
 * 关闭xhprof并显示性能链接
 * @author qzw 2013-9-10
 */
function redcloud_xhprof_disable($ret = 0) {
    if (!extension_loaded('xhprof')) {
        die("未安装xhprof，请安装xhprof或者关闭APP_XHPROF");
    }

    $xhprof_data = xhprof_disable();

    $xhprof_root = C('XHPROF_PATH');
    $xhprof_lib_php = $xhprof_root . 'xhprof_lib/utils/xhprof_lib.php';
    $xhprof_runs_php = $xhprof_root . 'xhprof_lib/utils/xhprof_runs.php';
    if (!file_exists($xhprof_lib_php))
        die("缺少文件:" . $xhprof_lib_php);
    if (!file_exists($xhprof_runs_php))
        die("缺少文件:" . $xhprof_lib_php);
    include_once $xhprof_lib_php;
    include_once $xhprof_runs_php;

    $xhprof_runs = new XHProfRuns_Default();
    $run_id = $xhprof_runs->save_run($xhprof_data, C('WEBSITE_CODE'));
    $xhprof_url = C('XHPROF_URL') . "/xhprof_html/index.php?run={$run_id}&source=" . C('WEBSITE_CODE');
    if ($ret)
        return $xhprof_url;
    $callgraph_url = C('XHPROF_URL') . "/xhprof_html/callgraph.php?run={$run_id}&source=" . C('WEBSITE_CODE');
    $str = '<div class="fixed-right-bot"><a href="' . $xhprof_url . '" target="_blank">XHP</a><a class="fixed-img" href="' . $callgraph_url . '"  target="_blank"></a></div>';
    echo $str;
}

/**
 * 去一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 * @param $pArray 一个二维数组
 * @param $pKey   数组的键的名称
 * @return 返回新的一维数组
 */
function getSubByKey($pArray, $pKey = "", $pCondition = "") {
    $result = array();
    foreach ($pArray as $temp_array) {
        if (is_object($temp_array)) {
            $temp_array = (array) $temp_array;
        }
        if (("" != $pCondition && $temp_array[$pCondition[0]] == $pCondition[1]) || "" == $pCondition) {
            $result[] = ("" == $pKey) ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
        }
    }
    return $result;
}

/**
 * 图片文件转换数组
 * @author fubaosheng 2014-11-19
 */
function fileToArray($fileArr) {
    if (empty($fileArr))
        return array();
    if (!is_array($fileArr ['name'])) {
        $fileArr ['name'] = array(
            $fileArr ['name']
        );
    }
    if (!is_array($fileArr ['tmp_name'])) {
        $fileArr ['tmp_name'] = array(
            $fileArr ['tmp_name']
        );
    }
    if (!is_array($fileArr ['type'])) {
        $fileArr ['type'] = array(
            $fileArr ['type']
        );
    }
    if (!is_array($fileArr ['size'])) {
        $fileArr ['size'] = array(
            $fileArr ['size']
        );
    }
    if (!is_array($fileArr ['error'])) {
        $fileArr ['error'] = array(
            $fileArr ['error']
        );
    }
    return $fileArr;
}

/**
 * 多文件上传
 * @author 钱志伟 2014-09-23
 */
function uploadMultiFile($paramArr = array()) {
    $options = array(
        'files' => array(),
        'uploadDirName' => 'editor',
        'allowFiles' => array(".gif", ".png", ".jpg", ".jpeg", ".bmp"),
        'maxSize' => 1000000000
    );
    $options = array_merge($options, $paramArr);
    extract($options);

    $data = array();
    if (!$files)
        return $data;

    $fileNum = count($files ['name']);
    for ($i = 0; $i < $fileNum; $i++) {
        $fileKey = 'tmp_' . $i;
        if (!$files ['name'] [$i])
            continue;

        $_FILES [$fileKey] = array(
            'name' => $files ['name'] [$i],
            'type' => $files ['type'] [$i],
            'tmp_name' => $files ['tmp_name'] [$i],
            'error' => $files ['error'] [$i],
            'size' => $files ['size'] [$i]
        );
        // 上传配置
        $config = array(
            "savePath" => getParameter('redcloud/upload/private_directory') . '/' . $uploadDirName . '/',
            "maxSize" => $maxSize, // 单位KB
            "allowFiles" => $allowFiles
        );

        $up = new \Common\Lib\BdEditorUpfile($fileKey, $config);
        $fileInfo = $up->getFileInfo();
        $fileInfo['relUrl'] = getParameter('redcloud.upload.private_url_path') . '/' . $uploadDirName . $fileInfo['relUrl'];
        $data [] = $fileInfo;
        unset($up);
    }
    return $data;
}

/**
 * 保存语音
 */
function saveAudio($paramArr = array()) {
    $options = array(
        'file' => array(), // 语音文件
        'duration' => 0, // 时长
        'dbType' => '' //切库  center中心库
    );
    $options = array_merge($options, $paramArr);
    extract($options);

//	require_once (SITE_PATH . '/addons/libs/Ffmpeg.class.php');
    $codeArr = array(
        1000 => '提交成功',
        1001 => '非法请求',
        1002 => '目录创建失败',
        1003 => '文件不能超过1M',
        1004 => '文件格式不正确',
        1005 => '上传文件异常',
        1006 => '文件拷贝失败',
        1007 => '文件转换失败',
        1008 => '提交失败'
    );
    $maxSize = 1024 * 1024; // 1M
    if (!$file || !$file ['name'])
        $ret = array('status' => 0, 'info' => $codeArr [1000], 'data' => array());
    // 文件大小判断
    if ($file ['size'] > $maxSize) {
        $ret = array(
            'status' => 0,
            'info' => $codeArr [1003],
            'data' => array()
        );
    }
    // 上传错误
    if ($file ['error']) {
        $ret = array(
            'status' => 0,
            'info' => $codeArr [1005],
            'data' => array()
        );
    }
    $folder = "/audio/" . date("Ym");
//	$save_folder = UPLOAD_PATH . $folder;
    $save_folder = getParameter('redcloud/upload/private_directory') . $folder;
    if (!file_exists($save_folder)) {
        if (!mk_dir($save_folder)) {
            $ret = array(
                'status' => 0,
                'info' => $codeArr [1002],
                'data' => array()
            );
        }
    }
    $time = time();
    $randString = rand_string(10);
    // 拷贝音频文件
    $ext = pathinfo($file ['name'], PATHINFO_EXTENSION);
    $srcAudioName = $time . '_' . $randString . '.' . $ext;
    $srcAudio = $save_folder . '/' . $srcAudioName;
    @copy($file ['tmp_name'], $srcAudio);
    if (!file_exists($srcAudio)) {
        $ret = array(
            'status' => 0,
            'info' => $codeArr [1006],
            'data' => array()
        );
    }
    // 将arm文件转换成wav格式的
    $wavFileName = $time . '_' . $randString . '.wav';
    $wavFilePath = $save_folder . '/' . $wavFileName;
    $ffmpeg = new \Common\Lib\Ffmpeg();
    $r = $ffmpeg->audioConvert(array(
        'srcAudio' => $srcAudio,
        'targetAudio' => $wavFilePath
    ));
    if (!$r) {
        // 删掉上传的音频文件
        @unlink($srcAudio);
        $ret = array(
            'status' => 0,
            'info' => $codeArr [1007],
            'data' => array(
                'error' => $ffmpeg->err
            )
        );
    }
    $data ['info'] = '';
    $data ['pathUrl'] = $folder . '/' . $wavFileName;
    $data ['armFileSrc'] = $folder . '/' . $srcAudioName;
    $data ['duration'] = $duration;
    $data ['ctime'] = $time;
    $audioId = createService("Service.AudioServiceModel")->addAudio($data, $dbType);
    $ret = array(
        'status' => 1,
        'info' => '成功',
        'data' => array(
            'audioId' => $audioId,
            'relPath' => getPrivateUrl(array("file" => $data ['pathUrl'])),
            'duration' => $duration
        )
    );
    return $ret;
}

/**
 * 根据数据库存储的图片路径获取完整路径
 * @author yangjinlong 2015-05-06
 */
function getPrivateUrl($paramArr = array()) {
    $options = array(
        'file' => '', // 数据库存储的图片路径
    );
    $options = array_merge($options, $paramArr);
    extract($options);
    if (!$file)
        return '';
    $file = trim($file, '/');
    $ret = C('SITE_URL') . '/Data/private_files/' . $file;
    return $ret;
}

/**
 * 装饰问答语音
 * @author 钱志伟 2015-05-10
 */
function decorateAskAudio(&$ask) {
    $audio = createService("Service.AudioServiceModel")->getAudio($ask['audioId'], array("pathUrl", "duration"));
    $audioUrl = getPrivateUrl(array("file" => $audio['pathUrl']));               #获取音频完整URL
    $ask['audioUrl'] = $audioUrl ? $audioUrl : '';                              #问题音频URL
    $ask['audioDuration'] = $audio['duration'] ? $audio['duration'] : 0;       #问题音频时长
}

/**
 * 优化图片
 * @author yangjinlong 2015-05-06
 */
function optimizeImg($img, $quality = 100) {
    if (defined('PHP_OS') && PHP_OS == 'Linux' && file_exists($img) && is_writable($img)) {
        $convert = C('CONVERT_BIN_PATH');
        if (file_exists($convert)) {
            $qualityStr = $quality != 100 ? " -quality {$quality}" : '';
            $cmd = "{$convert} {$qualityStr} -strip {$img} {$img}";
            exec($cmd, $out);
            return true;
        }
    }
    return false;
}

// 循环创建目录
function mk_dir($dir, $mode = 0755) {
    if (is_dir($dir) || @mkdir($dir, $mode))
        return true;
    if (!mk_dir(dirname($dir), $mode))
        return false;
    return @mkdir($dir, $mode);
}

/**
 * 从html提取图片
 * @author 钱志伟 2015-04-24
 * @modify 钱志伟 2016-3-18 未获得图片真实尺寸时，使用最小宽度，最小高度
 */
function getImgFromHtml($html = '', $imgMinWidth = 100, $imgMinHeight = 100) {
    $imgArr = array();
    $lineArr = html2line(array('html' => $html, 'imgMinWidth' => $imgMinWidth, 'imgMinHeight' => $imgMinHeight));
    if ($lineArr) {
        foreach ($lineArr as $line) {
            if ($line['type'] == 'img' && $line['content'])
                $imgArr[] = $line['content'];
        }
    }
    return $imgArr;
}

/**
 * 移动json返回
 * @param type $data
 * @param type $code
 * @param type $message
 * @param int $secret
 */
function mobileJson($param = array()) {
    $options = array(
        'data' => array(),
        'code' => 0,
        'message' => '',
        'secret' => 1,
        'securityCode' => '',
        'isCplusplus' => 0,
    );
    $options = array_merge($options, $param);
    extract($options);

    header("Content-type:text/json;charset=utf-8");
    if ($data && array_key_exists('data', $data)) {
        $return = $data;
    } else {
        $return ['data'] = $data;
    }
    $return ['code'] = $code;
    $return ['msg'] = $message;
    $return['xhprof'] = C('APP_XHPROF_AJAX') || intval($_REQUEST['xhprof']) ? redcloud_xhprof_disable($ret = true) : 'no setup';
    $exitTm = microtime(true);
    $return['runTm'] = 's:' . sprintf('%.3f', $GLOBALS['_beginTime']) . ' e:' . sprintf('%.3f', $exitTm) . ' tms=' . intval(($exitTm - $GLOBALS['_beginTime']) * 1000) . 'ms';
    $return['mem'] = byte_format(memory_get_peak_usage());
    $return['server'] = $_SERVER['HOSTNAME'];
    $return['serverTime'] = strval(time());

    if ($secret && $isCplusplus)
        exit(base64_encode(json_encode($return)));
    if ($secret)
        exit(_encrypt(trim(json_encode($return)), $securityCode));
    exit(json_encode($return));
}

/**
 * 字符串des加密
 * @param string $str
 * @return string
 */
function _encrypt($str, $securityCode = '') {
    //加密，返回大写十六进制字符串
    $size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
    $str = _pkcs5Pad($str, $size);
    return base64_encode(mcrypt_cbc(MCRYPT_DES, $securityCode, $str, MCRYPT_ENCRYPT, $securityCode));
}

function _pkcs5Pad($text, $blocksize) {
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
}

/**
 * 是否是生产环境
 * @author 钱志伟 2015-05-13
 */
function isProductEnv() {
    return C('RUN_ENVIRONMENT') == 1;
}

/**
 * 是否是开发环境
 * @author 钱志伟 2015-05-13
 */
function isDevelopmentEnv() {
    return C('RUN_ENVIRONMENT') == 0;
}

/**
 * +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 *                         0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 *                         +----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
    $str = '';
    switch ($type) {
        case 0:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1:
            $chars = str_repeat('0123456789', 3);
            break;
        case 2:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3:
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) {//位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if ($type != 4) {
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i = 0; $i < $len; $i++) {
            $str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
    }
    return $str;
}

/*
 * 时间转换
 * @author 杨金龙 2015-05-11
 */

function timeFilter($time) {
    $diff = time() - $time;
    if ($diff < 0) {
        return '未来';
    }

    if ($diff == 0) {
        return '刚刚';
    }

    if ($diff < 60) {
        return $diff . '秒前';
    }

    if ($diff < 3600) {
        return round($diff / 60) . '分钟前';
    }

    if ($diff < 86400) {
        return round($diff / 3600) . '小时前';
    }

    if ($diff < 2592000) {
        return round($diff / 86400) . '天前';
    }

    if ($diff < 31536000) {
        return date('m-d', $time);
    }

    return date('Y-m-d', $time);
}

/**
 * 获取客户端设备类型
 * $type 1 : iphone
 * $type 2 : android
 * @return int
 * @author 王磊 2015-05-19
 * @edit   fubaosheng 2015-05-22
 */
function getDeviceType() {
    $agent = strtolower($_SERVER ['HTTP_USER_AGENT']);
    $type = "android";
    if ((strpos($agent, 'iphone') > 0) || (strpos($agent, 'ipad') > 0) || $_REQUEST['ios'])
        $type = "iphone";
    return $type;
}

/**
 * 判断是否是微信
 * @author fubaosheng 2015-07-01
 */
function isWeiXin() {
    $agent = strtolower($_SERVER ['HTTP_USER_AGENT']);
    if (strpos($agent, 'micromessenger'))
        return true;
    else
        return false;
}

/**
 * 处理sql对象
 * 非中心站获取自己本站数据，中心获取所有数据 qzw 2015-07-10
 * @author 钱志伟 2015-07-10
 */
function processSqlObj($param = array()) {
    $options = array(
        'sqlObj' => false, #M()或Builder对象
        'siteSelect' => 'local', #all, local, {webcode}
    );
    $options = array_merge($options, $param);
    extract($options);

    if (!is_object($sqlObj))
        return $sqlObj;
    return $sqlObj;
}

/**
 * 提取指定文本长度 并去除html标签
 * @author Yjl 2015-06-02
 */
function getFilterText($param = array()) {
    $options = array(
        'str' => "",
        'len' => 50,
        'charset' => "utf-8"
    );
    $options = array_merge($options, $param);
    extract($options);
    $str = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", strip_tags($str));

    //如果截取长度小于等于0，则返回空
    if (!is_numeric($len) or $len <= 0)
        return "";

    $sLen = strlen($str);
    if ($len >= $sLen)
        return $str;

    //判断使用什么编码，默认为utf-8
    if (strtolower($charset) == "utf-8") {
        $len_step = 3; //如果是utf-8编码，则中文字符长度为3
    } else {
        $len_step = 2; //如果是gb2312或big5编码，则中文字符长度为2
    }

    $len_i = 0;
    //初始化计数当前已截取的字符串个数，此值为字符串的个数值（非字节数）
    $substr_len = 0; //初始化应该要截取的总字节数

    for ($i = 0; $i < $sLen; $i++) {
        if ($len_i >= $len)
            break; //总截取$len个字符串后，停止循环

//判断，如果是中文字符串，则当前总字节数加上相应编码的中文字符长度
        if (ord(substr($str, $i, 1)) > 0xa0) {
            $i += $len_step - 1;
            $substr_len += $len_step;
        } else { //否则，为英文字符，加1个字节
            $substr_len++;
        }
        $len_i++;
    }
    $result_str = substr($str, 0, $substr_len);
    return $result_str . "...";
}

/**
 * 设置使用中心库
 * @author 钱志伟 2015-07-29
 */
function setUseCenterDb() {
    if (C('SUPPORT_CENTER_DB'))
        C('USE_CENTER_DB', 1);
}

/**
 * 聊天室获得微秒时间
 * @author 钱志伟 2015-08-18
 */
function getMicroTm() {
    $arr = explode(' ', microtime());
    $mtm = $arr[1] . substr($arr[0], 2, 6);
    return $mtm;
}

/**
 * 设置使用中心库
 * @author 钱志伟 2015-07-29
 */
function setNoUseCenterDb() {
    if (C('SUPPORT_CENTER_DB'))
        C('USE_CENTER_DB', 0);
}

/**
 * 获得指定webcode对应域名
 * @param string $type 优先选取类型: base=>基本域名 ext=>扩展域名
 * @author 钱志伟 2015-08-13
 */
function getWebDomain($webCode = '', $type = 'base', $scheme = 'http://') {
    return WEB_DOMAIN;
}

/**
 *  根据两点间的经纬度计算距离
 * @link http://my.oschina.net/ekc/blog/84106
 * @param float $lat 纬度值
 * @param float $lng 经度值
 * @author 钱志伟 2015-08-20
 */
function getDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6367000; //approximate radius of earth in meters
    $lat1 = ($lat1 * pi()) / 180;
    $lng1 = ($lng1 * pi()) / 180;

    $lat2 = ($lat2 * pi()) / 180;
    $lng2 = ($lng2 * pi()) / 180;
    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}

/**
 * 是否使用中心库
 * @author 钱志伟 2015-07-29
 */
function isUseCenterDb() {
    if (C('SUPPORT_CENTER_DB'))
        return C('USE_CENTER_DB') == 1;
    return false;
}

/**
 * pdf文件浏览地址
 * @param $hashId
 * @return string
 */
function getPdfWebUrl($hashId) {
    $dcoument_url = C('SITE_URL') . '/Data/udisk/' . $hashId;
    $pdfjs_url = C('SITE_URL') . '/Public/assets/libs/pdfjs/view.html';

    return $pdfjs_url . '#' . $dcoument_url;
}

//function getSchoolName($webCode) {
//    $map['webCode'] = $webCode;
//    //qzw 2016-03-23 从中心库获得学校信息
////    $data = createService('Center\School.SchoolService')->switchCenterDb()->where($map)->find();
//    return $data['name'];
//}

function istrtotime($str) {
    $res = strtotime($str);
    return $res ? $res : $str;
}

//过滤掉首位的静态地址获取前缀
function tripPath($path,$prefix=DATA_FETCH_URL_PREFIX){
    if(strpos($path,$prefix) === 0){
        return mb_substr($path,mb_strlen($prefix));
    }
    if(strpos($path,"/" . $prefix) === 0){
        return mb_substr($path,mb_strlen($prefix) + 1);
    }
    return $path;
}

/**
 * 是否开启资源课程库
 * @author wanglei
 * @date   2015-09-06
 * @return bool
 */
function isOpenPublicCourse() {
    $show_public_course = C('SHOW_PUBLIC_COURSE');
    return isset($show_public_course) ? $show_public_course : true;
}

function denied($message = null) {
    E($message ? $message : '抱歉，您没有权限执行此操作');
}

/**
 * 上线引导
 * @author fubaosheng 2015-09-14
 */
function allowGuide($cookieVarName, $guideEndTm, $expire = 0) {
    $isGuide = 0;
    if (time() > $guideEndTm) { #过了引导期
        setcookie($cookieVarName, 1, $guideEndTm);
    } else { #引导期内
        if (!$_COOKIE[$cookieVarName]) { #未引导过
            $expire = $expire ? time() + $expire : $guideEndTm;
            setcookie($cookieVarName, 1, $expire);
            $isGuide = 1;
        }
    }
    return $isGuide;
}

/**
 * @author 钱志伟 2015-09-17
 */
function getScheme() {
    return $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
}

/**
 * 课程图片输出时用以下方式装饰一下（{{ default_path('coursePicture','3', 'large') }}）
 * @author 谈海涛 2015-09-21
 */
function decorateCoursePicList($parm) {
    foreach ($parm as $k => $course) {
        if($course['selectPicture'] != ""){
            $parm[$k]['smallPicture'] = trim($course['selectPicture']);
            $parm[$k]['middlePicture'] = trim($course['selectPicture']);
            $parm[$k]['largePicture'] = trim($course['selectPicture']);
        }
    }
    return $parm;
}

/**
 * 课程图片输出时用以下方式装饰一下（{{ default_path('coursePicture','3', 'large') }}）
 * @author 谈海涛 2015-09-21
 */
function decorateCoursePicture($course) {
    if ($course['selectPicture'] != "") {
        $course['smallPicture'] = trim($course['selectPicture']);
        $course['middlePicture'] = trim($course['selectPicture']);
        $course['largePicture'] = trim($course['selectPicture']);
    }
    return $course;
}

/**
 * 根据时间戳返回星期几
 * @param string $time 时间戳
 * @return 星期几
 */
function weekday($time, $str = "周") {
    if (is_numeric($time)) {
        $weekday = array($str . '日', $str . '一', $str . '二', $str . '三', $str . '四', $str . '五', $str . '六');
        return $weekday[date('w', $time)];
    }
    return false;
}

/**
 * 下载远程图片
 * @param      $file_url //远程图片路径
 * @param      $save_to //保存路径
 * @param null $time_out //下载超时
 * @param null $REFERER
 */
function download_remote_file_with_curl($file_url, $save_to, $time_out = null, $REFERER = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_URL, $file_url);
    if ($time_out)
        curl_setopt($ch, CURLOPT_TIMEOUT, $time_out);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($REFERER)
        curl_setopt($ch, CURLOPT_REFERER, $REFERER);
    $file_content = curl_exec($ch);

    curl_close($ch);
    $downloaded_file = fopen($save_to, 'w');
    fwrite($downloaded_file, $file_content);
    fclose($downloaded_file);
}

/**
 * 判断当前学校是否是gduf
 * @author fubaosheng 2015-09-28
 */
function isShowVirtualLab() {
    return in_array(strtolower(C('WEBSITE_CODE')), array('gduf', 'newgduf'));
}

/**
 * 关闭用户写
 * @author 钱志伟 2016-1-30
 */
function isCloseUserWrite() {
    return C('CLOSE_USER_WRITE') ? 1 : 0;
}

/**
 * 获取某个学校名
 * @author fubaosheng 2015-10-12
 */
function getWebNameByWebcode($webCode) {
    if (empty($webCode))
        return '';
    $site = createService('System.SettingServiceModel')->get('site', '', $webCode);
    return $site['name'] ? : '';
}

/**
 *  根据用户 id 获取用户金币
 * @author Czq 2016-03-17
 */
function getUserGold($userId) {
    $userGold = createService('User.UserService')->getUserGold($userId);
    return $userGold;
}

function currentWebCode() {
    $allWebCode = C("ALL_WEB_CODE");
    $code = C("WEBSITE_CODE");
    $webCode = $allWebCode[$code]["domainName"][0] ? : "";
    if ($webCode) {
        return $webCode;
    } else {
        $code .= in_array($_SERVER['SERVER_ADDR'], array('192.168.3.6')) ? ".cloud.com" : ".gkk.cn";
        return $code;
    }
}

function is_not_json($str) {
    return is_null(json_decode($str));
}

/**
 * 获取客户端操作系统
 * @author 谈海涛 2016-2-17
 */
function getplat() {
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $os = false;

    if (eregi('win', $agent)) {
        $os = 'Windows';
    } else if (eregi('linux', $agent)) {
        $os = 'Linux';
    } else if (eregi('unix', $agent)) {
        $os = 'Unix';
    } else if (eregi('sun', $agent) && eregi('os', $agent)) {
        $os = 'SunOS';
    } else if (eregi('ibm', $agent) && eregi('os', $agent)) {
        $os = 'IBM OS/2';
    } else if (eregi('Mac', $agent) && eregi('PC', $agent)) {
        $os = 'Macintosh';
    } else if (eregi('PowerPC', $agent)) {
        $os = 'PowerPC';
    } else if (eregi('AIX', $agent)) {
        $os = 'AIX';
    } else if (eregi('HPUX', $agent)) {
        $os = 'HPUX';
    } else if (eregi('NetBSD', $agent)) {
        $os = 'NetBSD';
    } else if (eregi('BSD', $agent)) {
        $os = 'BSD';
    } else if (ereg('OSF1', $agent)) {
        $os = 'OSF1';
    } else if (ereg('IRIX', $agent)) {
        $os = 'IRIX';
    } else if (eregi('FreeBSD', $agent)) {
        $os = 'FreeBSD';
    } else if (eregi('teleport', $agent)) {
        $os = 'teleport';
    } else if (eregi('flashget', $agent)) {
        $os = 'flashget';
    } else if (eregi('webzip', $agent)) {
        $os = 'webzip';
    } else if (eregi('offline', $agent)) {
        $os = 'offline';
    } else {
        $os = 'Unknown';
    }
    return $os;
}

/**
 * 中文字符串拆分成数组
 * @author 廖虹森 2016-3-30
 * @param      $str //要拆分的字符串
 * @param      $charset //字符编码
 */
function mbstringtoarray($str, $charset) {
    $strlen = mb_strlen($str);
    while ($strlen) {
        $array[] = mb_substr($str, 0, 1, $charset);
        $str = mb_substr($str, 1, $strlen, $charset);
        $strlen = mb_strlen($str);
    }
    return $array;
}

function cliGetWebcode($webCode) {
    fwrite(STDOUT, "输入webcode：");
    $webCode = trim(fgets(STDIN));
    if (empty($webCode)) {
        echo "请输入webCode：\r\n";
    }
    $web = C("REDCLOUD_IMPORT_ALLOW_WEBCODE");
    for ($i = 0; $i < count($web); $i++) {
        if ($webCode == $web[$i]) {
            $test = "code";
        }
    }
    if (!$test) {
        echo "请输入正确的webCode！\r\n";
        exit();
    }
    #qzw
    if(!defined('REDCLOUD_MOVE_CLOUD_WEBCODE')) define('REDCLOUD_MOVE_CLOUD_WEBCODE', $webCode);

    return $webCode;
}

/*
 * redcloud导数据发送短信
 */

function redcloudImportSendSMS($mobile, $content) {
    $param['mobile'] = $mobile;
    $param['content'] = $content;
    $param['account'] = 1;
    $sendMessage = new SendMessage();
    $res = $sendMessage->sendMessage2($param);
    return $res;
}
