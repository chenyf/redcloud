<?php
argvToGet($argv);

defined('NOW_TIME') || define('NOW_TIME',  $_SERVER['REQUEST_TIME']);
//if (!defined('CENTER')) {
//	define('CENTER', '');
//}

require_once "index.php";

function argvToGet($argv){
    foreach($argv as $k=>$v){
        #qzw 2015-08-31 “=”后支持- 例如  --webcode=qzw-zzz 
        if(preg_match('/--([a-zA-Z0-9_]+)\s*=([\[\],a-zA-Z0-9_\-\/]+)/',$v,$match)){
          $_GET[$match[1]] = $match[2];
        }
    }
    if(!isset($_GET["m"])) $_GET["m"] = "Cli";
    if(!isset($_GET["c"])) $_GET["c"] = "Index";
    if(!isset($_GET["a"])) $_GET["a"] = "IndexAction";    
    $query =$_GET["m"]."/".$_GET['c']."/".$_GET['a'];
    unset($_GET["m"]);
    unset($_GET["c"]);
    unset($_GET["a"]);
    $_GET["s"] = $query;  

}

/**
 * 队列调度
 * @author 钱志伟 2015-05-31
 */
class QueueDispatch{
    public function perform(){
        $args = $this->args;
        
        $queueName = ucfirst($args['queueName']);
        $method = $args['jobName'];
        $param = $args['array'];
        
        $queueClass = 'Cli\\Queue\\'. $queueName . 'Queue';

        $queueClass::$method($param);
    }
}