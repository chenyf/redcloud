<?php
/**
 * 系统评论
 * @author 谈海涛 2015-11-17
 */
namespace Common\Services;
class CommentService{
    //decrypt  encrypt $cmtType || !$cmtIdStr
    public function jiami($param=array()){
        $options = array(
            'cmtType'   => '',
            'cmtIdStr'  => '',
            'createUid' => '',
        );
        $options = array_merge($options, $param);
        extract($options);
        
        if(!$cmtType || !$cmtIdStr) return false;
        $secret = C('SECRET');
        $str = $cmtType.'_'.$cmtIdStr.'_'.$secret['COMMENT'];
        if($createUid) 
            $str = $cmtType.'_'.$cmtIdStr.'_'.$createUid.'_'.$secret['COMMENT'];
        return encrypt($str);
        
    }
    
    public function jiemi($str){
        if(!$str) return false;
        $string =decrypt($str);
        
        $arr = explode('_', $string);
        array_pop($arr);
        return $arr;
    }
    
    public function checkCommentVcode($cmtType ,$cmtIdStr , $vcode ){
        if(!$cmtType || !$cmtIdStr || !$vcode) return false;
        $arr = self::jiemi($vcode);
        if($arr['0'] != $cmtType) return false;
        if($arr['1'] != $cmtIdStr) return false;
        return $arr;
    }
    
    public function md5Vcode($type , $strId){
        if(!$type || !$strId) return false;
        if(!preg_match("/^[a-z0-9_]+$/", $type))  return false;
        if(!preg_match("/^[a-z0-9_]+$/", $strId))  return false;
        $secret = C('SECRET');
        // md5(webcode + 类型 + 参数id + 密钥)
        $str =  md5(C('WEBSITE_CODE').$type.$strId.$secret['APPRAISE']);
        return substr($str , 8,16);
    }
    
    public function checkVcode($type , $strId , $vcode){
        if(!preg_match("/^[a-z0-9_]+$/", $type))  return false;
        if(!preg_match("/^[a-z0-9_]+$/", $strId))  return false;
        $code = self::md5Vcode($type , $strId);
        return $code == $vcode ? true : false;
    }
    
    /**
     * 直播生成唯一设备id
     * @author tanhaitao 2016-01-21
     */
    public function getdeviceId($param){
        $options = array(
            'uid'           => '',
            'liveId'        => '',
            'webCode'       => '',
            'publicCourse'  => 0 ,
        );
        $options = array_merge($options, $param);
        extract($options);
        if(!$uid || !$liveId || !$webCode) return false;
        $randStr = time().mt_rand(1000,9999);
        $str = $webCode.'_'.$publicCourse.'_'.$uid.'_'.$liveId.'_'.$randStr;
        return encrypt($str);
        
    }
    /**
     * 直播解析设备id
     * @author tanhaitao 2016-01-21
     */
    public function parseDeviceId($str){
        if(!$str) return false;
        $string =decrypt($str);
        
        list($webCode,$publicCourse,$uid,$liveId,$randStr) = explode('_', $string);
        if(!$uid || !$liveId || !$webCode || !$randStr) return false;
        $data['webCode']        = $webCode;
        $data['publicCourse']   = $publicCourse;
        $data['uid']            = $uid;
        $data['liveId']         = $liveId;
        $data['randStr']        = $randStr;
        return $data;
    }
    
}