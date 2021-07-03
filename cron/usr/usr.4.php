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

$file   = str_replace('\\','/',dirname(realpath(__FILE__)).'/keywords.txt');

$handle = fopen($file, 'w');
foreach($values as $keyword => $ids ){
    //$contents .= $keyword.'|'.rtrim($ids, ',')."\n";
    //$contents .= md5($keyword).'|'.rtrim($ids, ',')."\n";
    $contents .= base_convert(md5($keyword),16,10).'|'.rtrim($ids, ',')."\n";
}
fwrite($handle, $contents);
fclose($handle);

$sql = "LOAD DATA LOCAL INFILE '$file'
        INTO TABLE usr_index
        FIELDS TERMINATED BY '|'";

$db->exec($sql);
