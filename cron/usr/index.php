<?php

require 'conn.php';

$db->query("DELETE FROM usr_index");
$stm = $db->query('SELECT search FROM usr');

require 'usr.4.php';
echo microtime_float() - $start, "\n";
