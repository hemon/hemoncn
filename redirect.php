<?php
// login flag
$isLogin = ( isset($_SESSION['usr']) ? true : false );
// set default tab
$thisTab = 'score';
// xxx.hemon.cn/ => /xxx.php
// xxx.php => /index.php?thistab=xxx
$baseName = basename($_SERVER['SCRIPT_FILENAME'],".php");
$tabs = array('cet','score','student','lib','cnki');
if( in_array($baseName, $tabs) && empty($_GET) ){
    $thisTab = $baseName;
    include "index.php";
    exit;
}
?>
