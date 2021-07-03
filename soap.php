<?php
//require files
require_once 'config.php';
require_once 'mod/score.func.php';

$server = new SoapServer(null, array('uri' => "http://test-uri/"));
$server->addFunction(array('getClassmates','getScore', 'getClasscode'));
$server->handle();
?>
