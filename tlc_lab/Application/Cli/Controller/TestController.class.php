<?php
namespace Cli\Controller;

use \Common\Lib\EnvCfgManage;
use Common\Services\SoapClientService;
class TestController {    
    /**
     * 批处理
     * php5.5 index_cli.php --c=Test --a=indexAction
     */
    public function indexAction(){
        C('DB_CENTER.DB_NAME', '00000000');
        C('DB_CENTER.DB_TYPE', 'mysqlc');
        print_r(C('DB_CENTER'));

        $baseModel = new \Common\Model\Common\BaseModel();
        $rs = $baseModel->switchCenterDB()->query('select database(), id, email from user limit 2');
        print_r($rs);
        $rs = $baseModel->query('select count(1) as cnt from user');
        print_r($rs);
        
//        $result = SoapClientService::remoteCall('mysql_query', 'select * from user limit 1;');
//        print_r($result);exit;
        exit;
    }
    /**
     * 导用户
     * php5.5 index_cli.php --c=Test --a=excelAction
     */
   public function excelAction(Request $request){
        $filePath = '/tmp/test.xls';
        if(!file_exists($filePath)) die(json_encode(array("status" => "error", "message"=>"文件不存在")));
        
        $currentSheet = $this->getPhpExcel($filePath);
        $allRows = $currentSheet->getHighestRow();
        
        for($currentRow = 2;$currentRow <= $allRows;$currentRow++){
            $field['verifiedMobile'] = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('A') - 65, $currentRow)->getValue());    #手机
            $field['email']          = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('B') - 65, $currentRow)->getValue());    #邮箱     
            $field['nickname']       = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('C') - 65, $currentRow)->getValue());    #姓名
            $sex                     = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('D') - 65, $currentRow)->getValue());    #性别
            
            $field['studNum'] = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('E') - 65, $currentRow)->getValue());    #学号
            $className        = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('F') - 65, $currentRow)->getValue());    #班级
            if(empty($className)) $class = false;

            $field['studNum'] ='';
            $classTitles = filterExcelTrim((string)$currentSheet->getCellByColumnAndRow(ord('E') - 65, $currentRow)->getValue());    #（班主任）所管班级名
            $classTitles = str_replace('；',';',$classTitles);
            if(empty($classTitles)) $class = false;
            #qzw 2015-11-18
            $field['studNum'] = str_replace("'", "", $field['studNum']);
            
//            if($field['verifiedMobile'] !='15081238440') continue;
//            print_r(filterExcelTrim($currentSheet->getCellByColumnAndRow(ord('C') - 65, $currentRow)->getValue()));

            echo $currentRow .':' . $field['verifiedMobile'] .':' . $field['email']. ':'.$field['nickname'] . ':' .$field['studNum'] . PHP_EOL;

        }
    }

    private function getPhpExcel($filePath){
        if($filePath){
            require("./Vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php");
            //获取excel文件
            $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
            $currentSheet = $objPHPExcel->getSheet(0);
            return $currentSheet;          
        }
    }
    
    
}
