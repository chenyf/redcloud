<?php
/*
 * 手机app 配置管理
 * @package    
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Think\Exception;

class MobileConfigController extends BaseController {

	public function indexAction() {

		$group = C('MOBILE_CONFIG_GROUP');

		$list = $this->getConfigService()->getConfigList();

		$this->render('MobileConfig:index', array(
			'group' => $group,
			'list'  => $list
		));
	}

	public function createAction() {

		$group = C('MOBILE_CONFIG_GROUP');
		$type  = C('MOBILE_CONFIG_TYPE');

		if ($_POST) {
			try {
				$this->getConfigService()->addConfig(I('post.'));
				$this->success('保存成功');
			} catch (Exception $e) {
				$this->error('保存失败: ' . $e->getMessage());
			}
		}

		$this->render('MobileConfig:config-modal', array(
			'group' => $group,
			'types' => $type,
		));
	}

	public function getConfigService() {
		return createService('Mobile.ConfigService');
	}
}