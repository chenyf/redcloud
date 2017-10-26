<?php
namespace System\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Services\BatProcessService;
use Common\Lib\Paginator;

class BatTaskController extends \Home\Controller\BaseController{

    public function pollAction (Request $request, $code, $strId, $microtime){
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $code = isset($data["code"]) ? $data["code"] : "";
            $strId = isset($data["strId"]) ? $data["strId"] : "";
            $microtime = isset($data["microtime"]) ? $data["microtime"] : "";
            $pollTaskTm = (isset($data["pollTaskTm"]) && $data["pollTaskTm"]) ? intval($data["pollTaskTm"]) : 0;

            $BatProcessObj = $this->getBatProcessService();
            $param = array();
            $param['code'] = $code;
            $param['strId'] = $strId;
            $param['microtime'] = $microtime;
            $param['pollTm'] = $pollTaskTm;

            $i = 0;
            while ($i++ < 55) {
                $pollTask = $BatProcessObj->getPollTaskList($param);
                if (!empty($pollTask)) {
                    $index = count($pollTask)-1;
                    $tm = strtotime($pollTask[$index]['processTm']);
                    $this->ajaxReturn(array('success'=>true,'info'=>'有数据','data'=>array('tm'=>$tm,'data'=>$pollTask)));
                }
                sleep(1);
            }
            $this->ajaxReturn(array('success'=>false,'info'=>'请求超时'));
        }

        $conf = $this->getConf($code);
        $name = $conf["name"];
        $itemTask = $conf["itemTask"];
        
