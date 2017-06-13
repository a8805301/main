<?php
/**
 * 即时后台任务启动事件
 * @author weicky
 * @package DiFangQiPai
 */
class Jobs_Event {
	/**
	 * 测试用的回显操作
	 */
	public static function TEST() {
		$args = func_get_args();
		echo "Get Job: test(" . implode(',', $args) . ");\n";
	}

	/**
	 * 更新排行榜
	 * @param int $mid 用户ID
	 * @param int $money 金币
	 * @param int $exp 经验
	 */
	public static function RANKUP($mid, $money, $exp,$region ) {
		if(App::$area=="dfqp"){
			Model::getInstance('rank.rank', 'redis')->setRank(Rank_Model::FHRANK, $mid, floatval($money));
			Model::getInstance('rank.rank', 'redis')->setRank(Rank_Model::JYRANK, $mid, intval($exp));
		}elseif(App::$area=="ddfqp"){
			Model::getInstance('rank.rank', 'redis')->setRankNew(Rank_Model::FHRANK, $mid, floatval($money), $region);
			Model::getInstance('rank.rank', 'redis')->setRankNew(Rank_Model::JYRANK, $mid, intval($exp), $region);
		}
	}

	/**
	 * 添加登录日志
	 * @param int $mid 用户ID
	 * @param int $appId 应用ID
	 * @param string $ver 应用版本
	 * @param int $channel 渠道ID
	 * @param int $loginTime 登录时间
	 * @param int $regTime 注册时间
	 * @param int $money 金币
	 * @param int $promoterId 推广员ID
	 */
	public static function LOGINLOG($mid, $appId, $ver, $channel, $loginTime, $regTime, $money, $promoterId) {
		Model::getInstance('logs.loginlog')->login($mid, $appId, $ver, $channel, $loginTime, $regTime, $money, $promoterId);

		if($promoterId) {
			Model::getInstance('partners.partner_datalogin')->add( $promoterId , $mid , $loginTime );
		}
	}

	/**
	 * 添加IP记录
	 * @param int $mid 用户ID
	 * @param string $ip 登录IP
	 * @param int $time 登录时间
	 */
	public static function IPLOG($mid, $ip, $time) {
		Model::getInstance('logs.iplog')->add($mid, $ip, $time);
	}

	/**
	 * 添加设备日志
	 * @param int $mid 用户ID
	 * @param string $appInfo 应用信息(应用ID|版本号|渠道ID) 【这里是为了减少参数数量因此合并多个信息到一个字段】
	 * @param string $devInfo 设备信息(设备类型|操作系统|分辨率) 【这里是为了减少参数数量因此合并多个信息到一个字段】
	 * @param string $netInfo 联网信息(联网模式|运营商) 【这里是为了减少参数数量因此合并多个信息到一个字段】
	 * @param string $guid 全球唯一ID
	 * @param string $devId 设备码
	 * @param string $macAddr 物理网卡地址
	 * @param int $loginTime 登录时间
	 * @param string $factoryid 厂商ID
	 * @param string $imei 设备的IMEI
	 * @param mixed ? //【这个是最后一个可用参数了】
	 */
	public static function DEVLOG($mid, $appInfo, $devInfo, $netInfo, $guid, $devId='', $macAddr='', $loginTime=0, $factoryid='', $imei='') {
		list($app, $ver, $channel)			= explode('|', $appInfo, 3);
		list($devType, $devOs, $resolution, $simulatorflag)	= explode('|', $devInfo, 4);
		list($netMode, $netOperator)		= explode('|', $netInfo, 2);
		$devType = is_numeric($simulatorflag) && $simulatorflag > 0 ? $devType."-vr-{$simulatorflag}" : $devType;
		Model::getInstance('devices.devices')->login($mid, $app, $ver, $channel, $devType, $devOs, $resolution, $netMode, $netOperator, $guid, $devId, $macAddr, $loginTime, $factoryid, $imei);
		//设备码映射
		if($devId) {
			Model::getInstance('devices.devicemap')->add($devId, $mid);
		}
		//物理网卡映射
		if($macAddr) {
			Model::getInstance('devices.macaddrmap')->add($macAddr, $mid);
		}
		//厂商ID
		if($factoryid) {
			Model::getInstance('devices.factoryidmap')->add($factoryid, $mid);
		}		
	}

	/**
	 * 设置推送token记录
	 * @param int $mid 用户ID
	 * @param int $app 应用ID
	 * @param string $token 推送TOKEN
	 * @param int $flags 推送开关
	 */
	public static function PUSHLOG($mid, $app, $token, $flags) {
		Model::getInstance('devices.pushtoken')->set($mid, $app, $token, $flags);
	}

