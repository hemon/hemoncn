<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space_index.php 7226 2008-04-29 07:35:18Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//�Ƿ����
$space['isfriend'] = $space['self'];
if($space['frienduid'] && in_array($_SGLOBAL['supe_uid'], explode(',', $space['frienduid']))) {
	$space['isfriend'] = 1;//�Ǻ���
}

//��������
//�Ա�
if(ckprivacy('profile')) {//��˽
	$space['showprofile'] = 1;
	$space['sex'] = $space['sex']=='1'?'<a href="network.php?ac=space&sex=1&searchmode=1">'.mlang('man').'</a>':($space['sex']=='2'?'<a href="network.php?ac=space&sex=2&searchmode=1">'.mlang('woman').'</a>':'');
	$space['birthday'] = ($space['birthyear']?"$space[birthyear]".mlang('year'):'').($space['birthmonth']?"$space[birthmonth]".mlang('month'):'').($space['birthday']?"$space[birthday]".mlang('day'):'');
	$space['marry'] = $space['marry']=='1'?'<a href="network.php?ac=space&marry=1&searchmode=1">'.mlang('unmarried').'</a>':($space['marry']=='2'?'<a href="network.php?ac=space&marry=2&searchmode=1">'.mlang('married').'</a>':'');
	$space['birth'] = trim(($space['birthprovince']?"<a href=\"network.php?ac=space&birthprovince=".rawurlencode($space['birthprovince'])."&searchmode=1\">$space[birthprovince]</a>":'').($space['birthcity']?" <a href=\"network.php?ac=space&birthcity=".rawurlencode($space['birthcity'])."&searchmode=1\">$space[birthcity]</a>":''));
	$space['reside'] = trim(($space['resideprovince']?"<a href=\"network.php?ac=space&resideprovince=".rawurlencode($space['resideprovince'])."&searchmode=1\">$space[resideprovince]</a>":'').($space['residecity']?" <a href=\"network.php?ac=space&residecity=".rawurlencode($space['residecity'])."&searchmode=1\">$space[residecity]</a>":''));
	$space['qq'] = empty($space['qq'])?'':"<a target=\"_blank\" href=\"http://wpa.qq.com/msgrd?V=1&Uin=$space[qq]&Site=$space[username]&Menu=yes\">$space[qq]</a>";
	$space['email'] = empty($space['email'])?'':"<a href=\"mailto:$space[email]\">$space[email]</a>";
	//�Զ���
	@include_once(S_ROOT.'./data/data_profilefield.php');
	$fields = empty($_SGLOBAL['profilefield'])?array():$_SGLOBAL['profilefield'];
} else {
	$space['showprofile'] = 0;
}
$space['creditstar'] = getstar($space['credit']);

//����
if($space['domain'] && $_SCONFIG['allowdomain'] && $_SCONFIG['domainroot']) {
	$space['domainurl'] = 'http://'.$space['domain'].'.'.$_SCONFIG['domainroot'];
} else {
	if($_SCONFIG['allowrewrite']) {
		$space['domainurl'] = getsiteurl().$space[uid];
	} else {
		$space['domainurl'] = getsiteurl()."?$space[uid]";
	}
}

//���˶�̬
$feedlist = array();
if(ckprivacy('feed')) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('feed')." WHERE uid='$space[uid]' ORDER BY dateline DESC LIMIT 0,10");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(ckfriend($value)) {
			$value = mkfeed($value);
			$feedlist[] = $value;
		}
	}
	$feednum = count($feedlist);
}

//���˷���
$sharelist = array();
if(ckprivacy('share')) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('share')." WHERE uid='$space[uid]' ORDER BY dateline DESC LIMIT 0,10");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value = mkshare($value);
		$sharelist[] = $value;
	}
}

//�����б�
$friendlist = array();
if(ckprivacy('friend')) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('friend')." WHERE uid='$space[uid]' AND status='1' LIMIT 0,6");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$friendlist[] = $value;
	}
	if($friendlist && empty($space['friendnum'])) {
		//���º��ѻ���
		include_once(S_ROOT.'./source/function_cp.php');
		friend_cache($space['uid']);
	}
}

//���㲩��
$doinglist = array();
if(ckprivacy('doing')) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('doing')." WHERE uid='$space[uid]' ORDER BY dateline DESC LIMIT 0,5");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$doinglist[] = $value;
	}
}

