<?php
function safe_unserialize($serial) {
    $unserial = unserialize($serial);
    
    if( false === $unserial ){
        $serial = preg_replace_callback('/s:(\d+):"(.*?)(?!");(?=[a-z|}])/', "replace_quote", $serial);
        $unserial = unserialize($serial);
    }

    return $unserial; 
}

function replace_quote($matches){
    $strlen = $matches[1];
    $string = $matches[2];
    
    if( '"' == substr($string, -1, 1) ){
        $string = substr($string, 0, $strlen);
    } else {
        //file_put_contents('./s.txt', $string, FILE_APPEND);
        $string = str_pad(' ', $strlen);
    }
    
    $string = sprintf("s:%d:\"%s\";", strlen($string), $string);
    return $string;
}

?>
