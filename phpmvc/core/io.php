<?php
/**
 * IO相关类定义文件
 * @author weicky
 * @package GameFactory
 */

/**
 * IO接口
 */
interface IOInterface {
	/**
	 * 输入
	 * @return array 接收到的数据
	 */
	public function input();

	/**
	 * 输出
	 * @param array $data 要输出的数据
	 */
	public function output($data);
}

/**
 * IO过滤器接口
 */
interface IOFilterInterface {
	/**
	 * 过滤
	 * @param string $data 要操作的数据
	 * @param string $op 操作类型 input:输出 output:输出
	 * @return string 返回处理后的结果
	 */
	public static function apply($data, $op);
}

/**
 * IO工厂类
 */
class IOFactory {
	/**
	 * 自动创建IO类对象
	 * @return IOInterface
	 */
	public static function autoCreate() {
		$content_type = array("application/octet-stream","text/xml");
		if(PHP_SAPI == 'cli') {
			return new CliIO;
		} elseif($_SERVER['REQUEST_METHOD'] == 'POST' && !in_array ($_SERVER['CONTENT_TYPE'], $content_type) && empty($_POST)) {
			return new RestIO;
		} else {
			return new WebIO;
		}
	}
}

/**
 * WEB常规IO
 */
class WebIO implements IOInterface {
	/**
	 * JSONP回调函数名
	 * @var string
	 */
	private $_jsonpCallback = '';

	/**
	 * 输入
	 * @return array 接收到的数据
	 */
	public function input() {
		if(isset($_REQUEST['callback']) && substr($_REQUEST['callback'], 0, 6) == 'jQuery') {
			$this->_jsonpCallback = $_REQUEST['callback'];
		}
		if( $_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['CONTENT_TYPE'] !='application/octet-stream'  ){
			return array_merge( $_POST , $_GET );
		}
		return $_GET;
	}

	/**
	 * 输出
	 * @param array $data 要输出的数据
	 */
	public function output($data) {
		if($data !== null) {
			if(!headers_sent()) {
				header('Content-Type: '. ($this->_jsonpCallback ? 'text/javascript' : 'application/json') .'; charset=utf-8');
			}
			$data = is_array($data) && empty($data) ? (object)$data : $data;
			$data = json_encode($data);
			echo ($this->_jsonpCallback ? "{$this->_jsonpCallback}({$data})" : $data);
		}
	}
}

/**
 * REST协议接口类
 */
class RestIO implements IOInterface {
	/**
	 * 过滤器列表
	 * @var array
	 */
	private $_filters;

	/**
	 * 构造函数
	 */
	public function __construct() {
		$this->_filters = array();
		if(isset($_SERVER['HTTP_IO_FILTERS'])) {
			header("io-filters: {$_SERVER['HTTP_IO_FILTERS']}");
			$arr = (array)explode(',', $_SERVER['HTTP_IO_FILTERS']);
			foreach($arr as $item) {
				$cls = ucfirst($item) . "IOFilter";
				if(class_exists($cls)) {
					$this->_filters[] = $cls;
				}
			}
		}
	}

	/**
	 * 过滤处理
	 * @param string $data 要处理的数据
	 * @param string $op 操作类型 input:输入 output:输出
	 * @return string 处理后的结果
	 */
	private function _apply($data, $op) {
		if($data && $this->_filters) {
			$filters = ($op == 'input' ? array_reverse($this->_filters) : $this->_filters);
			foreach($filters as $cls) {
				$data = call_user_func(array($cls, 'apply'), $data, $op);
			}
		}
		return $data;
	}

	/**
	 * 输入
	 * @return array 接收到的数据
	 */
	public function input() {
		$data = file_get_contents('php://input');
		$data = $this->_apply($data, __FUNCTION__);
		$param= (array)json_decode($data, true);
		if( !$param ){
			App::$log->add(Log::ERROR,"Reset-IO-json_decode-fail://raw-param:{$data}");
		}
		return $param;
	}

	/**
	 * 输出
	 * @param array $data 要输出的数据
	 */
	public function output($data) {
		if(!headers_sent()) {
			header('Content-Type: application/json; charset=utf-8');
		}
		$data = is_array($data) && empty($data) ? (object)$data : $data;
		$data = json_encode($data);
		$data = $this->_apply($data, __FUNCTION__);
		echo $data;
	}
}

/**
 * CLI模式IO类
 */
class CliIO implements IOInterface {
	/**
	 * 输入
	 * @return array 接收到的数据
	 */
	public function input() {
		$data = array();
		if($_SERVER['argv'][1]) {
			parse_str($_SERVER['argv'][1], $data);
		}
		return $data;
	}

	/**
	 * 输出
	 * @param array $data 要输出的数据「这里不做任何处理」
	 */
	public function output($data) {}
}

/**
 * Base64过滤器
 */
class Base64IOFilter implements IOFilterInterface {
	/**
	 * 过滤处理
	 * @param string $data 要处理的数据
	 * @param string $op 操作类型 input:输入 output:输出
	 * @return string 处理后的结果
	 */
	public static function apply($data, $op) {
		return ($op == 'input' ? base64_decode($data) : base64_encode($data));
	}
}

/**
 * Zlib压缩过滤器
 */
class ZlibIOFilter implements IOFilterInterface {
	/**
	 * 过滤处理
	 * @param string $data 要处理的数据
	 * @param string $op 操作类型 input:输入 output:输出
	 * @return string 处理后的结果
	 */
	public static function apply($data, $op) {
// 		if($op == 'output' && !headers_sent()) {
// 			header('Content-Type: application/x-compress');
// 		}
		return ($op == 'input' ? gzuncompress($data) : gzcompress($data));
	}
}