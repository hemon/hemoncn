<?php
include "./functions.php";
include "./dbconnect.php";

$query = mysql_query("select * from `room_ds` where `ID` = '".$_GET[id]."'");
$num = mysql_num_rows($query);
if(!$num)
{
	header("location:index.php");
	exit;
}
$name = mysql_result($query, 0, name);
$guest = mysql_result($query, 0, guest);
$host = mysql_result($query, 0, host);
$chess = mysql_result($query, 0, chess);
$flag = mysql_result($query, 0, flag);

if($guest != '' && $host != '' && $username != $guest && $username != $host)
{
header("location:index.php");
exit;
}

if($guest == '' && $host == '' || $username != $host && $guest == '' || $username != $guest && $host == '')
{
	header("location:join.php?roomid=".$_GET[id]);
	exit;
}

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
.chess_normal{
	padding:2px 2px 2px 2px;
	border:2px solid #FFFFFF;
	color:#000;
	FONT-SIZE: 13px
}
.chess_click{
	border:2px solid #000000;

	color:#000;
	FONT-SIZE: 13px
}

.chess_open{
	padding:2px 2px 2px 2px;
	border:2px solid #CC0000;
	color:#000;
	FONT-SIZE: 13px
}
.message_box{
	padding:2px 2px 2px 2px;
	border:1px solid #000000;
	background:#FFFFCC no-repeat;	
	color:#000;
	FONT-SIZE: 13px
}
.box_chat{
	border:1px solid #000000;
	background:#FFFFFF no-repeat;	
	color:#000;
	FONT-SIZE: 13px
}
</style>
<title><?php echo $name;?> - 斗兽棋online</title>
<script src="dd_code.js?autoplay=false"></script>
<script>
var message_guest = '';
var message_host = '';
var left_sum = 0;
var right_sum = 0;
var type = "";
var flag = '<?php echo $flag;?>';
var site = '<?php echo $username == $host?'right':'left';?>';
var chess = '<?php echo $chess;?>';
var chess_click = '';

