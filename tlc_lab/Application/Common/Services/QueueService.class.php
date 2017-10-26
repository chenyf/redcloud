<?php
/**
 * 队列管理
 * @author 钱志伟 2015-05-21
 */
namespace Common\Services;

use Resque;
class QueueService {

    public static function addJob($param=array()){
        $options = array(
            'jobName' => '',        #任务名, config.queue注册
            'param'   => array(),   #任务所需参数
        );
        $options = array_merge($options, $param);
        extract($options);
        
        if(!$jobName) return false;
        if(!array_key_exists($jobName, C('QUEUE'))) return false;
        if(!$webCode) $webCode = C('WEBSITE_CODE');
        if(!$webCode) return false;

        $jobCfg = C('QUEUE.'.$jobName);

        doLog(json_encode($jobCfg));

        $queue = $jobCfg['queueName'];
        // You can also use a DSN-style format:
        Resque::setBackend(C('REDIS.taskQueue'));
        $args = array(
            'queueName' => $queue,
            'jobName' => $jobCfg['func'],
            'time' => time(),
            'array' => $param,
        );

        $class = 'QueueDispatch'; #都必须经过队列调度
        $jobId = Resque::enqueue($queue, $class, $args, true);

        doLog("jobId:" . $jobId);
        //echo "Queued job ".$jobId."\n\n";
        return $jobId;
    }
}

?>
