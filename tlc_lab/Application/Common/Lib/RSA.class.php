<?php
/*
 * RSA加密解密
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Lib;

class RSA {


	/**
	 * 加密数据
	 * @param $data
	 * @return string
	 */
	public static function encrypt($data){
		$pi_key =  self::getPrivateKey();
		if(is_array($data)){
			$data = json_encode($data);
		}

		openssl_private_encrypt($data,$encrypted,$pi_key);
		var_dump($encrypted);
		$encrypted = base64_encode($encrypted);
		return $encrypted;
	}

	/**
	 * 解密数据
	 * @param $data
	 */
	public static function decrypt($data){
		$pu_key = self::getPublicKey();
		openssl_public_decrypt(base64_decode($data),$decrypted,$pu_key);
		return $decrypted;
	}

	/**
	 * 获取私钥
	 */
	private static function getPrivateKey(){
		$file = C('PRIVATE_KEY');
		if(!file_exists($file)){
			E('私钥不存在');
		}
		$private_key = file_get_contents($file);
		$pi_key =  openssl_pkey_get_private($private_key);
		if(!$pi_key){
			E('无效的私钥');
		}
		return $pi_key;
	}

	/**
	 * 获取公钥
	 */
	private static function getPublicKey(){
		$file = C('PUBLIC_KEY');
		if(!file_exists($file)){
			E('公钥不存在');
		}
		$public_key = file_get_contents($file);
		$pu_key = openssl_pkey_get_public($public_key);
		if(!$pu_key){
			E('无效的公钥');
		}
		return $pu_key;
	}

}