	/**
	 * 推送聊天信息(离线)
	 * @param number $mid
	 * @param json $data
	 */
	public static function PUSHCHATMSG($mid) {
		$chat = Model::getInstance('main.chat');
		$list = $chat->getunreadmsg($mid);
		if (!empty($list)) {
			$fmids = array();
			$usercache = Model::getInstance('usercache','redis');
			foreach ($list as $item) {
				if (!in_array($item['mid_from'], $fmids)) {
					$fmids[] = $item['mid_from'];
				}
			}
			$users = $usercache->getMulti($fmids,'nick');
			foreach ($list as $item) {
				$id = $item['id'];
				$mid_from = $item['mid_from'];
				unset($item['id']);
				unset($item['mid_to']);
				$item['nick'] = isset($users[$mid_from]['nick']) ? $users[$mid_from]['nick']:$mid_from;
				$result = mserver(CFG('@core', 'server.broadcast'))->push2Client($mid_from,$mid,10,$item);
				if ($result==1) {
					$chat->delete($id);
				}
			}
		}
	}
	/**
	 * 限时赛日志记录
	 * @param unknown_type $matchid
	 */
	public static function TIMELIMITRANKLOG($matchid){
		$rankinfo	= Model::getInstance("match.matchredis","redis")->rank("TIME_LIMIT_MATCH_".$matchid);
		if( !empty($rankinfo) ){
			$i 		= 3;
			$logData= Model::getInstance("logs.otherlog");
			do{
				$ret = $logData->insert('TIMELIMIT|'.date('YmdH') , json_encode($rankinfo) );
				if( $ret ){
					break;
				}
				$i--;
			}while($i);
		}
	}
	/**
	 * 推送奖励日志
	 * @param int $mid
	 * @param int $app 应用ID
	 * @param int $rewardid 奖励ID
	 * @param int $gold
	 * @param int $diamond
	 */
	public static function PUSHREWARDLOG($mid,$app,$rewardid,$gold,$diamond){
		Model::getInstance("logs.pushrewardlog")->add($mid,$app,$rewardid,$gold,$diamond);
	}

	/**
	 * 插入一条未注册代理商玩家数据
	 * @param  $mid
	 * @param  $pno
	 */
	public static function MEMBERPHONE($mid,$pno,$appid){
		Model::getInstance("agents.members")->insert($mid,$pno,$appid);
	}

	/**
	 * 登录后踢走其它地区的用户
	 * @param int $mid 用户ID
	 * @param string $platformId 平台ID
	 * @param int $platformType 平台类型
	 * @param int $region 地区ID
	 */
	public function LOGINKICK($mid, $platformId, $platformType, $region) {
		$idmap	= Model::getInstance('users.idmap');
		$users	= $idmap->getMidsByPlatformId($platformId, $platformType);
		$online	= Model::getInstance('main.online');
		$srv	= mserver();
		unset($users[$region]);
		foreach($users as $u) {
			if($u != $mid) {
				$online->moveOfflineUserBycache($u);
				$srv->offlineUser($u, '您已在别处登录！', 1072);
			}
		}
	}
	/**
	 * 私信升级引导
	 * @param int $mid
	 */
	public static function PUSHGUIDEUPBY($mid){
		$mid	= intval($mid);
		$msg	= "您正在使用游客账号，为了确保账号财产安全，请尽快到个人资料界面完成【账号升级】";
		Model::getInstance("main.newmessage")->sendMessage($mid,'账号安全',$msg,1);
	}

