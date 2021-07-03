<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: pm.php 12126 2008-01-11 09:40:32Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends base {

	function control() {
		$this->base();
		$this->load('user');
		$this->load('pm');
	}

	function oncheck_newpm() {
		$this->init_input();
		$this->user['uid'] = intval($this->input('uid'));
		exit($_ENV['pm']->check_newpm($this->user['uid']));
	}

	function onsendpm() {
		$this->init_input();
		$fromuid = $this->input('fromuid');
		$msgto = $this->input('msgto');
		$subject = $this->input('subject');
		$message = $this->input('message');
		$replypmid = $this->input('replypmid');
		$isusername = $this->input('isusername');
		if($fromuid) {
			$user = $_ENV['user']->get_user_by_uid($fromuid);
			if(!$user) {
				exit("0");
			}
			$this->user['uid'] = $user['uid'];
			$this->user['username'] = $user['username'];
		} else {
			$this->user['uid'] = 0;
			$this->user['username'] = '';
		}
		if($replypmid) {
			$isusername = 1;
			$pms = $_ENV['pm']->get_pm_by_pmid($this->user['uid'], $replypmid);
			if($pms[0]['msgfromid'] == $this->user['uid']) {
				$user = $_ENV['user']->get_user_by_uid($pms[0]['msgtoid']);
				$msgto = $user['username'];
			} else {
				$msgto = $pms[0]['msgfrom'];
			}
		}

		$msgto = array_unique(explode(',', $msgto));
		$isusername && $msgto = $_ENV['user']->name2id($msgto);
		$blackls = $_ENV['pm']->get_blackls($this->user['uid'], $msgto);
		$lastpmid = 0;
		foreach($msgto as $uid) {
			if(!$fromuid || !in_array('{ALL}', $blackls[$uid])) {
				$blackls[$uid] = $_ENV['user']->name2id($blackls[$uid]);
				if(!$fromuid || isset($blackls[$uid]) && !in_array($this->user['uid'], $blackls[$uid])) {
					$lastpmid = $_ENV['pm']->sendpm($subject, $message, $this->user, $uid, 0, 0, $replypmid);
				}
			}
		}
		exit("$lastpmid");
	}

	function ondelete() {
		$this->init_input();
		$this->user['uid'] = intval($this->input('uid'));
		$id = $_ENV['pm']->deletepm($this->user['uid'], $this->input('folder'), '', $this->input('pmids'));
		exit("$id");
	}

	function onignore() {
		$this->init_input();
		$this->user['uid'] = intval($this->input('uid'));
		$_ENV['pm']->set_ignore($this->user['uid']);
	}

 	function onls() {
 		$this->init_input();
 		$pagesize = $this->input('pagesize');
 		$folder = $this->input('folder');
 		$filter = $this->input('filter');
 		$page = $this->input('page');
 		$folder = in_array($folder, array('newbox', 'inbox', 'outbox')) ? $folder : 'inbox';
 		$filter = $filter ? (in_array($filter, array('newpm', 'systempm', 'announcepm')) ? $filter : '') : '';
 		$msglen = $this->input('msglen');
 		$this->user['uid'] = intval($this->input('uid'));
 		$pmnum = $_ENV['pm']->get_num($this->user['uid'], $folder, $filter);
 		if($pagesize > 0) {
	 		$pms = $_ENV['pm']->get_pm_list($this->user['uid'], $pmnum, $folder, $filter, $page, $pagesize);
	 		if(is_array($pms) && !empty($pms)) {
				foreach($pms as $key => $pm) {
					if($msglen) {
						$pms[$key]['message']{0} == "\t" && $pms[$key]['message'] = substr($pms[$key]['message'], 1);
						$pms[$key]['message'] = $_ENV['pm']->removecode($pms[$key]['message'], $msglen);
					} else {
						unset($pms[$key]['message']);
					}
					$pms[$key]['dateline'] = $pms[$key]['dbdateline'];
					unset($pms[$key]['dbdateline'], $pms[$key]['folder']);
				}
			}
			$result['data'] = $pms;
		}
		$result['count'] = $pmnum;
 		exit($this->serialize($result, 1));
 	}

 	function onviewnode() {
  		$this->init_input();
  		$this->user['uid'] = intval($this->input('uid'));
 		$pmid = $_ENV['pm']->pmintval($this->input('pmid'));
 		$type = $this->input('type');
 		$pm = $_ENV['pm']->get_pmnode_by_pmid($this->user['uid'], $pmid, $type);
 		if($pm) {
	 	 	require_once UC_ROOT.'lib/uccode.class.php';
			$this->uccode = new uccode();
			$pm['message'] = $this->uccode->complie($pm['message']);
			exit($this->serialize($pm, 1));
		}
 	}

 	function onview() {
 		$this->init_input();
 		$pmid = $this->input('pmid');
 		$this->user['uid'] = intval($this->input('uid'));
 		$pms = $_ENV['pm']->get_pm_by_pmid($this->user['uid'], $pmid);
 	 	require_once UC_ROOT.'lib/uccode.class.php';
		$this->uccode = new uccode();
		$this->uccode->lang = &$this->lang;
		$status = FALSE;
		foreach($pms as $key => $pm) {
			$pms[$key]['message'] = $this->uccode->complie($pms[$key]['message']);
			!$status && $status = $pm['msgtoid'] && $pm['new'];
		}
		$status && $_ENV['pm']->set_pm_status($this->user['uid'], $pmid);
		exit($this->serialize($pms, 1));
 	}

  	function onblackls_get() {
  		$this->init_input();
 		$this->user['uid'] = intval($this->input('uid'));
 		exit($_ENV['pm']->get_blackls($this->user['uid']));
 	}

 	function onblackls_set() {
 		$this->init_input();
 		$this->user['uid'] = intval($this->input('uid'));
 		$blackls = $this->input('blackls');
 		exit($_ENV['pm']->set_blackls($this->user['uid'], $blackls));
 	}

}

?>