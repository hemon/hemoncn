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
            js_alert("学号或者密码错误，提示：密码默认为教务处选课密码。");
        } else {
			$_SESSION['usr'] = $usr;
			$location = ( !empty($_REQUEST['refer']) ? $_REQUEST['refer']: 'http://my.hemon.cn');
			js_alert("登录成功！", "parent.location='$location'");
		}
        
        break;
    case 'logout':
        session_destroy();
        js_alert("安全退出！", "location='/index.php'");
        break;
	default:
		include('templates/login.html');
}

?>
