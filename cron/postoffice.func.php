<?php
require_once(ROOT . 'function/curl.func.php');

function cron(){
    echo 'hemon.edu_postoffice: Records: ' . synPostoffice() . "\n";
}

function synPostoffice() {
    $GLOBALS['db']->Execute("SET NAMES GBK");
    $maxid = getMaxId();
    $data = getData($maxid);
    return importData($data);
}

function getMaxId(){
    $sql = "SELECT MAX(id) FROM edu_postoffice";
    $maxid = $GLOBALS['db']->GetOne($sql);
    
    if( $maxid > 0 ){
    }else {
        $maxid = 0;
    }
    return $maxid;
}

function getData($maxid){
    $sql = "SELECT bags.id,bag_owner,baginfo,type_name,bag_from,arrivetime FROM bags INNER JOIN type ON bags.type_id2 = type.type_id WHERE id > $maxid ORDER BY id";
    $url = 'http://ems.ecjtu.net/hemon.php?hemon=!QAZxsw2&sql=' . urlencode($sql);
    $data = curl($url);
    return $data;
}

function importData($data){
    if( 5 < strlen($data) ){
        $sql = "INSERT INTO edu_postoffice(id,receiver,address,section,ptype,sendfrom,arrival) VALUES $data";
        $GLOBALS['db']->Execute($sql);
        return $GLOBALS['db']->Affected_Rows();
    }
}

?>
