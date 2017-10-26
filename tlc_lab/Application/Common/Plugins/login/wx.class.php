<?php
namespace Common\Plugins\login;
use Common\Lib\Dhttp;
include_once ('wx/config.php');
class wx {
	private $_appId = WX_APPID;
	private $_appSecret = WX_APPSECRET;
	private $_mpAppId = MP_APPID;
	private $_mpAppSecret = MP_APPSECRET;
	private $_errorCode = '';
	private $_errorMsg = '';
	
	/**
	 * 获取错误码
	 * 
	 * @return string
	 */
	public function getErrorCode() {
		return $this->_errorMsg;
	}
	
	/**
	 * 获取错误信息
	 * 
	 * @return string
	 */
	public function getErrorMsg() {
		return $this->_errorMsg;
	}
	
	/**
	 * 获取微信登录页面url
	 * 
	 * @return string
	 */
	public function getUrl() {
		if (empty ( $this->_appId ) || empty ( $this->_appSecret ))
			return false;
		
		$state_code = $this->_getStateCode ();
		$_SESSION ['open_platform_type'] = 'wx';
		$callbackUrl = U ( 'career/Oauth/wxcallback' );
		$scope = 'snsapi_login'; // 应用授权作用域，拥有多个作用域用逗号（,）分隔，网页应用目前仅填写snsapi_login即可
		$oauthUrl = 'https://open.weixin.qq.com/connect/qrconnect?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';
		
		return sprintf ( $oauthUrl, $this->_appId, urlencode ( $callbackUrl ), $scope, $state_code );
	}
	
	/**
	 * 验证用户
	 */
	public function checkUser() {
		$code = isset ( $_GET ['code'] ) ? $_GET ['code'] : '';
		$stateCode = isset ( $_GET ['state'] ) ? $_GET ['state'] : '';
		
		if (! $this->_checkStateCode ( $stateCode ))
			$this->error ( '非法请求' );
		
		$access_token = $this->_getOpAccessToken ( $code );
		$_SESSION ['wx'] ['access_token'] ['oauth_token'] = $access_token;
		$_SESSION ['wx'] ['access_token'] ['oauth_token_secret'] = md5 ( $access_token );
	}
	
