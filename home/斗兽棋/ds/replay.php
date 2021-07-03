<?php
include "./functions.php";
include "./dbconnect.php";

mysql_query("update `room_ds` set `chess` = '$c',`flag` = 'host' where `ID` = '".$_GET[roomid]."'");
header('location:room.php?id='.$_GET[roomid]);
?>