function rank(animal,num){
if(animal.match('right') && (num - 1 == 18 || num - 1 == 28 || num - 1 == 36))
return 0;
if(animal.match('left') && (num - 1 == 26 || num - 1 == 34 || num - 1 == 44))
return 0;
	if(animal.match('dino'))
	return 8;
	if(animal.match('tiger'))
	return 7;
	if(animal.match('cow'))
	return 6;
	if(animal.match('horse'))
	return 5;
	if(animal.match('dog'))
	return 4;
	if(animal.match('chick'))
	return 3;
	if(animal.match('snake'))
	return 2;
	if(animal.match('rat'))
	return 1;
	else
	return 0;
}
function is_water(num){
	if(num - 1 == 12 || num - 1 == 13 || num - 1 == 14 || num - 1 == 21 || num - 1 == 22 || num - 1 == 23 || num - 1 == 39 || num - 1 == 40 || num - 1 == 41 || num - 1 == 48 || num - 1 == 49 || num - 1 == 50)
	return 1;
	else return 0;
}
function getchess(num){
//num是被点的chess_ID号，应该是chess_split下标加1的
if(document.getElementById('result').innerHTML != '')
{
alert('游戏已经结束！');
send_request('getinfo.php?roomid=<?php echo $_GET[id];?>&site='+site+'&random='+Math.random());
}else{
if(document.getElementById('guest').innerHTML == '' || document.getElementById('host').innerHTML == '')
{
alert('你还没有对手，复制网址给好友即可跟好友一起游戏！');
}else{
if(chess_click == '')
{
	for(var i = 0;i < 63;i ++)
	{
		document.getElementById("chess_"+(i+1)).className = '';
	}
}
chess_split = chess.split(",");
	if((site == 'left' && flag == 'host') || (site == 'right' && flag == 'guest')){
	//看是不是自己执棋
	}else{
		if(chess_click == '' && !chess_split[num - 1].match(site)){
		//看是不是自己的棋
		}else{
		
			if(chess_click != '' && chess_split[num - 1].match(site) && chess_split[chess_click - 1].match(site))
			{
			//看点的是不是自己的棋
				for(var i = 0;i < 63;i ++)
				document.getElementById('chess_'+(i+1)).className = '';
				document.getElementById('chess_'+num).className = 'chess_click';
				chess_click = num;
			}else{
				if(chess_click != '' && !chess_split[chess_click - 1].match('rat') && (is_water(num)))
				{
				//看这棋子会不会游水
				}else{
					if(chess_click != '' && !(chess_click - num == -9 || chess_click - num == 9 || chess_click - num == 1 || chess_click - num == -1) && !jump_river(chess_click, num)){
					//走一步或者跳一步
					}else{
						if(chess_click != '' && (rank(chess_split[num - 1],num) > rank(chess_split[chess_click - 1],chess_click) || rank(chess_split[num - 1],num) == 1 && rank(chess_split[chess_click - 1],chess_click) == 8) && !(rank(chess_split[num - 1],num) == 8 && rank(chess_split[chess_click - 1],chess_click) == 1))
						{
						//大吃小，老鼠吃恐龙
						}else{
							if((is_water(chess_click)) && chess_split[num - 1] != 'blank')
							{
							//在水里的动物不能吃陆地上的动物
							}else{
								document.getElementById('chess_'+num).className = 'chess_click';
								if(chess_click)
								{
									send_request('do.php?roomid=<?php echo $_GET[id];?>&site='+site+'&from='+(chess_click - 1)+'&to='+(num - 1)+'&random='+Math.random());
								if(site == 'left')
								flag = 'host';
								else
								flag = 'guest';
								}
								chess_click = num;
							}
						}
					}
				}
			}
		}
	}
}}
}
function jump_river(chess_click, num){
	var chess_split = chess.split(",");
	if(rank(chess_split[chess_click - 1],num) == 7 || rank(chess_split[chess_click - 1],num) == 6)
	{
		if(chess_click - 1 == 3 || chess_click - 1 == 4 || chess_click - 1 == 5 || chess_click - 1 == 30 || chess_click - 1 == 31 || chess_click - 1 == 32 || chess_click - 1 == 57 || chess_click - 1 == 58 || chess_click - 1 == 59){
			if(chess_click - num == 27 || num - chess_click == 27)
			{
				if(chess_click > num)
				{
					if(chess_split[chess_click - 1 - 9] != 'blank' || chess_split[chess_click - 1 - 9 * 2] != 'blank')
					return 0
				}
				if(num > chess_click)
				{
					if(chess_split[chess_click - 1 + 9] != 'blank' || chess_split[chess_click - 1 + 9 * 2] != 'blank')
					return 0
				}
				return 1;
			}
		}
		if(chess_click - 1 == 11 || chess_click - 1 == 15 || chess_click - 1 == 20 || chess_click - 1 == 24 || chess_click - 1 == 38 || chess_click - 1 == 42 || chess_click - 1 == 47 || chess_click - 1 == 51){
			if(chess_click - num == 4 || num - chess_click == 4){
				if(chess_click > num)
				{
					for(var i = num - 1;i < chess_click - 1;i++)
					{
						if(chess_split[i] != 'blank')
						return 0;
					}
				}else{
					for(var i = chess_click - 1 + 1;i <= num - 1;i++)
					{
						if(chess_split[i] != 'blank')
						return 0;
					}				
				}
				return 1;
			}
		}
		return 0;
	}
	else
	return 0;
}
function show_chess(chess, num){
	num ++;
	return "<a href='javascript:getchess("+num+");'><img width=64 height=64 src=pictures/"+chess+"."+(chess == 'blank'?'gif':'png')+" border=0></a>";
}

function form_chess(){
if(flag == 'guest')
{
if(site == 'left')
document.getElementById('flag_s').innerHTML = '（你执棋）';
else
document.getElementById('flag_s').innerHTML = '（对方执棋）';
document.getElementById('guest_point').innerHTML = '&radic;'
document.getElementById('host_point').innerHTML = ''
document.getElementById('left_point').innerHTML = '&larr;'
document.getElementById('right_point').innerHTML = ''
}else{
if(site == 'right')
document.getElementById('flag_s').innerHTML = '（你执棋）';
else
document.getElementById('flag_s').innerHTML = '（对方执棋）';
document.getElementById('guest_point').innerHTML = ''
document.getElementById('host_point').innerHTML = '&radic;'
document.getElementById('left_point').innerHTML = ''
document.getElementById('right_point').innerHTML = '&larr;'
}
var chess_split = chess.split(",");
var str = '';
chess_click = '';
left_sum = 0;right_sum = 0;
str += '<table border=1 style="border-collapse: collapse" bordercolor="#111111">';
for(var i = 0;i < 7;i ++)
{
	str += "<tr>";
		for(var j = 0;j < 9;j ++)
		{
			if(((i == 1 || i == 2) && (j == 3 || j == 4 || j == 5)) || ((i == 4 || i == 5) && (j == 3 || j == 4 || j == 5)))
			var bgcolor = '#CCCCFF';
			else if((i == 2 && j == 0) || (i == 3 && j == 1) || (i == 4 && j == 0) || (i == 2 && j == 8) || (i == 3 && j == 7) || (i == 4 && j == 8))
			var bgcolor = '#CCCC66';
			else
			var bgcolor = 'white';
			str += "<td bgcolor="+bgcolor+"><div id='chess_"+(i * 9 + j + 1)+"'>"+show_chess(chess_split[i * 9 + j], i * 9 + j)+"</div></td>";
			if(chess_split[i * 9 + j].match('left'))
			left_sum ++;
			if(chess_split[i * 9 + j].match('right'))
			right_sum ++;
		}
	str += "</tr>";
}
str += '</table>';
return str;
}

