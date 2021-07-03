<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: tag.php 12126 2008-01-11 09:40:32Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends base {

	function control() {
		$seccode = rand(100000, 999999);
		$this->setcookie('uc_secc', $this->authcode($seccode."\t".time(), 'ENCODE'));

		@header("Expires: -1");
		@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");

		include_once UC_ROOT.'lib/seccode.class.php';
		$code = new seccode();
		$code->code = $seccode;
		$code->type = 0;
		$code->width = 70;
		$code->height = 21;
		$code->background = 0;
		$code->adulterate = 1;
		$code->ttf = 1;
		$code->angle = 0;
		$code->color = 1;
		$code->size = 0;
		$code->shadow = 1;
		$code->animator = 0;
		$code->fontpath = UC_ROOT.'images/fonts/';
		$code->datapath = UC_ROOT.'images/';
		$code->includepath = '';
		$code->display();
	}

}

?>