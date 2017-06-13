<?php
/**
 * 转义字符串
 * @param string $str 要转义的字符串
 * @return string 转义后的字符串
 */
function esc($str) {
	return addslashes(strval($str));
}
/**
 * 取消转义
 * @param mixed $val 要转义的变量
 * @return mixed 转义后的结果
 */
function unesc($val) {
	if (is_array($val)) {
		foreach($val as &$item) {
			$item = unesc($item);
		}
	} elseif(is_string($val)) {
		$val = stripslashes($val);
	}
	return $val;
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return string 状态描述字符串
 */
function http_status($code) {
	static $_status = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',
		// Success 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Moved Temporarily ', // 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		// 306 is deprecated but reserved
		307 => 'Temporary Redirect',
		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		509 => 'Bandwidth Limit Exceeded',
	);
	if(isset($_status[$code])) {
		header('HTTP/1.1 '.$code.' '.$_status[$code]);
		header('Status:'.$code.' '.$_status[$code]); // 确保FastCGI模式下正常
		return $_status[$code];
	}
	return '';
}

/**
 * 根据某唯一性字段重组数组索引
 * @param array $data 数组
 * @param string $field 数组某个唯一性字段
 * @return array
 */
function format_array($data, $field){
    $tmp = array();
    foreach($data as $key=>$val) {
        $tmp[$val[$field]] = $val;
    }
    $data = $tmp;
    unset($tmp);
    return $data;
}

/**
 * 获取应用的地区ID
 * @param int $appid 应用ID
 * @return int 地区ID
 */
function get_region_id($appid) {
	return floor(intval($appid) / 100000);
}
/**
 * 获取应用id范围
 * @param int $region 地区id
 * @return array
 */
function get_appid_range($region) {
	$region = intval($region);
	$start  = $region*100000;
	$end    = ($region+1)*100000-1;
	return array($start,$end);
}

/**
 * 获取应用的客户端类型ID
 * @param int $appid 应用ID
 * @return int 客户端类型ID
 */
function get_client_id($appid) {
	return floor((intval($appid) % 100000) / 1000);
}
/**
 * 获取次日零点时间戳
 * @return unixtime
 */
function get_next_day_zero(){
	$time	=	time()+86400;
	return mktime(0 , 0 , 0 , date('m',$time) , date('d',$time) , date('Y',$time));
// 	return ($time - $time%86400 - 8*60*60) + 86400;
}
/**
 * 获取指定日期零点时间戳
 * @return unixtime
 */
function get_day_zero($time=NULL){
	if( $time == NULL ){
		$time	=	time();
	}	
	return mktime(0 , 0 , 0 , date('m',$time) , date('d',$time) , date('Y',$time));
}
function gen_sig($params,$secret){
	ksort( $params, SORT_STRING );
	foreach( $params as $key => $value){
		if($key!='_'&&$key!='callback'&&$key!='__admin'){
			$str .= ($key . '=' . $value);
		}
	}
	$str = (string)$str;
	$str =  md5(md5($str).$secret);
	return (string)$str;
}

/**
 * 创建MoneyServer操作对象
 * @return MServer
 */
function mserver($netinfo=false,$timeout=0) {
	if(!class_exists('MServer', false)) {
		//require_once(App::$path . 'lib/class.encryptdecrypt.php');
		require_once(App::$path . 'lib/class.socketpacket.php');
		require_once(App::$path . 'lib/class.mserver.php');
	}
	if( $netinfo ){
		$mserver = new MServer($netinfo[0], $netinfo[1]);
		return $mserver;
	}
	$netinfo	= CFG('@core' , 'server.mserver');
	if( $timeout ){
		$mserver 	= new MServer($netinfo[0] , $netinfo[1],$timeout);
	}else{
		$mserver 	= new MServer($netinfo[0] , $netinfo[1]);
	}
	return $mserver;
}

/**
 * 创建DispatchServer操作对象
 * @return DispatchServer
 */
function gdserver($netinfo=false) {
	if(!class_exists('GDServer', false)) {
		require_once(App::$path . 'lib/class.gdserver.php');
	}
	if( $netinfo ){
		$gdserver = new GDServer($netinfo[0], $netinfo[1]);
		return $gdserver;
	}
	$netinfo	= CFG('@core' , 'server.dispatch');
	$gdserver 	= new GDServer($netinfo[0] , $netinfo[1]);
	return $gdserver;
}


/**
 * 创建静态MoneyServer操作对象
 * @return MServer
 */
function staticmserver($netinfo=false) {
	static $mserver;
	if( $mserver ){
		return $mserver;
	}
	if(!class_exists('MServer', false)) {
		//require_once(App::$path . 'lib/class.encryptdecrypt.php');
		require_once(App::$path . 'lib/class.socketpacket.php');
		require_once(App::$path . 'lib/class.mserver.php');
	}
	if( $netinfo ){
		$mserver = new MServer($netinfo[0], $netinfo[1]);
		return $mserver;
	}
	$netinfo	= CFG('@core' , 'server.mserver');
	$mserver 	= new MServer($netinfo[0] , $netinfo[1]);
	return $mserver;
}

/**
 * 使用cURL发送POST请求
 * @param string $url 请求地址
 * @param array $post POST数据数组
 * @param array $options HTTP选项数组
 * @param string $error 用于返回错误信息
 * @param int $errno 用于返回错误码
 * @param string $httpCode 用于返回响应的HTTP状态码
 * @return mixed 成功返回请求返回结果，失败返回flase
 */
