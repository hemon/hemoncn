<?php
mb_internal_encoding("utf-8");
mb_http_output("gbk");
ob_start("mb_output_handler");

ini_set('date.timezone','Asia/Shanghai');

require_once 'function/global.func.php';

$msg = $_REQUEST['msg']; //mb_convert_encoding($_REQUEST['msg'], "gbk", "UTF-8");
$qq = $_REQUEST['qq'];
$mobileString = "qq.com";
$isMobile = strpos($msg, $mobileString);

if ($isMobile === false) {
    $_REQUEST['isMobile'] = false;
}else{
    $_REQUEST['isMobile'] = true;
}

$msg = trim(sbc_dbc($msg,1));

//if ( strpos($msg, '[ZDY][32]') === 0 ) echo "";

file_put_contents(
    'tmp/log/qq', 
    date("Y-m-d H:i:s") . "\t" . $_REQUEST['im'] . "\n" . $msg  ."\n", 
    FILE_APPEND
);

$action = '';
$defaultAction = 'cet';

$actionMap = array(
    'score'    => 'score',
    'cj'       => 'score',
    '成绩'     => 'score',
    'subscibe' => 'subscibe',
    'dy'       => 'subscibe',
    '订阅'     => 'subscibe',
    '短信'     => 'subscibe',
    'cjdx'     => 'subscibe',
    '成绩短信' => 'subscibe',
    'cet'      => 'cet',
    '46'       => 'cet',
    '46ji'     => 'cet',
    '四六级'   => 'cet',
    'sms'      => 'qq_sms',
);



if(preg_match("/^(\d{15})/", $msg, $matches)){
    $action = 'cet';
    $msg = $matches[1];
} elseif(preg_match("/^(\d{14})/", $msg, $matches)) {
    $action = 'score';
    $msg = $matches[1];
} else {
    list($action, $msg) = explode(":", $msg, 2);
}

if( array_key_exists($action, $actionMap) ){
    require_once 'config.php';
    require_once 'mod/qq.func.php';
    $actionMap[$action]($msg);
} else {
    echo <<<EOT
①. 四六级查询「cet」
格式：「直接输入考号」或 cet:考号
360040071101729,cet:360040071101729
②. 成绩查询「score」
格式：「直接输入学号」或 score:学号
20042110010431,score:20042110010431
③. 成绩短信「subscibe」【免费】
格式：subscibe:学号 手机/小灵通
subscibe:20020410070111 13576001108
－－－－－－－－－－－－－－－－－－－－－
客服QQ：24303484 QQ群：52018863
－－－－－－－－－－－－－－－－－－－－－
「hemon.cn 机器人不回答任何无关问题」
EOT;
}

?>
