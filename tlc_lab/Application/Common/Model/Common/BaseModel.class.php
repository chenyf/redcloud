<?php
namespace Common\Model\Common;

use Common\Lib\WebCode;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Common\Model\Common\FieldSerializer;
use Common\Common\DynamicQueryBuilder;
use Common\Model\Util\HTMLPurifierFactory;
use Common\Lib\EnvCfgManage;
use Think\Model\RelationModel;
use Common\Model\Util\DictFilterUtil;

class BaseModel extends RelationModel {

	private static $_instance;

	private static $_dispatcher;

	protected $_moduleDirectories = array();

	protected $_moduleConfig = array();

	protected $environment;
	protected $debug;
	protected $booted;

	protected $parameterBag;

	protected $currentUser;

	protected $pool = array();

        protected $connection;

        protected $primaryKey = 'id';

        private static $cachedSerializer = array();

        protected $dataCache = array();


	public function _initialize(){

		$currentUser = D('User\CurrentUser');
		$currentUser->setData(D('Service\UserService')->getData());
		$this->setCurrentUser($currentUser);
	}

	public function creates($environment, $debug)
	{
		if (self::$_instance) {
			return self::$_instance;
		}

		$instance = new self();
		$instance->environment = $environment;
		$instance->debug = (Boolean) $debug;
		$instance->registerModuleDirectory(realpath(__DIR__ . '/../../../'));

		self::$_instance = $instance;

		return $instance;
	}


	public static function instance() {
		if (empty(self::$_instance)) {
			E('ServiceKernel未实例化');
		}
		self::$_instance->boot();
		return self::$_instance;
	}

	public static function dispatcher() {
		if (self::$_dispatcher) {
			return self::$_dispatcher;
		}

		self::$_dispatcher = new EventDispatcher();

		return self::$_dispatcher;
	}

	public function boot() {
		if (true === $this->booted) {
			return;
		}
		$this->booted = true;

		$moduleConfigCacheFile = $this->getParameter('kernel.root_dir') . '/cache/' . $this->environment . '/modules_config.php';

		if (file_exists($moduleConfigCacheFile)) {
			$this->_moduleConfig = include $moduleConfigCacheFile;
		} else {
			$finder = new Finder();
			$finder->directories()->depth('== 0');

			foreach ($this->_moduleDirectories as $dir) {

				if (glob($dir . '/*/Service', GLOB_ONLYDIR)) {

					$finder->in($dir . '/*/Service');
				}

			}

			foreach ($finder as $dir) {
				$filepath = $dir->getRealPath() . '/module_config.php';
				if (file_exists($filepath)) {
					$this->_moduleConfig = array_merge_recursive($this->_moduleConfig, include $filepath);
				}
			}

			if (!$this->debug) {
				$cache = "<?php \nreturn " . var_export($this->_moduleConfig, true) . ';';
				file_put_contents($moduleConfigCacheFile, $cache);
			}
		}

		$subscribers = empty($this->_moduleConfig['event_subscriber']) ? array() : $this->_moduleConfig['event_subscriber'];

		foreach ($subscribers as $subscriber) {
			$this->dispatcher()->addSubscriber(new $subscriber());
		}

	}

	public function setParameterBag($parameterBag) {
		$this->parameterBag = $parameterBag;
	}

	public function getParameter($name) {
		if (is_null($this->parameterBag)) {
			E('尚未初始化ParameterBag');
		}
		return $this->parameterBag->get($name);
	}

	public function hasParameter($name) {
		if (is_null($this->parameterBag)) {
			E('尚未初始化ParameterBag');
		}
		return $this->parameterBag->has($name);
	}

	public function setCurrentUser($currentUser) {
		$this->currentUser = $currentUser;
		return $this;
	}

	public function getCurrentUser() {
//                $currentUser = D('User\CurrentUser');
//		$currentUser->setData(D('Service\UserService')->getData());
//		$this->setCurrentUser($currentUser);
		if (is_null($this->currentUser)) {
			E('尚未初始化CurrentUser');
		}
                //edit by qzw
                //return $this->currentUser->data();
		return $this->currentUser;
	}

	public function getCurrentUid(){
		return	$this->currentUser['id'];
	}

	public function setEnvVariable(array $env) {
		$this->env = $env;
		return $this;
	}

	public function getEnvVariable($key = null) {
		if (empty($key)) {
			return $this->env;
		}

		if (!isset($this->env[$key])) {
			E("Environment variable `{$key}` is not exist.");
		}

		return $this->env[$key];
	}


    public function getConnection ()
    {
        $connIdx = $this->getDbConnIdx();
        if(!isset($this->connection[$connIdx])) $this->setConnection ();
        return $this->connection[$connIdx];
    }
    /**
     * 获得dbdc数据库连接索引
     * @author 钱志伟 2015-08-03
     */
    private function getDbConnIdx(){
        return $this->currentDb ? $this->currentDb : 'default';
    }

