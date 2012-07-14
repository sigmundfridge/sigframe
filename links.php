<?php
/*
Template Name: Links Page
*/
?>
<?php get_header() ?>	
<?php include (TEMPLATEPATH . '/top.php'); ?>
<div id="container">
	<div id="content" class="links">
		<ul>		
			<?php $args = array('taxonomy'=>'link_category');
			$link_cats = get_categories($args);
			$id = $link_cats->term_id;
			$args2 = array('category'=>$id, 'between'=> '<br/>','show_description'=>true);
			wp_list_bookmarks($args2); 
			?>
 		</ul>
		 <div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->
<?php include (TEMPLATEPATH . '/bottom.php'); ?>	
<?php get_footer() ?>