function send_request(url) {
http_request = false;

if (window.XMLHttpRequest) { 
http_request = new XMLHttpRequest();
if (http_request.overrideMimeType) {
http_request.overrideMimeType('text/xml');
}
} else if (window.ActiveXObject) { 
try {
http_request = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
try {
http_request = new ActiveXObject("Microsoft.XMLHTTP");
} catch (e) {}
}
}
if (!http_request) {
alert('不能创建 XMLHttpRequest 对象!');
return false;
}

http_request.onreadystatechange = processRequest;
http_request.open('GET', url, true);
http_request.send(null);

}


//处理返回信息
function processRequest() {
if (http_request.readyState == 1) {
//alert('正在连接');
//document.getELementById('network_status').innerHTML = '正在连接..';
}
if (http_request.readyState == 4) {
if (http_request.status == 200) {
if(http_request.responseText == 'end')
location.href='index.php';
document.getElementById('status').innerHTML = '';
	var e = http_request.responseText.split("|");
	if(e[2] && e[3])
	{
		document.getElementById('guest').innerHTML = e[2];
		document.getElementById('host').innerHTML = e[3];
	}
	if(e[4])
	{
		var message_guest = e[4];
		document.getElementById('message_guest').innerHTML = "<table width=150 border=0 cellpadding=0 cellspacing=0 class=message_box><tr><td><span style='color:#333399'><b>"+document.getElementById('guest').innerHTML+"</b></span></td></tr><tr><td>"+output_message(message_guest)+"</td></tr></table>";
	}
	if(e[5])
	{
		var message_host = e[5];
		document.getElementById('message_host').innerHTML = "<table width=150 border=0 cellpadding=0 cellspacing=0 class=message_box><tr><td><span style='color:#333399'><b>"+document.getElementById('host').innerHTML+"</b></span></td></tr><tr><td>"+output_message(message_host)+"</td></tr></table>";
	}
	if(e[0] && chess != e[0])
	{
		var chess_split_prev = chess.split(",");
		chess = e[0];
		flag = e[1];
		var chess_split_next = chess.split(",");
		document.getElementById('chess_pla').innerHTML = form_chess();
		var flag_change = 0;
		for(var i = 0;i < 63;i ++)
		{
			if(chess_split_next[i] != chess_split_prev[i])
			{
				flag_change = 1;
				document.getElementById('chess_'+(i+1)).className = 'chess_open';
				if(chess_split_prev[i] != 'blank' && chess_split_next[i] != 'blank')
				document.getElementById('status').innerHTML = (chess_split_prev[i].match('left')?'客人的':'房主的')+'<img width=64 height=64 src=pictures/'+chess_split_prev[i]+'.png>被吃掉';
			}
		}
		if(flag_change == 1)
		{
			document.getElementById('sound').innerHTML = dd_code('[wmp]msg.wav[/wmp]');
			flag_change = 0;
		}
		correctPNG()
	}
		document.getElementById('left_sum').innerHTML = left_sum;
		document.getElementById('right_sum').innerHTML = right_sum;	
var chess_split = chess.split(",");
if(left_sum == 0 || chess_split[27] != 'home')
{
document.getElementById('result').innerHTML = '游戏结束<br>胜方：<img src=winner.gif>'+document.getElementById('host').innerHTML+'';
}else
document.getElementById('result').innerHTML = '';
if(right_sum == 0 || chess_split[35] != 'home')
{
document.getElementById('result').innerHTML = '游戏结束<br>胜方：<img src=winner.gif>'+document.getElementById('guest').innerHTML+'';
}else
document.getElementById('result').innerHTML = '';

} else {
//document.getELementById('network_status').innerHTML = '连接失败，正在重新连接..';
}
}
}
//自定义调用函数
function getinfo(){
	if(document.getElementById('guest').innerHTML == '' || document.getElementById('host').innerHTML == '' || (site == 'left' && flag == 'host') || (site == 'right' && flag == 'guest'))
	{
		if(document.getElementById('guest').innerHTML == '' || document.getElementById('host').innerHTML == '')
		{
		document.getElementById('copy_url').style.display = '';
		}else
		document.getElementById('copy_url').style.display = 'none';
		send_request('getinfo.php?roomid=<?php echo $_GET[id];?>&site='+site+'&random='+Math.random());
	}
}
setInterval("getinfo()", 1000);
function copy_url(){
if(window.clipboardData.setData('text',document.location.href))
alert('复制成功！Ctrl + v 把地址发送给好友');
}
</script>
</head>

