<?php

function getList($key, $query = null, $se = '%所有库%'){
    $key = "?se=$se&key=" . urlencode($key); 
    $query = ifempty($query, $key);
    $url = CGI . $query;
    if($html = curl_proxy($url)){
        return parseList($html);
    } else {
        return false;
    }
}

function getBook($id, $type = 'bookid', $cache = 0){
    $id = formatBookId($id);
    $query = array(
        'bookid'  => "?serxc+/图书总库/记录索引号/$id",
        'loginid' => "?se=/图书总库/登录号&Key=$id",
    );

    if( isset($query[$type]) ){
        $url = CGI . $query[$type];
        if( $html = curl_proxy($url, $cache) ){
            return cnmarc($html);
        } else {
            return false;
        }
    }
}
?>
