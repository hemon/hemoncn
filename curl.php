<?php
require_once 'function/global.func.php';
require_once('mod/lib.func.php');

$url = $_REQUEST['url'];
$html = curl_cache($url, null, 86400, $url);
echo $html;
exit;
/*
$search = array(
    "'<script[^>]*?>.*?</script>'si" => '',
    "[src|href]=[\"|']/" => ''
)
$html = preg_replace(, "", $html);
echo $html;*/
?>
