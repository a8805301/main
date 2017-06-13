<?php
/**
 * 应用事件处理类定义文件
 * @author weicky
 * @package GameFactory
 */

/**
 * 应用事件处理类
 */
class App_Event {
	/**
	 * 初始化事件(设置配置、初始化操作)
	 */
	public static function start() {
		global $_DEBUG;
		date_default_timezone_set('PRC');
		include_once(App::$path . 'lib/functions.php');
		include_once(App::$path . 'lib/class.cmcc.php');
		include_once(App::$path . 'lib/class.bylog.php');
		//WEB模式下对自动转义的变量取消转义
		if(get_magic_quotes_gpc() && PHP_SAPI != 'cli') {
			if(!empty($_GET)) {
				foreach($_GET as &$v) {
					$v = unesc($v);
				}
			}
			if(!empty($_POST)) {
				foreach($_POST as &$v) {
					$v = unesc($v);
				}
			}
			if(!empty($_COOKIE)) {
				foreach($_COOKIE as &$v) {
					$v = unesc($v);
				}
			}
		}
		//对黑名单中的IP进行屏蔽
		$ip = getip();
		if(in_array($ip, (array)CFG('blacklist', 'ip'))) {
			App::$log->add(Log::DEBUG, "IP {$ip} in blacklist!");
			__exit("Fatal error: Call to undefined function fuck() in /home/daddy/index.php on line 43");
		}
	}

	/**
	 * 数据获取后事件(可做数据安全验证)
	 */
	public static function input() {
		global $_INPUT, $_DEBUG;
		include_once(App::$path . 'lib/class.compatible.php');
		Compatible::input();
		//跟踪用户请求
		if(PHP_SAPI == 'cli') {
			return;
		}
		$_DEBUG['TRACE'] = false;
		if(isset($_INPUT['__debug']) && $_INPUT['__debug']) {
			$_DEBUG['TRACE'] = true;
		} else {
			$cfg = (array)CFG('trace');
			$flags = false;
			if(!$cfg || (empty($cfg['ssid']) && empty($cfg['ip']))) {
				return;
			}
			if(!$flags && !empty($cfg['ssid']) && !empty($_INPUT['ssid']) && $_INPUT['ssid'] == $cfg['ssid']) {
				$flags = true;
			}
			if(!$flags && !empty($cfg['ip']) && !empty($_INPUT['ip']) && $_INPUT['ip'] == $cfg['ip']) {
				$flags = true;
			}
			if(!$flags && !empty($cfg['ip']) && ($_SERVER['HTTP_X_FORWARDED_FOR'] == $cfg['ip'] || $_SERVER['REMOTE_ADDR'] == $cfg['ip'])) {
				$flags = true;
			}
			$_DEBUG['TRACE'] = $flags;
		}
		if($_DEBUG['TRACE'] && empty($_INPUT['__admin'])) {
			$_DEBUG['TRACE_SEQ'] = rand(1, 99999999);
			$cfg	= CFG('@core', 'server.phpudp');
			$req	= http_build_query($_INPUT);
			$len	= strlen($req);
			$t		= microtime(true);
			$i		= 0;
			do {
				$data	= array(
					'type'	=> 2,
					'op'	=> 'in',
					's'	=> $_DEBUG['TRACE_SEQ'],
					't'		=> $t,
					'a'		=> $_INPUT['action'],
					'd'		=> substr($req, $i, 1800),
				);
				CMCC::getInstance($cfg[0], $cfg[1])->customReport(json_encode($data));
				$i += 1800;
			} while($i < $len);
		}
	}

	/**
	 * 分发后事件(可做权限验证)
	 */
	public static function dispatch() {
		$blacklist	= CFG('router','blacklist');
		$controller	= App::$action[0].".".App::$action[1];
		if($blacklist && in_array($controller,$blacklist)) {
			App::$action = array('debug','returnNull');
		}
	}

