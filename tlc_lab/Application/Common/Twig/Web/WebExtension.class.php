<?php
namespace Common\Twig\Web;

use Common\Common\ServiceKernel;
use Topxia\WebBundle\Util\CategoryBuilder;
use Common\Lib\ArrayToolkit;
use Common\Lib\FileToolkit;
//use Topxia\Common\NumberToolkit;
use Common\Lib\ConvertIpToolkit;
//use Topxia\Service\Util\HTMLPurifierFactory;
use Common\Model\Util\HTMLPurifierFactory;
use Common\Twig\Util\UploadToken;
use Common\Twig\Util\TargetHelper;
use Common\Lib\WebCode;


class WebExtension extends \Twig_Extension {

	protected $container;
	protected $pageScripts;

	public function __construct($container) {
		$this->container = $container;
	}

	public function getFilters() {
		return array(
			'getWeek'                 => new \Twig_Filter_Method($this, 'weekdayFilter'),
			'smart_time'              => new \Twig_Filter_Method($this, 'smarttimeFilter'),
			'data_format'             => new \Twig_Filter_Method($this, 'dataformatFilter'),
			'time_range'              => new \Twig_Filter_Method($this, 'timeRangeFilter'),
			'remain_time'             => new \Twig_Filter_Method($this, 'remainTimeFilter'),
			'location_text'           => new \Twig_Filter_Method($this, 'locationTextFilter'),
			'tags_html'               => new \Twig_Filter_Method($this, 'tagsHtmlFilter', array('is_safe' => array('html'))),
			'file_size'               => new \Twig_Filter_Method($this, 'fileSizeFilter'),
			'plain_text'              => new \Twig_Filter_Method($this, 'plainTextFilter', array('is_safe' => array('html'))),
			'sub_text'                => new \Twig_Filter_Method($this, 'subTextFilter', array('is_safe' => array('html'))),
			'duration'                => new \Twig_Filter_Method($this, 'durationFilter'),
			'tags_join'               => new \Twig_Filter_Method($this, 'tagsJoinFilter'),
			'navigation_url'          => new \Twig_Filter_Method($this, 'navigationUrlFilter'),
			'chr'                     => new \Twig_Filter_Method($this, 'chrFilter'),
			'bbCode2Html'             => new \Twig_Filter_Method($this, 'bbCode2HtmlFilter'),
			'score_text'              => new \Twig_Filter_Method($this, 'scoreTextFilter'),
			'fill_question_stem_text' => new \Twig_Filter_Method($this, 'fillQuestionStemTextFilter'),
			'fill_question_stem_html' => new \Twig_Filter_Method($this, 'fillQuestionStemHtmlFilter'),
			'get_course_id'           => new \Twig_Filter_Method($this, 'getCourseidFilter'),
			'purify_html'             => new \Twig_Filter_Method($this, 'getPurifyHtml'),
			'file_type'               => new \Twig_Filter_Method($this, 'getFileType'),
			'at'                      => new \Twig_Filter_Method($this, 'atFilter'),
			'dump'                    => new \Twig_Filter_Method($this, 'dump'),
		);
	}

	public function getFunctions() {
		return array(
			'cloudPanUrl'			  => new \Twig_Filter_Method($this, 'cloudPanUrl'), #取得网盘地址
			'isTurnPrivate'                      => new \Twig_Function_Method($this, 'isTurnPrivate'),
			//'getBeginLive'                      => new \Twig_Function_Method($this, 'getBeginLive'),
			'md5Vcode'                          => new \Twig_Function_Method($this, 'md5Vcode'),
			'md5Comment'                        => new \Twig_Function_Method($this, 'md5Comment'),
			'theme_global_script'               => new \Twig_Function_Method($this, 'getThemeGlobalScript'),
			'html_decode'                       => new \Twig_Filter_Method($this, 'html_decode'),
			'file_uri_parse'                    => new \Twig_Function_Method($this, 'parseFileUri'),
			// file_path即将废弃，不要再使用
			'file_path'                         => new \Twig_Function_Method($this, 'getFilePath'),
			'default_path'                      => new \Twig_Function_Method($this, 'getDefaultPath'),
			'user_default_path'                 => new \Twig_Function_Method($this, 'getUserDefaultPath'),
			'system_default_path'               => new \Twig_Function_Method($this, 'getSystemDefaultPath'),
			'file_url'                          => new \Twig_Function_Method($this, 'getFileUrl'),
			'ceil'                              => new \Twig_Function_Method($this, 'ceil'),
			'object_load'                       => new \Twig_Function_Method($this, 'loadObject'),
			'urlencode'                         => new \Twig_Function_Method($this, 'urlencode'),
			'setting'                           => new \Twig_Function_Method($this, 'getSetting'),
			'set_price'                         => new \Twig_Function_Method($this, 'getSetPrice'),
			'categoryName'                      => new \Twig_Function_Method($this, 'getCategoryName'),
			'cateParentName'                    => new \Twig_Function_Method($this, 'getCateParentName'),
			'percent'                           => new \Twig_Function_Method($this, 'calculatePercent'),
			'getSchoolName'                     => new \Twig_Function_Method($this, 'getSchoolName'),
			'category_choices'                  => new \Twig_Function_Method($this, 'getCategoryChoices'),
			'icon_choices'                      => new \Twig_Function_Method($this, 'getIconChoices'),
			'dict'                              => new \Twig_Function_Method($this, 'getDict'),
			'getTopCate'						=> new \Twig_Function_Method($this, 'getTopCate'),
			'getCourseNumberList'						=> new \Twig_Function_Method($this, 'getCourseNumberList'),
			'getBread'                          => new \Twig_Function_Method($this, 'getBread'),
			'getCrumbs'                         => new \Twig_Function_Method($this, 'getCrumbs'),
			'year'                              => new \Twig_Function_Method($this, 'getYear'),
			'getWebDomain'                      => new \Twig_Function_Method($this, 'getWebDomain'),
			'dict_text'                         => new \Twig_Function_Method($this, 'getDictText', array('is_safe' => array('html'))),
			'upload_max_filesize'               => new \Twig_Function_Method($this, 'getUploadMaxFilesize'),
			'js_paths'                          => new \Twig_Function_Method($this, 'getJsPaths'),
			'is_plugin_installed'               => new \Twig_Function_Method($this, 'isPluginInstaled'),
			'plugin_version'                    => new \Twig_Function_Method($this, 'getPluginVersion'),
			'version_compare'                   => new \Twig_Function_Method($this, 'versionCompare'),
			'courseName'                        => new \Twig_Function_Method($this, 'getCourseName'),
			'is_exist_in_subarray_by_id'        => new \Twig_Function_Method($this, 'isExistInSubArrayById'),
			'context_value'                     => new \Twig_Function_Method($this, 'getContextValue'),
			'is_feature_enabled'                => new \Twig_Function_Method($this, 'isFeatureEnabled'),
			'parameter'                         => new \Twig_Function_Method($this, 'getParameter'),
			'upload_token'                      => new \Twig_Function_Method($this, 'makeUpoadToken'),
			'free_limit_type'                   => new \Twig_Function_Method($this, 'getFreeLimitType'),
			'countdown_time'                    => new \Twig_Function_Method($this, 'getCountdownTime'),
			'convertIP'                         => new \Twig_Function_Method($this, 'getConvertIP'),
			'isHide'                            => new \Twig_Function_Method($this, 'isHideThread'),
			'userOutCash'                       => new \Twig_Function_Method($this, 'getOutCash'),
			'userInCash'                        => new \Twig_Function_Method($this, 'getInCash'),
			'userAccount'                       => new \Twig_Function_Method($this, 'getAccount'),
			'getUserNickNameById'               => new \Twig_Function_Method($this, 'getUserNickNameById'),
			'getUserWebcode'                    => new \Twig_Function_Method($this, 'getUserWebcode'),
			'getWebNameByWebcode'               => new \Twig_Function_Method($this, 'getWebNameByWebcode'),
                        'getUserGold'                       => new \Twig_Function_Method($this, 'getUserGold'),
			'blur_phone_number'                 => new \Twig_Function_Method($this, 'blur_phone_number'),
			'sub_str'                           => new \Twig_Function_Method($this, 'subStr'),
			'load_script'                       => new \Twig_Function_Method($this, 'loadScript'),
			'export_scripts'                    => new \Twig_Function_Method($this, 'exportScripts'),
			'getClassroomsByCourseId'           => new \Twig_Function_Method($this, 'getClassroomsByCourseId'),
			'asset'                             => new \Twig_Function_Method($this, 'getAssetUrl'),
			'CateTreeHtml'                      => new \Twig_Function_Method($this, 'getCateTreeHtml'),
			'getCopyPeopleByMsgType'            => new \Twig_Function_Method($this, 'getCopyPeopleByMsgType'),
			'getClassListByClassIds'            => new \Twig_Function_Method($this, 'getClassListByClassIds'),
			'getTeacherListByTids'              => new \Twig_Function_Method($this, 'getTeacherListByTids'),
			'getUserNameById'                   => new \Twig_Function_Method($this, 'getUserNameById'),
			'getCategoryNameByUid'              => new \Twig_Function_Method($this, 'getCategoryNameByUid'),
			'getUserInfo'                       => new \Twig_Function_Method($this, 'getUserInfo'),
			'getUserApply'                      => new \Twig_Function_Method($this, 'getUserApply'),
			'getTwigExtendsStr'                 => new \Twig_Function_Method($this, 'getTwigExtendsStr'),
			'mbSubstrLen'                       => new \Twig_Function_Method($this, 'mbSubstrLen'),
			'getMessageContentById'             => new \Twig_Function_Method($this, 'getMessageContentById'),
			'getClassNameListByClassIds'        => new \Twig_Function_Method($this, 'getClassNameListByClassIds'),
			'getTeacherNameListByUid'           => new \Twig_Function_Method($this, 'getTeacherNameListByUid'),
			'getCopyNameListByUid'              => new \Twig_Function_Method($this, 'getCopyNameListByUid'),
			'explodeChannelStr'                 => new \Twig_Function_Method($this, 'explodeChannelStr'),
			//'isCenterWeb'                       => new \Twig_Function_Method($this, 'isLocalCenterWeb'),
			'getWebNameByCode'                  => new \Twig_Function_Method($this, 'getWebNameByCode'),
			'getSiteOptions'                    => new \Twig_Function_Method($this, 'getSiteOptions'),
			'isCloseUserWrite'                  => new \Twig_Function_Method($this, 'isCloseUserWrite'),
			'U'                                 => new \Twig_Function_Method($this, 'U'),
			'isOpenPublicCourse'                => new \Twig_Function_Method($this, 'isOpenPublicCourse'),
			'targets'                           => new \Twig_Function_Method($this, 'targets'),
			'in_array'                          => new \Twig_Function_Method($this, 'in_array'),
			'is_array'                          => new \Twig_Function_Method($this, 'is_array'),
			'can'                               => new \Twig_Function_Method($this, 'can'),
			'htmlspecialchars_decode'           => new \Twig_Function_Method($this, 'htmlspecialchars_decode'),
			'strip_tags'                        => new \Twig_Function_Method($this, 'strip_tags'),
			'intval'                            => new \Twig_Function_Method($this, 'intval'),
			'isCourseOpen'                      => new \Twig_Function_Method($this, 'isCourseOpen'),
			'isCourseFree'                      => new \Twig_Function_Method($this, 'isCourseFree'),
			'getVersion'                        => new \Twig_Function_Method($this, 'getVersion'),
			'isShowLogoSfx'                     => new \Twig_Function_Method($this, 'isShowLogoSfx'), #by qzw
//			'getSiteType'                       => new \Twig_Function_Method($this, 'getSiteType'),
//			'getStrucName'                      => new \Twig_Function_Method($this, 'getStrucName'),
			'getUrlByWebCode'                   => new \Twig_Function_Method($this, 'getUrlByWebCode'),
			'getCourseApplyId'                  => new \Twig_Function_Method($this, 'getCourseApplyId'),
			'publicCourseName'                  => new \Twig_Function_Method($this, 'publicCourseName'),
			'navbarPublicCourseName'            => new \Twig_Function_Method($this, 'navbarPublicCourseName'),
			'privateCourseName'                 => new \Twig_Function_Method($this, 'privateCourseName'),
			'canManageCourse'                   => new \Twig_Function_Method($this, 'canManageCourse'),
			'courseCategoryIds'                 => new \Twig_Function_Method($this, 'courseCategoryIds'),
			'courseCategoryNames'               => new \Twig_Function_Method($this, 'courseCategoryNames'),
			'categoryParents'                   => new \Twig_Function_Method($this, 'getCategoryParents'),
			'login_select_pic_path'             => new \Twig_Function_Method($this, 'loginSelectPicPath'),
			'login_default_pic_path'            => new \Twig_Function_Method($this, 'loginDefaultPicPath'),
			'login_pic_path'                    => new \Twig_Function_Method($this, 'loginPicPath'),
			'register_success_select_pic_path'  => new \Twig_Function_Method($this, 'registerSuccessSelectPicPath'),
			'register_success_default_pic_path' => new \Twig_Function_Method($this, 'registerSuccessDefaultPicPath'),
			'register_success_pic_path'         => new \Twig_Function_Method($this, 'registerSuccessPicPath'),
			'register_poster_select_pic_path'   => new \Twig_Function_Method($this, 'registerPosterSelectPicPath'),
			'register_poster_default_pic_path'  => new \Twig_Function_Method($this, 'registerPosterDefaultPicPath'),
			'register_poster_pic_path'          => new \Twig_Function_Method($this, 'registerPosterPicPath'),
			'getResourceType'                   => new \Twig_Function_Method($this, 'getResourceType'),
			'cosUrlEncode'                      => new \Twig_Function_Method($this, 'cosUrlEncode'),
			'panelClass'                      	=> new \Twig_Function_Method($this, 'panelClass'),
			'loadDefaultCoursePoster'			=> new \Twig_Function_Method($this, 'loadDefaultCoursePoster'),
			'get_my_vps'						=> new \Twig_Function_Method($this, 'getMyVps'),
			'get_course_category'				=> new \Twig_Function_Method($this, 'getCourseCategory'),
			'get_thread_count'					=> new \Twig_Function_Method($this, 'getThreadCount'),
			'C'									=> new \Twig_Function_Method($this, 'config'),
			'Const'								=> new \Twig_Function_Method($this, 'getConst'),
			'tripPath'							=> new \Twig_Function_Method($this, 'tripPath'),
		);
	}

