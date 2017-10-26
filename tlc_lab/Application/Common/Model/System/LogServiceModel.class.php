<?php
namespace Common\Model\System;
use Think\Model;
use Common\Model\Common\BaseModel;

class LogServiceModel extends BaseModel{
    
    /**
     * 记录一般日志
     * @param  string $module  模块
     * @param  string $action  操作
     * @param  string $message 记录的详情
     */
    public function info($module, $action, $message, array $data = null){
        return $this->addLog('info', $module, $action, $message, $data);
    }

    /**
     * 记录警告日志
     * @param  string $module  模块
     * @param  string $action  操作
     * @param  string $message 记录的详情
     */
    public function warning($module, $action, $message, array $data = null){
        return $this->addLog('warning', $module, $action, $message, $data);
    }
    
    /**
     * 记录错误日志
     * @param  string $module  模块
     * @param  string $action  操作
     * @param  string $message 记录的详情
     */
    public function error($module, $action, $message, array $data = null){
        return $this->addLog('error', $module, $action, $message, $data);
    }
    
    /**
     * 日志搜索
     * @param  array   $conditions 搜索条件，
     *                 如array(
     *                 		'level'=>'info|warning|error', 
     *                      'nickname'=>'xxxxx',
     *                      'startDateTime'=> 'xxxx-xx-xx xx:xx',
     *                      'endDateTime'=> 'xxxx-xx-xx xx:xx'
     *                 );
     *                             
     * @param  array   $sort       按什么排序, 暂只提供'created'
     * @param  integer $start      开始行数
     * @param  integer $limit      返回最多行数
     * @return array        	   多维数组    
     * 
     */
    public function searchLogs($conditions, $sort, $start, $limit){
        $conditions = $this->prepareSearchConditions($conditions);
        switch ($sort) {
            case 'created':
                    $sort = array('createdTime','DESC');
                    break;
            case 'createdByAsc':
                    $sort = array('createdTime','ASC');
                    break;
            default:
                    throw $this->createServiceException('参数sort不正确。');
                    break;
        }
        $logs = $this->getLogDao()->searchLogs($conditions, $sort, $start, $limit);
        foreach ($logs as &$log) {
            $log['data'] = empty($log['data']) ? array() : json_decode($log['data'], true);
            unset($log);
        }
        return $logs;
    }

    /**
     * 根据指定搜索条件返回该条数。
     * @param  array   $conditions 搜索条件，
     *                 如array(
     *                 		'level'=>'info|warning|error', 
     *                      'nickname'=>'xxxxx',
     *                      'startDateTime'=> 'xxxx-xx-xx xx:xx',
     *                      'endDateTime'=> 'xxxx-xx-xx xx:xx'
     *                 );
     * @return interger           
     */
    public function searchLogCount($conditions){

        $conditions = $this->prepareSearchConditions($conditions);

        return $this->getLogDao()->searchLogCount($conditions);
    }

    public function analysisLoginNumByTime($startTime,$endTime , $siteSelect = 'local'){
        return $this->getLogDao()->analysisLoginNumByTime($startTime,$endTime, $siteSelect);
    }

    public function analysisLoginDataByTime($startTime,$endTime){
        return $this->getLogDao()->analysisLoginDataByTime($startTime,$endTime);
    }
    
    protected function addLog($level, $module, $action, $message, array $data = null){
        $currentUser =  $this->getCurrentUser();
        return $this->getLogDao()->addLog(array(
                'module' => $module,
                'action' => $action,
                'message' => $message,
                'data' => empty($data) ? '' : json_encode($data),
                'userId' => $currentUser['id'],
                'ip' => $currentUser['currentIp'],
                'createdTime' => time(),
                'level' => $level,
        ));		
    }
    
    protected function getLogDao(){
        return $this->createDao("System.Log");
    }
    
    protected function getUserService(){
        return $this->createService('User.UserService');
    }
    
    private function prepareSearchConditions($conditions){
        if (!empty($conditions['nickname'])) {
            $existsUser = $this->getUserService()->getUserByNickname($conditions['nickname']);
            $userId = $existsUser ? $existsUser['id'] : -1;
            $conditions['userId'] = $userId;
            unset($conditions['nickname']);
        }
        if (!empty($conditions['startDateTime']) && !empty($conditions['endDateTime'])) {
            $conditions['startDateTime'] = strtotime($conditions['startDateTime']);
            $conditions['endDateTime'] = strtotime($conditions['endDateTime']); 
        } else {
            unset($conditions['startDateTime']);
            unset($conditions['endDateTime']);
        }
        if (!empty($conditions['level']) && in_array($conditions['level'], array('info', 'warning', 'error'))) {
            $conditions['level'] = $conditions['level'];
        } else {
            unset($conditions['level']);
        }
        return $conditions;
    }
}
?>
