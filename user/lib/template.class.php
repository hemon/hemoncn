<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: template.class.php 12126 2008-01-11 09:40:32Z heyond $
*/

class template {

	//note var
	var $tpldir;
	var $objdir;

	var $tplfile;
	var $objfile;

	var $vars;//note 变量表
	var $force = 0;

	var $var_regexp = "\@?\\\$[a-zA-Z_][\\\$\w]*(?:\[[\w\-\.\"\'\[\]\$]+\])*";
	var $vtag_regexp = "\<\?=(\@?\\\$[a-zA-Z_][\\\$\w]*(?:\[[\w\-\.\"\'\[\]\$]+\])*)\?\>";
	var $const_regexp = "\{([\w]+)\}";

	var $language = '';

	function template() {
		$this->tpldir = UC_ROOT.'./view/default';
		$this->objdir = UC_DATADIR.'./view';
	}

	//note  publlic
	function assign($k, $v) {
		$this->vars[$k] = $v;
	}

	//note  publlic
	function display($file) {
		extract($this->vars, EXTR_SKIP);
		include $this->gettpl($file);
	}

	function gettpl($file) {
		isset($_REQUEST['inajax']) && ($file == 'header' || $file == 'footer') && $file = $file.'_ajax';
		isset($_REQUEST['inajax']) && ($file == 'admin_header' || $file == 'admin_footer') && $file = substr($file, 6).'_ajax';
		$this->tplfile = $this->tpldir.'/'.$file.'.htm';
		$this->objfile = $this->objdir.'/'.$file.'.php';

		//note 判断是否比较过期
		if($this->force || @filemtime($this->objfile) < filemtime($this->tplfile)) {
			if(empty($this->language)) {
				include $this->tpldir.'/templates.lang.php';
				$this->languages = &$languages;
			}
			$this->complie();
		}
		return $this->objfile;
	}

	function  complie() {
		$template = file_get_contents($this->tplfile);
		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
		$template = preg_replace("/\{lang\s+(\w+?)\}/ise", "\$this->lang('\\1')", $template);

		$template = preg_replace("/\{($this->var_regexp)\}/", "<?=\\1?>", $template);
		$template = preg_replace("/\{($this->const_regexp)\}/", "<?=\\1?>", $template);
		$template = preg_replace("/(?<!\<\?\=|\\\\)$this->var_regexp/", "<?=\\0?>", $template);

		$template = preg_replace("/\{\{eval (.*?)\}\}/ies", "\$this->stripvtag('<? \\1?>')", $template);
		$template = preg_replace("/\{eval (.*?)\}/ies", "\$this->stripvtag('<? \\1?>')", $template);
		$template = preg_replace("/\{for (.*?)\}/ies", "\$this->stripvtag('<? for(\\1) {?>')", $template);

		$template = preg_replace("/\{elseif\s+(.+?)\}/ies", "\$this->stripvtag('<? } elseif(\\1) { ?>')", $template);

		for($i=0; $i<2; $i++) {
			$template = preg_replace("/\{loop\s+$this->vtag_regexp\s+$this->vtag_regexp\s+$this->vtag_regexp\}(.+?)\{\/loop\}/ies", "\$this->loopsection('\\1', '\\2', '\\3', '\\4')", $template);
			$template = preg_replace("/\{loop\s+$this->vtag_regexp\s+$this->vtag_regexp\}(.+?)\{\/loop\}/ies", "\$this->loopsection('\\1', '', '\\2', '\\3')", $template);
		}
		$template = preg_replace("/\{if\s+(.+?)\}/ies", "\$this->stripvtag('<? if(\\1) { ?>')", $template);

		$template = preg_replace("/\{template\s+(\w+?)\}/is", "<? include \$this->gettpl('\\1');?>", $template);
		$template = preg_replace("/\{template\s+(.+?)\}/ise", "\$this->stripvtag('<? include \$this->gettpl(\\1); ?>')", $template);


		$template = preg_replace("/\{else\}/is", "<? } else { ?>", $template);
		$template = preg_replace("/\{\/if\}/is", "<? } ?>", $template);
		$template = preg_replace("/\{\/for\}/is", "<? } ?>", $template);

		$template = preg_replace("/$this->const_regexp/", "<?=\\1?>", $template);//note {else} 也符合常量格式，此处要注意先后顺??

		$template = "<? if(!defined('UC_ROOT')) exit('Access Denied');?>\r\n$template";
		$template = preg_replace("/(\\\$[a-zA-Z_]\w+\[)([a-zA-Z_]\w+)\]/i", "\\1'\\2']", $template);

//note 		file_put_contents($this->objfile, $template);
		$fp = fopen($this->objfile, 'w');
		fwrite($fp, $template);
		fclose($fp);
	}

	function stripvtag($s) {
		return preg_replace("/$this->vtag_regexp/is", "\\1", str_replace("\\\"", '"', $s));
	}

	function loopsection($arr, $k, $v, $statement) {
		$arr = $this->stripvtag($arr);
		$k = $this->stripvtag($k);
		$v = $this->stripvtag($v);
		$statement = str_replace("\\\"", '"', $statement);
		return $k ? "<? foreach((array)$arr as $k=>$v) {?>$statement<?}?>" : "<? foreach((array)$arr as $v) {?>$statement<? } ?>";
	}

	function lang($k) {
		return !empty($this->languages[$k]) ? $this->languages[$k] : "{ $k }";
	}
}

/*

Usage:
require_once 'lib/template.class.php';
$this->view = new template();
$this->view->assign('page', $page);
$this->view->assign('userlist', $userlist);
$this->view->display("user_ls");

*/

?>