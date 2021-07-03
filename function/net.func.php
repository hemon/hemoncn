<?php

function curl($url, $opt = null) {
    $options = array(
        CURLOPT_RETURNTRANSFER => 1,
        //CURLOPT_USERAGENT => 'BaiduSpider'
    );
    // array_merge : 如果只给了一个数组并且该数组是数字索引的，则键名会以连续方式重新索引。
    // array + array不会重建数组索引
    if( is_array($opt) ){
        $options = $opt + $options;
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $tmp = curl_exec ($ch); 
    curl_close ($ch);
    return $tmp;
}

function ip() {
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
    	$onlineip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
    	$onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
    	$onlineip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
    	$onlineip = $_SERVER['REMOTE_ADDR'];
    }
    $onlineip = preg_replace("/^([\d\.]+).*/", "\\1", $onlineip);
    return $onlineip;
}

?>
