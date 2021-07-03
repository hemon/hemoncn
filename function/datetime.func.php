<?php

function isoTime($time = ''){
    if( empty($time) ){
        $time = date('Y-m-d H:i:s');
    } else {
        $time = date('Y-m-d H:i:s', strtotime($time));
    }
    return $time;
}

function dttm2unixtime( $dttm2timestamp_in )
{
    //returns unix time stamp for a given date time string that comes from DB
    list( $date, $time ) = split(" ", $dttm2timestamp_in);
    list( $year, $month, $day ) = split( "-", $date );
    list( $hour, $minute, $second ) = split( ":", $time );
    return mktime( $hour, $minute, $second, $month, $day, $year );
}

//generate a timestamp from a certain date by using patterns
function str2time($strStr, $strPattern = null)
{
    // an array of the valide date characters, see: http://php.net/date#AEN21898
    $arrCharacters = array(
    'd', // day
    'm', // month
    'y', // year, 2 digits
    'Y', // year, 4 digits
    'H', // hours
    'i', // minutes
    's'  // seconds
    );
    // transform the characters array to a string
    $strCharacters = implode('', $arrCharacters);
    // splits up the pattern by the date characters to get an array of the delimiters between the date characters
    $arrDelimiters = preg_split('~['.$strCharacters.']~', $strPattern);
    // transform the delimiters array to a string
    $strDelimiters = quotemeta(implode('', array_unique($arrDelimiters)));
    // splits up the date by the delimiters to get an array of the declaration
    $arrStr     = preg_split('~['.$strDelimiters.']~', $strStr);
    // splits up the pattern by the delimiters to get an array of the used characters
    $arrPattern = preg_split('~['.$strDelimiters.']~', $strPattern);
    // if the numbers of the two array are not the same, return false, because the cannot belong together
    if (count($arrStr) !== count($arrPattern)) {
        return false;
    }
    // creates a new array which has the keys from the $arrPattern array and the values from the $arrStr array
    $arrTime = array();
    for ($i = 0;$i < count($arrStr);$i++) {
        $arrTime[$arrPattern[$i]] = $arrStr[$i];
    }
    // gernerates a 4 digit year declaration of a 2 digit one by using the current year
    if (isset($arrTime['y']) && !isset($arrTime['Y'])) {
        $arrTime['Y'] = substr(date('Y'), 0, 2) . $arrTime['y'];
    }
    // if a declaration is empty, it will be filled with the current date declaration
    foreach ($arrCharacters as $strCharacter) {
        if (empty($arrTime[$strCharacter])) {
            $arrTime[$strCharacter] = date($strCharacter);
        }
    }
    // checks if the date is a valide date
    if (!checkdate($arrTime['m'], $arrTime['d'], $arrTime['Y'])) {
        return false;
    }
    // generates the timestamp
    $intTime = mktime($arrTime['H'], $arrTime['i'], $arrTime['s'], $arrTime['m'], $arrTime['d'], $arrTime['Y']);
    // returns the timestamp
    return $intTime;
}

function dateAdd($v, $d = null, $f = "Y-m-d"){ 
    $d = ($d ? $d : date("Y-m-d")); 
    return date($f, strtotime($v." days", strtotime($d))); 
}

function dateDiff($interval, $dateTimeBegin, $dateTimeEnd) {
    //Parse about any English textual datetime
    //$dateTimeBegin, $dateTimeEnd
    $dateTimeBegin=strtotime($dateTimeBegin);
    if($dateTimeBegin === -1) {
      return("..begin date Invalid");
    }
    $dateTimeEnd=strtotime($dateTimeEnd);
    if($dateTimeEnd === -1) {
      return("..end date Invalid");
    }
    $dif=$dateTimeEnd - $dateTimeBegin;
    switch($interval) {
      case "s"://seconds
          return($dif);
      case "n"://minutes
          return(floor($dif/60)); //60s=1m
      case "h"://hours
          return(floor($dif/3600)); //3600s=1h
      case "d"://days
          return(floor($dif/86400)); //86400s=1d
      case "ww"://Week
          return(floor($dif/604800)); //604800s=1week=1semana
      case "m": //similar result "m" dateDiff Microsoft
          $monthBegin=(date("Y",$dateTimeBegin)*12)+
            date("n",$dateTimeBegin);
          $monthEnd=(date("Y",$dateTimeEnd)*12)+
            date("n",$dateTimeEnd);
          $monthDiff=$monthEnd-$monthBegin;
          return($monthDiff);
      case "yyyy": //similar result "yyyy" dateDiff Microsoft
          return(date("Y",$dateTimeEnd) - date("Y",$dateTimeBegin));
      default:
          return(floor($dif/86400)); //86400s=1d
    }
}

?>