        return $this->render('BatTask:poll', array(
                'code' => $code,
                'strId' => $strId,
                'microtime' => $microtime,
                'name' => $name,
                'itemTask' => $itemTask
            )
        );
    }

    /**
     * 子项任务列表
     * @author fubaosheng 2016-01-18
     */
    public function itemTaskListAction(Request $request, $code, $strId, $microtime, $limit){
        $limit = is_numeric($limit) ? intval($limit) : 10;
        $BatProcessObj = $this->getBatProcessService();
        $paramArr = array("code"=>$code,"strId"=>$strId,"microtime"=>$microtime,"status"=>"all");
        $count = $BatProcessObj->getTaskItemCount($paramArr);
        $paginator = new Paginator($this->get('request'), $count, $limit);
        $paramArr["start"] = $paginator->getOffsetCount();
        $paramArr["length"] = $paginator->getPerPageCount();
        $itemTaskList = $BatProcessObj->getTaskItemList($paramArr);
        
        $conf = $this->getConf($code);
        $name = $conf["name"];
        $itemTask = $conf["itemTask"];
        
        return $this->render('BatTask:item-task-list', array(
            "itemTaskList" => $itemTaskList,
            "paginator" => $paginator,
            "code" => $code,
            "strId" => $strId,
            "microtime" => $microtime,
            "name" => $name,
            "itemTask" => $itemTask
        ));
    }
    
    /**
     * 子项任务列表数据
     * @author fubaosheng 2016-01-19
     */
    public function itemTaskListDataAction(Request $request, $code, $strId, $microtime, $page, $limit){
        $page =  is_numeric($page)  ? intval($page)  : 1;
        $limit = is_numeric($limit) ? intval($limit) : 10;
        $BatProcessObj = $this->getBatProcessService();
        $paramArr = array("code"=>$code,"strId"=>$strId,"microtime"=>$microtime,"status"=>"all");
        $count = $BatProcessObj->getTaskItemCount($paramArr);
        $pageCount = ceil($count/$limit);
        $pageCount = ($page >= $pageCount) ? $pageCount : ($page <= 1 ? 1 : $page);
        $paramArr["start"] = ($pageCount - 1) * $limit;
        $paramArr["length"] = $limit;
        $itemTaskList = $BatProcessObj->getTaskItemList($paramArr);
        return $this->ajaxReturn(array('data'=>$itemTaskList,'info'=>'成功','status'=>true));
    }
    
    /**
     * 父项任务列表
     * @author fubaosheng 2016-01-18
     */
    public function taskListAction(Request $request, $code, $strId, $limit){
        $limit = is_numeric($limit) ? intval($limit) : 10;
        $BatProcessObj = $this->getBatProcessService();
        $paramArr = array("code"=>$code,"strId"=>$strId,"status"=>"all");
        $count = $BatProcessObj->getTaskListCount($paramArr);
        $paginator = new Paginator($this->get('request'), $count, $limit);
        $paramArr["start"] = $paginator->getOffsetCount();
        $paramArr["length"] = $paginator->getPerPageCount();
        $taskList = $BatProcessObj->getTaskList($paramArr);
        
        $conf = $this->getConf($code);
        $name = $conf["name"];
        $task = $conf["task"];
        
        return $this->render('BatTask:task-list', array(
            "taskList" => $taskList,
            "paginator" => $paginator,
            "code" => $code,
            "strId" => $strId,
            "name" => $name,
            "task" => $task
        ));
    }
    
    /**
     * 父项任务列表数据
     * @author fubaosheng 2016-01-19
     */
    public function taskListDataAction(Request $request, $code, $strId, $page, $limit){
        $page =  is_numeric($page)  ? intval($page)  : 1;
        $limit = is_numeric($limit) ? intval($limit) : 10;
        $BatProcessObj = $this->getBatProcessService();
        $paramArr = array("code"=>$code,"strId"=>$strId,"status"=>"all");
        $count = $BatProcessObj->getTaskListCount($paramArr);
        $pageCount = ceil($count/$limit);
        $pageCount = ($page >= $pageCount) ? $pageCount : ($page <= 1 ? 1 : $page);
        $paramArr["start"] = ($pageCount - 1) * $limit;
        $paramArr["length"] = $limit;
        $taskList = $BatProcessObj->getTaskList($paramArr);
        return $this->ajaxReturn(array('data'=>$taskList,'info'=>'成功','status'=>true));
    }
    
    /**
     * 开始任务
     * @author fubaosheng 2016-01-18
     */
    public function startTaskAction(Request $request, $code, $strId, $microtime){
        $BatProcessObj = $this->getBatProcessService();
        $isExistsTask = $BatProcessObj->isExistsTaskInfo($code, $strId, $microtime);
        if(!$isExistsTask)
            return $this->ajaxReturn(array('data'=>'','info'=>'任务不存在','status'=>false));
        $taskInfo = $BatProcessObj->getTaskInfo($code, $strId, $microtime);
        if($taskInfo["status"] == "doing")
            return $this->ajaxReturn(array('data'=>'','info'=>'任务已经进行','status'=>false));
        if($taskInfo["status"] == "stop")
            return $this->ajaxReturn(array('data'=>'','info'=>'任务已经停止','status'=>false));
        if($taskInfo["status"] == "finish")
            return $this->ajaxReturn(array('data'=>'','info'=>'任务已经完成','status'=>false));
        $result = $BatProcessObj->startTask($code, $strId, $microtime);
        if($result["status"]){
            $tr = $this->getTaskListTr($code, $strId, $microtime);
            return $this->ajaxReturn(array('data'=>$tr,'info'=>'操作成功','status'=>true));
        }else{
            return $this->ajaxReturn(array('data'=>'','info'=>$result['message'],'status'=>false));
        }
    }
    
    /**
     * 获取任务tr
     * @author fubaosheng 2016-01-19
     */
    private function getTaskListTr($code, $strId, $microtime){
        $conf = $this->getConf($code);
        $task = $conf["task"];
        $BatProcessObj = $this->getBatProcessService();
        $info = $BatProcessObj->getTaskInfo($code, $strId, $microtime);
        $info["microtime"] = $microtime;
        $tr =  $this->render('BatTask:task-list-tr', array(
                    "taskItem" => $info,
                    "code" => $code,
                    "strId" => $strId,
                    "task" => $task
                ),true);
        return $tr;
    }
    
     /**
     * 停止任务
     * @author fubaosheng 2016-01-18
     */
    public function stopTaskAction(Request $request, $code, $strId, $microtime){
        $BatProcessObj = $this->getBatProcessService();
        $isExistsTask = $BatProcessObj->isExistsTaskInfo($code, $strId, $microtime);
        if(!$isExistsTask)
            return $this->ajaxReturn(array('data'=>'','info'=>'任务不存在','status'=>false));
        $taskInfo = $BatProcessObj->getTaskInfo($code, $strId, $microtime);
        if($taskInfo["status"] == "stop")
            return $this->ajaxReturn(array('data'=>'','info'=>'任务已经停止','status'=>false));
        if($taskInfo["status"] == "finish")
            return $this->ajaxReturn(array('data'=>'','info'=>'任务已经完成','status'=>false));
        $result = $BatProcessObj->stopTask($code, $strId, $microtime);
        if($result["status"]){
            $tr = $this->getTaskListTr($code, $strId, $microtime);
            return $this->ajaxReturn(array('data'=>$tr,'info'=>$result['message'],'status'=>true));
        }else{
            return $this->ajaxReturn(array('data'=>'','info'=>$result['message'],'status'=>false));
        }
    }
    
    public function getBatProcessService(){
        return new BatProcessService();
    }
    
    public function getConf($code){
        $batTask = C("BAT_TASK");
        return $batTask[$code];
    }

}