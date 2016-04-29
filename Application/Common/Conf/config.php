<?php
return array(
	'URL_CASE_INSENSITIVE' =>true,
	'URL_MODEL'=> 2,

/*********** Pinnacle  Mysql ****************/
	'DB_TYPE' => 'mysql', // 数据库类型
	'DB_HOST' => '10.0.109.33', // 服务器地址
	'DB_NAME' => 'demo_event_db', // 数据库名
	'DB_USER' => 'clear', // 用户名
	'DB_PWD' => 'clear', // 密码
	'DB_PORT' => 3306, // 端口
	'DB_PREFIX' => '', // 数据库表前缀
	'DB_CHARSET'=> 'utf8', // 字符集

/*********** Pinnacle  Redis ****************/
	//'REDIS_HOST' => '10.0.109.33', // redis server IP
	//'REDIS_PORT' => '8181', //  Port

/*********** localhost  Redis ****************/
	'REDIS_HOST' => '127.0.0.1', // redis server IP
	'REDIS_PORT' => '6379', //  Port

/*********** localhost ****************/
	// 'DB_TYPE' => 'mysql', // 数据库类型
	// 'DB_HOST' => 'localhost', // 服务器地址
	// 'DB_NAME' => 'demo_event_db', // 数据库名
	// 'DB_USER' => 'root', // 用户名
	// 'DB_PWD' => '', // 密码
	// 'DB_PORT' => 3306, // 端口
	// 'DB_PREFIX' => '', // 数据库表前缀
	// 'DB_CHARSET'=> 'utf8', // 字符集
	
	'DB_DEBUG' => TRUE, // 数据库调试模式 开启后可以记录SQL日志


);