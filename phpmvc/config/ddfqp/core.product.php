<?php
return array(
	//数据库配置
	'db' => array(
		'main'		=> array('192.168.20.177:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_main'),
		'users'		=> array('192.168.20.177:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_users'),
		'common'	=> array('192.168.20.177:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_common'),
		'games'		=> array('192.168.20.177:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_games'),
		'playinfo'	=> array('192.168.20.178:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_playinfo'),
		'friends'	=> array('192.168.20.177:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_friends'),
		'logs'		=> array('192.168.20.203:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'ddfqp_logs'),
		'activity'	=> array('192.168.20.177:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_activity'),
		'devices'	=> array('192.168.20.178:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_devices'),
		'wechat'	=> array('192.168.20.178:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_wechat'),
		'team'		=> array('192.168.20.178:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_team'),
		'missiondata'=> array('192.168.20.178:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_missiondata'),
		'shangjia'  => array('192.168.20.177:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_shangjia'),
		'gather'    => array('192.168.20.178:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_gather'),
		'proxy'    	=> array('192.168.20.178:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_proxy'),
		'admin'    	=> array('192.168.20.178:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_admin'),
		'agents'	=> array('192.168.20.177:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_agents'),//废弃库
		'partners'	=> array('192.168.20.177:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_partners'),//废弃库
		'match'		=> array('192.168.20.177:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_match'),
		'reputation'=> array('192.168.20.178:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_reputation'), // 信誉体系
		// 'gamb' 		=> array('192.168.0.30:3388', 'dfqp', 'hxfcJ5amadaz8doYjjrwvr', 'dfqp_gamb'), //牌局记录
		// dr库配置(qpadmin在使用)
		'dr_main'	=> array('192.168.20.179:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_main'),
		'dr_users'	=> array('192.168.20.179:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_users'),
		'dr_common'	=> array('192.168.20.179:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_common'),
		'dr_games'	=> array('192.168.20.179:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_games'),
		'dr_devices'=> array('192.168.20.180:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_devices'),
		'dr_friends'=> array('192.168.20.179:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_friends'),
		'dr_proxy'  => array('192.168.20.180:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_proxy'),
		'dr_partners'=> array('192.168.20.179:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_partners'),
		'dr_activity'=> array('192.168.20.179:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_activity'),
		'dr_team'	=> array('192.168.20.180:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_team'),
		'dr_missiondata'=> array('192.168.20.180:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_missiondata'),
		'dr_shangjia'=> array('192.168.20.179:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_shangjia'),
		'dr_gather'  => array('192.168.20.180:3388', 'selectuser', 'zifguKm5qk2xtjHnjyygind$', 'df_gather'),
		'dr_wechat'	=> array('192.168.20.180:3388', 'pre_dfqp', 'rPnvq52rvnmmiuyosndg', 'df_wechat'),
	),
	//Memcached配置
	'mc' => array(
		'master'	=> array('192.168.20.192', 11200),
		'limit'		=> array('192.168.20.194', 11302),
		'system'	=> array('192.168.20.192', 11202),//系统热点数据缓存
		'notice'	=> array('192.168.20.192', 11203),
		'gamepos'	=> array('192.168.20.192', 11204),
		'luaversion'=> array('192.168.20.192', 11205),
		'bypass'	=> array('192.168.20.192', 11204),//博雅通行证热点数据
	),
	//Redis配置
	'redis' => array(
		'idmap'				=> array('192.168.20.193', 4513),
		'master'			=> array('192.168.20.194', 4600),
		'rank'				=> array('192.168.20.193', 4511),
		'gamb'				=> array('192.168.20.194', 4602),//牌局解析redis
		'online'			=> array('192.168.20.194', 4603),
		'gambcache' 		=> array('192.168.20.193', 4514),//解析后用户牌局信息缓存
		'playergambcache'	=> array('192.168.20.192', 4500),//玩家牌局缓存
		'serverconfig'		=> array('192.168.20.194', 4604),//server配置存储redis
		'serverconfigslave'	=> array( //server配置存储redis
			array('192.168.20.204', 4511),
			array('192.168.20.204', 4512),
			array('192.168.20.204', 4513),
			array('192.168.20.204', 4514),
			array('192.168.20.204', 4515),
			array('192.168.20.204', 4516),
			array('192.168.20.204', 4517),
			array('192.168.20.204', 4518),
			array('192.168.20.204', 4519),
			array('192.168.20.204', 4520),
		),
		'serverupgrade'		=> array('192.168.20.194', 4605),//Server升级控制redis
		'jobs' 				=> array('192.168.20.194', 4606),
		'match'				=> array('192.168.20.194', 4607),//比赛redis
		'usercache'			=> array('192.168.20.192', 4502), //用户信息缓存
		'lbs'				=> array('192.168.20.194', 4609), //LBS专用
		'props'				=> array('192.168.20.194', 4610),//玩家道具
		'missiondata'		=> array('192.168.20.194', 4611), //任务数据缓存
		'propsstream'		=> array('192.168.20.194', 4612),//道具流水日志
		'push'				=> array('192.168.20.194', 4613),//push奖励
		'defense'			=> array('192.168.20.194', 4613),//防刷策略
		'contest'			=> array('192.168.20.194', 4567),//比赛日志
		'matchuser'			=> array( //比赛用户信息(只有从库，主库192.168.20.194:4550)
			array('192.168.20.204', 4500),
			array('192.168.20.204', 4501),
			array('192.168.20.204', 4502),
			array('192.168.20.204', 4503),
			array('192.168.20.204', 4504),
			array('192.168.20.204', 4505),
			array('192.168.20.204', 4506),
			array('192.168.20.204', 4507),
			array('192.168.20.204', 4508),
			array('192.168.20.204', 4509),
			array('192.168.20.204', 4510),
		),
		'fastmatch'			=> array('192.168.20.194', 4550),//快速比赛日志
		'reward'			=> array('192.168.20.194', 4509),//奖励限制(这个redis不能满)
		'gather'			=> array('192.168.20.194', 4514),//物品箱信息缓存
		'alarm'				=> array('192.168.20.194', 4515),//后台PHP报警使用
		'deploy'			=> array('192.168.20.192', 4504),//后台共享配置专享
		'agent'				=> array('192.168.20.192', 4501),//代理商redis
		'session'			=> array('192.168.20.192', 4505),//session(在线相关)
		'sessionstat'		=> array('192.168.20.193', 4500),//session(在线相关)
		'privateroom'		=> array('192.168.20.194', 4518),//约牌房
		'matchinvite'		=> array('192.168.20.194', 4531),//邀请赛redis
		'cheatalarm'		=> array('192.168.20.192', 4507),//作弊预警信息存储
		'notifyserver'		=> array('192.168.20.192', 4508),//配置更新通知server
		'jubao'				=> array('192.168.20.192', 4509),//举报数据
		'baseconfig'		=> array('192.168.20.192', 4528),//基础公用配置
		'ucache0'			=> array('192.168.20.193', 4510), // 用户信息
		'ucache1'			=> array('192.168.20.193', 4501), // 用户信息
		'ucache2'			=> array('192.168.20.193', 4502), // 用户信息
		'ucache3'			=> array('192.168.20.193', 4503), // 用户信息
		'ucache4'			=> array('192.168.20.193', 4504), // 用户信息
		'ucache5'			=> array('192.168.20.193', 4505), // 用户信息
		'ucache6'			=> array('192.168.20.193', 4506), // 用户信息
		'ucache7'			=> array('192.168.20.193', 4507), // 用户信息
		'ucache8'			=> array('192.168.20.193', 4508), // 用户信息
		'ucache9'			=> array('192.168.20.193', 4509), // 用户信息
		'admintool'			=> array('192.168.20.193', 4512), // 后台工具redis
		'sitetake0'			=> array('192.168.20.192', 4515), // 配置下发
		'sitetake1'			=> array('192.168.20.192', 4516), // 配置下发
		'userlogin'			=> array('192.168.20.192', 4517), // 用户在线状态数据
		'install'			=> array('192.168.20.192', 4510), // 用户已经安装游戏
		'rcache0'			=> array('192.168.20.192', 4518), // 用户信誉信息
		'rcache1'			=> array('192.168.20.192', 4519), // 用户信誉信息
		'rcache2'   		=> array('192.168.20.192', 4520), // 用户信誉信息
		'rcache3'			=> array('192.168.20.192', 4521), // 用户信誉信息
		'rcache4'			=> array('192.168.20.192', 4522), // 用户信誉信息
		'rcache5'			=> array('192.168.20.192', 4523), // 用户信誉信息
		'rcache6'			=> array('192.168.20.192', 4524), // 用户信誉信息
		'rcache7'			=> array('192.168.20.192', 4525), // 用户信誉信息
		'rcache8'			=> array('192.168.20.192', 4526), // 用户信誉信息
		'rcache9'			=> array('192.168.20.192', 4527), // 用户信誉信息
		'morra'				=> array('192.168.20.192', 4531), // 猜拳小游戏记录
		'streamlog'			=> array('192.168.20.192', 4529), //牌局、金币流水队列
		'matchtotal'		=> array('192.168.20.192', 4532), // 比赛统计
		'wechat'			=> array('r-uf63b56de222fac4.redis.rds.aliyuncs.com', 6379, 'r-uf63b56de222fac4:Byredis123'),//微信
		'a2m'				=> array('r-uf63b56de222fac4.redis.rds.aliyuncs.com', 6379, 'uf63b56de222fac4:Byredis123'), // 代理用户关系
		'userscore'			=> array('r-uf63b56de222fac4.redis.rds.aliyuncs.com', 6379, 'uf63b56de222fac4:Byredis123', 1), // 活跃用户的信誉评分
		'userflags'			=> array('r-uf6a69bfd08adce4.redis.rds.aliyuncs.com', 6379, 'r-uf6a69bfd08adce4:Byredis123'), //用户标志位
		'configdata'		=> array('r-uf63b56de222fac4.redis.rds.aliyuncs.com', 6379, 'r-uf63b56de222fac4:Byredis123', 2),//config模型配置数据，提供给lua server使用，代替mc
		'userdevicescache'  => array('r-uf63b56de222fac4.redis.rds.aliyuncs.com', 6379, 'r-uf63b56de222fac4:Byredis123'),//常用设备  --占个位，避免上线的时候忘掉
		'simulator'			=> array('r-uf63b56de222fac4.redis.rds.aliyuncs.com', 6379, 'r-uf63b56de222fac4:Byredis123'),//
		'matchasync'		=> array('r-uf63b56de222fac4.redis.rds.aliyuncs.com', 6379, 'r-uf63b56de222fac4:Byredis123', 3),//比赛异步上报
		'matchfreetimes'		=> array('r-uf63b56de222fac4.redis.rds.aliyuncs.com', 6379, 'r-uf63b56de222fac4:Byredis123', 5),//人满开赛免费次数
	),
	//Socket服务配置
	'server' => array(
		'mserver'	=> array('192.168.20.172', 6201), //金币服务
		'broadcast'	=> array('192.168.20.172', 6201), //广播服务
		'config'	=> array('192.168.20.172', 9201), //更新配置服务
		'upgrade'	=> array('127.0.0.1', 19940), //升级控制服务
		'phpudp'	=> array('192.168.20.198', 6343), //PHP自己的UDP协议Server
		'dataserver'=> array('192.168.20.198', 6344), //PHP自己的UDP协议Server
		'monitorsrv'=> array('192.168.20.195', 9200), //接口监控server
		'cmc'		=> array('127.0.0.1', 6345), //云计算监控中心
		'push'		=> array('192.168.20.195', 1023), //推送中转Server
		'dispatch'	=> array('192.168.20.195',6901),//牌局分发器
		'logudp'	=> array('192.168.20.195',9001),//日志服
		'matchctl'	=> array('http://dfqp-cli.ifere.com/match/index.php', 80), //PHP自己的比赛管理Server
		'rpttserver'=> array('192.168.20.198', 6346),//信誉系统服务
	),
	//WEB请求入口地址
	'site_url' => 'http://mvsnspus01.ifere.com/ddfqp/',
	//各WEB服务器独立URL列表
	'site_workers_url' => array(
		'http://120.132.184.31/ddfqp/',
		'http://120.132.184.10/ddfqp/',
	),
	//防攻击代理web服
	'proxy_web_url'	  => 'http://mvsnspus01.ifere.com/svr_proxy/index.php',
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
		'url_bigfile' => 'http://bigfile-kw.static.17c.cn/bigfile-ws/dfqp/',
		'src_bigfile' => 'http://pcussplc01.ifere.com/dfqp/',
		'static_site' => 'http://uchead.static.17c.cn/dfqp/webphp/static_files_api.php', //客户端json文件请求时间戳接口
	),
	//验证码获取url地址
	'verifyurl'	=> 'http://passport.boyaa.com/authorcode.php',
	//会话有效期
	'session_life' => 900,
	//消息队列KEY
	'sysvmsg_key' => 8081,
	//运营组URL
	'opreation_url' => array(
		//地区对应活动地址
		1 => 'http://pcusspus01.ifere.com/?m=activities',
		2 => 'http://mvusspac01.ifere.com/index.php?m=activities&appid=9401',
		3 => 'http://mvusspac02.ifere.com/index.php?m=activities&appid=9402',
		4 => 'http://mvussplp02.ifere.com/index.php?m=activities&appid=9404',
		6 => 'http://mvusspac03.ifere.com/?m=activities&p=index&appid=9405',
		8 => 'http://pcusspac02.ifere.com/?m=activities&p=index', //乐山
		9 => 'http://ususcsac03.ifere.com/?m=activities&p=index&appid=9411', //济南
		10=> 'http://ususcsac02.ifere.com/?m=activities&p=index&appid=9410', //营口
		11=> 'http://ususcsac01.ifere.com/?m=activities&p=index&appid=9409', //昆明
		12=> 'http://mvspbt01.ifere.com/?m=activities&p=index&appid=9412', //南充
		13=> 'http://ususcsac04.ifere.com/?m=activities&p=index&appid=9413',//内江
		14=> 'http://ususcsac05.ifere.com/?m=activities&p=index&appid=9414',//泸州
		15=> 'http://ususspac05.ifere.com/?m=activities&p=index&appid=9417',//鞍山
		16=> 'http://ususspac04.ifere.com/?m=activities&p=index&appid=9416',//丹东
		17=> 'http://ususspac03.ifere.com/?m=activities&p=index&appid=9415',//盘锦
		18=> 'http://poker20002.ifere.com/?m=activities&p=index&appid=9419',//南昌
		19=> 'http://poker01001.ifere.com/?m=activities&p=index&appid=9418',//我是大牌
		20=> 'http://qujingpoker.ifere.com/?m=activities&p=index&appid=9422',//曲靖
		21=> 'http://zibopoker.ifere.com/?m=activities&p=index&appid=9420',//淄博
		22=> 'http://chaoyangpoker.ifere.com/?m=activities&p=index&appid=9424',//朝阳
		23=> 'http://liaoyangpoker.ifere.com/?m=activities&p=index&appid=9421',//辽阳
		24=> 'http://jinzhoupoker.ifere.com/?m=activities&p=index&appid=9428',//锦州
		25=> 'http://fuxinpoker.ifere.com/?m=activities&p=index&appid=9423',//阜新
		26=> 'http://huludaopoker.ifere.com/?m=activities&p=index&appid=9426',//葫芦岛
		27=> 'http://honghepoker.ifere.com/?m=activities&p=index&appid=9427',//红河
		28=> 'http://liaochengpoker.ifere.com/?m=activities&p=index&appid=9425',//聊城
		29=> 'http://benxipoker.ifere.com/?m=activities&p=index&appid=9430',//本溪
		30=> 'http://tielingpoker.ifere.com/?m=activities&p=index&appid=9433',//铁岭
		31=> 'http://shenyangpoker.ifere.com/?m=activities&p=index&appid=9432',//沈阳
		32=> 'http://fushunpoker.ifere.com/?m=activities&p=index&appid=9431',//抚顺
		33=> 'http://jiujiangpoker.ifere.com/?m=activities&p=index&appid=9429',//九江
		34=> 'http://dalipoker.ifere.com/?m=activities&p=index&appid=9434',//大理
		35=> 'http://yuxipoker.ifere.com/?m=activities&p=index&appid=9435',//玉溪
		37=> 'http://weifangpoker.ifere.com/?m=activities&p=index&appid=9436',//潍坊
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
		61=> 'http://puerpoker.ifere.com//?m=activities&p=index&appid=9455',//普洱
		60=> 'http://zunyipoker.ifere.com/?m=activities&p=index&appid=9456',//遵义
		62=> 'http://dezhoupoker.ifere.com/?m=activities&p=index&appid=9460',//德州
		64=> 'http://yantaipoker.ifere.com/?m=activities&p=index&appid=9459',//烟台
		67=> 'http://liupanshuipoker.ifere.com/?m=activities&p=index&appid=9462',//六盘水
	),
	//Im社交URL
	'im_url' => array(
		'url'=>array('http://139.224.36.105/ddfqp/index.php?action=externals.imdir'),//url地址
		'svr_ver'=>1458544065,//更新社交URL的时候需要修改此时间戳，根据客户端请求时带的时间戳判断是否返回数据
		'exchange'=> 'http://192.168.20.181:6253/msgproxy?',//新老版本转接服
	),
	//Im server分发端口
	'im_dir' => array(
		array('ip'=>'139.224.36.78', 'port'=>6210),
		array('ip'=>'139.224.36.78', 'port'=>6211),
		array('ip'=>'139.224.36.78', 'port'=>6212),
		array('ip'=>'139.224.36.78', 'port'=>6213),
		array('ip'=>'139.224.36.68', 'port'=>6010),
		array('ip'=>'139.224.36.68', 'port'=>6011),
		array('ip'=>'139.224.36.68', 'port'=>6012),
		array('ip'=>'139.224.36.68', 'port'=>6013),
		array('ip'=>'139.224.36.68', 'port'=>6014),
		array('ip'=>'139.224.36.68', 'port'=>6015),
		array('ip'=>'139.224.36.68', 'port'=>6016),
		array('ip'=>'139.224.36.68', 'port'=>6017),
	),
	//Im 加好友请求接口
	'im_friend' => array(
		'url'=> 'http://139.224.36.78:6253/msgproxy/add_friend',//加好友url
	),
	//活动上报地址
	'activity_center_server' => array('192.168.20.206', 1108),
	//停服时间配置
	//'shtutdown_time' => array('start' => 1485030940, 'end' => 1485036000),
/*	'share_url' => array(
		1 => 'http://yb.zhgymshwxfxyd.com/ddfqp/', //四川
		2 => 'http://yb.zhgymshwxfxyd.com/ddfqp/',//大连
		3 => 'http://yb.zhgymshwxfxyd.com/ddfqp/',//山东
		4 => 'http://yb.zhgymshwxfxyd.com/ddfqp/',//宜宾
		5 => 'http://yb.zhgymshwxfxyd.com/ddfqp/',//四国军棋
		6 => 'http://yb.zhgymshwxfxyd.com/ddfqp/',//自贡
		8 => 'http://yb.zhgymshwxfxyd.com/ddfqp/', //乐山
		9 => 'http://yb.zhgymshwxfxyd.com/ddfqp/', //济南
		10=> 'http://yb.zhgymshwxfxyd.com/ddfqp/', //营口
		11=> 'http://yb.zhgymshwxfxyd.com/ddfqp/', //昆明
		12=> 'http://yb.zhgymshwxfxyd.com/ddfqp/', //南充
		13=> 'http://yb.zhgymshwxfxyd.com/ddfqp/',//内江
		14=> 'http://yb.zhgymshwxfxyd.com/ddfqp/',//泸州
		15=> 'http://anshan.liaoninggames.com/',//鞍山
		16=> 'http://yb.zhgymshwxfxyd.com/ddfqp/',//丹东
		17=> 'http://yb.zhgymshwxfxyd.com/ddfqp/',//盘锦
		18=> 'http://yb.zhgymshwxfxyd.com/ddfqp/',//南昌
		19=> 'http://yb.zhgymshwxfxyd.com/ddfqp/',//我是大牌
	),*/
	//分享域名
	'share_url' => array(
		1 => 'http://www.jiangxigames.cn/ddfqp/', //四川1
		2 => 'http://dalian.liaoninggames.com/',//大连1
		3 => 'http://yb.zhgymshwxfxyd.com/ddfqp/',//山东
		4 => 'http://www.jiangxigames.cn/ddfqp/',//宜宾1
		5 => 'http://yb.zhgymshwxfxyd.com/ddfqp/',//四国军棋
		6 => 'http://www.jiangxigames.cn/ddfqp/',//自贡1
		8 => 'http://www.sichuanyouxi.com/', //乐山
		9 => 'http://weifang.shandonggames.com/', //济南1
		10=> 'http://yingkou.liaoninggames.com/', //营口1
		11=> 'http://qujing.yunnangames.com/', //昆明1
		12=> 'http://nanchong.sichuanyouxi.com/', //南充1
		13=> 'http://neijiang.sichuanyouxi.com/',//内江1
		14=> 'http://yb.zhgymshwxfxyd.com/ddfqp/',//泸州
		15=> 'http://anshan.liaoninggames.com/',//鞍山1
		16=> 'http://dandong.liaoninggames.com/',//丹东1
		17=> 'http://panjin.liaoninggames.com/',//盘锦1
		18=> 'http://nanchang.jiangxigames.com/',//南昌1
		19=> 'http://yb.zhgymshwxfxyd.com/ddfqp/',//我是大牌
		20=> 'http://qujing.yunnangames.com/',//曲靖1
		21=> 'http://weifang.shandonggames.com/',//淄博1
		22=> 'http://chaoyang.liaoninggames.com/',//朝阳1
		23=> 'http://liaoyang.liaoninggames.com/',//辽阳1
		24=> 'http://jinzhou.liaoninggames.com/',//锦州1
		25=> 'http://fuxin.liaoninggames.com/',//阜新1
		26=> 'http://huludao.liaoninggames.com/',//葫芦岛1
		27=> 'http://honghe.yunnangames.com/',//红河1
		28=> 'http://liaocheng.shandonggames.com/',//聊城
		29=> 'http://benxi.liaoninggames.com/',//本溪1
		32=> 'http://fushun.liaoninggames.com/',//抚顺1
		30=> 'http://tieling.liaoninggames.com/',//铁岭1
		31=> 'http://shenyang.liaoninggames.com/',//沈阳1
		33=> 'http://jiujiang.jiangxigames.com/',//九江1
		35=> 'http://yuxi.yunnangames.com/',//玉溪
		37=> 'http://weifang.shandonggames.com/',//潍坊1
		38=> 'http://lijiang.yunnangames.com/',//丽江
		49=> 'http://dehong.yunnangames.com/',//德宏
		54=> 'http://guiyang.yunnangames.com/',//贵阳
	),
	//公共部门的升级管理平台接口地址
	'update_center_api' => 'http://pkgserver.ifere.com/Api/LocalBoard/onlineUpdateInfo',
);
