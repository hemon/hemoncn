<?php

function thumbBookImg($srcImg, $dstImg, $def_width = 150){
    list($width, $height, $type, $attr) = getimagesize($srcImg);
    if( $def_width < $width ){
        $rate = $def_width/$width*100;
        thumbImg($srcImg, $dstImg, $rate);
    } else {
        copy($srcImg, $dstImg);
    }
    return $dstImg;
}

function thumbImg($srcImg, $dstImg, $width = 100){
    require_once 'lib/ThumbHandler.php';
    $t = new ThumbHandler();
	$t->setSrcImg($srcImg);
    $t->setCutType(1);
    $t->setDstImg($dstImg);
    $t->createImg($width);
    return $dstImg;
}

?>
