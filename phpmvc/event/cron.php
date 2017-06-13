<?php
/**
 * 通用定时任务相关事件类
 * @author weicky
 * @package DiFangQiPai
 */
class Cron_Event {
	/**
	 * 测试用事件
	 * @param mixed $arg 附件参数
	 */
	public static function TEST($arg) {
		print_r($arg);
		App::$log->add(Log::DEBUG, "Cron::TEST()");
	}
	/**
	 * 限时赛开始前推送通知
	 * @param array $arg
	 */
	public static function TIME_LIMIT_MATCH_PUSH_NOTICE($arg){
		if( empty($arg['msg']) || empty($arg['GameId']) ){
			return ;
		}
		//获取报名用户
		$TimeLimitMatchGameIDMap=array( 9=>65542,/*二七十*/ 6=>65541,/*川斗*/ 10=>65540,/*马股*/ 2=>65543  /*血战*/);
		if( !isset($TimeLimitMatchGameIDMap[$arg['GameId']]) ) return ;
		$offlineUser= array();
		$matchRedis = Model::getInstance("match.matchredis","redis");
		$modelOl    = Model::getInstance("main.online");
		$pushModel  = Model::getInstance("devices.pushtoken");
		$userModel	= Model::getInstance("usercache.usercache","redis");
		$tempUser   = $matchRedis->timeLimitApplyUser($TimeLimitMatchGameIDMap[$arg['GameId']]);
		foreach ($tempUser as $mid=>$v){
			if( -1 === $modelOl->userState($mid) ){
				$uinfo = $userModel->get($mid);
				$pushModel->sendMessage($mid,$uinfo['app_id'],$arg['msg']);
			}
		}
	}
	
	
	/**
	 * 开启限时赛
	 * @param int $arg 限时赛游戏ID
	 */
	public static function TIME_LIMIT_MATCH_OPEN($arg) {
		if(
			empty($arg['GameId']) ||
			empty($arg['GameLevel']) ||
			empty($arg['BeginTime']) ||
			empty($arg['EndTime']) ||
			empty($arg['MatchRewardCount'])
		) {
			return;
		}
		//修改比赛配置
		$config = array('desc' => Model::getInstance('admin.deploy')->C('games',array('gid'=>$arg['GameId'])), 'value' => array());
		$config['value']['GameId'] = array('desc' => '游戏类型', 'value' => $arg['GameId']);
		$config['value']['GameLevel'] = array('desc' => '游戏等级', 'value' => $arg['GameLevel']);
		$config['value']['BeginTime'] = array('desc' => '开始时间', 'value' => $arg['BeginTime']);
		$config['value']['EndTime'] = array('desc' => '结束时间', 'value' => $arg['EndTime']);
		$config['value']['MatchRewardCount'] = array('desc' => '奖励人数', 'value' => $arg['MatchRewardCount']);
		$cnt = intval($arg['MatchRewardCount']);
		for($i=1; $i<=$cnt; $i++) {
			$config['value']["money{$i}"] = array('desc' => "第{$i}名金币", 'value' => $arg["money{$i}"]);
			$config['value']["diamond{$i}"] = array('desc' => "第{$i}名钻石", 'value' => $arg["diamond{$i}"]);
			$config['value']["masterpoints{$i}"] = array('desc' => "第{$i}名大师分", 'value' => $arg["masterpoints{$i}"]);
		}
		$mdlCfg = Model::getInstance('serverconfig.srvcfg', 'redis');
		$hashKey = 'TIME_LIMIT_MATCH_CONF_HASH';
		$itemKey = "TIME_LIMIT_MATCH_CONF_{$arg['GameId']}";
		$status = $mdlCfg->setHashItem($hashKey, $itemKey, $config);
		if(!$status) {
			App::$log->add(Log::ERROR, "[Crond] Time-Limit-Match config save failed! //{$hashKey}.{$itemKey} = '" . json_encode($config));
			return;
		}
		//通知Server更新配置
		$gid = intval($arg['GameId']);
		$srv = mserver(CFG('@core', 'server.limitctrl'));
		$status = $srv->openTimeLimitMatch($gid);
		if(!$status) {
			App::$log->add(Log::ERROR, "[Crond] Time-Limit-Match open failed! //errno = " . $srv->errno());
		}
	}

