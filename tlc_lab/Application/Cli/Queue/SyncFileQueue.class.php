<?php
/** 同步文件到云盘队列 **/

namespace Cli\Queue;

use Common\Lib\MailBat;
class SyncFileQueue
{
    public function syncFile($param=array()){
        if(empty($param["id"]) || empty($param["userNum"]) || empty($param["path"])){
            E("SyncFileQueue-缺少必要的参数");
        }

        if(empty($param["name"])){
            $param["name"] = "default-".date('Ymd-H_i_s');
        }
        
        $sync_cli_script = getParameter("sync.cloud.cli_script");
        $php_bin = C("php_bin_path") ?: "/usr/local/php/bin/php";
        $sync_pwd = C('sync_cloud_passwd');
        $shell = "{$php_bin} {$sync_cli_script} --unum={$param['userNum']} --path={$param['path']} --pwd={$sync_pwd} --name={$param['name']}";
        doLog($shell);
        @exec($shell,$err_info,$err_code);

        if($err_code != 0){
            self::getCourseResourceService()->changeResourceSyncStatus($param["id"],"failure","");
            self::throwError("同步任务失败！{$err_code} - shell：" . $shell . " - error_info:" . implode("|",$err_info),$param["id"]);
        }else{
            self::getCourseResourceService()->changeResourceSyncStatus($param["id"],"success",$err_info[0]);
        }
    }

    private function throwError($error,$resourceId=0,$resourceName='') {
        $msg  = 'SyncFileQueue队列上传错误:' . $error;
        self::recordLog($msg);
        if(!empty($resourceId) && !empty($resourceName) && C('SYNC_FILE_FAIL'))
            self::sendMail($msg,$resourceId . " —— " . $resourceName);
        E($msg);
    }

    private function sendMail($content,$title){
        $emailArr = C('SYSTEM_MANAGER');
        $email = implode(";", $emailArr);

        $html = "<div>同步文件到云盘失败：资料的ID：{$title}</div>";
        $html .= "<p>错误信息：{$content}</p>";

        $param['subject'] = "文档转换格式失败";
        $param['html'] = $html;
        $param['to'] = $email;
        $mailBat = MailBat::getInstance();
        $xml = $mailBat->sendMailBySohu($param);
        //解析返回的xml
        $xmlArr = json_decode(json_encode((array) simplexml_load_string($xml)),true);
        $res = $xmlArr['message'];
    }


    private function recordLog($log) {
        self::getLogService()->error("Queue",'SyncFileQueue.syncFile',$log);
    }

    private function getLogService(){
        return createService('System.LogService');
    }

    private function getCourseResourceService(){
        return createService('Course.CourseResourceService');
    }
}