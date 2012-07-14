<div class="clear"></div>

<div class="two-col">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('First Footer Area') ) : // begin primary sidebar widgets ?>
	<h2>About</h2>
	<p>Futurosity Magazine Theme was designed by <a href="http://www.upstartblogger.com/" title="Upstart Blogger">Upstart Blogger</a> and is based on a designed previously used on <a href="http://www.futurosity.com/" title="Futurosity">Futurosity</a>. You will always find the latest information for this theme at <a href="http://www.upstartblogger.com/wordpress-theme-upstart-blogger-futurosity-magazine" title="Permalink to WordPress Theme: Upstart Blogger Futurosity Magazine">WordPress Theme: Upstart Blogger Futurosity Magazine Theme</a>.</strong></p>
	<p>This text can be replaced with a widget in the widget menu ('First Footer Area')</p>
<? endif; ?>
</div>

<div class="two-col">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Second Footer Area') ) : // begin primary sidebar widgets ?>
  	<h2>Recent posts/comments or similar?</h2>
	<p> Space is for rent.....</p>
<? endif; ?>
</div>
 
<div class="one-col">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Third Footer Area') ) : // begin primary sidebar widgets ?>
  <h2>Join</h2>
	<p><strong>Join <?php bloginfo('blog_name'); ?></strong>. Post comments and submit stories&mdash;engage, converse, create. Login, or join now.</p>
	<ul>
		<?php wp_register() ?>
		<li><?php wp_loginout() ?></li>
		<?php wp_meta() ?>
	</ul>
<? endif; ?>
</div>
 
 <div class="one-col">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Fourth Footer Area') ) : // begin primary sidebar widgets ?>
  	<h2>Subscribe</h2>
	<ul>
		<li><a href="<?php bloginfo('rss2_url') ?>" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?> <?php _e('Posts RSS feed', 'sandbox'); ?>" rel="alternate" type="application/rss+xml"><?php _e('All posts', 'sandbox') ?></a></li>
		<li><a href="<?php bloginfo('comments_rss2_url') ?>" title="<?php echo wp_specialchars(bloginfo('name'), 1) ?> <?php _e('Comments RSS feed', 'sandbox'); ?>" rel="alternate" type="application/rss+xml"><?php _e('All comments', 'sandbox') ?></a></li>
	</ul>
	<a href="<?php bloginfo('rss2_url') ?>" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?> <?php _e('Posts RSS feed', 'sandbox'); ?>" rel="alternate" type="application/rss+xml"><img src="<?php bloginfo('template_url'); ?>/images/feedbot.gif" width="125" style="border:none;" alt="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?> <?php _e('Posts RSS feed', 'sandbox'); ?>" /></a>
<? endif; ?>
</div>