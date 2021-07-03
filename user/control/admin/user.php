<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: user.php 12180 2008-01-17 05:56:43Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

define('UC_USER_CHECK_USERNAME_FAILED', -1);
define('UC_USER_USERNAME_BADWORD', -2);
define('UC_USER_USERNAME_EXISTS', -3);
define('UC_USER_EMAIL_FORMAT_ILLEGAL', -4);
define('UC_USER_EMAIL_ACCESS_ILLEGAL', -5);
define('UC_USER_EMAIL_EXISTS', -6);

define('UC_LOGIN_SUCCEED', 0);
define('UC_LOGIN_ERROR_FOUNDER_PW', -1);
define('UC_LOGIN_ERROR_ADMIN_PW', -2);
define('UC_LOGIN_ERROR_ADMIN_NOT_EXISTS', -3);
define('UC_LOGIN_ERROR_SECCODE', -4);
define('UC_LOGIN_ERROR_FAILEDLOGIN', -5);

class control extends adminbase {

	function control() {
		$this->adminbase();
		if($_GET['a'] != 'login' && $_GET['a'] != 'logout') {
			$this->check_priv();
			if(!$this->user['isfounder'] && !$this->user['allowadminuser']) {
				$this->message('no_permission_for_this_module');
			}
		}
		$this->load('user');
	}

