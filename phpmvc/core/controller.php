<?php
/**
 * 控制器类基类定义文件
 * @author weicky
 * @package GameFactory
 */

/**
 * 控制器基类
 */
class Controller {
	/**
	 * 工厂方法
	 * @param string $name 名称
	 * @return boolean|Controller 成功创建控制器类对象，失败返回false
	 */
	public static function factory($name) {
		$cls = App::triggerEvent('Derive.controller', $name);
		if(!$cls) {
			THROW_EXCEPTION("Controller '{$name}' don't exists!", 404);
		}
		return (new $cls);
	}

	/**
	 * 错误码：系统已关闭(停服维护)
	 */
	const CODE_SYSTEM_CLOSED = 1;

	/**
	 * 错误码：需要登录
	 */
	const CODE_NEED_LOGIN = 2;

	/**
	 * $_INPUT的快捷方式？
	 * @var array
	 */
	protected $_req = array();

	/**
	 * ?
	 * @var string
	 */
	public static $langName = 'error';

	/**
	 * 生成响应结果
	 * @param $data 处理结果
	 * @param $code api接口状态码
	 * @param $err 错误信息
	 */
	public function ret($data='', $code=200, $err=''){
		if(200 != $code){
			$ret = array(
				'code'   => $code,
				'error'  => $err ? $err : strval(LANG('error', "ERR_{$code}")),
				'result' => $data,
			);
		}else{
			$ret = array(
				'code'   => $code,
				'error'  => $err ? $err : '',
				'result' => $data,
			);
		}
		return $ret;
	}
}

/**
 * CLI模式控制器类基类(自动检查运行模式)
 */
class CliController {
	/**
	 * 构造函数
	 */
	public function __construct() {
		if(PHP_SAPI != 'cli') {
			exit("Run in CLI mode!");
		}
		Log::$writeOnAdd = true;
	}
}