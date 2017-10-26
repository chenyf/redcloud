<?php
/*$key = model('Xdata')->lget('platform');
define('QZONE_APPID',	$key['qzone_appid']);
define('QZONE_APPKEY',	$key['qzone_appkey']);*/
define('RENREN_APPID',	'f2502a1fc61247eda52cea128ab0caaa');
define('RENREN_APPKEY',	'b38078cc8c014a1a89406086b43c202d');
require_once( SITE_PATH.'/addons/libs/fsockopenHttp.class.php' );
class renren{
	function getUrl($callback){
		if (is_null($callback)) {
			$callback = U('career/Oauth/renrencallback');
		}
		$loginurl = sprintf('http://graph.renren.com/oauth/authorize?client_id=%s&redirect_uri=%s&response_type=code&pp=1&https=https://graph.renren.com',RENREN_APPID,rawurlencode($callback));
		return $loginurl;
	}
	//验证用户
	function checkUser(){
		$code = isset($_REQUEST['code']) ? $_REQUEST['code']: '';
		if (empty($code)) {
			return false;
		}

		$retuest = $this->getAccessToken($code);
		$result = $this->getSessionKey($retuest->access_token);
		//将access token，openid保存!!
		//XXX 作为demo,临时存放在session中，网站应该用自己安全的存储系统来存储这些信息
		$_SESSION['renren']['access_token']['oauth_token']  = $result->renren_token->session_key;
		$_SESSION['renren']['access_token']['oauth_token_secret']  = $result->renren_token->session_secret;
		$_SESSION['renren']["userid"]  = $result->user->id;
		$_SESSION['open_platform_type'] = 'renren';
		//第三方处理用户绑定逻辑
		//将openid与第三方的帐号做关联
		return true;
	}
	//用户资料
	function userInfo(){
		$url    = "http://api.renren.com/restserver.do";
		$parameters['api_key'] = RENREN_APPID;
		$parameters['method'] = 'users.getInfo';
		$parameters['V'] = '1.0';
		$parameters['call_id'] = time();
		$parameters['uids'] = $_SESSION['renren']["userid"];
		$parameters['format'] = 'json';
		$parameters['fields'] = 'uid,name,mainurl,sex,hometown_location';
		$parameters['session_key'] = $_SESSION['renren']['access_token']['oauth_token'];
		$parameters['sig'] = $this->rr_generate_sig($parameters,RENREN_APPKEY);
		
		$query = http_build_query($parameters);
		$sk = new fsockopenHttp();
		$me = json_decode($sk->Post($url.'?'.$query));
		$me = $me[0];
		$user['id']         =  $me->uid;
		$user['uname']       = $me->name;
		$user['province']    = $me->hometown_location->province;
		$user['city']        = $me->hometown_location->city;
		$user['location']    = $me->hometown_location->province.' '.$me->hometown_location->city;
		$user['userface']    = $me->mainurl;
		$user['sex']         = $me->sex;
		//print_r($user);
		return $user;
	}
	function getAccessToken($code)
	{
		$url = 'https://graph.renren.com/oauth/token';
		$parameters['client_id'] = RENREN_APPID;
		$parameters['client_secret'] = RENREN_APPKEY;
		$parameters['redirect_uri'] = U('career/Oauth/renrencallback');
		$parameters['grant_type'] = 'authorization_code';
		$parameters['code'] = $code;
		$query = http_build_query($parameters);
		$sk = new fsockopenHttp();
		$retuest = json_decode($sk->Get($url.'?'.$query));
		return $retuest;
	}
	function getSessionKey($access_token)
	{
		$url = 'http://graph.renren.com/renren_api/session_key';
		$parameters['oauth_token'] = $access_token;
		$query = http_build_query($parameters);
		$sk = new fsockopenHttp();
		$retuest = json_decode($sk->Post($url.'?'.$query));
		return $retuest;
	}
	function rr_generate_sig($params, $secret) {
		ksort($params);
		$sig = '';
		foreach($params as $key=>$value) {
			$sig .= "$key=$value";
		}
		$sig .= $secret;
		return md5($sig);
	}
}
?>