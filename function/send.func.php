<?php
function send(){
    list($sender, $tel, $msg, $cid, $task_id, $stime, $etime, $fstart, $fend) = func_get_args();
    $msg = iconv("UTF-8", "GB2312", $msg);
    return $sender($tel, $msg, $cid, $task_id, $stime, $etime, $fstart, $fend);
}

function sms(){
    list($tel, $msg, $cid) = func_get_args();
    if( !is_array($tel) or 100 < count($tel) ){
        return false;
    }
    $url = "http://202.109.129.165:8082/sht/mtsms.jsp";
	$post = array(
		'corpid'     => substr($cid, 0, 4) . 'JXXXTY',
		'password'   => '123456',	
		'subport'    => '',	
		'mobilelist' => implode_with_options($tel, $prefix = '', $k_v_glue = ',', 
                        $vwrap = '', $seperator = ';', $show_key = 1, $qstr = 0),	
		'content'    => urlencode($msg)
	);
	$opt = array(
	    CURLOPT_POSTFIELDS => implode_with_options(
                            $post, $prefix = '', $k_v_glue = '=', $vwrap = '', 
                            $seperator = '&', $show_key = 1, $qstr = 0)
    );
    $res = curl($url, $opt);
    // 发送超时
    return $res;
}

function tts($tel, $msg, $cid, $task_id = null, $stime = null, 
             $etime = null, $fstart = '00:00', $fend = '23:59', $fintype = 1) {
    if( !conn('share') ) return false ;
    // 参数处理
    // list($tel, $msg, $stime, $cid, $task_id, $etime, $fstart, $fend) = func_get_args();
    if ( preg_match("/^[^\\|\?|\*]+.vox$/", $msg) ) $fintype = 3;
    
    // 格式化日期时间
    $stime = isoTime($stime);
    if( empty($etime) ){
        $etime = date("Y-m-d", strtotime("+3 day", strtotime($stime)));
        $etime = substr($etime, 0, 10) . ' 22:00:00';
    }
    $etime = isoTime($etime);
    
    $sql = "INSERT INTO FInterface (
                    FTaskID, FLangtype, FInType, FInContent, 
                    FCallLib, FCallerNum, FPhone, FPriority, 
                    FCalloutTime, FCallEndTime, FIvr, FConvertFlag, 
                    FOprId, FStart, FEnd )
            VALUES (
                    '$task_id','1', '$fintype', '$msg', 
                    '2', '$cid', '$tel', 3, 
                    '$stime', '$etime', '1', 0, 
                    '0','$fstart', '$fend' )";
     
    if( $GLOBALS['db']->Execute($sql) ) {
        return true;
    }
    return false;
}

function conn($db = 'share'){
    if (isset($GLOBALS['db'])) return $GLOBALS['db'];
    try {
        require('adodb/adodb.inc.php');
        $dsn = "mssql://sa:share@134.225.51.12/XXTY";
        $GLOBALS['db'] =& ADONewConnection($dsn);
        return true;
    } catch (Exception $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        return false;
    }
}
?>
