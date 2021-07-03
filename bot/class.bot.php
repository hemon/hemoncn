<?php

function bot(){
    $PHPSESSID = jwc_login();
    
    file_empty("./class");
    
    $max = 1;
    
    for($i = 0; $i < $max; $i++){
        $url = "http://jwc.ecjtu.jx.cn/mis/class.php?start=$i&PHPSESSID=$PHPSESSID";
        $tmp = './tmp/class/'.$i;
        /*if( is_file($tmp) ){
            $res = file_get_contents($tmp);
        } else {*/
            $res = curl($url);
            file_put_contents($tmp, $res);
        //}
        
        $max = preg_match_one("/¹²<font color=red>(\d+)<\/font>Ò³/", $res);
        
        preg_match_all("|<td>(.*?)</td>(?:\W+)?<td>(\d{12})|", $res, $out, PREG_SET_ORDER);
        foreach($out as $line){
            unset($line[0]);
            file_put_contents('./class', implode("\t",$line)."\n", FILE_APPEND);
        }
    }
}

function file_empty($file){
    return file_put_contents($file, '');
}


function preg_match_one($pattern, $subject){
    if( preg_match($pattern, $subject, $matches) ){
        return $matches[1];
    }
    return false;
}

function jwc_login(){
    $login = 'http://jwc.ecjtu.jx.cn/mis/login.php?user=jwc&pass=jwc';
    $html  = curl($login, array(CURLOPT_HEADER => 1));

    preg_match('/PHPSESSID=(\w+)/', $html, $matches);
    return $matches[1];
}
