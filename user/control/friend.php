<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: friend.php 12126 2008-01-11 09:40:32Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends base {

	function control() {
		$this->base();
		$this->init_input();
		$this->load('friend');
	}

	function ondelete() {
		$uid = intval($this->input('uid'));
		$friendids = $this->input('friendids');
		$id = $_ENV['friend']->delete($uid, $friendids);
		exit("$id");
	}

	function onadd() {
		$uid = intval($this->input('uid'));
		$friendid = $this->input('friendid');
		$comment = $this->input('comment');
		$id = $_ENV['friend']->add($uid, $friendid, $comment);
		exit("$id");
	}

	function ontotalnum() {
		$uid = intval($this->input('uid'));
		$direction = intval($this->input('direction'));
		$totalnum = $_ENV['friend']->get_totalnum_by_uid($uid, $direction);
		exit("$totalnum");
	}

	function onls() {
		$uid = intval($this->input('uid'));
		$page = intval($this->input('page'));
		$pagesize = intval($this->input('pagesize'));
		$totalnum = intval($this->input('totalnum'));
		$direction = intval($this->input('direction'));
		$pagesize = $pagesize ? $pagesize : UC_PPP;
		$totalnum = $totalnum ? $totalnum : $_ENV['friend']->get_totalnum_by_uid($uid);
		$data = $_ENV['friend']->get_list($uid, $page, $pagesize, $totalnum, $direction);
		exit($this->serialize($data));
	}
}

?>