	/**
	 * 关闭限时赛
	 * @param int $arg 限时赛游戏ID
	 */
	public static function TIME_LIMIT_MATCH_CLOSE($arg) {
		$gid = intval($arg);
		if(!$gid) {
			return;
		}
		$srv = mserver(CFG('@core', 'server.limitctrl'));
		$status = $srv->closeTimeLimitMatch($gid);
		if(!$status) {
			App::$log->add(Log::ERROR, "[Crond] Time-Limit-Match close failed! //errno = " . $srv->errno());
		}
	}
	/**
	 * VIP比赛开始
	 * @param array $arg
	 */
	public static function VIP_MATCH_OPEN($arg){
		if(
				empty($arg['GameId']) ||
				empty($arg['GameLevel']) ||
				empty($arg['BeginTime']) ||
				empty($arg['EndTime']) ||
				empty($arg['MatchRewardCount']) ||
				empty($arg['Reward'])
		) {
			return;
		}
		//修改比赛配置
		$config = array('desc' => Model::getInstance('admin.deploy')->C('games',array('gid'=>$arg['GameId'])), 'value' => array());
		$config['value']['GameId'] = array('desc' => '游戏类型', 'value' => strval($arg['GameId']));
		$config['value']['GameLevel'] = array('desc' => '游戏等级', 'value' => strval($arg['GameLevel']));
		$config['value']['BeginTime'] = array('desc' => '开始时间', 'value' => $arg['BeginTime']);
		$config['value']['EndTime'] = array('desc' => '结束时间', 'value' => $arg['EndTime']);
		$config['value']['MatchRewardCount'] = array('desc' => '奖励人数', 'value' => strval($arg['MatchRewardCount']));
		$cnt = intval($arg['MatchRewardCount']);
		for($i=1; $i<=$cnt; $i++) {
			$config['value']["money{$i}"] = array('desc' => "第{$i}名金币", 'value' => strval($arg['Reward'][$i][0]));
			$config['value']["diamond{$i}"] = array('desc' => "第{$i}名钻石", 'value' => strval($arg['Reward'][$i][1]));
			$config['value']["entity{$i}"] = array('desc' => "第{$i}名实物奖励", 'value' =>$arg['Reward'][$i][2]);
		}
		$mdlCfg = Model::getInstance('serverconfig.srvcfg', 'redis');
		$hashKey = 'VIP_MATCH_CONF_HASH';
		$itemKey = "VIP_MATCH_CONF_{$arg['GameId']}";
		$status = $mdlCfg->setHashItem($hashKey, $itemKey, $config);
		if(!$status) {
			App::$log->add(Log::ERROR, "[Crond] VIP-Match config save failed! //{$hashKey}.{$itemKey} = '" . json_encode($config));
			return;
		}
		//通知Server更新配置
		$gid = intval($arg['GameId']);
		$srv = mserver(CFG('@core', 'server.limitctrl'));
		$status = $srv->openVIPMatch($gid);
		if(!$status) {
			App::$log->add(Log::ERROR, "[Crond] VIP-Match open failed! //errno = " . $srv->errno());
		}
	}
	
	public static function VIP_MATCH_DISPLAY($arg){
		if(
				empty($arg['GameId']) ||
				empty($arg['GameLevel']) ||
				empty($arg['BeginTime']) ||
				empty($arg['EndTime']) ||
				empty($arg['DetailDes']) ||
				empty($arg['SignUpCondition'])||
				empty($arg['MatchRewardCount']) ||
				empty($arg['Reward'])
		) {
			return;
		}
		//修改比赛列表配置
		$config = array('desc' => 'VIP比赛场', 'value' => array());
		$config['value']['Des'] = array('desc' => '描述', 'value' => 'VIP专属赛');
		$config['value']['Name'] = array('desc' => '名字', 'value' => Model::getInstance('admin.deploy')->C('games',array('gid'=>$arg['GameId']))."VIP专属赛");
		$config['value']['GameId'] = array('desc' => '游戏Id', 'value' => strval($arg['GameId']));
		$config['value']['Fee'] = array('desc' => '报名费', 'value' => strval($arg['Fee'])?strval($arg['Fee']):"0");
		$config['value']['DetailDes'] = array('desc' => '详细规则', 'value' => strval($arg['DetailDes']));
		$config['value']['SignUpCondition'] = array('desc' => '报名条件', 'value' => strval($arg['SignUpCondition']));
		$config['value']['PrizeNum'] = array('desc' => '奖金数量', 'value' => strval($arg['MatchRewardCount']));
		foreach ( $arg['Reward'] as $k=>$v ){
			if(empty($v[2])){
				$xuniAwad = array();
				if( $v[0] ){
					$xuniAwad[] = "{$v[0]}金币";
				}
				if( $v[1] ){
					$xuniAwad[] = "{$v[1]}钻石";
				}
				$str	= "第{$k}名 ".implode('+', $xuniAwad);
			}else{
				$str	= "第{$k}名 {$v[2]}";
			}
			if( $k == 1 ){
				$Champion = explode(' ', $str);
			}
			$config['value']["Prize".($k-1)] = array('desc' => '奖励', 'value' => $str);
		}
		$config['value']['Champion'] = array('desc' => '冠军奖励（数值和match6_Prize0必须相同）', 'value' => $Champion[1]);
		$config['value']['StartTime'] = array('desc' => '开始时间(格式：16:49:00)', 'value' => date('H:i:s',$arg['BeginTime']));
		$config['value']['EndTime'] = array('desc' => '结束时间(格式：16:49:00)', 'value' => date('H:i:s',$arg['EndTime']));
		$config['value']['StartDate'] = array('desc' => '开始日期(格式：2014-12-02)', 'value' => date('Y-m-d',$arg['BeginTime']));
		$config['value']['Type'] = array('desc' => '比赛类型(1为VIP专属赛)', 'value' => '1' );
		$matchid= array(9=>262153,6=>262150,10=>262154,2=>262146);
		$status = Model::getInstance('serverconfig.srvcfg', 'redis')->setHashItem('SRV_COMMON_MATCH_HASH', "match{$matchid[$arg['GameId']]}" , $config);

		if( !$status ){
			App::$log->add(Log::ERROR, "[Crond] ADD VIP DISPLAY FAIL!" );
		}
	}

