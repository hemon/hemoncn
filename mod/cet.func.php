<?php
function isExamId($examid){
    if ( !is_numeric($examid) 
        || (strlen(trim($examid)) != 15 ) ) {
        
    	return false;
    }
    return true;
}

function getExamType($examid){
    // 考试类型
    $examtype = substr($examid,9,1);
    if( $examtype == 1 ) {
        $t = 4;
    } else {
        $t = 6;
    }
    return $t;
}

function getScore($examid){
    $examType = getExamType($examid);
    // 缓存文件
    $cache = TMP . "cet/$examid.txt";
    // 如果存在缓存，直接读取
    if ( file_exists($cache) ) {
        $score = explode(",", file_get_contents($cache));
    } else {
        $url = "http://cet.99sushe.com/result.html?t=$examType&id=$examid";
        $output = curl($url);
        // header头文件里输出成绩，设置cookie：
        // score=听力,阅读,综合,写作,总分
        $score = explode(",", $output);
        // 如无成绩，score=error
        if( 5 == count($score) ){
            file_put_contents($cache, $output);
        } else {
            $score = false;
        }
    }
    return $score;
}

?>
