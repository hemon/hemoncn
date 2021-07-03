<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: user.php 428 2008-04-30 07:57:24Z monkey $
*/

!defined('IN_UC') && exit('Access Denied');

define('UC_USER_CHECK_USERNAME_FAILED', -1);
define('UC_USER_USERNAME_BADWORD', -2);
define('UC_USER_USERNAME_EXISTS', -3);
define('UC_USER_EMAIL_FORMAT_ILLEGAL', -4);
define('UC_USER_EMAIL_ACCESS_ILLEGAL', -5);
define('UC_USER_EMAIL_EXISTS', -6);

class control extends base {

	function control() {
		$this->base();
		$this->load('user');
	}

	//note public 外部接口
	function onsynlogin() {
		$this->init_input();
		if($this->app['synlogin']) {
			if($this->user = $_ENV['user']->get_user_by_uid($this->input['uid'])) {
				$synstr = '';
				foreach($this->cache['apps'] as $appid => $app) {
					if($app['synlogin'] && $app['appid'] != $this->app['appid']) {
						$synstr .= '<script type="text/javascript" src="'.$app['url'].'/api/uc.php?time='.$this->time.'&code='.urlencode($this->authcode('action=synlogin&username='.$this->user['username'].'&uid='.$this->user['uid'].'&password='.$this->user['password']."&time=".$this->time, 'ENCODE', $app['authkey'])).'"></script>';
					}
				}
				exit("$synstr");
			}
		}
	}

	//note public 外部接口
	function onsynlogout() {
		$this->init_input();
		if($this->app['synlogin']) {
			$synstr = '';
			foreach($this->cache['apps'] as $appid => $app) {
				if($app['synlogin'] && $app['appid'] != $this->app['appid']) {
					$synstr .= '<script type="text/javascript" src="'.$app['url'].'/api/uc.php?time='.$this->time.'&code='.urlencode($this->authcode('action=synlogout&time='.$this->time, 'ENCODE', $app['authkey'])).'"></script>';
				}
			}
			exit("$synstr");
		}
	}

	//note public 内部接口
	function ondelete() {
		$this->init_input();
		$uid = @$this->input('uid');
		$num = $_ENV['user']->delete_user($uid);
		exit("$num");
	}

	//note public 外部接口 注册校验接口
	function onregister() {
		$this->init_input();
		$username = $this->input('username');
		$password =  $this->input('password');
		$email = $this->input('email');
		if(($status = $this->_check_username($username)) < 0) {
			exit("$status");
		}
		if(($status = $this->_check_email($email)) < 0) {
			exit("$status");
		}
		$uid = $_ENV['user']->add_user($username, $password, $email);
		exit("$uid");
	}

	//note public 外部接口 编辑帐户信息
	function onedit() {
		$this->init_input();
		if(!$this->input('ignoreoldpw') && $email && ($status = $this->_check_email(@$this->input('email'))) < 0) {
			exit("$status");
		}
		$status = $_ENV['user']->edit_user($this->input('username'), $this->input('oldpw'), $this->input('newpw'), $this->input('email'), $this->input('ignoreoldpw'));

		if($newpw && $status > 0) {
			$this->load('note');
			$_ENV['note']->add('updatepw', 'username='.urlencode($this->input('username')).'&password=');
			$_ENV['note']->send();
		}
		exit("$status");
	}

	//note public 外部接口 登陆接口
	function onlogin() {
		$this->init_input();
		$isuid = @$this->input('isuid');
		$username = @$this->input('username');
		$password = @$this->input('password');
		if($isuid) {
			$user = $_ENV['user']->get_user_by_uid($username);
		} else {
			$user = $_ENV['user']->get_user_by_username($username);
		}

		//note 用户名不存在
		if(empty($user)) {
			$status = -1;
		} elseif($user['password'] != md5(md5($password).$user['salt'])) {
			$status = -2;
		} else {
			$status = $user['uid'];
		}
		$merge = $status != -1 && !$isuid && $_ENV['user']->check_mergeuser($username) ? 1 : 0;
		exit($this->serialize(array($status, $user['username'], $password, $user['email'], $merge)));
	}

	//note public 外部接口 ajax 校验 EMAIL
	function oncheck_email() {
		$this->init_input();
		if(($status = $this->_check_email($this->input('email'))) < 0) {
			exit("$status");
		} else {
			exit('1');
		}

	}

	//note public 外部接口 ajax 校验用户名
	function oncheck_username() {
		$this->init_input();
		$username = $this->input('username');
		if(($status = $this->_check_username($username)) < 0) {
			exit("$status");
		} else {
			exit('1');
		}
	}

	//note public 外部接口
	function onget_user() {
		$this->init_input();
		$username = $this->input('username');
		if(!$this->input('isuid')) {
			$status = $_ENV['user']->get_user_by_username($username);
		} else {
			$status = $_ENV['user']->get_user_by_uid($username);
		}
		if($status) {
			exit($this->serialize(array($status['uid'],$status['username'],$status['email'])));
		} else {
			exit("0");
		}
	}

