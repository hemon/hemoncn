<?php

$values = array();
foreach ( $stm as $row) {
    $keywords = explode(' ', $row['search']);
    $id = array_shift($keywords);
    
    foreach( $keywords as $keyword ){
        $keyword = trim($keyword);
        if( empty($keyword) ) continue;
        
        $values[$keyword] .= $id.',';
    }
}

foreach( $values as $keywords => $ids ){
    $ids = rtrim($ids, ',');
    $sql = "INSERT INTO usr_index_kw VALUES ('$keywords', '$ids')";
    $db->exec($sql);
}
