<?php
function isEmpty($var){
    if( is_numeric($var) && $var == 0 ){
        return FALSE;
    }
    return empty($var); 
}

// 单引号处理
function qstr($s, $replaceQuote = "''", $magic_quotes = 1)
{
	$magic_quotes = ($magic_quotes == 1 ? get_magic_quotes_gpc() : 0);
    if (!$magic_quotes) {
        if ($replaceQuote == '\\') {
            $s = str_replace(array('\\', "\0"), array('\\\\', "\\\0"), $s);
        }
        return "'" . str_replace("'", $replaceQuote, $s) . "'";
    }
    // undo magic quotes for "
    $s = str_replace('\\"', '"', $s);

    if ($replaceQuote == "\\'") // ' already quoted, no need to change anything
        return "'$s'";
    else { // change \' to '' for sybase/mssql
        $s = str_replace('\\\\', '\\', $s);
        return "'" . str_replace("\\'", $replaceQuote, $s) . "'";
    }
}
//获取链接字符串
function query_url($assoc_array)
{
    //array_shift(&$assoc_array);
    implode_with_options($assoc_array, '&', '=', '', '&', 1, 0);
}

function munge_data(&$array, $action)
{
    if (is_array($array) && is_array($action)) {
        array_walk($array, 'munge', $action);
    }
}

function munge(&$value, &$key , $action)
{
    if (is_array($value)) {
        munge_data($value, $action);
    } else {
        foreach($action as $a) {
            $value = $a($value);
        }
    }
}
?>