	/**
	 * -1 身份不合法
	 * -2 上传的数据流不合法
	 * -3 没有上传头象
	 */
	//note public 外部接口 flash 上传头像
	function onuploadavatar() {
		@header("Expires: 0");
		@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
		//header("Content-type: application/xml; charset=utf-8");
		$this->init_input(getgpc('agent', 'G'));

		$uid = $this->input('uid');
		if(empty($uid)) {
			exit;
		}
		if(empty($_FILES['Filedata'])) {
			exit;
		}
		$file = @$_FILES['Filedata']['tmp_name'];
		$filetype = '.jpg';//note FLASH 通过 MIME 头判断。
		@unlink(UC_DATADIR.'/tmp/upload'.$uid.$filetype);
		@copy($_FILES['Filedata']['tmp_name'], UC_DATADIR.'/tmp/upload'.$uid.$filetype) || @move_uploaded_file($_FILES['Filedata']['tmp_name'], UC_DATADIR.'/tmp/upload'.$uid.$filetype);
		$avatarurl = UC_DATAURL.'/tmp/upload'.$uid.$filetype;
		echo $avatarurl;
		exit;
	}

	//note public 外部接口 flash 方式裁剪头像 COOKIE 判断身份
	function onrectavatar() {
		@header("Expires: 0");
		@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
		header("Content-type: application/xml; charset=utf-8");
		$this->init_input(getgpc('agent'));
		$uid = $this->input('uid');
		if(empty($uid)) {
			exit('<root><message type="error" value="-1" /></root>');
		}
		$home = $this->get_home($uid);
		if(!is_dir(UC_DATADIR.'./avatar/'.$home)) {
			$this->set_home($uid, UC_DATADIR.'avatar/');
		}
		$bigavatarfile = UC_DATADIR.'./avatar/'.$this->get_avatar($uid, 'big');
		$middleavatarfile = UC_DATADIR.'./avatar/'.$this->get_avatar($uid, 'middle');
		$smallavatarfile = UC_DATADIR.'./avatar/'.$this->get_avatar($uid, 'small');
		$bigavatar = base64_decode(getgpc('avatar1', 'P'));
		$middleavatar = base64_decode(getgpc('avatar2', 'P'));
		$smallavatar = base64_decode(getgpc('avatar3', 'P'));
		if(!$bigavatar || !$middleavatar || !$smallavatar) {
			exit('<root><message type="error" value="-2" /></root>');
		}
		if($fp = @fopen($bigavatarfile, 'w')) {
			@fwrite($fp, $bigavatar);
			@fclose($fp);

			$fp = @fopen($middleavatarfile, 'w');
			@fwrite($fp, $middleavatar);
			@fclose($fp);

			$fp = @fopen($smallavatarfile, 'w');
			@fwrite($fp, $smallavatar);
			@fclose($fp);
			$filetype = '.jpg';//note FLASH 通过 MIME 头判断。
			@unlink(UC_DATADIR.'/tmp/upload'.$uid.$filetype);
			exit('<?xml version="1.0" ?><root><face success="1"/></root>');
		} else {
			exit('<?xml version="1.0" ?><root><face success="0"/></root>');
		}
	}

	//note public 得到保护的用户列表
	function ongetprotected() {
		$protectedmembers = $this->db->fetch_all("SELECT uid,username FROM ".UC_DBTABLEPRE."protectedmembers GROUP BY username");
		exit($this->serialize($protectedmembers));
	}

	function onaddprotected() {
		$this->init_input();
		$username = $this->input('username');
		$admin = $this->input('admin');
		$appid = $this->app['appid'];
		$usernames = (array)$username;
		foreach($usernames as $username) {
			$user = $_ENV['user']->get_user_by_username($username);
			$uid = $user['uid'];
			$this->db->query("REPLACE INTO ".UC_DBTABLEPRE."protectedmembers SET uid='$uid', username='$username', appid='$appid', dateline='{$this->time}', admin='$admin'", 'SILENT');
		}
		exit('1');
	}

	function ondeleteprotected() {
		$this->init_input();
		$username = $this->input('username');
		$usernames = (array)$username;
		foreach($usernames as $username) {
			$this->db->query("DELETE FROM ".UC_DBTABLEPRE."protectedmembers WHERE username='$username' AND appid='$appid'");
		}
		exit('1');
	}

	function onmerge() {
		$this->init_input();
		$oldusername = $this->input('oldusername');
		$newusername = $this->input('newusername');
		$uid = $this->input('uid');
		$password = $this->input('password');
		$email = $this->input('email');
		if(($status = $this->_check_username($newusername)) < 0) {
			return $status;
		}
		$uid = $_ENV['user']->add_user($newusername, $password, $email, $uid);
		$this->db->query("UPDATE ".UC_DBTABLEPRE."pms SET msgfrom='$newusername' WHERE msgfromid='$uid' AND msgfrom='$oldusername'");
		$this->db->query("DELETE FROM ".UC_DBTABLEPRE."mergemembers WHERE appid='".$this->app['appid']."' AND username='$oldusername'");
		exit("$uid");
	}

	//note private 校验用户名
	function _check_username($username) {
		$username = addslashes(trim(stripslashes($username)));
		if(!$_ENV['user']->check_username($username)) {
			return UC_USER_CHECK_USERNAME_FAILED;
		} elseif($username != $_ENV['user']->check_usernamecensor($username)) {
			return UC_USER_USERNAME_BADWORD;
		} elseif($_ENV['user']->check_usernameexists($username)) {
			return UC_USER_USERNAME_EXISTS;
		}
		return 1;
	}

	//note private 校验email
	function _check_email($email) {
		if(!$_ENV['user']->check_emailformat($email)) {
			return UC_USER_EMAIL_FORMAT_ILLEGAL;
		} elseif(!$_ENV['user']->check_emailaccess($email)) {
			return UC_USER_EMAIL_ACCESS_ILLEGAL;
		} elseif(!$this->settings['doublee'] && $_ENV['user']->check_emailexists($email)) {
			return UC_USER_EMAIL_EXISTS;
		} else {
			return 1;
		}
	}

}

?>