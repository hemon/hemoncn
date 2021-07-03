<?php
require_once './common.php';
$space = $_SGLOBAL['supe_uid']?getspace($_SGLOBAL['supe_uid']):array();
include template('ds');
?>