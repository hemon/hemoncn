<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_privacy.php 6763 2008-03-25 08:05:34Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

if(submitcheck('privacysubmit')) {
	
	//��˽
	foreach ($_POST['privacy']['view'] as $key => $value) {
		$space['privacy']['view'][$key] = intval($value);
	}
	//���Ͷ�̬
	$space['privacy']['feed'] = array();
	foreach ($_POST['privacy']['feed'] as $key => $value) {
		$space['privacy']['feed'][$key] = 1;
	}
	privacy_update();

	showmessage('do_success', 'cp.php?ac=privacy');
	
} elseif(submitcheck('privacy2submit')) {
	
	//����ɸѡ
	$space['privacy']['filter_icon'] = array();
	foreach ($_POST['privacy']['filter_icon'] as $key => $value) {
		$space['privacy']['filter_icon'][$key] = 1;
	}
	//�û�������
	$space['privacy']['filter_gid'] = array();
	foreach ($_POST['privacy']['filter_gid'] as $key => $value) {
		$space['privacy']['filter_gid'][$key] = intval($value);
	}
	privacy_update();
	
	//���º��ѻ���
	friend_cache($_SGLOBAL['supe_uid']);
	
	showmessage('do_success', 'cp.php?ac=privacy&op=view');
}

if($_GET['op'] == 'view') {
	//������
	$groups = getfriendgroup();

	//����
	$filter_icons = empty($space['privacy']['filter_icon'])?array():$space['privacy']['filter_icon'];
	$icons = $uids = $users = array();
	foreach ($filter_icons as $key => $value) {
		list($icon, $uid) = explode('|', $key);
		$icons[$key] = $icon;
		$uids[$key] = $uid;
	}
	if($uids) {
		$query = $_SGLOBAL['db']->query("SELECT uid, username FROM ".tname('space')." WHERE uid IN (".simplode($uids).")");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$users[$value['uid']] = $value['username'];
		}
	}

} elseif ($_GET['op'] == 'getgroup') {
	
	$gid = empty($_GET['gid'])?0:intval($_GET['gid']);
	$users = array();
	$query = $_SGLOBAL['db']->query("SELECT fusername FROM ".tname('friend')." WHERE uid='$_SGLOBAL[supe_uid]' AND status='1' AND gid='$gid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$users[] = $value['fusername'];
	}
	$ustr = empty($users)?'':shtmlspecialchars(implode(' ', $users));
	showmessage($ustr);//����
	
} else {
	
	//ҳ��ѡ��
	$_GET['op'] = 'push';
	
	$sels = array();
	foreach ($space['privacy']['view'] as $key => $value) {
		$sels['view'][$key] = array($value => ' selected');
	}
	foreach ($space['privacy']['feed'] as $key => $value) {
		$sels['feed'][$key] = ' checked';
	}
}

$actives = array($_GET['op']=>' class="active"');

include template('cp_privacy');

?>