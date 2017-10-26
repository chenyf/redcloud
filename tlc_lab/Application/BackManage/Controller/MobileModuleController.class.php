<?php
/*
 * 手机app 配置管理
 * @package    
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace BackManage\Controller;

use Think\Exception;

class MobileModuleController extends BaseController {


	public function installAction(){

		try{
			$this->getModuleService()->install();
			$this->success("数据生成成功");
		}catch (Exception $e){
			$this->success("数据生成失败:".$e->getMessage());
		}

	}

	public function indexAction() {


		$mainModules = $this->getModuleService()->getMainModule(I('type', 1));

		$currentModule = $this->getModuleService()->getModuleByName(I('type', 1), I('mainModule', 'INDEX'));

		$themeIds = $this->getModuleService()->getModuleThemeIds($currentModule['id']);

		$where['type'] = I('type', 1);
		$where['pid']  = $currentModule['id'];
		$list          = $this->getModuleService()->getModuleList($where);

		$this->render('Mobile:module-list', array(
			'mainModules'   => $mainModules,
			'mainModule'    => I("mainModule"),
			'type'          => I('type', 1),
			'list'          => $list,
			'currentModule' => $currentModule,
			'themeIds'      => $themeIds
		));
	}

	public function createAction() {
		$user = $this->getCurrentUser();

		$type = I('type', 0);
		$pid  = I('pid', 0);
		if ($_POST) {
			try {
				$this->getModuleService()->addModule(I('post.'), $user['id']);
				$this->success('保存成功');
			} catch (Exception $e) {
				$this->error('保存失败: ' . $e->getMessage());
			}
		}
		$this->render('Mobile:module-modal', array(
			'type' => $type,
			'pid'  => $pid,
		));
	}

	public function editAction() {

		if (I('sortIds')) {
			$this->getModuleService()->sortModule(I('sortIds'));
			$this->success('配置成功');
			exit;
		}

		if (I('action') == 'editStatus') {
			$this->getModuleService()->editStatus(I('id'), I('status'));
			$this->success('配置成功');
			exit;
		}

		$type   = I('type', 0);
		$pid    = I('pid', 0);
		$id     = I('id', 0);
		$module = $this->getModuleService()->getModule($id);

		$theme = $this->getModuleService()->ThemeList(array('type' => $type,'module'=>$module['name']));

		if ($_POST) {
			try {
				$this->getModuleService()->editModule(I('post.'), $module['name']);
				$this->success('保存成功');
			} catch (Exception $e) {
				$this->error('保存失败: ' . $e->getMessage());
			}
		}

		$this->render('Mobile:module-modal', array(
			'type'   => $type,
			'pid'    => $pid,
			'module' => $module,
			'theme'  => $theme,
		));
	}

	public function themeListAction() {

		$type = I('type', 1);
		//主模块列表
		$mainModules = $this->getModuleService()->getModuleConfig('mainModule',['type'=>$type]);

		$currentModule = $this->getModuleService()->getModuleConfig('currentModule', ['type'=>$type,'mainModule'=>I('mainModule', 'INDEX')]);

		$where['type'] = $type;
		$where['mainModule']  = I('mainModule','INDEX');
		//模块列表
		$ModuleList    = $this->getModuleService()->getModuleConfig('ModuleList',$where);
		$name           = I('name', $ModuleList['BANNER']['name']);
		//当前模块的样式列表
		$list          = $this->getModuleService()->ThemeList(array('module' => $name,'type'=>$type));
		//当前模块的默认样式
		$defaultTheme  = $this->getModuleService()->getModuleConfig('defaultTheme',array_merge($where,['name'=>$name]));
		$this->render('Mobile:theme-list', array(
			'mainModules'   => $mainModules,
			'mainModule'    => I("mainModule"),
			'type'          => I('type', 1),
			'name'          => $name,
			'ModuleList'    => $ModuleList,
			'list'          => $list,
			'currentModule' => $currentModule,
			'defaultTheme'  => $defaultTheme
		));
	}

	public function addThemeAction() {
		if ($_POST) {
			try {
				$user = $this->getCurrentUser();
				if ($_FILES['img'])
					$_POST['img'] = $this->getModuleService()->getBitImg($_FILES['img']);
				$this->getModuleService()->addTheme($_POST, $user['id']);
				$this->success('保存成功');
			} catch (Exception $e) {
				$this->success('保存失败:' . $e->getMessage());
			}
		}

		$this->render('Mobile:theme-modal', array(
			'module' => I('module'),
			'type'   => I('type')
		));

	}

	public function editThemeAction() {
		$id = I('id', 0);

		$theme = $this->getModuleService()->getTheme($id);

		if ($_POST) {
			try {
				if ($_FILES['img']) {
					$_POST['img'] = $this->getModuleService()->getBitImg($_FILES['img']);
				} else {
					unset($_POST['img']);
				}
				$this->getModuleService()->editTheme($_POST);
				$this->success('保存成功');
			} catch (Exception $e) {
				$this->error('保存失败: ' . $e->getMessage());
			}
		}

		$this->render('Mobile:theme-modal', array(
			'id'    => $id,
			'theme' => $theme
		));
	}

	public function delThemeAction($id) {
		try {
			$this->getModuleService()->delTheme($id);
			$this->success('删除成功');
		} catch (Exception $e) {
			$this->error('删除失败');
		}
	}

	public function getThemeImgAction($id) {
		if ($id) {
			return $this->getModuleService()->getThemeImg($id);
		}
	}

	public function getModuleService() {
		return createService('Mobile.ModuleService');
	}
}