<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: index.php 12117 2008-01-11 06:25:08Z heyond $
*/

error_reporting(0);

set_magic_quotes_runtime(0);

//note 开始时间
$mtime = explode(' ', microtime());
$starttime = $mtime[1] + $mtime[0];

define('UC_VERSION', '1.0.0');
define('UC_RELEASE', '20080429');

define('IN_UC', TRUE);
define('UC_ROOT', dirname(__FILE__).'/');

define('UC_API', strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/'))).'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
define('UC_DATADIR', UC_ROOT.'./data/');
define('UC_DATAURL', UC_API.'/data');
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

//note 清除变量
unset($GLOBALS, $_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);

$_GET     = daddslashes($_GET, 1, TRUE);
$_POST    = daddslashes($_POST, 1, TRUE);
$_COOKIE  = daddslashes($_COOKIE, 1, TRUE);
$_SERVER  = daddslashes($_SERVER);
$_FILES   = daddslashes($_FILES);
$_REQUEST = daddslashes($_REQUEST, 1, TRUE);

if(!@include UC_DATADIR.'config.inc.php') {
	exit('The file <b>data/config.inc.php</b> does not exist, perhaps because of UCenter has not been installed, <a href="install/index.php"><b>Please click here to install it.</b></a>.');
}

require UC_ROOT.'model/base.php';

$m = getgpc('m');
$a = getgpc('a');
if(empty($m) && empty($a)) {
	header('Location: admin.php');
	exit;
}

if(in_array($m, array('app', 'frame', 'user', 'pm', 'pm_client', 'tag', 'feed', 'friend', 'domain', 'credit'))) {
	include UC_ROOT."control/$m.php";
	$control = new control();
	//note 不允许访问私有方法
	$method = 'on'.$a;
	if(method_exists($control, $method) && $method{0} != '_') {
		$control->$method();
	} elseif(method_exists($control, '_call')) {
		$control->_call('on'.$a, '');
	} else {
		exit('Action not found!');
	}
} else {
	exit('Module not found!');
}

//note 结束时间
$mtime = explode(' ', microtime());
$endtime = $mtime[1] + $mtime[0];
//note echo '<script>document.getElementById(\'debug_time\').innerHTML = \''.number_format($endtime - $starttime, 5).'\'</script>'."\n";

function daddslashes($string, $force = 0, $strip = FALSE) {
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force, $strip);
			}
		} else {
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}

function getgpc($k, $var='R') {
	switch($var) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		case 'R': $var = &$_REQUEST; break;
	}
	return isset($var[$k]) ? $var[$k] : NULL;
}

?>