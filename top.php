<div id="category-head">
<?php 
$swom_theme->sigf_generate_featured('featured');
$swom_theme->sigf_generate_headlines('cat_pages','cat_order', 'max_cat','empty_cat','head_img' ,array('home_no'=>is_home(),'post_no'=>!is_home()));
?>
	<div class="clear"></div>
</div>
