在MySQL中，索引块通常是1024个字节，<br />
数据指针通常是4个字节，<br />
这对于有一个长度为3(中等整数)的索引的500,000行的表，<br />
通过公式可以计算出<br />
log(500,000)/log(1024/3*2/(3+4))+1= 4次搜索。<br /><br />

<?php

$index_block_length  = 1024;
$data_pointer_length = 4;

$row_count    = 10000;
$index_length = 18;


echo log($row_count)/log($index_block_length/3 * 2/($index_length + $data_pointer_length))+1;