<body>
<table>
	<tr><td>房间：</td><td><?php echo $name;?></td></tr>
	<tr><td>你是：</td><td><?php echo $username == $host?'房主':'客人';?><span id=flag_s></span></td></tr>
</table>

<table>
<tr><td><div class=message_box>客人：<span id="guest"><?php echo $guest;?></span> <span id=guest_point></span></div></td><td align=right><div class=message_box>房主：<span id="host"><?php echo $host;?></span> <span id=host_point></span></div></td><td></td></tr>
<tr><td colspan=2><span id=chess_pla></span></td><td align=left valign=top>
	<table>
		<tr><td nowrap>客人：<span id=left_sum>8</span> <span id=left_point></span></td></tr>
		<tr><td>房主：<span id=right_sum>8</span> <span id=right_point></span></td></tr>
		<tr><td><script>if(site == 'right')document.write('[<a href=replay.php?roomid=<?php echo $_GET[id];?>>重新开始</a>][<a href=end.php?roomid=<?php echo $_GET[id];?>>结束游戏</a>]');</script></td></tr>
		<tr><td><span id=status></span></td></tr>
		<tr><td><span id=result></span></td></tr>
		<tr><td><div id=message_guest></div></td></tr>
		<tr><td><div id=message_host></div></td></tr>
	</table>
</td></tr>
</table>

<table><tr><td>
	聊天：<input class=box_chat type=text name=message size=75 value='' onkeypress="if(event.keyCode==13) {document.getElementById('button').click();}"> <input style='display:none' name=button onclick='send_message();' type=button value=发送消息>
</td></tr></table>
<table>
	<tr>
		<td><span id=copy_url style='display:none'>游戏地址：<script>document.write(location.href);</script> <input type="button" value="复制地址，与好友一起游戏！" onclick="copy_url();"></span></td>
	</tr>
</table>
<table>
	<tr><td valign=top>
		游戏规则：</td><td>
把对方的太公吃掉或者把对方的全部棋子吃掉即可获胜；<br />
上标数字越大的动物越强，但是龙不能吃鼠，而鼠却可吃龙；<br />
只有鼠能过河（图中蓝色部分），但是当鼠在河里的时候不能吃陆地上的动物；<br />
图中褐色部分为陷阱，对方动物进入陷阱后，抵抗能力会消失，己方任何动物均可吃之。<br />
8&rarr;龙 7&rarr;虎 6&rarr;牛 5&rarr;马 4&rarr;狗 3&rarr;鸡 2&rarr;蛇 1&rarr;鼠<br />
	</td></tr>
</table>
<script>document.getElementById('chess_pla').innerHTML=form_chess();</script>
<script>
function output_message(message){
	message = message.replace('(加)','+');
	message = message.replace('(减)','-');
	message = message.replace('(和)','&');
	message = message.replace('(等于)','=');
	message = message.replace('(问号)','?');
	message = message.replace('(c)',"&#39;");
	return dd_code(message);
}
function send_message(){
	var message = document.getElementById('message').value;
	message = message.replace('<','&lt;');
	message = message.replace('>','&gt;');
	message = message.replace('+','(加)');
	message = message.replace('-','(减)');
	message = message.replace('&','(和)');
	message = message.replace('=','(等于)');
	message = message.replace('?','(问号)');
	message = message.replace("'",'(c)');
	if(message != '')
	{
		send_request('send_message.php?roomid=<?php echo $_GET[id];?>&message='+message+'&site='+site+'&random='+Math.random());
		document.getElementById('message').value = '';
	}
}
function correctPNG() 
   {
   for(var i=0; i<document.images.length; i++)
      {
          var img = document.images[i]
          var imgName = img.src.toUpperCase()
          if (imgName.substring(imgName.length-3, imgName.length) == "PNG")
             {
                 var imgID = (img.id) ? "id='" + img.id + "' " : ""
                 var imgClass = (img.className) ? "class='" + img.className + "' " : ""
                 var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
                 var imgStyle = "display:inline-block;" + img.style.cssText 
                 if (img.align == "left") imgStyle = "float:left;" + imgStyle
                 if (img.align == "right") imgStyle = "float:right;" + imgStyle
                 if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle                
                 var strNewHTML = "<span " + imgID + imgClass + imgTitle
                 + " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
             + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
                 + "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>" 
                 img.outerHTML = strNewHTML
                 i = i-1
             }
      }
   }
correctPNG();
</script>
<script>
ie4=(document.all)?true:false;
ns4=(document.layers)?true:false;
function keyDown(e){
	if(ie4){
		if(window.event.keyCode==13){
		document.getElementById('message').focus();
		var ieKey=event.keyCode;
		}
	}
}
document.onkeydown=keyDown; 
</script>
<span id="sound" style="display:none"></span>
</body>
</html>