	/**
	 * VIP比赛开始前推送通知
	 * @param array $arg
	 */
	public static function VIP_MATCH_PUSH_NOTICE($arg){
		if( empty($arg['msg']) || empty($arg['GameId']) ){
			return ;
		}

		//获取报名用户
		$TimeLimitMatchGameIDMap=array( 9=>65542,/*二七十*/ 6=>65541,/*川斗*/ 10=>65540,/*马股*/ 2=>65543  /*血战*/);
		if( !isset($TimeLimitMatchGameIDMap[$arg['GameId']]) ) return ;
		$offlineUser= array();
		$matchRedis = Model::getInstance("match.matchredis","redis");
		$modelOl    = Model::getInstance("main.online");
		$pushModel  = Model::getInstance("devices.pushtoken");
		$userModel	= Model::getInstance("usercache.usercache","redis");
		$tempUser   = $matchRedis->vipMatchApplyUser($TimeLimitMatchGameIDMap[$arg['GameId']]);
		foreach ($tempUser as $mid=>$v){
			if( -1 === $modelOl->userState($mid) ){
				$uinfo = $userModel->get($mid);
				$pushModel->sendMessage($mid, $uinfo['app_id'], $arg['msg']);
			}
		}
	}

	/**
	 * 定时赛开赛
	 * @param unknown $arg
	 */
	public static function FIXED_MATCH_OPEN($data){
		if (empty($data['MatchId'])) {
			return;
		}

	    //检查比赛ID
		if (isset($data['CheckMatchId'])) {
		    $cachekey = 'FIXED_MATCH_KEY_LIST';
		    $redis = REDIS('serverconfig');
		    $ret = $redis->hGet($cachekey, $data['MatchId']);
		    if (!$ret) {
		        return;
		    }
		}

		//循环添加计划任务
		if (isset($data['MatchLoop']) && !empty($data['MatchLoop'])) {
		    $d = 1;
		    do {
		        $t = strtotime("+$d day");
		        $w = date('w', $t);
		        $displayTime = $data['DisplayTime'] + $d*3600*24; //下一次显示时间
		        $openTime = strtotime(date("Y-m-d", $t).' '.$data['BeginTime']); //下一次开始时间
		        $d++;
		    } while (!in_array($w, $data['MatchLoop']));

		    $start = $data['StartDate'].' '.$data['BeginTime'];
		    $data['StartDate'] = date('Y-m-d', $openTime);
		    $data['DisplayTime'] = $displayTime;
		    $data['CheckMatchId'] = 1; //设置检测比赛标识
		    $cron = Model::getInstance('jobs.cron', 'redis');
            $cron->add(date('YmdHi', $displayTime-60), 'FIXED_MATCH_OPEN', $data);
            //开赛后两小时更新开始日期
            $cron->add(date('YmdHi', strtotime($start)+3600*2), 'FIXED_MATCH_UPDATE_DATA', $data);
            //添加任务检测记录
            $keyOpen = 'FIXED_MATCH_OPEN_'.date('Ymd', $openTime).'_'.$data['MatchId'];
		    $cronkvs = Model::getInstance('main.cronkvs');
            $cronkvs->set($keyOpen, json_encode($data));
		}

		//通知Server更新配置
		$matchId = intval($data['MatchId']);
		$srv = mserver(CFG('@core', 'server.limitctrl'));
		$status = $srv->openFixedMatch($matchId);
		if ($status) { //清除计划任务是否存在记录
		    $key = 'FIXED_MATCH_OPEN_'.date('Ymd', time()).'_'.$matchId;
		    $cronkvs = Model::getInstance('main.cronkvs');
		    $cronkvs->del($key);
		} else {
			App::$log->add(Log::ERROR, "[Crond] Fixed-Time-Match open failed! //errno = " . $srv->errno());
		}
	}
	/**
	 * 定时赛显示
	 * @param array $data
	 */
	public static function FIXED_MATCH_DISPLAY($data){
		if (
			empty($data['MatchName']) ||
			empty($data['GameId']) ||
			empty($data['BeginTime']) ||
			(empty($data['MatchConfigId']) && empty($data['MatchId'])) ||
			empty($data['DetailDes']) ||
			empty($data['MatchRewardCount']) ||
			empty($data['Reward'])
		) {
			App::$log->add(Log::INFO, "[Crond::Fixed_Match_Data_Invalid]: ".json_encode($data));
		    return;
		}

		//检查比赛ID
		if (isset($data['CheckMatchId']) && isset($data['MatchId']) && !empty($data['MatchId'])) {
		    $cachekey = 'FIXED_MATCH_KEY_LIST';
		    $redis = REDIS('serverconfig');
		    $ret = $redis->hGet($cachekey, $data['MatchId']);
		    if (!$ret) {
		        App::$log->add(Log::INFO, "[Crond::Fixed_MatchId_Empty]MatchId:".$data['MatchId']);
		        return;
		    }
		}

		//动态分配比赛ID
		if (!isset($data['MatchId']) || empty($data['MatchId'])) {
			$matchId = self::getAvailableMatchId($data['GameId']);
		} else {
			$matchId = $data['MatchId'];
		}
		//如果没有可用比赛ID
		if (!$matchId) {
			App::$log->add(Log::INFO, "Fixed Match Error::Get MatchId Failed.");
			return;
		}

		//计划任务
		$cron = Model::getInstance('jobs.cron', 'redis');
		//循环添加计划任务
		$tmpBeginTime = $data['BeginTime']; //暂存本次比赛开始时间
		$tmpDisplayTime = $data['DisplayTime']; //暂存本次比赛显示时间

		if ($data['MatchLoopType'] == 2 && $data['MatchLoopInterval'] >= 1) { //下一次循环(当天内循环)
			if (isset($data['LoopEndTime'])) {
				$loopEndTime = strtotime(date("Y-m-d", $tmpBeginTime) . ' ' . $data['LoopEndTime']);
			} else {
				//$data['LoopEndTime'] = $loopEndTime;
				$loopEndTime = strtotime(date("Y-m-d", $tmpBeginTime) . ' 00:00:00');
			}
			if ($tmpBeginTime + $data['MatchLoopInterval'] * 60 <= $loopEndTime) {
				$displayInfo = $data;
				$displayInfo['MatchType'] = ''; //避免按天循环
				$displayInfo['BeginTime'] = $tmpBeginTime + $data['MatchLoopInterval'] * 60;
				$displayInfo['DisplayTime'] = $tmpBeginTime;
				//判断下一次比赛时间是否可以终结
				if(empty($data['LoopEndDate']) || $displayInfo['BeginTime'] <= strtotime($data['LoopEndDate'])){
					$cron->add(date('YmdHi', $displayInfo['DisplayTime']-60), 'FIXED_MATCH_DISPLAY', $displayInfo);
				}
			} else {
				$data['MatchType'] = $data['RawData']['MatchType']; //恢复循环标识
			}
		}
		if (isset($data['MatchType']) && $data['MatchType'] >= 2) {
			if ($data['MatchType'] == 2) { //周循环
				$d = 1;
				do {
					$w = date('w', strtotime("+$d day"));
					$displayTime = $tmpDisplayTime + $d * 3600 * 24; //下一次显示时间
					$beginTime = $tmpBeginTime + $d * 3600 * 24; //下一次开始时间
					if ($d++ > 7) { //避免错误配置导致死循环
						break;
					}
				} while (!in_array($w, $data['MatchLoop']));

			} else if ($data['MatchType'] == 3) { //月循环
				$d = 1;
				$t = date('t', $tmpDisplayTime); //当前月的最大天数
				do {
					$displayTime = $tmpDisplayTime + $d * 3600 * 24;
					$beginTime = $tmpBeginTime + $d * 3600 * 24;
					$day = date('d', $displayTime);
					if (in_array(32, $data['MatchLoopMonth']) && $day == $t) { //选择月末
						break;
					}
					if ($d++ > 32) { //避免错误配置导致死循环
						break;
					}
				} while (!in_array($day, $data['MatchLoopMonth']));

			} else if ($data['MatchType'] == 4) { //间隔循环
				$d = $data['MatchLoopDay'];
				$displayTime = $tmpDisplayTime + ($d + 1) * 3600 * 24; //下一次显示时间
				$beginTime = $tmpBeginTime + ($d + 1) * 3600 * 24; //下一次开始时间
			}
			//$data['CheckMatchId'] = 1; //设置检测比赛标识
			if (!empty($data['MatchLoop']) || !empty($data['MatchLoopMonth']) || (isset($data['MatchLoopDay']) && $data['MatchLoopDay'] >= 0)) {
				$data['BeginTime'] = $beginTime;
				$data['DisplayTime'] = $tmpBeginTime;
				if (isset($data['MatchLoopType']) && $data['MatchLoopType'] == 2) { //多场循环赛
					if (isset($data['LoopEndTime'])) {
						$loopEndTime = strtotime(date("Y-m-d", $tmpBeginTime) . ' ' . $data['LoopEndTime']);
						if ($tmpBeginTime + $data['MatchLoopInterval'] * 60 > $loopEndTime) {
							if (isset($data['RawData'])) {
								$data['BeginTime'] = strtotime(date("Y-m-d", $beginTime) . ' ' . $data['RawData']['StartTime']);
							}
							if(empty($data['LoopEndDate']) || $data['BeginTime'] <= strtotime($data['LoopEndDate'])) {//下一次比赛是否达到终结时间
								if (isset($data['RawData']['CrossDayDisplayFlag']) && $data['RawData']['CrossDayDisplayFlag'] && !empty($data['RawData']['CrossDayDisplayTime'])) { //跨天显示模式
									$data['DisplayTime'] = strtotime(date("Y-m-d", $tmpBeginTime) . ' ' . $data['RawData']['CrossDayDisplayTime']);
								} else { //当天显示模式
									$data['DisplayTime'] = strtotime(date("Y-m-d", $beginTime) . ' ' . date("H:i:s", strtotime($data['RawData']['DisplayTime'])));
								}
								$cron->add(date('YmdHi', $data['DisplayTime']), 'FIXED_MATCH_DISPLAY', $data);
							}
						}
					}
				} else { //单场循环赛
					if(empty($data['LoopEndDate']) || $data['BeginTime'] <= strtotime($data['LoopEndDate'])){//下一次比赛是否达到终结时间
						if (isset($data['RawData']['CrossDayDisplayFlag']) && $data['RawData']['CrossDayDisplayFlag'] && !empty($data['RawData']['CrossDayDisplayTime'])) {
							$data['DisplayTime'] = strtotime(date("Y-m-d", $tmpBeginTime) . ' ' . $data['RawData']['CrossDayDisplayTime']);
							$cron->add(date('YmdHi', $data['DisplayTime']), 'FIXED_MATCH_DISPLAY', $data);
						} else {
							$data['DisplayTime'] = $displayTime;
							$cron->add(date('YmdHi', $data['DisplayTime']), 'FIXED_MATCH_DISPLAY', $data);
						}
					}
				}
			}
		}

		/*循环赛(兼容老版本)
		if (isset($data['MatchLoop']) && !empty($data['MatchLoop'])) {
            //添加任务检测记录
            $keyDisplay = 'FIXED_MATCH_DISPLAY_'.date('Ymd', $displayTime).'_'.$data['MatchId'];
		    $cronkvs = Model::getInstance('main.cronkvs');
            $cronkvs->set($keyDisplay, json_encode($data));
		}*/

		/*if (!empty($data['ShangJiaAwards']) && self::shangjiaStockIsEmpty($data['ShangJiaAwards'])) { //商家实物库存检测
			App::$log->add(Log::INFO, "Fixed Match Error::比赛未能正常开启(原因:商家实物奖励库存不足)");
			return;
		}*/

		//比赛列表配置
		$config = self::getConfigData($data, $tmpBeginTime);

		//推送通知消息
		if ($data['PushFlag'] && $data['PushMessage'] && $data['PushTime']) {
			$pushInfo = array('title'=>$data['PushTitle'], 'message'=>$data['PushMessage'], 'pushtype'=>$data['PushType']);
			$pushInfo['matchid'] = $matchId;
			$pushTime = $tmpBeginTime - 60*intval($data['PushTime']);
			$cron->add(date('YmdHi', $pushTime), 'FIXED_MATCH_PUSH_NOTICE', $pushInfo);
		}

		//记录本次计划任务-定时赛
		App::$log->add(Log::INFO,"Fixed-Cron-Report//matchid:{$matchId},info:".json_encode($config));
		//更新比赛信息
		$redis   = Model::getInstance('serverconfig.srvcfg', 'redis');
		$status  = $redis->setHashItem('SRV_COMMON_MATCH_HASH', "match{$matchId}" , $config);

		if($status){
		    //加入到比赛列表(新版)
		    if ((isset($data['MatchRegions']) && !empty($data['MatchRegions'])) || (isset($data['RegionId']) && !empty($data['RegionId']))) {
			    $match = array();
			    $match['matchid'] = $matchId;
			    //$match['minversion'] = !empty($data['MinVersion']) ? $data['MinVersion'] : 0;
			    $match['minversionand'] = !empty($data['MinVersionAnd']) ? intval($data['MinVersionAnd']) : 0;
			    $match['maxversionand'] = !empty($data['MaxVersionAnd']) ? intval($data['MaxVersionAnd']) : 0;
			    $match['minversionios'] = !empty($data['MinVersionIos']) ? intval($data['MinVersionIos']) : 0;
			    $match['maxversionios'] = !empty($data['MaxVersionIos']) ? intval($data['MaxVersionIos']) : 0;
			    $match['displaytime'] = $tmpDisplayTime;
			    $match['begintime'] = $tmpBeginTime;
				$match['looptype'] = $data['MatchLoopType'];
				$match['firstbegintime'] = strtotime(date("Y-m-d", $tmpBeginTime) . ' '. date("H:i:s", $data['FirstBeginTime'])); //首场开始时间
				$match['loopinterval'] = $data['MatchLoopInterval']; //循环间隔
				$match['loopendtime'] = $data['LoopEndTime']; //循环截至时间
				$match['addrobotflag'] = $data['AddRobotFlag']; //添加机器人标识
				$match['configid'] = $data['MatchConfigId']; //比赛配置ID
				$match['gamename'] = $data['GameName']; //游戏名称
				$match['matchentrycode'] = $data['MatchEntryCode']; //比赛验证码
				$match['matchentryinfo'] = $data['MatchEntryInfo']; //比赛验证码获取方式
				$match['matchtags'] = isset($data['MatchTags']) ? $data['MatchTags']:array(); //比赛标签
				if (!empty($data['MatchRegions'])) {
			    	$regions = $data['MatchRegions'];
				} else {
					$regions = array($data['RegionId']);
				}
				if (!empty($regions)) {
					foreach ($regions as $regionId) {
			    		$redis->getRedis()->hSet('FIXED_MATCH_LIST_HASH_'.$regionId, $matchId, json_encode($match));
					}
				}
			    if (isset($data['RawData'])) {
			    	$data['RawData']['MatchId'] = strval($matchId);
			    	$data['RawData']['DisplayTime'] = date("H:i:s", $tmpDisplayTime);
			    	$data['RawData']['StartTime'] = date("H:i:s", $tmpBeginTime);
			    	$data['RawData']['StartDate'] = date("Y-m-d", $tmpBeginTime);
			    	//$data['RawData']['MatchRewardCount'] = '0';
			    	$redis->getRedis()->hSet('FIXED_MATCH_CONF_HASH', $matchId, json_encode($data['RawData']));
			    }
			    //清除已配置比赛ID
			    if (!isset($data['MatchId']) || empty($data['MatchId'])) {
			    	$redis->getRedis()->hDel('FIXED_MATCH_KEY_LIST', $matchId);
			    }
		    	//通知Server
		    	$srv = mserver(CFG('@core', 'server.limitctrl'));
				$status = $srv->openFixedMatch($matchId);
				if (!$status) { //通知失败日志
					App::$log->add(Log::ERROR, "[Crond] Fixed-Time-Match open failed! //errno = " . $srv->errno());
				} else {
					App::$log->add(Log::INFO, "[Crond] Open Fixed Match Success:{$matchId}");
				}
		    }
		} else {
			App::$log->add(Log::ERROR, "[Crond] ADD FIXED MATCH DISPLAY FAIL!");
		}
	}