function curl_post($url, $post=array(), $options=array(), &$error=false, &$errno=false, &$httpCode=false, $isupload=false) {
	$defaults = array(
		CURLOPT_POST			=> 1,
		CURLOPT_HEADER			=> 0,
		CURLOPT_URL				=> $url,
		CURLOPT_FRESH_CONNECT	=> 1,
		CURLOPT_RETURNTRANSFER	=> 1,
		CURLOPT_FORBID_REUSE	=> 1,
		CURLOPT_CONNECTTIMEOUT	=> 30,
		CURLOPT_TIMEOUT			=> 60,
		CURLOPT_POSTFIELDS		=> ($isupload || is_string($post)) ? $post : http_build_query($post),
	);
	$ch = curl_init();
	$result = '';
	if($ch) {
		foreach($options as $k=>$v) {
			$defaults[$k] = $v;
		}
		curl_setopt_array($ch, $defaults);
		$result = curl_exec($ch);
		if($result === false) {
			if($error !== false) {
				$error = curl_error($ch);
			}
			if($errno !== false) {
				$errno = curl_errno($ch);
			}
		}
		if($httpCode !== false) {
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}
		curl_close($ch);
	}
	return $result;
}

/**
 * 使用cURL发送GET请求
 * @param string $url 请求地址
 * @param array $post GET数据数组
 * @param array $options HTTP选项数组
 * @param string $error 用于返回错误信息
 * @param int $errno 用于返回错误码
 * @param string $httpCode 用于返回响应的HTTP状态码
 * @return mixed 成功返回请求返回结果，失败返回flase
 */
function curl_get($url, $get=array(), $options=array(), &$error=false, &$errno=false, &$httpCode=false) {
	$defaults = array(
		CURLOPT_URL				=> $url. (strpos($url, '?') === FALSE ? '?' : '&'). http_build_query($get),
		CURLOPT_HEADER			=> 0,
		CURLOPT_RETURNTRANSFER	=> TRUE,
		CURLOPT_CONNECTTIMEOUT	=> 5,
		CURLOPT_TIMEOUT			=> 10,
	);
	$ch = curl_init();
	$result = '';
	if($ch) {
		foreach($options as $k=>$v) {
			$defaults[$k] = $v;
		}
		curl_setopt_array($ch, $defaults);
		
		$result = curl_exec($ch);
		if($result === false) {
			if($error !== false) {
				$error = curl_error($ch);
			}
			if($errno !== false) {
				$errno = curl_errno($ch);
			}
		}
		if($httpCode !== false) {
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}
		curl_close($ch);
	}
	return $result;
}
/**
 * 获取客户端IP
 * @return string IP地址
 */
function getip(){
	$keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
	foreach($keys as $key) {
		if(!empty($_SERVER[$key]) && checkip($_SERVER[$key])) {
			return $_SERVER[$key];
		}
	}
	return '0.0.0.0';
}
/**
 * 获取网通代理或教育网代理带过来的客户端IP
 *
 * @return        string/flase    IP串或false
 */
function qvia2ip($qvia) {
	if (strlen ( $qvia ) != 40) {
		return false;
	}
	$ips = array (hexdec ( substr ( $qvia, 0, 2 ) ), hexdec ( substr ( $qvia, 2, 2 ) ), hexdec ( substr ( $qvia, 4, 2 ) ), hexdec ( substr ( $qvia, 6, 2 ) ) );
	$ipbin = pack ( 'CCCC', $ips [0], $ips [1], $ips [2], $ips [3] );
	$m = md5 ( 'QV^10#Prefix' . $ipbin . 'QV10$Suffix%' );
	if ($m == substr ( $qvia, 8 )) {
		return implode ( '.', $ips );
	} else {
		return false;
	}
}
/**
 * 验证ip地址
 * @param        string    $ip, ip地址
 * @return        bool    正确返回true, 否则返回false
 */
function checkip($ip) {
	$ip = trim ( $ip );
	$pt = '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';
	if (preg_match ( $pt, $ip ) === 1) {
		return true;
	}
	return false;
}

/**
 * 获取数据中心上报对象
 * @return dc
 */
function dcenter() {
	static $dc = null;
	if(!class_exists('dc')) {
		include(App::$path . 'lib/class.dc.php');
	}
	if($dc == null) {
		$dc = new dc();
	}
	return $dc;
}
/**
 * 修改配置文件
 * @param array $option 值
 * @param string $section 文件名
 * @param bool $replace 是否直接替换旧值
 * @return bool
 */
function writeconfig(  $option , $section, $replace=false ){
	if( empty($option) ) return false;
	$env	= ($section{0} == '@' ? true : false);
	$section= $env?substr($section, 1):$section;
	$name	= $section . ($env ? '.' . App::$env : '');
	$file	= App::$path . "config/{$name}.php";
	if($replace){
		$content = $option;
	}else{
		$content = CFG($section);
		foreach ( $option as $key=>$value ){
			$content[$key] = $value;
		}
	}
	if(file_put_contents($file, "<?php\nreturn " . var_export($content , true ) . ';')){
		return true;
	}
	return false;
}

/**
 * 根据文件名删除配置文件
 */
function deleteConfig($name){
	if(!$name) return false;
	$file = App::$path . "config/{$name}.php";
	if(!is_file($file)) return true;
	if(unlink($file)) return true;
	return false;
}
/**
 * 验证电话号码
 *
 * @param        string        $phone, 电话号码
 * @param         string        $type, CHN中国大陆电话号码, INT国际电话号码
 * @return        bool        正确返回true, 错误返回false
 */
function istelephone($phone, $type = 'CHN') {
	$ret = false;
	switch($type){
		case "CHN":
			$phone  = preg_replace('/(\D)/', '' , $phone);
			$phone	= preg_replace('/^86/', '', $phone);
			$ret 	= (preg_match("/^(13[0-9]{9}$)|(14[0-9]{9}$)|(15[0|1|2|3|4|5|6|7|8|9]\d{8}$)|(18[0|1|2|3|4|5|6|7|8|9]\d{8})|(17[0-9]{9})/", trim($phone)) ? true : false);
			break;
		case "INT":
			$ret = (preg_match("/^((\(\d{3}\))|(\d{3}\-))?\d{6,20}$/", trim($phone)) ? true : false);
			break;
	}

	if ($ret === false) {
		return false;
	}

	return true;
}

