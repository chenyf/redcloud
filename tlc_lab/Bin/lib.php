<?php
function run($param=array()){
    $options = array(
        'taskPath' => '', #example: Bin/resque.php
        'taskType' => 'file', #任务类型：file=>文件，param=>其他
        'webCode'  => getenv('WEB_CODE'), #网站代号
    );
    $options = array_merge($options, $param);
    extract($options);

    $pid = pcntl_fork();
    if($pid<0){
        echo "fork fail: line: ".__LINE__.PHP_EOL;
        exit;
    }elseif($pid>0){
        echo "fork succ".PHP_EOL;
        return $pid;
//        $endpid=pcntl_waitpid($pid,$status,2);
//        if($status==0){  
//            //说明可能是daemon程序，后台运行  
//            if(file_exists("/proc/".($pid+1)."/stat")){  
//                //说明进程存在，需要定时判断  
//                write_log("program start success");
//                for (;;) {   
//                    if(file_exists("/proc/".($pid+1)."/stat")) {  
//                        //write_log("program is alive");
//                        usleep(1000000); 
//                    }else {  
//                        write_log("program die");
//                        break;  
//                    }
//                }     
//            } else {  
//                //说明进程不存在，并且不是非daemon状态。  
//                write_log("program start failed");   
//                exit(0);  
//            }
//        } else if($status>0){  
//            //说明是非daemon 程序，退出来了,需要重新启动  
//            write_log("program die");   
//            //continue;  
//        }else {  
//            exit(0);  
//        }
    }else{
        if($taskType=='file' && !file_exists($taskPath)) die("resque exec fail: no exists resque ".$taskPath.PHP_EOL);
        //echo "WEB_CODE={$webCode} /usr/sbin/php5.5 {$taskPath} --webCode=[{$webCode}]".PHP_EOL;
        $arg = $taskType == 'file' ? "{$taskPath}" : $taskPath;
        exec("/usr/sbin/php5.5 $arg");
        exit;
    }
}

function write_log($log, $webCode=''){
    echo "[".date('Y-m-d H:i:s')."] ".$log.PHP_EOL;
}

function killProcessAndChilds($pid,$signal) { 
    exec("ps -ef| awk '$3 == '$pid' { print  $2 }'", $output, $ret); 
    if($ret) return 'you need ps, grep, and awk'; 
    while(list(,$t) = each($output)) { 
        if ( $t != $pid ) { 
            killProcessAndChilds($t,$signal); 
        } 
    } 
    //echo "killing ".$pid." "; 
    posix_kill($pid, 9); 
} 