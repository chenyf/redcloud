<?php
/**
 * 队列处理
 * @author 钱志伟 2015-05-22
 */

namespace Cli\Controller;

use Resque;
use Resque_Job_Status;
use Resque_Log;
use Resque_Worker;
use Common\Services\QueueService;
class QueueController
{
    
    public function __construct() {
        date_default_timezone_set('GMT');
    }

    /**
     * 运行队列处理任务
     * @example COUNT=3 INTERVAL=5 QUEUE='*' php55 index_cli.php --c=Queue --a=resqueAction
     */
    public function resqueAction(){
        $this->execTask();
    }
    
    
   public function JobAction(){
      $JOB= getenv('JOB'); 
      $CLASS = getenv("CLASS");
      if(empty($JOB)) {
	die('Specify the name of a job to add. e.g, php queue.php PHP_Job');
       }
        $REDIS_BACKEND = getenv('REDIS_BACKEND');
        if(!$REDIS_BACKEND)  $REDIS_BACKEND = C('REDIS.taskQueue');
        // A redis database number
        $REDIS_BACKEND_DB = getenv('REDIS_BACKEND_DB');
        if(!empty($REDIS_BACKEND)) {
            if (empty($REDIS_BACKEND_DB))
                Resque::setBackend($REDIS_BACKEND);
            else
                Resque::setBackend($REDIS_BACKEND, $REDIS_BACKEND_DB);
        }
        
        $args = array(
        'time' => time(),
        'array' => array(
		    'msg_type' =>2,
          ),
        );
        $jobId = Resque::enqueue($JOB, $CLASS, $args, true);
        echo "Queued job ".$jobId."\n\n";
   }
   //QUEUEID=a437aad225b44c8809a20095af7ae8ff    php55 index_cli.php --c=Queue --a=checkStatusAction 
    public function checkStatusAction(){
        
        $QUEUEID= getenv('QUEUEID');
        if(empty($QUEUEID)) {
            die('Specify the ID of a job to monitor the status of.');
        }
        $REDIS_BACKEND = getenv('REDIS_BACKEND');
        if(!$REDIS_BACKEND)  $REDIS_BACKEND = C('REDIS.taskQueue');
        // A redis database number
        $REDIS_BACKEND_DB = getenv('REDIS_BACKEND_DB');
        if(!empty($REDIS_BACKEND)) {
            if (empty($REDIS_BACKEND_DB))
                Resque::setBackend($REDIS_BACKEND);
            else
                Resque::setBackend($REDIS_BACKEND, $REDIS_BACKEND_DB);
        }
        
        $status = new Resque_Job_Status($QUEUEID);
        if(!$status->isTracking()) {
                die("Resque is not tracking the status of this job.\n");
        }

        echo "Tracking status of ".$QUEUEID.". Press [break] to stop.\n\n";
        while(true) {
                fwrite(STDOUT, "Status of ".$QUEUEID." is: ".$status->get()."\n");
                sleep(1);
        }   

            }
    /**
     * 创建任务
     * php55 index_cli.php --c=Queue --a=addJobAction
     * @author 钱志伟 2015-05-22
     */
    public function addJobAction(){
          $options = array(
            'jobName'=>'',
        );
        $param = array_merge($options,$_GET);
        $jobName = $param["jobName"] = 'test';
        unset($param["jobName"]);
        $param['333'] = 3;
        $param['34234'] = 32;
        $param = array(
            'school'  => 'aaa',
            'xxxx' => 'bbb',
            'yyy' =>  'cccc',
            'className' => '计算机（1）班',
            'year' => 2034,
        );
        $jobId = QueueService::addJob(array(
            'jobName'=>$jobName,
            'param' =>$param,
//            'webCode' => 'qzw111',
        ));
        
//        $jobId = QueueService::addJob(array(
//            'jobName'=>'resetUserPass',
//            'param' =>array('uid'=>1),
//        ));
        
        
        if(!$jobId) die('add job fail!'.PHP_EOL.PHP_EOL);
        echo 'job add succ:' . $jobId . PHP_EOL;
    }
    /**
     * 执行任务
     * @author 钱志伟 2015-05-22
     */
    private function execTask(){
        
        $QUEUE = getenv('QUEUE');
        //$QUEUE = "default";
        if(empty($QUEUE)) {
            die("Set QUEUE env var containing the list of queues to work.\n");
        }

        /**
         * REDIS_BACKEND can have simple 'host:port' format or use a DSN-style format like this:
         * - redis://user:pass@host:port
         *
         * Note: the 'user' part of the DSN URI is required but is not used.
         */
        $REDIS_BACKEND = getenv('REDIS_BACKEND');
        if(!$REDIS_BACKEND)  $REDIS_BACKEND = C('REDIS.taskQueue');

        // A redis database number
        $REDIS_BACKEND_DB = getenv('REDIS_BACKEND_DB');
        if(!empty($REDIS_BACKEND)) {
            if (empty($REDIS_BACKEND_DB))
                Resque::setBackend($REDIS_BACKEND);
            else
                Resque::setBackend($REDIS_BACKEND, $REDIS_BACKEND_DB);
        }


        $logLevel = false;
        $LOGGING = getenv('LOGGING');
        $VERBOSE = getenv('VERBOSE');
        $VVERBOSE = getenv('VVERBOSE');
        if(!empty($LOGGING) || !empty($VERBOSE)) {
            $logLevel = true;
        }
        else if(!empty($VVERBOSE)) {
            $logLevel = true;
        }

        $APP_INCLUDE = getenv('APP_INCLUDE');
        //$APP_INCLUDE = 'index_cli.php';
        if($APP_INCLUDE) {
            if(!file_exists($APP_INCLUDE)) {
                die('APP_INCLUDE ('.$APP_INCLUDE.") does not exist.\n");
            }
            require_once $APP_INCLUDE;
        }

        // See if the APP_INCLUDE containes a logger object,
        // If none exists, fallback to internal logger
        if (!isset($logger) || !is_object($logger)) {
            $logger = new Resque_Log($logLevel);
        }

        $BLOCKING = getenv('BLOCKING') !== FALSE;

        $interval = 5;
        $INTERVAL = getenv('INTERVAL');
        if(!empty($INTERVAL)) {
            $interval = $INTERVAL;
        }

        $count = 1;
        $COUNT = getenv('COUNT');
        if(!empty($COUNT) && $COUNT > 1) {
            $count = $COUNT;
        }

        $PREFIX = getenv('PREFIX');
        if(!empty($PREFIX)) {
            $logger->log(Psr\Log\LogLevel::INFO, 'Prefix set to {prefix}', array('prefix' => $PREFIX));
            Resque_Redis::prefix($PREFIX);
        }

        \Resque_Event::listen('beforeFork', array('My_Resque_Plugin', 'beforeFork'));

        if($count > 1) {
            for($i = 0; $i < $count; ++$i) {
                $pid = Resque::fork();

                if($pid == -1) {
                    $logger->log(Psr\Log\LogLevel::EMERGENCY, 'Could not fork worker {count}', array('count' => $i));
                    die();
                }
                // Child, start the worker
                else if(!$pid) {
                    $queues = explode(',', $QUEUE);
                    $worker = new Resque_Worker($queues);
                    $worker->setLogger($logger);
                    $logger->log(\Psr\Log\LogLevel::NOTICE, 'Starting worker {worker}', array('worker' => $worker));
                    $worker->work($interval, $BLOCKING);
                    break;
                }
            }
        }
        // Start a single worker
        else {
            $queues = explode(',', $QUEUE);
            $worker = new Resque_Worker($queues);
            $worker->setLogger($logger);

            $PIDFILE = getenv('PIDFILE');
            if ($PIDFILE) {
                file_put_contents($PIDFILE, getmypid()) or
                    die('Could not write PID information to ' . $PIDFILE);
            }

            $logger->log(\Psr\Log\LogLevel::NOTICE, 'Starting worker {worker}', array('worker' => $worker));
            $worker->work($interval, $BLOCKING);
        }
    }
}
