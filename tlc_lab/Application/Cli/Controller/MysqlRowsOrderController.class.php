<?php
/**
 * MySQl记录条数排行
 * @author fubaosheng 2015-07-14
 */
namespace Cli\Controller;
use Common\Lib\MailBat;

class MysqlRowsOrderController{
    /**
     * 执行
     * /usr/local/php-5.5/bin/php  index_cli.php --c=MysqlRowsOrder --a=runAction >> /tmp/mysqlRowsOrder.log
     */
    public function runAction(){
        echo "[".date("Y-m-d H:i:s",time())."]开始查询数据\r\n".PHP_EOL;
        $field = "TABLE_NAME,ENGINE,TABLE_ROWS,DATA_LENGTH,INDEX_LENGTH,CREATE_TIME,UPDATE_TIME,TABLE_COLLATION";
        $where['TABLE_SCHEMA'] = C("DB_NAME");
        $center = C('DB_CENTER');
        $data['TABLE_SCHEMA'] = $center['DB_NAME'];
        $rows = M('information_schema.tables','',C('DB_MONITOR_ACCOUNT'))->field($field)->where($where)->order("TABLE_ROWS desc")->select();
        $centerRows = M('information_schema.tables','',C('DB_MONITOR_ACCOUNT'))->field($field)->where($data)->order("TABLE_ROWS desc")->select();
        $count = count($rows);
        $centerCount = count($centerRows);
        
        $mailBat = MailBat::getInstance();
        $manager = C("SYSTEM_MANAGER");
        $manager = implode(";", $manager);
        if($count>0){
            echo "[".date("Y-m-d H:i:s",time())."]结束查询数据，本地库总共有".$count ."条数据\r\n".PHP_EOL;
            $html = $this->getHtmlAction($rows,C("DB_NAME"));
            $param = array(
                'to' => $manager,
                'subject' => '本地库MySQl记录条数排行',
                'html' => $html
            );
            $mailBat->sendMailBySohu($param);
        }
        
        if($centerCount>0){
            echo "[".date("Y-m-d H:i:s",time())."]结束查询数据，中心库总共有".$centerCount ."条数据\r\n".PHP_EOL;
            $centerHtml = $this->getHtmlAction($centerRows,$center['DB_NAME']);
            $param = array(
                'to' => $manager,
                'subject' => '中心库MySQl记录条数排行',
                'html' => $centerHtml
            );
            $mailBat->sendMailBySohu($param);
        }
    }
    
    /*
    * 获取发送的内容
    */
    public function getHtmlAction($rows,$db_name){
        $title = "<h2>".$db_name."数据库</h2>";
        $tableStart = "<table border=2 cellspacing=0 cellpadding=0 style='width:100%;table-layout:fixed;text-align:center;'>";
        $tr = "<tr><td>序号</td><td style='width:20%'>表名</td><td>引擎</td><td>记录数</td><td>数据长度</td><td>索引长度</td><td>创建时间</td><td>更新时间</td><td>字符集</td></tr>";
        if($rows){
            foreach ($rows as $k => $row) {
                $k = $k+1;
                $tr.= "<tr><td>{$k}</td><td>{$row['TABLE_NAME']}</td><td>{$row['ENGINE']}</td><td>{$row['TABLE_ROWS']}</td><td>{$row['DATA_LENGTH']}</td><td>{$row['INDEX_LENGTH']}</td><td>{$row['CREATE_TIME']}</td><td>{$row['UPDATE_TIME']}</td><td>{$row['TABLE_COLLATION']}</td></tr>";
            }
        }
        $tableEnd = "</table>";
        $html = $title.$tableStart.$tr.$tableEnd;
        return stripslashes($html);
    }
}
?>
