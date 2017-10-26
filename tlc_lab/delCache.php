<?php
define('SITE_PATH', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
define('RUNTIME_PATH', SITE_PATH . DS .'Runtime' . DS);

utf8Header();

if(!defined('RUNTIME_PATH')) die("undefined:RUNTIME_PATH");
if(!is_dir(RUNTIME_PATH)) die("no exists RUNTIME_PATH's dir:");

exec('/bin/rm '.RUNTIME_PATH.' -rf');

$flushUrl = "http://". $_SERVER['HTTP_HOST'];
echo '刷新缓存：'.$flushUrl.' 成功';

echo '<a href="'.$flushUrl.'">去首页</a>','<br/>';

chdir(RUNTIME_PATH);
$cmd = "/usr/bin/du -sh ./*";
system($cmd);

echo "ok!!#aa333a#$$13!";

/**
 * 输出utf8编码头
 * @author zzw 2015-04-15
 */
function utf8Header($charset = 'utf-8') {
    header("Content-Type: text/html;charset={$charset}");
}
