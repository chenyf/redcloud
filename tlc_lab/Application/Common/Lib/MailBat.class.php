<?php
namespace Common\Lib;
class MailBat {
    private  function __construct($cfg = array()) {}
    private static $_instance;
    public function sendBatMailBySohu($param=array()){
        return $this->sendMailBySohu($param);
    }
    public static function  getInstance($cfg = array()){
        
        if(!(self::$_instance instanceof  self)){
            self::$_instance = new self($cfg = array());
        }
        return self::$_instance;
    }
    public function sendMailBySohu($param=array()){
        $options = array(
            'api_user'  => C("SOHU_API_USER"), # 使用api_user和api_key进行验证
            'api_key'   => C("SOHU_API_KEY"),   #密码
            'from'      => C("SOHU_FROM"), # 发信人，用正确邮件地址替代
            'fromname'  => C("SOHU_FROM_NAME"),   #* 要与样本名称70%以上相似
            'to'        => '',   #* 收件人地址，用正确邮件地址替代，多个地址用';'分隔
            'subject'   => '',   #* 要与样本主题匹配
            'label'     => C("SOHU_LABEL_ID"),    #  标签，sohu平台创建
            'html'      => '',   #* 正文,要与样本70%以上相似
        );
        $options  =  array_merge($options, $param);
        if(!$options['label']) unset($options['label']);
        
        $url = 'https://sendcloud.sohu.com/webapi/mail.send.xml';
        $opt = array(
            'http' => array(
                'method' => 'POST',
                'content' => http_build_query($options)
             )
        );
        $context  = stream_context_create($opt);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}