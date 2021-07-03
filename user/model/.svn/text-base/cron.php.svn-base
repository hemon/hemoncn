<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: cron.php 12126 2008-01-11 09:40:32Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class cronmodel {

	var $db;
	var $base;

	function cronmodel(&$base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function note_delete_user() {
		//
	}

	function note_delete_pm() {
		//
     		$data = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."badwords");
		return $data;
	}

}

?>