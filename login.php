<?php
require_once 'config.php';
require_once 'mod/login.func.php';

$action = $_REQUEST['action'];

switch ($action) {
    case 'login':
        $sid = $_REQUEST['sid'];
        $password = $_REQUEST['password'];
        $usr = login($sid, $password);
		
        if( empty($usr) ){
            js_alert("ѧ�Ż������������ʾ������Ĭ��Ϊ����ѡ�����롣");
        } else {
			$_SESSION['usr'] = $usr;
			$location = ( !empty($_REQUEST['refer']) ? $_REQUEST['refer']: 'http://my.hemon.cn');
			js_alert("��¼�ɹ���", "parent.location='$location'");
		}
        
        break;
    case 'logout':
        session_destroy();
        js_alert("��ȫ�˳���", "location='/index.php'");
        break;
	default:
		include('templates/login.html');
}

?>
