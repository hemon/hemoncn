<?php

foreach ($stm as $row) {
    $keywords = explode(' ', $row['search']);
    $id       = array_shift($keywords);
    
    foreach( $keywords as $keyword ){
        $keyword = trim($keyword);
        if( empty($keyword) ) continue;
        
        if( !$db->exec("INSERT INTO usr_index VALUES ('$keyword','$id')") ){
            $db->exec("UPDATE usr_index SET ids = CONCAT(ids, ',$id') WHERE keyword = '$keyword'");
        }
    }
}
