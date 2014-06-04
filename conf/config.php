<?php

/**
 *项目公共配置
 *@package PiGCms
 *@author PiGCms
 **/

return array(

	'LOAD_EXT_CONFIG' 		=> 'db,info,email,safe,upfile,cache,route,app,alipay',		

	'APP_AUTOLOAD_PATH'     =>'@.ORG',

	'OUTPUT_ENCODE'         =>  true, 			//页面压缩输出

	'PAGE_NUM'				=> 15,

	/*Cookie配置*/

	'COOKIE_PATH'           => '/',     		// Cookie路径

    'COOKIE_PREFIX'         => '',      		// Cookie前缀 避免冲突

	/*定义模版标签*/

	'TMPL_L_DELIM'   		=>'{weixin:',			//模板引擎普通标签开始标记

	'TMPL_R_DELIM'			=>'}',				//模板引擎普通标签结束标记

	'SHOW_PAGE_TRACE'		=>false,

    'LOG_RECORD'            => true,   // 默认不记录日志
    'LOG_TYPE'              => 3, // 日志记录类型 0 系统 1 邮件 3 文件 4 SAPI 默认为文件方式
    'LOG_DEST'              => '/var/log/weiapp/weiapp_info.log', // 日志记录目标
    'LOG_EXTRA'             => '', // 日志记录额外信息
    'LOG_LEVEL'             => 'EMERG,ALERT,CRIT,ERR,INFO',// 允许记录的日志级别
    'LOG_FILE_SIZE'         => 2097152,	// 日志文件大小限制
    'LOG_EXCEPTION_RECORD'  => true,    // 是否记录异常信息日志

);
require("shouquan.php");
?>