/**
 * 判断博雅号格式是否正确
 */
function isbynum( $bynum ) {
	if( preg_match('/[\x{4e00}-\x{9fa5}]+/u', $bynum) ) {//字符串中包含汉字
		return false;
	}
	$bynum = strtolower( trim($bynum) );
	if( strlen($bynum)<6 or strlen($bynum)>50 ) { //长度不在6—50范围
		return false;
	}
	$pt = '/^[a-zA-Z]+[a-z0-9_\-]+$/';
	if ( preg_match($pt, $bynum) === 1 ) {
		return true;
	}

	return false;
}
/**
 * 判断邮箱格式
 */
function isemail( $email ){
	$email = strtolower( trim($email) );
	$pt = '/^[a-z0-9_\-]+(\.[_a-z0-9\-]+)*@([_a-z0-9\-]+\.)+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)$/';
	if ( preg_match($pt, $email) === 1 ) {
		return true;
	}

	return false;
}
/**
 * 博雅账号类型判断
 * @param string $account
 * @param string $byType
 */
function isbyaccount( $account, &$byType ) {
	if( empty( $account ) ) return false;
	else if( isemail( $account ) ) {
		$byType = 'EMAIL';
		return true;
	}else if( isbynum( $account ) ) {
		$byType = 'BYNUM';
		return true;
	}else if( istelephone( $account ) ) {
		$byType = 'PHONE';
		return true;
	}
	return false;
}

/**
 * 常见web验证
 * @param array $arr
 * @param array $raw_arr 原始数据
 * @return array
 */
function verify( $arr , $raw_arr ){
	$ret = array();
	foreach ($arr as $v){
		switch( $v['type'] ){
			case 'int':
				$ret[$v['name']] = isset($raw_arr[$v['name']])?intval($raw_arr[$v['name']]):0;
				break;
			case 'float':
				$ret[$v['name']] = isset($raw_arr[$v['name']])?floatval($raw_arr[$v['name']]):0.0;
				break;
			case 'string':
				$ret[$v['name']] = isset($raw_arr[$v['name']])?trim($raw_arr[$v['name']]):'';
				break;
			default:
				$ret[$v['name']] = null;
		}
	}
	return $ret;
}

/**
 * 转换网络标识为网络名称
 * @param int $net 网络标识
 * @return string 网络名称
 */
function get_network_mode($net) {
	switch(intval($net)) {
		case 1:
			return 'WIFI';
		case 2:
			return '2G';
		case 3:
			return '3G';
		case 4:
			return '4G';
		default:
			return '';
	}
}

/**
 * 转换电信运营商标识到名称
 * @param int $operator 电信运营商标识
 * @return string 电信运营商名称
 */
function get_network_operator($operator) {
	switch(intval($operator)) {
		case 1:
			return '中国移动';
		case 2:
			return '中国联通';
		case 3:
			return '中国电信';
		default:
			return '';
	}
}
/**
 * 获取运营商
 * @param string $imsi
 * @return int 1-中国移动 2-中国联通 3-中国电信 0-未知
 */
function get_sms_operator($imsi){
	if( empty($imsi) ) return 0;
	$prefix	= substr(trim($imsi), 0,5);
	switch ($prefix){
		case "46000":
		case "46002":
		case "46007":
			return 1;
		case "46001":
		case "46006":
		case "46009":
			return 2;
		case "46003":
		case "46005":
		case "46011":
			return 3;
		default:
			return 0;
	}
}

/**
 * 过滤掉特殊字符
 * @param string 需要过滤字符串
 * @return string
 */
function filter_special_char($str) {
	$filters = array('~','!','@','#','$','%','^','&','(',')','+','=','{','}','|','\\',']','[','"','\'','\r','\n',' ','?','/','<','>',';',':');
	$str = str_replace($filters, '', $str);
	return $str;
}

/**
 * 将字符串类型的版本号转化为整型数字
 * @param string $value 版本号字符串(2.0.1)
 * @return int 版本号的整型数字
 */
function ver2long($value) {
	list($major, $minor, $release) = explode('.', $value);
	$major		= intval($major);
	$minor		= intval($minor);
	$release	= intval($release);
	return $major * 1000000 + $minor * 1000 + $release;
}
/**
 * 获取大厅版本号
 * @param string $value
 * @return number
 */
/*function hallver2long($value){
	list($major, $minor, $release,$hall) = explode('.', $value);
	return intval($hall);
}*/

/**
 * 获取大厅版本号
 * @param string $value
 * @return number
 */
function hallver2long($value){
	$value = explode('.', $value);
	$index = count($value)-1;
	return intval($value[$index]);
}

/**
 * 将整型格式的版本号转化为原本的字符串形式
 * @param int $value 版本号的整型数字(2000001)
 * @return string 版本号字符串(2.0.1)
 */
function long2ver($value) {
	$value		= intval($value);
	$major		= intval($value / 1000000);
	$minor		= intval(($value % 1000000) / 1000);
	$release	= intval($value % 1000);
	return "{$major}.{$minor}.{$release}";
}

/**
 * 获取缓存过期时间
 */
function get_cache_expire_time( $day = 1 ) {
	return strtotime('today') + (86400 * intval($day)) - time();
}

/**
 * 计算分页列表
 * @param int $current 发前页码
 * @param int $total 总页数
 * @param int $size 最大显示页码数
 * @return array 页码列表
 */
