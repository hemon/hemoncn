<?php
require_once 'config.php';
require_once 'lib/Validator.php';
require_once 'mod/subscibe.func.php';


$v = new Validator();
if( !$v->is_mobile($_REQUEST['mobile']) ){
    js_alert("手机号码格式不正确！");
}

$action = $_REQUEST['action'];

switch ($action) {
    case 'authcode':
        $authcode = authcode($_REQUEST['mobile']);
        if( 60 > (time() - $_SESSION['time']) ){
            $_SESSION['time'] = time();
            mail("$sid@139.com", "您的验证码为$authcode");
        }
        js_alert("验证码已发出，请耐心等待。");
	default:
        $sid = $_SESSION['usr']['sid'];
        $mobile = $_REQUEST['mobile'];
        $authcode = $_REQUEST['authcode'];
        $service = $_REQUEST['service'];
        js_alert(do_subscibe($sid, $mobile, $service, $authcode));
}

?>
