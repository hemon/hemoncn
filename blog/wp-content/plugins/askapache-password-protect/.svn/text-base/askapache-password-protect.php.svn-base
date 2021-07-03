<?php
/*
Plugin Name: AskApache Password Protect
Plugin URI: http://www.askapache.com/htaccess/htaccess-security-block-spam-hackers.html
Description: Advanced Security: Password Protection, Anti-Spam, Anti-Exploits, more to come...  <a href="options-general.php?page=askapache-password-protect.php">Configuration</a>
Version: 4.3.2
Author: AskApache
Author URI: http://www.askapache.com/

== Installation ==
1. Extract zip in plugins directory
2. Activate the Plugin
3. Setup plugin options
*/



/*
/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| AskApache Password Protect Plugin - Adds HTTP Basic Authentication |
| Copyright (C) 2008, AskApache, www.askapache.com                   |
| All rights reserved.                                               |
|                                                                    |
| This program is free software; you can redistribute it and/or      |
| modify it under the terms of the GNU General Public License        |
| as published by the Free Software Foundation; either version 2     |
| of the License, or (at your option) any later version.             |
|                                                                    |
| This program is distributed in the hope that it will be useful,    |
| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
| GNU General Public License for more details.                       |
|                                                                    |
| You should have received a copy of the GNU General Public License  |
| along with this program; if not, write to the                      |
| Free Software Foundation, Inc.                                     |
| 51 Franklin Street, Fifth Floor                                    |
| Boston, MA  02110-1301, USA                                        |
|                                                                    |
\--------------------------------------------------------------------/
*/
?>
<?php
if(@defined('AA_PP_DEBUG'))return;
else @define('AA_PP_DEBUG',0); // set this to 1 for verbose debugging


/* Define this to an absolute file location like /home/site.com/php-errors.log
   and any errors or debug messages will be appended to that file
   An easier way is to use a custom php.ini file and set that there.        */
//@define('AA_PP_DEBUG_LOGFILE','/yourlogfile.log');

@define('AA_PP_MAX_TIME',  100);  // max time for test script execution
@define('AA_PP_SOCKET_TIME', 30); // max time for test socket reads
@define('AA_PP_CONNECT_TIME', 5); // max time for test socket connect
@define('AA_CRLF', chr(13).chr(10));  // linebreak




// aa_pp_options_setup1
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_options_setup1() {
    add_options_page('AskApache Password Protection', 'AA PassPro', 8, basename(__FILE__), 'aa_pp_main_page');
}//=========================================================================================================================



// aa_pp_admin_header
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_admin_header(){
    global $aa_PP;
    
    if (!current_user_can(8)||!current_user_can('upload_files')) die(__("You are not allowed to be here"));
    $aa_PP=get_option('askapache_password_protect');
    
    if($_SERVER['REQUEST_METHOD']=='POST') aa_pp_get_post_values();
    
    update_option('askapache_password_protect',$aa_PP);
}//=========================================================================================================================


// aa_pp_get_post_values
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_get_post_values(){
    global $aa_PP;
    check_admin_referer('askapache-password-protect-update_modify');
    $aa_PP=get_option('askapache_password_protect');
    
    if(isset($_POST['resetaapp']))aa_pp_activate();
    if(isset($_POST['aapptestingdone']))$aa_PP['config_step']='2';
    if(isset($_POST['aapptestingredo']))aa_pp_activate();
    if(isset($_POST['aappsetupcomplete']))$aa_PP['config_step']='3';
    
    
    if(isset($_POST['adduser'])){
        if(isset($_POST['addhtaccessuser'])&&isset($_POST['addhtaccesspass']) && isset($_POST['aapassformat'])){
            $aa_current_htpasswd_users=aa_pp_extract_mark($aa_PP['htpasswd'],'AskApache PassPro');
            $aa_PP['HTPASSWD_RULES']=array_merge($aa_current_htpasswd_users,array(aa_pp_hashit($_POST['aapassformat'],$_POST['addhtaccessuser'],$_POST['addhtaccesspass'])));
            if(!aa_pp_insert_mark($aa_PP['htpasswd'],'AskApache PassPro',$aa_PP['HTPASSWD_RULES'])) return aa_pp_err('Error Creating '.$aa_PP['htpasswd']);
        }
    }
    
    else if(isset($_POST['changepasswordsettings'])){
        
        if(isset($_POST['htaccessrealm']) && $aa_PP['realm']=$_POST['htaccessrealm']) {
            $newrealm1=$_POST['htaccessrealm'];
            if(strlen($newrealm1)>45)$newrealm1=substr($newrealm1, 0, 45);
            $aa_PP['realm']=$newrealm1;
            if($aa_PP['S']['sid900']['ON']=='1')aa_pp_activate_sid('sid900');
            if($aa_PP['S']['sid800']['ON']=='1')aa_pp_activate_sid('sid800');
        }
        
        if(isset($_POST['htpasswdfile']) && $_POST['htpasswdfile']!=$aa_PP['htpasswd']){
            if(!is_writable(dirname($_POST['htpasswdfile'])) && !touch($_POST['htpasswdfile'])) return aa_pp_err($_POST['htpasswdfile'].' location is not writable!');
            else {
                if(!aa_pp_insert_mark($_POST['htpasswdfile'],'AskApache PassPro',aa_pp_extract_mark($aa_PP['htpasswd'],'AskApache PassPro')))return aa_pp_err('error writing new password file.');
                else aa_pp_unlink($aa_PP['htpasswd']);
                
                $aa_PP['htpasswd']=$_POST['htpasswdfile'];
                if($aa_PP['S']['sid900']['ON']=='1')aa_pp_activate_sid('sid900');
                if($aa_PP['S']['sid800']['ON']=='1')aa_pp_activate_sid('sid800');
            }
        }
        
    }
    
    else if(isset($_POST['aappsetupcomplete'])){
        if(isset($_POST['aapassformat']))$aa_PP['format']=$_POST['aapassformat'];
        if(isset($_POST['htaccessuser']) && isset($_POST['htaccesspass']))    $aa_PP['user']=$_POST['htaccessuser'];
        if(isset($_POST['htaccessrealm']) && $aa_PP['realm']!=$_POST['htaccessrealm']) {
            if(strlen($aa_PP['realm'])>45)$aa_PP['realm']=substr($aa_PP['realm'], 0, 45);
        }
        
        if(isset($_POST['htpasswdfile'])){
            if(!is_writable(dirname($_POST['htpasswdfile'])) && !touch($_POST['htpasswdfile'])) return aa_pp_err($_POST['htpasswdfile'].' location is not writable!');
            else $aa_PP['htpasswd']=$_POST['htpasswdfile'];
        }
        
        $aa_PP['HTPASSWD_RULES']=array(aa_pp_hashit($_POST['aapassformat'],$_POST['htaccessuser'],$_POST['htaccesspass']));
        if(!aa_pp_insert_mark($aa_PP['htpasswd'],'AskApache PassPro',$aa_PP['HTPASSWD_RULES'])) return aa_pp_err('Error Creating '.$aa_PP['htpasswd']);
		
        if(isset($_POST['sid900']))aa_pp_activate_sid('sid900');
        else aa_pp_erase_sid('sid900');
    }
    
	
    else if(isset($_POST['updatemodules'])){
        
        if($aa_PP['mod_rewrite_support']=='1'){
			$activate_modrewrite=false;

			foreach($_POST as $pname){
				if(strpos($pname,'sid10')!==false)$activate_modrewrite=true;
			}
			
            if($activate_modrewrite) aa_pp_activate_sid('modrewrite');
            else aa_pp_erase_sid('modrewrite');
            
        }
        
        
		foreach($aa_PP['S'] as $n=>$sid){ 
			if($n[0]!='s')continue;
        	if(isset($_POST[$n]))aa_pp_activate_sid($n);
        	else aa_pp_erase_sid($n);
		}
        
       /*
	    if($aa_PP['mod_security_support']=='1') {
            if(isset($_POST['sid2000']))aa_pp_activate_sid('modsecurity');
            else aa_pp_erase_sid('modsecurity');
        }
		*/
    }
    
    
    
    if(AA_PP_DEBUG){echo '<pre>';print_r($_POST);echo '</pre>';}
    update_option('askapache_password_protect',$aa_PP);
}//=========================================================================================================================






