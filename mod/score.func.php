<?php
$GLOBALS['P'] = 'edu_';
if($_REQUEST['td'] == '1'){
    $GLOBALS['P'] = 'edu_td';
}

function getStudentInfo($sid){
    if( isSid($sid) ){
    	$sql = "SELECT sid, cid, name
                FROM edu_student
                WHERE sid = '$sid'";
        $ttl = 30 * 24 * 3600;
    	$student = $GLOBALS['db']->CacheGetRow($ttl, $sql);
	if( !empty($student) ){
            $student['class'] = $GLOBALS['db']->CacheGetOne($ttl, "SELECT class FROM edu_class WHERE cid = ". SUBSTR($student['cid'], 0, 12));
        }else{
            $student = array('sid'=>$sid,'cid'=>$sid);
	}
	return $student;
    }
    return false;
}

function getClasscode($sid){
    $student = getStudentInfo($sid);
    return substr($student['class_id'], 0, 12);
}

function getClassmates($cid){
    if(strlen($cid) == 12){
    	$sql = "SELECT sid, name 
                FROM edu_student
                WHERE cid LIKE '$cid%'
                ORDER BY cid";
    	return $GLOBALS['db']->CacheGetAssoc(30 * 24 * 3600, $sql);
    }
    return false;
}

function getScore($where){
    $sql_where = implode(' AND ', $where);
    if(strpos($sql_where, 'course')) $inner_join = "INNER JOIN edu_student ON edu_student.sid = edu_score.sid"; 
	$sql = "SELECT *
            FROM edu_score
	    $inner_join
            WHERE $sql_where
            ORDER BY score DESC";
            
    $ttl = getTtl($where['term']);
    $ttl = 0;
    return $GLOBALS['db']->CacheGetAll($ttl, $sql);
}

function getTtl($term){
    if( $term == getLastTerm() ){
        $ttl = 3600;
    } else {
        $ttl = 365 * 24 * 3600;
    }
    return $ttl;
}
?>

