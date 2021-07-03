<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: admin.php 12126 2008-01-11 09:40:32Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class adminbase extends base {

	function adminbase() {
		$this->base();
		$a = getgpc('a');
		if(getgpc('m') !='user' && $a != 'login' && $a != 'logout') {
			$this->check_priv();
		}
	}

	/**
	 * 检查权限
	 *
	 */
	function check_priv() {
		$founderauth = getgpc('uc_founderauth', 'C');
		$arr = explode('|', $this->authcode($founderauth, 'DECODE', UC_KEY));
		$status = 0;
		if(empty($arr)) {
			$status = -1;
		} else {
			list($username, $password, $useragent, $isfounder) = $arr;
			if($isfounder == '1') {
				$username = 'UCenterAdministrator';
				$this->user['isfounder'] = 1;
				if($password != UC_FOUNDERPW || $useragent != md5($_SERVER['HTTP_USER_AGENT'])) {
					$status = -1;
				}
			} else {
				//note 数据库校验
				$admin = $this->db->fetch_first("SELECT a.*, m.* FROM ".UC_DBTABLEPRE."admins a LEFT JOIN ".UC_DBTABLEPRE."members m USING(uid) WHERE a.username='$username'");
				if(empty($password) || $admin['password'] != $password) {
					$status = -1;
				} else {
					$this->user = $admin;
				}
			}
		}
		$this->view->assign('user', $this->user);
		if($status < 0) {
			header('location: '.UC_API.'/admin.php?m=user&a=login');
			exit;
		} else {
			$this->user['username'] = $username;
			$this->user['admin'] = 1;
		}
	}

	/**
	 * 检查是否为 创始人
	 *
	 * @param int $uid
	 * @return bool
	 */
	function is_founder($username) {
		return $this->user['isfounder'];
	}

	/**
	 * 生成后台管理日志
	 *
	 * @param string $action 管理操作
	 * @param string $extra  附加信息
	 */
	function writelog($action, $extra = '') {
		$log = htmlspecialchars($this->user['username']."\t".$this->onlineip."\t".$this->time."\t$action\t$extra");
		$logfile = UC_ROOT.'./data/logs/'.gmdate('Ym', $this->time).'.php';
		if(@filesize($logfile) > 2048000) {
			PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
			$hash = '';
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
			for($i = 0; $i < 4; $i++) {
				$hash .= $chars[mt_rand(0, 61)];
			}
			@rename($logfile, UC_ROOT.'./data/logs/'.gmdate('Ym', $this->time).'_'.$hash.'.php');
		}
		if($fp = @fopen($logfile, 'a')) {
			@flock($fp, 2);
			@fwrite($fp, "<?PHP exit;?>\t".str_replace(array('<?', '?>'), '', $log)."\n");
			@fclose($fp);
		}
	}

	function _call($a, $arg) {
		//note 转发
		if(method_exists($this, $a) && $a{0} != '_') {
			$this->$a();
		} else {
			exit('Access Denied');
		}
	}

}

?>