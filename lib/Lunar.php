<?php
/**
* 公农历转换(1912 - 2012) 
* @author    hemon <hemono@gmail.com> 
* @Id: Lunar.php,v 1.0 2007/10/04 01:41:39$
* 
* Usage:
*   // 公历1983-10-5转农历
*   $lunar = new Lunar();
*   $date = $lunar->GetLar('1983-10-5',0);
*   echo date("Y-m-d", $date);
*   // 农历1983-8-29转公历
*   $date = $lunar->GetLar('1983-8-29',1);
*   echo date("Y-m-d", $date);
*/

class Lunar {
    var $LMDay = array();
    var $InterMonth = 0;
    var $InterMonthDays = 0;
    var $SLRangeDay = 0;

    var $SMDay = array(1 => 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    var $LongLife = array( 1 =>
    '132637048', '133365036', '053365225', '132900044', '131386034', '022778122', //6
    '132395041', '071175231', '131175050', '132635038', '052891127', '131701046', //12
    '131748035', '042741223', '130694043', '132391032', '021327122', '131175040', //18
    '061623129', '133402047', '133402036', '051769125', '131453044', '130694034', //24
    '032158223', '132350041', '073213230', '133221049', '133402038', '063466226', //30
    '132901045', '131130035', '042651224', '130605043', '132349032', '023371121', //36
    '132709040', '072901128', '131738047', '132901036', '051333226', '131210044', //42
    '132651033', '031111223', '131323042', '082714130', '133733048', '131706038', //48
    '062794127', '132741045', '131206035', '042734124', '132647043', '131318032', //54
    '033878120', '133477039', '071461129', '131386047', '132413036', '051245126', //60
    '131197045', '132637033', '043405122', '133365041', '083413130', '132900048', //66
    '132922037', '062394227', '132395046', '131179035', '042711124', '132635043', //72
    '102855132', '131701050', '131748039', '062804128', '132742047', '132359036', //78
    '051199126', '131175045', '131611034', '031866122', '133749040', '081717130', //84
    '131452049', '132742037', '052413127', '132350046', '133222035', '043477123', //90
    '133402042', '133493031', '021877121', '131386039', '072747128', '130605048', //96
    '132349037', '053243125', '132709044', '132890033');
    
    /**
     * 日历转换
     *      
     * @param string 日期
     * @param int    日期历法
     *      - 0 公历
     *        1 农历
     *
     * @return timestamp
     */
    function GetLar($date, $isLunar = 1){
        list($year, $month, $day) = split("-", $date);
        if($isLunar == 1)
            return $this->Lunar2Solar($year, $month, $day);
        else
            return $this->Solar2Lunar($year, $month, $day);
    }
    
    function IsLeapYear($AYear){
      return ($AYear % 4 == 0) and (($AYear % 100 <> 0) or ($AYear % 400 == 0));
    }
    
    function CovertLunarMonth($magicno){
         $m = $magicno;
         for ($i = 12; $i >= 1; $i--){
             $size = $m % 2;
             if ($size == 0)
                $this->LMDay[$i] = 29;
             else
                $this->LMDay[$i] = 30;
             $m = floor($m / 2);
         }
    }
    
    function ProcessMagicStr($yy){
         $yy = $yy - 1911;
         $magicstr = $this->LongLife[$yy];
         $this->InterMonth = substr($magicstr, 0, 2);
         $LunarMonth = substr($magicstr, 2, 4);
         $this->CovertLunarMonth($LunarMonth);
         $dsize = substr($magicstr, 6, 1);
         switch ($dsize) {
              case 0 : 
                $this->InterMonthDays = 0;
                break;
              case 1 : 
                $this->InterMonthDays = 29;
                break;
              case 2 : 
                $this->InterMonthDays = 30;
                break;
         }
         $this->SLRangeDay = substr($magicstr, 7, 2);
    }
    
    function DaysPerLunarMonth($LYear, $LMonth){
         $this->ProcessMagicStr($LYear);
         if ($LMonth < 0)
            return $this->InterMonthDays;
         else
            return $this->LMDay[$LMonth];
    }
    
    function Solar2Lunar($SYear, $SMonth, $SDay){
         if( !(1912 <= $SYear && $SYear <= 2012) ){
            return false;
         }
         $day = 0;
         if ($this->IsLeapYear($SYear))
            $this->SMDay[2] = 29;
         $this->ProcessMagicStr($SYear);
         if ($SMonth == 1)
            $day = $SDay;
         else {
            for($i = 1; $i <= $SMonth-1; $i++)
                $day = $day + $this->SMDay[$i];
            $day = $day + $SDay;
         }
         if ($day <= $this->SLRangeDay) {
            $day = $day - $this->SLRangeDay;
            $this->ProcessMagicstr($SYear-1);
            for ($i = 12; $i >= 1; $i--){
                $day = $day + $this->LMDay[$i];
                if ($day > 0)
                   break;
            }
            $LYear = $SYear - 1;
            $LMonth = $i;
            $LDay = $day;
         } else {
            $day = $day - $this->SLRangeDay;
            for($i = 1; $i <= $this->InterMonth-1; $i++){
                $day = $day - $this->LMDay[$i];
                if ($day <= 0)
                   break;
            }
            if ($day <= 0) {
               $LYear = $SYear;
               $LMonth = $i;
               $LDay = $day + $this->LMDay[$i];
            } else {
               $day = $day - $this->LMDay[$this->InterMonth];
               if ($day <= 0) {
                  $LYear = $SYear;
                  $LMonth = $this->InterMonth;
                  $LDay = $day + $this->LMDay[$this->InterMonth];
               } else {
                  $this->LMDay[$this->InterMonth] = $this->InterMonthDays;
                  for ($i = $this->InterMonth; $i <= 12; $i++){
                      $day = $day - $this->LMDay[$i];
                      if ($day <= 0)
                         break;
                  }
                  if ($i == $this->InterMonth)
                     $LMonth = 0 - $this->InterMonth;
                  else
                     $LMonth = $i;
                  $LYear = $SYear;
                  $LDay = $day + $this->LMDay[$i];
               }
            }
         }
         return mktime(0, 0, 0, $LMonth, $LDay, $LYear);
    }
    
    function Lunar2Solar($LYear, $LMonth, $LDay){
         if( !(1912 <= $LYear && $LYear <= 2012) ){
            return false;
         }
         $day = 0;
         $SYear = $LYear;
         if ($this->IsLeapYear($SYear))
            $this->SMDay[2] = 29;
         $this->processmagicstr($SYear);
         if ($LMonth < 0)
            $day = $this->LMDay[$this->InterMonth];
         if ($LMonth <> 1)
            for ($i = 1; $i <= $LMonth-1; $i++)
                $day = $day + $this->LMDay[$i];
         $day = $day + $LDay + $this->SLRangeDay;
         if (($this->InterMonth <> 13) and ($this->InterMonth < $LMonth))
            $day = $day + $this->InterMonthDays;
         for ($i = 1; $i <= 12; $i++){
             $day = $day - $this->SMDay[$i];
             if ($day <= 0)
                break;
         }
         if ($day > 0) {
            $SYear = $SYear + 1;
            if ($this->IsLeapYear($SYear))
               $this->SMDay[2] = 29;
            for ($i = 1; $i <= 12; $i++){
                $day = $day - $this->SMDay[$i];
                if ($day <= 0)
                   break;
            }
         }
         $day = $day + $this->SMDay[$i];
         $SMonth = $i;
         $SDay = $day;
         return mktime(0, 0, 0, $SMonth, $SDay, $SYear);
    }
}
?>
