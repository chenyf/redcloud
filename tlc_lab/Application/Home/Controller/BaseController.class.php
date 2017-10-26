<?php
namespace Home\Controller;

use Org\Util\ArrayToolkit;
use Think\Controller;
use Think\Storage;

use Symfony\Component\HttpFoundation\JsonResponse;
use Common\Common\ServiceKernel;
use Common\Common\AppContainer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Common\Twig\Web\WebExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Common\Model\Common\AccessDeniedException;
use Org\Util\Rbac;

class BaseController extends Controller {

	protected $user;

	public $container;
        
	public $teacherTaskActivityStatus = 0;

	public function __construct() {
		parent::__construct();
		Request::enableHttpMethodParameterOverride();
		// 记录center,方便切换中心库与本地库 @author 王磊 8/11
//		if (!defined('CENTER')) {
//			define('CENTER', intval($_REQUEST['center']) == 1 ? 'center' : '');
//		}
		$request        = Request::createFromGlobals();
		$app['request'] = $request;
		$user           = $this->user =  $this->getCurrentUser();
		//如果已登录
        if ($user->id > 0) {
            $app['user'] = $user;
            $this->user = $user;
        }

		if(strtolower(MODULE_NAME) == 'backmanage'){

			if($user->isLogin()){
				if(!goBackEnd()){
					$this->redirect('Home/Default/index');
					exit;
				}
			}else{
				$this->redirect('User/Signin/index');
				exit;
			}
		}


		$this->assign('app', $app);
		$this->assign('scheme', getScheme());
		$this->assign('isProductEnv', isProductEnv()); #是否生产环境 true|false qzw 2015-09-19
		$this->assign('oldCourse', intval($_REQUEST['oldCourse']));

                //$this->assign('isHttps', )
		$this->container = new AppContainer();
                #后台模板配置抽离 edit by qzw 2015-01-19
		if (!defined('THEME_PATH') && !in_array(MODULE_NAME, C('MODULE_BACKGROUND'))) {
			$themePath = $this->getThemePath();
			define("THEME_PATH", $themePath);
		}
	}

	//页面不存在
	public function _404($message = null) {
		$message = $message ? $message : '抱歉，您访问的内容不存在';
		if (IS_AJAX) {
			return json_encode([
				'status' => 404,
				'info'   => $message
			]);
		} else {
			$this->render('Home@Default:404', [
				'message' => $message,
				'forword' => $_SERVER['HTTP_REFERER']
			]);
		}
		exit;
	}
	

	/**
	 * 返回403（无权限）页面或json
	 * @param null $message
	 * @return string
	 */
	public function _403($message = null) {
		$message = $message ? $message : '抱歉，您没有足够的权限执行此操作。';
		if (IS_AJAX) {
			return json_encode([
				'status' => 403,
				'info'   => $message
			]);
		} else {
			$this->render('Home@Default:403', [
				'message' => $message,
				'forword' => $_SERVER['HTTP_REFERER']
			]);
		}
		exit;
	}

	/**
	 * 获取系统模板路径
	 * @author 杨金龙 2015-02-12
	 */
	private function getThemePath() {
		static $path = "";
		if (!$path) $uri = createService('System.SettingService')->get('theme', array());
		if (!empty($uri['uri']) && $uri['uri'] != 'default') {
			$realUrl = trim($uri['uri'], '/');
			$path    = "./Public/themes/" . $realUrl . "/TopxiaWebBundle/views/";
		} else {
			$path = "./Application/Home/View/";
		}
		return $path;
	}

	/**
	 * 获得当前用户
	 * 如果当前用户为游客，那么返回id为0, nickanme为"游客", currentIp为当前IP的CurrentUser对象。
	 * 不能通过empty($this->getCurrentUser())的方式来判断用户是否登录。
	 */
	protected function getCurrentUser() {

		return $this->getUserService()->getCurrentUser();
	}

	protected function isAdminOnline() {
		return isGranted('ROLE_ADMIN');
	}

//    public function getUser()
//    {
//        throw new \RuntimeException('获得当前登录用户的API变更为：getCurrentUser()。');
//    }

	protected function tipMessageCloseWindow($message){
		return $this->createMessageResponse("error",$message,"",5000,null,3000);
	}

