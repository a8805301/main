<?php
/**
 * @author jsonyu
 * @desc 定义常量
 * @date 2013-12-26
 */
define('SUCC',1);//全局标识更新数据库类型、验证类型正确
define('FAIL',0);//全局标识更新数据库类型、验证类型错误
define('CACHE_PRE','dfqp_');
define('LOG_MALL',10);
define('LOG_DIAMOND',11);
define('LOG_PROPS',12);
define('LOG_SILVER',13);
define('LOG_CRYSTAL',14);
define('LOG_GATHER',15);
define('LOG_LOGIN',16);
define('LOG_SIMULATOR',17);//模拟器登录
define('SILENCE_SIZE', 100);//静默更新大小
define('BYACCOUNT_EXPIRES',2592000);//通行证TOKEN过期时间
define('RECHARGE_MC_KEY', 'RECHARGE' );//用户缓存信息key
define('RECHARGE_MC_EXPIRES', 1728000 );//用户充值信息缓存20天
define('BACK_TRACE_FILE', 'debugbacktrace'); //可捕捉的异常和错误信息配置文件名
define('MAX_BACK_TRACE_LEN', 10); //错误日志调用回溯最大同时回溯数目
define('BACK_TRACE_CONDITIONS', 'backtrace_conditions'); //回溯条件保存在$_DEBUG中的变量名称
define('BAG_VOL',48);#背包容量
define('APPLE_PAY_MODE',99);//苹果支付渠道标识
define('UNION_APPID',1);//统一大厅APPID-特殊化处理
define('IOS_PAY_LIMIT_CACHE_KEY',"apple-pay-limit");#IOS支付原生支付限制
//define('DBREPORT',1);//DB日志临时常量