//��־
$bloglist = array();
if(ckprivacy('blog')) {
	$query = $_SGLOBAL['db']->query("SELECT b.uid, b.blogid, b.subject, b.dateline, b.pic, b.picflag, b.viewnum, b.replynum, b.friend, b.password, bf.message, bf.target_ids
		FROM ".tname('blog')." b
		LEFT JOIN ".tname('blogfield')." bf ON bf.blogid=b.blogid
		WHERE b.uid='$space[uid]'
		ORDER BY b.dateline DESC LIMIT 0,5");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(ckfriend($value)) {
			$value['pic'] = mkpicurl($value);
			$value['message'] = $value['friend']==4?lang('password_message'):getstr($value['message'], 150, 0, 0, 0, 0, -1);
			$bloglist[] = $value;
		}
	}
	$blognum = count($bloglist);
}

//���
$albumlist = array();
if(ckprivacy('album')) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('album')." WHERE uid='$space[uid]' ORDER BY updatetime DESC LIMIT 0,4");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(ckfriend($value)) {
			$value['pic'] = mkpicurl($value);
			$albumlist[] = $value;
		}
	}
}

//����ѡ��
$mtaglist = array();
if(ckprivacy('mtag')) {
	$query = $_SGLOBAL['db']->query("SELECT field.* FROM ".tname('tagspace')." main 
		LEFT JOIN ".tname('mtag')." field ON field.tagid=main.tagid 
		WHERE main.uid='$space[uid]' LIMIT 0, 100");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$mtaglist[$value['fieldid']][] = $value;
	}
	if($mtaglist) {
		@include_once(S_ROOT.'./data/data_profield.php');
	}
}

//����
$threadlist = array();
if(ckprivacy('thread')) {
	$query = $_SGLOBAL['db']->query("SELECT main.* FROM ".tname('thread')." main 
		WHERE main.uid='$space[uid]' 
		ORDER BY main.lastpost DESC LIMIT 0,5");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$threadlist[] = $value;
	}
}

//���԰�
$walllist = array();
if(ckprivacy('wall')) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE id='$space[uid]' AND idtype='uid' ORDER BY dateline DESC LIMIT 0,5");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['message'] = strlen($value['message'])>500?getstr($value['message'], 500, 0, 0, 0, 0, -1).' ...':$value['message'];
		$walllist[] = $value;
	}
}

//�Ƿ�����
$isonline = 0;
$isonline = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT lastactivity FROM ".tname('session')." WHERE uid='$space[uid]'"), 0);
if($isonline) $isonline = sgmdate('H:i:s', $isonline, 1);

//�ڲ�ͬӦ�û״̬
$applist = array();
if($_SGLOBAL['app']) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('app')." WHERE uid='$space[uid]'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$app = $_SGLOBAL['app'][$value['appid']];
		if(empty($app)) {
			include_once(S_ROOT.'./source/function_cache.php');
			app_cache();
			$app = $_SGLOBAL['app'][$value['appid']];
			if(empty($app)) continue;
		}
		$value['appname'] = $app['name'];
		switch ($app['type']) {
			case 'SUPEV':
				$value['url'] = "vspace.php?mid=$value[uid]";
				break;
			case 'XSPACE':
			case 'SUPESITE':
				$value['url'] = "index.php?uid-$value[uid]";
				break;
			case 'ECMALL':
				$value['url'] = "";
				break;
			case 'ECSHOP':
				$value['url'] = "";
				break;
			case 'DISCUZ':
			case 'UCHOME':
			default:
				$value['url'] = "space.php?uid=$value[uid]";
				break;
		}
		$value['url'] = $app['url'].'/'.$value['url'];
		$applist[] = $value;
	}
}

//���
$theme = empty($_GET['theme'])?'':preg_replace("/[^0-9a-z]/i", '', $_GET['theme']);
if($theme == 'uchomedefault') {
	$space['theme'] = $space['css'] = '';
} elseif($theme) {
	$cssfile = S_ROOT.'./theme/'.$theme.'/style.css';
	if(file_exists($cssfile)) {
		$space['theme'] = $theme;
		$space['css'] = '';
	}
} else {
	if(!$space['self'] && $_SGLOBAL['member']['nocss']) {
		$space['theme'] = $space['css'] = '';
	}
}

//����ÿͼ�¼
if(!$space['self'] && $_SGLOBAL['supe_uid']) {
	$setarr = array(
		'uid' => $space['uid'],
		'vuid' => $_SGLOBAL['supe_uid'],
		'vusername' => $_SGLOBAL['supe_username'],
		'dateline' => $_SGLOBAL['timestamp']
	);
	inserttable('visitor', $setarr, 0, true);
}

//����ÿ��б�
$visitorlist = array();
if($space['self']) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('visitor')." WHERE uid='$space[uid]' ORDER BY dateline DESC LIMIT 0,6");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$visitorlist[] = $value;
	}
}

include_once template("space_index");

?>