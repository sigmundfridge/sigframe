<?php get_header() ?>

<div id="container">
	<div id="left-col">	
		<h2 class="entry-title"><?php _e('Things Change', 'sandbox') ?></h2>
		<p class="archive-meta">Fiddlesticks. Things change. While you&rsquo;re here, though, why not have a look around?</p>
	</div>
	<div id="content">
		<div id="post-0" class="post error404">
			<div class="entry-content">
				<p>Apologies, but we can&rsquo;t find what you were looking for. Whatever it was, it probably never existed, or has been moved, deleted, or *gasp* lost.</p><p> You might try searching (see above, right). If you head over to the archives, you&rsquo;re sure to find something interesting.</p>
				<p> Or, just have a look at one of the random posts listed below.</p>
				<ul class = "no-list">
				<?php $rand_posts = get_posts( array( 'numberposts' => 5, 'orderby' => 'rand') );
				foreach( $rand_posts as $post ) : ?>
					<li>
						<h3><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h3>
					<?php if(has_excerpt($post->ID)):?>
						<?php the_excerpt(); ?>
					<?php endif; ?>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>		
		</div><!-- .post -->
	</div><!-- #content -->
	<?php get_sidebar() ?>
</div><!-- #container -->
<?php include (TEMPLATEPATH . '/bottom.php'); ?>	
<?php get_footer() ?>