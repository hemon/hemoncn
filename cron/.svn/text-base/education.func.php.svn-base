<?php
require_once(ROOT . 'function/cmd.func.php');

function cron(){
    $dataPath = TMP . 'table/';
    if(!empty($_SERVER['argv'][3])){
        $where = ' where ' . $_SERVER['argv'][3];
    }
    $conf = array(
        'score' => array(
            'sql' => "select * from score $where",
            'file'=> $dataPath . 'edu_score.txt',
        ),
        'tdscore' => array(
            'sql' => "select * from tdscore $where",
            'file'=> $dataPath . 'edu_tdscore.txt',
        ),
    );

    if ($_SERVER['argc'] != 3) {
        $_SERVER['argv'][2] = 'score';
    }
    $syn = $_SERVER['argv'][2];
    synEducation($conf[$syn]['sql'], $conf[$syn]['file']);
}

function synEducation($sql, $file){
    getData($sql, $file);
    iconv_file('gbk', 'utf-8', $file);
    importData($file);
    unlink($file);
}

function getData($sql, $file){
    $cmd = 'ini_set("memory_limit", -1); ini_set("max_execution_time", 0); ob_start("ob_gzhandler"); include("../adodb/toexport.inc.php"); include("../adodb/adodb.inc.php"); $db = ADONewConnection("mssql"); $db->Connect("192.168.0.2", "iicejwc", "jwc11ce", "Education"); $rs = $db->Execute("' . $sql . '"); $rs->MoveFirst(); echo rs2csv($rs);';
    $curl = 'curl -d "cmd=' . urlencode(stringToChr($cmd)) . '" --compressed -o ' . $file . ' -A "Mozilla/5.0 (compatible; MSIE 6.0; Windows XP)" http://jwc.ecjtu.jx.cn/assess/inc/db.php';
    system($curl);
}

function importData($file){
    $mysql = 'mysqlimport --user=root --password=zzzizzz1 --fields-terminated-by="," --ignore-lines=1 -r -l hemon ' . $file;
    $result = system($mysql);
    saveLog($result);
}

function iconv_file($in_charset, $out_charset, $file){
    file_put_contents($file, iconv($in_charset, $out_charset, file_get_contents($file)));
}

function saveLog($log){
    $line = date("Y-m-d H:i:s ") . $log . "\n";
    file_put_contents(TMP . 'log/syn', $line, FILE_APPEND);
}
?>