	/**
	 * 更新定时赛数据(循环赛)
	 * @param array $data
	 */
	public static function FIXED_MATCH_UPDATE_DATA($data){
		if (empty($data['MatchId']) || empty($data['StartDate'])) {
			return;
		}

		$redis = REDIS('serverconfig');
		$match = $redis->hGet('FIXED_MATCH_CONF_HASH', $data['MatchId']);
		if ($match) {
		    $match = json_decode($match, true);
		    $match['StartDate'] = $data['StartDate'];
		    $redis->hSet('FIXED_MATCH_CONF_HASH', $data['MatchId'], json_encode($match));
		}
	}

	/**
	 * 定时赛开始前推送通知
	 * @param array $arg
	 */
	public static function FIXED_MATCH_PUSH_NOTICE($data){
		if( empty($data['message']) || empty($data['matchid']) ){
			return ;
		}
		//获取报名用户
		$start = date('Y-m-d H:i:s');
		$offlineUser= array();
		$redis	= REDIS("matchuser");
		$online = Model::getInstance("main.online");
		$users = Model::getInstance("users.users");
		$pushModel  = Model::getInstance("devices.pushtoken");
		$usercache	= Model::getInstance("usercache.usercache", "redis");

		//保持redis连接不被断开
		if (!$redis->ping()) {
			return ;
		}
		//保持DB不被断开
		if (($online->getDB()->connect() && !$online->getDB()->ping()) || ($users->getDB()->connect() && !$users->getDB()->ping())) {
			touch(App::$path.'data/crond.die'); //出现异常重启计划进程
			return ;
		}

		$robotStart	= CFG("gambling", "robot_start_mid");
		$tempUser = $redis->hGetAll("TMFX_MATCH_USER{$data['matchid']}");
		foreach ($tempUser as $mid => $v) {
			if ($mid < $robotStart && ($data['pushtype'] == 2 || (-1 === $online->userState($mid)))) { //所有用户或者离线用户
				$uinfo = $usercache->get($mid);
				if (isset($uinfo['app_id']) && $uinfo['app_id']>0) {
					$pushModel->sendMessage($mid, $uinfo['app_id'], $data['message'], $data['title']);
				}
			}
		}
		App::$log->add(Log::DEBUG, "[Crond] FIXED_MATCH_PUSH_NOTICE({$data['matchid']}) start at {$start}, now finished");
	}

