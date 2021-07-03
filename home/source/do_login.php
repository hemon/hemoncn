<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: do_login.php 7358 2008-05-13 06:15:41Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$refer = rawurldecode($_SCOOKIE['_refer']);
if(preg_match("/(cp.php|admincp.php|do.php)/i", $refer)) {
	$refer = '';
}
if(empty($refer)) {
	$refer = 'space.php?do=home';
}

//��������
$uid = empty($_GET['uid'])?0:intval($_GET['uid']);
$invite = empty($_GET['invite'])?'':$_GET['invite'];
$invitearr = array();
if($uid && $invite) {
	include_once(S_ROOT.'./source/function_cp.php');
	$invitearr = invite_get($uid, $invite);
}

//û�е�¼��
$_SGLOBAL['nologinform'] = 1;

if(submitcheck('loginsubmit')) {

	$password = $_POST['password'];
	$username = trim($_POST['username']);
	$cookietime = intval($_POST['cookietime']);
	
	$cookiecheck = $cookietime?' checked':'';
	$membername = $username;
	
	if(empty($_POST['username'])) {
		showmessage('users_were_not_empty_please_re_login');
	}
	
	if($_SCONFIG['seccode_login']) {
		include_once(S_ROOT.'./source/function_cp.php');
		if(!ckseccode($_POST['seccode'])) {
			$_SGLOBAL['input_seccode'] = 1;
			include template('do_login');
			exit;
		}
	}

	//ͬ����ȡ�û�Դ
	if(!$passport = getpassport($username, $password)) {
		showmessage('login_failure_please_re_login', 'do.php?ac=login');
	}
	
	$setarr = array(
		'uid' => $passport['uid'],
		'username' => addslashes($passport['username']),
		'password' => md5("$passport[uid]|$_SGLOBAL[timestamp]")//���������������
	);
		
	//������ǰ�û�
	$query = $_SGLOBAL['db']->query("SELECT password FROM ".tname('member')." WHERE uid='$setarr[uid]'");
	if($value = $_SGLOBAL['db']->fetch_array($query)) {
		$setarr['password'] = addslashes($value['password']);
	} else {
		//���±����û���
		inserttable('member', $setarr, 0, true);
	}

	//��������session
	$space = insertsession($setarr);
	
	//����cookie
	ssetcookie('auth', authcode("$setarr[password]\t$setarr[uid]", 'ENCODE'), $cookietime);
	ssetcookie('loginuser', $passport['username'], 31536000);
	ssetcookie('_refer', '');
	
	//��ͨ�ռ�
	if(empty($space)) {
		include_once(S_ROOT.'./source/function_space.php');
		$space = space_open($setarr['uid'], $setarr['username'], 0, $passport['email']);
	}
	
	//ͬ����¼
	include_once S_ROOT.'./uc_client/client.php';
	$ucsynlogin = uc_user_synlogin($setarr['uid']);
	
	//��������
	if($invitearr) {
		//��Ϊ����
		invite_update($invitearr['id'], $setarr['uid'], $setarr['username'], $invitearr['uid'], $invitearr['username']);
	}

	showmessage('login_success', $_POST['refer'], 1, array($ucsynlogin));
}

$membername = empty($_SCOOKIE['loginuser'])?'':sstripslashes($_SCOOKIE['loginuser']);
$cookiecheck = ' checked';

include template('do_login');

?>
