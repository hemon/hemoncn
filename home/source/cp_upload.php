<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_upload.php 6963 2008-04-03 02:37:37Z zhengqingpeng $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$albumid = empty($_GET['albumid'])?0:intval($_GET['albumid']);

if(submitcheck('albumsubmit')) {
	//�������
	if($_POST['albumop'] == 'creatalbum') {
		$_POST['albumname'] = empty($_POST['albumname'])?'':getstr($_POST['albumname'], 50, 1, 1);
		if(empty($_POST['albumname'])) $_POST['albumname'] = gmdate('Ymd');
		
		$_POST['friend'] = intval($_POST['friend']);
		
		//��˽
		$_POST['target_ids'] = '';
		if($_POST['friend'] == 2) {
			//�ض�����
			$uids = array();
			$names = empty($_POST['target_names'])?array():explode(' ', str_replace(array(lang('tab_space'), "\r\n", "\n", "\r"), ' ', $_POST['target_names']));
			if($names) {
				$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." WHERE username IN (".simplode($names).")");
				while ($value = $_SGLOBAL['db']->fetch_array($query)) {
					$uids[] = $value['uid'];
				}
			}
			if(empty($uids)) {
				$_POST['friend'] = 3;//���Լ��ɼ�
			} else {
				$_POST['target_ids'] = implode(',', $uids);
			}
		} elseif($_POST['friend'] == 4) {
			//����
			$_POST['password'] = trim($_POST['password']);
			if($_POST['password'] == '') $_POST['friend'] = 0;//����
		}
		if($_POST['friend'] !== 2) {
			$_POST['target_ids'] = '';
		}
		if($_POST['friend'] !== 4) {
			$_POST['password'] == '';
		}
		
		//�������
		$setarr = array();
		$setarr['albumname'] = $_POST['albumname'];
		$setarr['uid'] = $_SGLOBAL['supe_uid'];
		$setarr['username'] = $_SGLOBAL['supe_username'];
		$setarr['dateline'] = $setarr['updatetime'] = $_SGLOBAL['timestamp'];
		$setarr['friend'] = $_POST['friend'];
		$setarr['password'] = $_POST['password'];
		$setarr['target_ids'] = $_POST['target_ids'];
		
		$albumid = inserttable('album', $setarr, 1);
		
		//�¼�
		if($_POST['friend'] < 3) {
			$fs = array();
			$fs['icon'] = 'album';
		
			$fs['title_template'] = '{actor} '.lang('create_a_new_album').' {album}';
			$fs['title_data'] = array('album'=>"<a href=\"space.php?uid=$_SGLOBAL[supe_uid]&do=album&id=$albumid\">$setarr[albumname]</a>");
			$fs['body_template'] = '';
			$fs['body_data'] = array();
			$fs['body_general'] = '';
	
			if(ckprivacy('album', 1)) {
				feed_add($fs['icon'], $fs['title_template'], $fs['title_data'], $fs['body_template'], $fs['body_data'], $fs['body_general'], array(), array(), $setarr['target_ids'], $setarr['friend']);
			}
		}
	} else {
		$albumid = intval($_POST['albumid']);
	}
	echo "<script>";
	echo "parent.no_insert = 1;";
	echo "parent.albumid = $albumid;";
	echo "parent.start_upload();";
	echo "</script>";
	exit();
	
} elseif(submitcheck('uploadsubmit')) {
	
	//��ʼ�ϴ�
	$albumid = $picid = 0;

	if(!checkperm('allowupload')) {
		echo "<script>";
		echo "alert(\"".lang('not_allow_upload')."\")";
		echo "</script>";
		exit();
	}

	//�ϴ�
	$uploadfiles = pic_save($_FILES['attach'], $_POST['albumid'], $_POST['pic_title']);
	
	if($uploadfiles && is_array($uploadfiles)) {
		$albumid = $uploadfiles['albumid'];
		$picid = $uploadfiles['picid'];
		$uploadStat = 1;
	} else {
		$uploadStat = $uploadfiles;
	}
	echo "<script>";
	echo "parent.albumid = $albumid;";
	echo "parent.uploadStat = '$uploadStat';";
	echo "parent.picid = $picid;";
	echo "parent.upload();";
	echo "</script>";
	exit();
	
} elseif(submitcheck('viewAlbumid')) {
	//�ϴ���ɷ���feed
	album_feed($_POST['opalbumid']);
	
	showmessage('upload_images_completed', "space.php?uid=$_SGLOBAL[supe_uid]&do=album&id=".(empty($_POST['opalbumid'])?-1:$_POST['opalbumid']), 0);
}

if(!checkperm('allowupload')) {
	showmessage('no_privilege');
}
	
$siteurl = getsiteurl();

//��ȡ���
$albums = getalbums($_SGLOBAL['supe_uid']);

//����
$actives = ($_GET['op'] == 'flash' || $_GET['op'] == 'cam')?array($_GET['op']=>' class="active"'):array('js'=>' class="active"');

//�ռ��С
$maxattachsize = checkperm('maxattachsize');
if(!empty($maxattachsize)) {
	$maxattachsize = $maxattachsize + $space['addsize'];//����ռ�
	$maxattachsize = formatsize($maxattachsize);
}
$space['attachsize'] = formatsize($space['attachsize']);

//������
$groups = getfriendgroup();
	
//ģ��
include_once template("cp_upload");

?>