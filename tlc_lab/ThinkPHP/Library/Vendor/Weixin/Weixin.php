<?php
include_once ('config.php');
Vendor('Dhttp.Dhttp');
class Weixin {
    
	private $_appId = MP_APPID;
	private $_appSecret = MP_APPSECRET;
	private $_errorMsg = '';
	
	/**
	 * 获取错误信息
	 * 
	 * @return string
	 */
	public function getErrorMsg() {
            return $this->_errorMsg;
	}
	
	/**
	 * 获取微信授权页面url
	 * @return string
	 */
	public function getUrl($callbackUrl = '') {
            
            if (empty($callbackUrl)) {
                $this->_errorMsg = '回调地址不能为空';
                return false;
            }
            
            if (empty($this->_appId) || empty($this->_appSecret)) {
                $this->_errorMsg = 'appId或者appSercret不能为空';
                return false;
            }
                
            $state_code = $this->_getStateCode();
            $scope = 'snsapi_userinfo'; // 应用授权作用域，拥有多个作用域用逗号（,）分隔，网页应用目前仅填写snsapi_login即可
            $oauthUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';

            return sprintf ( $oauthUrl, $this->_appId, urlencode($callbackUrl), $scope, $state_code );
	}
        
	
        /**
         * 验证用户
         * @param string $code
         * @param string $stateCode
         */
        public function getUinfo($code, $stateCode) {
            
            if (!$this->_checkStateCode ($stateCode)) {
                $this->_errorMsg = '非法请求';
                return false;
            }
            $access_token = $this->_getMpAccessToken($code);
            if (!$access_token) {
                return false;
            }
            
            $userinfoUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s';
            $uInfoUrl = sprintf ($userinfoUrl, $access_token, $this->_appId);
            
            $dhttp = new \Dhttp();
            $jsonData = $dhttp::doGet( $uInfoUrl ); // 获取

            $uInfo = json_decode( $jsonData, true );
            
            // 获取失败
            if (isset($uInfo['errcode'])) {
                $this->_errorMsg = $uInfo['errcode'] . ':' . $uInfo['errmsg'];
                return false;
            }

            return $uInfo;
	}
        
        /**
         * 获取用户基本信息
         * @param type $openid
         */
        public function getUinfoByOpenid($openid){
            
            if (empty($openid)) {
                $this->_errorMsg = '缺少openid参数';
                return false;
            }
            
            $access_token = $this->_getAccessToken();
            if (!$access_token) {
                return false;
            }
            
            $userinfoUrl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';
            $uInfoUrl = sprintf ($userinfoUrl, $access_token, $openid);
            
            $dhttp = new \Dhttp();
            $jsonData = $dhttp::doGet( $uInfoUrl ); // 获取

            $uInfo = json_decode( $jsonData, true );
            
            // 获取失败
            if (isset($uInfo['errcode'])) {
                $this->_errorMsg = $uInfo['errcode'] . ':' . $uInfo['errmsg'];
                return false;
            }
            
            if (isset($uInfo['subscribe']) && $uInfo['subscribe'] == 0) {
                $this->_errorMsg ='您还没有关注公众号';
                return false;
            }
            return $uInfo;
        }
	
	
	/**
	 * 获取state code
	 * 
	 * @return string
	 */
	private function _getStateCode() {
            $timeStamp = time ();
            $_SESSION['WX_STATE_CODE'] = md5($timeStamp);
            return $_SESSION ['WX_STATE_CODE'];
	}
	
	/**
	 * 验证state code
	 * 
	 * @param string $code        	
	 * @return bool
	 */
	private function _checkStateCode($code) {
            
            if (empty($code))
                return false;

            if (!isset($_SESSION['WX_STATE_CODE'] ))
                return false;

            if ($_SESSION['WX_STATE_CODE'] != $code)
                return false;

            return true;
	}
	
	/**
	 * 通过code获取access_token
	 * 
	 * @param string $code        	
	 * @return array
	 */
	private function _getMpAccessToken($code) {
            
            if (empty($this->_appId) || empty($this->_appSecret)) {
                $this->_errorMsg = 'appId或者appSercret不能为空';
                return false;
            }

            $tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
            $accessUrl = sprintf($tokenUrl, $this->_appId, $this->_appSecret, $code);
            
            $dhttp = new \Dhttp();
            $jsonData = $dhttp::doGet($accessUrl); // 获取
            $data = json_decode($jsonData, true );
            if (isset($data['errcode'])) {
                $this->_errorMsg = $data['errcode'] . ':' . $data['errmsg'];
                return false;
            }
            
            return $data['access_token'];
	}
        
        /**
         * 获取access_token
         * @return boolean
         */
        private function _getAccessToken(){
            
            $access_token = S('access_token');
		
            // 查看缓存中是否有数据
            if ($access_token)
                    return $access_token;
            
            if (empty($this->_appId) || empty($this->_appSecret)) {
                $this->_errorMsg = 'appId或者appSercret不能为空';
                return false;
            }

            $tokenUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
            $accessUrl = sprintf($tokenUrl, $this->_appId, $this->_appSecret);
            $dhttp = new \Dhttp();
            $jsonData = $dhttp::doGet($accessUrl); // 获取
            $data = json_decode($jsonData, true );
            
            if (isset($data['errcode'])) {
                $this->_errorMsg = $data['errcode'] . ':' . $data['errmsg'];
                return false;
            }
            
            // 缓存accessToken
            S('access_token', $data['access_token'], 7000);
            return $data['access_token'];
            
        }

}

?>
