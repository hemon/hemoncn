<?php
/**
 * google��ũ��ת�� 
 * @author    hemon <hemono@gmail.com> 
 * 
 * Usage:
 *   // ����1983-10-5תũ��
 *   echo lunar('1983-10-5');
 *   echo lunar('1983��10��05��'); 
 *   echo lunar('һ�Ű�����ʮ������');
 *   // ũ��1983-8-29ת����
 *   echo lunar('1983-8-29', 1, 3); 
 *
 * @param string ����
 * @param int    ��������
 *      - 0 ����
 *        1 ũ��  
 * @param int    �����ʽ
 *      - 0 ũ������
 *        1 ��֧
 *        2 ����
 *        3 ��������
 *        4 ����   
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
        $char = array("һ","��","��","��","��","��","��","��","��","ʮ","��","��","إ","��","��","��"); 
        $num = array("1","2","3","4","5","6","7","8","9","","0","0","2","-","-","");
        return date("Y-m-d", strtotime(str_replace($char, $num, $date[$format])) );
    }
    return false;
}
?>