// aa_pp_main_page
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_main_page() {
    global $aa_PP;
    
    aa_pp_print_header();
    ?>
    
    <form action="<?php echo attribute_escape($_SERVER["REQUEST_URI"]); ?>" method="post">
    <?php wp_nonce_field('askapache-password-protect-update_modify'); ?>
    <?php if(AA_PP_DEBUG){echo '<pre>';print_r($aa_PP);echo '</pre>';} ?>
    
    
    <?php
    if($aa_PP['config_step']=='1' || $aa_PP['htaccess_support']=='0')
    {
        $aa_PP['htaccessusers']=aa_pp_current_users($aa_PP['htpasswd'],'AskApache PassPro');
        aa_pp_run_tests();
        ?>
        <p class="submit"><label><br /><input name="aapptestingdone" id="aapptestingdone" value="<?php _e('Continue to AskApache Password Protection Setup &raquo;'); ?>" type="submit" class="button valinp" /></label></p>
        <p style="border-bottom:1px solid #CCC; padding:1em 0;"><br /></p>
        <?php
    }
    else if($aa_PP['config_step']=='2')
    {
		aa_pp_rmdir($aa_PP['test_dir']);
		?>
        <hr style="visibility:hidden;padding-top:.5em;clear:both;" /><div id="your-profile" style="min-width:300px;">
        <fieldset id="information" style="width:85%; float:none; margin:0 auto;">
        <legend>Installation</legend><hr style="visibility:hidden;padding-top:.5em;clear:both;" />
        <h3>Create User and Pass for .htpasswd</h3>
        <p style="width:300px;"><label>Protect Admin Folder Immediately? <input style="width:auto" class="checkbox" type="checkbox" name="sid900" id="sid900" checked="checked" /></label></p>
        <p><label>Pop-Up Message <em>Auth-Name / Realm</em>:<br /><input id="htaccessrealm" name="htaccessrealm" size="50" type="text" style="font-size:16px;" value="<?php echo $aa_PP['realm']; ?>" /></label></p>
        <p><label>Username:<br /><input id="htaccessuser" name="htaccessuser" size="16" type="text" style="font-size:16px;" value="<?php echo $aa_PP['user']; ?>" /></label></p>
        <p><label>Password:<br /><input id="htaccesspass" name="htaccesspass" size="16" type="text" style="font-size:16px;" value="" /></label></p>
        <?php aa_pp_show_encryptions('.htpasswd Encryption Settings:',0); ?>
        <p>Try to pick an .htpasswd location above your document_root, in other words, not in site.com/htdocs/.htpasswda1 but site.com/.htpasswda1</p>
        <p><label>.htpasswd location:<br /><input id="htpasswdfile" name="htpasswdfile" size="16" type="text" style="font-size:10px;" value="<?php echo $aa_PP['htpasswd']; ?>" /></label></p>
        <p class="submit"><label><br /><input name="aappsetupcomplete" id="aappsetupcomplete" value="<?php _e('Submit Settings &raquo;'); ?>" type="submit" class="button valinp" /></label></p>
        <?php aa_pp_show_encryptions('AskApache PassPro Encryption Algorithm Descriptions',4);?>
        <hr style="visibility:hidden;padding-top:1em;clear:both;" /></fieldset>
        </div><hr style="visibility:hidden;clear:both;" />
        <?php
    }
    else if($aa_PP['config_step']=='3')
    {    ?>
        
        <hr style="visibility:hidden;padding-top:.5em;clear:both;" /><div id="your-profile">
        <fieldset id="information" style="width:33%; float:left;">
        <legend>Modify Main Password Settings</legend>
        <p><label>Auth Name:<br /><input id="htaccessrealm" name="htaccessrealm" size="50" type="text" style="font-size:15px;" value="<?php echo $aa_PP['realm']; ?>" /></label></p>
        <p><label>.htpasswd location:<br /><input id="htpasswdfile" name="htpasswdfile" size="16" type="text" style="font-size:10px;" value="<?php echo $aa_PP['htpasswd']; ?>" /></label></p>
        <p><label><br /><input name="changepasswordsettings" id="changepasswordsettings" value="<?php _e('Change Password Settings &raquo;'); ?>" type="submit" class="button valinp" /></label></p>
        <p><label><br /><input name="resetaapp" id="resetaapp" value="Reset Plugin to Default Settings &raquo;" type="submit" class="button valinp" /></label></p>
        <?php if($_SERVER['REQUEST_METHOD']!='GET')    {?><span style="visibility:hidden;overflow:hidden;display:block;width:1px;height:1px;background-image:url('askapache-<?php echo rand(1,1000);?>-.bmp');"></span><?php }?>
        </fieldset>
        
        <fieldset id="contact-info" style="width:23%; float:left;">
        <legend>Add User</legend>
        <p><label>Username:<br /><input id="addhtaccessuser" name="addhtaccessuser" size="50" type="text" style="font-size:16px;" value="<?php if(isset($_POST['addhtaccessuser']))echo $_POST['addhtaccessuser']; ?>" /></label></p>
        <p><label>Password:<br /><input id="addhtaccesspass" name="addhtaccesspass" size="50" type="text" style="font-size:16px;" value="<?php if(isset($_POST['addhtaccesspass']))echo $_POST['addhtaccesspass']; ?>" /></label></p>
        <?php aa_pp_show_encryptions('.htpasswd Encryption Settings:',0); ?>
        <p><label><br /><input name="adduser" id="adduser" value="<?php _e('Add User &raquo;'); ?>" type="submit" class="button valinp" /></label></p>
        </fieldset>
        
        <?php $currentusersnow=aa_pp_current_users($aa_PP['htpasswd'],'AskApache PassPro');?>
        <table class="widefat" style="clear:none; width:23%; float:left; margin-top:25px;">
        <thead><tr>
        <th width="100%">Username</th>
        </tr></thead>
        <tbody id="the-list">
        <?php
        $countusrs=0;
        foreach($currentusersnow as $aauser2){
            $countusrs++;?> <tr id="l-<?php echo $countusrs; ?>" valign="middle"><td><?php echo $aauser2; ?><br /></td></tr><?php
        }
        ?>
        </tbody></table>
        <hr style="visibility:hidden;padding-top:.25em;clear:both;" />
        <hr style="visibility:hidden;clear:both;" /></div><hr style="visibility:hidden;clear:both;" />
        
        <hr style="visibility:hidden;padding-top:.5em;clear:both;" />
        <fieldset class="dbx-box"><div class="dbx-h-andle-wrapper">
        <h3 class="dbx-handle">Manage .htaccess Security Modules</h3></div>
        <div class="dbx-c-ontent-wrapper"><div class="dbx-content">
        <table class="widefat">
        <thead><tr>
        <th width="5%">SID</th>
        <th width="15%">Protection</th>
        <th width="50%">Description</th>
        <th width="15%">Response</th>
        <th style="text-align: center">Enable</th>
        </tr></thead>
        <tbody id="the-list">
        <?php
        $odd=' class="alternate"';
        foreach($aa_PP['S'] as $n=>$sid){ if($n[0]!='s')continue; ?>
            <tr id="l-<?php echo $n;?>" valign="middle"<?php echo $odd;?>>
            <td><?php echo str_replace('sid','',$n);?></td>
            <td><strong><?php echo $sid['TITLE'];?></strong><br /></td>
            <td><?php echo $sid['DESC'];?></td>
            <td><?php echo $sid['RESP'];?></td>
            <td align='center'><input type="checkbox" name="<?php echo $n;?>" id="<?php echo $n;?>" <?php if($sid['ON']=='1')echo 'checked="checked" ';
            if( ($n=='sid1030' &&  strtolower($_SERVER['HTTPS'])!='on') || ($n[3]=='1' && $aa_PP['mod_rewrite_support']=='0') || ($n[3]=='2' && $aa_PP['mod_security_support']=='0'))echo 'disabled="disabled" ';?> /></td>
            </tr>
        <?php $odd=(empty($odd)) ? ' class="alternate"' : ''; } ?>
        </tbody></table>
        <p><label><br /><input name="updatemodules" id="updatemodules" value="<?php _e('Update Modules &raquo;'); ?>" type="submit" class="button valinp" /></label></p>
        <p>New modules added with every upgrade.  Submit your module suggestions/bugs <a href="http://www.askapache.com/about/contact/">here</a>.</p>
        </div></div></fieldset>
        
        <hr style="visibility:hidden;padding-top:1.5em;clear:both;" />
        <div id="advancedstuff" class="dbx-group" style="min-width:300px;">
        
        <?php if(file_exists($aa_PP['S']['sid900']['FILE']) && @filesize($aa_PP['S']['sid900']['FILE']) >0){?>
            <fieldset class="dbx-box"><div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">wp-admin .htaccess</h3></div><div class="dbx-c-ontent-wrapper"><div class="dbx-content">
            <p class="desc"><?php echo $aa_PP['S']['sid900']['FILE']; ?><br style="clear:both;" /></p>
            <pre style="width:80%; margin-left:2em;"><?php aa_pp_readfile($aa_PP['S']['sid900']['FILE']); ?></pre>
            </div></div></fieldset><hr style="visibility:hidden;clear:both;padding-top:.25em;" />
        <?php } ?>
        <fieldset class="dbx-box">
        <div class="dbx-h-andle-wrapper"><h3 class="dbx-handle"><?php echo basename($aa_PP['htpasswd']);?></h3></div><div class="dbx-c-ontent-wrapper"><div class="dbx-content">
        <p class="desc"><?php echo $aa_PP['htpasswd']; ?></p>
        <pre style="width:80%; margin-left:2em;"><?php aa_pp_readfile($aa_PP['htpasswd']); ?></pre>
        </div></div></fieldset><hr style="visibility:hidden;clear:both;padding-top:.25em;" />

        <fieldset class="dbx-box"><div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">Root .htaccess</h3></div><div class="dbx-c-ontent-wrapper"><div class="dbx-content">
        <p class="desc"><?php echo $aa_PP['blog_root_htaccess']; ?><br style="clear:both;" /></p>
        <pre style="width:80%; margin-left:2em; overflow:auto;"><?php aa_pp_readfile($aa_PP['blog_root_htaccess']); ?></pre>
        </div></div></fieldset><hr style="visibility:hidden;clear:both;padding-top:.25em;" />
        
        <hr style="visibility:hidden;clear:both;padding-top:.25em;" />
        </div>
        <hr style="visibility:hidden;clear:both;" />
        <?php
    }?>
    </form>
    <?php
    
    aa_pp_print_footer();
    
}//=========================================================================================================================



