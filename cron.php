<?php
require_once 'config.php';
if($argc >= 2) {
    require_once "cron/{$argv[1]}.func.php";
    cron();
}
?>
