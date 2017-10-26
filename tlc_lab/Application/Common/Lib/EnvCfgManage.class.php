<?php
namespace Common\Lib;
/**
 * 环境配置文件config.env.xxx.php管理工具
 * @author 钱志伟 2015-08-10
 */
class EnvCfgManage {
    public function __construct() {}
    /**
     * 获得数据库配置
     * @author 钱志伟 2015-08-10
     */
    public static function getDBCfg($webCode=''){
        static $cacheArr = array();
        $webCode = strtolower($webCode);

        $dbCfg = unserialize(DEFAULT_DB_CFG);
        if(!$webCode) return $dbCfg;
        
        if(!isset($cacheArr[$webCode])){
            if($webCode == 'center') $cacheArr[$webCode] = C('DB_CENTER');
            else{
                $envCfg = self::getEnvCfg($webCode);
                foreach($dbCfg as $dbCfgName=>$dbCfgVal){
                    if(isset($envCfg[$dbCfgName])) $dbCfg[$dbCfgName] = $envCfg[$dbCfgName];
                }
                $dbCfg['DB_NUM'] = count($cacheArr) + 10;
                $cacheArr[$webCode] = $dbCfg;
            }
        }
        return $cacheArr[$webCode];
    }
    /**
     * 获得环境配置(优先取自己单独的配置，无单独配置config.env.xxx.php，取公共配置 config.env.common.php)
     * @author 钱志伟 2015-08-10
     */
    public static function getEnvCfg($webCode=''){
        $envCfg = self::readEnvCfg(self::getEnvCfgFile($webCode));
        if(!$envCfg){
            $envCfg = self::readEnvCfg(self::getEnvCfgFile('common'));
        }
        return array_merge(unserialize(DEFAULT_ENV_CFG), $envCfg);
    }

    /**
     * 获得环境配置文件路径
     * @author 钱志伟 2015-08-10
     */
    public static function getEnvCfgFile($webCode=''){
        return WEB_ENV_CONF_PATH . "config.env.{$webCode}.php";
    }
    
    /**
     * 读取环境配置文件
     * @author 钱志伟 2015-08-10
     */
    public static function readEnvCfg($webEnvFile){
        if(!file_exists($webEnvFile)) return $cfgArr;
        
        static $cacheArr = array();
        
        if(!isset($cacheArr[$webEnvFile])) {
            $cfgArr = array();
            $cfgContent = file_get_contents($webEnvFile);
//echo $cfgContent;
//echo '========================='.PHP_EOL;
            #去掉注释 qzw 2015-08-24
            $cfgContent = preg_replace("/(\/\/.*\n)/", "\n", $cfgContent);
            $cfgContent = preg_replace("/(\/\*.*\*\/)/", "", $cfgContent);
//echo $cfgContent;
//exit;
            $r = preg_match_all("/\s*define\((['\"])(?P<cfgName>.*)\\1,\s*(?P<cfgVal>.*)\);/", $cfgContent, $match);
            if($r){
                $cfgName = $match['cfgName'];
                $cfgVal = $match['cfgVal'];
                foreach($cfgName as $k=>$name){
                    if(strtolower($cfgVal[$k])=='true') $cfgVal[$k] = true;
                    elseif(strtolower($cfgVal[$k])=='false') $cfgVal[$k] = false;
                    $cfgVal[$k] = trim($cfgVal[$k], "'");
                    $cfgVal[$k] = trim($cfgVal[$k], '"');
                    $cfgArr[$name] = $cfgVal[$k];
                }
            }
            $cacheArr[$webEnvFile] = $cfgArr;
        }
        return $cacheArr[$webEnvFile];
    }
}