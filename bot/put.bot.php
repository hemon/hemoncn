<?php

require_once "lib/DB.php";

function bot(){
    
    $db = new DB('edu_score');

    $scores  = explode("\n", file_get_contents('./score'));
    $key  = array('term', 'sid', 'name', 'course', 'require', 'credit', 'score', 'score_1', 'score_2');
    foreach($scores as $score){
        $val = explode("\t", $score);
        if( 9 == count($val) ){
            $data = array_combine($key, $val);
            $data = array_filter($data, "bot_empty");
            $db->insert($data);
        }
    }
    unlink('./score');
}

function bot_empty($var){
    $var = trim($var);
    return !empty($var);
}

?>
