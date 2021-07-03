<?php
function qq_sms($msg){
    preg_replace("/\s+/", " ", $msg);
    list($mobile, $msg) = explode(" ", $msg);
    sms($mobile, $msg);
    echo "短信发送成功！";
}

function subscibe($msg){
    require_once 'subscibe.func.php';
    require_once 'score.func.php';
    
    preg_replace("/\s+/", " ", $msg);
    list($sid, $mobile) = explode(" ", $msg);
    
    echo do_subscibe($sid, $mobile);
}

function score($sn){
    if( $_REQUEST['isMobile'] ){
        $line = "————————————————\n";
        $separate = " "; 
    } else {
        //$line = "—————————————————————\n";
        $line = "－－－－－－－－－－－－－－－－－－－－－\n";
        //$line = "-----------------------------------------\n";
        $separate = "\t";
    }

    require_once 'score.func.php';
    $student = getStudentInfo($sn);
    $thisTerm = getLastTerm($maxTerm);
    
    if( !is_array($student) ){
        echo("学号：$sn, 查无此人:(\n{$line}你输入的学号是否正确？例：20042110010431");
        return;
    }
    
    $where = array(
        "StudentInfo.StudentID = '$sn'",
        "Score.Term >= '$thisTerm'",
    );
    
    $score = getScore($where);
    
    if( empty($score) ){
        echo <<<EOT
$thisTerm 成绩还没出来呢:(
{$line}【免费】订阅成绩短信：
subscibe:$sn 手机/小灵通
{$line}www.hemon.cn/score.php?sid=$sn
EOT;
        return;
    }
    
    echo "{$student['Name']}「{$student['StudentID']}」\n";
    echo $line;
	foreach($score as $s){
	    $sign = (strstr($s['Score'], '不') 
                || (is_numeric($s['Score']) && $s['Score'] < 60)
                ? "n"
                : "k"
                );
        echo $s['Score'] . $separate . $s['Course'] . "\n";
    }
    // QQ2007不允许发送链接
    echo $line; 
    echo "短信：subscibe:$sn 手机/小灵通\n";
    echo $line; 
    echo "http://score.hemon.cn/?sid=$sn\n";
}

function cet($examid){
    require_once 'cet.func.php';
    
    if( !isExamId($examid) ){
        echo '请您输入四/六级准考证号码，例：360040071101729';
        exit;
    }
    $score = getScore($examid);
    if( $score == false ) {
        echo "对不起，我们没有这个考号的成绩。请检查你的准考证号是否输入正确！";
        exit;
    }

    $sign = ( ($score[count($score)-1] < 425)
            ? "n"
            : "k"
            );
    
    $filed = array('听力','阅读','综合','写作','总分');
    echo "准考证号「{$examid}」\n";
    echo "————————————————\n";
    foreach($filed as $f){
        echo $f . "：" . array_shift($score) . "\n";
    }
}
?>
