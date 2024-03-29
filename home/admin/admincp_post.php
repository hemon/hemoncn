<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: admincp_post.php 7293 2008-05-06 01:49:26Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

if(submitcheck('deletesubmit')) {
	include_once(S_ROOT.'./source/function_delete.php');
	if(!empty($_POST['ids']) && deleteposts($_POST['ids'])) {
		cpmessage('do_success', $_POST['mpurl']);
	} else {
		cpmessage('choose_to_delete_the_topic', $_POST['mpurl']);
	}
}

//权限
$allowmanage = 0;
if(checkperm('managethread')) {
	$allowmanage = 1;
} else {
	//吧主
	$_GET['tagid'] = empty($_GET['tagid'])?'':intval($_GET['tagid']);
	if($_GET['tagid']) {
		$moderator = getcount('mtag', array('tagid'=>$_GET['tagid']), 'moderator');
		if(ismoderator($moderator)) {
			$allowmanage = 1;
		}
	}
}
if(!$allowmanage) {
	$_GET['uid'] = $_SGLOBAL['supe_uid'];//只能操作本人的
	$_GET['username'] = '';
}

$mpurl = 'admincp.php?ac=post';

//处理搜索
$intkeys = array('pid','uid', 'tagid', 'tid', 'isthread');
$strkeys = array('username', 'ip');
$randkeys = array(array('sstrtotime','dateline'));
$likekeys = array('message');
$results = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
$wherearr = $results['wherearr'];

$wheresql = empty($wherearr)?'1':implode(' AND ', $wherearr);
$mpurl .= '&'.implode('&', $results['urls']);

//排序
$orders = getorders(array('dateline'), 'pid DESC');
$ordersql = $orders['sql'];
if($orders['urls']) $mpurl .= '&'.implode('&', $orders['urls']);
$orderby = array($_GET['orderby']=>' selected');
$ordersc = array($_GET['ordersc']=>' selected');

//显示分页
$perpage = empty($_GET['perpage'])?0:intval($_GET['perpage']);
if(!in_array($perpage, array(20,50,100,1000))) $perpage = 20;

$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page = 1;
$start = ($page-1)*$perpage;
//检查开始数
ckstart($start, $perpage);

//显示分页
if($perpage > 100) {
	$count = 1;
	$selectsql = 'pid';
} else {
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('post')." WHERE $wheresql"), 0);
	$selectsql = '*';
}
$mpurl .= '&perpage='.$perpage;
$perpages = array($perpage => ' selected');

$list = array();
$multi = '';

$threads = $tids = array();
$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('post')." WHERE $wheresql"), 0);
if($count) {
	$query = $_SGLOBAL['db']->query("SELECT $selectsql FROM ".tname('post')." WHERE $wheresql $ordersql LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(!empty($value['message']) && empty($_GET['pid'])) {
			$value['message'] = getstr($value['message'], 150);
		}
		if(!empty($value['tid'])) $tids[$value['tid']] = $value['tid'];
		$list[] = $value;
	}
	if($tids) {
		$query = $_SGLOBAL['db']->query("SELECT tid, subject FROM ".tname('thread')." WHERE tid IN (".simplode($tids).")");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$threads[$value['tid']] = $value['subject'];
		}
	}
	$multi = multi($count, $perpage, $page, $mpurl);
}

//显示分页
if($perpage > 100) {
	$count = count($list);
}

?>