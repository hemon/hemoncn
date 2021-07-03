<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: domain.php 12126 2008-01-11 09:40:32Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends base {

	function control() {
		$this->base();
		$this->init_input();
		$this->load('domain');
	}

	function onls() {
		$domainlist = $_ENV['domain']->get_list(1, 99999, 99999);
		exit($this->serialize($domainlist));
	}
}

?>