        public function getUserGold($uid){
            return getUserGold($uid);
        }

		//获取配置
		public function config($param){
			return C($param);
		}


		//获取常亮
		public function getConst($name){
			switch ($name){
				case 'STATIC_KEY_BAIDU':
					return STATIC_KEY_BAIDU;
					break;
				case 'DATA_FETCH_URL_PREFIX':
					return DATA_FETCH_URL_PREFIX;
					break;
				default:
					return '';
			}
		}

		//过滤掉首位的静态地址获取前缀
		public function tripPath($path,$prefix=DATA_FETCH_URL_PREFIX){
			if(strpos($path,$prefix) === 0){
				return mb_substr($path,mb_strlen($prefix));
			}
			if(strpos($path,"/" . $prefix) === 0){
				return mb_substr($path,mb_strlen($prefix) + 1);
			}
			return $path;
		}

		//根据类型获取问答相关数量
		public function getThreadCount($type){
			$typeArr = array('newReply', 'myThread', 'myPost', 'myReply', 'myCollection');
			if(!in_array($type, $typeArr)){
				return 0;
			}
			
			return createService('Thread.ThreadService')->getThreadsCountByType(array(),$type);
		}

		//获取课程分类map
		public function getCourseCategory($courseId,$categoryId){
			$categoryService = createService('Taxonomy.CategoryService');
			$course_cateName = $categoryService->getNameById($categoryId);
			$cate_courseCode = $categoryService->getCourseCodeById($categoryId);

			$course_cateName = empty($course_cateName) ? "未选分类" : $course_cateName;
			$course_category = array('id'=>$courseId,'name'=>$course_cateName,'courseCode'=>$cate_courseCode);
			return $course_category;
		}

		//是否拥有VPS虚拟机
		public function getMyVps(){
			return createService('Course.VpsService')->getMyVps();
		}

		//获取默认课程封面图片列表
		public function loadDefaultCoursePoster(){
			return createService('Course.CourseService')->loadDefaultCoursePoster();
		}

		//获取网盘地址
		public function cloudPanUrl(){
			return PAN_URL;
		}

		//计算课程展示主页tab的class
		public function panelClass($type,$tab){
			if($type == $tab){
				return '';
			}
			return 'hide';
		}
        
        /**
         * 获取资料的类型
         * @author fubaosheng 2016-03-24
         */
        public function getResourceType($ext){
            if( in_array($ext,array('xls','xlsx')) )
                $type = "excel";
            elseif( in_array($ext,array('doc','docx')) )
                $type = "word";
            elseif( in_array($ext,array('pdf')) )
                $type = "pdf";
            elseif( in_array($ext,array('ppt','pptx')) )
                $type = "powerpoint";
            elseif( in_array($ext,array('jpg','jpeg','gif','png')) )
                $type = "image";
            elseif( in_array($ext,array('flv','avi','mp4','rm','rmvb')) )
                $type = "video";
            elseif( in_array($ext,array('mp3','wma')) )
                $type = "audio";
            elseif( in_array($ext,array('zip','rar','tar.gz')) )
                $type = "archive";
            else
                 $type = "";
            return $type;
        }

        /**
	 * 注册的广告图 用户选择的 不存在用默认0
	 * @author fubaosheng 2016-03-05
	 */
	public function registerPosterSelectPicPath($width = 0) {
		//获取用户上传的图片
		$registerConnect = createService('System.SettingServiceModel')->get('auth', array(), 'local', false);
		$pic             = $registerConnect["registerPosterBackgroundPic"] ?: "";
		//判断是否存在
		if ($pic) {
			$path = $_SERVER['DOCUMENT_ROOT'] . $pic;
			if (file_exists($path)) {
				if ($width > 0)
					return $pic . ".w{$width}.jpg";
				else
					return $pic;
			}
		}
		//不存在用默认 bg-pic.jpg
		$registerDefaultPic = C("REGISTER_POSTER_DEFAULT_PIC");
		if ($width > 0)
			return $registerDefaultPic["0"] . ".w{$width}.jpg";
		else
			return $registerDefaultPic["0"];
	}

