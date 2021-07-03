<?php

require_once "lib/DB.php";
// app engine need utf-8
$GLOBALS['db']->Execute("SET NAMES utf8");

function bot(){

    ini_set('arg_separator.output', '&');

    $table = $_REQUEST["table"];
    $limit = $_REQUEST["limit"] ? $_REQUEST["limit"] : 10;
    
    $url = "http://www.hemono.com/load/$table";
    $url = "http://localhost:8080/load/$table";

    $db = new DB("edu_$table");
    $bot_rs = "bot_rs_$table";
    
    while( $rs = $bot_rs($db, $limit) ){
        // data
        foreach($rs as $row){
            // header
            if( empty($data) ){
                $data[] = implode("\t", array_keys($row));
            }
            // body
            $data[]  = implode("\t", $row);
        }
        // query
        $post = http_build_query(array('data' => implode("\n", $data)));
        do {
            $res = curl($url, array(CURLOPT_VERBOSE => 1, CURLOPT_POSTFIELDS => $post));
        } while ($res);
        // updated
        $updated = array('updated' => date('Y-m-d H:i:s'));
        $ids     = implode(",", array_keys($rs));
        $db->update($updated, "id in ($ids)");
        // reset
        unset($data);
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
