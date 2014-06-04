<?php
header("Content-type: text/html; charset=utf-8");
if (get_magic_quotes_gpc()) {
	function stripslashes_deep($value){
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value); 
		return $value; 
	}
	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE); 
}
define('APP_DEBUG',1);
define('APP_NAME', 'weixin');
define('CONF_PATH','./conf/');
define('RUNTIME_PATH','./runtime/');
define('TMPL_PATH','./tpl/');
define('HTML_PATH','./data/html/');
define('APP_PATH','./app/');
define('CORE','./app/_Core');
// define('APP_DEBUG',TRUE); // 开启调试模式
require(CORE.'/weixin.php'); // thinkphp 入口文件
require("conf/config.php");
?>