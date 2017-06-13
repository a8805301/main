<?php
//项目环境 dev:开发 test:测试 product:生产
define('PROJECT_ENV', 'dev');
//项目主目录
define('PROJECT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
//项目语言
define('PROJECT_LANG', 'zh_CN');
//项目区服名(不同区服用户群不同)
define('PROJECT_AREA', 'ddfqp');
//项目PHP子服ID(用于标识相同区服下的不同物理服务器) 0:主服 >0:分服
define('PROJECT_WORKER', 0);

require(PROJECT_PATH . 'core/app.php');
App::run(PROJECT_PATH, PROJECT_ENV, PROJECT_LANG, PROJECT_AREA, PROJECT_WORKER);