	/**
	 * 子游戏统计上报
	 * @param int $mid 用户ID
	 * @param int $gid 游戏ID
	 * @param int $app 应用ID
	 * @param int $gameVer 游戏版本号
	 * @param string $ip IP
	 * @param int $time 时间
	 */
	public static function SUBGAMESTAT($mid, $gid, $app, $gameVer, $ip, $time) {
		$statApp	= $app . "-" . $gid;
		$model		= Model::getInstance("master.gameregister", "redis");
		$data		= $model->getDataByKey($mid, $gid);
		$is_first	= ($data==1) ? 0 : 1; //是否同一次登录该子游戏
		$deviceInfo	= Model::getInstance("devices.devices")->getLastDeviceInfo($mid);
		$device		= empty($deviceInfo['device_id']) ? $deviceInfo['uniq_id'] : $deviceInfo['device_id']; //设备标识
		$userinfo	= Model::getInstance("usercache.usercache", "redis")->get($mid, array("version", "channel_id")); //用户应用版本号和渠道ID
		if(empty($gameVer)) {
			$gameVer = Model::getInstance("master.gameregister", "redis")->getGameVer($mid, $gid);
			$gameVer = $gameVer ? $gameVer : '';
		}
		//------------------------------------------------------
		//今天是否第一次进入游戏
		$modelLimit = Model::getInstance("master.limit","mc");
		$login_value_game = $modelLimit->dayLoginGameFlag($mid, $gid, 4, 0);
		if($login_value_game == 0){
			//把标识设置+1
			$modelLimit->dayLoginGameFlag($mid, $gid, 4, 1);
		}
		//------------------------------------------------------
		if($is_first){//未注册，先注册，再上报
			$res = $model->setRegister($mid, $gid);
			if(!$res){//注册失败不上报
				return false;
			}
		}
		//------------------------------------------------------
		$stat_info = array(
			'uid'			=> $mid, //用户ID
			'platform_uid'	=> $mid, //平台ID
			'lts_at'		=> $time, //时间
			'is_first'		=> $is_first,//是否历史首次进入该子游戏
			'game_id'		=> $gid, //子游戏ID
			'channel_code'	=> $userinfo['channel_id'], //渠道来源
			'ip'			=> $ip, //IP
			'entrance_id'	=> '', //用户类型
			'version_info'	=> $gameVer,//$userinfo['version'], //版本号
			'ad_code'		=> '', //广告商标识
			'user_gamecoins'=> '', //用户进入的时候，携带的游戏币数额
			'lang'			=> '', //用户使用的语言
			'm_dtype'		=> '', //移动终端设备机型
			'm_pixel'		=> '', //移动终端设备屏幕尺寸大小
			'm_imei'		=> md5($device), //移动终端设备号
			'm_imsi'		=> '', // 移动用户的IMSI（国际移动用户识别码）
			'm_os'			=> '', //移动终端设备操作系统
			'm_network'		=> '', //移动终端联网接入方式
			'm_operator'	=> '', //移动网络运营商
			'source_path'	=> '', //来源路径
		);
		dcenter()->sendData($app, 38, $stat_info);
	}

	/**
	 * 累计登录上报
	 * @param int $mid 用户ID
	 */
	public static function TOTALLOGIN($mid, $appid){
		$mid = intval($mid);
		$appid = intval($appid);
		//写udp操作
		$cfg	= CFG('@core', 'server.dataserver');
		$reportData = array(
			'type'	=> 1,
			'mid'	=> $mid,
			'appid'	=> $appid
		);
		// App::$log->add(Log::DEBUG, "Before Write Udp For Totallogin|mid:{$mid}|appid:{$appid}");
		CMCC::getInstance($cfg[0], $cfg[1])->customReport(json_encode($reportData));
	}
	/**
	 * 累计玩牌上报
	 * @param int $mid 用户ID
	 */
	public static function TOTALPLAY($mid, $appid){
		$mid = intval($mid);
		$appid = intval($appid);
		//写udp操作
		$cfg	= CFG('@core', 'server.dataserver');
		$reportData = array(
			'type'	=> 2,
			'mid'	=> $mid,
			'appid'	=> $appid
		);
		// App::$log->add(Log::DEBUG, "Before Write Udp For Totalplay|mid:{$mid}|appid:{$appid}");
		CMCC::getInstance($cfg[0], $cfg[1])->customReport(json_encode($reportData));
	}

	/**
	 * @desc core.login接口代理商部分功能
	 * @param $mid
	 * @param $nick
	 * @param $phone
	 * @param $appid
	 * @param $ip
	 * @param $device
	 * @param $os
	 * @return bool|void
	 */
	public static function LOGINAGENTOP($mid, $nick, $phone, $appid, $ip, $device, $os){
		$mid	= intval($mid);
		$phone	= esc($phone);
		$appid	= intval($appid);
		$ip		= esc($ip);
		$device	= esc($device);
		$os		= esc($os);
		$region	= get_region_id($appid);
		$agentcache	= Model::getInstance("agent.agentcache", "redis");
		//判断是否已经绑定过代理商
		$isbind	= $agentcache->getBindFlagMap($mid);
		if($isbind){//绑定了则更新呢称和插入登陆记录
			$a2mModel = Model::getInstance("proxy.a2m");
			$a2mModel->updateUserInfo($mid,array('login_time' => time(),'nick' => $nick));
			//代理商下线玩家活跃表中插入活跃记录
			$aid = $a2mModel->getaid($mid);
			if ($aid) {//代理商下线玩家当日活跃记录
				$agentcache->setLoginAndPlayData($region, $aid, $mid, $type = 'login');
				Model::getInstance('proxy.agentloginlog')->add($mid, $aid, $region, date('Ymd', time()));
			}
			return true;
		}

		//检测是否有预注册绑定
		if($phone){
			$prebind = $agentcache->getPrebindinfo($phone, $region);
			if(!empty($prebind)){//绑定代理商操作
				$fromtype	= ($prebind['flag'] == 1)?1:4;//来源渠道
				Model::getInstance("proxy.a2m")->adda2mlink($prebind['mid'], $mid, $appid, $fromtype, false, $prebind['pno']);
			}
		}

		//IPKey绑定处理
		if($ip && $device && $os ){
			//通过ip及设备机型判断是否绑定代理商
			$ipkey = ipkey($ip,$device,$os);
			$masterredis= REDIS('agent');
			$h5device	= $masterredis->get($ipkey);
			if(!empty($h5device)){
				$h5device	= json_decode($h5device, 1);
				$fromtype	= isset($h5device['ftype'])?$h5device['ftype']:3;//来源渠道
				$ret = Model::getInstance("proxy.a2m")->adda2mlink($h5device['aid'], $mid, $appid, $fromtype);
				if($ret){
					$ipkeydata = Model::getInstance("proxy.agentipkeylog")->getLastOne($h5device['aid'],$ip,$device,$os,0);//获取最新一条信息
					if(!empty($ipkeydata)){//绑定
						Model::getInstance("proxy.agentipkeylog")->bandMidById($ipkeydata['id'],$mid);
					}
				}
				$masterredis->delete($ipkey);
				$flag = false;
			}
		}

		return true;
	}

