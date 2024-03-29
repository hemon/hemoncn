<?php
require_once 'function/sphinx.func.php';
require_once('function/serialize.func.php');

function getList($key, $page = 1, $pageSize = 20, $ttl = 3600){
    $offset = ( 1 < $page ? ($page - 1) * $pageSize : 0);
    $options = array(
        "Server"   => array('127.0.0.1', 3312),
        "Limits"   => array($offset, 20),
        "SortMode" => array(SPH_SORT_EXTENDED, "@rank DESC, @id DESC"),
    );

    $key  = iconv("GBK", "UTF-8", $key); 
    $data = sphinx_query($key, 'lib', $options);
    $ids  = array_keys($data['matches']);
    
    $list = array(
        'data'   => getBook($ids),
        'status' => $data,
    );
    return $list;
}

function getBook($ids, $type = 'bookid'){
    $doc = sphinx_doc($ids, 'lib_book');
    
    if( is_array($ids) ){
        return array_map('safe_unserialize', $doc);
    } else {
        return safe_unserialize($doc);
    }
}

function sphinx_doc($ids, $doc, $ttl = 86400){
    if( is_array($ids) ){
        $fields = 'id, doc';
        $where  = 'id in ('.implode(',', $ids).')';
        $get    = 'CacheGetAssoc';
    } else {
        $fields = 'doc';
        $where  = "id = $ids";
        $get    = 'CacheGetOne';
    }

    $sql = "select $fields from $doc where $where";
    return $GLOBALS['db']->$get($ttl, $sql);
}

?>