	/**
	 * 数据返回前事件(修改结果)
	 */
	public static function output() {
		global $_INPUT, $_OUTPUT, $_DEBUG;
		//支持跨域
		header("Access-Control-Allow-Origin: *");
		//返回结果兼容处理
		if(class_exists('Compatible', false)) {
			Compatible::output();
		}
		//跟踪用户请求返回结果
		if(PHP_SAPI == 'cli') {
			return;
		}
		if($_DEBUG['TRACE']) {
			$cfg	= CFG('@core', 'server.phpudp');
			$resp	= http_build_query($_OUTPUT);
			$len	= strlen($resp);
			$t		= microtime(true);
			$i		= 0;
			do {
				$data	= array(
					'type'	=> 2,
					'op'	=> 'out',
					's'		=> $_DEBUG['TRACE_SEQ'],
					't'		=> $t,
					'a'		=> $_INPUT['action'],
					'd'		=> substr($resp, $i, 1800),
				);
				CMCC::getInstance($cfg[0], $cfg[1])->customReport(json_encode($data));
				$i += 1800;
			} while($i < $len);
		}
	}

	/**
	 * 结束事件
	 */
	public static function finish() {
		if( App::$action[1] == 'getAssistiveBallCfg' ){
			return ;
		}
		global $_INPUT,$_OUTPUT;
		//curl调用次数统计
		$timeInterval = microtime(true) - App::$rtime;
		$cfg = CFG('@core', 'server.monitorsrv');
		$data = array(
			'type' => 4,
			'action' => App::$controllerName.'.'.App::$action[1],
			'timeInterval' => $timeInterval,
			'il'=>strlen(serialize($_INPUT)),//bytes
			'ol'=>strlen(serialize($_OUTPUT)),//bytes
		);
		CMCC::getInstance($cfg[0], $cfg[1])->customReport(json_encode($data));
	}

	/**
	 * 错误事件
	 * @param ErrorException $e 异常对象
	 */
	public static function error($e) {
		$file = $e->getFile();
		$line = $e->getline();
		$code = $e->getCode();
		global $_INPUT;
		$apiTypeList = array(
			'__admin' => 'Admin_',
			'__api' => 'Api_',
			'__cmsapi' => 'Cmsapi_',
			'df_partner' => 'Partner_',
			'df_shangjia' => 'Shangjia_',
		);
		$apiType = "无";
		foreach ($apiTypeList as $key => $val){
			if(isset($_INPUT[$key])){
				$apiType = $val;
				break;
			}
		}
		$message = str_replace("'",'',$e->getMessage());
		$ip = get_server_ip();
		$clientip = getip();
		if (in_array($code,array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR))) {
			$cfg	= CFG('@core', 'server.dataserver');
			$data	= array(
				'type' => 3,
				'file' => $file,
				'line' => $line,
				'code' => $code,
				'message' => $message,
				'area' => App::$area,
				'action'=>App::$action[0].'.'.App::$action[1],
				'apiType' => $apiType,
				'ip' => "{$ip},{$clientip}",
			);
			CMCC::getInstance($cfg[0], $cfg[1])->customReport(json_encode($data));
		}
	}
	/**
	 * 远程日志
	 * @param int $level
	 * @param string $msg
	 * 注意：此函数不允许使用App::$log方法打日志，这会陷入死循环
	 */
	public static function remotelog($level,$msg){
		static $udp_socket = NULL;
		if( !$udp_socket ){
			$netinfo = CFG("@core","server.logudp");
			if( $netinfo ){
				$udp_socket = stream_socket_client("udp://{$netinfo[0]}:{$netinfo[1]}", $error_code, $error_message, 1);
				if (!$udp_socket) {
					return ;
				}
			}else{
				return ;
			}
		}
		$msg = sprintf("%02d%s",$level,$msg);
		fwrite($udp_socket, $msg);
	}

}