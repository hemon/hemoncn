<?php

error_reporting(2048);

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

$start = microtime_float();

$dsn  = 'mysql:dbname=hemon;host=localhost';
$user = 'root';
$pass = 'zzzizzz1';

$db = new PDO($dsn, $user, $pass);

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$db->query("SET NAMES 'utf8'");
