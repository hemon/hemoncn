<?php
ini_set( 'display_errors',	'1' );
ini_set( 'error_reporting',	 E_ALL ^ E_NOTICE );
ini_set( 'date.timezone',	'Asia/Hong_Kong' );

try {
    $GLOBALS['db'] = new PDO('mysql:host=localhost;dbname=hemon', 'hemon', 'zzzizzz1');
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
$GLOBALS['db']->exec('SET NAMES GBK');

$sth = $GLOBALS['db']->prepare("SELECT id,url FROM bot_url WHERE id <= 4945");
$sth->execute();

$result = $sth->fetchAll();
foreach($result as $row){
     list($base, $course) = explode('Course=', $row['url']);
     $course = urlencode($course);
     $url = $base . "Course=" . $course;
     $GLOBALS['db']->exec("INSERT INTO bot_url_copy(url) VALUES ('$url')");
}
