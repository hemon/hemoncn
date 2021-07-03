<?php

function bot(){
    $cookie = 'tmp/cookie/score';
    $sessid = bot_sessid();
    
    $save_to = 'tmp/score/';
    $opts = array(
        CURLOPT_VERBOSE => 1,
        CURLOPT_CONNECTTIMEOUT => 1000
    );
    
    while ( $tasks = bot_task(1, 1) ) {
        foreach($tasks as $id => $url){
            $max = 1;
            for($start = 0; $start < $max; $start++){
            
                $page = "$url&start=$start&$sessid";
                $res  = curl($page, $opts);

                if( empty($res) ) {
                    $start = $start - 1;
                    continue;
                }
                
                if( strstr($res, 'login') ) {
                    bot_sessid();
                    $start = $start - 1;
                    continue;
                }

                if( $id > 145 &&
                    $start == 0 && 
                    preg_match('/>(\d+)\W+<\/select>/U', $res, $matches) ){
                    $max = $matches[1];
                }
                
                $file = $save_to . $id . '_' . $start;
                file_put_contents($file, $res);
            }
            
            $updated = date('Y-m-d H:i:s');
            $GLOBALS['db']->Execute("UPDATE bot_task SET updated = '$updated' WHERE id = $id"); 
        }
    }
}

function bot_task($sid, $limit = 1){
    $sql = "SELECT * FROM bot_seed WHERE id = $sid";
    $seed = $GLOBALS['db']->GetRow($sql); 
    
    $sql = "SELECT * FROM bot_task WHERE sid = $sid ORDER BY updated LIMIT $limit";
    $rs = $GLOBALS['db']->Execute($sql); 
    if ($rs) 
        while ($task = $rs->FetchRow()) {
            $urls[$task['id']] = bot_build_url($seed['url'], $seed['arg'], $task['val']);
        }
        
    return $urls;
}

function bot_build_url($url, $arg, $val){
    $arg  = explode(',', $arg);
    $val  = explode(',', $val);
    $data = array_combine($arg, $val);
    
    ini_set('arg_separator.output', '&');
    return $url . '?' . http_build_query($data);
}

function bot_sessid(){
    $url = 'http://jwc.ecjtu.jx.cn/mis/login.php?user=jwc&pass=jwc';
    $res = curl($url, array(CURLOPT_HEADER => 1));
    preg_match('/(PHPSESSID=\w+)/', $res, $matches);
    return $matches[1];
}

?>
