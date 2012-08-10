<?php get_header() ?>
<?php include (TEMPLATEPATH . '/top.php'); ?>
<div id = "container">
	<div id="left-col">
		<?php rewind_posts(); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<?php the_category(); ?>
		<h2 class="entry-title"><?php the_title(); ?></h2>
		<div class="excerpt"><?php the_excerpt(); ?></div>
		<p class="author">By <?php the_author_posts_link(); ?></p>
		<div class="author-desc"><p><?php the_author_meta('description'); ?></p></div>
		<div id="nav-above" class="navigation">					
			<h3>Browse in <?php 
			foreach((get_the_category()) as $cat) { 
				echo $cat->cat_name . ' '; 
				} ?>
			</h3>
			<div class="nav-previous"><?php previous_post_link('&laquo; %link', '%title', TRUE); ?></div>
			<div class="nav-next"><?php next_post_link('&raquo; %link', '%title', TRUE); ?></div>
		</div><!-- #nav-above -->
	</div>
	<div id="content">
		<div id="post-<?php the_ID(); ?>" class="<?php sandbox_post_class(); ?>">
			<div class="entry-content">
				<?php the_content(''.__('Read More <span class="meta-nav">&raquo;</span>', 'sandbox').''); ?>
				<?php 	wp_link_pages(array('before'=>"\t\t\t\t\t<div class='page-link'>".__('Pages: ', 'sandbox'),	'after' => '</div>\n','next_or_number'   => 'number',)); ?>
			<?php if (function_exists('the_tags') ) : ?>
				<div class = 'tags'><?php the_tags(); ?></div>
			<?php endif; ?>
			</div>		
			<div class="entry-meta">			
			<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) : // Comments and trackbacks open ?>
				<?php printf(__('<a class="comment-link" href="#respond" title="Post a comment">Post a comment</a> or leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'sandbox'), get_trackback_url()) ?>
			<?php elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) : // Only trackbacks open ?>
				<?php printf(__('Comments are closed, but you can leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'sandbox'), get_trackback_url()) ?>
			<?php elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) : // Only comments open ?>
				<?php printf(__('Trackbacks are closed, but you can <a class="comment-link" href="#respond" title="Post a comment">post a comment</a>.', 'sandbox')) ?>
			<?php elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) : // Comments and trackbacks closed ?>
				<?php _e('Both comments and trackbacks are currently closed.') ?>
			<?php endif; ?>
			</div>
			<div id="nav-below" class="navigation">
			 </div>
			<?php comments_template(); ?>
		<?php endwhile;?><?php endif; ?>							
		<?php the_post(); ?>
		</div><!-- .post -->
	</div>	
	<?php get_sidebar() ?>
</div><!-- #container -->
<?php include (TEMPLATEPATH . '/bottom.php'); ?>
<?php get_footer() ?>