<?php
/*$key = model('Xdata')->lget('platform');
define('QZONE_APPID',	$key['qzone_appid']);
define('QZONE_APPKEY',	$key['qzone_appkey']);*/
define('KAIXIN_APPID',	'69725219963332ecd6369b7e557ba50c');
define('KAIXIN_APPKEY',	'8e58f89010f6b15b5592e45dd2351897');
class kaixin{
	function getUrl($callback){
		if (is_null($callback)) {
			$callback = U('career/Oauth/kaixincallback');
		}
		$loginurl = sprintf('http://www.kaixin001.com/login/connect.php?redirect_uri=%s&appkey=%s&re=/kx001_receiver.html&t=57',rawurlencode($callback),KAIXIN_APPID);
		return $loginurl;
	}
	//验证用户
	function checkUser(){
		$code = isset($_COOKIE['kx_connect_session_key']) ? $_COOKIE['kx_connect_session_key']: '';
		if (empty($code)) {
			return false;
		}
		$url    = "http://www.kaixin001.com/rest/rest.php";
		$parameters['api_key'] = KAIXIN_APPID;
		$parameters['method'] = 'users.getLoggedInUser';
		$parameters['call_id'] = microtime();
		$parameters['format'] = 'json';
		$parameters['mode'] = '1';
		$parameters['session_key'] = $code;
		$parameters['v'] = '1.0';
		ksort($parameters);
		$parameters['sig'] = $this->rr_generate_sig($parameters,KAIXIN_APPKEY);
		$query = http_build_query($parameters);
		$me = json_decode($this->postRequest($url,$query));
		//unset($_COOKIE['kx_connect_session_key']);
		if (isset($me->error)) {
			return false;
		}
		$_SESSION['kaixin']['session_key'] = $code;
		$_SESSION['kaixin']['uid'] = $me->result;
		$_SESSION['open_platform_type'] = 'kaixin';
		return true;
	}
	//用户资料
	function userInfo(){
		$url    = "http://www.kaixin001.com/rest/rest.php";
		$parameters['api_key'] = KAIXIN_APPID;
		$parameters['method'] = 'users.getInfo';
		$parameters['v'] = '1.0';
		$parameters['uids'] = $_SESSION['kaixin']['uid'];
		$parameters['call_id'] = microtime();
		$parameters['mode'] = '1';
		$parameters['format'] = 'json';
		$parameters['session_key'] = $_SESSION['kaixin']['session_key'];
		ksort($parameters);
		$parameters['sig'] = $this->rr_generate_sig($parameters,KAIXIN_APPKEY);
		$query = http_build_query($parameters);
		$me = json_decode($this->postRequest($url,$query));
		if (isset($me->error) || !is_array($me)) {
			return array();
		}
		$me = $me[0];
		$user['id']         =  $me->uid;
		$user['uname']       = $me->name;
		$user['province']    = '';
		$user['city']        = '';
		$user['location']    = '';
		$user['userface']    = $me->logo50;
		$user['sex']         = $me->gender == 1?0:1;
		//print_r($user);
		return $user;
	}
	function rr_generate_sig($param, $secret) {
		ksort($param);
		$request_str = '';
		foreach ($param as $key => $value)
		{
			$request_str .= "$key=$value";
		}
		$sig = $request_str . $secret;
		return md5($sig);
	}
	public function postRequest($url,$query)
	{
		$post_string = $query;
		if (function_exists('curl_init'))
		{
			$useragent = 'kaixin001.com API PHP5 Client 1.1 (curl) ' . phpversion();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			if (strlen($post_string) >= 3)
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			}
			//use https
			//curl_setopt($ch, CURLOPT_USERPWD, "username:password");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			$result = curl_exec($ch);
			curl_close($ch);
		}
		else
		{
			$content_type = 'application/x-www-form-urlencoded';
			$content = $post_string;
			$result = self::runHttpPost($content_type,$content,$url);
		}
		return $result;
	}
	public function runHttpPost($content_type, $content, $server_addr) {
		$user_agent = 'kaixin001.com API PHP5 Client 1.1 (non-curl) ' . phpversion();
		$content_length = strlen($content);
		$context = array(
		'http' => array(
		'method' => 'GET',
		'user_agent' => $user_agent,
		'header' => 'Content-Type: ' . $content_type . "\r\n" .
		'Content-Length: ' . $content_length,
		'content' => $content
		)
		);
		$context_id = stream_context_create($context);
		$sock = fopen($server_addr, 'r', false, $context_id);
		$result = '';
		if ($sock)
		{
			while (!feof($sock))
			{
				$result .= fgets($sock, 4096);
			}
			fclose($sock);
		}
		return $result;
	}
}
?>