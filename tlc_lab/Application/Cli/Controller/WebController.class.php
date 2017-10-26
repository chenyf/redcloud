<?php
/**
 * 网站控制
 * @author 钱志伟 2015-07-01
 */

namespace Cli\Controller;

use \Common\Lib\EnvCfgManage;
class WebController {
    private $srcLogoDir = "/data_1/html/wanglei/cloud/Public/dataFolder/xitong/baidu_school_thumb/";

    private $checkField = array(
        'webCode' => array(
            'add'=>"ALTER TABLE `%s` ADD COLUMN `webCode`  char(15) NULL DEFAULT ''  COMMENT 'web代号'",
            'del'=>"ALTER TABLE `%s` DROP COLUMN `webCode`",
            'set'=>"UPDATE `%s` SET `webCode`='%s'",
            ),
        'owebPriv' => array(
            'add'=>"ALTER TABLE `%s` ADD COLUMN `owebPriv`  char(3)  NULL DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）'",
            'del'=>'ALTER TABLE `%s` DROP COLUMN `owebPriv`',
            'set'=>"UPDATE `%s` SET `owebPriv`='%s' WHERE `webCode`='%s'",
            ),
    );
    private $excludeTable = array('aaa');

    public function __construct() {
        date_default_timezone_set('GMT');
    }
    /**
     * php55 index_cli.php --c=Web --a=runAction
     */
    public function runAction(){
        $op = isset($_GET['op']) ? $_GET['op'] : 'showOp';
        if(method_exists($this, $op)) $this->$op();
        else {
            $this->showOp();
        }
    }
    /**
     * 批处理
     * php5.5 index_cli.php --c=Web --a=batAction
     */
    public function batAction(){
        $cmd = isset($_GET['cmd']) ? $_GET['cmd'] : 'showOp';
        if(method_exists($this, $cmd)) $this->$cmd();
        else {
            $this->showOp();
        }
    }

    /**
     * 批处理
     * php5.5 index_cli.php --c=Web --a=tmpAction
     */
    public function tmpAction(){
        $cmd = isset($_GET['cmd']) ? $_GET['cmd'] : 'showOp';
        if(method_exists($this, $cmd)) $this->$cmd();
        else {
            $this->showOp();
        }
    }

    /**
     * 继续调用mkWebcodeCfgFile
     */
    private function continueMkCfgFile($path,$example,$webCfgPath){
        $examplePath = $path."/".$example;
        $codeArr = C("ALL_WEB_CODE");
        do{
            fwrite(STDOUT, "请输入webCode：");
            $webCode = trim(fgets(STDIN));
            if(!array_key_exists($webCode,$codeArr)) echo("webCode不合法".PHP_EOL);
        }while(!array_key_exists($webCode,$codeArr));
        $webCodeConf = "config.env.{$webCode}.php";
        $webPath = $webCfgPath."/".$webCodeConf;

        if(!file_exists($webPath)){
            $originPath = $path."/".$webCodeConf;
            if(!file_exists($originPath)){
                $r = copy($examplePath, $originPath);
                if(!$r) die("配置模板{$example}复制失败");
            }
            fwrite(STDOUT, "请输入POLYV_DIR_ID：");
            $polyvId = trim(fgets(STDIN));
            if(empty($polyvId) || !is_numeric($polyvId)) die("POLYV_DIR_ID不合法");
            fwrite(STDOUT, "请输入SOHU_LABEL_ID：");
            $sohuId = trim(fgets(STDIN));
            if(empty($sohuId) || !is_numeric($sohuId)) die("SOHU_LABEL_ID不合法");
            $file = file_get_contents($originPath);
            $file = preg_replace("/(define\('POLYV_DIR_ID',)\s*.*\)/", "define('POLYV_DIR_ID',      {$polyvId})", $file);
            $file = preg_replace("/(define\('SOHU_LABEL_ID',)\s*.*\)/", "define('SOHU_LABEL_ID',     {$sohuId})", $file);
            file_put_contents($originPath, $file, LOCK_EX);
            exec("ln -s {$originPath} {$webPath}");
            echo "软连接创建成功\r\n";
        }

        fwrite(STDOUT, "是否查看(Y/N)：");
        $cat = strtolower(trim(fgets(STDIN)));
        if($cat == "y") echo file_get_contents($webPath)."\r\n";

        fwrite(STDOUT, "是否继续(Y/N)：");
        $continue = strtolower(trim(fgets(STDIN)));
        if($continue == "y") $this->continueMkCfgFile($path,$example,$webCfgPath);
    }

    /**
     * 批处理WebEnvConf
     * php55 index_cli.php --c=Web --a=tmpAction --cmd=mkWebcodeCfgFile
     */
    public function mkWebcodeCfgFile(){
        fwrite(STDOUT, "请输入模板路径(默认当前)：");
        $path = trim(fgets(STDIN));
        if(empty($path)){
            $path =  "/server/data0/deploy_v2/webEnvConf";
            echo "当前路径：{$path}\r\n";
        }
        if(!file_exists($path)) die("模板路径不存在");

        fwrite(STDOUT, "配置模板(config.env.example.php)：");
        $example = trim(fgets(STDIN)) ? : "config.env.example.php";
        $examplePath = $path."/".$example;
        if(!file_exists($examplePath)) die("配置模板{$example}不存在");

        fwrite(STDOUT, "网站配置路径(Application/Common/Conf/WebEnvConf)：");
        $webPath = trim(fgets(STDIN));
        if(!$webPath) $webPath = SITE_PATH . '/Application/Common/Conf/WebEnvConf';
        if(!file_exists($webPath)) die("网站配置路径不存在:".$webPath.PHP_EOL);

        $this->continueMkCfgFile($path,$example,$webPath);
        die;
    }