	/**
	 * @desc 保存用户推送标识
	 * @param $mid
	 * @param $appid
	 * @param $token
	 * @param $flags
	 */
	public static function SETPUSHTOKEN($mid, $appid, $token, $flags){
		Model::getInstance('devices.pushtoken')->set($mid, $appid, $token, $flags);
	}

	/**
	 * 支付后回调
	 * @param int $mid 用户ID
	 * @param int $amount 金额
	 */
	public static function PAYDONE($mid, $amount) {
		$mid = intval($mid);
		$amount = intval($amount);
		$plus = $amount * 10;
		if($plus) {
			$score = Model::getInstance('userscore')->incPositiveScore($mid, $plus);
			$desc = $amount; //$desc = "支付金额:{$amount}";
			Model::getInstance('scorelog')->add($mid, 1, 3, $plus, $score, $desc); //记录操作日志
		}
	}

	/**
	 * 定时赛获得名次
	 * @param int $mid 用户ID
	 * @param int $rank 排行
	 * @param int $matchConfigId 比赛配置ID
	 */
	public static function FIXEDMATCHWIN($mid, $rank, $matchConfigId) {
		$plus = 20;
		$score = Model::getInstance('userscore')->incPositiveScore($mid, $plus);
		$desc = $matchConfigId; //$desc = "比赛配置ID:{$matchConfigId},名次:{$rank}";
		Model::getInstance('scorelog')->add($mid, 1, 2, $plus, $score, $desc); //记录操作日志
	}

	/**
	 * 发送邮件
	 * @param $content
	 * @param $addr
	 * @param $subject
	 * @param null $cc
	 */
	public static function SEDNEMAIL($content, $addr, $subject, $cc=array()) {
		if( App::$env!='product' || empty($content) || empty($addr) || empty($subject)) {
			return;
		}
		if(!class_exists('PHPMailer')) {
			require_once App::$path.'lib/mail/phpmail.php';
			require_once App::$path.'lib/mail/smtp.php';
		}
		$mail = new PHPMailer;
		$mail->CharSet	= "UTF-8";
		$mail->SMTPAuth	= true;
		$mail->Host		= 'mail.boyaa.com';
		$mail->Port		= 587;
		$mail->Username	= 'LH.Rbt';
		$mail->Password	= 'Te@123rbt';
		$mail->From		= 'LH.Rbt@boyaa.com';
		$mail->FromName	= '烈火机器人';
		$mail->Subject	= $subject;
		$mail->Body		= $content;
		$mail->isSMTP();
		$mail->SMTPSecure = 'tls';
		$mail->isHTML(true);
		$mailAddr	= json_decode($addr,true);
		foreach($mailAddr as $add) {
			$mail->addAddress($add);
		}
		if(!empty($cc)) {
			$mailCC	= json_decode($cc,true);
			foreach($mailCC as $c) {
				$mail->addCC($c['email'], $c['name']);
			}
		}
		if(!$mail->send()) {
			App::$log->add(Log::DEBUG, 'Mailer Error: ' . $mail->ErrorInfo);
		}
	}

	/**
	 * 用户子游戏注册
	 * @param $mid
	 * @param $gid
	 * @param $type
	 * @return mixed
	 */
	public static function SUBGAMEREGISTER($mid, $gid, $type) {
		return Model::getInstance("master.gameregister","redis")->setRegister($mid,$gid,$type);
	}
}
