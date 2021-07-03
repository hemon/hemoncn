<?php // Do not delete these lines

	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
				?>

				<p class="nocomments"><?php echo $langpost_password_commenrs; ?><p>

				<?php
				return;
            }
        }

		/* This variable is for alternating comment background */
		$oddcomment = 'alt';

?>

<div id="interact">

	<?php if ($comments) : ?>

	<h3 id="comments">评论(<?php comments_number() ?>)</h3>
	<ol class="commentlist">

		<?php foreach ($comments as $comment) : ?>

		<li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
			<div class="commentmetadata"><span><?php comment_author() ?></span> - <?php comment_date('F jS, Y') ?> <?php comment_time() ?> </div>
			<?php comment_text() ?>
		</li>

		<?php /* Changes every other comment to a different class */
			if ('alt' == $oddcomment) $oddcomment = '';
			else $oddcomment = 'alt';
		?>

		<?php endforeach; /* end for each comment */ ?>
	</ol>

	<?php else : // this is displayed if there are no comments so far ?>

		<?php if ('open' == $post->comment_status) : ?> 
			<!-- If comments are open, but there are no comments. -->

		 <?php else : // comments are closed ?>
			<!-- If comments are closed. -->
			<p class="nocomments"><?php echo "评论关闭。";?></p>

		<?php endif; ?>
	<?php endif; ?>

	<?php if ('open' == $post->comment_status) : ?>
	<h3 id="respond">发表评论</h3>
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	<p><a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>"><?php echo "您必须登陆才可以发表评论"?></a></p>
	<?php else : ?>
	<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
		<?php if ( $user_ID ) : ?>
		<p><?php echo "您现在以"?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a> 的身份登录。<a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account"><?php echo "登出"?> &raquo;</a></p>
		<?php else : ?>
		<p><label for="author">姓名：</label><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" /> *必填</p>
		<p><label for="email">邮件：</label><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" /> *必填 (不会被公开)</p>
		<p><label for="url">网站：</label><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" /></p>
		<?php endif; ?>
		<p><textarea name="comment" id="comment" style="width: 100%;" rows="10" tabindex="4"></textarea></p>
		<p><input name="submit" type="submit" id="submit" tabindex="5" value="确定发表" />

		<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" /></p>
		<?php do_action('comment_form', $post->ID); ?>
	</form>
	<?php endif; // If registration required and not logged in ?>
	<?php endif; // if you delete this the sky will fall on your head ?>

</div>