    /**
     * 批量更新school表
     * php55 index_cli.php --c=Web  --a=batAction     --cmd=checkAndProcessSchool     --webcode=qzw,qzw2,qzw3
     */
    public function checkAndProcessSchool(){
        $code = isset($_GET['webcode']) ? $_GET['webcode'] : "";
        $codeArr = $code ? explode(',', $code) : array();

        $allWebCodeArr = C("ALL_WEB_CODE");
        foreach($allWebCodeArr as $k=>$v){
            $webCode = $k;
            if($codeArr && !in_array($webCode, $codeArr)) continue;

            if(!empty($v['domainName'])){
                $domain = implode(',', $v['domainName']);
            }else{
                $domain='';
            }

            $webEnvFile = EnvCfgManage::getEnvCfgFile($webCode);

            $envCfg = EnvCfgManage::readEnvCfg($webEnvFile);
            if(!$envCfg){
                echo $webCode.'=>配置为空',PHP_EOL;
                continue;
            }

            $androidApiKey = isset($envCfg['ANDROID_APIKEY']) ? $envCfg['ANDROID_APIKEY'] : '';
            $androidSecretKey = isset($envCfg['ANDROID_SECRETKEY']) ? $envCfg['ANDROID_SECRETKEY'] : '';
            $iosApiKey = isset($envCfg['IOS_APIKEY']) ? $envCfg['IOS_APIKEY'] : '';
            $iosSecretkey = isset($envCfg['IOS_SECRETKEY']) ? $envCfg['IOS_SECRETKEY'] : '';
            $iosDeploy = isset($envCfg['PUSH_IOS_DEPLOY']) ? $envCfg['PUSH_IOS_DEPLOY'] : '';


            $data['name'] = $v['webName'];
            $data['webCode'] = $k;
            $data['domain'] = $domain;
            $data['androidApiKey'] = $androidApiKey;
            $data['androidSecretKey'] = $androidSecretKey;
            $data['iosApiKey'] = $iosApiKey;
            $data['iosSecretKey'] = $iosSecretkey;
            $data['iosDeploy'] = $iosDeploy;
            $data['mtm'] = time();
            $model = createService('School.SchoolServiceModel')->switchCenterDB();
            $model = processSqlObj(array('sqlObj'=>$model,'siteSelect'=>'all'));
            $res = $model-> where("webCode = '$k'")->getField("id");
            if($res>0){
                $result = $model->where("id = ".$res)->save($data);
            }else{
                $data['opUid'] = 1;
                $data['ctm'] = time();
                $result = $model->add($data);
            }

            if($result > 0){
                echo 'webCode{' . $k ."}成功".PHP_EOL;
            }else{
                echo 'webCode{' . $k ."}失败".PHP_EOL;
            }
        }
    }

    /**
     * 批量设置组织结构
     * php55 index_cli.php --c=Web  --a=batAction     --cmd=checkAndProcessCategory      --webcode=qzw,qzw2,qzw3
     */
    public function checkAndProcessCategory(){
        $code = isset($_GET['webcode']) ? $_GET['webcode'] : "";

        if(!$code){
            $school_list = M('tmpschool')->getField('webCode',true);
            $list = $school_list;
        }else{
            $code = explode(',',$code);
//            foreach($code as $v){
//                   $count =  M('tmpschool')->setWebCode($v)->count();
//                    if($count<0){
//                                exit('webCode：'.$v.'不存在');
//                    }
//            }
            $list = $code;
        }
        //先检测category表是否已经有，没有情况下才会导入
        foreach($list as $vo){
            $count = createService('Taxonomy.CategoryModel')->count();

            if($count > 0 ){
                echo $vo.'已存在数据'.PHP_EOL;
                continue;
            }

            $this->addCategory($vo);
            echo $vo.'处理完成'.PHP_EOL;
        }
        echo '全部处理完成'.PHP_EOL;
    }

    private function addCategory($code, $parentId = 0, $lastId = 0) {
        $cates = createService('Taxonomy.CategoryModel')->where(array('parentId' => $parentId, 'webCode'=>'imufe'))->select();

        foreach ($cates as $v) {
            $parentId = $v['id'];
            unset($v['id']);
            $v['webCode'] = $code;
            $v['parentId'] = $lastId;
            $v['ctm'] = time();
            $lastParentId = createService('Taxonomy.CategoryModel')->add($v);
            if(createService('Taxonomy.CategoryModel')->where(array('parentId'=>$parentId, 'webCode'=>'imufe'))->count()) {
                $this->addCategory($code, $parentId, $lastParentId);
            }
        }
        return true;
    }




    /**
     * 批量开启客户端扫码
     * php55 index_cli.php --c=Web  --a=batAction     --cmd=checkAndProcessMobileScanStatus       --webcode=qzw,qzw2,qzw3
     */
    private function checkAndProcessMobileScanStatus($code){
        $code = isset($_GET['webcode']) ? $_GET['webcode'] : "";

        $mobileArr = $this->getSettingByName($code, 'mobile');
        $params['enabled'] = 1;
        if(!empty($mobileArr)){
            $this->setMobileValue($mobileArr, $params);
        }else{
            echo "没有数据".PHP_EOL;
        }
    }


    private function setMobileValue($mobileArr=array(), $params=array()){
        foreach($mobileArr as $key => $val){
            $id = $val['id'];
            $webCode = $val['webCode'];
            $mobileVal[$id] = unserialize($val['value']);
            $mobileVal[$id] = array_merge($mobileVal[$id] , $params);

//            echo '###########'. $webCode,PHP_EOL;

            $serviceModel = createService('System.SettingServiceModel');
            $info = $serviceModel->editSetting($id, $mobileVal[$id]);

            echo $val['webCode']."完成".PHP_EOL;

//            if($info){
//                echo $val['webCode']."成功".PHP_EOL;
//            }else{
//                echo  "{".$val['webCode']."} fail, error: ".$error.PHP_EOL;
//            }
        }
    }

