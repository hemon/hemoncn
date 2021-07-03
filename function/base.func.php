<?php
/**test
$hex = 'FFFFFFFFFFFFFFFF';
$dec = base_convert($hex, 16, 10);
$base = '36';

echo
$dec, "\n",
base_convert($hex, 16, $base), "\n", 
base_convert(base_convert($hex, 16, $base), $base, 10), "\n",
dec2base($dec, $base), "\n", 
base2dec(dec2base($dec, $base), $base), "\n",
bc_dec2base($dec, $base), "\n", 
bc_base2dec(bc_dec2base($dec, $base), $base), "\n"
;
*/

function digits($base) {
    switch (1) {
        case $base < 2 || 255 < $base:
            return false;
        case $base <= 62:
            $digits = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case $base <= 65:
            $digits .= '-_.';             // url not encode
            break;
        case $base <= 80:
            $digits .= '!$(*+,/:;=?@[]~'; // mobile url
            break;
        case $base <= 87:
            $digits .= '&\')^{|}';        // url
            break;
        case $base <= 93:
            $digits .= '%#<>\`';          // char
            break;
        case $base < 256:
            for($i = 1; $i <= $base; $i++) {
                $digits .= chr($i);
            }
    }
    return substr($digits, 0, $base);
}

function bc_dec2base($dec, $base, $digits=false) {
    if($base < 2 or $base > 256) die("Invalid Base: ".$base);
    bcscale(0);
    $value = "";
    if(!$digits) $digits = digits($base);
    while($dec > $base-1) {
        $rest  = bcmod($dec,$base);
        $dec   = bcdiv($dec,$base);
        $value = $digits[$rest].$value;
    }
    $value = $digits[intval($dec)].$value;
    return (string) $value;
}

function bc_base2dec($value, $base, $digits=false) {
    if($base < 2 or $base > 256) die("Invalid Base: ".$base);
    bcscale(0);
    if(!$digits) {
        $digits = digits($base);
        if($base < 37) {
            $value = strtolower($value);
        }
    }
    $size = strlen($value);
    $dec  = "0";
    for($loop = 0; $loop < $size; $loop++) {
        $element = strpos($digits, $value[$loop]);
        $power   = bcpow($base, $size-$loop-1);
        $dec     = bcadd($dec, bcmul($element, $power));
    }
    return (string) $dec;
}

function dec2base( $dec, $base, $digits=false ) {
    if(function_exists('bcscale')){
        return bc_dec2base($dec, $base, $digits);
    }
    $digits = (!$digits ? digits($base) : $digits );
    $out = "";
    for ( $t = floor( log10( $dec ) / log10( $base ) ); $t >= 0; $t-- ) {
        $a = floor( $dec / pow( $base, $t ) );
        $out = $out . substr( $digits, $a, 1 );
        $dec = $dec - ( $a * pow( $base, $t ) );
    }
    return $out;
}

function base2dec( $dec, $base, $digits=false ) {
    if(function_exists('bcscale')){
        return bc_base2dec($dec, $base, $digits);
    }
    
    $digits = (!$digits ? digits($base) : $digits );
    $out = 0;
    $len = strlen( $dec ) - 1;
    for ( $t = 0; $t <= $len; $t++ ) {
        $out = $out + strpos( $digits, substr( $dec, $t, 1 ) ) * pow( $base, $len - $t );
    }
    return $out;
}

?>
