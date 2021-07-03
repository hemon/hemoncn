<?php

function my_array_intersect_key(){
    $argc = func_num_args();
    $args = func_get_args();
    // 找到最小的数组
    $index = 0;
    $min   = count($args[0]);
    for( $i = 1; $i < $argc; $i++ ){
        $array_count = count($args[$i]);
        if( $array_count < $min ){
            $index = $i;
            $min   = $array_count;
        }
    }
    // 遍历最小的数组
    foreach( $args[$index] as $key => $val ){
        // 广度优先，遍历所有其他数组
        for( $i = 0; $i < $argc; $i++ ){
            // 最小的数组本身，无须比较
            if( $index == $i ) continue;
            // 如果其中一个数组没有此键，则不是交集
            if( !array_key_exists($key, $args[$i]) ){
                continue 2;
            }
        }
        // 遍历完毕，所有数组都含有$key键，加入交集
        $array_intersect[$key] = $val;
    }
    
    return $array_intersect;
}