    /**
     * 批量更新网站名称 副标题 seo信息
     * php55 index_cli.php --c=Web  --a=batAction     --cmd=checkAndProcessSeo     --webcode=qzw,qzw2,qzw3
     */
    private function checkAndProcessSeo(){
        $code = isset($_GET['webcode']) ? $_GET['webcode'] : "";
        $webCodeArr = C('ALL_WEB_CODE') ;
        $obj = M("tmpschool");
        $obj = processSqlObj(array('sqlObj'=>$obj,'siteSelect'=>'all'));
        $fields = array("id ,name, webCode");
        #查出所有学校tmpschool表数据
        $schoolArr = array();
        if(empty($code)){
            $schoolArr = $obj->where("webCode!=''")->field($fields)->select();
        }else{
            $codeArr = explode(',' , trim($code,','));
            foreach($codeArr as $k => $v){
                if(!key_exists($v, $webCodeArr)){
                    echo 'code{'. $v . '}不存在！'.PHP_EOL;
                    unset($codeArr[$k]);
                }
            }
            if(empty($codeArr)) die("获取的webCode都不存在");
            $where['webCode'] = array("in" , $codeArr);
            #先从tmpschool查 by qzw
            $schoolArr = $obj->where($where)->field($fields)->select();
            #查不到，从All_WEB_CODE查
            if(!$schoolArr){
                $allWebCode = C('ALL_WEB_CODE');
                foreach($codeArr as $webCode){
                    if(isset($allWebCode[$webCode])){
                        $webCodeInfo = $allWebCode[$webCode];
                        $schoolArr[] = array('name'=>$webCodeInfo['webName'], 'webCode'=>$webCode);
                    }
                }
            }
        }
        if(empty($schoolArr)) die ("未找到可用的院校名称及SEO关键字，请检查表中是否有数据");

        #获取指定或所有的setting表里site
        $siteArr = $this->getSettingByName($code, 'site');
        foreach($siteArr as $key => $val){
            $siteVal[$val['webCode']] = unserialize($val['value']);
        }

        foreach ($schoolArr as $k => $v ){
            $name    = $v['name'];
            $webCode = $v['webCode'];
            if(!key_exists($webCode, $webCodeArr)){
                echo 'webCode{'.$webCode.'}在setting表中不存在！'.PHP_EOL;
                continue;
            }
            #判断院校的webCode是否为空
            if(!empty($webCode)){
                $keywords = $name."云app， ".$name."高校云，高校云学习，云学习，优质课程，只能回答，趣味学习，视频教程、交流互动、免费、名师、实用";
                $description = "瑞德口袋云";
                $siteVal[$webCode]['name']            = $name;              #网站名称
                $siteVal[$webCode]['slogan']          = $name;              #网站副标题
                $siteVal[$webCode]['seo_keywords']    = $keywords;          #seo关键字
                $siteVal[$webCode]['seo_description'] = $description;       #seo描述信息
                $siteVal[$webCode]['copyright'] = '';                       #课程内容版权方
                $siteVal[$webCode]['icp'] = '';                             #icp备案
                $siteVal[$webCode]['logoType'] = 1;                         #初始化logo类型为校徽

                $serviceModel = createService('System.SettingServiceModel');
                $info = $serviceModel->set('site', $siteVal[$webCode], $siteSelect=$webCode);

                if(!$info) $error = $serviceModel->getDbError();
                if(empty($error) || $result){
                    echo $webCode."成功".PHP_EOL;
                }else{
                    echo "{".$webCode."} fail, error: ".$error.PHP_EOL;
                }
            }else{
                echo "ID为".$v['id']."的数据webCode为空".PHP_EOL;
            }
        }
    }

    private function getSettingByName($code='', $field='site'){
        #获取指定或所有的setting表里site
        $obj = createService('System.SettingModel');
        $codeArr = array();
        if(empty($code)){
            $site = $obj ->getAllSettingByCodes($codeArr, $field, 'all');
        }else{
            $codeArr = explode(',' , trim($code,','));
            
            if(empty($codeArr)) die("获取的webCode都不存在");
            $site = $obj -> getAllSettingByCodes($codeArr, $field,  'all');
        }

        return $site;
    }

    /**
     * 批量更改logo信息
     * php55 index_cli.php --c=Web  --a=batAction     --cmd=checkAndProcessLogo    --webcode=qzw,qzw2,qzw3
     */
    private function checkAndProcessLogo(){
        $code = isset($_GET['webcode']) ? $_GET['webcode'] : "";
        $site = array();
        $webCodeArr = C('ALL_WEB_CODE');
        #获取指定或所有的setting表里site

        $site = $this->getSettingByName($code, 'site');
        $siteCodeArr = array();
        #把所有的site里的value转化为数组
        if(!empty($site)){
            foreach($site as $key => $val){
                $siteVal[$val['id']] = unserialize($val['value']);
                $siteArr[$val['id']]  = $val;
                $siteCodeArr[$val['id']] = $val['webCode'];
            }
        }
        $mod = M("setting");
        $logoPath = getParameter("redcloud.upload.public_directory") . "/xitong/";
        #对每一个logo进行判断是否存在
        if(!empty($siteVal)){
            foreach($siteVal as $k => $v){
                $logo = $v['logo'];
                $logoFirst = trim($logo,'/');
                $logoFirst = substr($logoFirst,0,6);
                if($logoFirst!='Public'){
                    $logo = "Public/".trim($v['logo'],"/");
                }
                if(!$v['logo'] || !file_exists($logo)){
                    echo str_pad($siteCodeArr[$k], 15).':  '. $v['logo'].'不存在'.PHP_EOL;
                    $siteVal[$k]['logo'] = $logoPath.'logo_'.$siteArr[$k]['webCode'].'.jpg';
                    $data['value'] = serialize($siteVal[$k]);
                    $result = $mod->where("id =".$k)->setField($data);
//                    if(!$result) echo M()->getDbError() , PHP_EOL;
                    if(!$result) $error =  M()->getDbError();
                    if(empty($error) || $result){
                        echo $siteArr[$k]['webCode']."成功".PHP_EOL;
                    }else{
                        echo "{".$siteArr[$k]['webCode']."} fail, error: ".$error.PHP_EOL;
                    }
                }else{
                    echo str_pad($siteCodeArr[$k], 15).':  '. $v['logo']."存在".PHP_EOL;
                }
            }
        }
    }


