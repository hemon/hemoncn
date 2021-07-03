<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: link.php 7236 2008-04-30 01:20:22Z liguode $
*/

include_once('./common.php');

$refer = empty($_SGLOBAL['refer'])?"space.php?do=home":$_SGLOBAL['refer'];

if(empty($_GET['url'])) {
	showmessage('do_success', $refer, 0);
} else {
	$show_url = 'http://'.base64_decode($_GET['url']);
	$url = shtmlspecialchars_decode($show_url);
	
	if(!$_SCONFIG['linkguide']) {
		showmessage('do_success', $url, 0);//ֱ����ת
	}
	
	$url_ext = str_replace('http://www.', 'http://', $url);
	$now_url_ext = str_replace('http://www.', 'http://', getsiteurl());
	if(strexists($url_ext, $now_url_ext)) {
		showmessage('do_success', $url, 0);//��վ�ڵ�
	}
}

$space = array();
if($_SGLOBAL['supe_uid']) {
	$space = getspace($_SGLOBAL['supe_uid']);
}
if(empty($space)) {
	//�ο�ֱ����ת
	showmessage('do_success', $url, 0);
}

$show_url = sub_url($show_url, 70);

//ģ�����
include_once template("link");

//html������
function shtmlspecialchars_decode($text){
    return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
}


?>