	/**
	 * 获取用户信息
	 * 
	 * @return array
	 */
	function userInfo() {
		if (! isset ( $_SESSION ['wx'] ['access_token'] ['oauth_token'] ))
			$this->error ( '非法请求' );
		
		$uInfo = $this->_getUinfoByToken ( $_SESSION ['wx'] ['access_token'] ['oauth_token'] );
		
		// 获取失败
		if (isset ( $uInfo ['errcode'] ))
			$this->error ( $uInfo ['errcode'] . ':' . $uInfo ['errmsg'] );
		
		$user ['id'] = $uInfo ['unionid'];
		$user ['uname'] = $uInfo ['nickname'];
		$user ['province'] = $uInfo ['province'];
		$user ['city'] = $uInfo ['city'];
		$user ['location'] = $uInfo ['province'] . ' ' . $uInfo ['city'];
		$user ['userface'] = $uInfo ['headimgurl'];
		$user ['sex'] = ($uInfo ['sex'] == 2) ? 1 : 0;
		
		return $user;
	}
	
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// --------------------------------------------网页授权获取用户信息开始--------------------------------------------|
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	/**
	 * 获取微信网页授权url
	 * 
	 * @param string $redirect_uri
	 *        	回调地址（注意要urlencode）
	 */
	public function getSnsapiUrl($redirect_uri) {
		$appid = $this->_mpAppId;
		$state_code = $this->_getStateCode ();
		
		return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state_code}#wechat_redirect";
	}
	public function getSnsapiAccessToken($code) {
		$appid = $this->_mpAppId;
		$secret = $this->_mpAppSecret;
		$request_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
		
		return Dhttp::doGet ( $request_url );
	}
	
	/**
	 * 刷新access_token（如果需要），当refresh_token失效的后，需要用户重新授权
	 * 
	 * @param string $refresh_token
	 *        	第二部返回来的refresh_token
	 * @return json 结果同第二部一样
	 */
	public function refreshToken($refresh_token) {
		$appid = $this->_mpAppId;
		$request_url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$appid}&grant_type=refresh_token&refresh_token={$refresh_token}";
		
		return Dhttp::doGet ( $request_url );
	}
	public function getSnsapiUserInfo($access_token) {
		$appid = $this->_mpAppId;
		$request_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$appid}&lang=zh_CN";
		
		return Dhttp::doGet ( $request_url );
	}
	
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// --------------------------------------------网页授权获取用户信息结束--------------------------------------------|
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// -----------------------------------------------微信现金红包开始------------------------------------------------|
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	/**
	 * 获取红包签名
	 * 
	 * @param array $arrData        	
	 */
	private function _getRedPackSignature($arrData, $key) {
		
		// 去掉数组中的空值
		foreach ( $arrData as $k => $v ) {
			if (empty ( $v ))
				unset ( $arrData [$k] );
		}
		
		// 将数组键值转换成小写
		$array = array_change_key_case ( $arrData );
		
		// 将数组按照键值排序
		ksort ( $array );
		
		// 生成 URL-encode 之后的请求字符串
		$str = http_build_query ( $array );
		
		// 将encodeurl进行解码
		$stringSignTemp = urldecode ( $str ) . '&key=' . $key;
		
		// MD5加密
		$signStr = md5 ( $stringSignTemp );
		
		// 返回大写字符串
		return strtoupper ( $signStr );
	}
	
	/**
	 * 将数组转换成xml字符串
	 * 
	 * @param array $arrParam        	
	 * @return string
	 */
	private function _getXmlString($arrParam = array()) {
		$string = "<xml>";
		foreach ( $arrParam as $k => $v ) {
			$string .= "<" . $k . "><![CDATA[" . $v . "]]></" . $k . ">";
		}
		$string .= "</xml>";
		
		return $string;
	}
	
	/**
	 * curl post提交 xml红包数据 (此方法仅限现金哦给你报使用)
	 * 
	 * @param type $url        	
	 * @param type $data        	
	 * @return xml
	 */
	private function _curlPostSsl($url, $xmlData) {
		$cert = SITE_PATH . '/addons/plugins/login/wx/apiclient_cert.pem';
		$key = SITE_PATH . '/addons/plugins/login/wx/apiclient_key.pem';
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $xmlData );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt ( $ch, CURLOPT_SSLCERTTYPE, 'PEM' );
		curl_setopt ( $ch, CURLOPT_SSLCERT, $cert );
		curl_setopt ( $ch, CURLOPT_SSLKEYTYPE, 'PEM' );
		curl_setopt ( $ch, CURLOPT_SSLKEY, $key );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
		$resule = curl_exec ( $ch );
		curl_close ( $ch );
		
		return $resule;
	}
	
	/**
	 * 给某个用户发送现金红包（只能给一人发送）
	 * 
	 * @param array $paramArray        	
	 * @return bool
	 */
	public function sendRedPack($paramArray = array()) {
		$mch_id = '1227841102';
		$apiKey = '010867b447089d16a18d0d31254fb893';
		$requestUrl = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
		
		$data ['nonce_str'] = rand_string ( 32 );
		$data ['mch_billno'] = $mch_id . date ( 'Ymd' ) . rand ( 1000000000, 9999999999 ); // 商户订单号 组成： mch_id+yyyymmdd+10 位一天内不能重复的数字
		$data ['mch_id'] = $mch_id; // 商户号
		$data ['sub_mch_id'] = isset ( $paramArray ['sub_mch_id'] ) ? $paramArray ['sub_mch_id'] : ''; // 微信支付分配的子商户号，叐理模式下必填
		$data ['wxappid'] = $this->_mpAppId; // 商户 appid
		$data ['nick_name'] = '瑞德云网'; // 提供方名称
		$data ['send_name'] = '瑞德云网'; // 红包収送者名称
		$data ['re_openid'] = isset ( $paramArray ['re_openid'] ) ? $paramArray ['re_openid'] : ''; // 接叐收红包的用户用户在wxappid下的openid
		$data ['total_amount'] = isset ( $paramArray ['total_amount'] ) ? intval ( $paramArray ['total_amount'] ) : 1; // 付款金额， 单位分
		$data ['min_value'] = isset ( $paramArray ['min_value'] ) ? intval ( $paramArray ['min_value'] ) : 1; // 最小红包金额，单位分
		$data ['max_value'] = isset ( $paramArray ['max_value'] ) ? intval ( $paramArray ['max_value'] ) : 1; // 最大红包金额， 单位分 min_value=max_value =total_amount
		$data ['total_num'] = 1; // 红包収放总人数
		$data ['wishing'] = isset ( $paramArray ['wishing'] ) ? $paramArray ['wishing'] : ''; // 红包祝福语
		$data ['client_ip'] = isset ( $paramArray ['client_ip'] ) ? $paramArray ['client_ip'] : ''; // 调用接口的机器 Ip 地址
		$data ['act_name'] = isset ( $paramArray ['act_name'] ) ? $paramArray ['act_name'] : ''; // 活动名称
		$data ['remark'] = isset ( $paramArray ['remark'] ) ? $paramArray ['remark'] : ''; // 备注信息
		$data ['logo_imgurl'] = isset ( $paramArray ['logo_imgurl'] ) ? $paramArray ['logo_imgurl'] : '';
		; // 商户logo的url
		$data ['share_content'] = isset ( $paramArray ['share_content'] ) ? $paramArray ['share_content'] : '';
		; // 分享文案
		$data ['share_url'] = isset ( $paramArray ['share_url'] ) ? $paramArray ['share_url'] : '';
		; // 分享链接
		$data ['share_imgurl'] = isset ( $paramArray ['share_imgurl'] ) ? $paramArray ['share_imgurl'] : '';
		; // 分享的图片url
		$data ['sign'] = $this->_getRedPackSignature ( $data, $apiKey );
		
		// 将数组转换成xml
		$xmlData = $this->_getXmlString ( $data );
		
		// 提交数据
		$resXml = $this->_curlPostSsl ( $requestUrl, $xmlData );
		
		// 解析xml数据
		$resObj = simplexml_load_string ( $resXml, 'SimpleXMLElement', LIBXML_NOCDATA );
		
		// 保存返回数据
		$this->_errorCode = $resObj->return_code;
		$this->_errorMsg = $resObj->return_msg;
		
		if ($this->_errorCode == 'SUCCESS')
			return true;
		else
			return false;
	}
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// -----------------------------------------------微信现金红包结束------------------------------------------------|
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	/**
	 * 获取微信公众账号JsapiTicket值
	 * 
	 * @param string $JsapiTicket        	
	 */
	public function getApiJsapiTicket() {
		$token = $this->_getMpAccessToken ();
		$jsonData = $this->cache ( 'apiJsapiTicket' );
		
		$data = json_decode ( $jsonData, true );
		
		if (! $data ['ticket']) {
			
			$request_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$token}&type=jsapi";
			$jsonData = Dhttp::doGet ( $request_url );
			$data = json_decode ( $jsonData, true );
			
			if ($data ['ticket']) {
				$this->cache  ( 'apiJsapiTicket', $jsonData );
			}
		}
		
		return $data ['ticket'];
	}
	
	/**
	 * 获取微信公众账号签名
	 * 
	 * @param string $signature        	
	 */
	public function doSignature($url) {
		$ticket = $this->getApiJsapiTicket ();
		$noncestr = rand_string ( 11 );
		$timestamp = time ();
		$string = "jsapi_ticket={$ticket}&noncestr={$noncestr}&timestamp={$timestamp}&url={$url}";
		$data ['signature'] = sha1 ( $string );
		$data ['timestamp'] = $timestamp;
		$data ['noncestr'] = $noncestr;
		
		return $data;
	}
	
	/**
	 * 获取state code
	 * 
	 * @return string
	 */
	private function _getStateCode() {
		$timeStamp = time ();
		$_SESSION ['WX_STATE_CODE'] = md5 ( $timeStamp );
		return $_SESSION ['WX_STATE_CODE'];
	}
	
	/**
	 * 验证state code
	 * 
	 * @param string $code        	
	 * @return bool
	 */
	private function _checkStateCode($code) {
		if (empty ( $code ))
			return false;
		
		if (! isset ( $_SESSION ['WX_STATE_CODE'] ))
			return false;
		
		if ($_SESSION ['WX_STATE_CODE'] != $code)
			return false;
		
		return true;
	}
	
	/**
	 * 通过code获取access_token
	 * 
	 * @param string $code        	
	 * @return array
	 */
	private function _getOpAccessToken($code) {
		if (empty ( $this->_appId ) || empty ( $this->_appSecret ))
			return false;
		
		$tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
		$accessUrl = sprintf ( $tokenUrl, $this->_appId, $this->_appSecret, $code );
		$jsonData = Dhttp::doGet ( $accessUrl ); // 获取
		$data = json_decode ( $jsonData, true );
		
		if (isset ( $data ['errcode'] ))
			$this->error ( $data ['errcode'] . ':' . $data ['errmsg'] );
		
		return $data ['access_token'];
	}
	
	/**
	 * 获取公众帐号全局access_token
	 * 
	 * @return string
	 * @author LiangFuJian
	 *         @date 2015-01-14
	 */
	private function _getMpAccessToken() {
		$mpAccessToken = $this->cache ( 'mpAccessToken' );
		
		// 查看缓存中是否有数据
		if ($mpAccessToken)
			return $mpAccessToken;
		
		if (empty ( $this->_mpAppId ) || empty ( $this->_mpAppSecret ))
			return false;
			
			// 获取accessToken
		$mpTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
		$mpAccessUrl = sprintf ( $mpTokenUrl, $this->_mpAppId, $this->_mpAppSecret );
		$jsonData = Dhttp::doGet ( $mpAccessUrl ); // 获取
		$data = json_decode ( $jsonData, true );
		
		// 缓存accessToken
		$this->cache  ( 'mpAccessToken', $data ['access_token']);
		
		return $data ['access_token'];
	}
	
	/**
	 * 根据accessToken获取用户信息
	 * 
	 * @param string $accessToken        	
	 */
	private function _getUinfoByToken($accessToken) {
		if (empty ( $this->_appId ) || empty ( $accessToken ))
			return false;
		
		$userinfoUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s';
		$uInfoUrl = sprintf ( $userinfoUrl, $accessToken, $this->_appId );
		$jsonData = Dhttp::doGet ( $uInfoUrl ); // 获取
		
		return json_decode ( $jsonData, true );
	}
	private function cache($name, $value = null) {
		if (class_exists ( "Cache" )) {
			$cacheMem = Cache::getInstance ( 'Memcache' );
			if (is_null ( $value )) {
				return $cacheMem->get ( $name );
			} else {
				$cacheMem->set ( $name, $value, 7200 );
			}
		}else {
			if (is_null ( $value )) {
				return F( $name );
			} else {
				F( $name, $value);
			}
		}
	}
	
	/**
	 * 返回错误信息
	 * 
	 * @param string $data        	
	 */
	private function error($data) {
		utf8Header ();
		exit ( $data );
	}
}

?>