    private function opCommField($op){
        $table = isset($_GET['table']) ? $_GET['table'] : '';
        $webcode = isset($_GET['webcode']) ? $_GET['webcode'] : '';
        $priv = isset($_GET['priv']) ? $_GET['priv'] : '-';
        if($table=='all') $table = '';

        $allTables = $this->getAllTable();
        if($table && !in_array($table, $allTables)) die('no exists table: '.$table.PHP_EOL);

        if($allTables){
            foreach($allTables as $tbl){
                if($table && $table!=$tbl) continue;
                $fields = M($tbl)->getDbFields();
                foreach($this->checkField as $ckField=>$alterArr){
                    echo PHP_EOL;
                    if($op == 'check'){
                        $str = in_array($ckField, $fields) ? 'exists' : 'no exists';
                        $str .= ' ' . $ckField;
                    }else if($op == 'add'){
                        if($tbl && in_array($tbl, $this->excludeTable)) $str = '跳过';
                        else {
                            if(!in_array($ckField, $fields)) {
                                $r = M()->execute(sprintf($alterArr['add'], $tbl));
                                if(!$r) $str = 'error:' . M()->getDbError() . ' sql:'. M()->getLastSql();
                                else $str = 'add OK!';
                            }else {
                                $str = 'add ok';
                            }
                        }
                    }else if($op == 'del'){
                        if(in_array($ckField, $fields)) {
                            $r = M()->execute(sprintf($alterArr['del'], $tbl));
                            if(!$r) $str = 'error:' . M()->getDbError() . ' sql:'. M()->getLastSql();
                            else $str = 'del OK!';
                        }else {
                            $str = 'del ok';
                        }
                    }else if($op == 'set') {
                        if($ckField == 'owebPriv') continue;
                        if(!isset($alterArr['set'])) continue;
                        $r = M()->execute(sprintf($alterArr['set'], $tbl, $webcode));
                        if(!$r) $str = 'error:' . M()->getDbError() . ' sql:'. M()->getLastSql();
                        else $str = 'set webcode OK!';
                    }else if($op == 'setPriv') {
                        if($ckField == 'webCode') continue;
                        if(!isset($alterArr['set'])) continue;
                        $r = M()->execute(sprintf($alterArr['set'], $tbl, $priv, $webcode));
                        if(!$r) $str = 'error:' . M()->getDbError() . ' sql:'. M()->getLastSql();
                        else $str = 'setPriv webcode OK!';
                    }else{
                        echo "error op".PHP_EOL;
                        $this->showOp();
                        exit;
                    }
                    echo str_pad($tbl, 25, ' ', STR_PAD_RIGHT) .":" . $str;
                }

            }
        }
        echo PHP_EOL;
    }

    private function opField(){
        $op = isset($_GET['cmd']) ? $_GET['cmd'] : '';
        $this->opCommField($op);
    }

    private function getAllTable(){
        $rs = M()->query("SELECT TABLE_SCHEMA, TABLE_NAME as `table` FROM `information_schema`.`TABLES` where TABLE_SCHEMA = '".C('DB_NAME')."'");
        return \Common\Lib\ArrayToolkit::column($rs, 'table');
    }


    private function initWebCode(){
        $srcArr = M("tmpschool")->field("replace(trim(src),'http://','') as srcWebSrc,id")->where("webCode = ''")->select();
        $webCodeArr = array();
        foreach ($srcArr as $key =>$src) {
            $code = explode(".", $src['srcWebSrc']);
            $webCodeArr[$src['id']] = $code[1];
        }

        static $codeArr = array('center','bvtc','cncnc','fdsjy','gmtj','gzmodern','tstc','whcvc','xacxxy','zjicm','hbsi','znufe','ycxy','syau','imufe','oracle');
        $code = M("tmpschool")->field("webCode")->where("webCode != ''")->select();
        foreach($code as $val){
            $codeArr[] =$val['webCode'];
        }

        foreach ($webCodeArr as $id => $webCode) {
            if(in_array($webCode, $codeArr)){
                echo "{$webCode}已经存在，去表查看吧".PHP_EOL;
            }else{
                $r = M("tmpschool")->where("id = {$id}")->save(array('webCode'=>$webCode));
                if(!$r) echo "{$id}的{$webCode}修改失败".PHP_EOL;
                else array_push($codeArr, $webCode);
            }
        }

    }

    private function webCodeConf(){
        $allWebCode = C('ALL_WEB_CODE');
        $allWebCode = array_keys($allWebCode);

        $codeArr = M("tmpschool")->field("webCode,name")->where("webCode != ''")->select();
        if(!$codeArr) echo M()->getDbError(), PHP_EOL;
        foreach ($codeArr as $key => $value) {
            if(!in_array($value['webCode'], $allWebCode)){
                $str = "'{$value['webCode']}'";
                $str = str_pad($str, 13," ");
                echo "{$str}=> array('status'=>1, 'webName'=>'{$value['name']}'),".PHP_EOL;
            }
        }
    }

