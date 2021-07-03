<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: function_space.php 7058 2008-04-14 05:49:19Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//��ͨ�ռ�
function space_open($uid, $username, $gid=0, $email='') {
	global $_SGLOBAL;

	if(empty($uid) || empty($username)) return array();
	
	$space = array(
		'uid' => $uid,
		'username' => $username,
		'dateline' => $_SGLOBAL['timestamp'],
		'updatetime' => $_SGLOBAL['timestamp'],
		'groupid' => $gid
	);
	inserttable('space', $space, 0, true);
	inserttable('spacefield', array('uid'=>$uid, 'email'=>$email), 0, true);

	return $space;
}

//ѡ����Ϣ
function getmtag($id, $ckmanage=0) {
	global $_SGLOBAL;
	
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('mtag')." WHERE tagid='$id'");
	if(!$mtag = $_SGLOBAL['db']->fetch_array($query)) {
		showmessage('designated_election_it_does_not_exist');
	}
	
	//����
	$mtag['title'] = $_SGLOBAL['profield'][$mtag['fieldid']]['title'];
	if(empty($mtag['pic'])) {
		$mtag['pic'] = 'image/nologo.jpg';
	}

	//�Ƿ��Ա
	$query = $_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('tagspace')." WHERE tagid='$id' AND uid='$_SGLOBAL[supe_uid]' LIMIT 1");
	$mtag['ismember'] = $_SGLOBAL['db']->result($query, 0);
	
	//Ȩ��
	$mtag['allowmanage'] = 0;
	if($ckmanage) {
		$mtag['isadmin'] = $mtag['allowmanage'] = checkperm('managemtag');
		if(!$mtag['allowmanage'] && $mtag['ismember']) {
			//�ж��Ƿ����
			if($_SGLOBAL['supe_username'] && !empty($mtag['moderator'])) {
				$marr = explode("\t", $mtag['moderator']);
				if(in_array($_SGLOBAL['supe_username'], $marr)) {
					$mtag['allowmanage'] = 1;
					if($marr[0] == $_SGLOBAL['supe_username']) {
						$mtag['isadmin'] = 1;
					}
				}
				//���ǻ�Ա��ȴ�ǰ���
				if(empty($mtag['ismember']) && $mtag['allowmanage']) {
					$setusername = preg_quote($_SGLOBAL['supe_username'], '/');
					$setmoderator = preg_replace("/(\t$setusername|$setusername\t|$setusername)/", '', $mtag['moderator']);
					updatetable('mtag', array('moderator'=> addslashes($setmoderator), array('tagid'=>$mtag['tagid'])));
					$mtag['allowmanage'] = 0;
				}
			}
		}
	}
	
	return $mtag;
}

?>