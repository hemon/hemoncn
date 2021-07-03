<?php

require 'stm.php';

$values = '';
foreach ($stm as $row) {
    $keywords = explode(' ', $row['search']);
    foreach( $keywords as $keyword ){
        $values .= "('$keyword','$id'),";
    }
}
$values = rtrim($values, ',');
