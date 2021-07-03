<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />

<!-- Meta -->
<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
<meta name="description" content="<?php bloginfo('description'); ?>" />
<meta name="template" content="UCD as Blogool" />

<!-- Feeds -->
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS2 Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Comments RSS 2.0" href="<?php bloginfo('comments_rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="<?php bloginfo('name'); ?> RSS 0.92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!-- Favicon -->
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

<!-- WordPress Tags -->
<?php wp_head(); ?>
<?php wp_get_archives('type=monthly&format=link'); ?>
<script>if (top.location != self.location) top.location=self.location;</script>
</head>
<body>

<div id="doc2cols" class="clearfix">
	<div id="trlogin"><?php wp_loginout(); ?> | <?php wp_register(); ?></div>
	<div id="bd" class="clearfix">
