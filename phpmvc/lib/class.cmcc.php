<?php
/**
 * 云监控客户端库: Cloud Monitor Center Client library
 * @author SuperMan
 */
class CMCC {
	/**
	 * 实例列表
	 * @var array
	 */
	private static $_instances = array();

	/**
	 * 服务IP
	 * @var string
	 */
	private $_ip;

	/**
	 * 服务端口
	 * @var int
	 */
	private $_port;

	/**
	 * 套接字资源
	 * @var resource
	 */
	private $_sock;

	/**
	 * 错误码
	 * @var int
	 */
	private $_errno;

	/**
	 * 错误描述
	 * @var string
	 */
	private $_error;

	/**
	 * 获取对象实例(单例模式)
	 * @param string $ip 服务IP
	 * @param int $port 服务端口
	 * @return CMCC
	 */
	public static function getInstance($ip='127.0.0.1', $port=6345) {
		$id = "{$ip}:{$port}";
		if(!isset(self::$_instances[$id])) {
			self::$_instances[$id] = new CMCC($ip, $port);
		}
		return self::$_instances[$id];
	}

	/**
	 * 删除单例对象
	 * @param mixed $ip IP地址或要删除的单例对象(注意：如果传递CMCC对象，本函数调用结束后，单例对象不会马上被删除掉，需要手动释放实参方可)
	 * @param int $port 端口
	 */
	public static function deleteInstance($ip='127.0.0.1', $port=6345) {
		if(is_object($ip) && is_a($ip, __CLASS__)) {
			$id = $ip->getIp() . ':' . $ip->getPort();
			unset($ip); //释放对象引用(使计数减1)
		} else {
			$id = "{$ip}:{$port}";
		}
		if(isset(self::$_instances[$id])) {
			unset(self::$_instances[$id]);
		}
	}

	/**
	 * 构造函数
	 * @param string $ip 服务IP
	 * @param int $port 服务端口
	 */
	public function __construct($ip, $port) {
		$this->_ip = $ip;
		$this->_port = $port;
		$this->_sock = null;
		$this->_errno = 0;
		$this->_error = '';
	}

	/**
	 * 析构函数(用于关闭Socket资源)
	 */
	public function __destruct() {
		if($this->_sock) {
			fclose($this->_sock);
		}
	}

	/**
	 * 创建Socket资源
	 */
	private function _createSocket() {
		$this->_sock = @stream_socket_client("udp://{$this->_ip}:{$this->_port}", $this->_errno, $this->_error, 3);
		return $this->_sock;
	}

	/**
	 * 发送数据
	 * @param string $msg 数据报消息
	 * @return bool 是否成功
	 */
	private function _send($msg) {
		if(!$this->_sock) {
			$flag = $this->_createSocket();
		}
		if(!$this->_sock) {
			$err = error_get_last();
			if($err) {
				$this->_errno = $err['type'];
				$this->_error = $err['message'];
			}
			return false;
		}
		$flag = fwrite($this->_sock, $msg);
		if($flag === false) {
			$err = error_get_last();
			if($err) {
				$this->_errno = $err['type'];
				$this->_error = $err['message'];
			}
			return false;
		} else {
			$this->_errno = 0;
			$this->_error = '';
			return true;
		}
	}

	/**
	 * 上报数据(游戏ID、统计类型ID，请请联系云监控中心申请)
	 * @param int $game 游戏ID
	 * @param string $type 统计类别ID
	 * @param array $items 统计项与值的数组
	 * @return bool 是否成功 
	 */
	public function report($game, $type, $items) {
		$time = time();
		if(empty($game) || empty($type) || empty($items) || !is_array($items)) {
			return false;
		}
		$msg = "MT:t{$time}:g{$game}:{$type}" . json_encode($items);
		return $this->_send($msg);
	}

	/**
	 * 自定义上报
	 * @param string $data 自定义数据
	 * @return bool 是否成功
	 */
	public function customReport($data) {
		return $this->_send($data);
	}

	/**
	 * 获取错误码
	 */
	public function errno() {
		return $this->_errno;
	}

	/**
	 * 获取错误信息
	 */
	public function error() {
		return $this->_error;
	}

	/**
	 * 返回当前请求IP
	 * @return string IP地址
	 */
	public function getIp() {
		return $this->_ip;
	}

	/**
	 * 返回当前请求端口
	 * @return int 端口
	 */
	public function getPort() {
		return $this->_port;
	}
}
