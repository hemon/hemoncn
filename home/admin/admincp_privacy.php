<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: admincp_privacy.php 6740 2008-03-25 02:12:38Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//权限
if(!checkperm('manageconfig')) {
	cpmessage('no_authority_management_operation');
}

if(submitcheck('thevaluesubmit')) {

	$setarr = array();
	
	//ip允许
	$_POST['config']['ipaccess'] = trim(preg_replace("/(\s*(\r\n|\n\r|\n|\r)\s*)/", "\r\n", $_POST['config']['ipaccess']));
	if(!ipaccess($_POST['config']['ipaccess'])) {
		cpmessage('ip_is_not_allowed_to_visit_the_area', '', 1, array($_SGLOBAL[onlineip]));
	}

	//ip禁止
	$_POST['config']['ipbanned'] = trim(preg_replace("/(\s*(\r\n|\n\r|\n|\r)\s*)/", "\r\n", $_POST['config']['ipbanned']));
	if(ipbanned($_POST['config']['ipbanned'])) {
		cpmessage('the_prohibition_of_the_visit_within_the_framework_of_ip', '', 1, array($_SGLOBAL[onlineip]));
	}
	
	foreach ($_POST['config'] as $var => $value) {
		$value = trim($value);
		if(!isset($_SCONFIG[$var]) || $_SCONFIG[$var] != $value) {
			$setarr[] = "('$var', '$value')";
		}
	}
	
	//隐私
	$privacys = array();
	foreach ($_POST['privacy']['view'] as $key => $value) {
		$privacys['view'][$key] = intval($value);
	}
	//发送动态
	$privacys['feed'] = array();
	foreach ($_POST['privacy']['feed'] as $key => $value) {
		$privacys['feed'][$key] = 1;
	}
	$setarr[] = "('privacy', '".addslashes(serialize($privacys))."')";
	
	if($setarr) {
		$_SGLOBAL['db']->query("REPLACE INTO ".tname('config')." (var, datavalue) VALUES ".implode(',', $setarr));
	}

	//更新缓存
	include_once(S_ROOT.'./source/function_cache.php');
	config_cache();;

	cpmessage('do_success', 'admincp.php?ac=privacy');
}

$configs = array();
$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('config'));
while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($value['var'] == 'privacy') {
			$value['datavalue'] = empty($value['datavalue'])?array():unserialize($value['datavalue']);
		} else {
			$value['datavalue'] = shtmlspecialchars($value['datavalue']);
		}
	$configs[$value['var']] = $value['datavalue'];
}

//页面选择
$sels = array();
foreach ($configs['privacy']['view'] as $key => $value) {
	$sels['view'][$key] = array($value => ' selected');
}
foreach ($configs['privacy']['feed'] as $key => $value) {
	$sels['feed'][$key] = ' checked';
}

?>