<?php
require_once('function/curl.func.php');

$cnki_opt = array(
    //CURLOPT_VERBOSE    => 1,
    
    CURLOPT_PROXY      => '202.101.208.90:8081',
    CURLOPT_PROXYUSERPWD => 'hemon:!QAZxsw2',
    CURLOPT_PROXYTYPE  => CURLPROXY_SOCKS5,
    
    CURLOPT_COOKIEJAR  => "./tmp/cnki/".session_id(),
    CURLOPT_COOKIEFILE => "./tmp/cnki/".session_id(),
    
    CURLOPT_FOLLOWLOCATION => true
);

$cnki_type = array(
    // 拼音
    'pm'   => '篇名',
    'zt'   => '主题',
    'gjz'  => '关键词',
    'zy'   => '摘要',
    'zz'   => '作者&英文作者',
    'dyzz' => '第一责任人',
    'dw'   => '机构',
    'km'   => '中文刊名&英文刊名',
    'yw'   => '引文',
    'ck'   => '引文',
    'ckwx' => '引文',
    'qw'   => '全文',
    'n'    => '年',
    'q'    => '期',
    'jj'   => '基金',
    'flh'  => '分类号',
    'issn' => 'ISSN',
    'kh'   => 'CN',
    // English
    'title'    => '篇名',
    'subject'  => '主题',
    'key'      => '关键词',
    'keyword'  => '关键词',
    'abstract' => '摘要',
    'author'   => '作者&英文作者',
    'first'    => '第一责任人',
    'unit'     => '机构',
    'org'      => '机构',
    'mgz'      => '中文刊名&英文刊名',
    'magazine' => '中文刊名&英文刊名',
    'refer'    => '引文',
    'reference'=> '引文',
    'fulltext' => '全文',
    'year'     => '年',
    'issue'    => '期',
    'fund'     => '基金',
    'china'    => '分类号',
    'issn'     => 'ISSN',
    'cn'       => 'CN'
);

function cnki_query($query, $default='title'){
    $query = explode(' ', trim($query));
    foreach( $query as $q ){
        $q = trim($q);
        if( empty($q) ) continue;
        
        $q = explode(':', $q);
        switch ( count($q) ){
            case 1:
                $fields[] = $GLOBALS['cnki_type'][$default];
                $values[] = $q[0];
                break;
            case 2:
                if( array_key_exists($q[0], $GLOBALS['cnki_type']) ){
                    $fields[] = $GLOBALS['cnki_type'][$q[0]];
                    $values[] = $q[1];
                } else {
                    $fields[] = $GLOBALS['cnki_type'][$default];
                    $values[] = $q[0];
                }
        }
    }
    
    $string = '';
    $count  = count($fields);
    if( 0 < $count ){
        $string .= 'VarNum='.$count;
        for($i = 0; $i < $count; $i++){
            $string .= '&advancedfield'.($i+1).'='.urlencode($fields[$i]).'&advancedvalue'.($i+1).'='.urlencode($values[$i]);
            if( $i != $count ){
                $string .= '&logical'. ($i+2) . "=and";
            }
        }
    }
    return $string;
}