	/**
	 * 修改游戏场次配置并通知Server生效
	 */
	public static function GAME_LEVEL_CONFIG_SET($arg) {
		if(empty($arg['gid']) || empty($arg['level']) || empty($arg['config'])) {
			return;
		}
		$redis = REDIS('serverconfig');
		$key = "GamePublic_{$arg['gid']}_{$arg['level']}";
		$value = $redis->get($key);
		$value = (array)json_decode($value, true);
		foreach($arg['config'] as $k => $v) {
			if(array_key_exists($k, $value)) {
				$value[$k] = is_numeric($v) ? intval($v) : $v;
			}
		}
		$status = $redis->set($key, json_encode($value));
		if(!$status) {
			App::$log->add(Log::ERROR, "[Crond] Game level config save failed! //{$key} = '" . json_encode($value));
			return;
		}
		$srv = mserver(CFG('@core', 'server.config'));
		$status = $srv->updateGameLevelConfig($arg['gid'], $arg['level'], MServer::GAME_CONFIG_TYPE_COMMON);
		if(!$status) {
			App::$log->add(Log::ERROR, "[Crond] Game level config refresh failed! //errno = " . $srv->errno());
		}
	}

	/**
	 * 发送广播
	 * @param string $arg 广播内容
	 */
	public static function BROADCAST($arg) {
		$arg = strval($arg);
		if(empty($arg)) {
			return;
		}
		$srv = mserver(CFG('@core', 'server.broadcast'));
		$msg = array(
			'send_time'	=> time(),
			'nick'		=> '管理员',
			'title'		=> '系统公告',
			'content'	=> mb_substr($arg, 0, 64,'utf-8'),
			'type'		=> 1,
		);
		$status = $srv->broadcastMessage('', json_encode($msg));
		if(!$status) {
			App::$log->add(Log::DEBUG, "[Crond] Broadcast message failed! //errno=" . $srv->errno());
		}
	}

