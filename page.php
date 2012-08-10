<?php get_header() ?>
<?php include (TEMPLATEPATH . '/top.php'); ?>
<div id="container">
	<div id="left-col">	
		<?php the_post() ?>
		<h2 class="entry-title"><?php the_title(); ?></h2>
	</div>
	<div id="content">
		<div id="post-<?php the_ID(); ?>" class="<?php sandbox_post_class() ?>">
			<div class="entry-content">
				<?php the_content() ?>
				<?php wp_link_pages(array('before'           => "\t\t\t\t\t<div class='page-link'>".__('Pages: ', 'sandbox'),'after'            => "</div>\n",'next_or_number'   => 'number'))?>
				<?php edit_post_link(__('Edit', 'sandbox'),'<span class="edit-link">','</span>') ?>
			</div>
		</div><!-- .post -->

		<?php if ( get_post_custom_values('comments') ) comments_template() // Add a key+value of "comments" to enable comments on this page ?>
	</div><!-- #content -->
	<?php get_sidebar() ?>
</div><!-- #container -->
<?php include (TEMPLATEPATH . '/bottom.php'); ?>		
<?php get_footer() ?>