<?php
require_once 'config.php';
require_once 'redirect.php';
require_once 'mod/dopost.func.php';

$keyword = $_REQUEST['key'];
$page = $_REQUEST['page'];
$action = $_REQUEST['action'];

if( !empty($action) ){
    if ($isLogin) {
        switch ($action) {
            case 'add':
                $username = $_SESSION['usr']['sid'];
                $name = $_SESSION['usr']['name'];
                $content = $_REQUEST['content'];
                addPost($keyword, $username, $name, $content);
        }
    } else {
        js_alert("ÁôÑÔÇëÏÈµÇÂ½£¡","history.go(-1)");
    }
}

$rs = getPost($keyword, $page);
$post = $rs->GetArray();
$pageInfo = getPageInfo($rs);

include('templates/dopost.html');

?>
