<?php
ob_start(); 
mysql_connect("localhost", "__username__", "__password__");
mysql_query("set names 'gb2312'");
if(!@mysql_select_db("__database__"))
{
	header("location:install.php");
	exit;
}
if(isset($_COOKIE[username]))
$username = $_COOKIE[username];
else
$username = GetIP();
include_once "../common.php";
$username=$_SGLOBAL['supe_username'];

header('Content-Type:text/html;charset=GB2312');
?>