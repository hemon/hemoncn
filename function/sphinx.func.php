<?php
require_once('lib/SphinxClient.php');

function sphinx_query($query, $index, $options = array()){
    $_opt = array(
        "ArrayResult"      => '', //( bool $array_result )
        "ConnectTimeout"   => '', //( float $timeout )
        "FieldWeights"     => '', //( array $weights )
        "Filter"           => '', //( string $attribute , array $values [, bool $exclude ] )
        "FilterFloatRange" => '', //( string $attribute , float $min , float $max [, bool $exclude ] )
        "FilterRange"      => '', //( string $attribute , int $min , int $max [, bool $exclude ] )
        "GeoAnchor"        => '', //( string $attrlat , string $attrlong , float $latitude , float $longitude )
        "GroupBy"          => '', //( string $attribute , int $func [, string $groupsort ] )
        "GroupDistinct"    => '', //( string $attribute )
        "IDRange"          => '', //( int $min , int $max )
        "IndexWeights"     => '', //( array $weights )
        "Limits"           => '', //( int $offset , int $limit [, int $max [, int $cutoff ]] )
        "MatchMode"        => '', //( int $mode )
        "MaxQueryTime"     => '', //( int $qtime )
        "RankingMode"      => '', //( int $ranker )
        "Retries"          => '', //( int $count [, int $delay ] )
        "Server"           => '', //( string $server , int $port )
        "SortMode"         => '', //( int $mode [, string $sortby ] )
    );
    
    if( !empty($options) ) $_opt = array_intersect_key($options, $_opt);
    
    $sphinx = new SphinxClient;
    foreach($_opt as $opt => $val){
        if( empty($val)     ) continue;
        if( !is_array($val) ) $val = explode(',', $val);
        
        call_user_func_array(
            array($sphinx, 'set' . $opt), 
            $val
        );
    }
    
    return $sphinx->query($query, $index);
}

?>
