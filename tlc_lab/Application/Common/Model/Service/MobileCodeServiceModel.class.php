<?php
/**手机验证码
 * @author fubaosheng 2015-04-24
 */
namespace Common\Model\Service;

use Think\Model;
use Common\Lib\SendMessage;

class MobileCodeServiceModel extends \Common\Model\Common\BaseModel{
    
    private  $_errorMsg = '';
    private  $_errorCode = '';
    
    /**
     * 获取错误信息
     */
    public function getErrorMsg(){
        return $this->_errorMsg;
    }
        
    /**
     * 获取错误码
     */
    public function getErrorCode(){
        return $this->_errorCode;
    }
    
    /**
     * 获取手机动态密码
     */
    public function sendMobilePwd( $mobile, $uid = 0,$timeout = 60){
        $codeArr = array(
           1000 => '密码已发送请注意查收',
           1001 => '手机号码格式不正确',
           1002 => '请%d秒之后再重新获取',
           1004 => '获取密码失败',
           1005 => '此手机号没有注册过账号'
        );
         
        //手机号码的正则验证
        if ( !isValidMobile( $mobile ) ) {
            $this->_errorCode = 1001;
            $this->_errorMsg = $codeArr['1001'];
            return false;
        }

        $codeinfo = $this->table('user_idcode')
                    ->where(array('mobile' => $mobile))
                    ->order('id desc')
                    ->find();
        if ( !empty($codeinfo) ) {
            $timecha = time() - $codeinfo['created_at'];
            if ($timecha < $timeout) {
                $this->_errorCode = 1002;
                $this->_errorMsg = sprintf($codeArr['1002'],$timeout);
                return false;
            }
        }

        //根据手机号获取用户  中心库 edit fubaosheng 2015-08-10
        $uInfo = createService("User.UserServiceModel")->getUserByAccount($mobile);
        if ( !$uInfo ) {
            $this->_errorCode = 1005;
            $this->_errorMsg = $codeArr['1005'];
            return false;
        }

        $SMS_TPL = C('SMS_TPL');
        //验证通过
        $code = strtolower(rand_string());
        $param['mobile'] = $mobile;
        //发送内容
        $site = getSetting("site");
        $content = "{$uInfo['nickname']}同学您好，您的{$site['name']}高校云动态密码为{$code}，感谢您的使用，退订请回复TD。";
        $param['content'] = $content;
        $param['uid'] = $uid;
        $param['account'] = $SMS_TPL['LIVEPASSWORD']['type'];
        $sendMessage = new SendMessage();
        $res = $sendMessage->sendMessage2($param);

        if ( $res == 0 ) {
            $data['idcode'] = $code;
            $data['uid'] = $uid;
            $data['mobile'] = $mobile;
            $data['created_at'] = time();
            $this->table('user_idcode')->add($data);
            
            $this->_errorCode = 1000;
            $this->_errorMsg = $codeArr['1000'];
            return true;
        } else {
            $this->_errorCode = 1004;
            $this->_errorMsg = $codeArr['1004'];
            return false;
        }
    }
    
    /**
     * 验证手机密码
     */
    public function checkMobilePwd( $mobile, $code ) {
        $codeArr = array(
            1000 => '成功',
            1001 => '请输入正确的手机号',
            1003 => '密码已过期，请重新获取',
            1004 => '密码错误',
            1005 => '此手机号没有注册过账号'
        );
        $code = strtolower($code);
        
        if ( !isValidMobile( $mobile ) ) {
            $this->_errorCode = 1001;
            $this->_errorMsg = $codeArr[1001];
            return false;
        }

         //根据手机号获取用户  中心库 edit fubaosheng 2015-08-10
        $isBand = createService("User.UserServiceModel")->getUserByAccount($mobile);
        if ( !$isBand ) {
            $this->_errorCode = 1005;
            $this->_errorMsg = $codeArr['1005'];
            return false;
        }

        $codeInfo = $this->table('user_idcode')
                    ->where(array('mobile' => $mobile))
                    ->order('id desc')
                    ->find();
        //判断验证码是否超时 时间是10分钟
        if ($codeInfo && time() - $codeInfo['created_at'] > 60 * 10) {
            $this->_errorCode = 1003;
            $this->_errorMsg = $codeArr[1003];
            return false;
        }

        if ( $codeInfo && $codeInfo['idcode'] == $code ) {
            $this->_errorCode = 1000;
            $this->_errorMsg = $codeArr['1000'];
            return true;
       } else {
            $this->_errorCode = 1004;
            $this->_errorMsg = $codeArr['1004'];
            return false;
       }
    }
    