	/**
	 * 注册成功的背景图 用户选择的 不存在用默认0
	 * @author fubaosheng 2016-03-05
	 */
	public function registerSuccessSelectPicPath($width = 0) {
		//获取用户上传的图片
		$registerConnect = createService('System.SettingServiceModel')->get('auth', array(), 'local', false);
		$pic             = $registerConnect["registerSuccessBackgroundPic"] ?: "";
		//判断是否存在
		if ($pic) {
			$path = $_SERVER['DOCUMENT_ROOT'] . $pic;
			if (file_exists($path)) {
				if ($width > 0)
					return $pic . ".w{$width}.jpg";
				else
					return $pic;
			}
		}
		//不存在用默认 bg-pic.jpg
		$registerDefaultPic = C("REGISTER_SUCCESS_DEFAULT_PIC");
		if ($width > 0)
			return $registerDefaultPic["0"] . ".w{$width}.jpg";
		else
			return $registerDefaultPic["0"];
	}

	/**
	 * 登录的背景图 用户选择的 不存在用默认
	 * @author fubaosheng 2016-03-03
	 */
	public function loginSelectPicPath($width = 0) {
		//获取用户上传的图片
		$loginConnect = createService('System.SettingServiceModel')->get('login_bind', array(), 'local', false);
		$pic          = $loginConnect["backgroundImg"] ?: "";
		//判断是否存在
		if ($pic) {
			$path = $_SERVER['DOCUMENT_ROOT'] . $pic;
			if (file_exists($path)) {
				if ($width > 0)
					return $pic . ".w{$width}.jpg";
				else
					return $pic;
			}
		}
		//不存在用默认 bg-pic.jpg
		$loginDefaultPic = C("LOGIN_DEFAULT_PIC");
		if ($width > 0)
			return $loginDefaultPic["5"] . ".w{$width}.jpg";
		else
			return $loginDefaultPic["5"];
	}

	public function isTurnPrivate($courseId, $userId,$webCode) {
		return createService('Center\Course.PublicCourseToPrivateService')->isTurnPrivate($courseId, $userId,$webCode);
	}

	/**
	 * 注册成功的背景图 默认的5张
	 * @author fubaosheng 2016-03-05
	 */
	public function registerSuccessDefaultPicPath($index, $width = 0) {
		$registerDefaultPic = C("REGISTER_SUCCESS_DEFAULT_PIC");
		if (in_array($index, array("1", "2", "3", "4", "5"))) {
			if ($width > 0)
				return $registerDefaultPic[$index] . ".w{$width}.jpg";
			else
				return $registerDefaultPic[$index];
		} else {
			if ($width > 0)
				return $registerDefaultPic["0"] . ".w{$width}.jpg";
			else
				return $registerDefaultPic["0"];
		}
	}

	/**
	 * 注册的广告图 默认的3张
	 * @author fubaosheng 2016-03-05
	 */
	public function registerPosterDefaultPicPath($index, $width = 0) {
		$registerDefaultPic = C("REGISTER_POSTER_DEFAULT_PIC");
		if (in_array($index, array("1", "2", "3"))) {
			if ($width > 0)
				return $registerDefaultPic[$index] . ".w{$width}.jpg";
			else
				return $registerDefaultPic[$index];
		} else {
			if ($width > 0)
				return $registerDefaultPic["0"] . ".w{$width}.jpg";
			else
				return $registerDefaultPic["0"];
		}
	}

	/**
	 * 登录的背景图 默认的5张
	 * @author fubaosheng 2016-03-03
	 */
	public function loginDefaultPicPath($index, $width = 0) {
		$loginDefaultPic = C("LOGIN_DEFAULT_PIC");
		if (in_array($index, array("1", "2", "3", "4", "5"))) {
			if ($width > 0)
				return $loginDefaultPic[$index] . ".w{$width}.jpg";
			else
				return $loginDefaultPic[$index];
		} else {
			if ($width > 0)
				return $loginDefaultPic["5"] . ".w{$width}.jpg";
			else
				return $loginDefaultPic["5"];
		}
	}

	/**
	 * 登录的背景图
	 * @author fubaosheng 2016-03-04
	 */
	public function loginPicPath() {
		$loginConnect = createService('System.SettingServiceModel')->get('login_bind', array());
		$index        = $loginConnect["backgroundImgIndex"] ?: 0;
		if (in_array($index, array("1", "2", "3", "4", "5"))) {
			return $this->loginDefaultPicPath($index);
		} else {
			return $this->loginSelectPicPath();
		}
	}

	/**
	 * 注册的广告图
	 * @author fubaosheng 2016-03-05
	 */
	public function registerPosterPicPath() {
		$registerConnect = createService('System.SettingServiceModel')->get('auth', array());
		$index           = $registerConnect["registerPosterBackgroundPicIndex"] ?: 0;
		if (in_array($index, array("1", "2", "3"))) {
			return $this->registerPosterDefaultPicPath($index);
		} else {
			return $this->registerPosterSelectPicPath();
		}
	}

	/**
	 * 注册成功的背景图
	 * @author fubaosheng 2016-03-04
	 */
	public function registerSuccessPicPath() {
		$registerConnect = createService('System.SettingServiceModel')->get('auth', array());
		$index           = $registerConnect["registerSuccessBackgroundPicIndex"] ?: 0;
		if (in_array($index, array("1", "2", "3", "4", "5"))) {
			return $this->registerSuccessDefaultPicPath($index);
		} else {
			return $this->registerSuccessSelectPicPath();
		}
	}

	/**
	 * 课程是否开放
	 * @param $id
	 * @return mixed
	 */
	public function isCourseOpen($id) {
		return createService('Course.CourseService')->isOpen($id);
	}

	/**
	 * 课程是否免费
	 * @param $id
	 * @return mixed
	 */
	public function isCourseFree($id) {
		return createService('Course.CourseService')->isFree($id);
	}

	public function intval($num) {
		return intval($num);
	}

	/**
	 * 获取指定webCode院校的名称
	 * $param 院校的wenCode
	 * @author yangjinlong 2015-07-08
	 */
	public function getWebNameByCode($param = '') {
		$webArr = C('ALL_WEB_CODE');
		if (!empty($param)) {
			if (array_key_exists($param, $webArr)) {
				return $webArr[$param]['webName'];
			}
		}
		return '';
	}

	public function getUrlByWebCode($webCode) {
		if (empty($webCode)) return '';
		return getScheme() . "://{$webCode}" . C("BASE_SITE_DOMAIN");
	}

	public function htmlspecialchars_decode($str) {
		return htmlspecialchars_decode($str);
	}

	public function strip_tags($str) {
		return strip_tags($str);
	}

	public function ceil($num) {
		return ceil($num);
	}

	public function in_array($str, $arr) {
		return in_array($str, $arr);
	}

	public function is_array($arr) {
		return is_array($arr);
	}

	public function urlencode($str) {
		return urlencode($str);
	}

	public function isOpenPublicCourse() {
		return isOpenPublicCourse();
	}

	/**
	 * 获取院校拉下列表
	 * $param  获取该下拉列表搜索时传的值
	 * @author yangjinlong 2015-07-08
	 */
	public function getSiteOptions($param = '') {
		$webArr = C('ALL_WEB_CODE');
		$html   = "<option value=''>--全部院校--</option>";
		foreach ($webArr as $k => $v) {
			if (!empty($param) && $param == $k) {
				$html .= "<option title=" . $v['webName'] . " selected='selected' value='" . $k . "'>" . $v['webName'] . "</option>";
			} else {
				$html .= "<option title=" . $v['webName'] . " value='" . $k . "'>" . $v['webName'] . "</option>";
			}
		}
		echo $html;
	}

	/**
	 * 解析targets，获取信息
	 * @author fubaosheng 2015-07-29
	 */
	public function targets($targets) {
		$target       = is_array($targets) ? $targets : array($targets);
		$targetHelper = new TargetHelper();
		$arr          = $targetHelper->getTargets($target);
		return $arr[$targets];
	}


	/**
	 * 字符串截取函数
	 * @param string $str
	 * @param type   $map
	 * @return string
	 */
	public function mbSubstrLen($str, $map) {
		if (mb_strlen($str) <= $map["len"]) {
			return $str;
		}

		$str = mb_substr($str, 0, $map["len"], "utf-8") . "...";
		return $str;
	}

	/**
	 * 根据用户id获取用户名
	 * @author ZhaoZuoWu 2015-05-13
	 */
	public function getUserNameById($uid) {
		if (empty($uid)) return "系统用户";
		$userInfo = $this->getUserService()->getUser($uid);
		return $userInfo["nickname"];

	}
        
        /**
         * 根据用户id获取用户的手机
         * @anthor zhuxu 20160307
         */
        public function getUserVerifiedMobile($uid){
            if (empty($uid)) return "";
		$userInfo = $this->getUserService()->getUser($uid);
		return $userInfo["verifiedMobile"];
        }
        
	/**
	 * 根据用户id获取系专业班级名
	 * @author tanhaitao 2015-10-16
	 */
	public function getCategoryNameByUid($uid, $type) {
		$info = $this->getGroupService()->getfacultyClassNameByUid($uid);
		if ($type == 'group') $name = $info["groupName"];
		if ($type == 'faculty') $name = $info["facultyName"];
		return $name ?: '';
	}

	/**
	 * 获取用户的webcode
	 * @author fubaosheng 2015-10-12
	 */
	public function getUserWebcode($account) {
		if (empty($account)) return '';
		$user = createService('User.UserModel')->findUserByEmail($account);
		if (empty($user)) {
			$user = createService('User.UserModel')->findUserByVerifiedMobile($account);
		}
		return $user['webCode'] ?: '';
	}

