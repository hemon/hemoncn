<style type="text/css">
#debuginfo {margin:0pt auto; max-width: 960px; min-width: 760px;}
fieldset {margin-top: 2em; display: block;}
</style>
<div style="text-align: left;" id="debuginfo">
	<fieldset>
		<legend><b>GET:</b></legend>
		<? echo '<pre>'.print_r($_GET, TRUE).'</pre>';?>
	</fieldset>
	<fieldset>
		<legend><b>POST:</b></legend>
		<? echo '<pre>'.print_r($_POST, TRUE).'</pre>';?>
	</fieldset>
	<fieldset>
		<legend><b>COOKIE:</b></legend>
		<? echo '<pre>'.print_r($_COOKIE, TRUE).'</pre>';?>
	</fieldset>
	<fieldset>
		<legend><b>SQL:</b> <?=count($_SGLOBAL['debug_query'])?></legend>
		<? echo '<pre>'.print_r($_SGLOBAL['debug_query'], TRUE).'</pre>';?>
	</fieldset>
	<fieldset>
		<legend><b>Include:</b> <? echo count(get_included_files());?></legend>
		<? echo '<pre>'.print_r(get_included_files(), TRUE).'</pre>';?>
	</fieldset>
</div>