function pager($current, $total, $size) {
	$current	= intval($current);
	$total		= intval($total);
	$size		= intval($size);
	if(!$total || !$size) {
		return array();
	}
	if ($total > $size) {
		if ($size % 2 == 0) {
			$left = $size / 2 - 1;
			$right = $size / 2;
		} else {
			$left = $right = floor($size / 2);
		}
		if ($current <= $left + 1) {
			return range(1, $size, 1);
		} elseif ($current >= $total - $right) {
			return range($total - $size + 1, $total, 1);
		} else {
			return range($current - $left, $current + $right, 1);
		}
	} else {
		return range(1, $total, 1);
	}
}
/**
 * 创建充值对象
 * @return Voucher
 */
function voudcher() {
	if(!class_exists('Voucher', false)) {
		//require_once(App::$path . 'lib/class.encryptdecrypt.php');
		require_once(App::$path . 'lib/class.voucher.php');
	}
	return new Voucher();
}

/**
 * 判断客户端类型是否为PC桌面应用
 * @param int $app 应用ID
 * @return bool
 */
function client_is_desktop($app) {
	return get_client_id($app) == 7;
}

/**
 * 判断客户端类型是否为WEB应用
 * @param int $app 应用ID
 * @return bool
 */
function client_is_web($app) {
	return get_client_id($app) == 4;
}

/**
 * 判断客户端类型是否为PC(桌面应用+WEB应用)
 * @param int $app 应用ID
 * @return bool
 */
function client_is_pc($app) {
	return ($c = get_client_id($app)) && in_array($c, array(4, 7));
}

/**
 * 判断客户端类型是否为安卓应用
 * @param int $app 应用ID
 * @return bool
 */
function client_is_android($app) {
	return get_client_id($app) == 3;
}

/**
 * 判断客户端类型是否为iPhone应用
 * @param int $app 应用ID
 * @return bool
 */
function client_is_iphone($app) {
	return get_client_id($app) == 1;
}

/**
 * 判断客户端类型是否为iPad应用
 * @param int $app 应用ID
 * @return bool
 */
function client_is_ipad($app) {
	return get_client_id($app) == 2;
}

/**
 * 判断客户端类型是否为iOS应用(iPhone+iPad)
 * @param int $app 应用ID
 * @return bool
 */
function client_is_ios($app) {
	return ($c = get_client_id($app)) && in_array($c, array(1, 2));
}

/**
 * 判断客户端类型是否为移动应用
 * @param int $app 应用ID
 * @return bool
 */
function client_is_mobile($app) {
	return ($c = get_client_id($app)) && in_array($c, array(1, 2, 3));
}

/**
 *获取各个游戏中可用的roomlevel
 *@param int $game_id 默认值为0，获取所有的游戏的roomlevel；
 *           为某个游戏的id时，获取该游戏内的房间等级;
 *			 不合法id时返回空array()
 *@return array
 */
function enable_roomlevel($game_id = 0) {
	global $_CONFIG;
	$game_id 	= intval($game_id);
	$cfg_games 	= Model::getInstance('admin.deploy')->C('games');
	$cfg_level 	= CFG('system','roomlevel');
	$matchModel = Model::getInstance("admin.matchconfig");
	$return 	= array();
	if($game_id === 0) {
		foreach ($cfg_games as $key => $value) {
			$return[$key] = array();
			foreach ($cfg_level as $k => $v) {
				$levelInfo = $matchModel->get($key, 0, $k);
				if(!empty($levelInfo)) {
					$return[$key][$k] = 0;
				}
			}
		}
	} else {
		$return[$game_id] = array();
		foreach ($cfg_level as $k => $v) {
			$levelInfo = $matchModel->get($game_id, 0, $k);
			if(!empty($levelInfo)) {
				$return[$game_id][$k] = 0;
			}
		}		
	}
	return $return;
}
/**
 * 客户端跳转代码
 */
function jumpcode(){
	$jumpcodes = CFG('jumpcode', 'oldjump');
	return $jumpcodes;
}

/**
 * 伪异步调用
 * @param array $data 支持二级数组
 * @return boolean
 */
function syncCall( $data ){
	if( !$data ) return false;
	if( App::$env == 'dev' ){
		$socket	= fsockopen('127.0.0.1',80,$errno,$errstr , 30 );
		if( !$socket ){
			return false;
		}
		$http	.= "POST /index.php HTTP/1.1\r\n";
		$http	.= "Host: 365.by.com\r\n";
	}else{
		$socket	= fsockopen('127.0.0.1',80,$errno,$errstr , 30 );
		if( !$socket ){
			return false;
		}
		$http	.= "POST /dfqp/index.php HTTP/1.1\r\n";
		$http	.= "Host: mvsnspus01.ifere.com\r\n";
	}
	
	foreach ($data as $k=>$v){
		if( is_array($v) ){
			foreach ($v as $value){
				$post .= "{$k}[]=$value&";
			}
		}else{
			$post	.= "$k=$v&";
		}
	}
	$post 	= substr($post, 0 , strlen($post)-1);
	$http	.= "Content-Length: ".strlen($post)."\r\n";
	$http 	.= "Content-Type: application/x-www-form-urlencoded\r\n\r\n";
	$http	.= "Connection: Close\r\n\r\n";
	$http 	.= $post;
	fwrite($socket, $http);
	fclose($socket);
	return true;
}

/**
 * 获取剩余时间字符串
 * @param string $endtime
 * @return string
 */
function getTimeLeft($endtime){
	$now = time();
	$endtime = strtotime($endtime);
	$strtime = '';
	$time = $endtime-$now;

	if ($time<=0) {
		return '0天0小时0分钟';
	}
	if($time >= 86400){
		$strtime .= intval($time/86400).'天';
		$time = $time % 86400;
	}
	if($time >= 3600){
		$strtime .= intval($time/3600).'小时';
		$time = $time % 3600;
	}
	if($time >= 60){
		$strtime .= intval($time/60).'分钟';
		$time = $time % 60;
	}else{
		$strtime .= '';
	}

	return $strtime;
}

