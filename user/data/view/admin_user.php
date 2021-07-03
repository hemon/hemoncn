<? if(!defined('UC_ROOT')) exit('Access Denied');?>
<? include $this->gettpl('header');?>

<script src="js/common.js" type="text/javascript"></script>
<script src="js/calendar.js" type="text/javascript"></script>

<? if($a == 'ls') { ?>

	<script type="text/javascript">
		function switchbtn(btn) {
			$('srchuserdiv').style.display = btn == 'srch' ? '' : 'none';
			$('srchuserdiv').className = btn == 'srch' ? 'tabcontentcur' : '' ;
			$('srchuserbtn').className = btn == 'srch' ? 'tabcurrent' : '';
			$('adduserdiv').style.display = btn == 'srch' ? 'none' : '';
			$('adduserdiv').className = btn == 'srch' ? '' : 'tabcontentcur';
			$('adduserbtn').className = btn == 'srch' ? '' : 'tabcurrent';
		}
	</script>

	<div class="container">
		<? if($status) { ?>
		<div class="<? if($status > 0) { ?>correctmsg<? } else { ?>errormsg<? } ?>"><p><? if($status < 0) { ?><em>添加用户失败:</em> <? } ?><? if($status == 2) { ?>成功删除用户<? } elseif($status == 1) { ?>成功添加用户。<? } elseif($status == -1) { ?>用户名不合法<? } elseif($status == -2) { ?>用户名包含敏感字符<? } elseif($status == -3) { ?>该用户名已经被注册<? } elseif($status == -4) { ?>Email 地址不合法<? } elseif($status == -5) { ?>Email 包含不可使用的邮箱域名<? } elseif($status == -6) { ?>该 Email 地址已经被注册<? } ?></p></div>
		<? } ?>
		<div class="hastabmenu">
			<ul class="tabmenu">
				<li id="srchuserbtn" class="tabcurrent"><a href="#" onclick="switchbtn('srch')">搜索用户</a></li>
				<li id="adduserbtn"><a href="#" onclick="switchbtn('add')">添加用户</a></li>
			</ul>
			<div id="adduserdiv" class="tabcontent" style="display:none;">
				<form action="admin.php?m=user&a=ls&adduser=yes" method="post">
				<input type="hidden" name="formhash" value="<?=FORMHASH?>">
				<table width="100%">
					<tr>
						<td>用户名:</td>
						<td><input type="text" name="addname" class="txt" /></td>
						<td>密码:</td>
						<td><input type="text" name="addpassword" class="txt" /></td>
						<td>Email:</td>
						<td><input type="text" name="addemail" class="txt" /></td>
						<td><input type="submit" value="提 交"  class="btn" /></td>
					</tr>
				</table>
				</form>
			</div>
			<div id="srchuserdiv" class="tabcontentcur">
				<form action="admin.php?m=user&a=ls" method="post">
				<input type="hidden" name="formhash" value="<?=FORMHASH?>">
				<table width="100%">
					<tr>
						<td>用户名:</td>
						<td><input type="text" name="srchname" class="txt" /></td>
						<td>注册日期:</td>
						<td><input type="text" name="srchregdate" onclick="showcalendar();" value="" class="txt" /></td>
						<td><input type="radio" class="radio" name="srchbefore" id="before" value="1" /><label for="before">之前</label>��<input type="radio" class="radio" name="srchbefore" id="after" value="0" /><label for="after">之后</label></td>
						<td><input type="submit" value="提 交" class="btn" /></td>
					</tr>
				</table>
				</form>
			</div>
		</div>

		<? if($adduser) { ?><script type="text/javascript">switchbtn('add');</script><? } ?>

		<h3>用户列表</h3>
		<div class="mainbox">
			<? if($userlist) { ?>
				<form action="admin.php?m=user&a=ls&srchname=<?=$srchname?>&srchregdate=<?=$srchregdate?>" onsubmit="return confirm('该操作不可恢复，您确认要删除这些用户吗？');" method="post">
				<input type="hidden" name="formhash" value="<?=FORMHASH?>">
				<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
					<tr>
						<th><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall">删除</label></th>
						<th>用户名</th>
						<th>Email</th>
						<th>注册日期</th>
						<th>注册IP</th>
						<th>编辑</th>
					</tr>
					<? foreach((array)$userlist as $user) {?>
						<tr>
							<td class="option"><input type="checkbox" name="delete[]" value="<?=$user['uid']?>" class="checkbox" /></td>
							<td><strong><?=$user['username']?></strong></td>
							<td><?=$user['email']?></td>
							<td><?=$user['regdate']?></td>
							<td><?=$user['regip']?></td>
							<td><a href="admin.php?m=user&a=edit&uid=<?=$user['uid']?>">编辑</a></td>
						</tr>
					<? } ?>
					<tr class="nobg">
						<td><input type="submit" value="提 交" class="btn" /></td>
						<td class="tdpage" colspan="6"><?=$multipage?></td>
					</tr>
				</table>
				</form>
			<? } else { ?>
				<div class="note">
					<p class="i">目前没有相关记录!</p>
				</div>
			<? } ?>
		</div>
	</div>

<? } else { ?>

	<div class="container">
		<h3 class="marginbot">编辑会员资料
			<? if(getgpc('fromadmin')) { ?>
				<a href="admin.php?m=admin&a=ls" class="sgbtn">返回管理员列表</a>
			<? } else { ?>
				<a href="admin.php?m=user&a=ls" class="sgbtn">返回用户列表</a>
			<? } ?>
		</h3>
		<? if($status == 1) { ?>
			<div class="correctmsg"><p>编辑会员资料成功</p></div>
		<? } elseif($status == -1) { ?>
			<div class="correctmsg"><p>编辑会员资料失败</p></div>
		<? } else { ?>
			<div class="note"><p class="i">密码留空，保持不变。</p></div>
		<? } ?>
		<div class="mainbox">
			<form action="admin.php?m=user&a=edit&uid=<?=$uid?>" method="post">
			<input type="hidden" name="formhash" value="<?=FORMHASH?>">
				<table class="opt">
					<tr>
						<th>用户名:</th>
					</tr>
					<tr>
						<td>
							<input type="text" name="newusername" value="<?=$user['username']?>" class="txt" />
							<input type="hidden" name="username" value="<?=$user['username']?>" class="txt" />
						</td>
					</tr>
					<tr>
						<th>密　码:</th>
					</tr>
					<tr>
						<td>
							<input type="text" name="password" value="" class="txt" />
						</td>
					</tr>
					<tr>
						<th>Email:</th>
					</tr>
					<tr>
						<td>
							<input type="text" name="email" value="<?=$user['email']?>" class="txt" />
						</td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" 提 交 " class="btn" tabindex="3" /></div>
			</form>
		</div>
	</div>
<? } ?>
<? include $this->gettpl('footer');?>