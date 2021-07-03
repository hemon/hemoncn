<?php
include "./functions.php";
include "./dbconnect.php";

$query = mysql_query("select * from `room_ds` where `ID` = '".$_GET[roomid]."'");
$chess = mysql_result($query, 0, chess);
$chess = explode(",", $chess);
for($c ='', $i = 0;$i < 63;$i ++)
{
	$new_chess = $chess[$i];
	
	if($_GET[from] == $i)
	$new_chess = 'blank';
	if($_GET[to] == $i)
	$new_chess = $chess[$_GET[from]];
	
	$c .= $new_chess.',';
}
mysql_query("update `room_ds` set `chess` = '$c',`flag` = '".($_GET[site]==left?'host':'guest')."' where `ID` = '".$_GET[roomid]."'");
?>