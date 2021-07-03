<?php
/**
 * UCenter Ӧ�ó��򿪷� Example
 *
 * ���ƶ���Ϣƽ̨�� Example ����
 */

$timeoffset = 8;
$ppp = 10;

if(!empty($_GET['pmdel']) && !empty($_POST['delete'])) {
	if(uc_pm_delete($Example_uid, $_GET['folder'], $_POST['delete'])) {
		echo '<br />����Ϣ��ɾ��<br />';
	}
}

$_GET['folder'] = !empty($_GET['folder']) ? $_GET['folder'] : 'inbox';

if(!empty($_GET['pmignore'])) {
	uc_pm_ignore($Example_uid);
	$newpm = 0;
}

echo ($newpm ? '<font color="red">New!</font> <a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmignore=yes">������ʾ</a><br />' : '').
	'<br />
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=inbox">�ռ���</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=outbox">������</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=newbox&filter=newpm">δ����Ϣ</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=inbox&filter=newpm">δ���ռ���</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=outbox&filter=newpm">δ��������</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=inbox&filter=systempm">ϵͳ��Ϣ</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder=inbox&filter=announcepm">������Ϣ</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmsend=yes">������Ϣ</a>
	<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&blackls=yes">������</a><br />';

if(!empty($_GET['pmsend'])) {
	if(empty($_POST)) {
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmsend=yes">
			���͵�: <br /><input name="msgto" /><br />
			����: <br /><input name="subject" /><br />
			��Ϣ����: <br /><textarea name="message" cols="40" rows="10" /></textarea><br />
			<input type="submit" value="����" /></form>';
		exit;
	} else {
		if(uc_pm_send($Example_uid, $_POST['msgto'], $_POST['subject'], $_POST['message'], 1, $_POST['replypmid'], 1)) {
			echo '<br />����Ϣ�ѷ���<br />';
		} else {
			echo '<br />����Ϣ����ʧ�ܣ�<a href="###" onclick="history.back()">����</a><br />';
			exit;
		}
	}
}

if(!empty($_GET['blackls'])) {
	if(!empty($_POST)) {
		uc_pm_blackls_set($Example_uid, $_POST['blackls']);
		echo '<br />�������ѱ���<br />';
	}
	$blackls = uc_pm_blackls_get($Example_uid);
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?example=pmlist&blackls=yes">
		<textarea name="blackls" cols="40" rows="10" />'.htmlspecialchars($blackls).'</textarea><br />
		<input type="submit" value="����" /></form>';
	exit;
}

if(!empty($_GET['pmid'])) {
	$pms = uc_pm_view($Example_uid, $_GET['pmid']);
	echo '<br /><a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&folder='.$_GET['folder'].'&filter='.$_GET['filter'].'">����</a> ';
	foreach($pms as $pm) {
		echo '<hr>����:'.$pm['subject'].' '.($pm['new'] ? '<font color="red">New!</font>' : '').'<br />
			��������:'.gmdate('Y-m-d H:i:s', $pm['dateline'] + $timeoffset * 3600).'<br />������:'.$pm['msgfrom'].'<br /><br />'.$pm['message'];
	}

	if($pms[0]['msgfromid'] && $pms[0]['msgtoid']) {
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmsend=yes">
			<br /><input type="hidden" name="replypmid" value="'.$_GET['pmid'].'" /><br />
			����: <br /><input name="subject" /><br />
			��Ϣ����: <br /><textarea name="message" cols="40" rows="10" /></textarea><br />
			<input type="submit" value="�ظ�" /></form>';
	}
} else {
	$_GET['page'] =  max(1, intval($_GET['page']));
	$list = uc_pm_list($Example_uid, $_GET['page'], $ppp, $_GET['folder'], $_GET['filter'], 100);
	if($list['count']) {
		if($_GET['folder'] != 'newbox') {
			echo '<br />���� '.$list['count'].' ����Ϣ���� ';
			$totalpage = @ceil($list['count'] / $ppp);
			for($i = 1;$i <= $totalpage;$i++) {
				if($i != $_GET['page']) {
					echo '<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmdel=yes&folder='.$_GET['folder'].'&filter='.$_GET['filter'].'&page='.$i.'">['.$i.']</a> ';
				} else {
					echo '['.$i.'] ';
				}
			}
			echo ' ҳ';
		}

		echo ($_GET['filter'] != 'announcepm' ? '<form method="post" action="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmdel=yes&folder='.$_GET['folder'].'&filter='.$_GET['filter'].'">' : '').'<ul>';
		foreach($list['data'] as $pm) {
			$daterange = '';
			switch($pm['daterange']) {
				case 1: $daterange = '����';break;
				case 2: $daterange = '����';break;
				case 3: $daterange = 'ǰ��';break;
				case 4: $daterange = '����';break;
				case 5: $daterange = '����';break;
			}
			$new = $_GET['folder'] == 'inbox' && $pm['new'] == 1 || $_GET['folder'] == 'outbox' && $pm['new'] == 2;
			echo '<li '.($new ? 'style="font-weight: bold"': '').'>'.
				($_GET['filter'] != 'announcepm' ? '<input type="checkbox" name="delete[]" value="'.$pm['pmid'].'" />' : '').
				'<a href="'.$_SERVER['PHP_SELF'].'?example=pmlist&pmid='.$pm['pmid'].'&folder='.$_GET['folder'].'&filter='.$_GET['filter'].'">'.$pm['subject'].'</a>
				From '.$pm['msgfrom'].($pm['msgto'] ? ' To '.$pm['msgto'] : '').' @ '.gmdate('Y-m-d H:i:s', $pm['dateline'] + $timeoffset * 3600).' '.$daterange.'</li>
				<li style="list-style: none">'.$pm['message'].'</li>';
		}
		echo '</ul>'.($_GET['filter'] != 'announcepm' ? '<input type="submit" value="ɾ��" /></form>' : '');
	}
}

?>