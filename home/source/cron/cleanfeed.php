<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cleanfeed.php 6565 2008-03-14 09:26:09Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//����feed
if($_SCONFIG['feedday'] < 15) $_SCONFIG['feedday'] = 15;//����15��
$deltime = $_SGLOBAL['timestamp'] - $_SCONFIG['feedday']*3600*24;

//ִ��
$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE dateline < '$deltime'");

?>