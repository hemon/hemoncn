<?php
//行：7 * 列：9
//dino8 tiger7 cow6 horse5 dog4 dog3 chick2 snake1
//dino8 tiger7 cow6 horse5 dog4 snake3 chick2 rat1
$c .= 'cow_left,blank,dino_left,blank,blank,blank,rat_right,blank,tiger_right';
$c .= ',blank,chick_left,blank,blank,blank,blank,blank,snake_right,blank';
$c .= ',blank,blank,dog_left,blank,blank,blank,horse_right,blank,blank';
$c .= ',home,blank,blank,blank,blank,blank,blank,blank,home';
$c .= ',blank,blank,horse_left,blank,blank,blank,dog_right,blank,blank';
$c .= ',blank,snake_left,blank,blank,blank,blank,blank,chick_right,blank';
$c .= ',tiger_left,blank,rat_left,blank,blank,blank,dino_right,blank,cow_right';
function GetIP() { //获取IP
global $_SERVER;
   if ($_SERVER["HTTP_X_FORWARDED_FOR"])
      $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
   else if ($_SERVER["HTTP_CLIENT_IP"])
       $ip = $_SERVER["HTTP_CLIENT_IP"];
   else if (getenv("HTTP_X_FORWARDED_FOR"))
       $ip = getenv("HTTP_X_FORWARDED_FOR");
   else if ($_SERVER["REMOTE_ADDR"])
       $ip = $_SERVER["REMOTE_ADDR"];
   else if (getenv("HTTP_CLIENT_IP"))
       $ip = getenv("HTTP_CLIENT_IP");
   else if (getenv("REMOTE_ADDR"))
       $ip = getenv("REMOTE_ADDR");
   else
       $ip = "Unknown";
   return $ip;
}
?>