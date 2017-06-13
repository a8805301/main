<?php
/**
 * 数据模型类基类定义文件
 * @author weicky
 * @package GameFactory
 */

/**
 * 数据模型类基类
 */
class Model {
	/**
	 * 表名
	 * @var string
	 */
	protected $_table;

	/**
	 * 真实的表名(不包含库名)
	 * @var string
	 */
	protected $_realTableName;

	/**
	 * 模型类的数据源列表
	 * @var array
	 */
	protected $_dataSource;

	/**
	 * 数据库连接对象
	 * @var mudb
	 */
	protected $_db;

	/**
	 * 数据库分库数
	 * @var int
	 */
	protected $_dbClusterNumber;

	/**
	 * redis 链接对象
	 * @var muredis
	 */
	protected $_redis;
	/**
	 * memcached 链接对象
	 * @var mucache
	 */
	protected $_mc;

	/**
	 * 对象实例列表
	 * @var array
	 */
	protected static $_instances = array();

	/**
	 * 工厂方法
	 * @param string $name 模型名，例:main.users
	 * @param sting $driverType 数据驱动类型(db:数据库,mc:Memcache缓存,redis:Redis缓存)
	 * @param array $init 模型类初始化数组
	 * @return Model 模型类对象
	 */
	public static function factory($name, $driverType='db', $init=array()) {
		if(!in_array($driverType, array('db', 'redis', 'mc'))) {
			THROW_EXCEPTION("Unknown data source type '{$name}'!");
		}
		$pos		= strpos($name, '.');
		$driverId	= '';
		if($pos !== false) {
			$driverId	= substr($name, 0, $pos);
			$name		= substr($name, $pos + 1);
		}
		$cls		= App::triggerEvent('Derive.model', $name);
		if(!$cls) {
			THROW_EXCEPTION("Model '{$name}' don't exists!");
		}
		$driverId	=	empty($driverId) ? $name : $driverId;
		return new $cls($driverId, $driverType, '', $init);
	}

	/**
	 * 获取对象实例(单例模式)
	 * @param string $name 模型名，例:main.users
	 * @param sting $driverType 数据驱动类型(db:数据库,mc:Memcache缓存,redis:Redis缓存)
	 * @param array $init 模型类带参初始化数组(可扩展带参的构造方法)
	 * @return Model 模型类对象
	 */
	public static function getInstance($name , $driverType='db', $init = array()) {
		$id = "{$driverType}:{$name}";
		$id .= (empty($init) ? '':json_encode($init));
		if(!isset(self::$_instances[$id])) {
			self::$_instances[$id] = self::factory($name, $driverType, $init);
		}
		return self::$_instances[$id];
	}

	/**
	 * 构造函数
	 * @param string $driverId 数据驱动对象ID
	 * @param string $driverType 数据驱动类型
	 * @param string $prefix 表名前缀【废弃】
	 * @param array $init 模型类初始化数组
	 */
	public function __construct($driverId, $driverType = 'db' , $prefix='', $init = array()) {
		if(!is_array($this->_dataSource)) {
			$this->_dataSource = array();
		}
		if(empty($this->_dataSource)) {
			$this->_dataSource[$driverType] = $driverId;
		}
		if(!empty($this->_dataSource['db'])) {
			$this->_makeRealTableName();
			$dbConfig = CFG('@core', "db.{$this->_dataSource['db']}");
			if(CFG('@core', 'open_db_cluster') && is_array($dbConfig[0])) {
				$this->_dbClusterNumber = count($dbConfig);
			} else {
				$this->_dbClusterNumber = 0;
				$this->_createDB($this->_dataSource['db']);
			}
		}
		if(!empty($this->_dataSource['redis'])) {
			$this->_redis = REDIS($this->_dataSource['redis']);
		}
		if(!empty($this->_dataSource['mc'])) {
			$this->_mc = MC($this->_dataSource['mc']);
		}
	}

	/**
	 * 创建DB连接对象
	 * @param string $dbcfgname 数据库配置名
	 */
	protected function _createDB($dbcfgname) {
		$this->_db = DB($dbcfgname);
		$this->_makeFullTableName();
	}

	/**
	 * 生成完整表名
	 * @param $prefix 表名前缀
	 * @return string 表名
	 */
	protected function _makeFullTableName() {
		$dbprefix	= ($this->_db->aServer[3] ? $this->_db->aServer[3] . '.' : '');
		$this->_table = $dbprefix . $this->_realTableName;
	}

	/**
	 * 生成表名(无前缀)
	 * @return string 表名
	 */
	protected function _makeTableName() {
		$cls		= strtolower(get_class($this));
		$arr		= explode('_', $cls);
		$basename	= array_pop($arr); //"Model"
		$modelname	= array_pop($arr);
		return ($modelname ? $modelname : $basename);
	}

	/**
	 * 生成真实表名
	 */
	protected function _makeRealTableName() {
		$this->_realTableName = CFG('@core', 'table_prefix') . $this->_makeTableName();
	}

	/**
	 * 选择分库
	 * @param int $n 分库参数
	 * @param int $divideBy 参数$n除几(分库索引=floor($n / $divideBy) % 分库数)
	 * @param string $ds 数据类型
	 */
	protected function _selectCluster($n, $divideBy=1, $ds='db') {
		if(CFG('@core', 'open_db_cluster') && $ds == 'db') {
			if($this->_dbClusterNumber > 0) {
				$clusterId = floor($n / $divideBy) % $this->_dbClusterNumber;
				$cfgkey = "{$this->_dataSource['db']}.{$clusterId}";
			} else {
				$cfgkey = $this->_dataSource['db'];
			}
			$this->_createDB($cfgkey);
		}
	}

	/**
	 * 获取db对象
	 * @return mudb
	 */
	public function getDB() {
		return $this->_db;
	}

	/**
	 * 获取memcache对象
	 * @return mucache
	 */
	public function getMC() {
		return $this->_mc;
	}

	/**
	 * 获取redis对象
	 * @return muredis
	 */
	public function getRedis() {
		return $this->_redis;
	}

	/**
	 * 获取当前的表名
	 * @return string
	 */
	public function getTable(){
		return $this->_table;
	}
}
