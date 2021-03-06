<?php 
include_once( 'qq/config.php' );
include_once( 'qq/txwboauth.php' );

date_default_timezone_set('Asia/Chongqing');

class qq{

	var $loginUrl;

	function getUrl(){
		$o = new QqWeiboOAuth( QQWB_AKEY , QQWB_SKEY  );

		$keys = $o->getRequestToken(U('career/Oauth/qqcallback'));

		$this->loginUrl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , "");
		$_SESSION['qq']['keys'] = $keys;
		return $this->loginUrl;
	}

	function qqgetUrl(){
		$o = new QqWeiboOAuth( QQWB_AKEY , QQWB_SKEY  );

		$keys = $o->getRequestToken(U('weibo/operate/qqsava'));

		$this->loginUrl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , "");
		$_SESSION['qq']['keys'] = $keys;
		return $this->loginUrl;
	}


	//用户资料
	function userInfo(){
		$me = $this->doClient()->verify_credentials();
		
		$user['id']         = $me['data']['name'];
		$user['uname']       = $me['data']['nick'];
		$user['province']    = $me['data']['province_code'];
		$user['city']        = $me['data']['city_code'];
		$user['location']    = $me['data']['location'];
		$user['userface']    = $me['data']['head']."/120";
		$user['sex']         = ($me['data']['sex']=='1')?1:0;
		//print_r($user);
		return $user;
	}

	private function doClient($opt){

		$oauth_token = ( $opt['oauth_token'] )? $opt['oauth_token']:$_SESSION['qq']['access_token']['oauth_token'];
		$oauth_token_secret = ( $opt['oauth_token_secret'] )? $opt['oauth_token_secret']:$_SESSION['qq']['access_token']['oauth_token_secret'];

		return new QqWeiboClient( QQWB_AKEY , QQWB_SKEY ,  $oauth_token, $oauth_token_secret  );
	}

	function user($opt)
	{
		return $this->doClient($opt)->user_info();
	}

	//验证用户
	function checkUser(){
		$o = new QqWeiboOAuth( QQWB_AKEY , QQWB_SKEY , $_SESSION['qq']['keys']['oauth_token'] , $_SESSION['qq']['keys']['oauth_token_secret']  );
		$access_token = $o->getAccessToken(  $_REQUEST['oauth_verifier'] ) ;
		$_SESSION['qq']['access_token'] = $access_token;
		$_SESSION['open_platform_type'] = 'qq';
	}

	//发布一条微博
	function update($text,$opt){
		return $this->doClient($opt)->t_add($text);
	}

	//上传一个照片，并发布一条微博
	function upload($text,$pic,$opt){
		return $this->doClient($opt)->t_add_pic($text,$pic);
	}
	function follow($uname,$opt=array())
	{
		return $this->doClient($opt)->f_add($uname);
	}
}
?>