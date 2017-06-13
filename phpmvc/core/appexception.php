<?php 
/**
 * 异常类
 * @author jasydong
 * @package GameFactory
 */


/**
 * 异常类
 */
class AppException extends Exception {
	/**
	 * 需要忽略掉的错误类型
	 * @var array
	 */
	public static $ignoreErrors = array(E_STRICT, E_ALL);

	/**
	 * 需要中止的错误
	 * @var array
	 */
	public static $fatalErrors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR);

	/**
	 * 构造函数
	 * @param string $message 错误消息
	 * @param int $code 错误类型
	 * @param mixed $previous 未知
	 */
	public function __construct($message = null, $code = null, $previous = null) {
		parent::__construct($message, $code);
	}

	/**
	 * 错误捕获处理
	 * @param string $code
	 * @param string $error
	 * @param string $file
	 * @param string $line
	 * @throws AppException
	 */
	public static function errorHandler($code, $error, $file = null, $line = null) {
		AppException::exceptionHandler(new ErrorException($error, $code, 0, $file, $line));
		return !in_array($code, self::$fatalErrors);
	}

	/**
	 * shutdown处理函数，获取fatal错误
	 */
	public static function shutdownHandler(){
		$error = error_get_last();
		if($error){
			AppException::exceptionHandler(new ErrorException(
				$error['message'], $error['type'], 0, $error['file'], $error['line'])
			);
		}
	}

	/**
	 * 异常处理函数
	 */
	public static function exceptionHandler($e) {
		$file = $e->getFile();
		$line = $e->getline();
		$code = $e->getCode();
		$message = $e->getMessage();

		if(!in_array($code, self::$ignoreErrors)) {
			//记录或输出异常信息
			if (App::$log != null) {
				Log::$writeOnAdd = true;
				App::$log->add(Log::ERROR, sprintf("[%s.%s@%s#%d] (%d) %s", App::$action[0], App::$action[1], $file, $line, $code, $message));
			}
			App::triggerEvent('App.error', $e);
		}
	}

	/**
	 * eval代码执行错误捕获
	 * @param unknown $code
	 * @param unknown $error
	 * @param string $file
	 * @param string $line
	 */
	public static function evalErrorHandler($code, $error, $file = null, $line = null) {
		echo "\n{$error}: {$code} -- File {$file} line {$line}\n";
		return true;
	}

	/**
	 * eval代码执行异常处理函数
	 */
	public static function evalExceptionHandler($e) {
		$file = $e->getFile();
		$line = $e->getline();
		$code = $e->getCode();
		$message = $e->getMessage();
		echo "\n异常: [{$file}#{$line}] ({$code}) {$message}\n";
	}

	public static function evalShutdownHandler(){
		/*$error = error_get_last();
		if( $error ){
			echo json_encode(array('error' => $error, 'code' => 500));
		}*/
	}
}
