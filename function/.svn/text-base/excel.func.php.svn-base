<?php
function excel_read($file){
    ini_set('memory_limit', -1);
    ini_set('max_execution_time', 0);

    require_once 'Spreadsheet/Excel/reader.php';
    $excel = new Spreadsheet_Excel_Reader();
    $excel->setOutputEncoding('utf-8');
    $excel->read($file);
    
    $numRows = $excel->sheets[0]['numRows'];
    $numCols = $excel->sheets[0]['numCols'];
    
    $data = array();
    for ($i = 1; $i <= $numRows; $i++) {
        $row = array();
        for ($j = 1; $j <= $numCols; $j++) {
            $row[] = $excel->sheets[0]['cells'][$i][$j];
        }
        $data[] = $row;
    }
    return $data;
}

function excel_write(&$data, $file = 'excel.xls', $sheet = 'Sheet1') {
    ini_set('memory_limit', -1);
    ini_set('max_execution_time', 0);
    // 输出excel
    require 'Spreadsheet/Excel/Writer.php'; 
    $workbook = new Spreadsheet_Excel_Writer(); // 初始化类
    $workbook->setVersion(8);  //设置版本，设置版本为8，并且设置编码才不会出现中文乱码
    $workbook->send($file); // 发送 Excel 文件名供下载 
    $worksheet =& $workbook->addWorksheet($sheet); // 加入一个工作表 sheet-1 
    $worksheet->setInputEncoding('UTF-8');    //设置编码格式
    $rowcnt = count($data);
    for ($row = 0; $row < $rowcnt; $row ++) {
        $colcnt =  count($data[$row]);
        for ($col = 0; $col < $colcnt; $col ++) {
            $val = array_shift($data[$row]);
            $worksheet->writeString($row, $col, $val); // 在 sheet-1 中写入数据 
        }
    }
    $workbook->close(); // 完成下载
}
?>
