<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'lib/DB.php';
require_once 'mod/subscibe.func.php';

// usr
$usr = new DB('usr');
$action = $_REQUEST['action'];
if( method_exists($usr, $action) ){
    $user->$action($_REQUEST);
}
$where['sid'] = $_SESSION['usr']['sid'];
$my = $usr->select('row', $where);
// subscibe
$subscibe = new DB('subscibe');
$subscibe = $subscibe->select('row', $where);
// lunar
if( empty($my['lunar']) && !empty($my['birthday']) ){
    require_once 'lib/Lunar.php';
    $lunar = new Lunar();
    $my['lunar'] = date("Y-m-d", $lunar->getLar($my['birthday'], 0));
}

include('templates/my.html');

?>