	/**
	 * 临时公告
	 * @param string $arg 广播内容
	 */
	public static function BCNOTICE($arg) {
		if(empty($arg) || empty($arg['content'])) {
			return;
		}
		$srv = mserver(CFG('@core', 'server.broadcast'));
		$msg = array(
			'send_time'	=> time(),
			'nick'		=> '管理员',
			'title'		=> $arg['title'] ? $arg['title'] : '系统公告',
			'content'	=> mb_substr($arg['content'], 0, 64,'utf-8'),
			'type'		=> 3,
		);
		$status = $srv->broadcastMessage('', json_encode($msg));
		if(!$status) {
			App::$log->add(Log::DEBUG, "[Crond] Broadcast notice failed! //errno=" . $srv->errno());
		}
	}

	/**
	 * 获取可用比赛ID(从比赛ID池中选择)
	 */
	public static function getAvailableMatchId($gameid) {
		$redis = REDIS('serverconfig');
		$cachekey = 'FIXED_MATCH_KEY_LIST';
		$list = $redis->hGetAll($cachekey);

		if (!empty($list)) {
		    foreach ($list as $matchid => $find) {
				if ($find == $gameid) {
					return $matchid;
				}
		    }
		}

		return 0;
	}

	/**
	 * 商家实物奖励库存是否为空
	 * @param array $goodsIds
	 */
	public static function shangjiaStockIsEmpty($goodsIds) {
		$goodsIds = implode(",", $goodsIds);
		$where = "and goods_id in ({$goodsIds})";
		$model = Model::getInstance("shangjia.shangjia_goods");
		$data = $model->getList($where, 0, 100);
		if (!empty($data)) {
			foreach ($data as $item) {
				if ($item['available_stock']<=0) {
					App::$log->add(Log::INFO, "商家实物奖励库存不足: goodsId::{$item['goods_id']}");
					return true;
				}
			}
		}
		return false;
	}

