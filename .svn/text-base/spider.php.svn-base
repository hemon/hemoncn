<?php
require_once 'config.php';
require_once 'function/global.func.php';
require_once 'mod/lib.func.php';

$start = $argv[1];
$end   = $argv[2];

define("BOOKID", "tmp/bookid_{$start}_{$end}");

$start = (is_file(BOOKID) ? (int)file_get_contents(BOOKID) : $start);

for($bookid = $start; $bookid < $end; $bookid++){
    //echo $bookid, "\n"; 
    if( $book = getBook($bookid, 'bookid', 0) ){
        $marc = serialize($book);
        $insertSQL = "insert into lib_book values('$bookid', '$marc')";
        $GLOBALS['db']->Execute($insertSQL);
    }
    file_put_contents(BOOKID, $bookid);
}

?>
