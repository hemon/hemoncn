<?php
$client = new SoapClient(null, array('location' => "http://www.hemon.cn/soap.php",
                                     'uri'      => "http://score-uri/"));

$sid = '20050410100104';
$thisTerm = '2007.2';
$cid = $client->getClasscode($sid);

$course = '商务谈判';

if( empty($course) ){
    $where = array(
        'StudentInfo.StudentID' => $sid,
        'Score.Term' => $thisTerm,
    );
} else {
    $where = array(
        'SUBSTRING(StudentInfo.ClassCode, 1, 12)' => $cid,
        'Score.Term' => $thisTerm,
        'Course.Course' => $course,
    );
}

$score = $client->getScore($where);
print_r($score);

$classmates = $client->getClassmates($sid);
print_r($classmates);
?>
