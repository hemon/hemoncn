<?php
/*
    $this->meta = array(
        "pk"     => array('id'), // 主键
        "fields" => array('c1','c2'), // 列名
    ); 
*/

class DB 
{
    // ADO Connection
    var $dbo = null;
    // define table name
    var $table = '';
    // meta
    var $meta = array();
    // cache file path, with '/',
    var $cache = 'tmp/table/';
    
    function DB($table = null, $dbo = null){
        $this->setDbo($dbo);
        $this->setTable($table);
    }
    
    function setDbo($dbo = null){
        $this->dbo = ( is_object($dbo) ? $dbo : $this->dbo );
        if( empty($this->dbo) ){
            $this->dbo = $GLOBALS['db'];
        }
    }

    function setTable($table = null){
        $this->table = ifempty($table, $this->table);
        if( 2 != count($this->meta) ){
            $this->meta = $this->getMeta($this->table);
        }
    }
    
    function getMeta($table){
        // get table meta
        $meta = array();
        $cacheFile = $this->cache . $table;
        // if exsit cache
        if( is_file($cacheFile) ){
            $meta = unserialize(file_get_contents($cacheFile));
        } else {
            $metaColumns =& $this->dbo->MetaColumns($table);
    		foreach($metaColumns as $v) {
    		    $meta['fields'][] = $v->name;
    			if ( !empty($v->primary_key) ) $meta['pk'][] = $v->name;
    		}
    		// save cache
    		file_put_contents($cacheFile, serialize($meta));
        }
        return $meta;
    }
    
    function select($action='exec', $where=null, $order=null, $fields='*', 
                    $secs2cache=0, $p1=false, $p2=false, $p3=false){
        if ( !empty($order) ) {
            $order = ' ORDER BY ' . $order;
        }
        $where = $this->getWhere($where);
        $sql = "SELECT $fields FROM {$this->table} $where $order";
        return $this->execute($sql, $action, $secs2cache, $p1, $p2, $p3);
    }

    function execute($sql, $action='exec', $secs2cache=0, $p1=false, $p2=false, $p3=false){
        $actionMaps = array(
            'exec'  => 'Execute',     //$secs2cache,$sql=false,$inputarr=false
            'all'   => 'GetArray',    //$secs2cache,$sql=false,$inputarr=false
            'array' => 'GetArray',    //$secs2cache,$sql=false,$inputarr=false
            'one'   => 'GetOne',      //$secs2cache,$sql=false,$inputarr=false
            'row'   => 'GetRow',      //$secs2cache,$sql=false,$inputarr=false
            'col'   => 'GetCol',      //$secs2cache,$sql=false,$inputarr=false,$trim=false
            'assoc' => 'GetAssoc',    //$secs2cache,$sql=false,$force_array=false,$first2cols=false,$inputarr=false
            'page'  => 'PageExecute', //$secs2cache,$sql,$nrows,$page,$inputarr=false
            'limit' => 'SelectLimit', //$secs2cache,$sql,$nrows=-1,$offset=-1,$inputarr=false
        );
        $method = $actionMaps[$action];
        if( 0 < $secs2cache ){
            $method = 'Cache' . $method;
            $rs = $this->dbo->$method($secs2cache, $sql, $p1, $p2, $p3);
        } else {
            $rs = $this->dbo->$method($sql, $p1, $p2, $p3);
        }

        return $rs;
    }

    function selectPage($where, $page=1, $pageSize=10, $fields='*', $order=null, $cache='0'){
        $fields   = ifempty($where['fields']  , $fields);
        $order    = ifempty($where['order']   , $order);
        $cache    = ifempty($where['cache']   , $cache);
        $pageSize = ifempty($where['pageSize'], $pageSize);
        $page     = ifempty($where['page']    , $page);
        
        $rs = $this->select('page', $where, $order, $fields, $cache, $pageSize, $page);
        $data['data'] = $rs->GetArray();
        $data['page'] = $this->getPageInfo($rs);
        return $data;
    }
    
    function update(&$row, $where = null){
        $pkExists = array_keys_exists($this->meta['pk'], &$row);
        if ( count($pkExists) == count($this->meta['pk']) ) {
            if(!is_array($where)) $where = array();
            $where = array_merge($pkExists, $where);
        }
        $where = $this->getWhere($where);
        
        $fieldExists = array_keys_exists($this->meta['fields'], &$row);
        $fieldValuePairs = $this->implode($fieldExists, "", "=", "", ",");
        
        $sql = "UPDATE {$this->table} SET $fieldValuePairs $where";

        $this->dbo->Execute($sql);
        return $this->dbo->Affected_Rows();
    }
    
    function delete($where = null){
        $where = $this->getWhere($where);    
        $sql = "DELETE FROM {$this->table} $where";
                
        $this->dbo->Execute($sql);
        return $this->dbo->Affected_Rows();
    }
    
    function insert(&$row){
        $fieldExists = array_keys_exists($this->meta['fields'], &$row);
        $fields = $this->quoteFields(array_keys($fieldExists));
        $values = $this->implode($fieldExists, "", "", "", ",", 0, 1);
        
        $sql = "INSERT {$this->table} ($fields) VALUES ($values)";
        
        $this->dbo->Execute($sql);
        return $this->dbo->Insert_ID();
    }
    
    function getWhere($where){
        if( is_array($where) ){
            $fieldExists = array_keys_exists($this->meta['fields'], $where);
            $where = $this->implode($fieldExists, "", "=", "", " AND ");
        }
        
        $where = ( !empty($where) ? " WHERE $where" : "" );
        return $where;
    }
    
    function getPageInfo(&$rs){
        $pageInfo = array(
            'currentPage' => $rs->AbsolutePage(),
            'pageCount'   => $rs->LastPageNo(),
            'recordCount' => $rs->MaxRecordCount(),
            'pageSize'    => $rs->rowsPerPage,
            );
        return $pageInfo;
    }
    
    function implode(
        &$assoc_array, $prefix = '',    $k_v_glue = '', 
        $vwrap = '',   $seperator = '', $show_key = 1, 
        $qstr = 1,     $ig_empty = 1) 
    {
        foreach ($assoc_array as $k => $v) {
            $key = ( 1 == $show_key ? $k : '');
            $value = ( 1 == $qstr ? qstr($v) : $v);
            if(1 == $ig_empty && isEmpty($v)) continue;
            $tmp[] = $key . $k_v_glue . $vwrap . $value . strrev($vwrap);
        }
        if ( 0 < count($tmp) ){
            return $prefix . implode($seperator, $tmp);
        }
        return false;
    }
    
    function quoteFields($fields){
        if( '*' == $fields) return $fields;
        
        if( !is_array($fields) ){
            $fields = explode(',', $fields);
        }
        return $this->implode($fields, "", "", "`", ",", 0, 0);
    }
}

?>
