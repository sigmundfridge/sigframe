<div id="category-head">
<?php 
$tag = $options['feat_tag'];
if($tag):
	if($tag == -1) $tag = '';
	$rand_posts = get_posts( array( 'numberposts' => 1, 'orderby' => 'rand', 'tag_id' => $tag ) );
	if(!empty($rand_posts)):
		foreach( $rand_posts as $post ) : ?>
			<h2 id = 'headline'>Featured Post | <a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
		<?php endforeach;
	endif;
endif;
if(!$options['cat']['never']) :
	if(is_single()&&$options['cat']['post']||is_home()&&$options['cat']['home']||is_archive()&&$options['cat']['archive']||is_page()&&$options['cat']['page']||is_search()&&$options['cat']['search']):	
		$cat_ids = $options['cat_order']['id'];
		if(is_home()) $post_nos = $options['cat_order']['home_no'];
		else $post_nos = $options['cat_order']['post_no'];
		
		$max_cat = $options['max_cat'];		
		$list_empty_cat = $options['cat_order']['empty'];
		$show_empty_cat=$options['empty_cat'];
		$cat_no = count_categories($max_cat, $list_empty_cat, $show_empty_cat);		
		
		?>
		
		<ul id="latestPosts" class = "<?php if($cat_no>6) echo "jcarousel-skin-simple carousel"; else echo "no-carousel"?>">
	
		<?php 
			for($i=0;$i<$max_cat;$i++) {
				$cat_id = $cat_ids[$i];
				$default = is_home() ? 3 : 1;
				$post_no = !empty($post_nos[$i]) ? $post_nos[$i] : $default;
				$args = array( 'numberposts' => $post_no, 'category' =>$cat_id);
				$category = get_category($cat_id);
	
				if($category->category_count>0 || $show_empty_cat){
		?>		
			<li>
				<ul class="latest">
					<li>
					<h2 class="latest"><a href="<?php echo esc_url(get_category_link( $cat_id )); ?>"><?php echo get_cat_name($cat_id); ?></a></h2></li>
		<?php 		$feat_posts = get_posts( $args );
					foreach( $feat_posts as $post ) : setup_postdata( $post );
	?>
					<li>
						<ul class = "latestPost">
							<li class="list-time"><?php the_time('d'); ?>.<?php the_time('M'); ?></li>
							<li class="list-title"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></li>
		<?php 
					$location = is_home() ? $options['home_img'] : $options['other_img'];
					if ( has_post_thumbnail()&&$location) : ?>
							<li><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" ><?php the_post_thumbnail(); ?></a></li>
		<?php 	
					endif; ?>
							<li class="latest-excerpt"><?php the_excerpt(); ?></li>
						</ul>
					</li>
		<?php 		endforeach; ?>
				</ul>
			</li>
	<?php 			} 
			} ?>
		</ul>
		<div class="clear"></div>
	<?php 
	endif;
endif;?>
</div>
