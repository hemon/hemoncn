<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'redirect.php';
require_once 'mod/student.func.php';

$key = $_REQUEST['key'];
$page = ( isset($_REQUEST['page']) ? $_REQUEST['page'] : 1 );

if( 4 > strlen($key) ){
    js_alert("请至少输入四个字符（或两个汉字）作为查询条件！", "history.go(-1)");
}

$rs = getStudent($key, $page);
$student = $rs->GetArray();
$pageInfo = getPageInfo($rs);

$lastGrade = substr(getLastTerm(),0,4);
$beginGrade = $lastGrade - 3;
$thisGrade = $_REQUEST['grade'];

if( !($beginGrade <= $thisGrade && $thisGrade <= $lastGrade) ){
    unset($_REQUEST['grade']);
    unset($thisGrade);
}

$processed = processed($start);

include('templates/student.html');

?>
