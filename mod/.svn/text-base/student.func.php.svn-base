<?php
function getStudent($key, $page = 1, $n = 10){
    $where = getWhere($key);
    
	$sql = "SELECT sid,classcode,name,sex,class,
                   native,nation,birthday,email,img,unit
            FROM usr
            WHERE $where";

	$rs = $GLOBALS['db']->CachePageExecute(0, $sql, $n, $page);
	return $rs;
}

function getWhere($key){
    $key = explode(" ", $key);
    foreach($key as $k){
        if(substr($k, -2, 2) == 'Че') {
            $k = " $k";
        }
        $where .= "search like '%$k%' AND ";
    }
    if( !empty($_REQUEST['grade']) ){
        $where .= "classcode like '{$_REQUEST['grade']}%' AND ";
    }
    $where = substr($where, 0, strlen($where)-5);
    return $where;
}

function getImage($img, $size = 'main'){
    if($img == 'http://head.xiaonei.com/photos/0/0/head.jpg') 
        return '#';
        
    if( strpos($img, 'xiaonei') ){
        $image = getXiaoneiImg($img, $size);
    } elseif( strpos($img, 'zhanzuo') ){
        $image = getZhanzuoImg($img, $size);
    }
    return $image;
}

function getXiaoneiImg($img, $size){

    switch (TRUE){
        case strpos($img, 'head_') :
            $image = str_replace("head", $size, $img);
            break;
        case preg_match("/head(\d+)/", $img, $matches) :
            $offset = array( 'main' => 1, 'large' => 2);
            $image = preg_replace("/head(\d+)/", $size .  ($matches[1]-$offset[$size]), $img);
            break;
        case preg_match("/min(\d+)_/", $img, $matches):
             $image = preg_replace("/min(\d+)_/", "mid_", $img);
            break;
    }
    return $image;
}

function getZhanzuoImg($img, $size){
    $imgFile = array( 'main' => 'l.jpg', 'large' => 'bb.jpg');
    $image = str_replace("s.jpg", $imgFile[$size], $img);
    return $image;
}

function getRandStudent($rand = 10){
	$sql = "SELECT sid,classcode,name,sex,class,native,nation,birthday,email,img
            FROM usr
            WHERE img IS NOT NULL
            ORDER BY rand() LIMIT $rand";
            
	$rs = $GLOBALS['db']->Execute($sql);
	return $rs;
}
?>
