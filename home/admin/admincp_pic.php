<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: admincp_pic.php 7293 2008-05-06 01:49:26Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//权限
if(!$allowmanage = checkperm('managealbum')) {
	$_GET['uid'] = $_SGLOBAL['supe_uid'];//只能操作本人的
}

if(submitcheck('deletesubmit')) {
	include_once(S_ROOT.'./source/function_delete.php');
	if(!empty($_POST['ids']) && deletepics($_POST['ids'])) {
		cpmessage('do_success', $_POST['mpurl']);
	} else {
		cpmessage('choose_to_delete_pictures', $_POST['mpurl']);
	}
}


$mpurl = 'admincp.php?ac=pic';

//处理搜索
$intkeys = array('albumid', 'uid');
$strkeys = array();
$randkeys = array(array('sstrtotime','dateline'));
$likekeys = array('filename', 'title');
$results = getwheres($intkeys, $strkeys, $randkeys, $likekeys);
$wherearr = $results['wherearr'];

$wheresql = empty($wherearr)?'1':implode(' AND ', $wherearr);
$mpurl .= '&'.implode('&', $results['urls']);

//排序
$orders = getorders(array('dateline', 'size'), 'picid DESC');
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
	$selectsql = 'picid';
} else {
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('pic')." WHERE $wheresql"), 0);
	$selectsql = '*';
}
$mpurl .= '&perpage='.$perpage;
$perpages = array($perpage => ' selected');

$list = array();
$multi = '';

$albums = $users = array();
if($count) {
	$albumids = $uids = array();
	$query = $_SGLOBAL['db']->query("SELECT $selectsql FROM ".tname('pic')." WHERE $wheresql $ordersql LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['pic'] = mkpicurl($value);
		if($value['albumid']) {
			$albumids[$value['albumid']] = $value['albumid'];
		}
		if($value['uid']) {
			$uids[$value['uid']] = $value['uid'];
		}
		$value['size'] = formatsize($value['size']);
		$list[] = $value;
	}
	//相册
	if($albumids) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('album')." WHERE albumid IN (".simplode($albumids).")");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$albums[$value['albumid']] = $value;
		}
	}
	//用户
	if($uids) {
		$uidstr = simplode($uids);
		if($uidstr == "'$_SGLOBAL[supe_uid]'") {
			$users[$_SGLOBAL['supe_uid']] = $_SGLOBAL['supe_username'];
		} else {
			$query = $_SGLOBAL['db']->query("SELECT uid, username FROM ".tname('space')." WHERE uid IN ($uidstr)");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				$users[$value['uid']] = $value['username'];
			}
		}
	}
	$multi = multi($count, $perpage, $page, $mpurl);
}

//显示分页
if($perpage > 100) {
	$count = count($list);
}

?>