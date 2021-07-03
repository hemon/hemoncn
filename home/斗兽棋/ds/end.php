<?php
include "./functions.php";
include "./dbconnect.php";

mysql_query("delete from `room_ds` where `ID` = '".$_GET[roomid]."'");
header('location:index.php');
?>