<?php
include "./functions.php";
include "./dbconnect.php";
if($_POST[name] == '')
{
	setcookie(message, ���ֲ���Ϊ�գ�);
	header("location:index.php");
	exit;
}
$query = mysql_query("select count(*) as 'num' from `room_ds` where `host` = '".$_POST[name]."' or `guest` = '".$_POST[name]."' limit 1");
if(@mysql_result($query, 0, num))
{
	setcookie(message, ��׬����������Ѿ�������ʹ���ˣ�);
	header("location:index.php");
	exit;
}
setcookie(username, str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST[name])));
header("location:index.php");
?>