<?php
include "./functions.php";
include "./dbconnect.php";

$query = mysql_query("select * from `room_ds` where `ID` = '".$_GET[roomid]."'");
if(!mysql_num_rows($query))
die("end");
$chess = mysql_result($query, 0, chess);
$time_guest = mysql_result($query, 0, time_guest);
$time_host = mysql_result($query, 0, time_host);
$flag = mysql_result($query, 0, flag);
$guest = mysql_result($query, 0, guest);
$host = mysql_result($query, 0, host);
$message_guest = mysql_result($query, 0, message_guest);
$message_host = mysql_result($query, 0, message_host);

mysql_query("update `room_ds` set `time` = '".time()."', `time_".($_GET[site] == left?'guest':'host')."` = '".time()."' where `ID` = '".$_GET[roomid]."'");

if(time() - $time_guest > 30)
mysql_query("update `room_ds` set `guest` = '' where `ID` = '".$_GET[roomid]."'");
if(time() - $time_host > 30)
mysql_query("update `room_ds` set `host` = '' where `ID` = '".$_GET[roomid]."'");


echo $chess."|".$flag."|".$guest."|".$host."|".$message_guest."|".$message_host;
?>