// aa_pp_current_users
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_current_users($HTPASS, $mark){
    $CURRENT_USERS=array();
    $cu=array();
    @ $cu=aa_pp_extract_mark($HTPASS, $mark);
    if(is_array($cu) && sizeof($cu)>0){
        foreach($cu as $key){
            $CURRENT_USERS[]=preg_replace('/(.+):(.+)/', "\\1", $key, 1);
        }
    }
    return $CURRENT_USERS;
}//=========================================================================================================================













//---------------------------
function aa_pp_microtime(){
    global $aa_pp_script_time,$aa_pp_socket_read_time;
    return (float)array_sum(explode(' ', microtime()));
}//=====================================================================================




/*  very cool!  this is run during socket reads and checks whether the script
execution time limit or the socket read time limit has been met, killing
the script if so, otherwise returns true.  Run with a cron-like process */
//---------------------------
function aa_pp_time_ok($print=0) {
    global $aa_pp_script_time,$aa_pp_socket_read_time;
    
    $current_time=aa_pp_microtime();
    $total_time=($current_time - $aa_pp_script_time);
    $sock_time=($current_time - $aa_pp_socket_read_time);
    if($print) echo ($print==1) ? round($total_time,4)."\n" : round($sock_time,4)."\n";
    else {
        if((float)$total_time > AA_PP_MAX_TIME) return aa_pp_err('killed script.. time exceeded '.AA_PP_MAX_TIME.' Total: '.$total_time);
        if((float)$sock_time > AA_PP_SOCKET_TIME) return aa_pp_err('Killed socket.. time exceeded '.AA_PP_SOCKET_TIME.' Total: '.$sock_time);
    }
    return true;
}//=====================================================================================




/*  returns a socket pointer if valid or displays an error message
sets stream timeout, starts the clock to check for socket read time */
//---------------------------
function aa_pp_get_sock($target,$port){
    global $aa_pp_script_time,$aa_pp_socket_read_time;
    if(false===($fp = @fsockopen($target,$port,$errno,$errstr,AA_PP_CONNECT_TIME))||!is_resource($fp)) return aa_pp_sock_strerror($errno,$errstr);
    @stream_set_timeout($fp, AA_PP_SOCKET_TIME);
    return $fp;
}//=====================================================================================


/*  writes request, then reads response until EOF, script max, or socket max
returns response on success.  Uses buffer to allow size>100megs */
//---------------------------
function aa_pp_txrx($fp,$request,$chunk=128){
    global $aa_pp_script_time,$aa_pp_socket_read_time;
    $aa_pp_socket_read_time=aa_pp_microtime();
    $rec=$buf='';
    
    if(!@fwrite($fp, $request, strlen($request)))return aa_pp_err('fwrite error');
    while ( !@feof($fp) && aa_pp_time_ok() && strpos( $response, AA_CRLF )===false){
        $buf = @fread($fp, $chunk);
        $rec .= $buf;
    }
    if(!@fclose($fp))return aa_pp_err('fclose error');
    
    return $rec;
}//=====================================================================================



/*  handles fsockopen errors, printing them out though you may want to die on err */
//---------------------------
function aa_pp_sock_strerror($errno,$errstr){
    switch($errno){
        case -3:  $err="Socket creation failed"; break;
        case -4:  $err="DNS lookup failure"; break;
        case -5:  $err="Connection refused or timed out"; break;
        case 111: $err="Connection refused"; break;
        case 113: $err="No route to host"; break;
        case 110: $err="Connection timed out"; break;
        case 104: $err="Connection reset by client"; break;
        default:  $err="Connection failed"; break;
    }
    return aa_pp_err("Fsockopen failed! [{$errno}] {$err} ({$errstr})");
}//=====================================================================================