	//note public 管理员登陆
	function onlogin() {

		$this->load('user');
		$username = getgpc('username', 'P');
		$password = getgpc('password', 'P');
		$seccode = strtoupper(getgpc('seccode', 'P'));
		$isfounder = intval(getgpc('isfounder', 'P'));

		if($this->submitcheck()) {

			$failedlogin = $this->db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."failedlogins WHERE ip='$this->onlineip'");
			if($failedlogin['count'] > 4) {
				if($this->time - $failedlogin['lastupdate'] < 15 * 60) {
					$errorcode = UC_LOGIN_ERROR_FAILEDLOGIN;
				} else {
					$expiration = $this->time - 15 * 60;
					$this->db->query("DELETE FROM ".UC_DBTABLEPRE."failedlogins WHERE lastupdate<'$expiration'");
				}
			} else {
				include_once UC_ROOT.'lib/seccode.class.php';
				list($checkseccode, $expiration) = explode("\t", $this->authcode($_COOKIE['uc_secc'], 'DECODE'));
				$code = new seccode();
				$code->seccodeconvert($checkseccode);
				if($this->time - $expiration > 600) {
					exit('Access Denied');
				}
				if($checkseccode != $seccode) {
					$errorcode = UC_LOGIN_ERROR_SECCODE;//note 验证码错误
				} else {
					$errorcode = UC_LOGIN_SUCCEED;
					$this->user['username'] = $username;
					if($isfounder == 1) {
						$this->user['username'] = 'UCenterAdministrator';
						$md5password =  md5(md5($password).UC_FOUNDERSALT);
						if($md5password == UC_FOUNDERPW) {
							$this->setcookie('uc_founderauth', @$this->authcode("|$md5password|".md5($_SERVER['HTTP_USER_AGENT'])."|1", 'ENCODE', UC_KEY));
						} else {
							$errorcode = UC_LOGIN_ERROR_FOUNDER_PW;//note 创始人密码错误
						}
					} else {
						//note 查询 uc_admins 表
						$admin = $this->db->fetch_first("SELECT a.uid,m.salt,m.password FROM ".UC_DBTABLEPRE."admins a LEFT JOIN ".UC_DBTABLEPRE."members m USING(uid) WHERE a.username='$username'");
						if(!empty($admin)) {
							$md5password =  md5(md5($password).$admin['salt']);
							if($admin['password'] == $md5password) {
								$this->setcookie('uc_founderauth', @$this->authcode("$username|$md5password|".md5($_SERVER['HTTP_USER_AGENT'])."|0", 'ENCODE', UC_KEY));
							} else {
								$errorcode = UC_LOGIN_ERROR_ADMIN_PW;//note 管理员密码错误
							}
						} else {
							$errorcode = UC_LOGIN_ERROR_ADMIN_NOT_EXISTS;//note 该管理员不存在
						}
					}

					//note 登陆成功
					if($errorcode == 0) {
						$this->setcookie('uc_secc', '', -86400 * 365);
						$pwlen = strlen($password);
						$this->user['admin'] = 1;
						$this->writelog('login', 'succeed');
						header('location: admin.php');
						exit;
					} else {
						$this->writelog('login', 'error: user='.$this->user['username'].'; password='.($pwlen > 2 ? preg_replace("/^(.{".round($pwlen / 4)."})(.+?)(.{".round($pwlen / 6)."})$/s", "\\1***\\3", $password) : $password));
						if(empty($failedlogin)) {
							$expiration = $this->time - 15 * 60;
							$this->db->query("DELETE FROM ".UC_DBTABLEPRE."failedlogins WHERE lastupdate<'$expiration'");
							$this->db->query("INSERT INTO ".UC_DBTABLEPRE."failedlogins SET ip='$this->onlineip', count=1, lastupdate='$this->time'");
						} else {
							$this->db->query("UPDATE ".UC_DBTABLEPRE."failedlogins SET count=count+1,lastupdate='$this->time' WHERE ip='$this->onlineip'");
						}
					}
				}
			}
		}
		$username = htmlspecialchars($username);
		$password = htmlspecialchars($password);
		$this->view->assign('username', $username);
		$this->view->assign('password', $password);
		$this->view->assign('isfounder', $isfounder);
		$this->view->assign('errorcode', $errorcode);
		$this->view->display('admin_login');
	}

	//note public 管理员退出
	function onlogout() {
		$this->check_priv();
		$this->writelog('logout');
		$this->setcookie('uc_founderauth', '');
		header('location: admin.php');
	}

	//note public 内部接口
	function onadd() {
		$this->check_priv();
		if(!$this->submitcheck('submit')) {
			exit;
		}
		$username = getgpc('addname', 'P');
		$password =  getgpc('addpassword', 'P');
		$email = getgpc('addemail', 'P');

		if(($status = $this->_check_username($username)) < 0) {
			if($status == UC_USER_CHECK_USERNAME_FAILED) {
				$this->message('user_add_username_ignore', 'BACK');
			} elseif($status == UC_USER_USERNAME_BADWORD) {
				$this->message('user_add_username_badwords', 'BACK');
			} elseif($status == UC_USER_USERNAME_EXISTS) {
				$this->message('user_add_username_exists', 'BACK');
			}
		}
		if(($status = $this->_check_email($email)) < 0) {
			if($status == UC_USER_EMAIL_FORMAT_ILLEGAL) {
				$this->message('user_add_email_formatinvalid', 'BACK');
			} elseif($status == UC_USER_EMAIL_ACCESS_ILLEGAL) {
				$this->message('user_add_email_ignore', 'BACK');
			} elseif($status == UC_USER_EMAIL_EXISTS) {
				$this->message('user_add_email_exists', 'BACK');
			}
		}
		$uid = $_ENV['user']->add_user($username, $password, $email);
		$this->message('user_add_succeed', 'admin.php?m=user&a=ls');
	}

	//note public 内部接口
	function onls() {
		$this->check_priv();

		include_once UC_ROOT.'view/default/admin.lang.php';

		$status = 0;
		if(!empty($_POST['addname']) && $this->submitcheck()) {
			$this->check_priv();
			$username = getgpc('addname', 'P');
			$password =  getgpc('addpassword', 'P');
			$email = getgpc('addemail', 'P');

			if(($status = $this->_check_username($username)) >= 0) {
				if(($status = $this->_check_email($email)) >= 0) {
					$_ENV['user']->add_user($username, $password, $email);
					$status = 1;
					$this->writelog('user_add', "username=$username");
				}
			}
		}
		$this->view->assign('status', $status);

		if(!empty($_POST['delete'])) {
			$_ENV['user']->delete_user($_POST['delete']);
			$this->load('note');
			$_ENV['note']->send();
			$status = 2;
			$this->writelog('user_delete', "uid=".implode(',', $_POST['delete']));
		}
		$srchname = getgpc('srchname', 'R');
		$srchregdate = getgpc('srchregdate', 'R');
		$srchbefore = intval(getgpc('srchbefore', 'R'));

		$this->view->assign('srchname', $srchname);
		$this->view->assign('srchregdate', $srchregdate);
		$this->view->assign('srchbefore', $srchbefore);
		$srchbeforechecked = array($srchbefore=>' checked="checked"');
		$this->view->assign('srchbeforechecked', $srchbeforechecked);

		$sqladd = $srchname ? " AND username LIKE '%$srchname%'" : '';
		if($srchregdate != '' && strtotime($srchregdate)) {
			$operation = $srchbefore ? '<' : '>';
			$sqladd .= $srchregdate ? " AND regdate $operation ".strtotime($srchregdate) : '';
		}
		$sqladd = $sqladd ? " WHERE 1 $sqladd" : '';

		$num = $_ENV['user']->get_total_num($sqladd);
		$userlist = $_ENV['user']->get_list($_GET['page'], UC_PPP, $num, $sqladd);
		$multipage = $this->page($num, UC_PPP, $_GET['page'], 'admin.php?m=user&a=ls&srchname='.$srchname.'&srchregdate='.$srchregdate);

		$this->_format_userlist($userlist);
		$this->view->assign('userlist', $userlist);
		//$this->view->assign('apps', $this->cache['apps']);
		$adduser = getgpc('adduser');
		$a = getgpc('a');
		$this->view->assign('multipage', $multipage);
		$this->view->assign('adduser', $adduser);
		$this->view->assign('a', $a);
		$this->view->display('admin_user');

	}

	function onedit() {
		$uid = getgpc('uid');
		$status = 0;
		if(!$this->user['isfounder']) {
			$isprotected = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."protectedmembers WHERE uid = '$uid'");
			if($isprotected) {
				$this->message('user_edit_noperm');
			}
		}

		if($this->submitcheck()) {
			$username = getgpc('username', 'P');
			$newusername = getgpc('newusername', 'P');
			$password = getgpc('password', 'P');
			$email = getgpc('email', 'P');
			$usernamesqladd = $passwordsqladd = '';
			if($username != $newusername) {
				//note 判断是否已经存在用户名
				if($_ENV['user']->get_user_by_username($newusername)) {
					$this->message('admin_user_exists');
				}
				$usernamesqladd = "username='$newusername', ";
				//note 通知改名
				$this->load('note');
				$_ENV['note']->add('renameuser', 'uid='.$uid.'&oldusername='.urlencode($username).'&newusername='.urlencode($newusername));
			}
			if($password) {
				$salt = substr(uniqid(rand()), 0, 6);
				$orgpassword = $password;
				$password = md5(md5($password).$salt);
				$passwordsqladd = "password='$password', salt='$salt', ";
				$this->load('note');
				$_ENV['note']->add('updatepw', 'username='.urlencode($username).'&password=');
			}

			$this->db->query("UPDATE ".UC_DBTABLEPRE."members SET
				$usernamesqladd
				$passwordsqladd
				email='$email'
				WHERE uid='$uid'");
			$status = $this->db->errno() ? -1 : 1;
		}
		$user = $this->db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."members WHERE uid='$uid'");
		$this->view->assign('uid', $uid);
		$this->view->assign('user', $user);
		$this->view->assign('status', $status);
		$this->view->display('admin_user');
	}

	//note private 校验用户名

	function _check_username($username) {
		$username = addslashes(trim(stripslashes($username)));
		if(!$_ENV['user']->check_username($username)) {
			return UC_USER_CHECK_USERNAME_FAILED;
/*		} elseif($username != $_ENV['user']->replace_badwords($username)) {
			return UC_USER_USERNAME_BADWORD;*/
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
		} elseif($this->settings['doublee'] && $_ENV['user']->check_emailexists($email)) {
			return UC_USER_EMAIL_EXISTS;
		} else {
			return 1;
		}
	}

	function _format_userlist(&$userlist) {
		if(is_array($userlist)) {
			foreach($userlist AS $key => $user) {
				$userlist[$key]['regdate'] = $this->date($user['regdate']);
			}
		}
	}

}

?>