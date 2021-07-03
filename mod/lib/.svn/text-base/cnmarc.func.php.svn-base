<?php

function cnmarc($html){
    $reg = '/marc value="(.*?)"/';
    preg_match($reg, $html, $matches);
    $marcString = $matches[1];
	return parseMarc($marcString);
}

function parseMarc($marcString){
    $marcArray = split(chr(30), $marcString);
    foreach ( $marcArray as $marc){
    	$marc = split(chr(31), $marc);
    	$code = substr(trim(array_shift($marc)),0,3);
    	switch ($code){
    	    case 986:
    	    case 989:
    	        $cnmarc[$code] = parseMarc986($marc);
    	        break;
    	    default:
    			foreach ($marc as $a){
    				$key = substr($a, 0, 1);
    				$cnmarc[$code][$key] = substr($a, 1);
    			}
        }
    }
    return $cnmarc;
}

function parseMarc986($marcArray){
    $lib = array('a' => '教师',
                'b' => '南区',
                'c' => '南区',
                'd' => '南区',
                'o' => '北区',
                'g' => '光盘',
                'e' => '参考');
	foreach ($marcArray as $marcString){
		if( $marcString == 'a' ) continue;
		$key = substr($marcString,0,1);
		$val = substr($marcString,1);
		switch ($key){
			case 'a':
				$bookid = $val;
				$marc[$bookid]['a'] = $val;
				break;
			case 'h':
				if( empty($bookid) ){
				    $bookid = $val;
                } else {
                    $marc[$bookid]['h'] = $val;
                }
				break;
			case 'r':
				if(!isset($marc[$bookid]['b'])) $marc[$bookid]['r'] = $val;
				break;
			case 'f':
				if(!isset($marc[$bookid]['b'])) $marc[$bookid]['f'] = $val;
				break;
			case 't':
				if(!isset($marc[$bookid]['b'])) $marc[$bookid]['t'] = $val;
				break;
			case 'v':
				if(!isset($marc[$bookid]['b'])) $marc[$bookid]['v'] = $val;
				break;
			case 'b':
				$marc[$bookid]['b'] = $lib[$val];
				break;
		}
	}
    return $marc;
}

?>