/**
 * 获取缓存的实例
 * @param $type 要实例化的缓存方式
 * @param $store 要存储的位置
 * @return object 返回一个缓存对象
 */
function cache($type='db', $store='main.cache'){
	include_once(App::$path."lib/class.cache.php");
	return cache::instance($type, $store);
}

/**
 * 判断是不是中文
 * @author 刘紫华 2015-03-10
 * @param $str
 */
function checkChinese($str){
	if (preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$str)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 检查身份证号
 * @author 刘紫华 2015-04-28
 */
function checkidno($id_card) {
	if(strlen($id_card) == 18) {
		return idcard_checksum18($id_card);
	} elseif((strlen($id_card) == 15)) {
		$id_card = idcard_15to18($id_card);
		return idcard_checksum18($id_card);
	} else {
		return false;
	}
}
// 计算身份证校验码，根据国家标准GB 11643-1999
function idcard_verify_number($idcard_base) {
	if(strlen($idcard_base) != 17) {
		return false;
	}
	//加权因子
	$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
	//校验码对应值
	$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
	$checksum = 0;
	for($i=0; $i<strlen($idcard_base); $i++) {
		$checksum += substr($idcard_base, $i, 1) * $factor[$i];
	}
	$mod = $checksum % 11;
	$verify_number = $verify_number_list[$mod];
	return $verify_number;
}
// 将15位身份证升级到18位
function idcard_15to18($idcard){
	if(strlen($idcard) != 15){
		return false;
	}else{
		// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
		if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){
			$idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);
		}else{
			$idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
		}
	}
	$idcard = $idcard . idcard_verify_number($idcard);
	return $idcard;
}
	// 18位身份证校验码有效性检查
function idcard_checksum18($idcard){
	if(strlen($idcard) != 18) {
		return false;
	}
	$idcard_base = substr($idcard, 0, 17);
	if(idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
		return false;
	}else{
		return true;
	}
}

/**
 * 从博雅公共中心用户位置服务获取用户IP、手机号、ICCID对应的位置
 * @param string $value IP/手机号/ICCID的值
 * @param int $type 类型 1:IP 2:手机号 3:ICCID
 * @param int $mid 用户ID
 * @param int $app 用户ID
 * @return mixed 成功返回位置信息数组，否则返回false
 *  成功IP返回值：
 *     城市                                错误码                国家                                        省份
 *   {"city":"德阳","code":0,"country":"中国","province":"四川","query":"ip"}
 */
function boyaa_get_location($value, $type, $mid, $app) {
	$url	= CFG('boyaa', 'location_api');
	$ip		= "";
	$phone	= "";
	$iccid	= "";
	$bpid	= Model::getInstance('admin.deploy')->C('apps',array('appid'=>$app));
	$bpid 	= strval($bpid[1]);
	switch($type) {
		case 1: //IP
			$ip = urlencode($value);
			break;
		case 2: //手机号
			$phone = urlencode($value);
			break;
		case 3: //ICCID
			$iccid = urlencode($value);
			break;
		default:
			return false;
	}
	$url .= "ip={$ip}&phone={$phone}&iccid={$iccid}&bpid={$bpid}&uid={$mid}";
	$resp = curl_get($url, array(), array(
		CURLOPT_CONNECTTIMEOUT	=> 3,
		CURLOPT_TIMEOUT			=> 3,
	));
	if(!$resp) {
		return false;
	}
	$json = json_decode($resp, true);
	return (is_array($json) && !empty($json) ? $json : false);
}

/**
 * 获得电话号码归属地
 * @author 刘紫华 2015-07-23
 * @param $pno
 */
function getareabyphone($pno){
	include_once App::$path."lib/mobile/mobile.php";
	$mobile = new MobileInfo($pno);
	return $mobile->area;
}


/**
 * 获得IP归属地
 * @param string $ip 
 *  return array('country'=>'',//国家
				'province'=>'',//省份
				'city'=>'',//城市
				'operator'=>'',//运营商
				)
 */
function getareabyip($ip){
	static $ipdata;
	if( $ipdata == NULL ){
		include_once App::$path."lib/class.ipdata.php";
		$ipdata = new IpData(App::$path."lib/IpLocation/ipv4data.datx");
	}
	$ret	= $ipdata->find($ip);
	return $ret;
}

/**
 * logstorage存储日志数据
 * @param $arg 操作的命令数组
 * @return array or boolean insert 返回 boolean select 返回 array
 */
function storage($arg){
	include_once App::$path."lib/class.logstorage.php";
	$path = App::$path.'log';
	$ob   = new logstorage($path);
	$res  = $ob->query($arg);
	return $res;
}


/**
 * 数组排序回调函数(顺序)
 * @param $a,$b 数组前后键,$key排序字段,$mode排序方式
 */
function sort_m($a,$b) {
	$vals = array('sort' => 'asc',);
	while ( list($key, $val) = each($vals) ) {
		if ( strtolower($val) == "desc" ) {
			return ( $a["$key"] > $b["$key"] ) ? -1 : 1;
		}
		return ( $a["$key"] < $b["$key"] ) ? -1 : 1;
	}
}

/**
 * 数组排序回调函数(逆序)
 * @param $a,$b 数组前后键,$key排序字段,$mode排序方式
 */
function sort_n($a,$b) {
	$vals = array('sort' => 'desc',);
	while ( list($key, $val) = each($vals) ) {
		if ( strtolower($val) == "desc" ) {
			return ( $a["$key"] > $b["$key"] ) ? -1 : 1;
		}
		return ( $a["$key"] < $b["$key"] ) ? -1 : 1;
	}
}

