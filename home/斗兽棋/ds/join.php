<?php
include "./functions.php";
include "./dbconnect.php";

$query = mysql_query("select * from `room_ds` where `ID` = '".$_GET[roomid]."'");
$guest = mysql_result($query, 0, guest);
$host = mysql_result($query, 0, host);

if(($guest == '' && $host == '') || $host == '')
{
	mysql_query("update `room_ds` set `host` = '$username',`time_host` = '".time()."',`time` = '".time()."' where `ID` = '".$_GET[roomid]."'");
}elseif($host != '' && $guest == ''){
	mysql_query("update `room_ds` set `guest` = '$username',`time_guest` = '".time()."',`time` = '".time()."' where `ID` = '".$_GET[roomid]."'");
}else{
header("location:index.php");
exit;
}
header("location:room.php?id=".$_GET[roomid]);
exit;
?>