	/**
	 * 创建消息提示响应
	 * @param  string  $type     消息类型：info, warning, error
	 * @param  string  $message  消息内容
	 * @param  string  $title    消息抬头
	 * @param  integer $duration 消息显示持续的时间
	 * @param  string  $goto     消息跳转的页面
	 * @return Response
	 */
	protected function createMessageResponse($type, $message, $title = '', $duration = 0, $goto = null,$close_windows = 0) {
		if (!in_array($type, array('info', 'warning', 'error'))) {
			throw new \RuntimeException('type不正确');
		}

		if($duration == 0){
			$duration = 5000;
		}

		return $this->render('Home@Default:message', array(
			'type'     => $type,
			'message'  => $message,
			'title'    => $title,
			'duration' => $duration,
			'goto'     => $goto,
			'close_windows' => $close_windows
		));
	}

	protected function createMessageModalResponse($type, $message, $title = '', $duration = 0, $goto = null) {
		return $this->render('Default:message-modal', array(
			'type'     => $type,
			'message'  => $message,
			'title'    => $title,
			'duration' => $duration,
			'goto'     => $goto,
		));
	}

	protected function authenticateUser($user) {
//        $user['currentIp'] = getRequest()->getClientIp();
//        $user['currentIp'] = $this->container->get('request')->getClientIp();
//        $currentUser = new CurrentUserModel();
//        $currentUser->fromArray($user);
//
//       $this->setCurrentUser($currentUser);
//        ServiceKernel::instance()->setCurrentUser($currentUser);
//
//        $token = new UsernamePasswordToken($currentUser, null, 'main', $currentUser['roles']);
//        $this->container->get('security.context')->setToken($token);
//
//        $loginEvent = new InteractiveLoginEvent($this->getRequest(), $token);
//        $this->get('event_dispatcher')->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
//
//        $loginBind = $this->setting('login_bind', array());
//        if (empty($loginBind['login_limit'])) {
//            return ;
//        }
//
////        $sessionId = $this->container->get('request')->getSession()->getId();
//        $sessionId = getRequest()->getSession()->getId();
//        $this->getUserService()->rememberLoginSessionId($user['id'], $sessionId);
	}

	protected function setFlashMessage($level, $message) {
//        $this->get('session')->getFlashBag()->add($level, $message);
		$this->container->get('session')->getFlashBag()->add($level, $message);
	}

	protected function setting($name, $default = null) {
		return WebExtension::getSetting($name, $default);
		//    return $this->get('redcloud.twig.web_extension')->getSetting($name, $default);
	}

	protected function isPluginInstalled($name) {
		return $this->get('redcloud.twig.web_extension')->isPluginInstaled($name);
	}

	protected function createNamedFormBuilder($name, $data = null, array $options = array()) {
		return $this->container->get('form.factory')->createNamedBuilder($name, 'form', $data, $options);
	}

	/**
	 *  创建静态页面
	 * @access protected
	 * @htmlfile 生成的静态文件名称
	 * @htmlpath 生成的静态文件路径
	 * @param string $templateFile 指定要调用的模板文件
	 * 默认为空 由系统自动定位模板文件
	 * @return string
	 */
	protected function buildHtml($htmlfile='',$htmlpath='',$templateFile='',array $data = array()) {
		$this->assign($data);
		$content    =   $this->fetch($templateFile);
		$htmlpath   =   !empty($htmlpath) ? $htmlpath : HTML_PATH;
		$htmlfile   =   $htmlpath.$htmlfile.C('HTML_FILE_SUFFIX');
		Storage::put($htmlfile,$content,'html');
		return $htmlfile;
	}

	protected function buildTplHtml($htmlpath='',$templatePath='',array $data = array()){
		$this->assign($data);

		$tpl_file_list = $this->fetchList($templatePath);
		$htmlpath   =   !empty($htmlpath)?$htmlpath:HTML_PATH;
//		doLog(implode("|",$tpl_file_list));
		foreach ($tpl_file_list as $tpl_file){
			$htmlfile = basename($tpl_file);
			doLog("tpl_file:" . $tpl_file);
			$this->buildHtml($htmlfile,$htmlpath,$tpl_file,$data);
		}

		return $htmlpath."index.html";
	}

