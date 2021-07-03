<?php

function bot(){
    
    $class_fetched_file = glob("./tmp/student/*");
    foreach( $class_fetched_file as $class ){
        $class_fetched[ array_pop(explode('/', $class)) ] = null;
    }
    
    $class_all = @file("./class");
    foreach( $class_all as $class ){
        list($class, $classid) = explode("\t", trim($class));
        //if( array_key_exists($class, $class_fetched) ) continue;
        
        $url = "http://jwc.ecjtu.jx.cn:8080/jwcmis/stuquery.jsp?banji=$classid";
        $tmp = './tmp/student/'.$classid;
        if( is_file($tmp) ){
            $res = file_get_contents($tmp);
        } else {
            $res = curl($url);
            file_put_contents($tmp, $res);
        }
        
        preg_match_all('|<tr align="center"> <td align="center" width="41" height="16"><font size="2">\d+</font></td><td align="center" width="41" height="16"><font size="2">(.*?)</font></td><td align="center" width="41" height="16"><font size="2">(\d+)</font></td><td align="center" width="41" height="16"><font size="2">(\d+)</font></td><td align="center" width="41" height="16"><font size="2">.*?</font></td></tr>|', $res, $out, PREG_SET_ORDER);
        foreach($out as $line){
            unset($line[0]);
            file_put_contents('./student', implode("\t",$line)."\n", FILE_APPEND);
        }
    }
}
?>
