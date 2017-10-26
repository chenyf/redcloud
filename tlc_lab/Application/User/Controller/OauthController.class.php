<?php
namespace User\Controller;

use Symfony\Component\HttpFoundation\Request;
use sina;
use wx;

class OauthController extends \Home\Controller\BaseController {
    
    private $_arrType = array('sina','qzone','wx');
    
    /**
     * 获取各个类型的链接，跳转
     * @author fbs 2016-04-06
     */
    public function tryOtherLoginAction(Request $request){
        
        $type = I('type') ? : '';
        if( !in_array($type, $this->_arrType))
            return $this->createMessageResponse('info', '登录方式选择错误', null, 3000, U('User/Signin/index'));
      
        $platform = new $type();
        $url = $platform->getUrl();
        redirect($url);
    }
    
    /**
     * 新浪回调
     */
    public function sinaCallbackAction(Request $request){
        $sina = new sina();
        $sina->checkUser();
        redirect(U('User/Oauth/otherLogin'));
    }

    /**
     * 微信回调
     */
    public function wxCallbackAction(Request $request){
        $wx = new wx();
        $wx->checkUser();
        redirect(U('User/Oauth/otherLogin'));
    }
    
    /**
     * 外站帐号登录
     * 验证授权信息，通过后与本站绑定账号
     */
    public function otherLoginAction(Request $request){
        
    }
    
}

?>
