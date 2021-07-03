<?php
require_once 'config.php';
//require_once 'redirect.php';
require_once 'mod/cnki.func.php';

$action = $_REQUEST['action'];
//$action = 'download';
switch ($action) {
    case 'detail':
        $data = cnki_detail($_REQUEST['QueryID'], $_REQUEST['CurRec']);
        //exit( @json_encode( $data ) ); 
        extract($data);
        include 'templates/cnki_detail.html';
        break;
    case 'download':
        exit( cnki_download($_REQUEST['filename'], $_REQUEST['tablename'], $_REQUEST['dflag'], true) );
    case 'view':
        exit( cnki_download($_REQUEST['filename'], $_REQUEST['tablename'], 'pdfdown', false) );
    default:
        $key  = $_REQUEST['key'];
        $list = cnki_list($key, $_REQUEST['page'], $_REQUEST['QueryID']);
        $processed = processed($start);
        include 'templates/cnki.html';
        //echo str_replace(array('singledbdetail.aspx?','download.aspx?'), array('?action=detail&','?action=download&'), $html);
}