	/**
	 * 根据用户id用户信息
	 * @author wanglei 2015-08-20
	 */
	public function getUserInfo($uid, $field = array('*')) {
		$userInfo = createService('User.UserModel')->field($field)->find($uid);
		return $userInfo;

	}

	public function getUserService() {
		return createService("User.UserService");
	}

	public function getGroupService() {
		return createService('Group.GroupService');
	}

	public function getMessageService() {
		return createService("Message.MsgServiceModel");
	}

	/**
	 * 获取模板字符串
	 * @author  YangJinlong 2015-05-13
	 */
	public function getTwigExtendsStr($template, $defaultAliadName = "@Web") {
		if ($defaultAliadName == "@Admin") return "@Admin/" . trim($template, "/");

		$file = THEME_PATH . trim($template, '/');
		if (file_exists($file)) {
			return "@Web/" . trim($template, "/");
		} else {
			$file = "./Application/Home/View/" . trim($template, '/');
			return "@Home/" . trim($template, "/");
		}
	}

	public function explodeChannelStr($channelStr) {
		if (empty($channelStr)) return "";
		$list = explode(",", $channelStr);
		$html = "<table>";
		$html .= "<thead><tr><th style='color:#fff;'>设备列表</th></tr></thead>";
		$html .= "<tbody>";
		foreach ($list as $key => $value) {
			$html .= "<tr id='user-table-tr-' style='text-align:left;'>";
			$html .= "<td style='color:#fff;'>" . $value . "</td>";
			$html .= "</tr>";
		}
		$html .= "</tbody></table>";

		return $html;
	}

	/**
	 * 根据消息id获取消息内容
	 * @param type $task_id
	 * @return string
	 */
	public function getMessageContentById($task_id) {
		$messageRow = $this->getMessageService()->searchMessageById($task_id);
		if (empty($messageRow)) return "";
		$html = "<table>";
		$html .= "<thead><tr><th>标题</th><th>内容</th></tr></thead>";
		$html .= "<tbody>";
		$html .= "<tr id='user-table-tr-' style='text-align:left;'>";
		$html .= "<td>" . htmlspecialchars($messageRow['title']) . "</td>";
		$html .= "<td>" . htmlspecialchars($messageRow['content']) . "</td>";
		$html .= "</tr>";
		$html .= "</tbody></table>";
		return $html;
	}

	public function getTeacherNameListByUid($teacher_id) {
		return $teacher_id;
		$userInfo = createService("User.UserModel")->getUserInfoByCustom(array("field" => "nickname", "where" => "a.id in(" . $teacher_id . ")", 'limit' => 0));
		$nameArr  = ArrayToolkit::column($userInfo, "nickname");
		if (empty($nameArr)) return "";
		return implode(",", $nameArr);
	}

	/**
	 * 根据teacher_id字符串获取老师列表
	 * @param type $teacher_id
	 * @return string
	 */
	public function getTeacherListByTids($teacher_id) {
		$userInfo = createService("User.UserModel")->getUserInfoByCustom(array("field" => "nickname", "where" => "a.id in(" . $teacher_id . ")", 'limit' => 0));
		$nameArr  = ArrayToolkit::column($userInfo, "nickname");
		if (empty($nameArr)) return "";
		//$nameStr = implode(",", $nameArr);
		$html = "<table>";
		$html .= "<thead><tr><th style='color:#fff;'>老师列表</th></tr></thead>";
		$html .= "<tbody>";
		foreach ($nameArr as $key => $value) {
			$html .= "<tr id='user-table-tr-' style='text-align:left;'>";
			$html .= "<td style='color:#fff;'>" . $value . "</td>";
			$html .= "</tr>";
		}
		$html .= "</tbody></table>";

		return $html;
	}

	/**
	 * 根据班级id获取班级名称
	 * @param type $class_id
	 * @return type
	 */
	public function getClassNameListByClassIds($class_id) {
		$groupList    = createService("Group.GroupServiceModel")->getGroupsByIds($class_id);
		$arr          = ArrayToolkit::column($groupList, "title");
		$classNameStr = implode(",", $arr);
		return $classNameStr;
	}

	/**
	 * 根据班级id获取
	 * @param type $class_id
	 * @return string
	 */
	public function getClassListByClassIds($class_id) {
		$groupList = createService("Group.GroupServiceModel")->getGroupsByIds($class_id);
		$arr       = ArrayToolkit::column($groupList, "title");
		if (empty($arr)) return "";
		//$nameStr = implode(",", $nameArr);
		$html = "<table>";
		$html .= "<thead><tr><th style='color:#fff;'>班级列表</th></tr></thead>";
		$html .= "<tbody>";
		foreach ($arr as $key => $value) {
			$html .= "<tr id='user-table-tr-' style='text-align:left;'>";
			$html .= "<td style='color:#fff;'>" . $value . "</td>";
			$html .= "</tr>";
		}
		$html .= "</tbody></table>";

		return $html;
	}

	public function getCopyNameListByUid($copy_ids, $msgtype) {
		switch ($msgtype["msgtype"]) {
			case 1:
				$field = "a.verifiedMobile as name";
				break;
			case 2:
				$field = "a.email as name";
				break;
			case 3:
				$field = "a.nickname as name";
				break;
			case 4:
				$field = "a.nickname as name";
				break;
			default:
				$field = "a.nickname as name";
				break;
		}
		$userInfo = createService("User.UserModel")->getUserInfoByCustom(array("field" => $field, "where" => "a.id in(" . $copy_ids . ")", 'limit' => 0));
		$nameArr  = ArrayToolkit::column($userInfo, "name");
		$nameStr  = implode(",", $nameArr);
		return $nameStr;
	}

	public function getCopyPeopleByMsgType($copy_ids, $msgtype) {
		switch ($msgtype["msgtype"]) {
			case 1:
				$field = "a.verifiedMobile as name";
				break;
			case 2:
				$field = "a.email as name";
				break;
			case 3:
				$field = "a.nickname as name";
				break;
			case 4:
				$field = "a.nickname as name";
				break;
			default:
				$field = "a.nickname as name";
				break;
		}
		$userInfo = createService("User.UserModel")->getUserInfoByCustom(array("field" => $field, "where" => "a.id in(" . $copy_ids . ")", 'limit' => 0));
		$nameArr  = ArrayToolkit::column($userInfo, "name");
		if (empty($nameArr)) return "";
		//$nameStr = implode(",", $nameArr);
		$html = "<table>";
		$html .= "<thead><tr><th style='color:#fff;'>抄送人列表</th></tr></thead>";
		$html .= "<tbody>";
		foreach ($nameArr as $key => $value) {
			$html .= "<tr id='user-table-tr-' style='text-align:left;'>";
			$html .= "<td style='color:#fff;'>" . $value . "</td>";
			$html .= "</tr>";
		}
		$html .= "</tbody></table>";

		return $html;
	}

	public function getAssetUrl($path, $packageName = null) {
		$str = substr($path, 0, 7);
		if ($str == 'Public/') {
			return "/" . $path;
		}
		return "/Public/" . $path;
		// return $this->container->get('templating.helper.assets')->getUrl($path, $packageName);
	}

	/**
	 * Minify the query
	 * @param string $query
	 * @return string
	 */
	public function minifyQuery($query) {
		$result   = '';
		$keywords = array();
		$required = 1;

		// Check if we can match the query against any of the major types
		switch (true) {
			case stripos($query, 'SELECT') !== false:
				$keywords = array('SELECT', 'FROM', 'WHERE', 'HAVING', 'ORDER BY', 'LIMIT');
				$required = 2;
				break;

			case stripos($query, 'DELETE') !== false :
				$keywords = array('DELETE', 'FROM', 'WHERE', 'ORDER BY', 'LIMIT');
				$required = 2;
				break;

			case stripos($query, 'UPDATE') !== false :
				$keywords = array('UPDATE', 'SET', 'WHERE', 'ORDER BY', 'LIMIT');
				$required = 2;
				break;

			case stripos($query, 'INSERT') !== false :
				$keywords = array('INSERT', 'INTO', 'VALUE', 'VALUES');
				$required = 2;
				break;

			// If there's no match so far just truncate it to the maximum allowed by the interface
			default:
				$result = substr($query, 0, $this->maxCharWidth);
		}

		// If we had a match then we should minify it
		if ($result == '') {
			$result = $this->composeMiniQuery($query, $keywords, $required);
		}

		// Remove unneeded boilerplate HTML
		$result = str_replace(array("<pre style='background:white;'", "</pre>"), array("<span", "</span>"), $result);

		return $result;
	}

	public function subStr($text, $start, $length) {
		$text = trim($text);

		$length = (int)$length;
		if (($length > 0) && (mb_strlen($text) > $length)) {
			$text = mb_substr($text, $start, $length, 'UTF-8');
		}
		return $text;
	}

	public function getOutCash($userId, $timeType = "oneWeek") {
		$time      = $this->filterTime($timeType);
		$condition = array(
			'userId'    => $userId,
			'type'      => "outflow",
			'cashType'  => 'Coin',
			'startTime' => $time,
		);

//        return ServiceKernel::instance()->createService('Cash.CashService')->analysisAmount($condition);
		return createService('Cash.CashServiceModel')->analysisAmount($condition);
	}

