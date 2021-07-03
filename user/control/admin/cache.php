<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: cache.php 12135 2008-01-14 03:47:01Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends adminbase {

	function control() {
		$this->adminbase();
		$this->check_priv();
		if(!$this->user['isfounder'] && !$this->user['allowadmincache']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load('cache');
	}

	function onupdate() {
		$updated = false;
		if($this->submitcheck('submit')) {
			$type = getgpc('type', 'P');
			if(!is_array($type) || in_array('data', $type)) {
				$_ENV['cache']->updatedata();
			}
			if(!is_array($type) || in_array('tpl', $type)) {
				$_ENV['cache']->updatetpl();
			}
			$updated = true;
		}
		$this->view->assign('updated', $updated);
		$this->view->display('admin_cache');
	}
}

?>