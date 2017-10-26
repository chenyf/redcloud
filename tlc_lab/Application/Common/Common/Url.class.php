<?php
/**
 * 常用url
 *
 * @author 钱志伟 2015-05-06
 */
namespace Common\Common;
use Common\Twig\Web\WebExtension;
class Url {
    
    public static function getInstance(){
        return new self();
    }
    /**
     * 获得类别图标url
     * @author 钱志伟 2015-05-06
     */
    public static function getCategoryIconUrl($param=array()){
        $options = array(
            'iconFromType' => '', #来源：define=>自定义 system=>系统
            'icon'         => '', #相对路径
        );
        $options = array_merge($options, $param);
        extract($options);
        $uri = '';
        if($iconFromType=='system') {
            if(!$icon) $icon = 'default.png';
            $uri = getParameter('category_system_icon_urlpath') . '/' . ltrim($icon);
        }else{
            $uri = '/Public/' . ltrim($icon,'/');
        }
        return C('SITE_URL') . $uri;
    }
    
    /**
     * 获取用户头像
     * @param type $param
     */
    public function getUserFaceUrl($param = array()){
        $options = array(
            'type'=>'middle', //middle=>中等头像 big=>大头像 ,small =>小头像
            'uid' =>0,//用户id
        );
       $param = array_merge($options,$param);
       extract($param);
       static $arr = array();
       $str = $uid."_".$type;
       if(isset($arr[$str])) return $arr[$str];
        $user =$this->getUserService()->getUser($uid);
        $url = $user["mediumAvatar"];
        if($type == "big"){
            $url = $user["largeAvatar"];
        }else if($type == "middle"){
            $url = $user["mediumAvatar"];
        }else if($type == "small"){
            $url = $user["smallAvatar"];
        }
        $WebExtension  = new WebExtension();
        //$fileUrl = $WebExtension->getDefaultPath("avatar",$url);
        $fileUrl = $WebExtension->getUserDefaultPath($uid,$type); #edit tanhaitao 2015-09-25
        $arr[$str] = $fileUrl;
        return $arr[$str];
        
    }
    protected function getUserService()
    {
        return createService('User.UserServiceModel');
    }
    
    /**
     * 获取课程图片根据原url
     * @author fubaosheng 2015-05-12
     */
    public function getCoursePic($publicUrl = ''){
        $pic = C("site_url");
        $WebExtension  = new WebExtension();
        if(!$publicUrl) $publicUrl = "";
        $pic.= $WebExtension->getDefaultPath('coursePicture',$publicUrl,'large');
        return $pic;
    }
}