	protected function listTpl($templatePath){
		return $this->fetchList($templatePath);
	}

	protected function sendEmail($to, $title, $body, $format = 'text') {
//        $format == 'html' ? 'text/html' : 'text/plain';
		$format = ($format == 'html') ? 'text/html' : 'text/plain';
		$config = $this->setting('mailer', array());

		if (empty($config['enabled'])) {
			return false;
		}

		$transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'])
			->setUsername($config['username'])
			->setPassword($config['password']);
		$mailer    = \Swift_Mailer::newInstance($transport);

		$email = \Swift_Message::newInstance();
		$email->setSubject($title);
		$email->setFrom(array($config['from'] => $config['name']));
		$email->setTo($to);
		if ($format == 'text/html') {
			$email->setBody($body, 'text/html');
		} else {
			$email->setBody($body);
		}

		$mailer->send($email);

		return true;
	}

	protected function createJsonResponse($data) {
		echo json_encode($data);
		exit();
//        return new JsonResponse($data);
	}

	protected function createJsonResponse_a($data) {
//        echo json_encode($data);
//        exit();
		return new JsonResponse($data);
	}

	/**
	 * JSONM
	 * https://github.com/lifesinger/lifesinger.github.com/issues/118
	 */
	protected function createJsonmResponse($data) {
		$response = new JsonResponse($data);
		$response->setCallback('define');
		return $response;
	}

	protected function createAccessDeniedException($message = null) {
		if ($message) {
			return new \Common\Model\Common\AccesssDeniedException($message);
		} else {
			return new \Common\Model\Common\AccesssDeniedException();
		}
	}

	protected function getServiceKernel() {
		return ServiceKernel::instance();
	}

	protected function getUserService() {
		return createService('User.UserServiceModel');
	}

	protected function getLogService() {
		return createService('System.LogServiceModel');
	}

	/**
	 * Renders a view.
	 * @param string   $view       The view name
	 * @param array    $parameters An array of parameters to pass to the view
	 * @param Response $response   A response instance
	 * @return Response A Response instance
	 */
	public function render($view, array $parameters = array(), $return = false, Response $response = null) {
		$this->assign('debug', 1);

		$this->assign($parameters);
		if (!$return) {
			echo $this->fetch($view);
		} else {
			return $this->fetch($view);
		}
	}


