<?php
/**
 * google公农历转换 
 * @author    hemon <hemono@gmail.com> 
 * 
 * Usage:
 *   // 公历1983-10-5转农历
 *   echo lunar('1983-10-5');
 *   echo lunar('1983年10月05日'); 
 *   echo lunar('一九八三年十月五日');
 *   // 农历1983-8-29转公历
 *   echo lunar('1983-8-29', 1, 3); 
 *
 * @param string 日期
 * @param int    日期历法
 *      - 0 公历
 *        1 农历  
 * @param int    输出格式
 *      - 0 农历日期
 *        1 干支
 *        2 属相
 *        3 公历日期
 *        4 星期   
 *
 * @return string
 */
function lunar($date, $cal = 0, $format = 0){
    $cals = array('%E5%85%AC%E5%8E%86','%E5%86%9C%E5%8E%86');
    $pattern = '/\<table border=0 cellpadding=0 cellspacing=0>\<tr>\<td width=47 valign=middle>\<img border=0 width=40 height=30 alt="" valign=middle src="\/images\/lunar\/lunar_animal_\d+.gif">\<\/td>\<td valign=top>\<font size=-1>\<table cellspacing=0 cellpadding=2 border=0>\<tr>\<td>\<font size=-1>\S+ (.*)\<\/font>\<\/td>\<td>\<font size=-1>(.+)(.)\<\/font>\<\/td>\<\/tr>\<tr>\<td>\<font size=-1>\S+ (.*)\<\/font>\<\/td>\<td>\<font size=-1>(.*)\<br>\<\/font>\<\/td>\<\/tr>\<\/table>\<\/font>\<\/td>\<\/tr>\<\/table>/';
    do {
        $html = file_get_contents("http://www.google.cn/search?q={$cals[$cal]}$date");
        if( $html != false ) break;
    } while(true);
    
    if(preg_match($pattern, $html, $date)){
        array_shift($date);
        $char = array("一","二","三","四","五","六","七","八","九","十","零","○","廿","年","月","日"); 
        $num = array("1","2","3","4","5","6","7","8","9","","0","0","2","-","-","");
        return date("Y-m-d", strtotime(str_replace($char, $num, $date[$format])) );
    }
    return false;
}
?>
