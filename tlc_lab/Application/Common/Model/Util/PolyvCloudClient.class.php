<?php

namespace Common\Model\Util;

use Common\Lib\ArrayToolkit;

class PolyvCloudClient {

	protected $url;

	protected $writetoken;

	protected $readtoken;

	protected $cid;

	private $settingService;

	public function __construct(array $options) {
		$this->settingService = createService('System.SettingServiceModel');
		$setting              = $this->settingService->get('storage');
		$this->writetoken     = $setting['write_token'];
		$this->readtoken      = $setting['read_token'];
		$this->url            = getScheme() . '://v.polyv.net/uc/services/rest?method=uploadfile';
		$this->cid            = C('POLYV_DIR_ID');
	}

	public function makeUploadParams($options) {
		$jsonRPC = json_encode([
			'title' => $options['name'],
			'desc'  => 'desc'
		]);
		$params  = [
			'url'        => $this->url,
			'postParams' => [
				'writetoken' => $this->writetoken,
				'cid'        => $this->cid,
				'JSONRPC'    => $jsonRPC
			],
		];
		return $params;
	}

}