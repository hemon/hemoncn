<?php

function bot(){
    $dir = "tmp/score/";
    if (is_dir($dir)) {
       if ($dh = opendir($dir)) {
           while (($file = readdir($dh)) !== false) {
               $file = $dir . $file;
               if( 'file' == filetype($file) ){
                    $res = file_get_contents($file);
                    preg_match_all ("|<tr><td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>(?:\W+)?<td>(.*?)</td>|", $res, $out, PREG_SET_ORDER);
                    foreach($out as $line){
                        unset($line[0]);
                        file_put_contents('./score', implode("\t",$line)."\n", FILE_APPEND);
                    }
                    unlink($file);
               }
           }
           closedir($dh);
       }
    }
}
/*
function bot_cron(){
    $urls = bot_urls();
    $data = bot_get($urls);
    bot_save($data);
}

function bot_urls(){
    $GLOBALS['db']->get
    return $urls;
}

function bot_get($urls){
    foreach($urls as $url){
        
    }
}

function bot_save($data){
    
}
*/
?>
