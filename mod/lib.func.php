<?php
require_once('function/cmd.func.php');
require_once('function/curl.func.php');
require_once('function/img.func.php');
require_once('function/sphinx.func.php');
require_once('mod/lib/cnmarc.func.php');

define("CGI", 'http://172.16.15.155:8100/dtcgibin/cgi32.exe');

function parseList($html){
    $reg = '/\<TR>\<NOBR>\<TD ALIGN=RIGHT>\<A HREF="\/dtcgibin\/cgi32.exe(.*?)">\d{5}	\<\/A>\<\/TD>\<TD>(.*?)\<\/TD>\<TD>(.*?)\<\/TD>\<TD>(.*?)\<\/TD>\<TD>(.*?)\<\/TD>(?:\<TD>(.*?)\<\/TD>)? \<\/NOBR>\<\/TR>/';
    preg_match_all ($reg, $html, $matches, PREG_SET_ORDER);
    //$list['data'] = array_sort($matches, 6);
    $list['data'] = $matches;
    
    $reg = '/\<A HREF="\/dtcgibin\/cgi32.exe\?cse=(.*?)">/';
    if( preg_match($reg, $html, $matches) ){
        $list['more'] = '?cse=' . $matches[1];
    }
    return $list;
}

function getUser($userid){
    if( strlen($userid) >= 8 ) $isNewId = '新';
    $query = "?se=/{$isNewId}读者库/证条码号&key=$userid";
    $url = CGI . $query;
    $html = curl_proxy($url);
    if($html = curl_proxy($url)){
        $list = parseList($html);
    } else {
        return false;
    }
    
    if( 0 < count($list) ){
        foreach($list['data'] as $row){
            $query = $row[1];
            $url = CGI . $query;
            $html = curl_proxy($url);
            if($html = curl_proxy($url)){
                return cnmarc($html);
            } else {
                return false;
            }
        }
    }
    return cnmarc($html);
}

function formatBookId($bookid){
    return str_pad($bookid, 7, '0', STR_PAD_LEFT);
}

function getBookImg($isbn){
    $img = TMP . "isbn/$isbn.jpg";
    $img_150 = TMP . "isbn_150/{$isbn}.jpg";
    $noimg = "images/noimg.gif";
    
    // isbn格式正确
    if( 4 > count(explode("-", $isbn)) ){
        return $noimg;
    }    
    // 已经存在150宽度图片
    if( file_exists($img_150) ){
        return $img_150;
    }
    // 原文件存在 或 获取douban图片
    if( file_exists($img) || getDoubanImg($isbn, $img) ){
        // 创建缩略图
        return thumbBookImg($img, $img_150, 150);
    }
    return $noimg;
}


function getDoubanImg($isbn, $filename){
    $url = "http://www.douban.com/isbn/$isbn/";
    $html = curl($url, $options);
    if( preg_match('/http\:\/\/otho\.douban\.com\/lpic\/s\d+\.jpg/', $html, $matches) ){
        $imgUrl = $matches[0];
        $hFile = fopen($filename, 'w');
        $options = array(CURLOPT_FILE => $hFile);
        curl($imgUrl, $options);
        return $filename;
    } else {
        return false;
    }
}

function localType($key, $index, $type = 'lib'){
    if( $type == 'lib' and !is_int($index) ){
        switch ($index){
            case '光盘': return '404 室';
            case '教师': return '107 室';
            case '参考': return '304 室';
        }
    }
    $local = array(
        'A' => array('马列主义、毛泽东思想','五号库 ','311 室 ','3 楼'),
        'C' => array('社会科学总论','二号库 ','311 室 ','3 楼'),
        'E' => array('军事','一号库 ','311 室 ','3 楼'),
        'G' => array('文化、科学、教育、体育','306 室 ','311 室 ','1、3 楼'),
        'I' => array('文学','五号库 ','311 室 ','3 楼'),
        'K' => array('历史、地理','二号库 ','311 室 ','3 楼'),
        'O' => array('数理科学和化学','二号库 ','411 室 ','3 楼'),
        'Q' => array('生物科学','一号库 ','411 室 ','3 楼'),
        'S' => array('农业科学','一号库 ','411 室 ','3 楼'),
        'TB' => array('一般工业技术','四号库 ','411 室 ','2、3 楼'),
        'TE' => array('石油、天然气工业','四号库 ','411 室 ','2、3 楼'),
        'TG' => array('金属学、金属工艺','四号库 ','411 室 ','2、3 楼'),
        'TJ' => array('武器工业','','411 室 ','2、3 楼'),
        'TL' => array('原子能技术','四号库 ','411 室 ','2、3 楼'),
        'TN' => array('无线电电子、电讯技术','四号库 ','411 室 ','2、3 楼'),
        'TQ' => array('化学工业','206 室 ','411 室 ','2、3 楼'),
        'TU' => array('建筑科学','三号库 ','411 室 ','2、3 楼'),
        'U' => array('交通运输','二号库 ','411 室 ','2、3 楼'),
        'X' => array('环境科学 、安全科学','二号库 ','411 室 ','3 楼'),
        'B' => array('哲学 、宗教','六号库 ','311 室 ','3 楼'),
        'D' => array('政治、法律','一号库 ','311 室 ','3 楼'),
        'F' => array('经济','六号库 ','311 室 ','1、3 楼'),
        'H' => array('语言 、文字','306 室 ','311 室 ','1、3 楼'),
        'J' => array('艺术','五号库 ','311 室 ','3 楼'),
        'N' => array('自然科学总论','二号库 ','311 室 ','1、3 楼'),
        'P' => array('天文学、地球科学','一号库 ','411 室 ','3 楼'),
        'R' => array('医药、卫生','一号库 ','411 室 ','1、3 楼'),
        'T' => array('工业技术','四号库 ','411 室 ','2、3 楼'),
        'TD' => array('矿业工程','四号库 ','411 室 ','2、3 楼'),
        'TF' => array('冶金工业','四号库 ','411 室 ','2、3 楼'),
        'TH' => array('机械、仪表工艺','四号库 ','411 室 ','2、3 楼'),
        'TK' => array('动力工程','四号库 ','411 室 ','2、3 楼'),
        'TM' => array('电工技术','四号库 ','411 室 ','2、3 楼'),
        'TP' => array('自动化技术、计算机技术','206 室 ','411 室 ','2、3 楼'),
        'TS' => array('轻工业、手工业','206 室 ','411 室 ','2、3 楼'),
        'TV' => array('水利工程','206 室 ','411 室 ','2、3 楼'),
        'V' => array('航空、航天','','411 室 ','2、3 楼'),
        'Z' => array('综合性图书','二号库 ','411 室 ','3 楼'),
    );
    
    $libIndex = array('' => 1,'南区' => 1,'北区' => 2);
    if( preg_match('/^([A-Z]+)/', $key, $matches) ){
        $key = $matches[1];
        $index = (is_int($index) ? $index : $libIndex[$index] );
        return $local[$key][$index];
    }
}

function douban($isbn){
    $url = "http://www.douban.com/isbn/$isbn/";
    $html = curl_cache($url, null, 86400, $url);
    $html = preg_replace("'<script[^>]*?>.*?</script>'si", "", $html);
    preg_match('/\<div id="tablem">(.*)\<div id="tablerm">/s', $html, $matches);
    $html = '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><body>' . $matches[1] . '<script>parent.douban();</script></body></html>';
    return $html;
}
?>
