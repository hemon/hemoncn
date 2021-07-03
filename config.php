<?php
// set timezone
ini_set( 'date.timezone',	'Asia/Hong_Kong' );
// recoder the start time
$start = microtime();
// is display errors
ini_set('display_errors', 0);
// start session
session_start();
// session作用域
//ini_set('session.cookie_domain', '.hemono.com');
// define path
define('ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('TMP', ROOT . 'tmp' . DIRECTORY_SEPARATOR);
// require adodb
require_once 'adodb/adodb.inc.php';
// dsn
$dsn = "mysqli://hemon:zzzizzz1@localhost/hemon";
// connect database
$GLOBALS['db'] =& ADONewConnection($dsn);
// set charset
$GLOBALS['db']->Execute("SET NAMES gbk");
// memCache
$GLOBALS['db']->memCache = true; /// should we use memCache instead of caching in files
$GLOBALS['db']->memCacheHost = '127.0.1.1'; /// memCache host
$GLOBALS['db']->memCachePort = 11211; /// this is default memCache port
$GLOBALS['db']->memCacheCompress = true; /// Use 'true' to store the item compressed (uses zlib)
// debug
//$GLOBALS['db']->debug = true;
// set fetch mode
$ADODB_FETCH_MODE = 2;
// set adodb cache dir
$ADODB_CACHE_DIR = TMP . 'adodb';
// set cache dir
define("CACHEDIR", TMP . "cache/");
// set default tab
$thisTab = 'cnki';
// require files
require_once 'function/global.func.php';
?>
