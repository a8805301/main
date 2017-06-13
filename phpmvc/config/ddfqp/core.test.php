<?php
return array(
	//数据库配置
	'db' => array(
		'main'		=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_main'),
		'users'		=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_users'),
		'common'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_common'),
		'games'		=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_games'),
		'playinfo'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_playinfo'),
		'devices'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_devices'),
		'friends'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_friends'),
		'logs'		=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_logs'),
		'partners'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_partners'),
		'activity'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_activity'),
		'wechat'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_wechat'),
		'team'		=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_team'),
		'missiondata'=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_missiondata'),
		'agents'    => array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_agents'),
		'shangjia'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_shangjia'),
		'gather'    => array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_gather'),
		'proxy'		=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_proxy'),
		'admin'		=> array('192.168.27.169:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_admin'),
		'match'		=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_match'),
		'dr_main'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_main'),
		'dr_users'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_users'),
		'dr_common'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_common'),
		'dr_games'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_games'),
		'dr_devices'=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_devices'),
		'dr_friends'=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_friends'),
		'dr_partners'=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_partners'),
		'dr_activity'=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_activity'),
		'dr_team'	=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_team'),
		'dr_missiondata'=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_missiondata'),
		'dr_agents'  => array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_agents'),
		'dr_shangjia'=> array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_shangjia'),
		'dr_gather'  => array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_gather'),
		'dr_proxy'	 => array('192.168.27.169:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'df_proxy'),
	),
	//Memcached配置
	'mc' => array(
		'master'	=> array('192.168.27.168', 11200),
		'limit'		=> array('192.168.27.168', 11200),
		'system'	=> array('192.168.27.168', 11200),//系统热点数据缓存
		'notice'	=> array('192.168.27.168', 11200),//系统热点数据缓存
		'gamepos'	=> array('192.168.27.168', 11200),//系统热点数据缓存
		'luaversion'	=> array('192.168.27.168', 11200),//系统热点数据缓存
		'bypass'	=> array('192.168.27.168', 11200),//通行证热点数据
	),
	//Redis配置
	'redis' => array(
		'idmap'				=> array('192.168.27.168', 4505),
		'master'			=> array('192.168.27.168', 4506),
		'rank'				=> array('192.168.27.168', 4506),
		'gamb'				=> array('192.168.27.168', 4552),//牌局解析redis
		'online'			=> array('192.168.27.168', 4552),
		'gambcache' 		=> array('192.168.27.168', 4506),//解析后用户牌局信息缓存
		'playergambcache'	=> array('192.168.27.168', 4506),//玩家牌局缓存
		'serverconfig'		=> array('192.168.27.168', 4516),//server配置存储redis
		'serverconfigslave'	=> array('192.168.27.168', 4516), //server配置存储redis
		'serverupgrade'		=> array('192.168.27.168', 4551),//Server升级控制redis
		'jobs' 				=> array('192.168.27.168', 4506),
		'match'				=> array('192.168.27.168', 4516),//比赛redis
		'usercache'			=> array('192.168.27.168', 4505), //用户信息缓存
		'lbs'				=> array('192.168.27.168', 4506), //LBS专用
		'props'				=> array('192.168.27.168', 4553),//玩家道具
		'missiondata'		=> array('192.168.27.168', 4506), //任务数据缓存
		'propsstream'		=> array('192.168.27.168',4506),//道具流水日志
		'push'				=> array('192.168.27.168',4506),//push奖励
		'defense'			=> array('192.168.27.168', 4506),//防刷策略
		'contest'			=> array('192.168.27.168', 4506),//比赛日志
		'matchuser'			=> array('192.168.27.168', 4612),//比赛用户信息
		'fastmatch'			=> array('192.168.27.168', 4516, 1),//快速比赛日志
		'reward'			=> array('192.168.27.168', 4506),//奖励限制(这个redis不能满)
		'gather'			=> array('192.168.27.168', 4506),//物品箱信息缓存
		'alarm'				=> array('192.168.27.168', 4517),//报警收集日志
		'deploy'			=> array('192.168.27.168', 4517),//后台共享配置专享
		'agent'				=> array('192.168.27.168', 4506),//代理商
		'wechat'			=> array('192.168.27.168', 4506),//微信
		'session'			=> array('192.168.27.168', 4506),//session
		'sessionstat'		=> array('192.168.27.168', 4506),//sessionstat
		'privateroom'		=> array('192.168.27.168', 4505),//约牌房
		'matchinvite'		=> array('192.168.27.168', 4505),//邀请赛redis
		'notifyserver'		=> array('192.168.27.168', 4517),//配置更新通知server
		'baseconfig'		=> array('192.168.27.168', 4505),//基础公用配置
		'jubao'				=> array('192.168.27.168', 4505),//举报数据
		'userlogin'			=> array('192.168.27.168', 4506), // 用户在线状态数据
		'ucache0'			=> array('192.168.27.168', 4505), // 用户信息
		'ucache1'			=> array('192.168.27.168', 4505), // 用户信息
		'ucache2'			=> array('192.168.27.168', 4505), // 用户信息
		'ucache3'			=> array('192.168.27.168', 4505), // 用户信息
		'ucache4'			=> array('192.168.27.168', 4505), // 用户信息
		'ucache5'			=> array('192.168.27.168', 4505), // 用户信息
		'ucache6'			=> array('192.168.27.168', 4505), // 用户信息
		'ucache7'			=> array('192.168.27.168', 4505), // 用户信息
		'ucache8'			=> array('192.168.27.168', 4505), // 用户信息
		'ucache9'			=> array('192.168.27.168', 4505), // 用户信息
		'install'			=> array('192.168.27.168', 4505), // 安装游戏记录
		'sitetake0'			=> array('192.168.27.168', 4505), // 升级配置拉取记录
		'sitetake1'			=> array('192.168.27.168', 4505), // 升级配置拉取记录
		'morra'				=> array('192.168.27.168', 4505), // 猜拳小游戏记录
		'streamlog'			=> array('192.168.27.168', 4505),
		'a2m'				=> array('192.168.27.168', 4506), //代理用户关系
		'matchtotal'		=> array('192.168.27.168', 4517), //比赛统计
		'userscore'			=> array('192.168.27.168', 4505, '', 1), //用户信誉缓存
		'lastplay'			=> array('192.168.27.168', 4505, '', 2), //用户最后玩牌
		'configdata'		=> array('192.168.27.168', 4517),//config模型配置数据，提供给lua server使用，代替mc
		'simulator'			=> array('192.168.27.168', 4506),//模拟器redis
		'userdevicescache'  => array('192.168.27.168', 4506),//常用设备
		'matchasync'		=> array('192.168.27.168', 4517), //比赛异步上报
		'matchfreetimes'    => array('192.168.27.168', 4500),//人满开赛免费次数
	),
	//Socket服务配置
	'server' => array(
		'mserver'	=> array('192.168.27.165', 19930), //金币服务
		'broadcast'	=> array('192.168.27.165', 19930), //广播服务
		'config'	=> array('192.168.27.165', 19929), //更新配置服务
		'upgrade'	=> array('192.168.27.165', 0), //升级控制服务
		'phpudp'	=> array('192.168.27.159', 6341), //PHP自己的UDP协议Server
		'dataserver'=> array('192.168.27.159', 6342), //PHP自己的UDP协议Server
		'monitorsrv'=> array('192.168.27.159', 9200), //接口监控server
		'cmc'		=> array('127.0.0.1', 6345), //云计算监控中心
		'push'		=> array('127.0.0.1', 9220), //推送中转Server
		'dispatch'	=> array('127.0.0.1', 6901),//牌局分发器
		'logudp'	=> array('127.0.0.1', 9001),//日志服
		'matchctl'	=> array('http://pcususus01.ifere.com/match/index.php', 80), //PHP自己的比赛管理Server
		'rpttserver'=> array('127.0.0.1', 6346),//信誉系统服务
	),
	//WEB请求入口地址
	'site_url' => 'http://pcususus01.ifere.com/ddfqp/',
	//各WEB服务器独立URL列表
	'site_workers_url' => array(
		'http://pcususus01.ifere.com/ddfqp/',
	),
	//表名前缀
	'table_prefix' => 'dfqp_',
	//CDN配置
	'cdn' => array(
		'url'		=> 'http://uchead.static.17c.cn/dfqp/',
		'https_url'	=> 'https://dfqppic.266.com/dfqp/',
		'upload'	=> 'http://dfqpicon.ifere.com/uploadicon.php', // http://cdnsource.17c.cn/dfqp/webphp/
		'uploadpic'	=> 'http://dfqpicon.ifere.com/uploadpic.php', // http://cdnsource.17c.cn/dfqp/webphp/
		'xapi'		=> 'http://dfqpicon.ifere.com/api.php',
		'qrcode'	=> 'http://dfqpicon.ifere.com/qrcode.php',
		'url_bigfile' => 'http://365.oa.com/cdn-bigfile/',
		'src_bigfile' => 'http://365.oa.com/cdn-bigfile/',
		'static_site' => 'http://uchead.static.17c.cn/dfqp/webphp/static_files_api.php'
	),
	//验证码获取url地址
	'verifyurl'	=> 'http://passport.boyaa.com/authorcode.php',
	//会话有效期
	'session_life' => 900,
	//消息队列KEY
	'sysvmsg_key' => 8083,
	//运营组URL
	'opreation_url' => array(
		//地区对应活动地址
		1 => 'http://pcusspus01.ifere.com/?m=activities',
		2 => 'http://mvusspac01.ifere.com/index.php?m=activities&appid=9401',
		3 => 'http://mvusspac02.ifere.com/index.php?m=activities&appid=9402',
		4 => 'http://mvussplp02.ifere.com/index.php?m=activities&appid=9404',
		6 => 'http://mvussplp02.ifere.com/index.php?m=activities&appid=9405',
		9 => 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9411', //济南
		10=> 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9410', //营口
		11=> 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9409', //昆明
		12=> 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9412',//南充
		13=> 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9413',//内江
		14=> 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9414',//泸州
		15=> 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9417',//鞍山
		16=> 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9416',//丹东
		18=> 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9419',//南昌
		19=> 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9418',//我是大牌
		17=> 'http://192.168.204.68/operating/web/index.php?m=activities&p=index&appid=9415',//盘锦
		20=> 'http://qujingpoker.ifere.com/?m=activities&p=index&appid=9422',//曲靖
		21=> 'http://zibopoker.ifere.com/?m=activities&p=index&appid=9420',//淄博
		22=> 'http://chaoyangpoker.ifere.com/?m=activities&p=index&appid=9424',//朝阳
		23=> 'http://liaoyangpoker.ifere.com/?m=activities&p=index&appid=9421',//辽阳
		24=> 'http://jinzhoupoker.ifere.com/?m=activities&p=index&appid=9428',//锦州
		25=> 'http://fuxinpoker.ifere.com/?m=activities&p=index&appid=9423',//阜新
		26=> 'http://huludaopoker.ifere.com/?m=activities&p=index&appid=9426',//葫芦岛
		27=> 'http://honghepoker.ifere.com/?m=activities&p=index&appid=9427',//聊城
		28=> 'http://liaochengpoker.ifere.com/?m=activities&p=index&appid=9425',//聊城
		29=> 'http://benxipoker.ifere.com/?m=activities&p=index&appid=9430',//本溪
		30=> 'http://tielingpoker.ifere.com/?m=activities&p=index&appid=9433',//铁岭
		31=> 'http://shenyangpoker.ifere.com/?m=activities&p=index&appid=9432',//沈阳
		32=> 'http://fushunpoker.ifere.com/?m=activities&p=index&appid=9431',//抚顺
		33=> 'http://jiujiangpoker.ifere.com/?m=activities&p=index&appid=9429',//九江
		34=> 'http://dalipoker.ifere.com/?m=activities&p=index&appid=9434',//大理
		35=> 'http://yuxipoker.ifere.com/?m=activities&p=index&appid=9435',//玉溪
		38=> 'http://lijiangpoker.ifere.com/?m=activities&p=index&appid=9437',//丽江
		39=> 'http://chuxiongpoker.ifere.com/?m=activities&p=index&appid=9438',//楚雄
		41=> 'http://jilinpoker.ifere.com/?m=activities&p=index&appid=9442',//吉林
		42=> 'http://changchunpoker.ifere.com/?m=activities&p=index&appid=9440',//长春
		43=> 'http://sipingpoker.ifere.com/?m=activities&p=index&appid=9441',//四平
		44=> 'http://songyuanpoker.ifere.com/?m=activities&p=index&appid=9439',//松原
		45=> 'http://liaoyuanpoker.ifere.com/?m=activities&p=index&appid=9461',//辽源
		47=> 'http://daqingpoker.ifere.com/?m=activities&p=index&appid=9445',//大庆
		48=> 'http://jiningpoker.ifere.com/?m=activities&p=index&appid=9446',//济宁
		51=> 'http://baishanpoker.ifere.com/?m=activities&p=index&appid=9444',//白山
		52=> 'http://yanbianpoker.ifere.com/?m=activities&p=index&appid=9443',//延边
		49=> 'http://dehongpoker.ifere.com/?m=activities&p=index&appid=9447',//德宏
		46=> 'http://haerbinpoker.ifere.com/?m=activities&p=index&appid=9448',//哈尔冰
		50=> 'http://xsbnpoker.ifere.com/?m=activities&p=index&appid=9449',//西双版纳
		54=> 'http://guiyangpoker.ifere.com/?m=activities&p=index&appid=9450',//贵阳
		40=> 'http://tonghuapoker.ifere.com/?m=activities&p=index&appid=9451',//通化
		53=> 'http://baichengpoker.ifere.com/?m=activities&p=index&appid=9452',//白城
		56=> 'http://linyipoker.ifere.com/?m=activities&p=index&appid=9453',//临沂
		57=> 'http://shangraopoker.ifere.com/?m=activities&p=index&appid=9457',//上饶
		59=> 'http://wenshanpoker.ifere.com/?m=activities&p=index&appid=9454',//文山
		61=> 'http://puerpoker.ifere.com/?m=activities&p=index&appid=9455',//普洱
		60=> 'http://zunyipoker.ifere.com/?m=activities&p=index&appid=9456',//遵义
		62=> 'http://dezhoupoker.ifere.com/?m=activities&p=index&appid=9460',//德州
		64=> 'http://yantaipoker.ifere.com/?m=activities&p=index&appid=9459',//烟台
		67=> 'http://liupanshuipoker.ifere.com/?m=activities&p=index&appid=9462',//六盘水
	),
	//Im社交URL
	'im_url' => array(
		'url'=>array('http://pcususus01.ifere.com/ddfqp/index.php?action=externals.imdir'),//url地址
		'svr_ver'=>1455612655,//更新社交URL的时候需要修改此时间戳，根据客户端请求时带的时间戳判断是否返回数据
		'exchange'=> 'http://192.168.0.29:19034/msgproxy?',//新老版本转接服
	),
	//Im server分发端口
	'im_dir' => array(
		array('ip'=>'106.15.108.84', 'port'=>9956),
		array('ip'=>'106.15.108.84', 'port'=>9957),
		array('ip'=>'106.15.108.84', 'port'=>9958),
		array('ip'=>'106.15.108.84', 'port'=>9959),
	),
	//停服时间配置
	//'shtutdown_time' => array('start' => 1406149200, 'end' => 1406154600),
	//分享域名
	'share_url' => array(
		1 => 'http://pcususus01.ifere.com/ddfqp/', //四川
		2 => 'http://pcususus01.ifere.com/ddfqp/',//大连
		3 => 'http://pcususus01.ifere.com/ddfqp/',//山东
		4 => 'http://pcususus01.ifere.com/ddfqp/',//宜宾
		5 => 'http://pcususus01.ifere.com/ddfqp/',//四国军棋
		6 => 'http://pcususus01.ifere.com/ddfqp/',//自贡
		8 => 'http://pcususus01.ifere.com/ddfqp/', //乐山
		9 => 'http://pcususus01.ifere.com/ddfqp/', //济南
		10=> 'http://pcususus01.ifere.com/ddfqp/', //营口
		11=> 'http://pcususus01.ifere.com/ddfqp/', //昆明
		12=> 'http://pcususus01.ifere.com/ddfqp/', //南充
		13=> 'http://pcususus01.ifere.com/ddfqp/',//内江
		14=> 'http://pcususus01.ifere.com/ddfqp/',//泸州
		15=> 'http://pcususus01.ifere.com/ddfqp/',//鞍山
		16=> 'http://pcususus01.ifere.com/ddfqp/',//丹东
		17=> 'http://pcususus01.ifere.com/ddfqp/',//盘锦
		18=> 'http://pcususus01.ifere.com/ddfqp/',//南昌
		19=> 'http://pcususus01.ifere.com/ddfqp/',//我是大牌
	),
	//公共部门的升级管理平台接口地址
	'update_center_api' => 'http://pkgserver.ifere.com/Api/LocalBoard/onlineUpdateInfoForTest',
);
