<?php

function get_kw($kw){
    $kw = trim($kw);
    if( empty($kw) ) return false;

    $kw = preg_replace('/\s{2,}/', ' ', $kw);
    $kws = explode(' ', $kw);
    if( empty($kws) ) return false;
    
    return array_map("md5_num", $kws);
}

function md5_num($kw){
    return base_convert(md5($kw), 16, 10);
}

function get_ids($kid){
    global $db;
    $sql = 'SELECT ids FROM usr_index WHERE kid IN ('. implode(',', $kid) .')';
    $ids = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN, 0);
    
    $ids = ( 1 < count($ids) ) ? ids_array_intersect($ids) : $ids[0];
    return $ids;
}

function ids_split($ids){
    return array_flip(explode(',', $ids));
}

function ids_array_intersect($ids){
    $ids = array_map('ids_split', $ids);
    $ids = call_user_func_array("array_intersect_key", $ids);
    $ids = implode(',', array_keys($ids));
    return $ids;
}

function get_usr($ids){
    global $db;
    $sql = "SELECT * FROM usr WHERE sid IN ($ids)";
    return $db->query($sql)->fetchAll();
}

require 'conn.php';

$kid = get_kw($_REQUEST['kw']);
if( !empty($kid) ){
    $ids = get_ids($kid);
    $res = get_usr($ids);
}

include('search.html');