    /**
     * 创建logo
     *  php55 index_cli.php --c=Web  --a=tmpAction --cmd=mklogo
     * @author 钱志伟 2015-08-04
     */
    private function mklogo(){
        $allTmpSchool = $this->getTmpSchool();

        $dt = date('ymd');
        $dstLogoDir = "/data_1/html/tmp/dstLogo_{$dt}/";

        if(!is_dir($dstLogoDir)) mkdir($dstLogoDir, 0755, true);
        if(!file_exists($dstLogoDir)) die("创建失败".PHP_EOL);

        if($allTmpSchool){
            foreach($allTmpSchool as $webCode=>$school){
                $schoolName = $school['name'];
                $filePattern = $this->srcLogoDir . $schoolName .'\.*';
                $fileArr = glob($filePattern);
                echo str_pad($webCode, 10).':'.$schoolName.  str_repeat(' ', 40-mb_strlen($schoolName)).': ';
                if(!$fileArr){
                    echo '无原始logo';
                }else{
                    $filename = 'logo_'.$webCode.'.jpg';
                    $dstLogo = $dstLogoDir . $filename;
                    if(!file_exists($dstLogo)){
                        $cmd = sprintf('%s -resize 2000x50 %s %s', C('CONVERT_CMD'), $fileArr[0], $dstLogo);
                        exec($cmd);
                    }
                    echo file_exists($dstLogo) ? '创建成功 '.$filename : '创建失败';
                }
                echo PHP_EOL;
            }

            echo "位置: ".$dstLogoDir.PHP_EOL;
        }
    }

    /**
     * 初始化账号
     * @author fubaosheng 2015-09-07
     * php55 index_cli.php --c=Web --a=batAction --cmd=initAccount --webcode=qzw,qzw2,qzw3
     */
    private function initAccount(){
        $webcode = isset($_GET['webcode']) ? $_GET['webcode'] : '';
        $webcodeArr = $webcode ? array_unique(explode(",",$webcode)) : array();
        $allWebcode = C("ALL_WEB_CODE");
        $allWebcode = array_keys($allWebcode);
        $initWebcode = array();
        if(!empty($webcodeArr)){
            foreach ($webcodeArr as $key => $value) {
                if(in_array($value,$allWebcode)) $initWebcode[$key] = $value;
                else  echo "webcode：{$value}不在配置文件".PHP_EOL;
            }
        }else{
            $initWebcode = $allWebcode;
        }
        $initWebcode = array_values(array_unique($initWebcode));
        if(empty($initWebcode)) die("没有要初始化的webCode".PHP_EOL);

        //开始初始化账号
        foreach ($initWebcode as $code) {
            $uid = createService("User.UserServiceModel")->getTestAccountId($code);
            if($uid){
                echo "{$code}账号已经存在".PHP_EOL;
            }else{
                $uid = createService("User.UserServiceModel")->autoRegister($code);
                if($uid) echo "{$code}账号初始化成功，账号：redcloud_test_{$code}@redcloud.com".PHP_EOL;
                else echo "{$code}账号初始化失败".PHP_EOL;
            }
        }
    }

    ############### 初始化 start
    /**
     * 获取某一个学校初始账号，没有创建
     * @author fubaosheng 2015-09-25
     */
    private function getInitAccountByWebcode($webCode){
        $uid = createService("User.UserServiceModel")->getTestAccountId($webCode);
        if(empty($uid)){
            $uid = createService("User.UserServiceModel")->autoRegister($webCode);
        }
        return $uid;
    }

    /**
     * 获取分类根据id
     * @author fubaosheng 2015-09-25
     */
    private function getCategoryById($id,$webCode){
        $category = createService("Taxonomy.CategoryModel")->getCategory($id);
        if(!empty($category)){
            return $this->initCategory($category, $webCode);
        }
        return array();
    }

    /**
     * 初始化学校分类，没有创建
     * @author fubaosheng 2015-09-25
     */
    private function initCategory($category,$webCode){
        $where = array();
        $where['webCode'] = $webCode;
        $where['code'] = $category['code'];
        $where['name'] = $category['name'];
        $where['ctm'] = $category['ctm'];
        $categoryArr =  M("category")->where($where)->find();
        if(empty($categoryArr)){
            $categoryArr = createService("Taxonomy.CategoryModel")->addCategory($category);
        }
        return $categoryArr;
    }

    /**
     * 获取课程根据id
     * @author fubaosheng 2015-09-25
     */
    private function getCourseById($id,$webCode){
        $course = createService("Course.CourseModel")->getCourse($id);
        if(!empty($course)){
            return $this->initCourse($course, $webCode);
        }
        return array();
    }

     /**
     * 初始化课程，没有创建
     * @author fubaosheng 2015-09-25
     */
    private function initCourse($course,$webCode){
        $where = array();
        $where['webCode'] = $webCode;
        $where['status'] = $course['status'];
        $where['title'] = $course['title'];
        $where['createdTime'] = $course['createdTime'];
        $courseArr =  M("course")->where($where)->find();
        if(empty($courseArr)){
            $courseArr = createService("Course.CourseModel")->addCourse($course);
        }
        return $courseArr;
    }

    /**
     * 初始化课程的分类，没有创建
     * @author fubaosheng 2015-09-25
     */
    private function initCourseCategory($courseCategory,$webCode){
        $where = array();
        $where['webCode'] = $webCode;
        $where['microTm'] = $courseCategory['microTm'];
        $where['ctm'] = $courseCategory['ctm'];
        $courseCategoryArr =  M("course_category_rel")->where($where)->find();
        if(empty($courseCategoryArr)){
            $courseCategoryId = createService("Course.CourseCategoryModel")->store($courseCategory);
            $courseCategoryArr = M("course_category_rel")->where(array('id'=>$courseCategoryId))->find();
        }
        return $courseCategoryArr;
    }

    /**
     * 获取课程章节根据id
     * @author fubaosheng 2015-09-25
     */
    private function getCourseChapterById($id,$webCode){
        $courseChapter = createService("Course.CourseChapterModel")->getChapter($id);
        if(!empty($courseChapter)){
            return $this->initCourseChapter($courseChapter, $webCode);
        }
        return array();
    }

