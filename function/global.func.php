<?php
function isUTF8($string) {
    if (is_array($string)) {
        $enc = implode('', $string);
        return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
    } else {
        return (utf8_encode(utf8_decode($string)) == $string);
    }
}

function array_keys_exists($keys, &$array, $ig_empty = true){
    if( !is_array($keys) ){
        $keys = explode(",", $keys);
    }
    
    $exists = array();
    foreach ( $keys as &$key )
    {
        // 不存在此索引
        if ( !array_key_exists($key, $array) ) {
            continue;
        } else {
            // 存在索引，但元素为空
            if( $ig_empty == true && isEmpty($array[$key]) )
                continue;
        }
        // 否则，保存进$exists
        $exists[$key] = $array[$key];
    }
    return $exists;
}

function array_find_key(&$key, &$array, $igEmpty=true) {
    $find = array();
    foreach($key as $k){
        // 检验索引存在
        if (!array_key_exists($k, $array)) {
            continue;
        }
        // 忽略空值
        if ( true == $igEmpty && isEmpty($array[$k]) ) {
            continue;
        }
        $find[$k] = $array[$k];
    }
    return $find;
}

function query( $keys = '' ){
    $keys = explode(',',$keys);
    if( !empty($keys) ){
        $date = array_find_key($keys, $_REQUEST);
    } else {
        $date = $_REQUEST;
    }
    return http_build_query($date);
}

function isSid($sid){
    return preg_match("/^\d{14}$/", $sid);
}

function is_mobile($str)
{
    return preg_match("/^((\(\d{3}\))|(\d{3}\-))?1(3|5)\d{9}$/", $str);
} 

function is_phone($str)
{
    return preg_match("/^0[1-9]\d{9}$/", $str);
}

function getLastTerm($maxTerm = null)
{
    $today = getdate();
    $term['year'] = $today['year'];
    $term['term'] = '1';
    // 学年下学期（3-7月）
    if ( 3 < $today['mon'] && $today['mon'] < 10 ) {
        $term['term'] = '2';
    }
    // 9月，新学年开年，之前仍为上一学年
    if ( 10 > $today['mon'] ) {
        $term['year'] = $today['year'] - 1;
    }
    $term = $term['year'] . '.' . $term['term'];
    if( !is_null($maxTerm) && $term > $maxTerm ){
        $term = $maxTerm;
    }
    return $term;
}

function getThisTerm($lastTerm, $maxTerm){
    if( !empty($_REQUEST['term']) ){
    	$lastTerm = $_REQUEST['term'];
    }
    if( $lastTerm > $maxTerm ){
        $lastTerm = $maxTerm;
    }
    return $lastTerm;
}

function getMaxTerm($student){
    $year = substr($student['cid'], 0, 4) + 4;
    $maxTerm = $year . '.2';
    return $maxTerm;
}

function getTerm($beginTerm, $lastTerm)
{
	$beginYear = substr($beginTerm, 0, 4);
	$lastYear = substr($lastTerm, 0, 4);
	$allTerm = array();
	for($year = $lastYear; $year >= $beginYear; $year--){
		for($i = 2; $i >= 1; $i--){
			$term = $year . '.' . $i;
			if($term <= $lastTerm){
				$allTerm[] = $term;
			}
		}
	}
    return $allTerm;
}

function processed($start){
	$end =  microtime();
	$start = explode(' ',$start);
	$end = explode(' ',$end);
	$processed = $end[0]+trim($end[1]) - $start[0]-trim($start[1]);
	return sprintf("%8.6f", $processed);
}

function implode_with_options(&$assoc_array, $prefix = '',    $k_v_glue = '', 
                                $vwrap = '', $seperator = '', $show_key = 1, 
                                $qstr = 1,   $ig_empty = 1) {
    foreach ($assoc_array as $k => $v) {
        $key = ( 1 == $show_key ? $k : '');
        $value = ( 1 == $qstr ? qstr($v) : $v);
        if(1 == $ig_empty && isEmpty($v)) continue;
        $tmp[] = $key . $k_v_glue . $vwrap . $value . strrev($vwrap);
    }
    if ( 0 < count($tmp) ){
        return $prefix . implode($seperator, $tmp);
    }
    return false;
}

