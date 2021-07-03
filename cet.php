<?php
require_once 'config.php';
require_once 'redirect.php';
require_once 'mod/cet.func.php';

$examid = trim($_REQUEST['examid']);

if( !isExamId($examid) ){
    js_alert('准考证号码不正确！例：360040071101729', 'history.go(-1)');
}

$score = getScore($examid);

if( $score == false ) {
    js_alert("对不起，我们没有这个考号的成绩。\n请检查你的准考证号是否输入正确！", 'history.go(-1)');
}

$processed = processed($start);
include("./templates/cet.html");

?>
