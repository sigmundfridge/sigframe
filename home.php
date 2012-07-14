<?php 
	get_header();
?>
<div id="container">

<?php include (TEMPLATEPATH . '/top.php'); ?>
  		<div class="h-three-col">
			<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Home Area Left') ) : // begin primary sidebar widgets ?>
 				<h2>Space for Rent</h2>
				<p>You can put any widget in this area....</p>
			<?php endif; ?>
		</div>		
		<div class="h-three-col">
			<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Home Area Right') ) : // begin primary sidebar widgets ?>
				<h2>Tag Cloud</h2>
				<ul class="etc">
					<li><?php if (function_exists('wp_tag_cloud') ) : ?>
							<?php wp_tag_cloud('smallest=8&largest=26&'); ?>
						<?php endif; ?>
					</li>
				</ul>
			<?php endif; ?>					
		</div>  
</div><!-- #container -->
<?php include (TEMPLATEPATH . '/bottom.php'); ?>
<?php get_footer() ?>