<?php
require_once 'config.php';
require_once 'redirect.php';
require_once 'lib/DB.php';

if($_REQUEST['ptype'] == '全部') unset($_REQUEST['ptype']);
$page = (!empty($_REQUEST['page']) ? $_REQUEST['page'] : 1);

$ptypes = array('全部','快递','挂号信','包裹','印刷品','汇款单','退件','银行卡');
$ptype = (!empty($_REQUEST['ptype']) ? $_REQUEST['ptype']: '全部');

$db = new DB('edu_postoffice');
$post = $db->selectPage($_REQUEST, $page, 20, '*', 'arrival DESC', 0);
$processed = processed($start);
include('templates/post.html');

?>
