<?php

/**
 * 
 */

namespace Common\Lib;
use Common\Lib\Dhttp;
use Common\Lib\CCPRest;
class SendMessage {
    private $_account = array();
    public function __construct() {
        $this->_account = $this->getSmsSetting();
    }
    
     /**
     * 发送短信
     *
     * @param array $param        	
     * @return int 0=>发送成功 -1=>发送失败 -2=>部分发送失败
     * @author LiangFuJian 2013-11-15
     * @modify 2015-01-14 LiangFuJian
     */
    public function send($paramArr) {
        // 账号密码
        $account = $this->_account;
        $option = array(
            'mobile' => '', // 电话号码 如果群发则电话号码用英文逗号分隔如123242342,325435345
            'content' => '', // content 短信发送的内容
            'uid' => 0, // 用户id 默认0=>系统发送
            'subid' => '0001', // 选填 string 通道号码末尾添加的扩展号码
            'account' => 1, // 1=>验证码接口
            'timeout' => 5  // 请求超时时间
        );
        $param = array_merge($option, $paramArr);
        $msg = urlencode($param['content']); // 对信息内容进行url编码
        $arrtel = explode(',', $param['mobile']); // 将电话号码用逗号分隔成数组
        $num = count($arrtel); // 获取数组长度
        $sendnum = 1000; // 规定最多发的短信数

        $data ['message'] = $param ['content'];
        $data ['uid'] = $param ['uid'];
        $data ['created_at'] = time();
        // 判断发送短信是否超过1000
        if ($num <= $sendnum) {
            $rs = Dhttp::doGet("http://114.215.130.61:8082/SendMT/SendMessage?subid=0001&UserName={$account['sms_user']}&UserPass={$account['sms_password']}&Mobile={$param['mobile']}&Content={$msg}".$param ['timeout']);
//            $data ['mobile'] = $param ['mobile'];
//            $data ['status'] = $rs;
//            $data ['messagenum'] = $num;
//            M('sendMessage')->add($data);

            if ($rs >= 0)
                return 0;
            else
                return - 1;
        } else {
            // 发送 短信数/100 整数部分的短信
            for ($i = 1; $i <= floor($num / $sendnum); $i++) {
                $tmparr = array_slice($arrtel, ($i - 1) * $sendnum, $i * $sendnum);
                $tmptel = implode(',', $tmparr);
                $rs = Dhttp::doGet("http://114.215.130.61:8082/SendMT/SendMessage?subid=0001&UserName={$account['sms_user']}&UserPass={$account['sms_password']}&Mobile=$tmptel&Content=$msg".$param ['timeout']);
                if ($rs >= 0)
                    $rs = 0;
                else
                    $rs = - 1;

                $res [] = $rs;
//                $data ['mobile'] = $tmptel;
//                $data ['status'] = $rs;
//                $data ['messagenum'] = $sendnum;
//                M('sendMessage')->add($data);
            }
            // 发送余下的短信
            $remainarr = array_slice($arrtel, floor($num / $sendnum) * $sendnum, $num);
            $remaintel = implode(',', $remainarr);
            $rs = Dhttp::doGet("http://114.215.130.61:8082/SendMT/SendMessage?subid=0001&UserName={$account['sms_user']}&UserPass={$account['sms_password']}&Mobile=$remaintel&Content=$msg&subid=&SendTime=".$param ['timeout']);
            if ($rs >= 0)
                $rs = 0;
            else
                $rs = - 1;

            $res [] = $rs;

//            $data ['mobile'] = $remaintel;
//            $data ['status'] = $rs;
//            $data ['messagenum'] = $num % $sendnum;
//            M('sendMessage')->add($data);

            if (in_array(- 1, $res) && in_array(0, $res))
                return - 2; // 部分发送失败
            else if (!in_array(0, $res))
                return - 1; // 全部发送失败
            else if (!in_array(- 1, $res))
                return 0; // 全部发送成功
        }
    }
    
    private function getSmsSetting(){
         $settingService= createService("System.SettingServiceModel");
         $settingInfo = $settingService->get("sms");
         return $settingInfo;
    }
    