function aa_pp_run_tests(){
    global $wpdb, $aa_PP, $aa_pp_script_time,$aa_pp_socket_read_time;
	
	$aa_pp_script_time=aa_pp_microtime();
	
    $sep = "\n<p>" . str_repeat('=', 80) . "</p>\n";
    $success='<strong style="color:green;">[ SUCCESS ]</strong>';
    $fail='<strong style="color:red;">[ FAILED ]</strong>';
    
    $aa_pp_test_responder="<?php\n@header('Content-Type: image/gif');\n@header('Content-Length: 49');\n".
    	'echo pack("H*","47494638396101000100910000000000ffffffffffff00000021f90405140002002c00000000010001000002025401003b");'.
		"\nexit;\nexit();\n?>";


	$aa_pp_test_401="<?php\nob_start();\n@header('HTTP/1.1 401 Authorization Required');\n@header('Status: 401 Authorization Required');\n?>\n".
    	'<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'."<html>\n<head>\n<title>401 Authorization Required</title>\n</head>\n<body>\n<h1>Authorization Required</h1>\n".
    	'<p>Protected by <a href="http://www.askapache.com/wordpress/htaccess-password-protect.html">AskApache Password Protection</a></p>'.
     	"\n</body>\n</html>\n<?php\n\$g=ob_get_clean();\necho \$g;\nexit;\nexit();\n?>";


	$aa_pp_rel_docroot=$aa_PP['root_path'].'/wp-content/'.basename($aa_PP['test_dir']).'/';
	//$testing_mods=$aa_PP['test_dir'].'test.php';
	//$testing_mods_401=$aa_PP['test_dir'].'401.php';
	//$testing_mods_root=$aa_PP['test_dir'];

	$siteurl=get_option('siteurl');
	$su=parse_url($siteurl);
    $test_base_url=str_replace('//','/',$su['scheme'].':///'.$su['host'].$aa_pp_rel_docroot);
    
	
    
    $aa_pp_1_htaccess_test=array(
        "DirectoryIndex {$aa_pp_rel_docroot}test.php test.php",
        "ErrorDocument 401 {$aa_pp_rel_docroot}401.php",
        "ErrorDocument 403 {$aa_pp_rel_docroot}401.php",
		'#',
		'#mod_rewrite test',
        "<IfModule mod_rewrite.c>",
        "RewriteEngine On",
        "RewriteBase /",
        'RewriteCond %{QUERY_STRING} !^$ [NC]',
        'RewriteCond %{QUERY_STRING} !askapachetest1 [NC]',
        'RewriteRule .* /? [R=307,L]',
        "</IfModule>",
		'#',
		'#mod_security test',
        "<IfModule mod_security.c>",
        "SecFilterEngine On",
        'SecFilter askapachetest1 "deny,nolog,noauditlog,status:503"',
        "</IfModule>",
		'#',
		'# mod_alias test',
        "<IfModule mod_alias.c>",
        "RedirectMatch 305 ^.*askapacheredirecttest$ ".$aa_PP['scheme']."://".$_SERVER['HTTP_HOST']."/",
        "</IfModule>",
		'#',
		'# encryption test',
		'<Files passtest.php>',
        'Order Deny,Allow','Deny from All','Satisfy Any',
        'AuthName "askapache test"',
        "AuthUserFile ".$aa_PP['test_dir']."/.htpasswda1",
        "AuthType Basic",
        "Require valid-user",
		'</Files>'		
	);
	
    
	
	
	
	echo "<br /><br />$sep<h3>File Permissions and Writable Tests</h3>$sep";

    $mess= ( @wp_mkdir_p( $aa_PP['test_dir'] ) ) ? $success : $fail;
    echo "<h4>$mess Creating test folders</h4><pre>".$aa_PP['test_dir'].'</pre>';
    
    $mess= ( @is_writable($aa_PP['test_dir']) || @chmod($aa_PP['test_dir'],766)) ? $success : $fail;
    echo "<h4>$mess Test folder writable</h4><pre>".$aa_PP['test_dir'].'</pre>';
    
    $mess= ( @is_writable(ABSPATH.'wp-admin') || @touch(ABSPATH.'wp-admin/.htaccess')) ? $success : $fail;
    echo "<h4>$mess /wp-admin/.htaccess file is writable</h4><pre>".ABSPATH.'wp-admin/.htaccess'.'</pre>';
    
    $mess= ( aa_pp_insert_mark($aa_PP['htpasswd'],'AskApache PassPro',array()) ) ? $success : $fail;
    echo "<h4>$mess .htpasswda1 file is writable</h4><pre>".$aa_PP['htpasswd'].'</pre>';
    
    $mess= ( aa_pp_file_put_c($aa_PP['test_dir']."/test.php",$aa_pp_test_responder) ) ? $success : $fail;
    echo "<h4>$mess Create image test file</h4><pre>".$aa_PP['test_dir'].'/test.php</pre>';

    $mess= ( aa_pp_file_put_c($aa_PP['test_dir']."/401.php",$aa_pp_test_401) ) ? $success : $fail;
    echo "<h4>$mess Create 401 test file</h4><pre>".$aa_PP['test_dir'].'/401.php</pre>';
	
    $mess= ( aa_pp_insert_mark($aa_PP['test_dir'].'/.htaccess', 'Test', $aa_pp_1_htaccess_test) ) ? $success : $fail;
    echo "<h4>$mess .htaccess test file writable</h4><pre>".$aa_PP['test_dir'].'/.htaccess</pre>';
	



	
	echo "<br /><br />$sep<h3>PHP Capabilities Tests</h3>$sep";
	$fsock=( function_exists('fsockopen') && !@in_array('fsockopen', @explode(',',@ini_get('disable_functions'))) );
    $mess= ( $fsock ) ? $success : $fail;
    echo "<h4>$mess fsockopen enabled and allowed</h4>";
    
    $mess= ( @version_compare(phpversion(),'4.3.0','>=') ) ? $success : $fail;
    echo "<h4>$mess Compatible php version</h4>";
    



	echo "<br /><br />$sep<h3>.htaccess Capabilities Tests</h3>$sep";
	
    $rv=aa_pp_test_resp("{$test_base_url}test.php",'200'); 
	$mess= ( $rv[0]==200 ) ? $success : $fail;
	$aa_PP['htaccess_support'] = ( $rv[0]==200 ) ? '1' : '0';	
    echo "<h4>$mess <a href=\"http://www.askapache.com/htaccess/apache-htaccess.html\">.htaccess</a> capability detection</h4><pre>{$test_base_url}test.php</pre>";
	aa_pp_resp_code($rv[0],$rv[1]);
    

    $rv=aa_pp_test_resp("{$test_base_url}test.php?Q=1",'307'); 
	$mess= ( $rv[0]==307 ) ? $success : $fail;
    $aa_PP['mod_rewrite_support'] = ( $rv[0]==307 ) ? '1' : '0';
    echo "<h4>$mess <a href=\"http://www.askapache.com/htaccess/mod_rewrite-tips-and-tricks.html\">mod_rewrite</a> capability detection</h4><pre>{$test_base_url}test.php?Q=1</pre>";
	aa_pp_resp_code($rv[0],$rv[1]);

    
    $rv=aa_pp_test_resp("{$test_base_url}askapacheredirecttest",'305'); 
	$mess= ( $rv[0]==305 ) ? $success : $fail;
    $aa_PP['mod_alias_support'] = ( $rv[0]==305 ) ? '1' : '0';
	echo "<h4>$mess <a href=\"http://www.askapache.com/htaccess/seo-search-engine-friendly-redirects-without-mod_rewrite.html\">mod_alias</a> capability detection</h4><pre>{$test_base_url}askapacheredirecttest</pre>";
	aa_pp_resp_code($rv[0],$rv[1]);

    
    $rv=aa_pp_test_resp("{$test_base_url}test.php?askapachetest1",'503'); 
	$mess= ( $rv[0]==503 ) ? $success : $fail;
    $aa_PP['mod_security_support'] = ( $rv[0]==503 ) ? '1' : '0';
    echo "<h4>$mess <a href=\"http://www.askapache.com/htaccess/mod_security-htaccess-tricks.html\">mod_security</a> capability detection</h4><pre>{$test_base_url}test.php?askapachetest1</pre>";
	aa_pp_resp_code($rv[0],$rv[1]);
	
	
	
	echo "<br /><br />$sep<h3>Encryption Function Tests</h3>$sep";
	$oke=array();
	$oke['PLAIN']=$aa_PP['algorithms']['PLAIN'];
	
	if( function_exists('md5') ) $oke['MD5']=$aa_PP['algorithms']['MD5'];
	$mess= ( function_exists('md5') ) ? $success : $fail;
    echo "<h4>$mess md5 encryption function exists</h4>";

	if( function_exists('crypt') ) $oke['CRYPT']=$aa_PP['algorithms']['CRYPT'];
    $mess= ( function_exists('crypt') ) ? $success : $fail;
    echo "<h4>$mess crypt encryption function exists</h4>";

	if( function_exists('sha1') ) $oke['SHA1']=$aa_PP['algorithms']['SHA1'];
    $mess= ( function_exists('sha1') ) ? $success : $fail;
    echo "<h4>$mess sha1 encryption function exists</h4>";
	
	$aa_PP['algorithms']=$oke;
	$oke=array();



	echo "<br /><br />$sep<h3>Encryption Authentication Working</h3>$sep";
	$htpasswds=aa_pp_hashit('TEST');

    $mess= ( aa_pp_file_put_c($aa_PP['test_dir']."/passtest.php",$aa_pp_test_responder) ) ? $success : $fail;
    echo "<h4>$mess Create encryption test file</h4><pre>".$aa_PP['test_dir'].'/passtest.php</pre>';

    $mess= ( aa_pp_insert_mark($aa_PP['test_dir'].'/.htpasswda1','AskApache PassPro',$htpasswds) ) ? $success : $fail;
    echo "<h4>$mess Create .htpasswda1 test file</h4><pre>".$aa_PP['test_dir'].'/.htpasswda1'.'</pre>';
	
	foreach($aa_PP['algorithms'] as $key=>$value){
    	$rb=aa_pp_test_resp("{$test_base_url}passtest.php",'401',"fail{$key}"); 
		$rg=aa_pp_test_resp("{$test_base_url}passtest.php",'200',"test{$key}"); 
		$aa_PP['algorithms'][$key]['enabled'] = ( $rb[0]==401  && $rg[0]==200 ) ? '1' : '0';
		$mess= ( $aa_PP['algorithms'][$key]['enabled']=='1' ) ? $success : $fail;
		if($aa_PP['algorithms'][$key]['enabled']=='1'){
			$oke[$key]=$aa_PP['algorithms'][$key];
    		$aa_PP['htaccess_support'] = '1';
		}
    	echo "<h4>$mess <a href=\"http://www.askapache.com/online-tools/htpasswd-generator/\">{$key} encryption</a> capability detection</h4>";
		aa_pp_resp_code($rb[0],$rb[1]);
		aa_pp_resp_code($rg[0],$rg[1]);
	}
	$aa_PP['algorithms']=$oke;
	
    update_option('askapache_password_protect',$aa_PP);
}




function aa_pp_test_resp($url,$exp_code,$user_pass=''){
	
	aa_pp_mess("Testing {$url} expecting code {$exp_code}");
    
    $rbody=$data='';$resp_headers=array();
    
    //$path=wp_nonce_url($p, 'askapache-crazy-cache-backend');
    
    $ub = @parse_url($url);
    if(!isset($ub['host'])||empty($ub['host'])) return aa_pp_err("bad url {$url}");
    $proto	= ($ub['scheme']=='https')?'ssl://':'';
    $port	= (isset($ub['port'])&&!empty($ub['port'])) ? $ub['port']:($proto!='')?443:80;
    $path	= (isset($ub['path'])&&!empty($ub['path'])) ? $ub['path']:'/';
    $query	= (isset($ub['query'])&&!empty($ub['query'])) ? '?'.$ub['query'] : '';
    $host	= $ub['host'];
    $ipp	= @gethostbyname($host);
    $ip     = ($ipp!=$host) ? long2ip(ip2long($ipp)) : $host;
    $cookie = AUTH_COOKIE.'='.urlencode($_COOKIE[AUTH_COOKIE]);
	$ref	= $ub['scheme'].'://'.$ub['host'].'/';
    
    
    $headers=array(
    "GET {$path}{$query} HTTP/1.0",
    "Host: {$host}",
    'User-Agent: Mozilla/5.0 (AskApache/; +http://www.askapache.com/)',
    'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,*/*;q=0.5',
    'Accept-Language: en-us,en;q=0.5',
    'Accept-Encoding: none',
    'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
    'Connection: close',
    "Referer: {$ref}"
    );

	if(strlen($user_pass)>1) $headers[]='Authorization: Basic '.base64_encode($user_pass.":".$user_pass);
    $request=join(AA_CRLF,$headers).AA_CRLF.AA_CRLF;

    $fp=aa_pp_get_sock($proto.$ip, $port);
    if($fp){
		$rec=aa_pp_txrx($fp,$request);
        list($resp_headers,$rbody) = explode(AA_CRLF.AA_CRLF, trim($rec), 2);
        if(!preg_match("|HTTP/[0-1]\.[0-1] ($exp_code)|is", trim($resp_headers), $response_code))return aa_pp_err("bad response code {$request} {$resp_headers} {$reponse_code}");
        return array((int)$response_code[1],'<pre>'.$request.$resp_headers.'</pre>');
    } else return aa_pp_err("fp test failed for {$path}");
    
}


