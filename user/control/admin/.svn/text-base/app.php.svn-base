<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: app.php 12186 2008-01-17 06:40:29Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends adminbase {

	function control() {
		$this->adminbase();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadminapp']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load('app');
		$this->load('misc');
	}

	//note public 应用
	function onls() {
		$status = $affectedrows = 0;
		if(!empty($_POST['delete'])) {
			$affectedrows += $_ENV['app']->delete_apps($_POST['delete']);
			foreach($_POST['delete'] as $k=>$appid) {
				$_ENV['app']->alter_app_table($appid, 'REMOVE');
				unset($_POST['name'][$k]);
			}
			$this->load('cache');
			$_ENV['cache']->updatedata();
			$this->writelog('app_delete', 'appid='.implode(',', $_POST['delete']));
			$status = 2;

			$_CACHE = $this->cache('settings');

			//note 通知
			$this->load('note');
			$notedata = $this->db->fetch_all("SELECT appid, type, name, url, ip, charset, synlogin, extra, recvnote FROM ".UC_DBTABLEPRE."applications");
			$notedata = $this->_format_notedata($notedata);
			$this->base->cache['apps'];

			$notedata['UC_API'] = UC_API;
			$_ENV['note']->add('updateapps', '', $this->serialize($notedata));
			$_ENV['note']->send();
		}

		$a = getgpc('a');
		$applist = $_ENV['app']->get_apps();
		$this->view->assign('status', $status);
		$this->view->assign('a', $a);
		$this->view->assign('applist', $applist);

		$this->view->display('admin_app');
	}

	function onadd() {
		if(!$this->submitcheck()) {
			$md5ucfounderpw = md5(UC_FOUNDERPW);
			$this->view->assign('md5ucfounderpw', $md5ucfounderpw);

			$a = getgpc('a');
			$this->view->assign('a', $a);
			$typelist = array('UCHOME'=>'UCenter Home','XSPACE'=>'X-Space','DISCUZ'=>'Discuz!','SUPESITE'=>'SupeSite','SUPEV'=>'SupeV','ECSHOP'=>'ECShop','ECMALL'=>'ECMall','OTHER'=>$this->lang['other']);
			$this->view->assign('typelist', $typelist);
			$this->view->display('admin_app');
		} else {
			$type = getgpc('type', 'P');
			$name = getgpc('name', 'P');
			$url = getgpc('url', 'P');
			$ip = getgpc('ip', 'P');
			$authkey = getgpc('authkey', 'P');
			$synlogin = getgpc('synlogin', 'P');
			$recvnote = getgpc('recvnote', 'P');
			$tagtemplates = array();
			$tagtemplates['template'] = getgpc('tagtemplates', 'P');
			$tagfields = explode("\n", getgpc('tagfields', 'P'));
			foreach($tagfields as $field) {
				$field = trim($field);
				list($k, $v) = explode(',', $field);
				if($k) {
					$tagtemplates['fields'][$k] = $v;
				}
			}
			$tagtemplates = $this->serialize($tagtemplates, 1);

			if(!$_ENV['misc']->check_url($_POST['url'])) {
				$this->message('app_add_url_invalid', 'BACK');
			}
			if(!empty($_POST['ip']) && !$_ENV['misc']->check_ip($_POST['ip'])) {
				$this->message('app_add_ip_invalid', 'BACK');
			}
			$app = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."applications WHERE name='$name'");
			if($app) {
				$this->db->query("UPDATE ".UC_DBTABLEPRE."applications SET name='$name', url='$url', ip='$ip', authkey='$authkey', synlogin='$synlogin', type='$type', tagtemplates='$tagtemplates' WHERE appid='$app[appid]'");
				$appid = $app['appid'];
			} else {
				$this->db->query("INSERT INTO ".UC_DBTABLEPRE."applications SET name='$name', url='$url', ip='$ip', authkey='$authkey', synlogin='$synlogin', type='$type', recvnote='$recvnote', tagtemplates='$tagtemplates'");
				$appid = $this->db->insert_id();
			}

			//通知
			$this->load('note');
			$notedata = $this->db->fetch_all("SELECT appid, type, name, url, ip, charset, synlogin, extra, recvnote FROM ".UC_DBTABLEPRE."applications");
			$notedata = $this->_format_notedata($notedata);
			$notedata['UC_API'] = UC_API;
			$_ENV['note']->add('updateapps', '', $this->serialize($notedata));
			$_ENV['note']->send();

			$this->load('cache');
			$_ENV['cache']->updatedata('apps');

			$_ENV['app']->alter_app_table($appid, 'ADD');
			$this->writelog('app_add', "appid=$appid; appname=$_POST[name]");
			header("location: admin.php?m=app&a=detail&appid=$appid&addapp=yes");
		}
	}

	function onping() {
		$ip = getgpc('ip');
		$url = getgpc('url');
		$appid = intval(getgpc('appid'));
		$this->load('note');
		$url = $_ENV['note']->get_url_code('test', '', $appid);
		$status = $_ENV['app']->test_api($url, $ip);
		if($status == '1') {
			echo 'document.getElementById(\'status'.$appid.'\').innerHTML = "<img src=\'images/correct.gif\' border=\'0\' class=\'statimg\' \/><span class=\'green\'>'.$this->lang['app_connent_ok'].'</span>";';
		} else {
			echo 'document.getElementById(\'status'.$appid.'\').innerHTML = "<img src=\'images/error.gif\' border=\'0\' class=\'statimg\' \/><span class=\'red\'>'.$this->lang['app_connent_false'].'</span>";';
		}
	}

	//note public 应用详情
	function ondetail() {
		$appid = getgpc('appid');
		$updated = false;
		if($this->submitcheck()) {
			$type = getgpc('type', 'P');
			$name = getgpc('name', 'P');
			$url = getgpc('url', 'P');
			$ip = getgpc('ip', 'P');
			$authkey = getgpc('authkey', 'P');
			$synlogin = getgpc('synlogin', 'P');
			$recvnote = getgpc('recvnote', 'P');
			$extra = serialize(getgpc('supe', 'P'));
			$tagtemplates = array();
			$tagtemplates['template'] = MAGIC_QUOTES_GPC ? stripslashes(getgpc('tagtemplates', 'P')) : getgpc('tagtemplates', 'P');
			$tagfields = explode("\n", getgpc('tagfields', 'P'));
			foreach($tagfields as $field) {
				$field = trim($field);
				list($k, $v) = explode(',', $field);
				if($k) {
					$tagtemplates['fields'][$k] = $v;
				}
			}
			$tagtemplates = $this->serialize($tagtemplates, 1);

			$this->db->query("UPDATE ".UC_DBTABLEPRE."applications SET appid='$appid', name='$name', url='$url', type='$type', ip='$ip', authkey='$authkey', synlogin='$synlogin', recvnote='$recvnote', extra='$extra', tagtemplates='$tagtemplates' WHERE appid='$appid'");
			$updated = true;
			$this->load('cache');
			$_ENV['cache']->updatedata('apps');
			$this->writelog('app_edit', "appid=$appid");

			$this->load('note');
			$notedata = $this->db->fetch_all("SELECT appid, type, name, url, ip, charset, synlogin, extra, recvnote FROM ".UC_DBTABLEPRE."applications");
			$notedata = $this->_format_notedata($notedata);
			$notedata['UC_API'] = UC_API;
			$_ENV['note']->add('updateapps', '', $this->serialize($notedata));
			$_ENV['note']->send();
		}

		$app = $this->db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."applications WHERE appid='$appid'");

		$tagtemplates = $this->unserialize($app['tagtemplates']);
		$template = htmlspecialchars($tagtemplates['template']);
		$tmp = '';
		if(is_array($tagtemplates['fields'])) {
			foreach($tagtemplates['fields'] as $field => $memo) {
				$tmp .= $field.','.$memo."\n";
			}
		}
		$tagtemplates['fields'] = $tmp;
		$a = getgpc('a');
		$this->view->assign('a', $a);
		$this->view->assign('appid', $app['appid']);
		$this->view->assign('name', $app['name']);
		$this->view->assign('url', $app['url']);
		$this->view->assign('ip', $app['ip']);
		$this->view->assign('authkey', $app['authkey']);
		$synloginchecked = array($app['synlogin'] => 'checked="checked"');
		$recvnotechecked = array($app['recvnote'] => 'checked="checked"');
		$this->view->assign('synlogin', $synloginchecked);
		$this->view->assign('charset', $app['charset']);
		$this->view->assign('dbcharset', $app['dbcharset']);
		$this->view->assign('type', $app['type']);
		$this->view->assign('recvnotechecked', $recvnotechecked);
		$typelist = array('UCHOME'=>'UCenter Home','XSPACE'=>'X-Space','DISCUZ'=>'Discuz!','SUPESITE'=>'SupeSite','SUPEV'=>'SupeV','ECSHOP'=>'ECShop','ECMALL'=>'ECMall','OTHER'=>$this->lang['other']);
		$this->view->assign('typelist', $typelist);
		$this->view->assign('updated', $updated);
		$addapp = getgpc('addapp');
		$this->view->assign('addapp', $addapp);
		$this->view->assign('tagtemplates', $tagtemplates);

		$this->view->display('admin_app');
	}
	
	function _format_notedata($notedata) {
		$arr = array();
		foreach($notedata as $key=>$note) {
			$arr[$note['appid']] = $note;
		}	
		return $arr;
	}
}

?>