#!/usr/sbin/php5.5 -q
<?php
/**
 * 消息队列任务
 * @author 钱志伟 2015-08-25
 */
$webCode = getenv('WEBCODE');
$flag = getenv('FLAG');

$siteRoot = dirname(dirname(__FILE__)) . '/';
$webCodeEnv = $webCode ? "WEBCODE={$webCode}" : '';

$cmd = "COUNT=1 INTERVAL=1 QUEUE='MsgTask' WEBCODE={$webCode} /usr/sbin/php5.5 {$siteRoot}index_cli.php --c=Queue --a=resqueAction >> /server/cron_log/{$flag}_msgTask_resque.log 2>&1 &";
exec($cmd);