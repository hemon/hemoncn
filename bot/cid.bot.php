<?php

require_once "lib/DB.php";
// app engine need utf-8
$GLOBALS['db']->Execute("SET NAMES utf8");

function bot(){

    ini_set('arg_separator.output', '&');

    $table = $_REQUEST["table"];
    $limit = $_REQUEST["limit"] ? $_REQUEST["limit"] : 10;
    
    $url = "http://www.hemono.com/score/update/";
    //$url = "http://localhost:8080/load/$table";
    
    $table = 'student';

    $db = new DB("edu_$table");
    $bot_rs = "bot_rs_$table";
    
    while( $rs = $bot_rs($db, $limit) ){
        // data
        foreach($rs as $row){
            extract($row);
            // body
            $urls[]  = $url . "?sid=$sid&cid=$cid";
        }
        // query
        curl_m($urls, array(CURLOPT_VERBOSE => 1));
        // updated
        $updated = array('updated' => date('Y-m-d H:i:s'));
        $ids     = implode(",", array_keys($rs));
        $db->update($updated, "id in ($ids)");
        // reset
        unset($urls);
    }
}

function bot_rs_score($db, $limit = 10){
    return $db->select(
                'limit', 
                'updated IS NULL', 
                null, 
                '`id`,`sid`,`name`,`course`,`require`,`credit`,`score`,`term`', 
                0, 
                $limit)->GetAssoc();
}

function bot_rs_student($db, $limit = 10){
    return $db->select(
                'limit', 
                'updated IS NULL', 
                null, 
                'id,sid,left(cid,12) cid,name', 
                0, 
                $limit)->GetAssoc();
}

?>