function aa_pp_resp_code($code,$other=''){
    $desc=( function_exists('get_status_header_desc') ) ? $code.' '.get_status_header_desc($code).'</a></p>' : $code.'</a></p>'."\n";
    echo '<p>Received Response Code: <a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-'.$code.'">'.$desc;
	if(AA_PP_DEBUG)echo $other;
}






// aa_pp_extract_mark
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_extract_mark( $filename, $marker ) {
    if (!file_exists($filename) || !is_readable($filename))return false;
    $result = array ();
    if ($markerdata = explode("\n", implode('',file($filename)))){
        $state = false;
        foreach ($markerdata as $markerline) {
            if (strpos($markerline,'# END '.$marker)!==false)$state = false;
            if ($state)$result[]=$markerline;
            if (strpos($markerline,'# BEGIN '.$marker)!==false)$state = true;
        }
    }
    return $result;
}//=========================================================================================================================






// aa_pp_insert_mark
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_insert_mark( $filename, $marker, $insertion ) {
    if (file_exists($filename) && !is_writable($filename) && @!chmod($filename,0666) && !touch($filename)) return false;
    if (!file_exists($filename) && is_writable(dirname($filename)) && touch($filename))$markerdata = '';
    else $markerdata = explode("\n", implode('',file($filename)));
    
    $f=fopen( $filename, 'w');
    $foundit = false;
    if ($markerdata) {
        $state = true;
        foreach ( $markerdata as $n => $markerline) {
            if (strpos($markerline,'# BEGIN '.$marker)!== false)$state = false;
            if($state) {
                if ($n+1 < count($markerdata))fwrite($f,"{$markerline}\n");
                else fwrite($f, "{$markerline}");
            }
            if (strpos($markerline, '# END ' . $marker) !== false) {
                if(is_array($insertion) && count($insertion) > 0){
                    fwrite($f,"# BEGIN {$marker}\n");
                    if (is_array($insertion)) foreach ( $insertion as $insertline ) fwrite($f, "{$insertline}\n");
                    fwrite($f, "# END {$marker}\n");
                }
                $state=true;
                $foundit=true;
            }
        }
    }
    if (!$foundit) {
        if(is_array($insertion) && count($insertion) > 0){
            fwrite($f,"# BEGIN {$marker}\n");
            foreach ($insertion as $insertline)fwrite($f, "{$insertline}\n");
            fwrite($f,"# END {$marker}\n");
        }
        
    }
    fclose($f);
    return true;
}//=========================================================================================================================





