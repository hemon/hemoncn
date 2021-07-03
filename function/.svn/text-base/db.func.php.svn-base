<?php
/*  1. 定义表
    $table = array(
        "name"   => "jiveuser", // 表名
        "pk"     => "username", // 主键
        "fields" => "", // 列名
    );
*/

function db($action, $table, $request, $autoExecute = 1, $page = 1, $n = 10, $cache = 0){
    $action = strtolower($action);
    $dml = array('update','delete','insert');
    
    if( $action == 'select' ){
        $sql = $action($table, $request, $page, $n);
        if($autoExecute){
            if($cache > 0){
                return $GLOBALS['db']->CachePageExecute($cache, $sql, $n, $page);
            } else {
                return $GLOBALS['db']->PageExecute($sql, $n, $page);
            }
        }
    } elseif ( in_array($action, $dml) ){
        $sql = $action($table, $request);
        if($autoExecute)
            return $GLOBALS['db']->Execute($sql);
    }
    
    return $sql;
}

function select($table, &$where = null, $page = 1, $n = 10, $fields = '*'){

    if( is_array($where) ){
        $fieldExists = array_keys_exists($table['fields'], $where);
        $where = implode_with_options($fieldExists, "", "=", "", " AND ");
    }
    
    if( !empty($where) ){
        $where = "WHERE $where";
    }
    
    $sql = "SELECT $fields FROM {$table['name']} $where";

    return $sql;
}

function update($table, &$request = null){
    if ( !array_key_exists($table['pk'], $request) ) {
        return false;
    }
    $fieldExists = array_keys_exists($table['fields'], &$request);
    $fieldValuePairs = implode_with_options($fieldExists, "", "=", "", ",");
    
    $sql = "UPDATE {$table['name']}
            SET $fieldValuePairs 
            WHERE {$table['pk']} = '{$request[$table['pk']]}'";
            
    return $sql;
}

function delete($table, &$request = null){
    if ( $request != null ){
        $fieldExists = array_keys_exists($this->table['fields'], &$request);
        $where = 'WHERE ' . implode_with_options($fieldExists, "", "=", "", " and ");
    }
    $sql = "DELETE FROM {$this->table['name']} $where";

    return $sql;
}

function insert($table, &$request){
    $fieldExists = array_keys_exists($table['fields'], &$request);
    $fields = implode(",", array_keys($fieldExists));
    $values = implode_with_options($fieldExists);
    
    $sql = "INSERT {$table['name']} ($fields) 
            VALUES ($values)";

    return $sql;
}

function getWhere($where){
    if( is_array($where) ){
        $fieldExists = array_keys_exists($table['fields'], $where);
        $where = implode_with_options($fieldExists, "", "=", "", " AND ");
    }
    
    if( !empty(trim($where)) ){
        $where = "WHERE $where";
    }
    return $where;
}

?>
