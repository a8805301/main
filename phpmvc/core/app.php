<?php
/**
 * 主控应用程序类定义文件
 * @author weicky
 * @package GameFactory
 */

/**
 * 主控应用程序类
 */
class App {
	/**
	 * 项目主目录
	 * @var string
	 */
	public static $path;

	/**
	 * 项目环境 开发:dev 测试:test 产品:product
	 * @var string
	 */
	public static $env;

	/**
	 * 项目语言包
	 * @var string
	 */
	public static $lang;

	/**
	 * 项目区服ID(不同区服拥有不同的用户群)
	 * @var string
	 */
	public static $area;

	/**
	 * 项目物理子服ID(同区服下的子服ID)
	 * @var int
	 */
	public static $workerId;

	/**
	 * 请求接口名
	 * @var string
	 */
	public static $action;

	/**
	 * IO类型名
	 * @var string
	 */
	public static $ioClass;

	/**
	 * 日志操作对象
	 * @var Log
	 */
	public static $log;
	/**
	 * 请求到达时间
	 * @var float
	 */
	public static $rtime;

	/**
	 * 控制器类名
	 * @var string
	 */
	public static $controllerName;

	/**
	 * 自动加载回调
	 * @param string $class_name 类名
	 * @return boolean 是否已加载
	 */
	public static function autoload($class_name) {
		$pos = strrpos($class_name, '_');
		if($pos !== false) {
			$parts 		= explode('_', $class_name);
			$rootCls	= array_pop($parts);
			$baseCls	= array_pop($parts);
			$tables		= array(
				'Controller'	=> 'controller/',
				'Model'			=> 'model/',
				'Event'			=> 'event/',
			);
			if(array_key_exists($rootCls, $tables)) {
				array_push($parts, $rootCls);
				array_push($parts, $baseCls);
				$file = App::$path . strtolower(implode('/', $parts)) . '.php';
				if(file_exists($file)) {
					@include($file);
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 加载框架文件
	 */
	protected static function _requireFiles() {
		require_once(self::$path . 'core/io.php');
		require_once(self::$path . 'core/controller.php');
		require_once(self::$path . 'core/model.php');
		require_once(self::$path . 'core/misc.php');
		require_once(self::$path . 'core/log.php');
		require_once(self::$path . 'core/appexception.php');
		require_once(self::$path . 'config/constant.php');
	}

	/**
	 * 分发请求
	 */
	protected static function _dispatch() {
		global $_INPUT;
		$ret = isset($_INPUT['action']) ? explode('.', $_INPUT['action'], 2) : array();
		$ret[0]	= isset($ret[0]) ? preg_replace('/\W/', '', $ret[0]) : 'index';
		$ret[1]	= isset($ret[1]) ? preg_replace('/\W/', '', $ret[1]) : 'index';
		self::$action = array($ret[0], $ret[1]);
	}

	/**
	 * 触发一个事件
	 * @param string $event 事件名(事件处理类名.方法名)
	 * @return mixed|boolean 事件处理程序返回结果或失败时返回false
	 */
	public static function triggerEvent($event) {
		$handle = explode('.', $event, 2);
		if(!$handle[0] || !$handle[1]) {
			return false;
		}
		$handle[0] = ucfirst($handle[0]) . '_Event';
		if(class_exists($handle[0]) && method_exists($handle[0], $handle[1])) {
			$param = (array)func_get_args();
			array_shift($param);
			return call_user_func_array($handle, $param);
		}
		return false;
	}

	/**
	 * 注册错误处理函数
	 */
	protected static function _regErrorHandler() {
		//判断是否需要记录notice
		if(defined('E_DEPRECATED')) {
			AppException::$ignoreErrors[] = E_DEPRECATED;
			AppException::$ignoreErrors[] = E_USER_DEPRECATED;
		}
		if((error_reporting() & E_NOTICE) == 0) {
			AppException::$ignoreErrors[] = E_NOTICE;
			AppException::$ignoreErrors[] = E_USER_NOTICE;
		}
		//注册捕获错误和异常函数
		set_error_handler(array('AppException', 'errorHandler'));
		set_exception_handler(array('AppException', 'exceptionHandler'));
		//注册致命错误函数
		register_shutdown_function(array('AppException', 'shutdownHandler'));
	}

	/**
	 * 运行
	 *
	 * @param string $path 项目主目录
	 * @param string $env 项目环境
	 */
	public static function run($path, $env, $lang='zh_CN', $area='', $workerId=0) {
		global $_CONFIG, $_INPUT, $_OUTPUT, $_LANG, $_DATA, $_DEBUG; //全局变量

		//初始化变量
		self::$path = $path;
		self::$env = $env;
		self::$lang = $lang;
		self::$area = $area;
		self::$workerId = $workerId;
		self::$rtime = microtime(true);
		$_CONFIG = $_INPUT = $_LANG = $_DATA = $_DEBUG = array();
		//加载框架
		spl_autoload_register(array('App', 'autoload'));

		self::_requireFiles();
		$io				= IOFactory::autoCreate();
		self::$ioClass	= get_class($io);

		try {
			//日志操作
			self::$log = Log::instance();
			self::$log->attach(new Logger(self::$path . 'data/log/') , Log::STRACE);
			self::$log->attach(new Logger(self::$path . 'data/debug/') , array(Log::DEBUG));
			self::_regErrorHandler();

			//触发请求开始事件
			self::triggerEvent('App.start');
			//获取请求数据
			$_INPUT			= $io->input();
			//触发请求数据提取完毕事件
			self::triggerEvent('App.input');
			//分发请求
			self::_dispatch();
			//触发请求分发完毕事件
			self::triggerEvent('App.dispatch');
			//创建控制类对象
			$controller		= Controller::factory(self::$action[0]);
			if($controller && method_exists($controller, self::$action[1])) {
				self::$controllerName = get_class($controller); //获取控制器类名
				//调用控制器对象方法
				$_OUTPUT	= call_user_func(array($controller, self::$action[1]));
			} else {
				THROW_EXCEPTION("Not Found '{$_INPUT['action']}'", E_USER_NOTICE);
			}
		} catch(Exception $e) {
			$code = $e->getCode();
			if(self::$log && $code !== null){
				//self::$log->add(LOG_ERR, $e->getMessage()." \r\nTrace:".$e->getTraceAsString());
				AppException::exceptionHandler($e);
			}
		}

		//处理返回结果
		if(isset($_OUTPUT)){
			//触发数据返回前事件
			self::triggerEvent('App.output');
			$io->output($_OUTPUT);
		}

		//触发请求处理完毕事件
		self::triggerEvent('App.finish');
	}

	/**
	 * 加载配置文件
	 * @param string $name 配置文件名(无后缀)
	 * @param bool $env 是否区分项目运行环境
	 */
	public static function loadConfig($name, $env=false) {
		global $_CONFIG;
		$_CONFIG[$name] = array();
		$files = (array)self::triggerEvent('Derive.config', $name, $env);
		if($files) {
			foreach($files as $file) {
				$overwrites = (array)@include($file);
				foreach($overwrites as $k => $v) {
					$_CONFIG[$name][$k] = $v;
				}
				unset($overwrites);
			}
		}
	}

	/**
	 * 加载语言文件
	 * @param string $name 语言文件名(无后缀)
	 */
	public static function loadLang($name) {
		global $_LANG;
		$_LANG[$name] = array();
		$files = (array)self::triggerEvent('Derive.lang', $name);
		if($files) {
			foreach($files as $file) {
				$_LANG[$name] = array_merge($_LANG[$name], (array)@include($file));
			}
		}
	}

	/**
	 * 加载视图
	 * @param string $name 视图文件名(无后缀)
	 */
	public static function loadView($name, $vars=array()) {
		$file = self::triggerEvent('Derive.view', $name);
		if($file) {
			if($vars){
				extract($vars);
			}
			@include($file);
		}
	}
}