    /**
     * 获取手机验证码
     */
    public function sendMobileCode( $mobile, $uid = 0,$type ,$timeout = 60) {
        $codeArr = array(
            1000 => '验证码已发送请注意查收',
            1001 => '请输入正确的手机号码',
            1002 => '请%d秒之后再重新获取',
            1003 => '此手机已绑定，请更换其它手机号',
            1004 => '发送失败',
            1005 => '该账号不存在'
        );
        
        //手机号码的正则验证
        if ( !isValidMobile( $mobile ) ) {
            $this->_errorCode = 1001;
            $this->_errorMsg = $codeArr['1001'];
            return false;
        }

        $codeinfo = $this->table('user_idcode')
                    ->where(array('mobile' => $mobile))
                    ->order('id desc')
                    ->find();
        if ( !empty($codeinfo) ) {
            $timecha = time() - $codeinfo['created_at'];
            if ($timecha < $timeout) {
                $this->_errorCode = 1002;
                $this->_errorMsg = sprintf($codeArr['1002'],$timeout);
                return false;
            }
        }
        
        //根据手机号获取用户  中心库 edit fubaosheng 2015-08-10
        if($type != "sms_teacher_cash"){
            if($type=='sms_forget_password'){
                $uInfo = createService("User.UserServiceModel")->getUserByAccount($mobile);
            }else{
                $uInfo = $this->table('user')
                       ->field('id')
                       ->where(array('verifiedMobile'=>$mobile))
                       ->find();
            }
            //判断发送短信的类型 是注册或者是找回密码
            if($type=='sms_forget_password'){    //此类型为找回密码
                if ( !$uInfo ) {      //如果用户不存在
                    $this->_errorCode = 1005;
                    $this->_errorMsg = $codeArr['1005'];
                    return false;
                }
            }else{
                if ( $uInfo ) {     //注册操作时  如果用户存在
                    $this->_errorCode = 1003;
                    $this->_errorMsg = $codeArr['1003'];
                    return false;
                }
            }
        }
        
        $SMS_TPL = C('SMS_TPL');
        //验证通过
        $code = rand(90000, 100000);
        $param['mobile'] = $mobile;
        //发送内容
        $site = getSetting("site");
        $uname = $uInfo['nickname'] ? : "";
        if($type == "sms_registration"){
            $content = "{$uname}同学您好，您注册的{$site['name']}高校云验证码为{$code}，感谢您的注册，退订请回复TD。";
        }else if( in_array($type,array('sms_forget_password','sms_change')) ){
            $content = "{$uname}同学您好，您的{$site['name']}高校云验证码为{$code}，感谢您的使用，退订请回复TD。";
        }else{
            $content = sprintf($SMS_TPL['AUTHCODE']['content'], $code);
        }
        $param['content'] = $content;
        $param['uid'] = $uid;
        $param['account'] = $SMS_TPL['AUTHCODE']['type'];
        $sendMessage = new SendMessage();
        $res = $sendMessage->sendMessage2($param);
        
        if ( $res == 0 ) {
            $data['idcode'] = $code;
            $data['uid'] = $uid;
            $data['mobile'] = $mobile;
            $data['created_at'] = time();
            $this->table('user_idcode')->add($data);
            
            $this->_errorCode = 1000;
            $this->_errorMsg = $codeArr['1000'];
            return true;
        } else {
            $this->_errorCode = 1004;
            $this->_errorMsg = $codeArr['1004'];
            return false;
        }
    }
    
