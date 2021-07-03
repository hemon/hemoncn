<?php
ini_set( 'display_errors',	'1' );
ini_set( 'error_reporting',	 E_ALL ^ E_NOTICE );
ini_set( 'date.timezone',	'Asia/Hong_Kong' );

function __autoload($class_name) {
	$class_file = str_replace('_', '/', $class_name) . '.php';
	if( is_file($class_file ) ){
		require_once $class_file;
	}
}

function term(){
    $today = getdate();
    $year  = $today['year'];
    $month = $today['mon'];
    $term  = 1;
    // 学年下学期（3-10月）
    if ( 3 < $month && $month < 10 ) {
        $term = 2;
    }
    // 9月开始新学年，之前仍为上一学年
    if ( $month < 10 ) {
        $year -= 1;
    }
    return "$year.$term";
}

function worker($class_name){
	return new $class_name();
}

define("TERM", term());

try {
    $GLOBALS['db'] = new PDO('mysql:host=localhost;dbname=hemon', 'root', 'zzzizzz1');
    $GLOBALS['db']->exec('SET NAMES GBK');
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

$worker = worker($argv[1]);

if( 4 == $argc ){
    $worker->limit = $argv[2];
    $worker->priority = $argv[3];
}

$worker->loop();
