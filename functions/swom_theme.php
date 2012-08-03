<?php
require('sig_frame.php');

class SwomTheme extends SigFramework {

	function __construct($sections) {
		$this->shortName = 'sWOM';
    	parent::__construct($sections);
	}


	public function get_settings() {
		
		/* General Settings
		===========================================*/
		
		$this->settings['logo'] = array(
			'title'   => __( 'Custom Logo' ),
			'desc'    => __( 'Enter a URL or upload an image' ),
			'std'     => '',
			'type'    => 'image',
			'section' => 'general',
			'alt' => __('Preview of site logo'),
			'button' => __('Upload Logo'),
			'validation'=> 'text'
		);
		
		$this->settings['favi'] = array(
			'title'   => __( 'Custom Favicon' ),
			'desc'    => __( 'Enter a URL or upload an image. Images should be 16x16 and ico, png or gif format' ),
			'std'     => '',
			'type'    => 'image',
			'section' => 'general',
			'alt' => __('Preview of site favicon'),
			'button' => __('Upload Favicon'),
			'validation'=> 'text'
		);
		
		
		$tags = get_tags();
		$choices = array();
		foreach($tags as $tag) {
			$choices[$tag->term_id] = $tag->name;
		}
		
		$this->settings['featured'] = array(
			'title'   => __( 'Featured Tag' ),
			'desc'    => __( 'Posts with this tag will appear as "Featured Posts" in the header' ),
			'type'    => 'select',
			'std'     => '-1',
			'section' => 'general',
			'choices' => array(
							'-1' => '*All Posts*',
							'0' => '*No Headline*')
							+ $choices
		);
		
		$this->settings['head_tracker'] = array(
			'title'   => __( 'Tracking Code' ),
			'desc'    => __( 'Paste your analytics code here. It will be inserted into the head tag of your site' ),
			'std'     => '',
			'type'    => 'textarea',
			'section' => 'general',
			'validation' => 'html'
		);
		
		/* Headline Layout
		===========================================*/
		$all_ids = get_all_category_ids();
		if(!is_array($this->options['cat_order']))   $cat_order = $all_ids;								
		else {
			$cat_order = array_keys($this->options['cat_order']);
			$cat_order = array_intersect($cat_order,$all_ids);
		}
		$merged = array_merge($cat_order, array_diff($all_ids, $cat_order));
	
		$categories = get_categories('hide_empty=0');
		foreach($categories as $category) {
			$cat_label[$category->term_id] =  $category->cat_name.' ('.$category->category_count.')';
		}
		
		$choices = array();
		foreach($merged as $id) {
			$choices[$id] = $cat_label[$id];
		}		
		
		$this->settings['cat_order'] = array(
			'title'   => __( 'Choose category order and post count for headlines' ),
			'desc'    => __( '<p>Drag the category titles to set the order they will be displayed in the headlines.</p>
							<p>The number in the brackets is the total number of posts under each category</p>
							<p>Use the input boxes next to each category to determine how many posts will be displayed under each headline category on the front page, and on all other pages</p>
							<p>The default is 3 posts on the home page, and 1 on all other pages</p>
							' ),
			'header'  => "<div id = 'sort_title'>
							<div class = 'cat left'>Category (Total post count)</div><div class = 'right post_no'><span class = 'head'>Number of posts to display:</span><span class = 'post_home post_head'>Home</span><span class = 'post_other post_head'>Other</span></div>
						  </div>
						  <div class = 'clear'></div>",
			'class' => 'cat_menu',
			'type'    => 'category_filter',
			'section' => 'head_layout',
			'choices' => $choices,
			'children'   => array(
				'post_no' => array(
					'desc'   => __(''),
					'type'    => 'text',
					'std'	  => '3',
					'validation' => 'integer'
				),
				'home_no' => array(
					'desc'   => __(''),
					'type'    => 'text',
					'std'	  => '5',
					'validation' => 'integer'
				),

			)
		);
		
		$child_def = array();
		$default = array();
		foreach($this->settings['cat_order']['children'] as $name => $child) {
			$child_def[$name] = $child['std'];
		}
		foreach($merged as $id) {
			$default[$id] = $child_def;
		}
		$this->settings['cat_order']['std'] =$default;

		$max = array();
		$max_values = range(1, count($all_ids));
		$max = array_combine(range(1, count($all_ids)),array_values($max_values));		
		$this->settings['max_cat'] = array(
			'title'   => __( 'Maximum headline categories' ),
			'desc'    => __( 'Select the max number of headline categories to display (empty ones may be ignored)' ),
			'type'    => 'select',
			'std'     => count($all_ids),
			'section' => 'head_layout',
			'choices' => $max
		);
	
		
		$this->settings['empty_cat'] = array(
			'title'   => __( 'Show empty categories' ),
			'desc'    => __( 'Tick to show empty headline categories' ),
			'type'    => 'checkbox',
			'section' => 'head_layout',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		
		$this->settings['cat_pages'] = array(
			'title'   => __( 'Pages to display headlines' ),
			'desc'    => __( 'Show categories on ticked pages' ),
			'type'    => 'array',
			'section' => 'head_layout',
			'children'   => array(
				'never' => array(
					'desc'   => __( 'Never display (overrides all other boxes)' ),
					'type'    => 'checkbox',
					'std'	  => '0',
					'class'	  => 'deselect'
				),
				'home'=>array(
					'desc'   => __( 'Homepage' ),
					'type'    => 'checkbox',
					'std'	  => '1'
				),		
				'posts'=>array(
					'desc'   => __( 'Posts' ),
					'type'    => 'checkbox',
					'std'	  => '1'
				),		
				'pages'=>array(
					'desc'   => __( 'Pages' ),
					'type'    => 'checkbox',
					'std'	  => '0'
				),		
				'archives'=>array(
					'desc'   => __( 'Archives' ),
					'type'    => 'checkbox',
					'std'	  => '0'
				),		
				'search'=>array(
					'desc'   => __( 'Search' ),
					'type'    => 'checkbox',
					'std'	  => '0'
				),		
			)
		);

		/* Headline Images
		===========================================*/

		$this->settings['head_img'] = array(
			'title'   => __( 'Pages to display featured images under headline categories' ),
			'desc'    => __( 'Show headline featured images on ticked pages' ),
			'type'    => 'array',
			'section' => 'head_images',
			'children'   => array(
				'never' => array(
					'desc'   => __( 'Never display (overrides all other boxes)' ),
					'type'    => 'checkbox',
					'std'	  => '0',
					'class'	  => 'deselect'
				),
				'home'=>array(
					'desc'   => __( 'Homepage' ),
					'type'    => 'checkbox',
					'std'	  => '1'
				),		
				'posts'=>array(
					'desc'   => __( 'Posts' ),
					'type'    => 'checkbox',
					'std'	  => '1'
				),		
				'pages'=>array(
					'desc'   => __( 'Pages' ),
					'type'    => 'checkbox',
					'std'	  => '0'
				),		
				'all'=>array(
					'desc'   => __( 'All Headlines' ),
					'type'    => 'checkbox',
					'std'	  => '0'
				),			
			)
		);
		
	
		$this->settings['max_feat_h'] = array(
			'type'    => 'text',
			'title'   => __( 'Maximum height of featured images' ),
			'desc'    => __( 'Enter an integer maximum height for all featured images' ),
			'std'     => '150',
			'section' => 'head_images',
			'validation' => 'integer'
		);	
		
		
		/* Carousel
		===========================================*/		
		
		$wrap_options = array('none','circular', 'first', 'last', 'both');
		$this->settings['wrap'] = array(
			'type'    => 'select',
			'title'   => __( 'Wrap style' ),
			'desc'    => __( 'Choose a wrap method i.e. how the carousel behaves when it reaches the last/first category' ),
			'std'     => 'none',
			'section' => 'carousel',
			'choices' => array_combine(array_values($wrap_options), array_values($wrap_options))
		);
		
		$anim = array('linear','jswing','easeInQuad','easeOutQuad','easeInOutQuad','easeInCubic','easeOutCubic','easeInOutCubic','easeInQuart','easeOutQuart','easeInOutQuart','easeInSine','easeOutSine','easeInOutSine','easeInExpo','easeOutExpo','easeInOutExpo','easeInQuint','easeOutQuint','easeInOutQuint','easeInCirc','easeOutCirc','easeInOutCirc','easeInElastic','easeOutElastic','easeInOutElastic','easeInBack','easeOutBack','easeInOutBack','easeInBounce','easeOutBounce','easeInOutBounce');
		$this->settings['easing'] = array(
			'type'    => 'select',
			'title'   => __( 'Animation' ),
			'desc'    => __( 'Choose an animation style. For more information see the<a href="http://jqueryui.com/demos/effect/easing.html">jQuery easing demos</a>' ),
			'std'     => 'easeInQuad',
			'section' => 'carousel',
			'choices' => array_combine(array_values($anim), array_values($anim))
		);
		
		$trigger = array('click','mouseover');
		$this->settings['trigger'] = array(
			'type'    => 'select',
			'title'   => __( 'Trigger for scrolling' ),
			'desc'    => __( 'Scrolling can be triggered by left click or moving the cursor over the scroll bar' ),
			'std'     => 'click',
			'section' => 'carousel',
			'choices' => array_combine(array_values($trigger), array_values($trigger))
		);
		
		$all_ids = get_all_category_ids();
		$choice_values = range(1, count($all_ids));
		$step = array_combine(range(1, count($all_ids)),array_values($choice_values));
		$this->settings['step'] = array(
			'type'    => 'select',
			'title'   => __( 'Step size' ),
			'desc'    => __( 'How many categories to scroll on each cycle' ),
			'std'     => '1',
			'section' => 'carousel',
			'choices' => $step
		);
			
		$this->settings['speed'] = array(
			'type'    => 'text',
			'title'   => __( 'Animation speed (ms)' ),
			'desc'    => __( 'Enter an integer value for the animation speed in ms. Type "0" for off' ),
			'std'     => '100',
			'section' => 'carousel',
			'validation' => 'integer'
		);	
		
		$this->settings['autoscroll'] = array(
			'type'    => 'text',
			'title'   => __( 'Auto scroll delay (seconds)' ),
			'desc'    => __( 'Enter an integer value for the delay before the headlines auto-scroll (in seconds). Type "0" for no auto scrolling' ),
			'std'     => '0',
			'section' => 'carousel',
			'validation' => 'integer'
		);	

		/* Reset
		===========================================*/
/*		
		$this->settings['reset_theme'] = array(
			'section' => 'reset',
			'title'   => __( 'Reset theme' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset theme options to their defaults.' )
		);
*/		
	}
	
	public function register_sections() {
		foreach ( $this->sections as $slug => $title ) {
			if ( $slug == 'carousel' )
				add_settings_section( $slug, $title, array( &$this, 'display_carousel_section' ), 'sigf-options' );
			else
				add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'sigf-options' );
		}
	}
	
	/**
	 * Description for Carousel section
	 *
	 */
	public function display_carousel_section() {
	echo '<p>
			The headline category display uses jCarousel. For more information see the <a href="http://sorgalla.com/projects/jcarousel/">jCarousel homepage</a>
		  </p>'	;				
	}
	
	public function admin_scripts() {
		wp_enqueue_script('admin-scripts', get_stylesheet_directory_uri()."/functions/js/theme-admin-js.js");
		$this->register_thumbs($this->options['max_feat_h']);
		parent::admin_scripts();
	}
	
	public function theme_init() {
		$this->register_thumbs($this->options['max_feat_h']);
		parent::theme_init();
	}

	public function do_theme_script() {
		
		$options=$this->options;
		
		wp_register_script("jcarousel-js", get_stylesheet_directory_uri()."/scripts/jquery.jcarousel.min.js",array("jquery"));
		wp_register_script("carousel-start", get_stylesheet_directory_uri()."/scripts/carousel-main.js",array("jcarousel-js"));
		wp_register_script("jquery-ui", "https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js", array	("jcarousel-js"));
		$carousel_count = $this->category_count($options['cat_order'], $options['max_cat'], $options['empty_cat']);
		if($carousel_count>6) {
			wp_enqueue_script('jquery');
			wp_enqueue_script("jcarousel-js");
			wp_enqueue_script("carousel-start");
			wp_enqueue_script("jquery-ui");
		}
		$params = array('carousel_size' => $carousel_count,'carousel_easing'=>$options['easing'],'carousel_step' => $options['step'], 'carousel_wrap'=>__($options['wrap']),'carousel_speed'=>$options['speed'],'carousel_auto'=>$options['autoscroll'],'carousel_trigger'=>$options['trigger']);
		wp_localize_script( 'carousel-start', 'carouselParam', $params);
	}
	
	public function do_theme_style() {
		wp_enqueue_style('jcarousel-css', get_stylesheet_directory_uri()."/css/jcarousel-simple/skin.css");
	}
	
	public function sigf_generate_headlines($location_id,$order_id, $max_id, $empty_id, $image_id, $post_labels){
		$options = $this->options;
		if($this->sigf_shouldihere($options[$location_id])) {
			
			$show_images = $this->sigf_shouldihere($options[$image_id]);
			$list = parent::sigf_generate_headline_list($order_id, $max_id, $empty_id, $post_labels, $show_images);
			$category_count = $this->category_count($options[$order_id], $options[$max_id], $options[$empty_id]);
			$carousel = $category_count>6 ? 'jcarousel-skin-simple carousel' : 'no-carousel';
			echo 
<<<EOT
			<ul id="latestPosts" class = "$carousel">$list</ul>
EOT;
		
		}
		else echo '';
	}

}

$swomTheme = new SwomTheme (
	array(
		'general' => __('General Settings',$shortName),
		'head_layout' => __('Headlines - Layout',$shortName),
		'head_images' => __('Headlines - Images',$shortName),
		'carousel' => __('Headlines - Carousel',$shortName),
	)
);

?>