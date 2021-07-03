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
		$this->load('note');
		$this->load('misc');
	}

	function onrequest() {
		$uid = intval($this->input('uid'));
		$from = intval($this->input('from'));
		$to = intval($this->input('to'));
		$toappid = intval($this->input('toappid'));
		$amount = intval($this->input('amount'));
		$status = 0;
		if(isset($this->settings['creditexchange'][$this->app['appid'].'_'.$from.'_'.$toappid.'_'.$to])) {
			$this->load('app');
			$toapp = $_ENV['app']->get_apps('ip', "appid = '$toappid'");
			$url = $_ENV['note']->get_url_code('updatecredit', "uid=$uid&credit=$to&amount=$amount", $toappid);
			$status = trim($_ENV['misc']->dfopen($url, 0, '', '', 1, $toapp['ip'], UC_NOTE_TIMEOUT));
		}
		echo $status ? 1 : 0;
		exit;
	}
}

?>