<? if(!defined('UC_ROOT')) exit('Access Denied');?>
<? include $this->gettpl('header');?>

<div class="container">
	<h3>日志列表</h3>
	<div class="mainbox">
		<? if($loglist) { ?>
			<table class="datalist">
				<tr>
					<th>操作者</th>
					<th>IP</th>
					<th>时间</th>
					<th>操作</th>
					<th>其他 </th>
				</tr>
				<? foreach((array)$loglist as $log) {?>
					<tr>
						<td><strong><?=$log[1]?></strong></td>
						<td><?=$log[2]?></td>
						<td><?=$log[3]?></td>
						<td><?=$log[4]?></td>
						<td><?=$log[5]?></td>
					</tr>
				<? } ?>
				<tr class="nobg">
					<td class="tdpage" colspan="5"><?=$multipage?></td>
				</tr>
			</table>
		<? } else { ?>
			<div class="note">
				<p class="i">目前没有相关记录!</p>
			</div>
		<? } ?>
	</div>
</div>

<? include $this->gettpl('footer');?>