/*数组按某键值排序*/
function array_sort_key($key, $data, $type='desc')
{
    $temp_data = array();
    foreach ($data as $v) {
        $temp_data[$v[$key]] = $v;
    }

    ($type=='desc') ? krsort($temp_data) : ksort($temp_data);
    $data = array_values($temp_data);
    return $data;
}


/**
 * 格式化手机号码
 * @param string $phone 手机号
 */
function format_phone($phone){
	$phone  = preg_replace('/(\D)/', '' , $phone);
	$phone	= preg_replace('/^86|\+86/', '', $phone);
	return $phone;
}
/**
 * 生成二维码数据(非文件)
 * @param string $data 二维码内容
 * @return string 返回base64_encode之后过的数据
 * 如何使用base64数据：<img src="data:image/png;base64,返回的数据"/>
 * @link https://en.wikipedia.org/wiki/Data_URI_scheme
 */
function qr_code_data($data){
	if( empty($data) ){
		return false;
	}
	require_once App::$path.'lib/class.qrcode.php';
	$tmpPath	= App::$path.'data/qr_'.time().'.png';
	QRcode::png($data,$tmpPath,'M',5);
	$fileContents= file_get_contents($tmpPath);
	unlink($tmpPath);
	return base64_encode($fileContents);
}
/**
 * 生成二维码(图片url，图片存档cdn)
 * @param string $data 
 * @param string $filename 文件名
 * @param string $size 尺寸(接收samll/middle/big)
 * @return string $url/false
 */
function qr_code_url($data,$filename,$size='middle'){
	if( empty($data) || empty($filename) ){
		return false;
	}
	$post['data'] 		= $data;
	$post['filename']	= $filename;
	$post['size']		= $size;
	$res	= curl_post(CFG("@core","cdn.qrcode"),$post);
	$ret	= json_decode($res,true);
	if( is_array($ret) && $ret['flag'] == 1 ){
		return CFG("@core","cdn.url")."images/qr/{$filename}.png";
	}
	return false; 
}

/**
 * 根据物品箱物品ID查询物品箱TYPE
 * @param  int $gatherid 物品箱物品ID
 * @return int $type     物品箱类型
 */ 
function gatherIdToType($itemId) {
	$gatherConfig    = CFG('gather' , 'type');
	$debris_type     = $gatherConfig['debris'];     //碎片物品箱类型
	$electronic_type = $gatherConfig['electronic']; //线上物品(电子商品)物品箱类型
	$giftbag_type    = $gatherConfig['giftbag'];    //礼包兑换物品箱类型
	$shangjia_type   = $gatherConfig['shangjia'];   //商家商品物品箱类型
	$props_type      = $gatherConfig['props'];      //普通道具物品箱类型
    if($itemId > 200000 && $itemId <=300000) {
        $type = $electronic_type; //线上物品(电子商品)奖励(ID大于200000且小于等于300000)	
    } elseif($itemId > 300000 && $itemId <=400000) {
        $type = $giftbag_type;    //礼包兑换类虚拟奖励(ID大于300000且小于等于400000)	
    } elseif($itemId > 400000 && $itemId <=500000) {
        $type = $debris_type;     //碎片合成类奖励(ID大于400000且小于等于500000)	
    } elseif($itemId > 100000 && $itemId <=200000) {
        $type = $shangjia_type;   //商家商品类奖励(ID大于100000且小于等于200000)	
    } else {
    	$type = $props_type;      //普通道具
    }
    return $type;
}

/**
 * 调试print输出规范
 * @param array $ret 
 * @return string
 */
function pre($ret){
	echo "<pre>";
    print_r($ret);
    echo "</pre>";
    exit();
}

/**
 * @desc 代理商地区，避免业务代码中多出写，新增代理商地区修改复杂
 */
function agentRegion(){
	$regionIds = array_keys(Model::getInstance('admin.deploy')->C('regions'));
	$noProxy = array(3, 4, 5, 6, 8, 12, 13, 14, 19);//未接入代理商的地区id
	$res = array_diff($regionIds, $noProxy);
	return array_values($res);
}

/**
 * @desc 代理商日志
 * @param $path
 * @param $logdata
 */
function agentlog($path, $logdata){
	$nowStr	= date('Y-m-d H:i:s');
	list( $year , $month ) = explode('-', $nowStr);
	$file	= App::$path."data/agentlog/{$path}_{$year}_{$month}.log";
	file_put_contents($file, $logdata."\r\n",FILE_APPEND);
}

/**
 * @desc 兼容新代理商和老代理商控制开关
 * @attention 谷刚专用，动前请联系谷刚！！！
 */
function controlByGuugle(){
	return true;
}

/**
 * 事件系统报警接口
 * @param string $subject 报警标题
 * @param string $content 报警内容
 * @param string $bpid 应用BPID
 * @param array $users 用户英文名列表
 */
function boyaa_notify($subject, $content, $bpid, $users, &$err, &$resp) {
	$err = "";
	$resp = "";
	$url = 'http://notice.boyaa.com/index.php/common_api/app_data';
	$data = array (
		'auth_id'		=> 9,
		'auth_token'	=> 'ec9c3841b1572cf819698d32925e53e0',//测试环境
		'content'		=> $subject . ';' . $content,
		'fbpid'			=> $bpid,
		'priority'		=> 5,
		'contact_user'	=> implode(',', $users),//英文名
		'time'			=> time(),
		'sms_auth_id'	=> 58, //短信报警
		'sms_auth_token'=> 'a6263fc1c08fe7bc7620b3afb1bb86b3', //短信报警
	);
	$url  .= '?' . http_build_query($data);
	$resp = curl_get($url, array(), array(), $errno, $err, $code);
	if($errno || $code != 200) {
		return false;
	} else {
		$json = json_decode($resp, true);
		if(!$json || $json['error_code']) {
			return false;
		}
	}
	return true;
}