	/**
	 * Generates a URL from the given parameters.
	 * @param string      $route         The name of the route
	 * @param mixed       $parameters    An array of parameters
	 * @param bool|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
	 * @return string The generated URL
	 * @see UrlGeneratorInterface
	 */
	public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
		$routeHomeArr = C("route_home");
		//var_dump($routeHomeArr["course_show"],$name);exit();
		$routeAdminArr = C("route_admin");
		$map           = "";
		if (isset($routeHomeArr[$route])) {
			$map = $routeHomeArr[$route];
		} else if (isset($routeAdminArr[$route])) {
			$map = $routeAdminArr[$route];
		}
		$url = U($map, $parameters);
		return $url;
	}

	/**
	 * Forwards the request to another controller.
	 * @param string $controller The controller name (a string like BlogBundle:Post:index)
	 * @param array  $path       An array of path parameters
	 * @param array  $query      An array of query parameters
	 * @return Response A Response instance
	 */
	public function forward($controller, array $path = array(), array $query = array()) {
		$controller = explode(":", $controller);
		$len        = count($controller);
		$url        = "";
		if (strpos($controller[0], "Admin") !== false) {
			$controller[0] == "Admin";
		} else {
                        #note by qzw 2015-11-20
			//$controller[0] = "Home";
		}
		$url = implode("/", $controller);
		$this->redirect($url, $path, 0);
		/*
		$path['_controller'] = $controller;
		$subRequest = $this->container->get('request')->duplicate($query, null, $path);

		return $this->container->get('http_kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
		 */
	}

	/**
	 * Returns a RedirectResponse to the given URL.
	 * @param string $url    The URL to redirect to
	 * @param int    $status The status code to use for the Response
	 * @return RedirectResponse
	 */
	/*
	public function redirect($url, $status = 302)
	{
		return new RedirectResponse($url, $status);
	}

	 */

	/**
	 * Returns a rendered view.
	 * @param string $view       The view name
	 * @param array  $parameters An array of parameters to pass to the view
	 * @return string The rendered view
	 */
	public function renderView($view, array $parameters = array(), $return = false, Response $response = null) {
//        return $this->container->get('templating')->render($view, $parameters);
		return $this->render($view, $parameters, $return);
	}


	/**
	 * Streams a view.
	 * @param string           $view       The view name
	 * @param array            $parameters An array of parameters to pass to the view
	 * @param StreamedResponse $response   A response instance
	 * @return StreamedResponse A StreamedResponse instance
	 */
	public function stream($view, array $parameters = array(), StreamedResponse $response = null) {
		$templating = $this->container->get('templating');

		$callback = function () use ($templating, $view, $parameters) {
			$templating->stream($view, $parameters);
		};

		if (null === $response) {
			return new StreamedResponse($callback);
		}

		$response->setCallback($callback);

		return $response;
	}

	/**
	 * Returns a NotFoundHttpException.
	 * This will result in a 404 response code. Usage example:
	 *     return $this->createMessageResponse('error','Page not found!');
	 * @param string     $message  A message
	 * @param \Exception $previous The previous exception
	 * @return NotFoundHttpException
	 */
	public function createNotFoundException($message = 'Not Found', \Exception $previous = null) {
		return new NotFoundHttpException($message, $previous);
	}

	/**
	 * Creates and returns a Form instance from the type of the form.
	 * @param string|FormTypeInterface $type    The built type of the form
	 * @param mixed                    $data    The initial data for the form
	 * @param array                    $options Options for the form
	 * @return Form
	 */
	public function createForm($type, $data = null, array $options = array()) {
		return $this->container->get('form.factory')->create($type, $data, $options);
	}

	/**
	 * Creates and returns a form builder instance
	 * @param mixed $data    The initial data for the form
	 * @param array $options Options for the form
	 * @return FormBuilder
	 */
	public function createFormBuilder($data = null, array $options = array()) {
		return $this->container->get('form.factory')->createBuilder('form', $data, $options);
	}

	/**
	 * Shortcut to return the request service.
	 * @return Request
	 */
	public function getRequest() {
		return getRequest();
		//return $this->container->get('request');
	}

	/**
	 * Shortcut to return the Doctrine Registry service.
	 * @return Registry
	 * @throws \LogicException If DoctrineBundle is not available
	 */
	public function getDoctrine() {
		if (!$this->container->has('doctrine')) {
			throw new \LogicException('The DoctrineBundle is not registered in your application.');
		}

		return $this->container->get('doctrine');
	}

	/**
	 * Get a user from the Security Context
	 * @return mixed
	 * @throws \LogicException If SecurityBundle is not available
	 * @see Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser()
	 */
	public function getUser() {
		if (!$this->container->has('security.context')) {
			throw new \LogicException('The SecurityBundle is not registered in your application.');
		}

		if (null === $token = $this->container->get('security.context')->getToken()) {
			return;
		}

		if (!is_object($user = $token->getUser())) {
			return;
		}

		return $user;
	}

	/**
	 * Returns true if the service id is defined.
	 * @param string $id The service id
	 * @return bool    true if the service id is defined, false otherwise
	 */
	public function has($id) {
		return $this->container->has($id);
	}

	/**
	 * Gets a service by id.
	 * @param string $id The service id
	 * @return object The service
	 */
	public function get($id) {
		if ($id == 'request') return Request::createFromGlobals();
		elseif ($id == 'redcloud.twig.web_extension') {
			return new WebExtension();
		} elseif ($id == 'session') {
			return $this->container->get($id);
		} elseif ($id == 'redcloud.target_helper') {
			return $this->container->get($id);
		} elseif ($id == 'filesystem') {
			return $this->container->get($id);
		}
		E("qzw define: no found obj {$id} in BaseController");
		return $this->container->get($id);
	}

	public function jsonResponse($status = 200, $code = 'DFT_CODE', $msg = '') {
		if (C('code.' . $code)) $msg = C('code.' . $code);

		if ($status == 200) {
			die("undefined succ return baseController");
		} else {
			$json = new JsonResponse(array('error' => array('name' => $code, 'message' => $msg)), $status);
		}
		$json->send();
		exit;
	}

	public function setCurrentUser($currentUser) {
		$this->currentUser = $currentUser;
		return $this;
	}

	/**
	 * 显示错误提示
	 * @author 钱志伟
	 */
	public function showError($msg = '') {
		$request = Request::createFromGlobals();
		\Symfony\Component\HttpFoundation\Response::create($msg, 200)->prepare($request)->send();
	}

	private function getGroupService() {
		return createService('Group.GroupServiceModel');
	}

	public function showHtml($filepath){
		if(empty($filepath) or substr($filepath,strrpos($filepath,".html") + 1,mb_strlen($filepath)) != "html"){
			return $this->showError("invalid html file");
		}else if(!is_file($filepath)){
			return $this->createMessageResponse("error","不存在该页面");
		}else{
			header("Content-Type:text/html;charset=utf-8");
			// 页面缓存
			ob_start();
			ob_implicit_flush(0);
			echo file_get_contents($filepath);
			ob_flush();
			exit();
		}
	}

	//构建教师的个人主页静态页面
	protected function buildTeacherHtml($user,$isPreview = false,array $data = array(),$tpl_index = 0){

		$currentUser = $this->getCurrentUser();

		if($currentUser->isLogin() && (!$currentUser->isAdmin() && $currentUser['id'] != $user['id']))
		{
			return $this->redirect('User/Signin/index');
		}


		if(empty($user) || !in_array('ROLE_TEACHER',$user['roles'])){
			return false;
		}

		$static_dir = getParameter("user.teacher.homepage_dir");
//        $filename = $user['userNum'] . ".html";

		if(empty($data)){
			$data = createService('User.TeacherInfoService')->getTeacherInfoByTeacherId($user->id);
			$data['contacts'] = json_decode($data['contacts']);
			$data['intros'] = json_decode($data['intros']);
			$data['teaches'] = json_decode($data['teaches']);
			$data['researches'] = json_decode($data['researches']);
			$data['publications'] = json_decode($data['publications']);

			$tpl_index = empty($data['tpl']) ? 1 : $data['tpl'];
		}

		//教师的tlc在线课程
		$data['tlc_course'] =   array();
		$teacherCourse = createService("Course.CourseService")->findTeacherCourseList($user['id']);

		$arr['key'] = "TLC在线课程";
		$arr['value']   =   $teacherCourse;
//        array_push($data['tlc_course'],$arr);
		array_push($data['teaches'],$arr);

		if($isPreview){
			$htmlDir = rtrim($static_dir, "/") . "/preview/{$user['userNum']}/";
		}else{
			$htmlDir = rtrim($static_dir, "/") . "/{$user['userNum']}/";
		}

		$userProfile    =  $this->getUserService()->getUserProfile($user['id']);

//        $viewPath = $this->buildHtml(trim($filename, "/"), $htmlDir, 'homepage_tpl', array(
//            'data' => $data,
//            'user' => $user,
//            'profile'   =>  $userProfile
//        ));

		$tpl_path = "Home@HomepageTpl:Tpl$tpl_index";

		//网站配置信息
		$site = createService('System.SettingServiceModel')->get('site', array());

		if(empty($site['school_name'])){
			$title = $user['nickname'] . " -个人主页";
		}else{
			$title = $user['nickname'] . " -个人主页," . $site['school_name'] . "/" . $site['school_english_name'];
		}

		$description = $site['school_name'] . $userProfile['college'] . $user['nickname'] . $userProfile['position'] . "主页," . $site['school_english_name'];
		$keyword_list = array($user['nickname'],$userProfile['college'],$userProfile['position'],$site['school_name'],$site['school_english_name']);

		$keyword_list = array_filter($keyword_list);

		if(@is_dir($htmlDir)){
			@mkdir($htmlDir, 0777, true);
		}

		$viewPath = $this->buildTplHtml($htmlDir, $tpl_path, array(
			'title'			=>	$title,
			'description'	=>	$description,
			'keywords'		=>	implode(',',$keyword_list),
			'data' => $data,
			'site'  =>  $site,
			'user' => $user,
			'profile'   =>  $userProfile
		));

		return $viewPath;
	}

}
