<?php
function implode_with_options(&$assoc_array, $prefix = '',    $k_v_glue = '', 
                                $vwrap = '', $seperator = '', $show_key = 1, 
                                $qstr = 1,   $ig_empty = 1) {
    foreach ($assoc_array as $k => $v) {
        $key = ( 1 == $show_key ? $k : '');
        $value = ( 1 == $qstr ? qstr($v) : $v);
        if(1 == $ig_empty && isEmpty($v)) continue;
        $tmp[] = $key . $k_v_glue . $vwrap . $value . strrev($vwrap);
    }
    if ( 0 < count($tmp) ){
        return $prefix . implode($seperator, $tmp);
    }
    return false;
}

function in_array_multi_key($needle, &$haystack)
{
    // mutidimentional search for in_array function
    // only matches the key, values don't count.
    if ( array_key_exists($needle, $haystack) )
    {
        return TRUE;
    }
    foreach ( $haystack as $key => $value )
    {
        if ( is_array($value) )
        {
            $work = in_array_multi_key($needle, $value);
            if ( $work )
            {
                return TRUE;
            }
        }
    }
    return FALSE;
}

function regex_array_keys ( &$arr, $pattern ) {
    $results[] = false;
    if( !is_array( $arr ) )
        return false;
    
    while( !is_null( $key = key( $arr ) ) ) {
        if( preg_match( $pattern, $key ) )
            $results[] = $key;
        next($arr);
    }
    reset( $arr );
    
    return $results;
}
//替换数个值
function assoc_replace($assoc_array, $s, $mode = 'str')
{
    $searchReg = array_keys($assoc_array);
    $replaceReg = array_values($assoc_array);
    $replace = $mode . '_replace';
    $text = $replace($searchReg, $replaceReg, $s);
    return $text;
}
//模拟sql select
function select_rs(&$table, $fields = '*', $diff = false)
{
    $rs = array();
    $rowNum = 0;
    foreach($table as $row){
    	if( is_array($fields) ){
    		$tmp = array();
	    	foreach($fields as $field_name){
	    		$field_name = strtolower($field_name);
	    		if ( isset($row[$field_name]) ) { 
                    $tmp[$field_name] = $row[$field_name];
                    if (true == $diff) unset($row[$field_name]);
                }
	    	}
	    	$rs[] = $tmp;
		} elseif( $fields == '*' ) {
			return $table;
			if(true == $diff) unset($table);
		} else {
			$rs[] = $row[$col];
			if(true == $diff) unset($row[$field_name]);
		}
    }
    return $rs;
}

function array_key_group($array, $key = 0)
{
    foreach ($array as $row)
    {
        $firstkey = array_keys($row);
        $firstkey = $firstkey[$key];
        $k = $row[$firstkey];
        unset($row[$firstkey]);
        if(count($row) == 1){
            $row = array_pop($row);
        }
        $newarray[$k] = $row;
    }
    return $newarray;
}

// 查找$array中存在$key索引的元素
function array_find_key(&$key, &$array, $igEmpty=true) {
    $find = array();
    foreach($key as $k){
        // 检验索引存在
        if (!array_key_exists($k, $array)) {
            continue;
        }
        // 忽略空值
        if ( true == $igEmpty && isEmpty($array[$k]) ) {
            continue;
        }
        $find[$k] = $array[$k];
    }
    return $find;
}

function array_keys_exists($keys, &$array, $ig_empty = true){
    if( !is_array($keys) ){
        $keys = explode(",", $keys);
    }
    foreach ( $keys as &$key )
    {
        if ( !array_key_exists($key, $array) ) {
            return FALSE;
        } else {
            if( $ig_empty == true && isEmpty($array[$key]) )
                return FALSE;
        }
    }
    return TRUE;
}

function array_sort($array, $keySort, $sort='krsort'){
    $newArray = array();
    foreach($array as $key => &$val){
        $newArray[$val[$keySort]] = $val;
    }
    krsort($newArray);
    return $newArray;
}

?>