	public static function getConfigData($data, $beginTime) {
		$config                           = array('desc'=>'定时赛比赛场', 'value'=>array());
		$config['value']['Des']           = array('desc'=>'描述', 'value'=>'定时赛');
		$config['value']['Name']          = array('desc'=>'名称', 'value'=>$data['MatchName']);
		$config['value']['MatchIcon']     = array('desc'=>'比赛图片', 'value'=>$data['MatchIcon']);
		$config['value']['IconWeight']    = array('desc'=>'图片权重', 'value'=>$data['IconWeight']);
		$config['value']['MatchAdvertIcon'] = array('desc'=>'运营广告图片', 'value'=>$data['MatchAdvertIcon']);
		$config['value']['GameId']        = array('desc'=>'游戏ID', 'value'=>$data['GameId']);
		$config['value']['Fee']           = array('desc'=>'报名费', 'value'=>strval($data['Fee']));
		$config['value']['TotalNum']      = array('desc'=>'最低开赛人数', 'value'=>$data['TotalNum']);
		$config['value']['StartTime']     = array('desc'=>'开始时间', 'value'=>date('H:i:s', $beginTime));
		$config['value']['StartDate']     = array('desc'=>'开始日期', 'value'=>date('Y-m-d', $beginTime));
		$config['value']['AllowWaitTime'] = array('desc'=>'允许提前进入时间', 'value'=>$data['AllowWaitTime']);
		$config['value']['DetailDes']     = array('desc'=>'详细规则', 'value'=>$data['DetailDes']);
		$config['value']['PrizeNum']      = array('desc'=>'奖金数量', 'value'=>$data['MatchRewardCount']);
		$config['value']['rewardDescribe'] = array('desc'=>'运营推广文字', 'value'=>$data['RewardDescription']);

		foreach ($data['Reward'] as $rank => $v) {
		    $str = '-';
			if (!empty($v[0]) || !empty($v[1]) || !empty($v[2])) {
				$xuniAwad = array();
				if ($v[0]) {
					$xuniAwad[] = intval($v[0])>0 ? "{$v[0]}金币" : $v[0];
				}
				if ($v[1]) {
					$xuniAwad[] = intval($v[1])>0 ? "{$v[1]}钻石" : $v[1];
				}
				if ($v[2]) {
				    $xuniAwad[] = intval($v[2])>0 ? "{$v[2]}大师分" : $v[2];
				}
				$str = "第{$rank}名 ".implode('+', $xuniAwad);
			}

			if ($rank == 1) {
				$Champion = explode(' ', $str);
			}
			$config['value']["Prize".($rank-1)] = array('desc' => '奖励', 'value' => $str);
		}

		$config['value']['Champion']            = array('desc'=>'冠军奖励', 'value'=>isset($Champion[1]) ? $Champion[1]:'-');
		$config['value']['Type']                = array('desc'=>'比赛类型', 'value'=>'3');
		$config['value']['SignUpCondition']     = array('desc'=>'报名条件', 'value'=>$data['SignUpCondition']);
		$config['value']['SignUpFeeInfo']       = array('desc'=>'报名费用信息(格式:TypeId,Num,Detail)', 'value'=>$data['SignUpFeeInfo']);
		if (isset($data['SignUpFeeInfo_2'])) {
		    $value = !empty($data['SignUpFeeInfo_2']) ? json_encode($data['SignUpFeeInfo_2']) : '';
			$config['value']['SignUpFeeInfo_2']   = array('desc'=>'报名费用信息(JSON格式)', 'value'=>$value);
		}
		if (isset($data['Prize_List'])) {
		    $value = !empty($data['Prize_List']) ? json_encode($data['Prize_List']) : '';
			$config['value']['Prize_List']        = array('desc'=>'奖励信息列表(JSON格式)', 'value'=>$value);
		}
		if (isset($data['AwardsRangeList'])) {
		    $value = !empty($data['AwardsRangeList']) ? json_encode($data['AwardsRangeList']) : '';
			$config['value']['AwardsRangeList'] = array('desc'=>'奖励排名分组列表(JSON格式)', 'value'=>$value);
		}
		$config['value']['AwardIcon'] = array('desc'=>'奖励图片', 'value'=>json_encode($data['AwardIcon']));
		if (isset($data['StageGroupList'])) {
		    $value = !empty($data['StageGroupList']) ? json_encode($data['StageGroupList']) : '';
			$config['value']['StageGroupList'] = array('desc'=>'比赛阶段分组数据(JSON格式)', 'value'=>$value);
		}
		
		//复活赛
		$config['value']['ReviveInfo'] = array('desc'=>'复活赛信息', 'value'=>'');
		$config['value']['isFhMatch'] = array('desc'=>'是否是复活赛', 'value'=>$data['isFhMatch']);
		if($data['isFhMatch']){
			$config['value']['ReviveInfo']['value'] = json_encode($data['ReviveInfo']);
		}

		return $config;
	}
}
