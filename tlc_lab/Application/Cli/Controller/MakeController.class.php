<?php

/**
 * 自动生成模块(Controller ，ServiceModel，model，view)
 */

namespace Cli\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Controller\BaseController;

class MakeController {

	/**
	 * 执行
	 */
	public function run() {

	}

	/**
	 * php  index_cli.php --c=Make --a=makeModule --module=SchoolCourse
	 */
	public function makeModule() {
		$module = I('module');

		if ($module) {

			$config = include('./Application/Cli/Controller/Make/config/' . $module . '.php');

			if($config){
				$this->makeController($config['app'], $config['module']);
				$this->makeService($config['app'], $config['module']);
				$this->makeModel($config['app'], $config['module'], $config['table']);
				$this->makeView($config['app'], $config['module'], $config['title']);

			}else{
				$this->error('读取配置文件失败');
			}

		} else {
			$this->error('请输入module参数');
		}
	}

	/**
	 * 生成Controller
	 * @param $app
	 * @param $module
	 */
	public function makeController($app, $module) {

		$content = $this->render('Controller', [
			'app'    => $app,
			'module' => $module,
		]);

		$path = './Application/' . $app . '/Controller/' . $module . 'Controller.class.php';

		$this->put_content($path, $content);
	}

	/**
	 * 生成ServiceModel
	 * @param $app
	 * @param $module
	 */
	public function makeService($app, $module) {

		$content = $this->render('ServiceModel', [
			'app'    => $app,
			'module' => $module,
		]);

		$path = './Application/Common/Model/' . $app . '/' . $module . 'ServiceModel.class.php';

		$this->put_content($path, $content);
	}


	/**
	 * 生成model
	 * @param $app
	 * @param $module
	 * @param $table
	 */
	public function makeModel($app, $module, $table) {

		$content = $this->render('Model', [
			'app'    => $app,
			'module' => $module,
			'table'  => $table,
		]);

		$path = './Application/Common/Model/' . $app . '/' . $module . 'Model.class.php';

		$this->put_content($path, $content);
	}

	/**
	 * 生成view
	 * @param $app
	 * @param $module
	 * @param $title
	 */
	public function makeView($app, $module, $title) {

		$content = $this->render('index', [
			'app'    => $app,
			'module' => $module,
			'title'  => $title,
		]);

		$path = './Application/' . $app . '/View/' . $module . '/index.html.twig';
		$this->put_content($path, $content);

		$content = $this->render('modal', [
			'app'    => $app,
			'module' => $module,
			'title'  => $title,
		]);

		$path = './Application/' . $app . '/View/' . $module . '/modal.html.twig';

		$this->put_content($path, $content);
	}


	/**
	 * 生成js
	 * @param $app
	 * @param $module
	 */
	public function makeJs($app, $module){

	}

	/**
	 * 生成文件
	 * @param $path
	 * @param $content
	 */
	private function put_content($path, $content) {

		if (file_exists($path)) {
			$this->error($path . ' 文件已存在');
			exit;
		} else {
			$res = file_put_contents($path, $content);
		}

		if ($res) {
			$this->success($path . ' 生成成功');
		} else {
			$this->error($path . ' 生成失败');
		}
	}

	/**
	 * 渲染模板
	 * @param $template
	 * @param $params
	 * @return mixed
	 */
	private function render($template, $params) {
		$path = './Application/Cli/Controller/Make/' . $template . '.md';
		if (!file_exists($path)) {
			$this->error($path . ' 模板不存在');
			exit;
		}

		$content = file_get_contents($path);

		$keys   = array_keys($params);
		$values = array_values($params);

		foreach ($keys as &$v) {
			$v = '/\{\{' . $v . '\}\}/i';
		}

		$pattern     = $keys;
		$replacement = $values;

		return preg_replace($pattern, $replacement, $content);
	}

	private function success($str) {
		echo "\e[0;32m " . $str . " \e[0m " . PHP_EOL;
	}

	private function error($str) {
		echo "\e[0;31m " . $str . " \e[0m " . PHP_EOL;
	}

}

?>
