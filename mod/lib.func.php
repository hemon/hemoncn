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
    if( strlen($userid) >= 8 ) $isNewId = '��';
    $query = "?se=/{$isNewId}���߿�/֤�����&key=$userid";
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
    
    // isbn��ʽ��ȷ
    if( 4 > count(explode("-", $isbn)) ){
        return $noimg;
    }    
    // �Ѿ�����150���ͼƬ
    if( file_exists($img_150) ){
        return $img_150;
    }
    // ԭ�ļ����� �� ��ȡdoubanͼƬ
    if( file_exists($img) || getDoubanImg($isbn, $img) ){
        // ��������ͼ
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
            case '����': return '404 ��';
            case '��ʦ': return '107 ��';
            case '�ο�': return '304 ��';
        }
    }
    $local = array(
        'A' => array('�������塢ë��˼��','��ſ� ','311 �� ','3 ¥'),
        'C' => array('����ѧ����','���ſ� ','311 �� ','3 ¥'),
        'E' => array('����','һ�ſ� ','311 �� ','3 ¥'),
        'G' => array('�Ļ�����ѧ������������','306 �� ','311 �� ','1��3 ¥'),
        'I' => array('��ѧ','��ſ� ','311 �� ','3 ¥'),
        'K' => array('��ʷ������','���ſ� ','311 �� ','3 ¥'),
        'O' => array('�����ѧ�ͻ�ѧ','���ſ� ','411 �� ','3 ¥'),
        'Q' => array('�����ѧ','һ�ſ� ','411 �� ','3 ¥'),
        'S' => array('ũҵ��ѧ','һ�ſ� ','411 �� ','3 ¥'),
        'TB' => array('һ�㹤ҵ����','�ĺſ� ','411 �� ','2��3 ¥'),
        'TE' => array('ʯ�͡���Ȼ����ҵ','�ĺſ� ','411 �� ','2��3 ¥'),
        'TG' => array('����ѧ����������','�ĺſ� ','411 �� ','2��3 ¥'),
        'TJ' => array('������ҵ','','411 �� ','2��3 ¥'),
        'TL' => array('ԭ���ܼ���','�ĺſ� ','411 �� ','2��3 ¥'),
        'TN' => array('���ߵ���ӡ���Ѷ����','�ĺſ� ','411 �� ','2��3 ¥'),
        'TQ' => array('��ѧ��ҵ','206 �� ','411 �� ','2��3 ¥'),
        'TU' => array('������ѧ','���ſ� ','411 �� ','2��3 ¥'),
        'U' => array('��ͨ����','���ſ� ','411 �� ','2��3 ¥'),
        'X' => array('������ѧ ����ȫ��ѧ','���ſ� ','411 �� ','3 ¥'),
        'B' => array('��ѧ ���ڽ�','���ſ� ','311 �� ','3 ¥'),
        'D' => array('���Ρ�����','һ�ſ� ','311 �� ','3 ¥'),
        'F' => array('����','���ſ� ','311 �� ','1��3 ¥'),
        'H' => array('���� ������','306 �� ','311 �� ','1��3 ¥'),
        'J' => array('����','��ſ� ','311 �� ','3 ¥'),
        'N' => array('��Ȼ��ѧ����','���ſ� ','311 �� ','1��3 ¥'),
        'P' => array('����ѧ�������ѧ','һ�ſ� ','411 �� ','3 ¥'),
        'R' => array('ҽҩ������','һ�ſ� ','411 �� ','1��3 ¥'),
        'T' => array('��ҵ����','�ĺſ� ','411 �� ','2��3 ¥'),
        'TD' => array('��ҵ����','�ĺſ� ','411 �� ','2��3 ¥'),
        'TF' => array('ұ��ҵ','�ĺſ� ','411 �� ','2��3 ¥'),
        'TH' => array('��е���Ǳ���','�ĺſ� ','411 �� ','2��3 ¥'),
        'TK' => array('��������','�ĺſ� ','411 �� ','2��3 ¥'),
        'TM' => array('�繤����','�ĺſ� ','411 �� ','2��3 ¥'),
        'TP' => array('�Զ������������������','206 �� ','411 �� ','2��3 ¥'),
        'TS' => array('�Ṥҵ���ֹ�ҵ','206 �� ','411 �� ','2��3 ¥'),
        'TV' => array('ˮ������','206 �� ','411 �� ','2��3 ¥'),
        'V' => array('���ա�����','','411 �� ','2��3 ¥'),
        'Z' => array('�ۺ���ͼ��','���ſ� ','411 �� ','3 ¥'),
    );
    
    $libIndex = array('' => 1,'����' => 1,'����' => 2);
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