	public function getInCash($userId, $timeType = "oneWeek") {
		$time      = $this->filterTime($timeType);
		$condition = array(
			'userId'    => $userId,
			'type'      => "inflow",
			'cashType'  => 'Coin',
			'startTime' => $time,
		);
//        return ServiceKernel::instance()->createService('Cash.CashService')->analysisAmount($condition);
		return createService('Cash.CashServiceModel')->analysisAmount($condition);
	}

	public function getAccount($userId) {
//        return ServiceKernel::instance()->createService('Cash.CashAccountServiceModel')->getAccountByUserId($userId);
		return createService('Cash.CashAccountServiceModel')->getAccountByUserId($userId);
	}

	private function filterTime($type) {
		$time = 0;
		switch ($type) {
			case 'oneWeek':
				$time = time() - 7 * 3600 * 24;
				break;
			case 'oneMonth':
				$time = time() - 30 * 3600 * 24;
				break;
			case 'threeMonths':
				$time = time() - 90 * 3600 * 24;
				break;
			default:
				break;
		}

		return $time;
	}

	public function getUserNickNameById($userId) {
		$user = $this->getUserById($userId);
		return $user['nickname'];
	}

	public function getClassroomsByCourseId($courseId) {
		$classrooms   = array();
		$classroomIds = ArrayToolkit::column(createService('Classroom.ClassroomService')->findClassroomsByCourseId($courseId), 'classroomId');
		foreach ($classroomIds as $key => $value) {
			$classrooms[$value] = createService('Classroom.ClassroomService')->getClassroom($value);
		}

		return $classrooms;
	}

	private function getUserById($userId) {
		return createService('User.UserService')->getUser($userId);
	}

	public function isExistInSubArrayById($currentTarget, $targetArray) {
		foreach ($targetArray as $target) {
			if ($currentTarget['id'] == $target['id']) {
				return true;
			}
		}
		return false;
	}

	public function getThemeGlobalScript() {
		/**
		 * @author ZhaoZuoWu 2015-03-27
		 */
		$theme = $this->getSetting('theme.uri', 'default');
		//$filePath = realpath($this->container->getParameter('kernel.root_dir') . "/../web/themes/{$theme}/js/global-script.js");
		$filePath = realpath(getParameter('kernel.root_dir') . "/../web/themes/{$theme}/js/global-script.js");
		if ($filePath) {
			return 'theme/global-script';
		}
		return '';
	}

	public function isPluginInstaled($name) {

		/**
		 * @author ZhaoZuoWu 2015-03-2
		 */
		//$plugins = $this->container->get('kernel')->getPlugins();
		$plugins = getPlugins();
		foreach ($plugins as $plugin) {
			if (is_array($plugin)) {
				if (strtolower($name) == strtolower($plugin['code'])) {
					return true;
				}
			} else {
				if (strtolower($name) == strtolower($plugin)) {
					return true;
				}
			}
		}
		return false;
	}

	public function getPluginVersion($name) {
		$plugins = $this->container->get('kernel')->getPlugins();
		foreach ($plugins as $plugin) {
			if (strtolower($plugin['code']) == strtolower($name)) {
				return $plugin['version'];
			}
		}
		return null;
	}

	public function versionCompare($version1, $version2, $operator) {
		return version_compare($version1, $version2, $operator);
	}

	public function getJsPaths() {
		/**
		 * @author ZhaoZuoWu 2015-03-27
		 */
		$request  = getRequest();
		$basePath = $request->getBasePath();
		$theme    = $this->getSetting('theme.uri', 'default');

		$plugins = getPlugins();
		$names   = array();
		foreach ($plugins as $plugin) {
			if (is_array($plugin)) {
				if ($plugin['type'] != 'plugin') {
					continue;
				}
				$names[] = $plugin['code'];
			} else {
				$names[] = $plugin;
			}
		}

		$paths = array(
			'common' => 'common',
			'theme'  => "{$basePath}/themes/{$theme}/js"
		);

		foreach ($names as $name) {
			$name                   = strtolower($name);
			$paths["{$name}bundle"] = "{$basePath}/bundles/{$name}/js";
		}

		return $paths;
	}

	public function getContextValue($context, $key) {
		$keys  = explode('.', $key);
		$value = $context;
		foreach ($keys as $key) {
			if (!isset($value[$key])) {
				throw new \InvalidArgumentException(sprintf("Key `%s` is not in context with %s", $key, implode(array_keys($context), ', ')));
			}
			$value = $value[$key];
		}

		return $value;
	}

	public function isFeatureEnabled($feature) {
		$features = getParameter('enabled_features') ? getParameter('enabled_features') : array();
		#$features = $this->container->hasParameter('enabled_features') ? $this->container->getParameter('enabled_features') : array();
		return in_array($feature, $features);
	}

	public function getParameter($name, $default = null) {
		/**
		 * @author ZhaoZuoWu 2015-03-27
		 */
		if (!getParameter($name)) {
			return $default;
		}
		return getParameter($name);
	}

	public function makeUpoadToken($group, $type = 'image', $duration = 18000) {
		$maker = new UploadToken();
		return $maker->make($group, $type, $duration);
	}

	public function getConvertIP($IP) {

		if (!empty($IP)) {
			$location = ConvertIpToolkit::convertIp($IP);
			if ($location === 'INNA')
				return '未知区域';
			return $location;
		}
		return '';
	}

	public function dataformatFilter($time) {
		if (empty($time)) {
			return;
		}
		return date('Y-m-d H:i', $time);
	}

	//郭俊强 2015-09-06  页面打印
	public function dump($array) {
		if (empty($array)) {
			return;
		}
		return var_dump($array);
	}

	//tanhaitao 2015-10-19  html转义回来
	public function html_decode($str) {
		return htmlspecialchars_decode($str);
	}

	public function smarttimeFilter($time) {
		$diff = time() - $time;
		if ($diff < 0) {
			return '未来';
		}

		if ($diff == 0) {
			return '刚刚';
		}

		if ($diff < 60) {
			return $diff . '秒前';
		}

		if ($diff < 3600) {
			return round($diff / 60) . '分钟前';
		}

		if ($diff < 86400) {
			return round($diff / 3600) . '小时前';
		}

		if ($diff < 2592000) {
			return round($diff / 86400) . '天前';
		}

		if ($diff < 31536000) {
			return date('m-d', $time);
		}

		return date('Y-m-d', $time);
	}

	public function remainTimeFilter($value) {
		$remain = $value - time();

		if ($remain <= 0) {
			return '0分钟';
		}

		if ($remain <= 3600) {
			return round($remain / 60) . '分钟';
		}

		if ($remain < 86400) {
			return round($remain / 3600) . '小时';
		}

		return round($remain / 86400) . '天';
	}

	public function getCountdownTime($value) {
		$countdown = array('days' => 0, 'hours' => 0, 'minutes' => 0, 'seconds' => 0);

		$remain = $value - time();
		if ($remain <= 0) {
			return $countdown;
		}

		$countdown['days'] = intval($remain / 86400);
		$remain            = $remain - 86400 * $countdown['days'];

		$countdown['hours'] = intval($remain / 3600);
		$remain             = $remain - 3600 * $countdown['hours'];

		$countdown['minutes'] = intval($remain / 60);
		$remain               = $remain - 60 * $countdown['minutes'];

		$countdown['seconds'] = $remain;

		return $countdown;
	}

	public function durationFilter($value) {
		$minutes = intval($value / 60);
		$seconds = $value - $minutes * 60;
		return sprintf('%02d', $minutes) . ':' . sprintf('%02d', $seconds);
	}

	public function timeRangeFilter($start, $end) {
		$range = date('Y年n月d日 H:i', $start) . ' - ';

		if ($this->container->get('redcloud.timemachine')->inSameDay($start, $end)) {
			$range .= date('H:i', $end);
		} else {
			$range .= date('Y年n月d日 H:i', $end);
		}

		return $range;
	}

        public  function cosUrlEncode($path) {
            $path = rawurlencode($path);
            return str_replace('%2F', '/', $path);
        }
      
	public function tagsJoinFilter($tagIds) {
		if (empty($tagIds) or !is_array($tagIds)) {
			return '';
		}

		$tags  = createService('Taxonomy.TagService')->findTagsByIds($tagIds);
		$names = ArrayToolkit::column($tags, 'name');

		return join($names, ',');
	}

	public function navigationUrlFilter($url) {
		$url = (string)$url;
		if (strpos($url, '://')) {
			return $url;
		}

		if (!empty($url[0]) and ($url[0] == '/')) {
			return $url;
		}

		return getRequest()->getBaseUrl() . '/' . $url;

	}

	/**
	 * @param  [type] $districeId [description]
	 * @param  string $format     格式，默认格式'P C D'。
	 *                            P -> 省全称,     p -> 省简称
	 *                            C -> 城市全称,    c -> 城市简称
	 *                            D -> 区全称,     d -> 区简称
	 * @return [type]             [description]
	 */
	public function locationTextFilter($districeId, $format = 'P C D') {
		$text  = '';
		$names = createService('Taxonomy.LocationService')->getLocationFullName($districeId);


		$len = strlen($format);
		for ($i = 0; $i < $len; $i++) {
			switch ($format[$i]) {
				case 'P':
					$text .= $names['province'];
					break;

				case 'p':
					$text .= $this->mb_trim($names['province'], '省');
					break;

				case 'C':
					$text .= $names['city'];
					break;

				case 'c':
					$text .= $this->mb_trim($names['city'], '市');
					break;

				case 'D':
				case 'd':
					$text .= $names['district'];
					break;

				default:
					$text .= $format[$i];
					break;
			}
		}

		return $text;
	}

