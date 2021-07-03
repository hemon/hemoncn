<?php

foreach ($stm as $row) {
    // 1
    $keywords = explode(' ', $row['search']);
    $id       = array_shift($keywords);
    
    // 2
    $values   = '';
    foreach( $keywords as $keyword ){
        $keyword = trim($keyword);
        if( empty($keyword) ) continue;
        
        $values .= "('$keyword','$id'),";
    }
    $values = rtrim($values, ',');
    
    // 3
    $sql = "INSERT INTO 
                usr_index 
            VALUES 
                $values 
            ON DUPLICATE KEY UPDATE 
                ids = CONCAT(ids, ',', VALUES(ids))";

    $db->exec($sql);
}
