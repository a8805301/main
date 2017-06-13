<?php
/**
 * 杂项函数定义文件
 * @author weicky
 * @package GameFactory
 */

/**
 * 配置获取函数
 * @param string $section 配置段名 '@文件':表示该文件区分项目环境
 * @param string $key 配置键名
 * @return mix 配置值
 */
function CFG($section, $key='') {
	global $_CONFIG;
	$env		= ($section{0} == '@' ? true : false);
	$section	= $env ? substr($section, 1) : $section;
	$val		= null;
	$fields		= $key ? explode('.', $key) : array();
	if(!array_key_exists($section, $_CONFIG)) {
		App::loadConfig($section, $env);
	}
	$val = $_CONFIG[$section];
	foreach($fields as $field){
		if(isset($val[$field])){
			$val = $val[$field];
		}else{
			return null;
		}
	}
	return $val;
}

/**
 * 语言字段获取函数
 * @param string $section 语言包名
 * @param string $key 语言字段名
 * @return string 语言描述
 */
function LANG($section, $key='') {
	global $_LANG;
	if(!array_key_exists($section, $_LANG)) {
		App::loadLang($section);
	}
	$fields		= $key ? explode('.', $key) : array();
	$val = $_LANG[$section];
	foreach($fields as $field){
		if(isset($val[$field])){
			$val = $val[$field];
		}else{
			return null;
		}
	}
	return $val;
}

/**
 * 获取DB/CACHE连接对象
 * @param string $driver 存储服务类型
 * @param string $name 配置标识
 * @param bool $persistent 是否持久化
 * @return mixed 连接对象
 */
function DATA($driver, $name, $persistent=false) {
	global $_DATA;
	switch($driver) {
		case 'db':
			$cls = 'mudb';
			$file = App::$path . 'lib/class.mudb.php';
			break;
		case 'mc':
			$cls = 'mucache';
			$file = App::$path . 'lib/class.mucache.php';
			break;
		case 'redis':
			$cls = 'muredis';
			$file = App::$path . 'lib/class.muredis.php';
			break;
		default:
			THROW_EXCEPTION("Data driver '{$driver}' don't exists!");
	}
	if(!class_exists($cls, false)) {
		require_once($file);
	}
	if(!array_key_exists($driver, $_DATA)) {
		$_DATA[$driver] = array();
	}
	if(!isset($_DATA[$driver][$name])) {
		$cfg = CFG('@core', "{$driver}.{$name}");
		if(!$cfg) {
			THROW_EXCEPTION("Can't find configuration of '{$driver}.{$name}'!");
		}
		$_DATA[$driver][$name] = new $cls($cfg, $persistent);
	}
	return $_DATA[$driver][$name];
}

/**
 * 获取DB连接对象
 * @param string $name 配置标识
 * @param bool $persistent 是否持久化
 * @return mudb 连接对象
 */
function DB($name, $persistent=false) {
	return DATA('db', $name, $persistent);
}

/**
 * 获取Memcached连接对象
 * @param string $name 配置标识
 * @param bool $persistent 是否持久化
 * @return mucache 连接对象
 */
function MC($name, $persistent=false) {
	return DATA('mc', $name, $persistent);
}

/**
 * 获取Redis连接对象
 * @param string $name 配置标识
 * @param bool $persistent 是否持久化
 * @return muredis 连接对象
 */
function REDIS($name, $persistent=false) {
	return DATA('redis', $name, $persistent);
}

/**
 * 抛出异常
 * @param string $msg 异常信息
 * @param int $code 异常代码
 * @throws AppException
 */
function THROW_EXCEPTION($msg='' ,$code=E_USER_ERROR) {
	global $_DEBUG;
	//$_DEBUG['_debug_backtrace_arr'] = debug_backtrace();
	throw new AppException($msg, $code);
}

/**
 * 安全的die
 * @param string $str 输出内容
 */
function __die($str='') {
	echo $str;
	THROW_EXCEPTION('', E_ALL);
}

/**
 * 安全的exit
 * @param string $str 输出内容
 */
function __exit($str='') {
	echo $str;
	THROW_EXCEPTION('', E_ALL);
}

/**
 * 简单调试日志记录
 * @param mixed $data 内容
 * @param string $file 文件名(不带扩展名)
 * @param int $fsize 文件最大几M
 */
function DEBUG_LOG($data, $file='debug', $fsize=1) {
	$data = is_string($data) ? $data : var_export($data, true);
	$file = App::$path . "data/debug/{$file}.txt";
	$size = file_exists($file) ? @filesize($file) : 0;
	$flag = $size < max(1, $fsize) * 1024 * 1024; //标志是否附加文件.文件控制在1M大小
	@file_put_contents($file, "{$data}\n", $flag ? FILE_APPEND : null);
}