    /**
     * 发送短信
     * @param array $param        	
     * @return int 0=>发送成功 -1=>发送失败 -2=>部分发送失败
     * @author LiangFuJian 2013-11-15
     * @modify 2015-06-01 LiangFuJian
     */
    public function sendMessage2($paramArr) {
        // 账号密码
        $account = $this->_account;
        $option = array(
            'mobile' => '', // 电话号码 如果群发则电话号码用英文逗号分隔如123242342,325435345
            'content' => '', // content 短信发送的内容
            'uid' => 0, // 用户id 默认0=>系统发送
            'subid' => '0001', // 选填 string 通道号码末尾添加的扩展号码
            'account' => 1, // 发送短信使用的账号 0=>群发或者广告接口 1=>验证码接口
            'timeout' => 5,  // 请求超时时间
            'sign'    => '高校云', //签名  
        );
        $param = array_merge($option, $paramArr);
        
        $msg = urlencode($param ['content']); // 对信息内容进行url编码
        $arrtel = explode(',', $param ['mobile']); // 将电话号码用逗号分隔成数组
        $num = count($arrtel); // 获取数组长度
        $sendnum = 500; // 规定最多发的短信数

        $data ['message'] = $param ['content'];
        $data ['uid'] = $param ['uid'];
        $data ['created_at'] = time();
        // 判断发送短信是否超过500
        if ($num <= $sendnum) {
            $r = Dhttp::doGet("http://115.28.112.245:8082/SendMT/SendMessage?subid=0001&UserName={$account['sms_user']}&UserPass={$account['sms_password']}&Mobile={$param['mobile']}&Content=【{$param["sign"]}】{$msg}", $param ['timeout']);

            $aRs = explode(',',$r);

            if ($aRs[0] == '03' || $aRs[0] == '00') 
                $rs = 0;
            else
                $rs = -1;

//            $data ['mobile'] = $param ['mobile'];
//            $data ['status'] = $rs;
//            $data ['messagenum'] = $num;
//            M('sendMessage')->add($data);

            return $rs;

        } else {
            // 发送 短信数/100 整数部分的短信
            for ($i = 1; $i <= floor($num / $sendnum); $i++) {
                $tmparr = array_slice($arrtel, ($i - 1) * $sendnum, $i * $sendnum);
                $tmptel = implode(',', $tmparr);
                $r = Dhttp::doGet("http://115.28.112.245:8082/SendMT/SendMessage?subid=0001&UserName={$account['sms_user']}&UserPass={$account['sms_password']}&Mobile=$tmptel&Content=【{$param["sign"]}】$msg", $param ['timeout']);

                $aRs = explode(',',$r);

                if ($aRs[0] == '03' || $aRs[0] == '00')
                    $rs = 0;
                else
                    $rs = -1;

                $res [] = $rs;
//                $data ['mobile'] = $tmptel;
//                $data ['status'] = $rs;
//                $data ['messagenum'] = $sendnum;
//                M('sendMessage')->add($data);
            }
            // 发送余下的短信
            $remainarr = array_slice($arrtel, floor($num / $sendnum) * $sendnum, $num);
            if ($remainarr) {
                $remaintel = implode(',', $remainarr);
                $r = Dhttp::doGet("http://115.28.112.245:8082/SendMT/SendMessage?subid=0001&UserName={$account['sms_user']}&UserPass={$account['sms_password']}&Mobile=$remaintel&Content=【{$param["sign"]}】$msg&subid=&SendTime=", $param ['timeout']);
                $aRs = explode(',',$r);

                if ($aRs[0] == '03' || $aRs[0] == '00')
                    $rs = 0;
                else
                    $rs = -1;

                $res [] = $rs;
//                $data ['mobile'] = $remaintel;
//                $data ['status'] = $rs;
//                $data ['messagenum'] = $num % $sendnum;
//                M('sendMessage')->add($data);
            }

            if (in_array(- 1, $res) && in_array(0, $res))
                return - 2; // 部分发送失败
            else if (!in_array(0, $res))
                return - 1; // 全部发送失败
            else if (!in_array(- 1, $res))
                return 0; // 全部发送成功
        }
    }
    
    /**
     * 发送短信
     * 0=>发送成功 -1=>发送失败 -2=>部分发送失败
     * mobile手机号，如果群发则手机号用英文逗号分隔如123242342,325435345
     * datas内容数据，用来替换模板中的序号，顺序必须按照序号来
     * templateId模板ID
     * @author fubaosheng 2015-11-03
     */
    public function sendSms($param = array()){
        $options = array(
            'mobile' => '',
            'datas'  => array(),
            'templateId' => 0
        );
        $options = array_merge($options,$param);
        extract($options);
        
        $arrtel = explode(',', $mobile); // 将手机号用逗号分隔成数组
        $num = count($arrtel); // 获取数组长度
        $sendnum = 100; // 规定最多发的短信数
        
        $apiArr = array(
            'serverIP' => 'app.cloopen.com',#不要带http://
            'serverPort' => '8883',#端口号
            'softVersion' => '2013-12-26',#版本
            'accountSid' => '8a48b5514fba2f87014fcaf764f6253a',
            'accountToken' => 'c88d5b36976c400d9c819a5caef9e4c5',
            'appId' => '8a48b551506925be01506a565daa05a5',#应用ID
        );
        extract($apiArr);
        
        $rest = new CCPRest($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);
        
        if ($num <= $sendnum) {
            $result = $rest->sendTemplateSMS($mobile,$datas,$templateId);
            echo "手机号:{$mobile},模板Id:{$templateId},内容：".implode(",",$data).",返回状态:".$result->statusCode.";\r\n";
            if($result->statusCode == "000000")
                return 0;
            else
                return -1;
        }else{
            // 发送 短信数/100 整数部分的短信
            for ($i = 1; $i <= floor($num / $sendnum); $i++) {
                $tmparr = array_slice($arrtel, ($i - 1) * $sendnum,$sendnum);
                $tmptel = implode(',', $tmparr);
                $r = $rest->sendTemplateSMS($tmptel,$datas,$templateId);
                echo "手机号:{$mobile},模板Id:{$templateId},内容：".implode(",",$data).",返回状态:".$result->statusCode.";\r\n";
                if ($r->statusCode == "000000")
                    $rs = 0;
                else
                    $rs = -1;
                $res[] = $rs;
            }
            // 发送余下的短信
            $remainarr = array_slice($arrtel, floor($num / $sendnum) * $sendnum, $num);
            if ($remainarr) {
                $remaintel = implode(',', $remainarr);
                $r = $rest->sendTemplateSMS($remaintel,$datas,$templateId);
                echo "手机号:{$mobile},模板Id:{$templateId},内容：".implode(",",$data).",返回状态:".$result->statusCode.";\r\n";
                if($r->statusCode == "000000")
                    $rs = 0;
                else
                    $rs = -1;
                $res[] = $rs;
            }
            if (in_array(- 1, $res) && in_array(0, $res))
                return -2; // 部分发送失败
            else if (!in_array(0, $res))
                return -1; // 全部发送失败
            else if (!in_array(-1, $res))
                return 0; // 全部发送成功
        }
    }
}

?>