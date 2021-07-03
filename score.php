<?php
require_once 'config.php';
require_once 'redirect.php';
require_once 'mod/score.func.php';

$sid     = $_REQUEST['sid'];
$course  = $_REQUEST['course'];
$student = getStudentInfo($sid);
if( !is_array($student) ){
    js_alert("Sorry �����޴��ˣ�", "history.go(-1)");
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
    /* ��ѯͬ��ͬѧ
    *  old:ʹ��substring�Ƚ�ǰ12λ�༶����Ƿ����
    *  new:����classcode����,ʹ��like xxx%��ѯʱ��ʹ������
    */
    $where[] = "cid LIKE '$cid%'";
    $where[] = "course = '$course'";
}

if($thisTerm == $lastTerm) {
    // Ӣ��ּ���ѧ,��ǰѧϰ��ѧ��Ӣ��
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

