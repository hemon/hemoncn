<?php
function bot(){
    do{
        curl('http://www.hemono.com/score/update/?count=30',  array(CURLOPT_VERBOSE => 1));
    } while(1);
}

?>
