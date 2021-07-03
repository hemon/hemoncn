<?php get_header(); ?>
<div id="content">
	<div id="content_inner">
		<div id="hd">
			<h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
		</div><!--/hd -->
		
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		
			<div class="post" id="post-<?php the_ID(); ?>">
<script type="text/javascript">
function fsmaller(){
		var f = new Number(document.getElementById('entry_content').style.fontSize.substr(0,2));
		if (f>13)
		{
			f = f - 2;
			document.getElementById('entry_content').style.fontSize = f + "px";
			document.getElementById('entry_content').style.lineHeight = "160%";
			if (f==13)
			{
				document.getElementById('fsmaller').className = "fsizeDisabled";			
			}
			document.getElementById('flarger').className = "fsizeOK";
		}		
}
function flarger(){
		var f = new Number(document.getElementById('entry_content').style.fontSize.substr(0,2));
		if (f<19)
		{
			f = f + 2;
			document.getElementById('entry_content').style.fontSize = f + "px";
			document.getElementById('entry_content').style.lineHeight = "160%";
			if (f==19)
			{
				document.getElementById('flarger').className = "fsizeDisabled";			
			}
			document.getElementById('fsmaller').className = "fsizeOK";
		}
}
</script>

				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="到《<?php the_title(); ?>》的永久链接"><?php the_title(); ?></a></h2>
				<p class="authordate">
					<span><?php the_time('Y') ?>年<?php the_time('m') ?>月<?php the_time('j') ?>日 <?php the_time('A') ?> <?php the_time('i') ?>:<?php the_time('s') ?> | 作者：<?php the_author_posts_link(); ?></span>
				</p>
				<div class="entry">
					<?php the_content('<br />查看全文 &raquo;'); ?>
				</div>
				<p class="postmetadata">
					<span style="float: right;color: #999;"><a href="#comments" style="font-size: 14px;">发表评论</a>(<?php comments_number() ?>)</span> 分类：<?php the_category(', ') ?> <?php edit_post_link('编辑',' | ',''); ?>
				</p>
			</div>

			<?php comments_template(); ?>

		<?php endwhile; else: ?>
			
			<br /><h2 class="center">没有找到结果。您可在右侧搜索框中重新尝试</h2>

		<?php endif; ?>

	</div>
</div><!--/content_inner -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>