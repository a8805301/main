<?php
/**
 * log udp传送管道sdk
 * 
 * 调用方式:
 * include Bylog.php //或者自身项目支持的自动加载方式引入类
 * 
 * $log['level'] = 'ERROR';
 * $log['错误类型'] = '登录失败';
 * $log['日志'] = 'abcdefghijklmg';
 * $log['xxxx'] = 'aaaaaa';
 * 
 * Bylog::write( $log );
 * 
 * $log = '我爱中国，中国爱我!';
 * Bylog::write( $log );
 * 
 * RexQiu
 * @since 2014/9/12
 * @modify jakquan
 * 
 * PS：每条日志信息最大建议不要超过2K字节(应该够用吧^+^)
 * 【查看日志 http://paycenterlog.oa.com/#/dashboard/file/qipailog.json】
 */
class Bylog{

	//传送方式
	const STYPE = "udp";
	/**
	 * 传送host地址
	 */
	const HOST = "127.0.0.1";

	//传送端口
	const PORT = "1105";

	/**
	 * socket错误码
	 * @var integer
	 */
	public $error_code = 0;

	/**
	 * socket错误信息
	 * @var string
	 */
	public $error_message = '';

	/**
	 * log sdk 实例
	 */
	private static $instance;

	/**
	 * udp socket
	 */
	private static $socket;

	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$error_code = 0;

		$error_message = '';

		if (!isset(self::$socket) || empty(self::$socket)) {
			self::$socket = stream_socket_client(self::STYPE.'://'.self::HOST.':'.self::PORT, $this->error_code, $this->error_message, 30);
		}
	}

	public function __destruct()
	{
		if (self::$socket) {
			fclose(self::$socket);
			self::$socket = null;
		}
	}
	/**
	 * 获得实例
	 * @return Bylog
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance) || empty(self::$instance)) {
			self::$instance = new Bylog();
		}
		return self::$instance;
	}

	/**
	 * 外部调用写
	 * @param  mixed $str 日志内容,不是数据则以msg为key，值为日志内容的一个数组方式上报
	 * @return array array('ret' => boolean,'msg'=>'结果') ret为false失败
	 */
	public static function write($str)
	{
		if( is_array($str) ){
			$str = json_encode($str);
		}
		
		$Bylog = Bylog::getInstance();

		$ret = $Bylog->_writeUDP($str);

		return array('ret' => $ret, 'msg'=> $Bylog->error_message);
	}

	/**
	 * 写UDP
	 * @param  string $str
	 * @return [type]
	 */
	protected function _writeUDP($str)
	{
		if (empty(self::$socket)) {
			return FALSE;
		}

		$ret = fwrite(self::$socket, $str);

		//写入成功判断
		if ($ret > 0) {
			return TRUE;
		}else{
			return FALSE;
		}
	}
}//end class Bylog