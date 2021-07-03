<?php
/**
 * UCenter 应用程序开发 Example
 *
 * 自制短消息平台的 Example 代码
 */

$timeoffset = 8;
$ppp = 10;

if(!empty($_GET['pmdel']) && !empty($_POST['delete'])) {
	if(uc_pm_delete($Example_uid, $_GET['folder'], $_POST['delete'])) {
		echo '<br />短消息已删除<br />';
	}
}

$_GET['folder'] = !empty($_GET['folder']) ? $_GET['folder'] : 'inbox';

if(!empty($_GET['pmignore'])) {
	uc_pm_ignore($Example_uid);
	$newpm = 0;
}

echo ($newpm ? '<font color="red">New!</font> <a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmignore=yes">忽略提示</a><br />' : '').
	'<br />
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=inbox">收件箱</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=outbox">发件箱</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=newbox&filter=newpm">未读消息</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=inbox&filter=newpm">未读收件箱</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=outbox&filter=newpm">未读发件箱</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=inbox&filter=systempm">系统消息</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=inbox&filter=announcepm">公共消息</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmsend=yes">发送消息</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&blackls=yes">黑名单</a><br />';

if(!empty($_GET['pmsend'])) {
	if(empty($_POST)) {
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmsend=yes">
			发送到: <br /><input name="msgto" /><br />
			标题: <br /><input name="subject" /><br />
			消息内容: <br /><textarea name="message" cols="40" rows="10" /></textarea><br />
			<input type="submit" value="发送" /></form>';
		exit;
	} else {
		if(uc_pm_send($Example_uid, $_POST['msgto'], $_POST['subject'], $_POST['message'], 1, $_POST['replypmid'], 1)) {
			echo '<br />短消息已发送<br />';
		} else {
			echo '<br />短消息发送失败，<a href="###" onclick="history.back()">返回</a><br />';
			exit;
		}
	}
}

if(!empty($_GET['blackls'])) {
	if(!empty($_POST)) {
		uc_pm_blackls_set($Example_uid, $_POST['blackls']);
		echo '<br />黑名单已保存<br />';
	}
	$blackls = uc_pm_blackls_get($Example_uid);
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?example=pmlist&blackls=yes">
		<textarea name="blackls" cols="40" rows="10" />'.htmlspecialchars($blackls).'</textarea><br />
		<input type="submit" value="发送" /></form>';
	exit;
}

if(!empty($_GET['pmid'])) {
	$pms = uc_pm_view($Example_uid, $_GET['pmid']);
	echo '<br /><a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder='.$_GET['folder'].'&filter='.$_GET['filter'].'">返回</a> ';
	foreach($pms as $pm) {
		echo '<hr>标题:'.$pm['subject'].' '.($pm['new'] ? '<font color="red">New!</font>' : '').'<br />
			发送日期:'.gmdate('Y-m-d H:i:s', $pm['dateline'] + $timeoffset * 3600).'<br />发信人:'.$pm['msgfrom'].'<br /><br />'.$pm['message'];
	}

	if($pms[0]['msgfromid'] && $pms[0]['msgtoid']) {
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmsend=yes">
			<br /><input type="hidden" name="replypmid" value="'.$_GET['pmid'].'" /><br />
			标题: <br /><input name="subject" /><br />
			消息内容: <br /><textarea name="message" cols="40" rows="10" /></textarea><br />
			<input type="submit" value="回复" /></form>';
	}
} else {
	$_GET['page'] =  max(1, intval($_GET['page']));
	$list = uc_pm_list($Example_uid, $_GET['page'], $ppp, $_GET['folder'], $_GET['filter'], 100);
	if($list['count']) {
		if($_GET['folder'] != 'newbox') {
			echo '<br />共有 '.$list['count'].' 条消息，第 ';
			$totalpage = @ceil($list['count'] / $ppp);
			for($i = 1;$i <= $totalpage;$i++) {
				if($i != $_GET['page']) {
					echo '<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmdel=yes&folder='.$_GET['folder'].'&filter='.$_GET['filter'].'&page='.$i.'">['.$i.']</a> ';
				} else {
					echo '['.$i.'] ';
				}
			}
			echo ' 页';
		}

		echo ($_GET['filter'] != 'announcepm' ? '<form method="post" action="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmdel=yes&folder='.$_GET['folder'].'&filter='.$_GET['filter'].'">' : '').'<ul>';
		foreach($list['data'] as $pm) {
			$daterange = '';
			switch($pm['daterange']) {
				case 1: $daterange = '今天';break;
				case 2: $daterange = '昨天';break;
				case 3: $daterange = '前天';break;
				case 4: $daterange = '上周';break;
				case 5: $daterange = '更早';break;
			}
			$new = $_GET['folder'] == 'inbox' && $pm['new'] == 1 || $_GET['folder'] == 'outbox' && $pm['new'] == 2;
			echo '<li '.($new ? 'style="font-weight: bold"': '').'>'.
				($_GET['filter'] != 'announcepm' ? '<input type="checkbox" name="delete[]" value="'.$pm['pmid'].'" />' : '').
				'<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmid='.$pm['pmid'].'&folder='.$_GET['folder'].'&filter='.$_GET['filter'].'">'.$pm['subject'].'</a>
				From '.$pm['msgfrom'].($pm['msgto'] ? ' To '.$pm['msgto'] : '').' @ '.gmdate('Y-m-d H:i:s', $pm['dateline'] + $timeoffset * 3600).' '.$daterange.'</li>
				<li style="list-style: none">'.$pm['message'].'</li>';
		}
		echo '</ul>'.($_GET['filter'] != 'announcepm' ? '<input type="submit" value="删除" /></form>' : '');
	}
}

?>