<?php

require 'stm.php';

$values = array();
foreach ($stm as $row) {
    $keywords = explode(' ', $row['search']);
    foreach( $keywords as $keyword ){
        $values[] = "('$keyword','$id')";
    }
}
$values = implode(',', $values);
