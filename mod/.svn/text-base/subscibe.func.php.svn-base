<?php
function do_subscibe($sid, $mobile, $service, $authcode){
    
    if( !is_mobile($mobile) && !is_phone($mobile) ){
        return("手机或小灵通号码格式不对！小灵通格式：区号+电话号码，例:079188888888");
    }
    
    $result = subscibed($sid, $mobile, $service, $authcode);
    
    switch ($result){
        case 0:
            $msg = "订阅失败！验证码不正确。";
            break;
        case 1:
            $msg = "订阅成功！";
            break;
        default:
            $msg = "订阅失败！请重试。";
    }
    return $msg;
}

function is_subscibed($sid, $mobile)
{
    $sql = "SELECT COUNT(1) FROM subscibe WHERE sid = '$sid' AND mobile = '$mobile'";
    return (bool) $GLOBALS['db']->GetOne($sql);
}

function mobile_subscibed($mobile)
{
    $sql = "SELECT COUNT(1) FROM subscibe WHERE mobile = '$mobile'";
    return $GLOBALS['db']->GetOne($sql);
}

function subscibed($sid, $mobile, $service, $authcode)
{
    $enabled = ($authcode == authcode($mobile) ? 1 : 0);
    $subscibed = date('Y-m-d H:i:s');
    $sql = "INSERT INTO subscibe(sid, mobile, subscibed, enabled) 
            VALUES('$sid', '$mobile', '$subscibed', '$enabled') 
            ON DUPLICATE KEY 
            UPDATE mobile = '$mobile', subscibed = '$subscibed', enabled = '$enabled'";
    if( $GLOBALS['db']->Execute($sql) ){
        return $enabled;
    } else {
        return false;
    }
}

function authcode($mobile)
{
    return substr(crc32($mobile), -4);
}
?>