    /**
     * 验证手机验证码
     */
    public function checkMobileCode( $mobile, $code ,$type) {
        $codeArr = array(
            1000 => '成功',
            1001 => '请输入正确的手机号码',
            1002 => '该手机号码已被注册',
            1003 => '验证码已过期，请重新获取',
            1004 => '短信验证码输入错误',
            1005 => '该账号不存在'
        );
        
        if ( !isValidMobile( $mobile ) ) {
            $this->_errorCode = 1001;
            $this->_errorMsg = $codeArr[1001];
            return false;
        }
        
        if($type != "sms_teacher_cash"){
            //根据手机号获取用户  中心库 edit fubaosheng 2015-08-10
           if($type=='sms_forget_password')
               $isBand = createService("User.UserServiceModel")->getUserByAccount($mobile);
           else
               $isBand = $this->table('user')->field('verifiedMobile')->where(array('verifiedMobile'=>$mobile))->find();
           if($type=='sms_forget_password'){    //此类型为找回密码
               if ( !$isBand ) {      //如果用户不存在
                   $this->_errorCode = 1005;
                   $this->_errorMsg = $codeArr['1005'];
                   return false;
               }
           }else{
               if ( $isBand ) {     //注册操作时  如果用户存在
                   $this->_errorCode = 1002;
                   $this->_errorMsg = $codeArr['1002'];
                   return false;
               }
           }
        }
        
        $codeInfo = $this->table('user_idcode')
            ->where(array('mobile' => $mobile))
            ->order('id desc')
            ->find();
        //判断验证码是否超时 时间是30分钟
        if ($codeInfo && time() - $codeInfo['created_at'] > 60 * 30) {
            $this->_errorCode = 1003;
            $this->_errorMsg = $codeArr[1003];
            return false;
        }
        
        if ( $codeInfo && $codeInfo['idcode'] == $code ) {
           $this->_errorCode = 1000;
           $this->_errorMsg = $codeArr['1000'];
           return true;
       } else {
           $this->_errorCode = 1004;
           $this->_errorMsg = $codeArr['1004'];
           return false;
       }
    }
    
    /**
    * 用户绑定手机
    */
    public function bindMobile($uid,$mobile,$code){
        $codeArr = array(
            1000 => '成功',
            1001 => '失败',
            1002 => '请输入正确的手机号',
            1003 => '请重新获取验证码',
            1004 => '验证码错误',
            1005 => '验证码已过期，请重新获取',
            1006 => '此手机号已经绑定过，请换其他手机号'
        );

        if ( !isValidMobile( $mobile ) ) {
            $this->_errorCode = 1002;
            $this->_errorMsg = $codeArr[1002];
            return false;
        }

        $codeInfo = $this->table('user_idcode')
                ->where(array('uid' => $uid))
                ->order('id desc')
                ->find();
        if (!$codeInfo) {
            $this->_errorCode = 1003;
            $this->_errorMsg = $codeArr[1003];
            return false;
        }

        if ($codeInfo['idcode'] != $code) {
            $this->_errorCode = 1004;
            $this->_errorMsg = $codeArr[1004];
            return false;
        }
        //判断验证码是否超时 时间是30分钟
        if (time() - $codeInfo['created_at'] > 60 * 30) {
            $this->_errorCode = 1005;
            $this->_errorMsg = $codeArr[1005];
            return false;
        }

        $isBand = $this->table('user')->field('verifiedMobile')->where(array('verifiedMobile'=>$mobile))->find();
        if($isBand){
            $this->_errorCode = 1006;
            $this->_errorMsg = $codeArr[1006];
            return false;
        }
        $parm=array('uid'=>$uid);
        hook("bindMobileGeneralize",$parm);
        $map['id'] = $uid;
        $r = $this->table('user')
            ->where($map)
            ->setField('verifiedMobile',$mobile);
        if($r) synchroData(array('db'=>'private','sql'=>$this->table('user')->getLastSql()));
        return $r;
    }
    
}
?>