	public function tagsHtmlFilter($tags, $class = '') {
		$links = array();
		$tags  = createService('Taxonomy.TagService')->findTagsByIds($tags);
		foreach ($tags as $tag) {
			$url     = $this->container->get('router')->generate('course_explore', array('tagId' => $tag['id']));
			$links[] = "<a href=\"{$url}\" class=\"{$class}\">{$tag['name']}</a>";
		}
		return implode(' ', $links);
	}

	public function parseFileUri($uri) {
		return D('Content\FileService')->parseFileUri($uri);
	}

	public function getFilePath($uri, $default = '', $absolute = false) {
//        $assets = $this->container->get('templating.helper.assets');
//        $request = $this->container->get('request');
		$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
		$assets  = new \Symfony\Component\Templating\Helper\CoreAssetsHelper(new \Symfony\Bundle\FrameworkBundle\Templating\Asset\PathPackage($request, '5.1.4', '%s?%s'), array());
		#tanhaitao 2015-09-21 add
		if (!empty($uri) && in_array(intval($uri), C('COURSE_DEFAULT_PIC_IDX'), TRUE)) {
			$course_pic = C('COURSE_DEFAULT_PIC');
			$url        = $assets->getUrl($course_pic[intval($uri)]);
			return $url;
		}
		if (empty($uri)) {
			$url = $this->getAssetUrl('assets/img/default/' . $default);
			//$url = $assets->getUrl('assets/img/default/' . $default);
			//$url = $request->getBaseUrl() . '/assets/img/default/' . $default;
			if ($absolute) {
				$url = $request->getSchemeAndHttpHost() . $url;
			}
			return $url;
		}
		$uri = $this->parseFileUri($uri);
		if ($uri['access'] == 'public') {
			$url = rtrim(getParameter('redcloud.upload.public_url_path'), ' /') . '/' . $uri['path'];
			$url = ltrim($url, ' /');
			$url = $assets->getUrl($url);

			if ($absolute) {
				$url = $request->getSchemeAndHttpHost() . $url;
			}

			return $url;
		} else {

		}
	}

	public static function userDefaultPath($uid, $type = 'middle', $absolute = false){
		switch ($type) {
			case 'small':
				$size = 'smallAvatar';
				break;
			case 'large':
				$size = 'largeAvatar';
				break;
			case 'big':
				$size = 'largeAvatar';
				break;
			case 'middle':
			default:
				$type = 'middle';
				$size = 'mediumAvatar';
		}
		$uBaseInfo = self::getUserService()->getUser($uid);
		if (empty($uBaseInfo[$size])) {
			static $urlArr = array();
			$str = $uid . "_" . $type;
			if (isset($urlArr[$str])) return $urlArr[$str];

			$request  = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
			$assets   = new \Symfony\Component\Templating\Helper\CoreAssetsHelper(new \Symfony\Bundle\FrameworkBundle\Templating\Asset\PathPackage($request, '5.1.4', '%s?%s'), array());
			$user_pic = C('USER_DEFAULT_PIC');
			$roles    = $uBaseInfo['roles'];
			$roles    = array_values(array_filter($roles));

			$profile = self::getUserService()->getUserProfile($uid);
			$gender  = $profile['gender'];

			if ($gender == 'male') {
				$url = $user_pic['STUDENT_MALE'];
				if (in_array('ROLE_TEACHER', $roles)) $url = $user_pic['TEACHER_MALE'];
			}

			if ($gender == 'female') {
				$url = $user_pic['STUDENT_FEMALE'];
				if (in_array('ROLE_TEACHER', $roles)) $url = $user_pic['TEACHER_FEMALE'];
			}

			if ($absolute) $url = $request->getSchemeAndHttpHost() . $url;
			if (empty($url)) $url = $user_pic['STUDENT_MALE'];
			$publicUrlpath = '/Public/assets/img/default/';
			$url           = $assets->getUrl($publicUrlpath . $type . $url);
			$urlArr[$str]  = $url;
			return $urlArr[$str];
		}
		return self::getDefaultPath('avatar', $uBaseInfo[$size], '', $absolute = false);
	}

	/**
	 * 获取用户头像
	 * $type : small 小头像, middle:中头像, large:大头像 ，big:大头像
	 * @param        $uid
	 * @param string $type
	 * @return mixed
	 * @author tanhaitao 2015-09-25
	 */
	function getUserDefaultPath($uid, $type = 'middle', $absolute = false) {

		switch ($type) {
			case 'small':
				$size = 'smallAvatar';
				break;
			case 'large':
				$size = 'largeAvatar';
				break;
			case 'big':
				$size = 'largeAvatar';
				break;
			case 'middle':
			default:
				$type = 'middle';
				$size = 'mediumAvatar';
		}
		$uBaseInfo = $this->getUserService()->getUser($uid);
		if (empty($uBaseInfo[$size])) {
			static $urlArr = array();
			$str = $uid . "_" . $type;
			if (isset($urlArr[$str])) return $urlArr[$str];

			$request  = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
			$assets   = new \Symfony\Component\Templating\Helper\CoreAssetsHelper(new \Symfony\Bundle\FrameworkBundle\Templating\Asset\PathPackage($request, '5.1.4', '%s?%s'), array());
			$user_pic = C('USER_DEFAULT_PIC');
			$roles    = $uBaseInfo['roles'];
			$roles    = array_values(array_filter($roles));

			$profile = $this->getUserService()->getUserProfileSimple($uid);
			$gender  = $profile['gender'];

			if ($gender == 'male') {
				$url = $user_pic['STUDENT_MALE'];
				if (in_array('ROLE_TEACHER', $roles)) $url = $user_pic['TEACHER_MALE'];
			}

			if ($gender == 'female') {
				$url = $user_pic['STUDENT_FEMALE'];
				if (in_array('ROLE_TEACHER', $roles)) $url = $user_pic['TEACHER_FEMALE'];
			}

			if ($absolute) $url = $request->getSchemeAndHttpHost() . $url;
			if (empty($url)) $url = $user_pic['STUDENT_MALE'];
			$publicUrlpath = '/Public/assets/img/default/';
			$url           = $assets->getUrl($publicUrlpath . $type . $url);
			$urlArr[$str]  = $url;
			return $urlArr[$str];
		}
		return $this->getDefaultPath('avatar', $uBaseInfo[$size], '', $absolute = false);
	}

	public function getDefaultPath($category, $uri = "", $size = '', $absolute = false) {

		$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
		$assets  = new \Symfony\Component\Templating\Helper\CoreAssetsHelper(new \Symfony\Bundle\FrameworkBundle\Templating\Asset\PathPackage($request, '5.1.4', '%s?%s'), array());

		$cdn    = createService('System.SettingService')->get('cdn', array());
		$cdnUrl = (empty($cdn['enabled'])) ? '' : rtrim($cdn['url'], " \/");

		#tanhaitao 2015-09-21 add
		if (!empty($uri) && !strstr($uri, "://")) {
			return $uri;
		}

		#LiangFuJian 2015-08-12 modified
		if (empty($uri) || !strstr($uri, "://")) {
			$publicUrlpath = '/Public/assets/img/default/';
			$url           = $assets->getUrl($publicUrlpath . $size . $category);

			$defaultSetting = createService('System.SettingServiceModel')->get('default', array());

			$key      = 'default' . ucfirst($category);
			$fileName = $key . 'FileName';
			if (array_key_exists($key, $defaultSetting) && array_key_exists($fileName, $defaultSetting)) {
				if ($defaultSetting[$key] == 1) {
					$url = $assets->getUrl($publicUrlpath . $size . $defaultSetting[$fileName]);
					return $url;
				} else {
					if ($absolute) {
						$url = $request->getSchemeAndHttpHost() . $url;
					}
					return $url;
				}
			} else {
				return $url;
			}
		}

		$uri = $this->parseFileUri($uri);
		if ($uri['access'] == 'public') {

			$url = rtrim(getParameter('redcloud.upload.public_url_path'), ' /') . '/' . $uri['path'];
			$url = ltrim($url, ' /');
			$url = $assets->getUrl($url);

			if ($cdnUrl) {
				$url = $cdnUrl . $url;
			} else {
				if ($absolute) {
					$url = $request->getSchemeAndHttpHost() . $url;
				}
			}

			return $url;
		} else {

		}
	}

	/**
	 * 系统默认(头像、课程等)图片路径
	 * @param      $category
	 * @param bool $systemDefault
	 * @return string
	 */
	public function getSystemDefaultPath($category, $systemDefault = false) {
		//$assets = $this->container->get('templating.helper.assets');
		$publicUrlpath = '/Public/assets/img/default/';

		$defaultSetting = D('System\SettingService')->get('default', array());

		if ($systemDefault && isset($defaultSetting)) {
			$fileName = 'default' . ucfirst($category) . 'FileName';
			if (array_key_exists($fileName, $defaultSetting)) {
				$url = $publicUrlpath . $defaultSetting[$fileName];
			} else {
				$url = $publicUrlpath . $category;
			}
		} else {
			$url = $publicUrlpath . $category;
		}

		return $url;
	}