function qstr($s, $replaceQuote = "''", $magic_quotes = 1)
{
	$magic_quotes = ($magic_quotes == 1 ? get_magic_quotes_gpc() : 0);
    if (!$magic_quotes) {
        if ($replaceQuote == '\\') {
            $s = str_replace(array('\\', "\0"), array('\\\\', "\\\0"), $s);
        }
        return "'" . str_replace("'", $replaceQuote, $s) . "'";
    }
    // undo magic quotes for "
    $s = str_replace('\\"', '"', $s);

    if ($replaceQuote == "\\'") // ' already quoted, no need to change anything
        return "'$s'";
    else { // change \' to '' for sybase/mssql
        $s = str_replace('\\\\', '\\', $s);
        return "'" . str_replace("\\'", $replaceQuote, $s) . "'";
    }
}

function isEmpty($var){
    if( is_numeric($var) && $var == 0 ){
        return FALSE;
    }
    return empty($var); 
}

function ifempty($test, $default) {
    return ( !empty($test) ? $test : $default);
}

function page($pageinfo, $query, $pageCnt = 10){
    $queryString = query($query);
    $pageScript =  "<script>document.write(page('$pageinfo[recordCount]', '$pageinfo[pageSize]', $pageCnt, '$pageinfo[pageCount]', '$pageinfo[currentPage]', '&$queryString'))</script>";
    return $pageScript;
}

function js_alert($message = '', $after_action = '', $url = '', $charset = '') {
    if (!empty($charset)) {
        @header("Content-Type: text/html; charset=$charset");
    }
    $out = "<script language=\"javascript\" type=\"text/javascript\">\n";
    if (!empty($message)) {
        $out .= "alert(\"";
        $out .= t2js($message);
        $out .= "\");\n";
    }
    if (!empty($after_action)) {
        $out .= $after_action . "\n";
    }
    if (!empty($url)) {
        $out .= "document.location.href=\"";
        $out .= $url;
        $out .= "\";\n";
    }
    $out .= "</script>";
    echo $out;
    exit;
}

function t2js($content) {
    return str_replace("\n", "\\n", addslashes($content));
}



function sms($tel, $msg){
    $msg = urlencode($msg);
    $baseUrl = "http://202.109.129.136:8088/qxtweb/intf/websend.jsp?user=hemon&passwd=023023";
    $url = "$baseUrl&mobile=$tel&content=$msg";
    if(preg_match('/\<code\>(\d+)\<\/code\>/', file_get_contents($url), $match)){
        if( $match[1] == 1 )
            return false;
        else
            return $match[1];
    }
    // 发送超时
    return false;
}
/*
参数名	类型	功能
1.		strCorpId	STRING	企业登陆名，统一分配
2.		strPassword	STRING	登陆密码
3.		strMobileList	STRING	手机号列表，每次最多100个号码，格式：id,mobile;id,mobile;。。。
如：1,13912345678;2,13987654321;
id是消息序列号，用于返回状态报告
4.		strSubPort	STRING	发送短信时使用的子端口，一般默认为空
5.		strContent	STRING	短信内容，使用GB2312或ASCII编码，需要注意长度，移动联通最多70个字，小灵通最多35～54个字
6.		pstrResult	STRING*	出参
若返回空字符串则数据提交失败，失败原因为网络问题（服务器ip和端口错误或用户网络没连入internet）。具体原因可以见log记录。
不为空时返回提交短信的结果，具体格式在下面中说明。
*/
function _sms($tel, $msg){
    if( !is_array($tel) ){
        $tel = explode(";", $tel);
    }
    
    $num = count($tel);
    if( $num == 0 || 100 < $num ){
        return false;
    }

    $url = "http://202.109.129.165:8082/sht/mtsms.jsp";
    $mobilelist = implode_with_options($tel, '', ',',  '', ';', 1, 0);
	$post = array(
		'corpid'     => 'hemon',
		'password'   => '023023',
		'mobilelist' => $mobilelist,
		'content'    => urlencode($msg)
	);
	
	$opt = array(
	    CURLOPT_POSTFIELDS => build_query($post)
    );
    $res = curl($url, $opt);
    // 发送超时
    return $res;
}

