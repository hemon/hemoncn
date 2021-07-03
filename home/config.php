<?php
/*
	[Ucenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: config.php 6798 2008-03-27 01:04:03Z liguode $
*/

$_SC = array();

//Ucenter Home 配置参数
$_SC['dbhost']  		= 'localhost'; //服务器地址
$_SC['dbuser']  		= 'root'; //用户
$_SC['dbpw'] 	 		= 'zzzizzz1'; //密码
$_SC['dbcharset'] 		= 'utf8'; //字符集
$_SC['pconnect'] 		= 0; //是否持续连接
$_SC['dbname']  		= 'hemon'; //数据库
$_SC['tablepre'] 		= 'uch_'; //表名前缀
$_SC['charset'] 		= 'utf-8'; //页面字符集
$_SC['headercharset'] 	= 1; //强制使用字符集
$_SC['gzipcompress'] 	= 0; //启用gzip
$_SC['founder'] 		= '1'; //创始人 UID, 可以支持多个创始人，之间使用 “,” 分隔
$_SC['template']		= 'default'; //选择模板目录
$_SC['cookiepre'] 		= 'uchome_'; //COOKIE前缀
$_SC['cookiedomain'] 	= ''; //COOKIE作用域
$_SC['cookiepath'] 		= '/'; //COOKIE作用路径
$_SC['attachdir']		= './attachment/'; //附件本地保存位置(服务器路径, 属性 777, 必须为 web 可访问到的目录, 相对目录务必以 "./" 开头, 末尾加 "/")
$_SC['attachurl']		= 'attachment/'; //附件本地URL地址(可为当前 URL 下的相对地址或 http:// 开头的绝对地址, 末尾加 "/")
$_SC['tplrefresh']		= 0; //判断模板是否更新的效率等级，数值越大，效率越高; 设置为0则永久不判断
$_SC['bgcredit']        = 50; //型男素女购买投票资格消耗积分
//UCenter 配置参数
define('UC_CONNECT', 'mysql'); // 连接 UCenter 的方式: mysql/NULL, 默认为空时为 fscoketopen(), mysql 是直接连接的数据库, 为了效率, 建议采用 mysql
//数据库相关 (mysql 连接时, 并且没有设置 UC_DBLINK 时, 需要配置以下变量)
define('UC_DBHOST', 'localhost'); // UCenter 数据库主机
define('UC_DBUSER', 'root'); // UCenter 数据库用户名
define('UC_DBPW', 'zzzizzz1'); // UCenter 数据库密码
define('UC_DBNAME', 'hemon'); // UCenter 数据库名称
define('UC_DBCHARSET', 'utf8'); // UCenter 数据库字符集
define('UC_DBTABLEPRE', '`hemon`.uc_'); // UCenter 数据库表前缀
define('UC_DBCONNECT', '0'); // UCenter 数据库持久连接 0=关闭, 1=打开
//通信相关
define('UC_KEY', 'qd5cHfieh8LfW2H0q0JeX3M0m1wdG3D1cbd3Z7xcB223o0j3U61cv4qb6fl8Y3w7'); // 与 UCenter 的通信密钥, 要与 UCenter 保持一致
define('UC_API', 'http://www.hemon.cn/user/'); // UCenter 的 URL 地址, 在调用头像时依赖此常量
define('UC_CHARSET', 'utf-8'); // UCenter 的字符集
define('UC_IP', '127.0.0.1'); // UCenter 的 IP, 当 UC_CONNECT 为非 mysql 方式时, 并且当前应用服务器解析域名有问题时, 请设置此值
define('UC_APPID', '1'); // 当前应用的 ID
//输出缓冲
include_once("./sn.php");
ob_start('ob_sn_name');
?>
