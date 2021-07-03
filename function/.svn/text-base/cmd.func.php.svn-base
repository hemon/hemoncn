<?php
function stringToChr($string) {
    $pattern = array("|\'(.*?)\'|", "|\"(.*?)\"|");
    return preg_replace_callback($pattern, "toChrString", $string);
}

function toChrString($matches) {
    $string =  $matches[1];
    $length = strlen($string);
    for($i = 0; $i < $length; $i++){
        $chrString .= 'chr(' . ord($string{$i}) . ').';
    }
    return substr($chrString, 0, -1);
}
?>