function cnki_list($query, $page=1, $QueryID='', $pageSize=20, $ics='gbk'){
    $page = (int) $page;
    $page = ($page > 1 ? $page : 1);
    if ( !isset($QueryID) ) {
        $query = cnki_query(iconv($ics, 'utf-8', $query));
        $opt[CURLOPT_POSTFIELDS] = "$query&RecordsPerPage=$pageSize&searchAttachCondition=&SearchQueryID=$QueryID&SearchFieldRelationDirectory=%E4%B8%BB%E9%A2%98%2F%5B%5D%2C%E7%AF%87%E5%90%8D%2F%5B%5D%2C%E9%A2%98%E5%90%8D%2F%5B%5D%2C%E4%BD%9C%E8%80%85%26%E8%8B%B1%E6%96%87%E4%BD%9C%E8%80%85%2F%5BSYS_Author_Relevant%5D%2C%E5%85%B3%E9%94%AE%E8%AF%8D%5B%5D%2C%E7%AC%AC%E4%B8%80%E8%B4%A3%E4%BB%BB%E4%BA%BA%2F%5BSYS_Author_Relevant%5D%2C%E6%9C%BA%E6%9E%84%2F%5BSYS_Organization_Relevant%5D%2C%E6%91%98%E8%A6%81%2F%5B%5D%2C%E5%BC%95%E6%96%87%2F%5B%5D%2C%E5%9F%BA%E9%87%91%2F%5BSYS_Fund_Relevant%5D%2C%E5%85%A8%E6%96%87%2F%5B%5D&updateTempDB=CJFDTEMP%2CCJFDYTMP&bCurYearTempDB=1&searchMatch=0&imageField.x=22&imageField.y=12&SearchRange=ALL&PublicationDate=&yearstart=1999&yearend=2010&order=dec&TablePrefix=CJFD&UserRight=ID%3D%22TPI_Basic_Hidden_UserRight%22&TableType=PY&advancedfrequency1=&hdnIsAll=false&NaviField=%E4%B8%93%E9%A2%98%E5%AD%90%E6%A0%8F%E7%9B%AE%E4%BB%A3%E7%A0%81&NaviDatabaseName=ZJCLS&systemno=&hdnFathorCode=sysAll&selectbox=A&selectbox=B&selectbox=C&selectbox=F&selectbox=G&selectbox=H&selectbox=I&selectbox=J&strNavigatorValue=%2CA%2CB%2CC%2CD%2CE%2CF%2CG%2CH%2CI%2CJ&strNavigatorName=%2C%E7%90%86%E5%B7%A5A%28%E6%95%B0%E5%AD%A6%E7%89%A9%E7%90%86%E5%8A%9B%E5%AD%A6%E5%A4%A9%E5%9C%B0%E7%94%9F%29%2C%E7%90%86%E5%B7%A5B%28%E5%8C%96%E5%AD%A6%E5%8C%96%E5%B7%A5%E5%86%B6%E9%87%91%E7%8E%AF%E5%A2%83%E7%9F%BF%E4%B8%9A%29%2C%E7%90%86%E5%B7%A5C%28%E6%9C%BA%E7%94%B5%E8%88%AA%E7%A9%BA%E4%BA%A4%E9%80%9A%E6%B0%B4%E5%88%A9%E5%BB%BA%E7%AD%91%E8%83%BD%E6%BA%90%29%2C%E5%86%9C%E4%B8%9A%2C%E5%8C%BB%E8%8D%AF%E5%8D%AB%E7%94%9F%2C%E6%96%87%E5%8F%B2%E5%93%B2%2C%E6%94%BF%E6%B2%BB%E5%86%9B%E4%BA%8B%E4%B8%8E%E6%B3%95%E5%BE%8B%2C%E6%95%99%E8%82%B2%E4%B8%8E%E7%A4%BE%E4%BC%9A%E7%A7%91%E5%AD%A6%E7%BB%BC%E5%90%88%2C%E7%94%B5%E5%AD%90%E6%8A%80%E6%9C%AF%E5%8F%8A%E4%BF%A1%E6%81%AF%E7%A7%91%E5%AD%A6%2C%E7%BB%8F%E6%B5%8E%E4%B8%8E%E7%AE%A1%E7%90%86&singleleafcode=";
        $html = curl('http://dlib.cnki.net/kns50/classical/singledbbrief.aspx?ID=1', $opt+$GLOBALS['cnki_opt']);
    } else {
        $html = curl("http://dlib.cnki.net/kns50/classical/singledbbrief.aspx?curpage=$page&RecordsPerPage=$pageSize&QueryID=$QueryID&turnpage=1", $GLOBALS['cnki_opt']);
    }
    
    $cnki_list = array();
    // stat
    if ( preg_match('|(\d+)条|', $html, $match) ) {
        $cnki_list['stat'] = $match[1];
    }
    $html = iconv('utf-8', $ics, $html);
    // data
    if ( preg_match_all('|<td  bgcolor=#\w{6} align=left valign=center STYLE="word-wrap: break-word  ;font-size:9pt ;Color:#000000">(.*?)</td>|s', $html, $matches) ) {
        foreach( $matches[1] as $i => $value ){
            $val = trim($value);
            $row = floor( $i / 5 );
            $col = $i % 5;
            switch ( $col ){
                case 0:
                    $tmp = array();
                    if( preg_match('|download.aspx\?(.*?)"|', $val, $match) ) $tmp[] = $match[1];
                    break;
                case 1:
                    if( preg_match('|href="singledbdetail.aspx\?(.*?)"|', $val, $match) ) $tmp[] = $match[1]; 
                    $tmp[] = strip_tags($val);
                    break;
                case 2:
                case 3:
                    $tmp[] = $val;
                    break;
                case 4:
                    $tmp[] = $val;
                    $cnki_list['data'][ $row ] = $tmp;
            }
        }
    }
    return $cnki_list;
}

