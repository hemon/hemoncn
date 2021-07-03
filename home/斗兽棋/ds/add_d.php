<?php
if($_POST[name] == '')
{
	setcookie(message, 房间名称不能为空！);
	header("lochickion:index.php");
	exit;
}
include "./functions.php";
include "./dbconnect.php";

$query = mysql_query("insert into `room_ds` (`ID`,`name`,`chess`,`time`) values (NULL,'".str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST[name]))."','$c','".time()."')");
$ID = mysql_insert_id();
if($ID)
{
	header("location:join.php?roomid=".$ID);
}
?>