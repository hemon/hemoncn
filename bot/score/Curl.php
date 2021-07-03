<?php

class Curl {

    public static $CURLOPT = array(
    	//CURLOPT_VERBOSE        => 1,
        CURLOPT_TIMEOUT        => 600,
        CURLOPT_RETURNTRANSFER => 1,
    );

    public static function exec($url, $opt = array(), $save_to = false){
        if( is_array($url) && 1 == count($url) ){
            $url = array_pop($url);
        }
        if( is_array($url) ){
            return self::gets($url, $opt, $save_to);
        } else {
            return self::get($url, $opt, $save_to);
        }
    }

    public static function get($url, $opt = array(), $save_to = false){
        // load opt
        $options = self::$CURLOPT;
        if( is_array($opt) ){
            $options += $opt;
        }
        // set file handel in curl_opt
        if( $save_to ){
    		$save_file = $save_to . basename($url);
    		$fp = fopen($save_file, 'w');
    		$options[CURLOPT_FILE] = $fp;
        }
        // exec
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        // close file handel
        if( $save_to ) fclose($fp);
        // error
        if( curl_errno($ch) ){
            print curl_error($ch);
        }
        // close
        curl_close ($ch);
        return $result;
    }
    
    public static function gets($urls, $opt = array(), $save_to = false){
        // load opt
        $options = self::$CURLOPT;
        if( is_array($opt) ){
            $options += $opt;
        }
        // curl init && add handel
        $mh = curl_multi_init();
        foreach ($urls as $i => $url) {
            // set file handel in this curl_opt
    		if( $save_to ){
    			$save_file = $save_to . basename($url);
    			$fp[$i] = fopen($save_file, 'w');
    			$options[CURLOPT_FILE] = $fp[$i];
    		}
    		// add curl to multi_curl handel
    		$conn[$i] = curl_init($url);
            curl_setopt_array($conn[$i], $options);
            curl_multi_add_handle($mh, $conn[$i]);
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
    		if ( '' == ($err = curl_error($conn[$i])) ) {
    			$res[$i] = curl_multi_getcontent($conn[$i]);
    		} else {
    			print "Curl error on handle $i: $err\n";
    		}
    		
            curl_multi_remove_handle($mh, $conn[$i]);
            curl_close($conn[$i]);
            
            if( $save_to ) fclose($fp[$i]);
        }
        
        curl_multi_close($mh);
        
        return $res;
    }
}
