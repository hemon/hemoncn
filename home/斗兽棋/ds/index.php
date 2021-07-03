<?php
include "./functions.php";
include "./dbconnect.php";
include_once "../common.php";
mysql_query("delete from `room_ds` where '".time()."' - `time` > 30");
if(isset($_COOKIE[message]))
{
echo "<script>alert('".$_COOKIE[message]."');</script>";
setcookie(message, null);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>斗兽棋online</title>
<style>
body{
font-size:15px;
}
A:visited {
	COLOR: #000000; TEXT-DECORATION: none
}
A:hover {
	COLOR: #000000; TEXT-DECORATION: underline
}
A:active {
	COLOR: #000000; TEXT-DECORATION: none
}
A:link {
	COLOR: #000000; TEXT-DECORATION: none
}
</style>
</head>

<body>
<table>
<tr><td colspan="2"><img src="logo.gif" /></td><td>
	<form name=form method=post action=change_name.php style="margin:0px">
	<table>
		<tr>
			<td>
			你的名字：
			</td>
			<td><input type=text name=name size=12 value=<?php echo $username;?>></td>
			<td><input type=submit name=submit value=更换></td>
		</tr>
	</table>
	</form>
	<form name="form" method="post" action="add_d.php" style="margin:0px">
	<table><tr><td>新建房间：</td><td><input type="text" name="name" size="25" value="<?php echo "".time()."号";?>" /> <input type=submit value=新建></td></tr></table>
	</form>
</td></tr>
<tr><td></td><td colspan="2">
	<table>
	<?php
	$query = mysql_query("select * from `room_ds`");
	$num = mysql_num_rows($query);
	for($i = 0;$i < $num;$i ++)
	{
	$ID = mysql_result($query, $i, ID);
	$name = mysql_result($query, $i, name);
	$host = mysql_result($query, $i, host);
	$guest = mysql_result($query, $i, guest);
	$room_sum = 0;
	if($host)
	$room_sum ++;
	if($guest)
	$room_sum ++;
	?>
		<tr><td valign="middle"><img src="dot.gif" /> <?php echo $name;?>（<?php echo $room_sum;?>人/2）[<a href="room.php?id=<?php echo $ID;?>">进入房间</a>]</td></tr>
	<?php
	}
	?>
	</table>
</td></tr>
</table>

</body>
</html>
<span style="display:none">
<script type="text/javascript" src="http://js.tongji.yahoo.com.cn/1/100/33/ystat.js"></script><noscript><a href="http://js.tongji.yahoo.com.cn"><img src=http://js.tongji.yahoo.com.cn/1/100/33/ystat.gif></a></noscript></span>