	public function loadScript($js) {
		$js = is_array($js) ? $js : array($js);

		if ($this->pageScripts) {
			$this->pageScripts = array_merge($this->pageScripts, $js);
		} else {
			$this->pageScripts = $js;
		}
	}

	public function exportScripts() {
		return $this->pageScripts;
	}

	/**
	 * 文件上传路径
	 * @param        $uri
	 * @param string $default
	 * @param bool   $absolute
	 * @return string
	 */
	public function getFileUrl($uri, $default = '', $absolute = false) {
		//$assets = $this->container->get('templating.helper.assets');
		// $request = $this->container->get('request');

		if (empty($uri)) {
			$url = '/Public/assets/img/default/' . $default;
			if ($absolute) {
				$url = $_SERVER['HTTP_HOST'] . $url;
			}
			return $url;
		}

		$url = rtrim(getParameter('redcloud.upload.public_url_path'), ' /') . '/' . $uri;

		//$url = $assets->getUrl($url);

		if ($absolute) {
			$url = $_SERVER['HTTP_HOST'] . $url;
		}

		return $url;
	}

	public function fileSizeFilter($size) {
		$currentValue = $currentUnit = null;
		$unitExps     = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3);
		foreach ($unitExps as $unit => $exp) {
			$divisor      = pow(1000, $exp);
			$currentUnit  = $unit;
			$currentValue = $size / $divisor;
			if ($currentValue < 1000) {
				break;
			}
		}

