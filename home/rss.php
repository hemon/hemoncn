<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: rss.php 6703 2008-03-24 02:37:52Z liguode $
*/

include_once('./common.php');

@header("Content-type: application/xml");

$pagenum = 10;
$tag = '<?';
$rssdateformat = 'D, d M Y H:i:s T';

$siteurl = getsiteurl();
$uid = empty($_GET['uid'])?0:intval($_GET['uid']);
$list = array();

if(!empty($uid)) {
	$space = getspace($uid);
}
if(empty($space)) {
	//站点更新rss
	$space['username'] = $_SCONFIG['sitename'];
	$space['spacename'] = $_SCONFIG['sitename'];
	$space['email'] = $_SCONFIG['adminemail'];
	$space['space_url'] = $siteurl;
	$space['lastupdate'] = sgmdate($rssdateformat);
	$space['privacy']['blog'] = 1;
} else {
	$space['username'] = $space['username'].'@'.$_SCONFIG['sitename'];
	$space['space_url'] = $siteurl."space.php?uid=$space[uid]";
	$space['lastupdate'] = sgmdate($rssdateformat, $space['lastupdate']);
}

//10篇最新日志
$uidsql = empty($space['uid'])?'':"WHERE b.uid='$space[uid]'";
$query = $_SGLOBAL['db']->query("SELECT bf.message, b.blogid, b.subject, b.replynum, b.viewnum, b.uid, b.username, b.friend, b.dateline, b.pic, b.picflag
	FROM ".tname('blog')." b
	LEFT JOIN ".tname('blogfield')." bf ON bf.blogid=b.blogid $uidsql
	ORDER BY dateline DESC
	LIMIT 0,$pagenum");
while ($value = $_SGLOBAL['db']->fetch_array($query)) {
	if($value['friend'] || !empty($space['privacy']['blog'])) {
		$value['message'] = '';
	} else {
		$value['message'] = getstr($value['message'], 300, 0, 0, 0, 0, -1);
		$value['pic'] = mkpicurl($value);
		if($value['pic']) {
			$value['message'] .= "<br /><img src=\"$value[pic]\">";
		}
	}
	$value['dateline'] = sgmdate($rssdateformat, $value['dateline']);
	$list[] = $value;
}

include template('space_rss');

?>