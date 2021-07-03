<? if(!defined('UC_ROOT')) exit('Access Denied');?>
<? include $this->gettpl('header');?>

<div class="container">
	<? if($updated) { ?>
		<div class="correctmsg"><p>更新成功。</p></div>
	<? } elseif($a == 'register') { ?>
		<div class="note fixwidthdec"><p class="i">允许/禁止的 Email 地址只需填写 Email 的域名部分，每行一个，例如 @hotmail.com</p></div>
	<? } ?>
	<? if($a == 'ls') { ?>
		<div class="mainbox nomargin">
			<form action="admin.php?m=setting&a=ls" method="post">
				<input type="hidden" name="formhash" value="<?=FORMHASH?>">
				<table class="opt">
					<tr>
						<th colspan="2">日期格式:</th>
					</tr>
					<tr>
						<td><input type="text" class="txt" name="dateformat" value="<?=$dateformat?>" /></td>
						<td>使用 yyyy(yy) 表示年，mm 表示月，dd 表示天。如 yyyy-mm-dd 表示 2000-1-1</td>
					</tr>
					<tr>
						<th colspan="2">时间格式:</th>
					</tr>
					<td>
						<input type="radio" id="hr24" class="radio" name="timeformat" value="1" <?=$timeformat[1]?> /><label for="hr24">24 小时制</label>
						<input type="radio" id="hr12" class="radio" name="timeformat" value="0" <?=$timeformat[0]?> /><label for="hr12">12 小时制</label>
					</td>
					<tr>
						<th colspan="2">时区:</th>
					</tr>
					<tr>
						<td>
							<select name="timeoffset">
								<option value="-12" <?=$checkarray['-12']?>>(GMT -12:00) Eniwetok, Kwajalein</option>
								<option value="-11" <?=$checkarray['-11']?>>(GMT -11:00) Midway Island, Samoa</option>
								<option value="-10" <?=$checkarray['-10']?>>(GMT -10:00) Hawaii</option>
								<option value="-9" <?=$checkarray['-9']?>>(GMT -09:00) Alaska</option>
								<option value="-8" <?=$checkarray['-8']?>>(GMT -08:00) Pacific Time (US &amp; Canada), Tijuana</option>
								<option value="-7" <?=$checkarray['-7']?>>(GMT -07:00) Mountain Time (US &amp; Canada), Arizona</option>
								<option value="-6" <?=$checkarray['-6']?>>(GMT -06:00) Central Time (US &amp; Canada), Mexico City</option>
								<option value="-5" <?=$checkarray['-5']?>>(GMT -05:00) Eastern Time (US &amp; Canada), Bogota, Lima, Quito</option>
								<option value="-4" <?=$checkarray['-4']?>>(GMT -04:00) Atlantic Time (Canada), Caracas, La Paz</option>
								<option value="-3.5" <?=$checkarray['-3.5']?>>(GMT -03:30) Newfoundland</option>
								<option value="-3" <?=$checkarray['-3']?>>(GMT -03:00) Brassila, Buenos Aires, Georgetown, Falkland Is</option>
								<option value="-2" <?=$checkarray['-2']?>>(GMT -02:00) Mid-Atlantic, Ascension Is., St. Helena</option>
								<option value="-1" <?=$checkarray['-1']?>>(GMT -01:00) Azores, Cape Verde Islands</option>
								<option value="0" <?=$checkarray['0']?>>(GMT) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia</option>
								<option value="1" <?=$checkarray['1']?>>(GMT +01:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome</option>
								<option value="2" <?=$checkarray['2']?>>(GMT +02:00) Cairo, Helsinki, Kaliningrad, South Africa</option>
								<option value="3" <?=$checkarray['3']?>>(GMT +03:00) Baghdad, Riyadh, Moscow, Nairobi</option>
								<option value="3.5" <?=$checkarray['3.5']?>>(GMT +03:30) Tehran</option>
								<option value="4" <?=$checkarray['4']?>>(GMT +04:00) Abu Dhabi, Baku, Muscat, Tbilisi</option>
								<option value="4.5" <?=$checkarray['4.5']?>>(GMT +04:30) Kabul</option>
								<option value="5" <?=$checkarray['5']?>>(GMT +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
								<option value="5.5" <?=$checkarray['5.5']?>>(GMT +05:30) Bombay, Calcutta, Madras, New Delhi</option>
								<option value="5.75" <?=$checkarray['5.75']?>>(GMT +05:45) Katmandu</option>
								<option value="6" <?=$checkarray['6']?>>(GMT +06:00) Almaty, Colombo, Dhaka, Novosibirsk</option>
								<option value="6.5" <?=$checkarray['6.5']?>>(GMT +06:30) Rangoon</option>
								<option value="7" <?=$checkarray['7']?>>(GMT +07:00) Bangkok, Hanoi, Jakarta</option>
								<option value="8" <?=$checkarray['8']?>>(GMT +08:00) &#x5317;&#x4eac;(Beijing), Hong Kong, Perth, Singapore, Taipei</option>
								<option value="9" <?=$checkarray['9']?>>(GMT +09:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>
								<option value="9.5" <?=$checkarray['9.5']?>>(GMT +09:30) Adelaide, Darwin</option>
								<option value="10" <?=$checkarray['10']?>>(GMT +10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok</option>
								<option value="11" <?=$checkarray['11']?>>(GMT +11:00) Magadan, New Caledonia, Solomon Islands</option>
								<option value="12" <?=$checkarray['12']?>>(GMT +12:00) Auckland, Wellington, Fiji, Marshall Island</option>
							</select>
						</td>
						<td>默认为: GMT +08:00</td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" 提 交 " class="btn" tabindex="3" /></div>
			</form>
		</div>
	<? } else { ?>
		<div class="mainbox nomargin">
			<form action="admin.php?m=setting&a=register" method="post">
				<input type="hidden" name="formhash" value="<?=FORMHASH?>">
				<table class="opt">
					<tr>
						<th colspan="2">是否允许同一 Email 地址注册多个用户:</th>
					</tr>
					<tr>
						<td>
							<input type="radio" id="yes" class="radio" name="doublee" value="1" <?=$doublee[1]?> /><label for="yes">是</label>
							<input type="radio" id="no" class="radio" name="doublee" value="0" <?=$doublee[0]?> /><label for="no">否</label>
						</td>
					</tr>
					<tr>
						<th colspan="2">允许的 Email 地址:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="accessemail"><?=$accessemail?></textarea></td>
						<td valign="top">只允许使用这些域名结尾的 Email 地址注册。</td>
					</tr>
					<tr>
						<th colspan="2">禁止的 Email 地址:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="censoremail"><?=$censoremail?></textarea></td>
						<td valign="top">禁止使用这些域名结尾的 Email 地址注册。</td>
					</tr>
					<tr>
						<th colspan="2">禁止的用户名:</th>
					</tr>
					<tr>
						<td><textarea class="area" name="censorusername"><?=$censorusername?></textarea></td>
						<td valign="top">可以设置通配符，如: 张三*</td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" 提 交 " class="btn" tabindex="3" /></div>
			</form>
		</div>
	<? } ?>
</div>

<? include $this->gettpl('footer');?>