/**
 * @desc 新版本手机短信通知
 * @param $api 请求API
 * @param $authId	 授权ID
 * @param $authKey	 授权密钥
 * @param $phone	 手机号码
 * @param $tplId	 模板ID
 * @param $params	 模板数组变量
 * @param $zoneId	 地区码 默认86
 * @return bool|int
 */
function boyaa_sms($api, $authId, $authKey, $phone, $tplId, $params=array(), $zoneId='86') {
	$err = "";
	$resp = "";
	$data = array (
		'auth_id'		=> $authId,
		'auth_token'	=> $authKey,
		'phone'			=> is_array($phone) ? implode(',', $phone) : strval($phone),
		'tpl_id'		=> $tplId,
		'zone_id'		=> $zoneId,
		'params'		=> json_encode($params),
		'p'				=> 1,
	);
	$api  .= '?' . http_build_query($data);
	$resp = curl_get($api, array(), array(), $errno, $err, $code);
	if($errno || $code != 200) {
		return false;
	} else {
		$json = json_decode($resp, true);
		if(!$json || $json['code']) {
			return false;
		}
	}
	return true;
}

/**
 * @desc 获取config文件的修改时间
 * @param $filename 配置文件名 如 level.php
 * @param $area	 公共的话就不需要填 其它的话用App::$are
 * @return bool|int
 */
function getfilemtime($filename, $area=''){
	if(preg_match("/[^\w\-\.]/", $filename)) return false;
	$file	= App::$path.'config/'.($area?$area.'/':'').$filename;//配置文件
	if(!file_exists($file)) return false;
	$updateTime = filemtime($file);
	return (int)$updateTime;
}

/**
 * 截取字符串
 * @param $_string
 * @param $_num
 * @param $tail
 * @return string
 */
function cut_str($_string,$_num,$tail=''){
	if(mb_strlen($_string,'utf-8')>$_num){
		$_string=mb_substr($_string,0,$_num,'utf-8').$tail;
	}
	return $_string;
}

/**
 * @desc 根据经验值获取等级和呢称
 * @param $exp
 * @return array
 */
function getUserLevel($exp){
	$exp	= ($exp>0)?intval($exp):0;
	$levelInfo	= CFG('level');
	$ret	= array('level'=>0, 'name'=>$levelInfo[0]['name']);
	foreach($levelInfo as $level=>$row){
		if($exp < $row['exp']){
			$lev = $level-1;
			$ret	= array('level'=>$lev, 'name'=>$levelInfo[$lev]['name']);
			break;
		}
	}
	return $ret;
}

/**
 * 增加金条场后level分解为玩法、货币、原有level
 * @param $level_id
 * @return array
 */
function levelParam($level_id){
	$level_org = intval(substr($level_id,-3));//组合前的level
	$play_mode = floor($level_id / 1000) % 10;//玩法
	$base_chip_type = floor($level_id / 10000);//货币类型
	return array(
		0 => $level_org,
		1 => $play_mode,
		2 => $base_chip_type
	);
}

/**
 * 700 金条场需求开关
 * @return bool
 */
function envFor700fn(){
	if(App::$env == 'test' || App::$env == 'dev' || App::$env == 'product'){
		return true;
	}
	return false;
}


/**
 * 710需求开关
 * @return bool
 */
function envFor710fn(){
	if(App::$env == 'dev'){
		return true;
	}
	return false;
}

/**
 * 返回版本应用的游戏房间level集合
 * @param int $game_id
 * @return array|mixed
 */
function gameLevelKeys($game_id){

	$game_id = intval($game_id);
	$base_chip_type = array(0,1);
	$play_mode_mapping = Model::getInstance('admin.deploy')->C('playmode', array('gid'=>$game_id));
	$levels		= array_keys((array)CFG('system', 'roomlevel'));
	$keys = array();
	$redis		= REDIS('serverconfig');
	$gamePublicKey = "GamePublicKeyData_". $game_id;
	$gamePublicKeyData = $redis->get($gamePublicKey);
	if(!empty($gamePublicKeyData)){
		return json_decode($gamePublicKeyData, true);
	}

	//货币类型
	foreach($base_chip_type as $b){
		if(isset($play_mode_mapping) && !empty($play_mode_mapping)){
			//有多种玩法
			foreach($play_mode_mapping as $pid=>$val){
				//房间level
				foreach($levels as $level){
					$keys[] = "GamePublic_{$game_id}_". ($b * 10000 + $pid * 1000 + $level);
				}
			}
		}else{
			//房间level
			foreach($levels as $level){
				$keys[] = "GamePublic_{$game_id}_". ($b * 10000 + $level);
			}
		}
	}
	//设置10分钟过期
	$redis->set($gamePublicKey, json_encode($keys), 600);
	return $keys;

}



/**
 * 根据两个时间点获取时间差
 * @param int $stime
 * @param int $etime
 * @param string $date_formatter
 * @param boolean $pkey
 * @return array
 */
function get_date_range($stime, $etime, $date_formatter = '', $pkey = false)
{
    $date_range = array();
    $stime = !strtotime($stime)?$stime:strtotime($stime);
    $etime = !strtotime($etime)?$etime:strtotime($etime);
    while ($stime <= $etime) {
        $month = date($date_formatter, $stime);
        if ($date_formatter) {
            if ($pkey) {
                $date_range[$month][] = date($date_formatter, $stime);
            } else {
                $date_range[] = date($date_formatter, $stime);
            }
        } else {
            if ($pkey) {
                $date_range[$month][] = $stime;
            } else {
                $date_range[] = $stime;
            }
        }
        $stime = strtotime('+1 day', $stime);
    }

    return $date_range;
}