function build_query($array){
    return implode_with_options(
                $array, $prefix = '', $k_v_glue = '=', $vwrap = '', 
                $seperator = '&', $show_key = 1, $qstr = 0
            );
}

function getPageInfo($rs)
{
    $pageInfo = array(
        'currentPage' => $rs->AbsolutePage(),
        'pageCount'   => $rs->LastPageNo(),
        'recordCount' => $rs->MaxRecordCount(),
        'pageSize'    => $rs->rowsPerPage,
        );
    return $pageInfo;
}

function getProvince($native)
{
    $province = substr($native, 0, 4);
    if($province == '黑龙') $province .= '江';
    
    return $province;
}

function getNative($native)
{
    $native = preg_replace("/省|市|县|区/", " ", $native);
    $native = explode(" ", trim($native));
    return $native;
}

function getClass($class)
{
    $class = split ("200", $class);
    $class[1] = '200' . $class[1];
    return $class;
}

function getHostBasename(){
    return substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], "."));
}

//From:http://www.phpxuexi.cn

function sbc_dbc($str,$args2) { //半角和全角转换函数，第二个参数如果是0,则是半角到全角；如果是1，则是全角到半角

    $DBC = Array( 

        '０' , '１' , '２' , '３' , '４' ,  

        '５' , '６' , '７' , '８' , '９' , 

        'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,  

        'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' , 

        'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,  

        'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' , 

        'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,  

        'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' , 

        'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,  

        'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' , 

        'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,  

        'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' , 

        'ｙ' , 'ｚ' , '－' , '　'  , '：' ,

        '．' , '，' , '／' , '％' , '＃' ,

        '！' , '＠' , '＆' , '（' , '）' ,

        '＜' , '＞' , '＂' , '＇' , '？' ,

        '［' , '］' , '｛' , '｝' , '＼' ,

        '｜' , '＋' , '＝' , '＿' , '＾' ,

        '￥' , '￣' , '｀'

    );

  $SBC = Array( //半角

         '0', '1', '2', '3', '4',  

         '5', '6', '7', '8', '9', 

         'A', 'B', 'C', 'D', 'E',  

         'F', 'G', 'H', 'I', 'J', 

         'K', 'L', 'M', 'N', 'O',  

         'P', 'Q', 'R', 'S', 'T', 

         'U', 'V', 'W', 'X', 'Y',  

         'Z', 'a', 'b', 'c', 'd', 

         'e', 'f', 'g', 'h', 'i',  

         'j', 'k', 'l', 'm', 'n', 

         'o', 'p', 'q', 'r', 's',  

         't', 'u', 'v', 'w', 'x', 

         'y', 'z', '-', ' ', ':',

         '.', ',', '/', '%', '#',

         '!', '@', '&', '(', ')',

         '<', '>', '"', '\'','?',

         '[', ']', '{', '}', '\\',

         '|', '+', '=', '_', '^',

         '$','~', '`'

    );

if($args2==0) 
  return str_replace($SBC,$DBC,$str);  //半角到全角
if($args2==1)
  return str_replace($DBC,$SBC,$str);  //全角到半角
else
  return false;
} 

function array_iconv($in_charset, $out_charset, $array){
    $iconv_func = "__" . md5("iconv_{$in_charset}_{$out_charset}");
    if( !function_exists($iconv_func) ){
        eval("function $iconv_func(&\$value){
            \$value = iconv('$in_charset', '$out_charset', \$value);
        }");
    }
    
    array_walk_recursive($array, $iconv_func);
    return $array;
}