    /**
     * 初始化课程章节，没有创建
     * @author fubaosheng 2015-09-25
     */
    private function initCourseChapter($courseChapter,$webCode){
        $where = array();
        $where['webCode'] = $webCode;
        $where['type'] = $courseChapter['type'];
        $where['title'] = $courseChapter['title'];
        $where['createdTime'] = $courseChapter['createdTime'];
        $courseChapterArr =  M("course_chapter")->where($where)->find();
        if(empty($courseChapterArr)){
            $courseChapterArr = createService("Course.CourseChapterModel")->addChapter($courseChapter);
        }
        return $courseChapterArr;
    }

    /**
     * 初始化课时，没有创建
     * @author fubaosheng 2015-09-25
     */
    private function initCourseLesson($courseLesson,$webCode){
        $where = array();
        $where['webCode'] = $webCode;
        $where['status'] = $courseLesson['status'];
        $where['type'] = $courseLesson['type'];
        $where['title'] = $courseLesson['title'];
        $where['createdTime'] = $courseLesson['createdTime'];
        $courseLessonArr =  M("course_lesson")->where($where)->find();
        if(empty($courseLessonArr)){
            $courseLessonArr = createService("Course.LessonModel")->addLesson($courseLesson);
        }
        return $courseLessonArr;
    }

     /**
     * 初始化课程学员，没有创建
     * @author fubaosheng 2015-09-25
     */
    private function initCourseMember($courseMember,$webCode){
        $where = array();
        $where['webCode'] = $webCode;
        $where['joinedType'] = $courseMember['joinedType'];
        $where['role'] = $courseMember['role'];
        $where['createdTime'] = $courseMember['createdTime'];
        $courseMemberArr =  M("course_member")->where($where)->find();
        if(empty($courseMemberArr)){
            $courseMemberArr = createService("Course.CourseMemberModel")->addMember($courseMember);
        }
        return $courseMemberArr;
    }