function cnki_detail($QueryID, $CurRec, $ocs='gbk'){
    //$html = curl("http://dlib.cnki.net/kns50/detail.aspx?QueryID=$QueryID&CurRec=$CurRec", $GLOBALS['cnki_opt']);
    //file_put_contents('./detail', $html);
    $html = file_get_contents('./detail');
    $html = iconv('utf-8', $ocs, $html);
    // detail
    preg_match_all("|<td width=\"132\" style='word-break:break-all'>
(.*?)</td>
<td width=\"\" style='word-break:break-all'>
(.*?)</td>|s", $html, $matches);
    $matches[2] = array_map("strip_tags", $matches[2]);
    unset($matches[0]);
    $rs['data'] = $matches;
    // download
    preg_match("|/KNS50/download.aspx\?(.*?)&dflag|", $html, $match);
    $rs['download'] = $match[1];
    // title
    preg_match("|<span class='datatitle'>(.*)</span>|", $html, $match);
    $rs['title'] = strip_tags($match[1]);
    
    return $rs;
}

function cnki_download($filename, $tablename, $dflag='pdfdown', $down=true){
    if( empty($dflag) ) $dflag = 'pdfdown';
    $opt[CURLOPT_RETURNTRANSFER] = false;
    if($down) $opt[CURLOPT_HEADERFUNCTION] = 'cnki_download_header';
    $url = "http://dlib.cnki.net/kns50/download.aspx?filename=$filename&tablename=$tablename&dflag=$dflag";
    //$url = "http://dlib.cnki.net/kns50/download.aspx?filename=5JGeQpmM3J2R1hnePR0YR52Q6JnT3IzQ2ZmVMVHWwY1a410MwtGNCRlT0s2T0pkdxEXOqVWaIBFU4BXWZRDdv1URtFFd1tUetFzYSdXOBNna3dTaqBnSrZVMkFWe2N0LnRTdDRWTtZ1V2UkUQl0YwEkRTpGRGVmW&tablename=CJFD2009&dflag=$dflag";
    curl($url, $opt+$GLOBALS['cnki_opt']);
}

function cnki_download_header($resURL, $strHeader) {
    if (preg_match('/^Content-Disposition: attachment; filename=(.*?)$/i', $strHeader, $match)) {
        cnki_filename(rtrim($match[1]));
    }
    return strlen($strHeader);
}

function cnki_filename($filename){
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $filename = iconv('gbk','utf-8',$filename);
    header('Content-Type: application/octet-stream');
    if ( strpos($ua, "MSIE") !== false ) {
        $encoded_filename = urlencode($filename);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } elseif ( strpos($ua, "Firefox") !== false ) { 
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
    } else {
        header('Content-Disposition: attachment; filename="' . $filename . '"');
    }
}