		return sprintf('%.1f', $currentValue) . $currentUnit;
	}

	public function loadObject($type, $id) {
//		$kernel = ServiceKernel::instance();
		switch ($type) {
			case 'user':
				return createService('User.UserService')->getUser($id);
			case 'category':
				return createService('Taxonomy.CategoryService')->getCategory($id);
			case 'course':
				return createService('Course.CourseService')->getCourse($id);
			case 'file_group':
				return createService('Content.FileService')->getFileGroup($id);
			default:
				return null;
		}
	}

	public function plainTextFilter($text, $length = null, $htmlspecialchars = false) {
		$text = strip_tags($text);
		$text = str_replace(array("\n", "\r", "\t"), '', $text);
		$text = str_replace('&nbsp;', ' ', $text);
		$text = trim($text);
		if ($htmlspecialchars) {
			$text = htmlspecialchars_decode($text);
			if (($length > 0) && (mb_strlen($text, 'UTF-8') > $length)) {
				$text = mb_substr($text, 0, $length, 'UTF-8');
				$text = htmlspecialchars($text);
				$text .= '...';
			}
		} else {
			if (($length > 0) && (mb_strlen($text, 'UTF-8') > $length)) {
				$text = mb_substr($text, 0, $length, 'UTF-8');
				$text .= '...';
			}
		}
		return $text;
	}

	public function subTextFilter($text, $length = null) {
		$text = strip_tags($text);

		$text = str_replace(array("\n", "\r", "\t"), '', $text);
		$text = str_replace('&nbsp;', ' ', $text);
		$text = trim($text);

		$length = (int)$length;
		if (($length > 0) && (mb_strlen($text, 'utf-8') > $length)) {
			$text = mb_substr($text, 0, $length, 'UTF-8');
			$text .= '...';
		}

		return $text;
	}

	public function getFileType($fileName, $string = null) {
		$fileName = explode(".", $fileName);

		$name = strtolower($fileName[1]);
		if ($string)
			$name = strtolower($fileName[1]) . $string;

		return $name;
	}

	public function chrFilter($index) {
		return chr($index);
	}

	public function isHideThread($id) {
		$need = createService('Group.ThreadService')->sumGoodsCoinsByThreadId($id);

		$thread = createService('Group.ThreadService')->getThread($id);

		$data = explode('[/hide]', $thread['content']);
		foreach ($data as $key => $value) {

			$value = " " . $value;
			sscanf($value, "%[^[][hide=reply]%[^$$]", $replyContent, $replyHideContent);
			if ($replyHideContent)
				return true;
		}

		if ($need)
			return true;

		return false;
	}

	/**
	 * @param string   $bbCode
	 * @param Booleans $setImgAttr 设置显示图片的属性值
	 * @return string
	 */
	public function bbCode2HtmlFilter($bbCode, $setImgAttr = false) {
		$ext    = $this;
		$arr    = array(
			'ext'        => $ext,
			'setImgAttr' => $setImgAttr,
		);
		$bbCode = preg_replace_callback('/\[image\](.*?)\[\/image\]/i', function ($matches) use ($arr) {
			$src = $arr['ext']->getFileUrl($matches[1]);
			$url = C('SITE_URL');
			if ($arr['setImgAttr']) {
				return "<br/><img width='200' height='150' src='{$url}{$src}' /><br/>";
			} else {
				return "<br/><img src='{$url}{$src}' /><br/>";
			}
		}, $bbCode);
		$bbCode = preg_replace_callback('/\[audio.*?id="(\d+)"\](.*?)\[\/audio\]/i', function ($matches) {
			return "<span class='audio-play-trigger' href='javascript:;' data-file-id=\"{$matches[1]}\" data-file-type=\"audio\"></span>";
		}, $bbCode);

		return $bbCode;
	}

	public function scoreTextFilter($text) {
		$text = number_format($text, 1, '.', '');

		if (intval($text) == $text) {
			return (string)intval($text);
		}
		return $text;
	}

	public function fillQuestionStemTextFilter($stem) {
		return preg_replace('/\[\[.+?\]\]/', '____', $stem);
	}

	public function fillQuestionStemHtmlFilter($stem) {
		$index = 0;
		$stem  = preg_replace_callback('/\[\[.+?\]\]/', function ($matches) use (&$index) {
			$index++;
			return "<span class='question-stem-fill-blank'>({$index})</span>";
		}, $stem);
		return $stem;
	}

	public function getCourseidFilter($target) {
		$target = explode('/', $target);
		$target = explode('-', $target[0]);
		return $target[1];
	}

	public function getPurifyHtml($html, $trusted = false) {
		if (empty($html)) {
			return '';
		}

		$config = array(
			'cacheDir' => getParameter('kernel.cache_dir') . '/htmlpurifier'
		);

		$factory  = new HTMLPurifierFactory($config);
		$purifier = $factory->create($trusted);

		return $purifier->purify($html);
	}

	public function atFilter($text, $ats = array()) {
		if (empty($ats) || !is_array($ats)) {
			return $text;
		}

		$router = $this->container->get('router');

		foreach ($ats as $nickname => $userId) {
			$path = $router->generate('user_show', array('id' => $userId));
			$html = "<a href=\"{$path}\" data-uid=\"{$userId}\" target=\"_blank\">@{$nickname}</a>";

			$text = preg_replace("/@{$nickname}/ui", $html, $text);
		}

		return $text;
	}

	public function getSetting($name, $default = '', $webCode = '') {
		/*DEBUG
		 */
		$t     = $name;
		$names = explode('.', $name);

		$name = array_shift($names);
		if (empty($name)) {
			return $default;
		}
		$value   = createService('System.SettingServiceModel')->get($name, null);
		if (!isset($value)) {
			return $default;
		}

		if (empty($names)) {
			return $value;
		}

		$result = $value;
		foreach ($names as $name) {
			if (!isset($result[$name])) {
				return $default;
			}
			$result = $result[$name];
		}
		return $result;
	}

	public function calculatePercent($number, $total) {
		if ($number == 0 or $total == 0) {
			return '0%';
		}

		if ($number >= $total) {
			return '100%';
		}
		return intval($number / $total * 100) . '%';
	}

	public function getSetPrice($price) {
		return NumberToolkit::roundUp($price);
	}

	/**
	 * 生成分类树
	 * @param        $groupName
	 * @param string $indent
	 * @return array
	 * @edit fubaosheng 2015-05-18
	 */
	public function getCategoryChoices($groupName, $indent = '　') {

		$choices    = array();
		$categories = D('Taxonomy\CategoryService')->getCategoryTree();

		foreach ($categories as $category) {
			if ($category['isDelete'] == 1) $category['name'] = "【已删除】" . $category['name'];
			$choices[$category['id']] = str_repeat(is_null($indent) ? '' : $indent, ($category['depth'] - 1)) . $category['name'];
		}

		return $choices;
	}

	/**
	 * 获得图标
	 * @param        $groupName
	 * @param string $indent
	 * @return array
	 */
	public function getIconChoices($groupName, $indent = '　') {
		$choices = array();
		$pattern = '.*[jpg|png|jpeg|gif]$';

		$icons = array();
		if ($groupName == 'category') {
			$dirPath = getParameter('category_system_icon');
			$icons   = myScanDir($dirPath, $pattern, $absPath = 0);
		}
		if ($icons) {
			foreach ($icons as $icon) {
				$choices[$icon] = $icon;
			}
		}

		return $choices;
	}

	public function getDict($type) {
		return DataDict::dict($type);
	}

	public function getYear($type) {
		return DataDict::year($type);
	}

	public function getDictText($type, $key) {
		return DataDict::text($type, $key);
	}

	public function getUploadMaxFilesize($formated = true) {
		$max = FileToolkit::getMaxFilesize();
		if ($formated) {
			return FileToolkit::formatFileSize($max);
		}
		return $max;
	}

	public function getName() {
		return 'topxia_web_twig';
	}

	public function getFreeLimitType($course) {
		if (!empty($course['freeStartTime']) && !empty($course['freeEndTime'])) {
			$startTime = $course['freeStartTime'];
			$endTime   = $course['freeEndTime'];
			$now       = time();

			if ($startTime > $now) {
				return 'free_coming'; //即将限免
			} elseif ($endTime >= $now) {
				return 'free_now'; //正在限免
			} elseif ($endTime < $now) {
				return 'free_end'; //限免结束
			} else {
				return 'no_free';
			}
		} else {
			return 'no_free';
		}
	}

	public function blur_phone_number($phoneNum) {
		$head = substr($phoneNum, 0, 3);
		$tail = substr($phoneNum, -4, 4);
		return ($head . '****' . $tail);
	}

	public function mb_trim($string, $charlist = '\\\\s', $ltrim = true, $rtrim = true) {
		$both_ends = $ltrim && $rtrim;

		$char_class_inner = preg_replace(
			array('/[\^\-\]\\\]/S', '/\\\{4}/S'), array('\\\\\\0', '\\'), $charlist
		);

		$work_horse = '[' . $char_class_inner . ']+';
		$ltrim && $left_pattern = '^' . $work_horse;
		$rtrim && $right_pattern = $work_horse . '$';

		if ($both_ends) {
			$pattern_middle = $left_pattern . '|' . $right_pattern;
		} elseif ($ltrim) {
			$pattern_middle = $left_pattern;
		} else {
			$pattern_middle = $right_pattern;
		}

		return preg_replace("/$pattern_middle/usSD", '', $string);
	}

	/**
	 * 生成html分类树
	 * @param $cid
	 * @param $role_id
	 */
	public function getCateTreeHtml($cid = 0, $role_id = 0) {
		echo createService('Role.RoleService')->getCateTreeHtml($cid, $role_id);
	}


	/**
	 * 获取分类面包屑数组
	 * @param $id
	 * @return mixed
	 */
	public function getCrumbs($id) {
		return array_reverse(createService('Taxonomy.CategoryService')->getParentsById($id));
	}

	/**
	 * 根据分类ID获取所有的同辈分类或子分类
	 * @param $id
	 * @return mixed
	 */
	public function getTopCate($id){
		$taxonomyObj = createService('Taxonomy.CategoryService');
		$cate = $taxonomyObj->getCategoryById($id);
		if($cate['parentId'] != 0){
			//$cate['selected'] = true;
			$cate['child'] = $taxonomyObj->findCategoryBrother($id,true);
			foreach ($cate['child'] as $k=>$c){
				if($c['id'] == $id){
					$cate['child'][$k]['selected'] = true;
					break;
				}
			}
		}else{
			$cate['child'] = $taxonomyObj->findCategoryChildren($id);
		}
		return $cate;
	}

	/**
	 * 根据课程分类也即学院分类的ID获取其下所有的课程编号
	 */
	public function getCourseNumberList($id=null){
		$taxonomyObj = createService('Taxonomy.CategoryService');
		return $taxonomyObj->findCourseCodeByCategoryId($id);
	}

	/**
	 * 生成面包屑
	 * @author 王磊
	 * @param $id
	 * @param $page
	 */
	public function getBread($id, $page = 'course', $isClass = false) {
		if (!empty($id)) {
			$taxonomyObj = createService('Taxonomy.CategoryService');
			$arr  = $taxonomyObj->getParentsById($id);
			$arr  = array_reverse($arr);
			$line = '<em>&gt;</em>';
			$str  = '<ol class="breadcrumb">';
			foreach ($arr as $k => $v) {
				if (($k + 1) == count($arr)) {
					$str .= "<li class='active'><span>{$v['name']}</span></li>";
				} else {
					switch ($page) {
						case 'class':
							if ($isClass) {
								$str .= "<li><a href=" . U('Course/Course/explore', array('type' => 'class', 'category' => $v['id'])) . ">{$v['name']}</a></li>";
							} else
								$str .= "<li><a href=" . U('Wclass/Group/search', array('category' => $v['id'])) . ">{$v['name']}</a></li>";
							break;
						case 'course':
						default:
							$str .= "<li><a href=" . U('Course/Course/explore', array('category' => $v['id'], 'center' => I('center'))) . ">{$v['name']}</a></li>";
					}

				}
			}
			$str .= '</ol>';
			echo $str;
		}
	}

	public function getCategoryParents($id) {
		$parents = createService('Taxonomy.CategoryService')->getParentsById($id);
		return array_reverse($parents);
	}

	/**
	 * /**
	 * URL组装 支持不同URL模式
	 * @param string       $url    URL表达式，格式：'[模块/控制器/操作#锚点@域名]?参数1=值1&参数2=值2...'
	 * @param string|array $vars   传入的参数，支持数组和字符串
	 * @param string       $suffix 伪静态后缀，默认为true表示获取配置值
	 * @param boolean      $domain 是否显示域名
	 * @return string
	 */
	public function U($url = '', $vars = '', $suffix = true, $domain = false) {
		return U($url, $vars, $suffix, $domain);
	}

	/**
	 * 获取学校名称
	 * @param $webCode
	 * @return mixed
	 */
	public function getSchoolName($webCode) {
		return getSchoolName($webCode);
	}

	/**
	 * 获取学校类型
	 * @param $webCode
	 * @return mixed
	 */
	public function getSchoolType() {
		return 'school';
	}

	public function getWebDomain($webCode = '', $type = 'base', $scheme = 'http://') {
		return getWebDomain($webCode, $type, $scheme);
	}

	/**
	 * 获取某个学校名
	 * @author fubaosheng 2015-10-12
	 */
	public function getWebNameByWebcode($webCode) {
		return getWebNameByWebcode($webCode);
	}

	/**
	 * 获取分类名称
	 * @param $categoryId
	 */
	public function getCategoryName($categoryId) {
		return createService('Taxonomy.CategoryService')->getCategoryName($categoryId);
	}

	/**
	 * 根据分类id获取父级和父级以上的名字
	 * @author fubaosheng 2015-12-08
	 */
	public function getCateParentName($categoryId) {
		$name = createService('Taxonomy.CategoryService')->getCateParentName($categoryId);
		$name = trim($name, "—") ?: "";
		return $name;
	}

	public function getCourseName($courseId) {
		$data = createService('Course.CourseService')->getCourse($courseId);
		return $data['title'];
	}

	/**
	 * 获取用户加入班级申请
	 * @author fubaosheng 2015-10-12
	 */
	public function getUserApply($applyId) {
		if (empty($applyId)) return array();
		$userApply = createService("Group.GroupApplyService")->findById($applyId);
		return $userApply ?: array();
	}

	/**
	 * 是否展现logo后缀
	 * @author 钱志伟 2015-11-10
	 */
	public function isShowLogoSfx() {
		return !in_array(C('WEBSITE_CODE'), C('NO_SHOW_LOGO_PFX'));
	}

	public function getVersion() {
		return defined('WEB_VERSION') ? WEB_VERSION : '';
	}

	/**
	 * 生成检验码:  md5(webcode + 类型 + 参数id + 密钥)
	 * @author 谈海涛 2015-11-30
	 */
	public function md5Vcode($type, $strId) {
		if (!$type || !$strId) return false;
		if (!preg_match("/^[a-z0-9_]+$/", $type)) return false;
		if (!preg_match("/^[a-z0-9_]+$/", $strId)) return false;
		$secret = C('SECRET');
		// md5(webcode + 类型 + 参数id + 密钥)
		$str = md5(C('WEBSITE_CODE') . $type . $strId . $secret['APPRAISE']);
		return substr($str, 8, 16);
	}


	/**
	 * 评论参数加密 string $createUid  页面创建者uid（如海报创建者uid）
	 * @author 谈海涛 2015-12-1
	 */
	public function md5Comment($cmtType, $cmtIdStr, $createUid) {
		if (!$cmtType || !$cmtIdStr) return false;
		$secret = C('SECRET');
		$str    = $cmtType . '_' . $cmtIdStr . '_' . $secret['COMMENT'];
		if ($createUid)
			$str = $cmtType . '_' . $cmtIdStr . '_' . $createUid . '_' . $secret['COMMENT'];
		return encrypt($str);
	}

	/**
	 * 获取课程申请记录的id，待处理的
	 * @author fubaosheng 2015-12-01
	 */
	public function getCourseApplyId($courseId) {
		return createService("Course.CourseService")->getCourseApplyId($courseId);
	}

	public function publicCourseName() {
		return C("PUBLIC_COURSE_NAME");
	}

	public function navbarPublicCourseName() {
		return C("NAVBAR_PUBLIC_COURSE_NAME");
	}

	public function privateCourseName() {
		return C("PRIVATE_COURSE_NAME");
	}

	public function canManageCourse($courseId) {
		return createService("Course.CourseService")->canManageCourse($courseId);
	}

	//获取课程的分类ID
	public function courseCategoryIds($courseId) {
		if (!$courseId) return "";
		return createService("Course.CourseService")->getCourseCategory($courseId) ?: "";
	}

	/**
	 * 是否关闭用户写
	 * @author 钱志伟 2016-01-30
	 */
	public function isCloseUserWrite() {
		return isCloseUserWrite();
	}

	public function courseCategoryNames($courseId) {
		if (!$courseId) return array();
		$categoryIds = $this->courseCategoryIds($courseId);
		if (!$categoryIds) return array();
		return createService('Taxonomy.CategoryService')->getNameByIds($categoryIds) ?: array();
	}

}

