<?php
ob_start();
@header('HTTP/1.1 401 Authorization Required');
@header('Status: 401 Authorization Required');
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><html>
<head>
<title>401 Authorization Required</title>
</head>
<body>
<h1>Authorization Required</h1>
<p>Protected by <a href="http://www.askapache.com/wordpress/htaccess-password-protect.html">AskApache Password Protection</a></p>
</body>
</html>
<?php
$g=ob_get_clean();
echo $g;
exit;
exit();
?>