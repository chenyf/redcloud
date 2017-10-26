<?php
/**
 * 默认队列
 * @author tanhaitao 2015-12-16
 */
namespace Cli\Queue;
use Common\Services\BatProcessService;
class BatTaskQueue {

    public function execSubTask($param = array()){
        $options = array(
            'code' => '',
            'strId' => '',
            'microtime' => '',
            'itemMicrotime' => '',
        );
        $options = array_merge($options, $param);
        extract($options);
        
        $BatProcessObj = new BatProcessService();
        
        #判断父项是否存在和父项是否正在进行
        $isExistsTask = $BatProcessObj->isExistsTaskInfo($code, $strId, $microtime);
        if(!$isExistsTask)
            return false;
        $taskInfo = $BatProcessObj->getTaskInfo($code, $strId, $microtime);
        if($taskInfo["status"] != "doing")
            return false;
        
        #判断子项是否存在和子项是否未开始
        $isExistsItem = $BatProcessObj->isExistsItemInfo($code, $strId, $itemMicrotime);
        if(!$isExistsItem)
            return false;
        $itemInfo = $BatProcessObj->getTaskItemInfo($code, $strId, $itemMicrotime);
        if($itemInfo["status"] != "nostart")
            return false;
        
        #判断是否有回调函数
        $batTask = C("BAT_TASK");
        $taskRule = $batTask[$code];
        if( empty($taskRule["processTaskMethod"]) )
            return false;
        
        #调用回调函数(自定义) 处理子项数据
        $callback = call_user_func($taskRule['processTaskMethod'], $options);
        if( empty($callback) )
            return false;
        
        #修改子项和父项
        $itemStatus = $callback["status"] ? "success" : "failure";
        $itemArr = array("status" => $itemStatus, "remark" => $callback["message"] );
        $BatProcessObj->updateTaskItemInfo($code, $strId, $itemMicrotime, $itemArr);
        $BatProcessObj->modifyTaskInfo($options);
    }
    
}