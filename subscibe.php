<?php
require_once 'config.php';
require_once 'lib/Validator.php';
require_once 'mod/subscibe.func.php';


$v = new Validator();
if( !$v->is_mobile($_REQUEST['mobile']) ){
    js_alert("�ֻ������ʽ����ȷ��");
}

$action = $_REQUEST['action'];

switch ($action) {
    case 'authcode':
        $authcode = authcode($_REQUEST['mobile']);
        if( 60 > (time() - $_SESSION['time']) ){
            $_SESSION['time'] = time();
            mail("$sid@139.com", "������֤��Ϊ$authcode");
        }
        js_alert("��֤���ѷ����������ĵȴ���");
	default:
        $sid = $_SESSION['usr']['sid'];
        $mobile = $_REQUEST['mobile'];
        $authcode = $_REQUEST['authcode'];
        $service = $_REQUEST['service'];
        js_alert(do_subscibe($sid, $mobile, $service, $authcode));
}

?>
