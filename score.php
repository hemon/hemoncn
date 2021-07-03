<?php
require_once 'config.php';
require_once 'redirect.php';
require_once 'mod/score.func.php';

$sid     = $_REQUEST['sid'];
$course  = $_REQUEST['course'];
$student = getStudentInfo($sid);
if( !is_array($student) ){
    js_alert("Sorry ，查无此人！", "history.go(-1)");
}

$cid       = substr($student['cid'], 0, 12);
$beginTerm = substr($sid, 0, 4);
$maxTerm   = getMaxTerm($student);
$lastTerm  = getLastTerm($maxTerm);
$thisTerm  = getThisTerm($lastTerm, $maxTerm);
$terms     = getTerm($beginTerm, $lastTerm);

if( empty($course) ){
    $where[] = "sid = '$sid'";
} else {
    /* 查询同班同学
    *  old:使用substring比较前12位班级编号是否相等
    *  new:建立classcode索引,使用like xxx%查询时可使用索引
    */
    $where[] = "cid LIKE '$cid%'";
    $where[] = "course = '$course'";
}

if($thisTerm == $lastTerm) {
    // 英语分级教学,提前学习下学期英语
    $where[] = "term >= '$thisTerm'";
} else {
    $where[] = "term = '$thisTerm'";
}

$score      = getScore($where);
$classmates = getClassmates($cid);

if( $_REQUEST['output'] === 'json' ){
    exit(json_encode(array_iconv('gbk','utf-8',$score)));
}

$processed = processed($start);

$actives[$thisTerm] = ' class="active"';
include('templates/score.html');

?>

