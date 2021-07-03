<?php

error_reporting(2048);

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

$start = microtime_float();

require 'h.php';

echo microtime_float() - $start;

$start = microtime_float();

require 'g.php';

echo microtime_float() - $start;

$start = microtime_float();

require 's.php';

echo microtime_float() - $start;
