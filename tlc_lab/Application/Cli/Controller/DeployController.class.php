<?php
/**
 * 部署（多库模式）
 * @author 钱志伟 2015-07-01
 */

namespace Cli\Controller;

use \Common\Lib\EnvCfgManage;
class DeployController {
    private $dbRsyncInfoDir = "/tmp/db_struc_dir";
    private $errorLog = "/tmp/db_struc_dir/error.log";
    
    public function __construct() {
        date_default_timezone_set('GMT');
    }

    /**
     * php5.5 index_cli.php --c=Deploy --a=rsyncDBStruc
     * @author 钱志伟 2015-08-17
     */
    public function rsyncDBStruc(){
        do{
            fwrite(STDOUT, "请输入网站的webcode(全部=>all)：");
            $webcode = trim(fgets(STDIN));
        }while(!$webcode);

        $baseWebCode = 'center';

       // print_r(EnvCfgManage::getDBCfg($baseWebCode));
        
//        $allWebCode = $webcode=='all' ? array_keys(C('ALL_WEB_CODE')) : array($webcode);
        
        $rsynceFinishCfg = array();
        
        foreach(C('ALL_WEB_CODE') as $targetWebCode=>$info){
            if($webcode!='all' && $webcode!=$targetWebCode) continue;

            $dbCfg = EnvCfgManage::getDBCfg($targetWebCode);
            echo str_pad($targetWebCode, 10).' use db ======================>' . $dbCfg['DB_HOST'].':'. str_pad($dbCfg['DB_NAME'], 10).'=====>';
            
            if($info['status']!=1) {
                echo "网站状态关闭，不进行同步";
            } else{
                $flag = md5(json_encode($dbCfg));
                if($dbCfg && in_array($flag, $rsynceFinishCfg)){
                    echo "跳过:上面有相同库" . PHP_EOL;
                    continue;
                }

                $resultStr = $this->__rsyncDBStruc($baseWebCode, $targetWebCode);
                echo $resultStr . PHP_EOL;
                $rsynceFinishCfg[] = $flag;
            }
            echo PHP_EOL;
        }
    }
    

    private function __rsyncDBStruc($baseWebCode='center', $targetWebCode=''){
        if($baseWebCode == $targetWebCode) {
            return "same baseDb and targetDb!";
//            echo "{$baseWebCode} ->{$targetWebCode} result: jump".PHP_EOL;
//            return false;
        }

        if(!is_dir($this->dbRsyncInfoDir)) mkdir($this->dbRsyncInfoDir, 0755);
        if(!is_dir($this->dbRsyncInfoDir)) die("create dir {$this->dbRsyncInfoDir} fail!".PHP_EOL);
        
        $baseDbCfg = EnvCfgManage::getDBCfg($baseWebCode);
        $baseDns = $this->getDbDns($baseDbCfg);
        $targetDbCfg = EnvCfgManage::getDBCfg($targetWebCode);
        
        $targetDns = $this->getDbDns($targetDbCfg);

        $patch = $this->dbRsyncInfoDir . '/'.$targetDbCfg['DB_NAME'].'_'.$targetWebCode.'.'.date('Ymd').".patch.sql";
        if(file_exists($patch)) @unlink($patch);
        if(file_exists($patch)) die('del old fail:'.$patch);
        
        $cmd = sprintf("%s  %s  %s  -c  --output-directory=%s  --tag='%s' >%s", C('SCHEMASYNC_CMD'), $baseDns, $targetDns, $this->dbRsyncInfoDir, $targetWebCode, $this->errorLog);
//        echo $cmd, PHP_EOL;exit;
        echo PHP_EOL;
        exec($cmd, $out, $ret);
        
        if(!file_exists($patch)){
            echo "path fail! look for ".$this->errorLog . PHP_EOL;
            echo file_get_contents($this->errorLog).PHP_EOL;
            exit;
        }
      //  print_r($targetDbCfg);
//        echo $patch.PHP_EOL;
        
        $runPatchCmd = sprintf("%s -h %s -u %s -p%s %s<%s", C('MYSQL_CMD'), $targetDbCfg['DB_HOST'], $targetDbCfg['DB_USER'], $targetDbCfg['DB_PWD'], $targetDbCfg['DB_NAME'], $patch);
//        echo $runPatchCmd. PHP_EOL;
        exec($runPatchCmd);
        return 'success';
    }

    private function getDbDns($param=array()){
        $options = array(
            'DB_HOST' => '',
            'DB_NAME' => '',
            'DB_USER' => '',
            'DB_PWD'  => '',
            'DB_PORT' => 3306,
        );
        $options = array_merge($options, $param);
        extract($options);
        
        return "mysql://{$DB_USER}:{$DB_PWD}@{$DB_HOST}:{$DB_PORT}/{$DB_NAME}";
    }
    
    /**
     * php5.5 index_cli.php --c=Deploy --a=help
     */
    public function help(){
        $scriptName = 'php5.5 index_cli.php --c=Deploy';
        echo "web deploy manager tool  v0.1".PHP_EOL.PHP_EOL;
        echo "usage: {$scriptName} --a=rsyncDBStruc".PHP_EOL;
        echo "usage: {$scriptName} --help".PHP_EOL;
        exit;
    }
    
}
