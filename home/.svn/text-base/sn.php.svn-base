<?php
/*
	(C) 2007-2008 hemon.cn.
	$Id: sn.php 2008-08-30 17:32 hemon $
*/

sn_update();

function ob_sn_name($buffer){
    return preg_replace_callback('/>(200\d{11})/', 'sn_name', $buffer);
}

function sn_name($matches){
    $sn = $matches[1];
    if( $name = xcache_get($sn) ){
        return ">$name";
    } else {
        return ">$sn";
    }
}

function sn_update(){
    if( !xcache_isset('20020410070133') ){
        $sn_array = sn_array();
        sn_xcache($sn_array);
    }
}

function sn_array(){
    require_once '../config.php';
    $GLOBALS['db']->Execute("SET NAMES utf8");
    
    $sql = "SELECT StudentID, Name FROM edu_studentinfo WHERE Name IS NOT NULL
            UNION SELECT sn, name FROM usr WHERE name IS NOT NULL";
    $sn = $GLOBALS['db']->GetAssoc($sql);
    return $sn;
}

function sn_xcache($array){
    foreach($array as $key => $value){
        xcache_set($key, $value);
    }
    return true;
}

function sn_file($array, $file = 'data/sn_array.php'){
    return file_put_contents($file, '<?php return ' . var_export($array, true) . '?>');
}

?>
