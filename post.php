<?php
require_once 'config.php';
require_once 'redirect.php';
require_once 'lib/DB.php';

if($_REQUEST['ptype'] == 'ȫ��') unset($_REQUEST['ptype']);
$page = (!empty($_REQUEST['page']) ? $_REQUEST['page'] : 1);

$ptypes = array('ȫ��','���','�Һ���','����','ӡˢƷ','��','�˼�','���п�');
$ptype = (!empty($_REQUEST['ptype']) ? $_REQUEST['ptype']: 'ȫ��');

$db = new DB('edu_postoffice');
$post = $db->selectPage($_REQUEST, $page, 20, '*', 'arrival DESC', 0);
$processed = processed($start);
include('templates/post.html');

?>
