#!/usr/sbin/php5.5 -q
<?php
/**
 * 后台任务管理
 * WEB_ROOT=$qzw_cloud FLAG=dev MANUAL=1 /usr/sbin/php5.5 Bin/setup.php startMultiTask
 * WEB_ROOT=$qzw_cloud FLAG=dev /usr/sbin/php5.5 Bin/setup.php killTask
 * @author 钱志伟 2015-06-08
 */
if(!$webRoot = getenv('WEB_ROOT')) die("no set env WEB_ROOT".PHP_EOL);
if(!file_exists($webRoot)) die("webroot no exists: ".$webRoot . PHP_EOL);
chdir($webRoot);
//echo "current dir:" . getcwd() . PHP_EOL;

$libPath = "Bin/lib.php";
if(!file_exists($libPath)) die("no exists: ".$libPath.PHP_EOL);
include($libPath);

//print_r($argv);
$op = isset($argv[1]) ? $argv[1] : '';
$param1 = isset($argv[2]) ? $argv[2] : '';

#根据参数执行
$backEndTask = new BackEndTask();
if(!$op || $op=='help' || !method_exists($backEndTask, $op)){
    $backEndTask->help();
    exit;
}
if($op == 'showTask') {
    $backEndTask->showTask();
}
if($op == 'killTask') {
    $backEndTask->killTask();
}
if($op == 'startOneTask'){
    $backEndTask->startOneTask($param1);
}
if($op == 'startMultiTask'){
    $backEndTask->startMultiTask();
}

exit;

//$pid = run(array('taskPath'=>'-r "sleep(30);echo 44;"', 'taskType'=>'param', 'webCode'=>$webCode));
//echo 'task2: '.$pid.PHP_EOL;
//$pid = run(array('taskPath'=>'-r "sleep(20);echo 20044;"', 'taskType'=>'param', 'webCode'=>$webCode));
//echo 'task2: '.$pid.PHP_EOL;
//$pid = run(array('taskPath'=>'Bin/test.php', 'taskType'=>'file'));
//echo 'task3: '.$pid.PHP_EOL;
//$pid = run(array('taskPath'=>'Bin/test.php', 'taskType'=>'file'));
//echo 'task3: '.$pid.PHP_EOL;

class BackEndTask{
    private $flag = '';
    private $taskArr = array(
           array('name'=>'默认队列及用户队列', 'file'=>'Bin/resque.php'),
           array('name'=>'消息队列', 'file'=>'Bin/msgTaskResque.php'),
//            'Bin/test.php',
    );
    
    public function showTask(){
        $this->checkWebCode();
        $cmd = 'ps -ef|grep "\['.$this->flag.'\]"';
        exec($cmd, $out, $ret);

        if(!$out) echo "no task!!!!";
        array_map(function($line){
            echo $line.PHP_EOL;
        }, $out);
    }
    
    public function startOneTask($taskPath){
        if(!$taskPath) die('startOneTask fail: emtpy taskPath'.PHP_EOL);

        $taskType = file_exists($taskPath) ? 'file' : 'param';
        $pid = run(array('taskPath'=>$taskPath, 'taskType'=>$taskType));
        echo 'task: '.$pid.PHP_EOL;
    }
    
    public function startMultiTask(){
        echo "将要开启下列任务：", PHP_EOL;
        foreach($this->taskArr as $idx=>$task){
            echo ($idx+1).'. '.$task['name'].PHP_EOL;
        }
        $noSetupArr = array();
        if(getenv('MANUAL')){
            fwrite(STDOUT, PHP_EOL."注意：已开队列会影响新队列,不需要开启任务(序号，逗号分隔, 例如:1,2,3 默认跳过)：");
            $select = trim(fgets(STDIN));
            if($select) $noSetupArr = explode(',', str_replace(' ', '', $select));
        }
        foreach($this->taskArr as $num=>$task){
            if($noSetupArr && in_array($num+1, $noSetupArr)) continue;
            $this->startOneTask($task['file']);
        }
        echo "end startMultiTask".PHP_EOL;
    }
    
//    public function startMultiSiteTask($param){
//        $siteCfg = file_exists($param) ? file_get_contents($param) : $param;
//        $siteCfg = preg_replace('/[^a-zA-Z0-9_,]+/i','', $siteCfg);
//        
//        $siteArr = array();
//        
//        $siteArr = explode(',', $siteCfg);
//        foreach($siteArr as $key=>$site){
//            if(!preg_match('/[a-zA-Z0-9_]+/i', $site)) unset($siteArr[$key]);
//        }
//    }
    
    public function killTask(){
        $this->checkWebCode();
        $ps = "ps -ef|grep '\[{$this->flag}\]'|grep -v killTask|grep -v grep|awk '{print $2}'";
        exec($ps, $pidArr, $ret);
        if(!$pidArr) die("killTask result: no process".PHP_EOL);
        array_map(function($pid){
            killProcessAndChilds($pid, 9);
        }, $pidArr);
        exec($ps, $pidArr, $ret);
        if(!$pidArr) die('killTask succ'.PHP_EOL);
        die('killTask fail:' . implode(',', $pidArr) . PHP_EOL);
    }
    
    public function checkWebCode(){
        if(!$this->flag = getenv('FLAG')) die("no set FLAG=?".PHP_EOL);
    }
    
    public function help(){
        $scriptName = $_SERVER['_'];
        echo "web site backend tool  v0.1".PHP_EOL.PHP_EOL;
        echo "usage: FLAG=[FLAG] {$scriptName} showTask".PHP_EOL;
        echo "  or : FLAG=[FLAG] {$scriptName} killTask ".PHP_EOL;
        echo "  or : FLAG=[FLAG] {$scriptName} startOneTask [taskPath]".PHP_EOL;
        echo "  or : FLAG=[FLAG] {$scriptName} startMultiTask".PHP_EOL;
        echo "  or :  {$scriptName} help ".PHP_EOL;
        exit;
    }
}