    public function setConnection()
    {
        $connIdx = $this->getDbConnIdx();
        #配置前缀 qzw 2015-07-29
        $upStr  = strtoupper($this->currentDb);
        $cfgPfx = $this->currentDb && $this->currentDb!='default' ? "DB_{$upStr}." : '';

        $dbName = C($cfgPfx.'DB_NAME');
        $params = array('driver' => 'pdo_mysql', 'host' => C($cfgPfx.'DB_HOST'), 'port' => C($cfgPfx.'DB_PORT'), 'dbname' => $dbName, 'user' => C($cfgPfx.'DB_USER'),
                        'password' => C($cfgPfx.'DB_PWD'), 'driverOptions' => array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
        $eventManager = new \Symfony\Bridge\Doctrine\ContainerAwareEventManager($this);
        $mappingTypes = array('enum' => 'string');

        $cf = new \Doctrine\Bundle\DoctrineBundle\ConnectionFactory(array());
        $this->connection[$connIdx] = $cf->createConnection($params, $config=false, $eventManager, $mappingTypes);

        #加属性__db by qzw 2015-08-31
        $this->connection[$connIdx]->__db = $dbName;
    }

	public function createService($name) {
		if (empty($this->pool[$name])) {
			$class             = $this->getClassName('service', $name);
//                        echo $class;die;
			$this->pool[$name] = new $class();
		}
                #支持公共webcode设置 qzw 2015-08-07
                #支持切库
//                $this->pool[$name]->webCodeEvent = $this->webCodeEvent;
//                $this->pool[$name] = $this->pool[$name]->switchDB($this->currentDb);
//                $this->pool[$name] = $this->pool[$name]->triggerWebCodeEvent($this->pool[$name]);
		return $this->pool[$name];
	}

	public function createDao($name) {
		if (empty($this->pool[$name])) {
			$class = $this->getClassName('dao', $name);
			$dao   = new $class();
			//$dao->setConnection($this->getConnection());
			$this->pool[$name] = $dao;
		}
                #支持公共webcode设置 qzw 2015-08-07
                #支持切库
//                $this->pool[$name]->webCodeEvent = $this->webCodeEvent;
//                $this->pool[$name] = $this->pool[$name]->switchDB($this->currentDb);
//                $this->pool[$name] = $this->pool[$name]->triggerWebCodeEvent($this->pool[$name]);
		return $this->pool[$name];
	}

	public function getEnvironment() {
		return $this->environment;
	}

	public function isDebug() {
		return $this->debug;
	}

	public function registerModuleDirectory($dir) {
		$this->_moduleDirectories[] = $dir;
	}

	public function getModuleConfig($key, $default = null) {
		if (!isset($this->_moduleConfig[$key])) {
			return $default;
		}
		return $this->_moduleConfig[$key];
	}

	//返回：Common/Model/$module/$className
	private function getClassName($type, $name) {
                $namespace = substr(__NAMESPACE__, 0, -strlen('Common') - 1);
		list($module, $className) = explode('.', $name);
		$type = strtolower($type);
                //qzw 2015-08-03
                if(($type=='dao' || $type=='service') && substr($className, -5)!='Model') $className .= 'Model';
		return $namespace . '\\' . $module . '\\' . $className ;
	}

        protected function wave ($id, $fields) {
            $sql = "UPDATE {$this->getTable()} SET ";
            $fieldStmts = array();
            foreach (array_keys($fields) as $field) {
                $fieldStmts[] = "{$field} = {$field} + ? ";
            }
            $sql .= join(',', $fieldStmts);
            $sql .= "WHERE id = ?";

            $params = array_merge(array_values($fields), array($id));
            return $this->getConnection()->executeUpdate($sql, $params);
        }

        public function getTable(){
            if($this->tableName){
                return $this->tableName;
            }else{
                return self::TABLENAME;
            }
        }

        protected function fetchCached(){
            $args = func_get_args();
            $callback = array_pop($args);

            $key = implode(':', $args);
            if (isset($this->dataCached[$key])) {
                return $this->dataCached[$key];
            }

            array_shift($args);
            $this->dataCached[$key] = call_user_func_array($callback, $args);

            return $this->dataCached[$key];
        }

        protected function clearCached(){
            $this->dataCached = array();
        }

        protected function createDaoException($message = null, $code = 0) {
            return new DaoException($message, $code);
        }

        protected function createDynamicQueryBuilder($conditions){
            $dQueryBuilder = new DynamicQueryBuilder($this->getConnection(), $conditions);
            return $dQueryBuilder;
        }

        public function createSerializer(){
            if (!isset(self::$cachedSerializer['field_serializer'])) {
                self::$cachedSerializer['field_serializer'] = new FieldSerializer();
            }
            return self::$cachedSerializer['field_serializer'];
        }

        protected function filterStartLimit(&$start, &$limit){
           $start = (int) $start;
           $limit = (int) $limit;
        }

        protected function checkOrderBy (array $orderBy, array $allowedOrderByFields){
            if (empty($orderBy[0]) or empty($orderBy[1])) {
                throw new \RuntimeException('orderBy参数不正确');
            }

            if (!in_array($orderBy[0], $allowedOrderByFields)){
                throw new \RuntimeException("不允许对{$orderBy[0]}字段进行排序", 1);
            }
            if (!in_array($orderBy[1], array('ASC','DESC'))){
                throw new \RuntimeException("orderBy排序方式错误", 1);
            }

            return $orderBy;
        }
       protected function createServiceException($message = 'Service Exception', $code = 0)
    {
        return new \Think\Exception($message, $code);
    }

     protected function purifyHtml($html, $trusted = false)
    {
        if (empty($html)) {
            return '';
        }

//        $config = array(
//            'cacheDir' => getParameter('kernel.cache_dir') .  '/htmlpurifier'
//        );
//
//        $factory = new HTMLPurifierFactory($config);
//        $purifier = $factory->create($trusted);
//
//        return $purifier->purify($html);

	    $remove_xss_html =  remove_xss($html);

		$filter_sensitive_dict_html = (new DictFilterUtil())->filterString($remove_xss_html);

		return $filter_sensitive_dict_html;
    }
}