// aa_pp_hashit
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_hashit($format,$user='',$pass=''){
	global $aa_PP;
    aa_pp_mess('Created '.$format.' Hash');
    $hash='';
    switch ($format){
        case 'TEST':
		$hash=array();
		foreach($aa_PP['algorithms'] as $key=>$value)$hash[]=aa_pp_hashit($key,"test{$key}","test{$key}");
        return $hash;
        break;
        case 'PLAIN':
        $hash=$user.':'.$pass;
        break;
        case 'CRYPT':
        $seed = NULL;
        for ($i = 0; $i < 8; $i++) {$seed .= substr('0123456789abcdef', rand(0,15), 1);}
        $hash=$user.':'.crypt($pass, "$1$".$seed);
        break;
        case 'SHA1':
        $hash=$user.':{SHA}'.base64_encode(pack("H*", sha1($pass)));
        break;
        case 'MD5': // php.net/crypt.php#73619
        $saltt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
        $len = strlen($pass);$text = $pass.'$apr1$'.$saltt;$bin = pack("H32", md5($pass.$saltt.$pass));
        for($i = $len; $i > 0; $i -= 16) { $text .= substr($bin, 0, min(16, $i)); }
        for($i = $len; $i > 0; $i >>= 1) { $text .= ($i & 1) ? chr(0) : $pass{0}; }
        $bin = pack("H32", md5($text));
        for($i=0; $i<1000; $i++) { $new = ($i & 1) ? $pass : $bin; if ($i % 3) $new .= $saltt; if ($i % 7) $new .= $pass; $new .= ($i & 1) ? $bin : $pass; $bin = pack("H32", md5($new)); }
        for($i=0; $i<5; $i++) { $k = $i + 6; $j=$i + 12; if($j==16){ $j = 5; } $TRp = $bin[$i].$bin[$k].$bin[$j].$TRp; }
        $TRp = chr(0).chr(0).$bin[11].$TRp;
        $TRp = strtr(strrev(substr(base64_encode($TRp), 2)),"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
        "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
        $hash="$user:$"."apr1"."$".$saltt."$".$TRp;
        break;
    }

    return $hash;
}//=========================================================================================================================









// aa_pp_print_header
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_print_header(){
    global $aa_PP;
    ?>
    
    <p style="text-align:center; font-size:1.1em; margin-top:0; background-color:#E4F2FD; padding-top:10px; border-bottom:1px solid #CCC; padding-bottom:10px;">
    <?php echo $aa_PP['plugin_data']['Title'];?> <strong><?php echo $aa_PP['plugin_data']['Version'];?></strong> by <?php echo $aa_PP['plugin_data']['Author'];?>&nbsp;&nbsp;&nbsp;|&nbsp; <a href="http://www.askapache.com/seo/404-google-wordpress-plugin.html">Google 404 Plugin</a> - <a href="http://www.askapache.com/htaccess/apache-htaccess.html">.htaccess tutorial</a> - <a href="http://www.htaccesselite.com/">.htaccess help forum</a>
    </p><hr style="visibility:hidden;" />
    
    <div class="wrap" style="min-width:600px;">
    <h2><?php echo $aa_PP['plugin_data']['Name']; ?></h2>
    <br />
    <?php if($aa_PP['config_step']!='3'){?>
        <div class="updated" id="message" style="margin:.25em auto 0 auto;">
        <p>NOTE: This is an incredibly powerful plugin. This can easily take your site down temporarily.<br /><br />This plugin modifies 2 files on your server <strong>/.htaccess + /wp-admin/.htaccess</strong> this plugin does NOT modify wordpress.</p>
        <p>If you experience an error that you can't fix by disabling a security SID from the AskApache Password Protection Option Panel or resetting/re-activating the plugin, all you need to do is remove the sections added by the plugin from the 2 .htaccess files using ftp, ssh, webftp, or contact support, etc..  You should definately figure out how to do access those 2 files before you get going.  That said, this plugin is sweet have fun!  ;)</p>
        </div>
    <?php }
}//=========================================================================================================================




// aa_pp_show_encryptions
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_show_encryptions($label,$type=0){
    global $aa_PP;
    
    if($type==0)
	{ 
	?>
        <p><label><?php _e($label); ?><br />
        <select name="aapassformat" id="aapassformat">
        <?php foreach($aa_PP['algorithms'] as $key=>$value){?>
        	<option value="<?php echo $key;?>"<?php if($aa_PP['format']==$key)echo ' selected="selected"';elseif($aa_PP['algorithms'][$key]['enabled']!='1')echo ' disabled="disabled"';?>><?php echo $key;?>   </option>
        <?php }?>
        </select>
        </label></p>
     <?php
     } 
	 elseif($type==3)
	 {
     ?>
        <p><label><?php _e($label); ?><br />
        <input id="aapassformat" name="aapassformat" type="hidden" value="<?php echo $aa_PP['format']; ?>" /></label></p>
        <ul>
        <?php foreach($aa_PP['algorithms'] as $key=>$value){?>
        	<li><label><input class="valinp" name="aapassformat" id="aapassformat<?php echo strtolower($key);?>" type="radio" value="<?php echo $key;?>" <?php if($aa_PP['format']==$key)echo 'checked="checked"';
        	elseif($aa_PP['algorithms'][$key]['enabled']!='1')echo 'disabled="disabled"'; ?> /> <strong><?php echo $key;?></strong> -
            <?php echo $aa_PP['algorithms'][$key]['desc'];?></label></li>
        <?php }?>
        </ul>
    <?php
    }
    else if($type==4)
	{
     ?>
        <h4><?php _e($label); ?></h4>
        <?php foreach($aa_PP['algorithms'] as $key=>$value){?>
        	<p><strong><?php echo $key;?></strong> - <?php echo $aa_PP['algorithms'][$key]['desc'];?></p>
        <?php }?>
        <hr style="visibility:hidden;padding-top:.25em;clear:both;" />
    <?php
    }
}//=========================================================================================================================






// aa_pp_print_footer
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_print_footer(){
    global $aa_PP;
    ?>
    <hr style="visibility:hidden;clear:both;" />
    <div style="width:650px; margin:0 auto;">
    <div style="width:32%; overflow:hidden; float:left;">
    <h3 class="dbx-handle">About This Plugin</h3>
    <p><?php echo _e('Version:'); echo ' <strong>'.$aa_PP['plugin_data']['Version'].'</strong>'; ?></p>
    <p><?php echo _e('Info: '); echo str_replace($aa_PP['plugin_data']['Name'],'Plugin Homepage',$aa_PP['plugin_data']['Title']); ?></p>
    <p><?php _e('Author'); ?>: <?php echo $aa_PP['plugin_data']['Author']; ?></p>
    </div>
    <div style="width:32%; overflow:hidden; float:left;">
    <h3 class="dbx-handle">AskApache Links</h3>
    <p>&middot; <a href="http://www.askapache.com/seo/404-google-wordpress-plugin.html">Google 404 Plugin</a></p>
    <p>&middot; <a href="http://www.askapache.com/online-tools/htpasswd-generator/">.htpasswd Generator</a></p>
    <p>&middot; <a href="http://www.askapache.com/htaccess/apache-htaccess.html">htaccess tutorial</a></p>
    <p>&middot; <a href="http://perishablepress.com/press/2008/04/20/how-to-block-proxy-servers-via-htaccess/?AskApache" title="Perishable Press">Blocking Proxy</a></p>
    </div>
    <div style="width:32%; overflow:hidden; float:left;">
    <h3 class="dbx-handle">Security Articles</h3>
    <p>&middot; <a href="http://codex.wordpress.org/Hardening_WordPress">Hardening WordPress</a></p>
    <p>&middot; <a href="http://www.askapache.com/htaccess/mod_security-htaccess-tricks.html">mod_security tricks</a></p>
    <p>&middot; <a href="http://codex.wordpress.org/Changing_File_Permissions">WordPress File Perms</a></p></div><hr style="visibility:hidden;padding-top:.25em;clear:both;" />
    </div>
    </div>
    <hr style="visibility:hidden;padding-top:.25em;clear:both;" />
    
    <?php
}//=========================================================================================================================







// aa_pp_file_put_c
//---------------------------
function aa_pp_file_put_c($filename,$content){
    aa_pp_mess("creating {$filename}");
    if(@file_exists($filename)){
        aa_pp_mess("backing up {$filename}");
        if(!@copy($filename,$filename.time()))aa_pp_mess("couldnt backup {$filename}");
        aa_pp_unlink($filename);
    }
    
    if (function_exists("file_put_contents")) return @file_put_contents($filename, $content);
    if(!$fh = @fopen($filename, 'wb')) return aa_pp_err("couldnt fopen {$filename}");
    if(!@fwrite($fh, $content, strlen($content))) return aa_pp_err("couldnt fwrite  to {$filename}");
    if(!@fclose($fh)) return aa_pp_err("couldnt fclose {$filename}");
    return true;
}//=====================================================================================




// aa_pp_readfile
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_readfile($filename,$g=true){
    aa_pp_mess("reading {$f}");
    if(!$fh = @fopen($filename, 'rb')) return aa_pp_err("couldnt fopen {$filename}");
    if(!$filecontent = @fread($fh, @filesize($filename))) return aa_pp_err("couldnt fread {$filename}");
    if(!@fclose($fh)) return aa_pp_err("couldnt fclose {$filename}");
    
    if(!$g)return $filecontent;
    else echo htmlspecialchars($filecontent);
}//=========================================================================================================================



// aa_pp_mkdir
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_mkdir($dirname){
    aa_pp_mess("Creating temporary directory {$dirname}");
    if(!@wp_mkdir_p($dirname))return aa_pp_err("failed to create directory {$dirname}");
    return $TRpdirname;
}//=========================================================================================================================





//---------------------------
function aa_pp_rmdir($file) {
    $file=rtrim($file,'/');
    if(strpos($file,ABSPATH.'wp-content/askapache')===false) return aa_pp_err("cant remove this dir: {$file}");
    
    if (is_dir($file) && !is_link($file)) {
	    aa_pp_mess("deleting directory {$file}");
        $d=dir($file);
        while( false!==($r=$d->read())) {
            if($r=="."||$r==".."||is_link($d->path.$r))continue;
            if (!aa_pp_rmdir($d->path.'/'.$r)) aa_pp_err("Failed to remove ".$d->path.'/'.$r);
        }
        $d->close();
        aa_pp_mess("Removed temporary test directory {$dir}");
        return @rmdir($file);
    } else return aa_pp_unlink($file);
}//=====================================================================================



// aa_pp_1_unlink
//---------------------------
function aa_pp_unlink($f) {
    aa_pp_mess("deleting {$f}");
    clearstatcache();
    if(! @file_exists($f) )return true;
    if( @chmod($f,0777) && @unlink($f) )return true;
    $stat = @stat(@dirname($f)); $dp = $stat['mode'] & 0007777;
    if( @chmod(dirname($f),$dp) && @unlink($f) && @chmod(dirname($f),$stat['mode']))return true;
    if(! @file_exists($f) )return true;
    return aa_pp_err("couldnt delete {$f}");
}//=====================================================================================





// aa_pp_err
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_err($message=''){
	if(@defined('AA_PP_DEBUG_LOGFILE'))error_log($message, 3, AA_PP_DEBUG_LOGFILE);
	else error_log($message);
    return false;
}//=========================================================================================================================



// aa_pp_mess
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_mess($message=''){
	if(@defined('AA_PP_DEBUG_LOGFILE'))error_log($message, 3, AA_PP_DEBUG_LOGFILE);
	else if(AA_PP_DEBUG)error_log($message);
    if(AA_PP_DEBUG){ ?> <div id="message" class="updated fade" style="margin:1em auto;"><p><?php echo $message;?></p></div> <?php }
}//=========================================================================================================================








//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_generate_sid_rules($sid){
    global $aa_PP;
    
    switch ($sid){
        case 'sid700':
        $sidrules=array(
        'Options -Indexes',
		'DirectoryIndex index.html index.php '.$aa_PP['root_path'].'index.php'
        );
        break;
        case 'sid800':
        $sidrules=array(
        '<Files wp-login.php>',
        'Order Deny,Allow',
        'Deny from All',
        'Satisfy Any',
        '',
        'AuthName "'.$aa_PP['realm'].'"',
        'AuthUserFile '.$aa_PP['htpasswd'],
        'AuthType Basic',
        'Require valid-user',
        '</Files>'
        );
        break;
        case 'sid900':
        $sidrules=array(
        'Order Deny,Allow',
        'Deny from All',
        'Satisfy Any',
        '',
        'AuthName "'.$aa_PP['realm'].'"',
        'AuthUserFile '.$aa_PP['htpasswd'],
        'AuthType Basic',
        'Require valid-user',
        '',
        '<FilesMatch "\.(ico|pdf|flv|jpg|jpeg|mp3|mpg|mp4|mov|wav|wmv|png|gif|swf|css|js)$">',
        'Allow from All',
        '</FilesMatch>',
        '',
        '<FilesMatch "(async-upload)\.php$">',
        '<IfModule mod_security.c>',
        'SecFilterEngine Off',
        '</IfModule>',
        'Allow from All',
        '</FilesMatch>'
        );
        break;
        case 'modrewrite':
        $sidrules=array(
        'RewriteEngine On',
        'RewriteBase '.$aa_PP['root_path']
        );
        break;
        case 'sid1000':
        $sidrules=array(
        'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ '.$aa_PP['root_path'].'wp-content/.*$ [NC]',
        'RewriteCond %{REQUEST_FILENAME} !^.+flexible-upload-wp25js.php$',
        'RewriteCond %{REQUEST_FILENAME} ^.+\.(php|html|htm|txt)$',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1010':
        $sidrules=array(
        'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ '.$aa_PP['root_path'].'wp-includes/.*$ [NC]',
        'RewriteCond %{THE_REQUEST} !^[A-Z]{3,9}\ '.$aa_PP['root_path'].'wp-includes/js/.+/.+\ HTTP/ [NC]',
        'RewriteCond %{REQUEST_FILENAME} ^.+\.php$',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1011':
        $sidrules=array(
        //'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /.*%20.*\ HTTP/ [NC,OR]',
        //'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /.+(main|load|image|start)\.php.*\ HTTP/ [NC,OR]',
        'RewriteCond %{REQUEST_URI} !^'.$aa_PP['root_path'].'(wp-login.php|wp-admin/|wp-content/plugins/|wp-includes/).* [NC]',
        'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ ///.*\ HTTP/ [NC,OR]',
        'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /.*\?\=?(http|ftp|ssl|https):/.*\ HTTP/ [NC,OR]',
        'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /.*\?\?.*\ HTTP/ [NC,OR]',
        'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /.*\.(asp|ini|dll).*\ HTTP/ [NC,OR]',
        'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /.*\.(htpasswd|htaccess|aahtpasswd).*\ HTTP/ [NC]',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1012':
        $sidrules=array(
        'RewriteCond %{HTTP_REFERER} !^$',
        'RewriteCond %{REQUEST_URI} !^'.$aa_PP['root_path'].'(wp-login.php|wp-admin/|wp-content/plugins/|wp-includes/).* [NC]',
        'RewriteCond %{HTTP_REFERER} !^'.$aa_PP['scheme'].'://'.$_SERVER['HTTP_HOST'].'.*$ [NC]',
        'RewriteRule \.(ico|pdf|flv|jpg|jpeg|mp3|mpg|mp4|mov|wav|wmv|png|gif|swf|css|js)$ - [F,NS,L]'
        );
        break;
        case 'sid1015':
        $sidrules=array(
        'RewriteCond %{REQUEST_METHOD} !^(GET|HEAD|POST|PROPFIND|OPTIONS|PUT)$ [NC]',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1017':
        $sidrules=array(
        'RewriteCond %{HTTP:VIA}%{HTTP:FORWARDED}%{HTTP:USERAGENT_VIA}%{HTTP:X_FORWARDED_FOR}%{HTTP:PROXY_CONNECTION} !^$ [OR]',
        'RewriteCond %{HTTP:XPROXY_CONNECTION}%{HTTP:HTTP_PC_REMOTE_ADDR}%{HTTP:HTTP_CLIENT_IP} !^$',
        'RewriteCond %{REQUEST_URI} !^'.$aa_PP['root_path'].'(wp-login.php|wp-admin/|wp-content/plugins/|wp-includes/).* [NC]',
		'RewriteCond %{REQUEST_METHOD} =POST',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1018':
        $sidrules=array(
        'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ '.$aa_PP['root_path'].'.*/wp-comments-post\.php.*\ HTTP/ [NC]',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1019':
        $sidrules=array(
        'RewriteCond %{THE_REQUEST} !^[A-Z]{3,9}\ .+\ HTTP/(0\.9|1\.0|1\.1) [NC]',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1020':
        $sidrules=array(
        'RewriteCond %{REQUEST_URI} !^'.$aa_PP['root_path'].'(wp-login.php|wp-admin/|wp-content/plugins/|wp-includes/).* [NC]',
        'RewriteCond %{THE_REQUEST} !^[A-Z]{3,9}\ [a-zA-Z0-9\.\+_/\-\?\=\&]+\ HTTP/ [NC]',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1021':
        $sidrules=array(
		'RewriteCond %{REQUEST_METHOD} =POST',
        'RewriteCond %{REQUEST_URI} !^'.$aa_PP['root_path'].'(wp-admin/|wp-content/plugins/|wp-includes/).* [NC]',
        'RewriteCond %{HTTP:Content-Length} ^$',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1022':
        $sidrules=array(
		'RewriteCond %{REQUEST_METHOD} =POST',
        'RewriteCond %{REQUEST_URI} !^'.$aa_PP['root_path'].'(wp-login.php|wp-admin/|wp-content/plugins/|wp-includes/).* [NC]',
        'RewriteCond %{HTTP:Content-Type} !^(application/x-www-form-urlencoded|multipart/form-data.*(boundary.*)?)$ [NC]',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        /*case 'sid1023':
        $sidrules=array(
		'RewriteCond %{THE_REQUEST} !^[A-Z]{3,9}\ .*(\.\./|\./\.).*\ HTTP/ [NC]',
        'RewriteRule .* - [F,NS,L]'
        );
        break;*/
        case 'sid1024':
        $sidrules=array(
		'RewriteCond %{HTTP_COOKIE} ^.*PHPSESS?ID.*$',
		'RewriteCond %{HTTP_COOKIE} !^.*PHPSESS?ID=([0-9a-z]+);.*$',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1025':
        $sidrules=array(
        'RewriteCond %{REQUEST_URI} !^'.$aa_PP['root_path'].'(wp-login.php|wp-admin/|wp-content/plugins/|wp-includes/).* [NC]',
		'RewriteCond %{HTTP_HOST} ^$',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1026':
        $sidrules=array(
		'RewriteCond %{HTTP:Content-Disposition} \.php [NC]',
		'RewriteCond %{HTTP:Content-Type} image/.+ [NC]',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1027':
        $sidrules=array(
		'RewriteCond %{REQUEST_METHOD} =POST',
        'RewriteCond %{REQUEST_URI} !^'.$aa_PP['root_path'].'(wp-login.php|wp-admin/|wp-content/plugins/|wp-includes/).* [NC]',
		'RewriteCond %{HTTP_USER_AGENT} ^-?$',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1028':
        $sidrules=array(
		'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /.*/wp-comments-post\.php.*\ HTTP/ [NC]',
		'RewriteCond %{HTTP_REFERER} ^-?$',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
        case 'sid1029':
        $sidrules=array(
		'RewriteCond %{HTTP_USER_AGENT} ^.*(opera|mozilla|firefox|msie|safari).*$ [NC,OR]',
		'RewriteCond %{HTTP_USER_AGENT} ^-?$',
		'RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /.+/trackback/?\ HTTP/ [NC]',
		'RewriteCond %{REQUEST_METHOD} =POST',
        'RewriteRule .* - [F,NS,L]'
        );
        break;
		
        case 'sid1030':
        $sidrules=array(
        'RewriteCond %{HTTPS} !=on [NC]',
        'RewriteRule .* '.$aa_PP['scheme'].'://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]'
        );
        break;
        case 'modsecurity':
        $sidrules=array(
        'SecFilterEngine On',
        'SecFilterCheckURLEncoding On',
        'SecFilterCheckUnicodeEncoding Off',
        'SecFilterScanPOST On',
        'SecFilterDefaultAction "deny,nolog,noauditlog,status:403"'
        );
        break;
        /*
		case 'sid2000':
        $sidrules=array(
        'SecFilterSelective REQUEST_METHOD "^(GET|HEAD)$" "chain"',
        'SecFilterSelective HTTP_Content-Length "!^$"',
        '',
        'SecFilterSelective REQUEST_METHOD "!^(GET|HEAD)$" "chain"',
        'SecFilterSelective HTTP_Content-Type "!(^application/x-www-form-urlencoded$|^multipart/form-data;)"',
        '',
        'SecFilterSelective REQUEST_METHOD "^POST$" "chain"',
        'SecFilterSelective HTTP_Content-Length "^$"',
        '',
        'SecFilterSelective ARG_cache_lastpostdate "<\?php"',
        '',
        '<Files wp-comments-post.php>',
        'SecFilterSelective ARG_comment_post_ID "^$"',
        '</Files>'
        );
        break;
		*/
    }
    
    return $sidrules;
}//=========================================================================================================================



//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_erase_sid($sid){
    global $aa_PP;
    $aa_PP['S']["$sid"]['ON']='0';
    if(!aa_pp_insert_mark($aa_PP['S'][$sid]['FILE'],"AskApache $sid",''))return aa_pp_err('Failed to erase '.$sid."-".$aa_PP['S']["$sid"]['FILE']);
}//=========================================================================================================================




//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_activate_sid($sid){
    global $aa_PP;
    $aa_PP['S']["$sid"]['ON']='1';
    if(!aa_pp_insert_mark($aa_PP['S']["$sid"]['FILE'],"AskApache $sid",aa_pp_generate_sid_rules($sid)))return aa_pp_err('Failed to create '.$sid."-".$aa_PP['S']["$sid"]['FILE']);
}//=========================================================================================================================



// aa_pp_activate
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_activate(){
    global $aa_PP;
    $aa_PP=array();
    
    /* we start over every time because its good practice to know what your .htaccess is doing. */
    /* again I apologize but its better to be safe when the whole server is on the line. */
    
    $oldoptions=array('aa_home_folder','aa_wpadmin_folder','aa_htpasswd_file','aa_htaccess_file','aa_original_htpasswd','aa_original_htaccess','aa_plugin_message','aa_plugin_version','aa_home','aa_wpadmin',
    'aa_htpasswd_f','aa_htaccess_f','aa_user','aa_plugin_message','aa_home_folder','aa_wpadmin_folder','aa_htpasswd_file','aa_htaccess_file','aa_original_htpasswd','aa_original_htaccess','aa_plugin_message',
    'aa_plugin_version','aa_pp_docroot_htaccess','aa_pp_wp_includes_htaccess','aa_pp_wp_content_htaccess','aa_pp_wp_includes_htaccess','aa_pp_main_base64','aa_pp_ok');
    foreach($oldoptions as $key)$F=delete_option($key);
    
    $aa_PP['htpasswd'] = ABSPATH.'.htpasswda1';
    $aa_PP['htaccessusers']='';
    $aa_PP['realm']='Protected By AskApache';
    $aa_PP['user']='admin';
    $aa_PP['format']='SHA1';
    
    $home_path = parse_url(get_option('siteurl'));
    $aa_PP['scheme']                = $home_path['scheme'];
    $aa_PP['plugin_data']           = get_plugin_data(__FILE__);
    $aa_PP['pass']                  = '';
    $aa_PP['blog_root_htaccess']    = ABSPATH.'.htaccess';
    $aa_PP['root_path']             = rtrim($home_path['path'],'/').'/';
    $aa_PP['config_step']           = '1';
    $aa_PP['test_dir']              = ABSPATH.'wp-content/askapache';
    
    $aa_PP['htaccess_support']       = '0';
    $aa_PP['mod_alias_support']      = '0';
    $aa_PP['mod_security_support']   = '0';
    $aa_PP['mod_rewrite_support']    = '0';
    
    
    $aa_PP['algorithms']=array(
    'CRYPT'    =>    array('enabled'=>'0','desc'=>'Unix only. Uses the traditional Unix crypt function with a randomly-generated 32-bit salt.'),
    'MD5'    =>    array('enabled'=>'0','desc'=>'Base64-encoded SHA-1 digest of the password.'),
    'SHA1'    =>    array('enabled'=>'0','desc'=>'Apache-specific algorithm using an iterated MD5 digest of random 32-bit salt and the password.'),
    'PLAIN'    =>    array('enabled'=>'0','desc'=>'(i.e. unencrypted) Windows, BEOS, &amp; Netware only')
    );
    
    
    
    $aa_PP['S']['sid700']=array(
    'ON'=>'0',
    'TITLE'=>'Directory Protection',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-htaccess.html">Disable</a>',
    'DESC'=>'Enable the DirectoryIndex Protection, preventing directory index listings and defaulting.');
    
    $aa_PP['S']['sid800']=array(
    'ON'=>'0',
    'TITLE'=>'Password Protect wp-login.php',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-401">401</a>',
    'DESC'=>'Requires a valid user/pass to access the login page - *** Safe, Use.');
    
    $aa_PP['S']['sid900']=array(
    'ON'=>'0',
    'TITLE'=>'Password Protect wp-admin',
    'FILE'=>ABSPATH.'wp-admin/.htaccess',
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-401">401</a>',
    'DESC'=>'Requires a valid user/pass to access any non-static (css, js, images) file in this directory. - *** Safe, Use.');
    
    $aa_PP['S']['modrewrite']=array(
    'ON'=>'0',
    'TITLE'=>'Mod_Rewrite Support',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/mod_rewrite-tips-and-tricks.html">Read More</a>',
    'DESC'=>'Uses the Apache Module mod_rewrite');
    
    $aa_PP['S']['sid1000']=array(
    'ON'=>'0',
    'TITLE'=>'Protect wp-content',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-401">401</a>',
    'DESC'=>'Denies any Direct request for files ending in .php with a 403 Forbidden.. May break plugins/themes');
    
    $aa_PP['S']['sid1010']=array(
    'ON'=>'0',
    'TITLE'=>'Protect wp-includes',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies any Direct request for files ending in .php with a 403 Forbidden.. May break plugins/themes');
    
    $aa_PP['S']['sid1011']=array(
    'ON'=>'0',
    'TITLE'=>'Common Exploits',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Block common exploit requests with 403 Forbidden. These can help alot, may break some plugins.');
    
    $aa_PP['S']['sid1012']=array(
    'ON'=>'0',
    'TITLE'=>'Stop Hotlinking',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies any request for static files (images, css, etc) if referrer is not local site or empty.');
    
    $aa_PP['S']['sid1015']=array(
    'ON'=>'0',
    'TITLE'=>'Safe Request Methods',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies any request not using <a href="http://www.askapache.com/online-tools/request-method-scanner/">GET,PROPFIND,POST,OPTIONS,PUT,HEAD</a> - *** Safe, Use.');
    
    $aa_PP['S']['sid1017']=array(
    'ON'=>'0',
    'TITLE'=>'Forbid Proxies',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies any POST Request using a Proxy Server. Can still access site, but not comment.  See <a href="http://perishablepress.com/press/2008/04/20/how-to-block-proxy-servers-via-htaccess/">Perishable Press</a>');


    $aa_PP['S']['sid1018']=array(
    'ON'=>'0',
    'TITLE'=>'Real wp-comments-post.php',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies any POST attempt made to a non-existing wp-comments-post.php - *** Safe, Use.');

    $aa_PP['S']['sid1019']=array(
    'ON'=>'0',
    'TITLE'=>'HTTP PROTOCOL',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies any badly formed HTTP PROTOCOL in the request, 0.9, 1.0, and 1.1 only  - *** Safe, Use.');

    $aa_PP['S']['sid1020']=array(
    'ON'=>'0',
    'TITLE'=>'SPECIFY CHARACTERS',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies any request for a url containing characters other than "a-zA-Z0-9.+/-?=&"  - REALLY helps but may break your site depending on your links.');

    $aa_PP['S']['sid1021']=array(
    'ON'=>'0',
    'TITLE'=>'BAD Content Length',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies any POST request that doesnt have a Content-Length Header - *** Safe, Use.');

    $aa_PP['S']['sid1022']=array(
    'ON'=>'0',
    'TITLE'=>'BAD Content Type',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies any POST request with a content type other than application/x-www-form-urlencoded|multipart/form-data - *** Safe, Use.');

    $aa_PP['S']['sid1023']=array(
    'ON'=>'0',
    'TITLE'=>'Directory Traversal',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies Requests containing ../ or ./. which is a directory traversal exploit attempt - *** Safe, Use.');

    /*$aa_PP['S']['sid1024']=array(
    'ON'=>'0',
    'TITLE'=>'PHPSESSID Cookie',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Only blocks when a PHPSESSID cookie is sent by the user and it contains characters other than 0-9a-z - *** Safe, Use.');*/

    $aa_PP['S']['sid1025']=array(
    'ON'=>'0',
    'TITLE'=>'NO HOST:',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies requests that dont contain a HTTP HOST Header. - *** Safe, Use.');

    $aa_PP['S']['sid1026']=array(
    'ON'=>'0',
    'TITLE'=>'Bogus Graphics Exploit',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies obvious exploit using bogus graphics  - *** Safe, Use.');

    $aa_PP['S']['sid1027']=array(
    'ON'=>'0',
    'TITLE'=>'No UserAgent, No Post',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies POST requests by blank user-agents.  May prevent a small number of visitors from POSTING.');

    $aa_PP['S']['sid1028']=array(
    'ON'=>'0',
    'TITLE'=>'No Referer, No Comment',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies any comment attempt with a blank HTTP_REFERER field, highly indicative of spam.  May prevent some visitors from POSTING.');

    $aa_PP['S']['sid1029']=array(
    'ON'=>'0',
    'TITLE'=>'Trackback Spam',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-403">403</a>',
    'DESC'=>'Denies obvious trackback spam.   See <a href="http://ocaoimh.ie/2008/07/03/more-ways-to-stop-spammers-and-unwanted-traffic/">Holy Shmoly!</a>');


    
    $aa_PP['S']['sid1030']=array(
    'ON'=>'0',
    'TITLE'=>'SSL-Only Site',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html#status-301">301</a>',
    'DESC'=>'Redirects all non-SSL (https) requests to your https-enabled url');
    
    $aa_PP['S']['modsecurity']=array(
    'ON'=>'0',
    'TITLE'=>'Mod_Security Support',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/mod_security-htaccess-tricks.html">Read More</a>',
    'DESC'=>'Uses the Apache Module mod_security');
    
    /*
	$aa_PP['S']['sid2000']=array(
    'ON'=>'0',
    'TITLE'=>'Anti-Spam, Anti-Exploits',
    'FILE'=>$aa_PP['blog_root_htaccess'],
    'RESP'=>'<a href="http://www.askapache.com/htaccess/mod_security-htaccess-tricks.html">Read More</a>',
    'DESC'=>'Denies Obvious Spam and uses advanced mod_security protection');
	*/
    
    // delete these old files
    if(@is_file(ABSPATH.'wp-includes/.htaccess')) aa_pp_unlink(ABSPATH.'wp-includes/.htaccess');
    if(@is_file(ABSPATH.'wp-content/.htaccess')) aa_pp_unlink(ABSPATH.'wp-content/.htaccess');
    aa_pp_insert_mark($aa_PP['blog_root_htaccess'], 'AskApache PassPro', '');
    foreach($aa_PP['S'] as $n=>$sid)aa_pp_erase_sid($n);
    update_option('askapache_password_protect',$aa_PP);
}//=========================================================================================================================




// aa_pp_deactivate
//-------------------------------------------------------------------------------------------------------------------------
function aa_pp_deactivate(){
    global $aa_PP;
    $aa_PP=get_option('askapache_password_protect');
    foreach($aa_PP['S'] as $n=>$sid)aa_pp_erase_sid($n);
    aa_pp_insert_mark($aa_PP['blog_root_htaccess'], 'AskApache PassPro', '');
    delete_option('askapache_password_protect');
    unset($aa_PP);
}//=========================================================================================================================




register_activation_hook(__FILE__, 'aa_pp_activate');
register_deactivation_hook(__FILE__, 'aa_pp_deactivate');
if( strpos($_SERVER['REQUEST_URI'], basename(__FILE__))!==false ) add_action('admin_head', 'aa_pp_admin_header');
add_action('admin_menu', 'aa_pp_options_setup1');
?>