    /**
    * 初始化课程
    * @author fubaosheng 2015-09-25
    * php55 index_cli.php --c=Web --a=batAction --cmd=dataWebCode
    */
    private function dataWebCode(){
        $baseWebCode = "datawebcode";
        echo "请输入样本webCode(默认datawebcode)：{$baseWebCode}",PHP_EOL;
//        do{
//            fwrite(STDOUT, '请输入样本webCode(默认datawebcode)：');
//            $baseWebCode = trim(fgets(STDIN)) ? : "datawebcode";
//        }while(!$baseWebCode);
        $allWebcode = C("ALL_WEB_CODE");
        $allWebcode = array_keys($allWebcode);
        if(!empty($allWebcode)){
            if(!in_array($baseWebCode, $allWebcode)){
                die("样本webCode{$baseWebCode}不在配置文件中");
            }

            fwrite(STDOUT, '请输入目标webCode(不输代表所有)：');
            $targetWebcode =  trim(fgets(STDIN)) ? : "";
            if(!empty($targetWebcode)){
                if($baseWebCode == $targetWebcode){
                    die("目标webCode{$targetWebcode}和样本webCode{$baseWebCode}相同");
                }
                if(!in_array($targetWebcode, $allWebcode)){
                    die("目标webCode{$targetWebcode}不在配置文件中");
                }
                $allWebcode = array($targetWebcode);
            }

            $baseCategory = M("category")->where(array('webCode'=>$baseWebCode))->select();
            $baseCourse = M("course")->where(array('webCode'=>$baseWebCode))->select();
            $baseCourseMember = M("course_member")->where(array('webCode'=>$baseWebCode,'role'=>'teacher'))->select();
            $baseCourseCategory = M("course_category_rel")->where(array('webCode'=>$baseWebCode))->select();
            $baseCourseChapter = M("course_chapter")->where(array('webCode'=>$baseWebCode))->select();
            $baseCourseLesson = M("course_lesson")->where(array('webCode'=>$baseWebCode,'type'=>'video'))->select();

            foreach ($allWebcode as $webCode) {
                if($baseWebCode != $webCode ){
                    $uid = $this->getInitAccountByWebcode($webCode);
                    echo "{$webCode}学校开始，初始化账号id：{$uid}\r\n";
                    #复制category
                    if(!empty($baseCategory)){
                        echo "开始复制category\r\n";
                        $webCodeCategory = array('isSupper'=>1,'createUid'=>$uid,'webCode'=>$webCode,'owebPriv'=>'-','isTest'=>1);
                        foreach($baseCategory as &$category){
                            $category = array_merge($category,$webCodeCategory);
                            unset($category['id']);
                            $categoryArr = $this->initCategory($category,$webCode);
                            if($categoryArr['parentId'] && ($categoryArr['parentId'] == $category['parentId'])){
                                $parentCategory = $this->getCategoryById($categoryArr['parentId'], $webCode);
                                if($parentCategory){
                                    createService("Taxonomy.CategoryModel")->updateCategory($categoryArr['id'],array('parentId'=>$parentCategory['id']));
                                }
                            }
                        }
                    }
                    #复制course
                    if(!empty($baseCourse)){
                        echo "开始复制course\r\n";
                        $webCodeCourse = array('categoryId'=>0,'teacherIds'=>"|{$uid}|",'studentNum'=>0,'hitNum'=>0,'userId'=>$uid,'webCode'=>$webCode,'owebPriv'=>'-','relCommCourseId'=>-1,'relCommCourseTm'=>0,'isTest'=>1,'recommended'=>1,'recommendedSeq'=>1,'recommendedTime'=>time());
                        foreach($baseCourse as &$course){
                            $course = array_merge($course,$webCodeCourse);
                            unset($course['id']);
                            $courseArr = $this->initCourse($course,$webCode);
                        }
                    }
                    #复制course_member
                    if(!empty($baseCourseMember)){
                        echo "开始复制course_member\r\n";
                        $webCodeCourseMember = array('classroomId'=>0,'userId'=>$uid,'learnedNum'=>0,'noteNum'=>0,'noteLastUpdateTime'=>0,'isLearned'=>0,'webCode'=>$webCode,'owebPriv'=>'-','isTest'=>1);
                        foreach ($baseCourseMember as &$courseMember) {
                            $courseMember = array_merge($courseMember,$webCodeCourseMember);
                            unset($courseMember['id']);
                            $courseMemberArr = $this->initCourseMember($courseMember,$webCode);
                            if($courseMemberArr['courseId'] == $courseMember['courseId']){
                                $courseDataArray = $this->getCourseById($courseMemberArr['courseId'], $webCode);
                                if($courseDataArray){
                                    createService("Course.CourseMemberModel")->updateMember($courseMemberArr['id'],array('courseId'=>$courseDataArray['id']));
                                }
                            }
                        }
                    }
                    #复制course_category_rel
                    if(!empty($baseCourseCategory)){
                        echo "开始复制course_category_rel\r\n";
                        $webCodeCourseCategory = array('uid'=>$uid,'webCode'=>$webCode,'owebPriv'=>'-','isTest'=>1);
                        foreach ($baseCourseCategory as &$courseCategory) {
                           $courseCategory = array_merge($courseCategory,$webCodeCourseCategory);
                           unset($courseCategory['id']);
                           $courseCategoryArr = $this->initCourseCategory($courseCategory,$webCode);
                           if($courseCategoryArr['categoryId'] == $courseCategory['categoryId']){
                              $courseCategoryData = $this->getCategoryById($courseCategoryArr['categoryId'], $webCode);
                              if($courseCategoryData){
                                  M('course_category_rel')->where(array('id'=>$courseCategoryArr['id']))->save(array('categoryId'=>$courseCategoryData['id']));
                              }
                           }
                           if($courseCategoryArr['courseId'] == $courseCategory['courseId']){
                              $courseData = $this->getCourseById($courseCategoryArr['courseId'], $webCode);
                              if($courseData){
                                  M('course_category_rel')->where(array('id'=>$courseCategoryArr['id']))->save(array('courseId'=>$courseData['id']));
                              }
                           }
                        }
                    }
                    #复制course_chapter
                    if(!empty($baseCourseChapter)){
                        echo "开始复制course_chapter\r\n";
                        $webCodeCourseChapter = array('webCode'=>$webCode,'owebPriv'=>'-','isTest'=>1);
                        foreach ($baseCourseChapter as &$courseChapter) {
                            $courseChapter = array_merge($courseChapter,$webCodeCourseChapter);
                            unset($courseChapter['id']);
                            $courseChapterArr = $this->initCourseChapter($courseChapter, $webCode);
                            if($courseChapterArr['parentId'] && ($courseChapterArr['parentId'] == $courseChapter['parentId'])){
                                $courseChapterData = $this->getCourseChapterById($courseChapterArr['parentId'], $webCode);
                                if($courseChapterData){
                                    createService("Course.CourseChapterModel")->updateChapter($courseChapterArr['id'],array('parentId'=>$courseChapterData['id']));
                                }
                            }
                            if($courseChapterArr['courseId'] == $courseChapter['courseId']){
                                $courseDataArr = $this->getCourseById($courseChapterArr['courseId'], $webCode);
                                if($courseDataArr){
                                    createService("Course.CourseChapterModel")->updateChapter($courseChapterArr['id'],array('courseId'=>$courseDataArr['id']));
                                }
                            }
                        }
                    }
                    #复制course_lesson
                    if(!empty($baseCourseLesson)){
                        echo "开始复制course_lesson\r\n";
                        $webCodeCourseLesson = array('materialNum'=>0,'quizNum'=>0,'learnedNum'=>0,'viewedNum'=>0,'userId'=>$uid,'webCode'=>$webCode,'owebPriv'=>'-','isTest'=>1);
                        foreach ($baseCourseLesson as  &$courseLesson) {
                            $courseLesson = array_merge($courseLesson,$webCodeCourseLesson);
                            unset($courseLesson['id']);
                            $courseLessonArr = $this->initCourseLesson($courseLesson, $webCode);
                            if($courseLessonArr['chapterId'] && ($courseLessonArr['chapterId'] == $courseLesson['chapterId']) ){
                                $courseLessonData = $this->getCourseChapterById($courseLessonArr['chapterId'], $webCode);
                                if($courseLessonData){
                                    createService("Course.LessonModel")->updateLesson($courseLessonArr['id'],array('chapterId'=>$courseLessonData['id']));
                                }
                            }
                            if($courseLessonArr['courseId'] == $courseLesson['courseId']){
                                $courseArray =  $this->getCourseById($courseLessonArr['courseId'], $webCode);
                                if($courseArray){
                                    createService("Course.LessonModel")->updateLesson($courseLessonArr['id'],array('courseId'=>$courseArray['id']));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    ############### 初始化 end

    /**
     * 修改用户webCode及owebPriv  5314824a925343@qq.com,qzw8811302@126.com
     * author 谈海涛 2015-09-07
     * php55 index_cli.php --c=Web --a=batAction --cmd=setUserWebcodePriv
     */
    private function setUserWebcodePriv(){
        fwrite(STDOUT, "请输入帐号类型(0=>邮箱 1=>手机 2=>uid)：");
        $type = trim(fgets(STDIN));
        if(!in_array($type,array(0,1,2))) die("账号类型输入不合法");
        $obj = createService("User.UserServiceModel");
        if($type == 0){
          fwrite(STDOUT, "请根据上面类型输入帐号（多个逗号分隔：");
          $emails = explode(',',trim(fgets(STDIN)));
          $estr = '';
          foreach ($emails as $k => $v) {
              if(!isValidEmail($v)){
                  $k++;
                  $estr .="第{$k}个邮箱输入错误:$v  \r\n";
              }
          }
          if(!empty($estr)) die($estr);
          $users = $obj->getUserByEmails($emails);
          if(empty($users)) die('无数据，安全退出');
          $str = "当前属于：\r\n";
          foreach ($users as $v){
             $str .= "[uid={$v['id']}]  webcode={$v['webCode']},  owebPriv={$v['owebPriv']} \r\n";
          }
        fwrite(STDOUT, $str."请输入新的webcode：");
        $webCode = trim(fgets(STDIN));
        foreach ($users as $v) {
          $obj->updateWebCodePriv($v['id'],array('webCode' =>$webCode ));
        }
        fwrite(STDOUT, "请输入新的owebPriv：");
        $owebPriv = trim(fgets(STDIN));
        foreach ($users as $v) {
          $obj->updateWebCodePriv($v['id'],array('owebPriv' =>$owebPriv ));
        }
        $users = $obj->getUserByEmails($emails);
        }

        if($type == 1){
          fwrite(STDOUT, "请根据上面类型输入帐号（多个逗号分隔：");
          $mobiles = explode(',',trim(fgets(STDIN)));
          $estr = '';
          foreach ($mobiles as $k => $v) {
              if(!isValidMobile($v)){
                  $k++;
                  $estr .="第{$k}个手机输入错误:$v  \r\n";
              }
          }
          if(!empty($estr)) die($estr);
          $users = $obj->getUserByVerifiedMobiles($mobiles);
          if(empty($users)) die('无数据，安全退出');
          $str = "当前属于：\r\n";
          foreach ($users as $v){
             $str .= "[uid={$v['id']}]  webcode={$v['webCode']},  owebPriv={$v['owebPriv']} \r\n";
          }
        fwrite(STDOUT, $str."请输入新的webcode：");
        $webCode = trim(fgets(STDIN));
        foreach ($users as $v) {
          $obj->updateWebCodePriv($v['id'],array('webCode' =>$webCode ));
        }
        fwrite(STDOUT, "请输入新的owebPriv：");
        $owebPriv = trim(fgets(STDIN));
        foreach ($users as $v) {
          $obj->updateWebCodePriv($v['id'],array('owebPriv' =>$owebPriv ));
        }
        $users = $obj->getUserByVerifiedMobiles($mobiles);
        }

        if($type == 2){
          fwrite(STDOUT, "请根据上面类型输入帐号（多个逗号分隔：");
          $uids = explode(',',trim(fgets(STDIN)));
          $users = $obj->getUsers($uids);
          if(empty($users)) die('无数据，安全退出');
          $str = "当前属于：\r\n";
          foreach ($users as $v){
             $str .= "[uid={$v['id']}]  webcode={$v['webCode']},  owebPriv={$v['owebPriv']} \r\n";
          }
        fwrite(STDOUT, $str."请输入新的webcode：");
        $webCode = trim(fgets(STDIN));
        foreach ($users as $v) {
          $obj->updateWebCodePriv($v['id'],array('webCode' =>$webCode ));
        }
        fwrite(STDOUT, "请输入新的owebPriv：");
        $owebPriv = trim(fgets(STDIN));
        foreach ($users as $v) {
          $obj->updateWebCodePriv($v['id'],array('owebPriv' =>$owebPriv ));
        }
        $users = $obj->getUsers($uids);

        }
        $strs = "修改成功 \r\n";
        foreach ($users as $v){
            $strs .= "[uid={$v['id']}]  webcode={$v['webCode']},  owebPriv={$v['owebPriv']} \r\n";
          }
       fwrite(STDOUT, $strs);die();
    }


    private function showOp(){
        $scriptName = 'php55 index_cli.php --c=Web --a=runAction --op=';
        $batScriptName = 'php55 index_cli.php --c=Web --a=batAction';
        $tmpScriptName = 'php55 index_cli.php --c=Web --a=tmpAction';
        echo "web db manager tool  v0.1".PHP_EOL.PHP_EOL;
        echo "usage: {$scriptName}opField --cmd=check --table=[all|tablename]".PHP_EOL;
        echo "usage: {$scriptName}opField --cmd=add   --table=[all|tablename]".PHP_EOL;
        echo "usage: {$scriptName}opField --cmd=del   --table=[all|tablename]".PHP_EOL;
        echo "usage: {$scriptName}opField --cmd=set   --table=[all|tablename] --webcode=qzw".PHP_EOL;
        echo "usage: {$scriptName}opField --cmd=setPriv   --table=[all|tablename] --webcode=qzw --priv=wr".PHP_EOL;
        echo "usage: {$batScriptName} --cmd=initWebCode".PHP_EOL;
        echo "usage: {$scriptName}webCodeConf".PHP_EOL;
        echo "usage: {$batScriptName}     --cmd=checkAndProcessLogo  --webcode=qzw,qzw2,qzw3".PHP_EOL;
        echo "usage: {$batScriptName}     --cmd=checkAndProcessSeo   --webcode=qzw,qzw2,qzw3".PHP_EOL;
        echo "usage: {$batScriptName}     --cmd=checkAndProcessMobileBanner   --webcode=qzw,qzw2,qzw3".PHP_EOL;
        echo "usage: {$batScriptName}     --cmd=checkAndProcessMobileScanStatus   --webcode=qzw,qzw2,qzw3".PHP_EOL;
        echo "usage: {$batScriptName}     --cmd=checkAndProcessCategory   --webcode=qzw,qzw2,qzw3".PHP_EOL;
        echo "usage: {$batScriptName}     --cmd=checkAndProcessSchool   --webcode=qzw,qzw2,qzw3".PHP_EOL;
        echo "usage: {$batScriptName}     --cmd=initAccount   --webcode=qzw,qzw2,qzw3".PHP_EOL;
        echo "usage: {$batScriptName}     --cmd=dataWebCode".PHP_EOL;
        echo "usage: {$tmpScriptName}     --cmd=mklogo".PHP_EOL;
        echo "usage: {$tmpScriptName}     --cmd=mkWebcodeCfgFile".PHP_EOL;
        echo "usage: {$scriptName}showOp".PHP_EOL;
        exit;
    }


	private function getSetting() {
		return createService('System.SettingModel');
	}

        protected function getSettingService()
        {
            return createService('System.SettingServiceModel');
        }

}