/**
 * 处理图片链接(安卓返回HTTP图片链接)
 */
function iconHttps2Http($appid, $hall_ver, $url) {
	$android = client_is_android($appid);
	return ($android && $hall_ver<800) ? str_replace("https", "http", $url):$url;
}

/**
 * 获取服务器ip
 */
function get_server_ip(){
	if(isset($_SERVER)){
		if(isset($_SERVER['SERVER_ADDR'])){
			$server_ip=isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
		}else{
			$server_ip=isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : '';
		}
	}else{
		$server_ip = getenv('SERVER_ADDR');
	}
	return $server_ip;
}

/**
 * @param $region
 */
function region2area($region){
	$area = array(
		18 => 'zhongbu',
		33 => 'zhongbu',
		9 => 'zhongbu',
		21 => 'zhongbu',
		28 => 'zhongbu',
		37 => 'zhongbu',
		11 => 'kungming',
		20 => 'dali',
		34 => 'dali',
		27 => 'honghe',
		35 => 'honghe',
		38 => 'lijiang',
		39 => 'lijiang',
		4  => 'yibin',
		6  => 'zigong',
		8 => 'leshan',
		12 => 'nanchong',
		13 => 'neijiang',
		14 => 'luzhou',
		2 => 'dalian',
		10 => 'dongbei',
		15 => 'dongbei',
		16 => 'dongbei',
		17 => 'dongbei',
		22 => 'dongbei',
		23 => 'dongbei',
		24 => 'dongbei',
		25 => 'dongbei',
		26 => 'dongbei',
		29 => 'dongbei',
		30 => 'dongbei',
		31 => 'dongbei',
		32 => 'dongbei',
		1 => 'sichuan',
		3 => 'xinan',
		5 => 'xinan',
		19 => 'xinan',
		36 => 'xinan',
		40 => 'dongbei',
		41 => 'dongbei',
		42 => 'dongbei',
		43 => 'dongbei',
		44 => 'dongbei',
		45 => 'dongbei',
		5 => 'siguojunqi',
	);
	return (isset($area[$region]))?$area[$region]:'xibei';
}

/**
 * 货币类型映射，和server保持一致
 * @param $type int
 * @return int
 */
function currencyToServerType($type){
	$type = intval($type);
	$data = 0;
	switch($type) {
		case 0: // 金币
			$data = 1;
			break;
		case 1: // 金条
			$data = 11;
			break;
		case 2: // 钻石
			$data = 8;
			break;
	}
	return empty($data) ? $type : $data;
}

/**
 * 金额位数保留
 * @param int $num
 * @param int $bit
 * @return int
 */
function number_sprintf($num,$bit){
	return sprintf("%.{$bit}f", $num); 
}

/**
 * IP KEY
 * @param $ip
 * @param $device
 * @param $os
 * @return string
 */
function ipkey($ip,$device,$os){
	$ip		= esc($ip);
	$os		= esc($os);
	$device	= esc($device);
	if(empty($ip) || empty($device) || empty($os)) {
		return;
	}
	return 'login'.md5(strtolower($ip.$device.$os));
}
/**
 * 分包加密ipkey
 * @param  int $region  地区
 * @param  int $aidLast 推荐码末尾数
 * @param  string $device  设备类型
 * @param  string $os      操作系统
 * @param  string $ip      ip
 * @return string
 */
function subpackageIpkey($region,$aidLast, $device, $os, $ip = ''){
	$region = intval($region);
	$aidLast = intval($aidLast);
	$device = esc($device);
	$os = esc($os);
	if($aidLast < 0 || $aidLast > 9 || empty($device) || empty($os) || empty($region)) {
		return;
	}
	$md5Str = $device.$os.$aidLast.$region;
	$pre = 'subpackage';
	if(!empty($ip)){
		$md5Str .= $ip;
		$pre .= "_ip";
	}
	$ipkey = $pre.md5(strtolower($md5Str));
	return $ipkey;
}

/**
 * @desc 是否模拟器登陆。
 * @param int $isSimulator 是否模拟器。0不是，1是
 * @param string $ip 登陆IP
 * @return bool
 */
function isSimulator($isSimulator, $ip, $flag) {
	$flagData = array(2);
	if(App::$env == 'product' && $isSimulator != 0 && in_array($flag, $flagData) && !in_array($ip, (array)CFG('boyaa', 'ip'))) {
		return true;
	}
	return false;
}

/**
 * @desc 获取默认城市
 * @param appid
 * @return $city
 */
function getDefaultCity($appid) {
	$region = get_region_id($appid);
	$regionInfo =  Model::getInstance('admin.equip')->getRegionInfo($region);
	$provinceName = $regionInfo['provinceName'] ?  $regionInfo['provinceName'] : '';
	$cityName = $regionInfo['cityName'] ?  $regionInfo['cityName'] : '';
	if($provinceName == '其他') {
		$city = $cityName;
	} else {
		$provinceName = mb_substr($provinceName,0,-1,'utf-8');
		$city = $provinceName.' '.$cityName;
	}
	return $city;
}

/**
 * 比赛类型映射
 * @param $type  赛事类型
 * @param $level 场次id
 */
function matchTypeMapping($type,&$level) {
	$type = intval($type);
	switch($type) {
		case 0: #定人赛
			$level = 700;
			break;
		case 3: #定时赛
			$level = 500;
			break;
		case 6: #邀请赛
			$level = 800;
			break;
	}
}

/**
 * 获取云计算数据上报对象
 * @return dc
 */
function dcyjs() {
	static $dcyjs = null;
	if(!class_exists('dcyjs')) {
		include(App::$path . 'lib/class.dcyjs.php');
	}
	if($dcyjs == null) {
		$dcyjs = new dcyjs();
	}
	return $dcyjs;
}