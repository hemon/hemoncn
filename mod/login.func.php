<?php
function login($sid, $password){
	      if( $usr =  auth_db($sid, $password) ){
	} elseif( $usr = auth_jwc($sid, $password) ){	
	} else {
		return false;
	}
	return $usr;
}

function auth_db($sid, $password){
	$sql = "SELECT sid,username,password,classcode,
                    name,sex,class,native,nation,birthday,email
            FROM usr
            WHERE sid = '$sid'";

	$usr = $GLOBALS['db']->GetRow($sql);
	if( $usr['password'] == $password ){
	   return $usr;
    }
	return false;
}

function auth_jwc($sid, $password){
	$url = "http://jwc.ecjtu.jx.cn:8080/jwcmis/jwbm/index.jsp?studentid=$sid&pwd=$password";
	$res = file_get_contents($url);
	if( strstr($res, '´íÎó') ){
		return false;
	} else {
		$sql = "INSERT INTO usr(sid, password) 
                VALUES ('$sid', '$password')
                ON DUPLICATE KEY 
                UPDATE password = '$password'";
		$GLOBALS['db']->Execute($sql);
		return array('sid' => $sid);
	}
}

function auth_type($username, $default='username'){
    require_once 'lib/Validator.php';
    $allowAuthType = array('sid','email','idcode');
    $v = new Validator();
    foreach($allowAuthType as $type){
        $method = "is_$type";
        if( $v->$method($username) )
            return $type;
    }
    return $default;
}

?>
