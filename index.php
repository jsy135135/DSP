<?php
/**
 * 单入口系统
 */
//error_reporting(E_All);
@set_time_limit(240);
@ini_set("memory_limit",'-1');
define('APP_DEBUG', true);				//是否开启调试模式
require("./Core/Conf/define.php");
require(THINK_PATH."/ThinkPHP.php");	//加载框架入口文件