<?php get_header(); ?>
<div id="content">
	<div id="content_inner">
		<div id="hd">
			<h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
		</div><!--/hd -->
		
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
			<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="到《<?php the_title(); ?>》的永久链接"><?php the_title(); ?></a></h2>
			<p class="authordate">
				<span><?php the_time('Y') ?>年<?php the_time('m') ?>月<?php the_time('j') ?>日 <?php the_time('A') ?> <?php the_time('i') ?>:<?php the_time('s') ?> | 作者：<?php the_author_posts_link(); ?></span>
			</p>
			<div class="entry">
				<?php the_content('<br />查看全文 &raquo;'); ?>
			</div>
			<p class="postmetadata">
				分类：<?php the_category(', ') ?> | <?php comments_popup_link("没有评论","评论(1)","评论(%)"); ?> <?php edit_post_link('编辑',' | ',''); ?>
			</p>
		</div>

		<!--
		<?php trackback_rdf(); ?>
		-->

		<?php endwhile; ?>

		<div class="navigation">
            <div class="alignleft"><?php posts_nav_link('','','&laquo; 上一页') ?></div>
            <div class="alignright"><?php posts_nav_link('','下一页 &raquo;','') ?></div>
        </div>
	
	<?php else : ?>

		<br /><h2 class="center">没有找到结果。您可在右侧搜索框中重新尝试</h2>

	<?php endif; ?>

	</div>
</div><!--/content_inner -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>