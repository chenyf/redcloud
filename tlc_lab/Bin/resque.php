#!/usr/sbin/php5.5 -q
<?php
/**
 * 队列任务
 * @author 钱志伟 2015-08-25
 */
$flag = getenv('FLAG');
$webCode = getenv('WEBCODE');

$siteRoot = dirname(dirname(__FILE__)) . '/';

$webCodeEnv = $webCode ? "WEBCODE={$webCode}" : '';

$cmd = "COUNT=1 INTERVAL=1 {$webCodeEnv} QUEUE='default' /usr/sbin/php5.5 {$siteRoot}index_cli.php --c=Queue --a=resqueAction >> /server/cron_log/{$flag}_default_resque.log 2>&1 &";
exec($cmd);
$cmd = "COUNT=1 INTERVAL=1 {$webCodeEnv} QUEUE='user' /usr/sbin/php5.5 {$siteRoot}index_cli.php --c=Queue --a=resqueAction  >> /server/cron_log/{$flag}_user_resque.log 2>&1 &";
exec($cmd);
$cmd = "COUNT=1 INTERVAL=1 {$webCodeEnv} QUEUE='ConvertFile' /usr/sbin/php5.5 {$siteRoot}index_cli.php --c=Queue --a=resqueAction  >> /server/cron_log/{$flag}_convertFile_resque.log 2>&1 &";
exec($cmd);
$cmd = "COUNT=1 INTERVAL=1 {$webCodeEnv} QUEUE='CourseCopy' /usr/sbin/php5.5 {$siteRoot}index_cli.php --c=Queue --a=resqueAction >> /server/cron_log/{$flag}_courseCopy_resque.log 2>&1 &";
exec($cmd);
$cmd = "COUNT=1 INTERVAL=1 {$webCodeEnv} QUEUE='BatTask' /usr/sbin/php5.5 {$siteRoot}index_cli.php --c=Queue --a=resqueAction >> /server/cron_log/{$flag}_BatTask_resque.log 2>&1 &";
exec($cmd);