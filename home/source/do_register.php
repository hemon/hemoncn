<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: do_register.php 7352 2008-05-13 01:41:40Z zhengqingpeng $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$op = $_GET['op'] ? trim($_GET['op']) : '';

if($_SGLOBAL['supe_uid']) {
	showmessage('do_success', 'space.php?do=home', 0);
}

//û�е�¼��
$_SGLOBAL['nologinform'] = 1;

//��������
$uid = empty($_GET['uid'])?0:intval($_GET['uid']);
$invite = empty($_GET['invite'])?'':$_GET['invite'];
$invitearr = array();
if($uid && $invite) {
	include_once(S_ROOT.'./source/function_cp.php');
	$invitearr = invite_get($uid, $invite);
}

if(empty($op)) {
	
	if($_SCONFIG['closeregister']) {
		if($_SCONFIG['closeinvite']) {
			showmessage('not_open_registration');
		} elseif(empty($invitearr)) {
			showmessage('not_open_registration_invite');
		}
	}
	
	//�Ƿ�ر�վ��
	checkclose();
	
	if(submitcheck('registersubmit')) {
	
		//�Ѿ�ע���û�
		if($_SGLOBAL['supe_uid']) {
			showmessage('registered', 'space.php');
		}
		
		if($_SCONFIG['seccode_register']) {
			include_once(S_ROOT.'./source/function_cp.php');
			if(!ckseccode($_POST['seccode'])) {
				showmessage('incorrect_code');
			}
		}
		
		if(!@include_once S_ROOT.'./uc_client/client.php') {
			showmessage('system_error');
		}
	
		if($_POST['password'] != $_POST['password2']) {
			showmessage('password_inconsistency');
		}
		
		if(!$_POST['password'] || $_POST['password'] != addslashes($_POST['password'])) {
			showmessage('profile_passwd_illegal');
		}
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];
		
		$newuid = uc_user_register($username, $password, $email);
		if($newuid <= 0) {
			if($newuid == -1) {
				showmessage('user_name_is_not_legitimate');
			} elseif($newuid == -2) {
				showmessage('include_not_registered_words');
			} elseif($newuid == -3) {
				showmessage('user_name_already_exists');
			} elseif($newuid == -4) {
				showmessage('email_format_is_wrong');
			} elseif($newuid == -5) {
				showmessage('email_not_registered');
			} elseif($newuid == -6) {
				showmessage('email_has_been_registered');
			} else {
				showmessage('register_error');
			}
		} else {
			$setarr = array(
				'uid' => $newuid,
				'username' => $username,
				'password' => md5("$newuid|$_SGLOBAL[timestamp]")//���������������
			);
			//���±����û���
			inserttable('member', $setarr, 0, true);
			
			//��ͨ�ռ�
			include_once(S_ROOT.'./source/function_space.php');
			$space = space_open($newuid, $username, 0, $email);
	
			//����session
			insertsession($setarr);
			
			//����cookie
			ssetcookie('auth', authcode("$setarr[password]\t$setarr[uid]", 'ENCODE'), 2592000);
			ssetcookie('loginuser', $username, 31536000);
			ssetcookie('_refer', '');
	
			//��������
			if($invitearr) {
				invite_update($invitearr['id'], $setarr['uid'], $setarr['username'], $invitearr['uid'], $invitearr['username']);
			}
	
			showmessage('registered', 'space.php?do=home');
		}
	
	}
	
	include template('do_register');
	
} elseif($op == "checkusername") {

	$username = trim($_GET['username']);

	@include_once (S_ROOT.'./uc_client/client.php');
	$ucresult = uc_user_checkname($username);

	if($ucresult == -1) {
		showmessage('user_name_is_not_legitimate');
	} elseif($ucresult == -2) {
		showmessage('include_not_registered_words');
	} elseif($ucresult == -3) {
		showmessage('user_name_already_exists');
	} else {
		showmessage('succeed');
	}
} elseif($op == "checkseccode") {
	if($_SCONFIG['seccode_register']) {
		include_once(S_ROOT.'./source/function_cp.php');
		if(ckseccode(trim($_GET['seccode']))) {
			showmessage('succeed');
		} else {
			showmessage('incorrect_code');
		}
	}
}
?>
