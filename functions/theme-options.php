<?php

class sigFramework {

	private $themeName;
	private $shortName;
	private $sections;
	private $checkboxes;
	private $settings;
	private $options;

	public function __construct($sections) {
		$this->checkboxes = array();
		$this->settings = array();
		$this->sections = $sections;
		$this->get_settings();
		$this->themeName = get_current_theme();
		$this->shortName = 'sWOM';
	
		add_action( 'admin_menu', array( &$this, 'add_pages' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		
		if ( ! get_option( 'sigf_options' ) )
			$this->initialize_settings();
		else $this->options = get_option( 'sigf_options' );
		
	}

	public function add_pages() {
		$admin_page = add_theme_page( __( 'Theme Options',$shortName), __( 'Theme Options',$shortName ), 'manage_options', 'sigf-options', array( &$this, 'display_page' ) );
		
		add_action( 'admin_print_scripts-' . $admin_page, array( &$this, 'scripts' ) );
		add_action( 'admin_print_styles-' . $admin_page, array( &$this, 'styles' ) );
		
	}

	public function create_setting( $args = array() ) {
		
		$defaults = array(
			'id'      => 'default_field',
			'title'   => __( 'Default Field',$shortName ),
			'desc'    => __( 'This is a default description.',$shortName ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general',
			'choices' => array(),
			'class'   => '',
			'value' => null,
			'button' =>	'Upload',
			'alt' =>'',
			'children' => array()
		);
			
		extract( wp_parse_args( $args, $defaults ) );
		
		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class,
			'button' 	=> $button,
			'alt' 		=> $alt,
			'children'	=> $children
		);
		
		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;
		
		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'sigf-options', $section, $field_args );
	}


	public function display_page() {
			
//	if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false;
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true )
			$saved = "<div class='saved'><p><strong>Options saved</strong></p></div>";
		else $saved = '';	
		
echo 
<<<EOT
		<div class='wrap'>
			$saved
			<div class = 'options'>
				<form method='post' action='options.php'>
EOT;
			settings_fields( 'sigf_options' );
			echo get_screen_icon();				
echo 
<<<EOT
			<h2>$this->themeName Theme Options</h2>			
					<div id = "tab_wrap">
						<ul>
EOT;
foreach($this->sections as $id => $title) {
echo 						"<li><a href='#{$id}'>{$title}</a></li>";
}			
echo 
<<<EOT
						</ul>
EOT;
		$this->sigf_do_settings_sections( $_GET['page'] );
	
echo <<<EOT
					</div>
					<p class='submit'>
						<input name = 'submit' type='submit' class='button-primary' value='Save Options' />
					</p>
				</form>
			</div>
		</div>
EOT;
	}

	
	/**
	 * Description for section
	 *
	 * @since 1.0
	 */
	public function display_section() {
		// code		
//		print_r($this->options);
	}
	
	/**
	 * Description for About section
	 *
	 * @since 1.0
	 */
	public function display_carousel_section() {
	echo '<p>
			The headline category display uses jCarousel. For more information see the<a href="http://sorgalla.com/projects/jcarousel/">jCarousel homepage</a>
		  </p>'	;				
	}


	/**
	 * HTML output for text field
	 *
	 * @since 1.0
	 */
	public function display_setting( $args = array() ) {
		
		extract( $args );
		$options = $this->options;	
		if(is_array($id)) {
			$id_list = $id;
			$name = ''.implode('][',$id).'';	
			$array_ref = implode("']['",$id);
			$id = str_replace ("'", '', implode('__', $id));
		}
		elseif(!is_array($id_list)&&$args['type']=='array'){
			$id_list[]= $args['id'];
			$array_ref = $id;
		}
		else {
			$name = $id;			
			$array_ref = $id;
		}
		
		$exec = "\$value = esc_attr(\$options['".$array_ref."']);";
		eval($exec);
		
		if ( empty( $value ) && $type != 'checkbox' )
			$value = $std;
		elseif ( empty( $value ) )
			$value = $std;		
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;

		switch ( $type ) {
			
			case 'array':
				foreach($children as $key=>$child){
//					$quoted_key = "'".$key."'";
					$quoted_key = $key;
					$old_list = $id_list;
					array_push($id_list,$quoted_key);
					$child['id']= $id_list;
					$child['value'] = $value;
					$this->display_setting($child);
					$id_list=$old_list;
				}
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span>';
				
				break;		
				
			case 'heading':
				echo '<h4>' . $desc . '</h4>';
			break;
			
			case 'checkbox':
				
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="sigf_options[' . $name . ']" value="1" ' . checked( $value, 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';		
				break;
				
			case 'select':
				echo '<select class="select' . $field_class . '" name="sigf_options[' . $name . ']">';
				
				foreach ( $choices as $opt_value => $label )
					echo '<option value="' . esc_attr( $opt_value ) . '"' . selected( $value, $opt_value, false ) . '>' . $label . '</option>';
				
				echo '</select>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
			
			case 'radio':
				$i = 0;
				foreach ( $choices as $opt_value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="sigf_options[' . $name . ']" id="' . $id . $i . '" value="' . esc_attr( $opt_value ) . '" ' . checked( $value, $opt_value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
			
			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="sigf_options[' . $name . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $value ) . '</textarea>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
			
			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="sigf_options[' . $name . ']" value="' . esc_attr( $value ) . '" />';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
				
				case 'image':
				if ( $desc != '' )
					$desc_html = '<br /><span class="description">' . $desc . '</span>';
		 		else $desc_html='';
		 		if($value)
		 			$image  = '<img class = "exist-preview" id="logo-p" src="'.$value.'" alt="'.$alt.'" />';
				else $image = '';
				
		 		echo 
<<<EOT
					<input class="regular-text upload_field $field_class" type="text" id=" $id " name="sigf_options[$name]" placeholder=" $std " value="$value" />
					<input class="upload_image_button" type="button" value="$button" />
					$desc_html
			 		<div id="logo-preview" class = "img-preview">
			 		$image
			 		</div>
EOT;
		 		break;
		 		 		
			case 'hidden':
		 		echo '<input type="hidden" id="' . $id . '" name="sigf_options[' . $name . ']" value="' . esc_attr( $options[$id] ) . '" />';
			break;
			
			case 'category_filter':
				$categories = get_categories('hide_empty=0');
				foreach($categories as $category) {
					$new[$category->term_id]=$category;
				}
				echo '<div class = "cat_menu"><ul id = "sort_cat">';
				$desc = $args['desc'];
				unset($args['desc']);
				foreach($choices as $choice) {
					echo '<li><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>';
					echo $new[$choice]->cat_name.' ('.$new[$choice]->category_count.')';
					$args ['type'] = 'array';
					$old_args=$args['id'];
					$args ['id'] = array($args['id'], $choice);
					$this->display_setting($args);
					echo '</li>';
					$args['id']=$old_args;
				};	
			echo '</ul></div>';
			echo $desc;
			break;
		 	
			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="sigf_options[' . $name . ']" placeholder="' . $std . '" value="' . esc_attr( $value ) . '" />';
		 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		
		 		break;
		 	
		}
		
	}
	
	/**
	 * Settings and defaults
	 * 
	 * @since 1.0
	 */
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
			'button' => __('Upload Logo')
		);
		
		$this->settings['favi'] = array(
			'title'   => __( 'Custom Favicon' ),
			'desc'    => __( 'Enter a URL or upload an image. Images should be 16x16 and ico, png or gif format' ),
			'std'     => '',
			'type'    => 'image',
			'section' => 'general',
			'alt' => __('Preview of site favicon'),
			'button' => __('Upload Favicon')
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
			'choices' => array_merge(array(
							'0' => '*No Headline*',
							'-1' => '*All Posts')
							, $choices)
		);
		
		$this->settings['head_tracker'] = array(
			'title'   => __( 'Tracking Code' ),
			'desc'    => __( 'Paste your analytics code here. It will be inserted into the head tag of your site' ),
			'std'     => '',
			'type'    => 'textarea',
			'section' => 'general'
		);
		
		/* Headline Layout
		===========================================*/
		
		$all_ids = get_all_category_ids();
		if(empty($this->options['cat_order']))   $cat_order = $all_ids;								
		else $cat_order = array_keys($this->options['cat_order']);
		$merged = array_merge($cat_order, array_diff($all_ids, $cat_order));
		
		$this->settings['cat_order'] = array(
			'title'   => __( 'Maximum headline categories' ),
			'desc'    => __( 'Select the max number of headline categories to display (empty ones may be ignored)' ),
			'type'    => 'category_filter',
			'section' => 'head_layout',
			'choices' => $merged,
			'children'   => array(
				'post_no' => array(
					'desc'   => __(''),
					'type'    => 'text',
					'std'	  => '3'
				),
				'home_no' => array(
					'desc'   => __(''),
					'type'    => 'text',
					'std'	  => '5'
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
					'std'	  => '0'
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
					'std'	  => '0'
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
			'title'   => __( 'Maximum height of featured images' ),
			'desc'    => __( 'Enter an integer maximum height for all featured images' ),
			'std'     => '150',
			'type'    => 'text',
			'section' => 'head_images',
		);	
		
		
		/* Carousel
		===========================================*/		
		
		$wrap_options = array('none','circular', 'first', 'last', 'both');
		$this->settings['wrap'] = array(
			'title'   => __( 'Wrap style' ),
			'desc'    => __( 'Choose a wrap method i.e. how the carousel behaves when it reaches the last/first category' ),
			'type'    => 'select',
			'std'     => 'none',
			'section' => 'carousel',
			'choices' => array_combine(array_values($wrap_options), array_values($wrap_options))
		);
		
		$anim = array('linear','jswing','easeInQuad','easeOutQuad','easeInOutQuad','easeInCubic','easeOutCubic','easeInOutCubic','easeInQuart','easeOutQuart','easeInOutQuart','easeInSine','easeOutSine','easeInOutSine','easeInExpo','easeOutExpo','easeInOutExpo','easeInQuint','easeOutQuint','easeInOutQuint','easeInCirc','easeOutCirc','easeInOutCirc','easeInElastic','easeOutElastic','easeInOutElastic','easeInBack','easeOutBack','easeInOutBack','easeInBounce','easeOutBounce','easeInOutBounce');
		$this->settings['easing'] = array(
			'title'   => __( 'Animation' ),
			'desc'    => __( 'Choose an animation style. For more information see the<a href="http://jqueryui.com/demos/effect/easing.html">jQuery easing demos</a>' ),
			'type'    => 'select',
			'std'     => 'easeInQuad',
			'section' => 'carousel',
			'choices' => array_combine(array_values($anim), array_values($anim))
		);
		
		$trigger = array('click','mouseover');
		$this->settings['trigger'] = array(
			'title'   => __( 'Trigger for scrolling' ),
			'desc'    => __( 'Scrolling can be triggered by left click or moving the cursor over the scroll bar' ),
			'type'    => 'select',
			'std'     => 'click',
			'section' => 'carousel',
			'choices' => array_combine(array_values($trigger), array_values($trigger))
		);
		
		$all_ids = get_all_category_ids();
		$choice_values = range(1, count($all_ids));
		$step = array_combine(range(1, count($all_ids)),array_values($choice_values));
		$this->settings['step'] = array(
			'title'   => __( 'Step size' ),
			'desc'    => __( 'How many categories to scroll on each cycle' ),
			'type'    => 'select',
			'std'     => '1',
			'section' => 'carousel',
			'choices' => $step
		);
			
		$this->settings['speed'] = array(
			'title'   => __( 'Animation speed (ms)' ),
			'desc'    => __( 'Enter an integer value for the animation speed in ms. Type "0" for off' ),
			'std'     => '100',
			'type'    => 'text',
			'section' => 'carousel',
		);	
		
		$this->settings['autoscroll'] = array(
			'title'   => __( 'Auto scroll delay (seconds)' ),
			'desc'    => __( 'Enter an integer value for the delay before the headlines auto-scroll (in seconds). Type "0" for no auto scrolling' ),
			'std'     => '0',
			'type'    => 'text',
			'section' => 'carousel',
		);	
		
		

		
		
/*		$this->settings['example_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'    => 'Example Heading',
			'type'    => 'heading'
		);
		
		$this->settings['example_radio'] = array(
			'section' => 'carousel',
			'title'   => __( 'Example Radio' ),
			'desc'    => __( 'This is a description for the radio buttons.' ),
			'type'    => 'radio',
			'std'     => '',
			'choices' => array(
				'choice1' => 'Choice 1',
				'choice2' => 'Choice 2',
				'choice3' => 'Choice 3'
			)
		);
		
		$this->settings['example_select'] = array(
			'section' => 'general',
			'title'   => __( 'Example Select' ),
			'desc'    => __( 'This is a description for the drop-down.' ),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => 'Other Choice 1',
				'choice2' => 'Other Choice 2',
				'choice3' => 'Other Choice 3'
			)
		);
*/		
		/* Appearance
		===========================================*/
		
/*		$this->settings['custom_css'] = array(
			'title'   => __( 'Custom Styles' ),
			'desc'    => __( 'Enter any custom CSS here to apply it to your theme.' ),
			'std'     => '',
			'type'    => 'textarea',
			'section' => 'appearance',
			'class'   => 'code'
		);
*/				
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
	
	/**
	 * Initialize settings to their default values
	 * 
	 * @since 1.0
	 */
	public function initialize_settings() {
		
		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' )
				$default_settings[$id] = $setting['std'];
		}
		
		update_option( 'sigf_options', $default_settings );
		
	}
	
	/**
	* Register settings
	*
	* @since 1.0
	*/
	public function register_settings() {
		
		register_setting( 'sigf_options', 'sigf_options', array ( &$this, 'validate_settings' ) );
		
		foreach ( $this->sections as $slug => $title ) {
			if ( $slug == 'carousel' )
				add_settings_section( $slug, $title, array( &$this, 'display_carousel_section' ), 'sigf-options' );
			else
				add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'sigf-options' );
		}
		
		$this->get_settings();
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$setting['value']=$this->options[$id];
			$this->create_setting( $setting );
			}
	}
	
	/**
	* jQuery Tabs
	*
	* @since 1.0
	*/
	public function scripts() {
	 	wp_enqueue_script('jquery-ui-sortable');
  		wp_enqueue_script('jquery-ui-tabs');
 		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('admin-scripts', get_stylesheet_directory_uri()."/functions/js/theme-admin-js.js");
	
		if ( function_exists( 'add_theme_support' ) ) { 
			add_theme_support( 'post-thumbnails' );
			if (isset($options['max_feat_h'])) $height = $options['max_feat_h'];
			else $height = 170;
			set_post_thumbnail_size( 134, $height, true ); // Normal post thumbnails
			add_image_size( 'featured_full', 400, 9999 ); // Permalink thumbnail size
		}
	}
	
	/**
	* Styling for the theme options page
	*
	* @since 1.0
	*/
	public function styles() {
		
		wp_enqueue_style("theme_style", get_stylesheet_directory_uri()."/functions/css/admin.css", false, "1.0", "all");
		//wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/base/jquery-ui.css'); 
		wp_enqueue_style('thickbox');
	
	}		

	
	/**
	* Validate settings
	*
	* @since 1.0
	*/
	public function validate_settings( $input ) {
		
		if ( ! isset( $input['reset_theme'] ) ) {
			$options = get_option( 'sigf_options' );
			
			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}
			
			return $input;
		}
		return false;
		
	}
	
	function sigf_do_settings_sections($page) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
		return;
	foreach ( (array) $wp_settings_sections[$page] as $section ) {
		if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]) )
			continue;
		echo '<div class="section" id = "'.$section['id'].'">';
		if ( $section['title'] )
			echo "<h3>{$section['title']}</h3>\n";
		call_user_func($section['callback'], $section);
		$this->sigf_do_settings_fields($page, $section['id']);
		echo '</div>';
		}
	}

function sigf_do_settings_fields($page, $section) {
	global $wp_settings_fields;

	if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
		return;

	foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
		echo 
<<<EOT
			<div class = 'option'>
				<label for='{$field['args']['label_for']}' class = 'description'> {$field['title']} </label>
				<span class ='opt_input'>
EOT;

		call_user_func($field['callback'], $field['args']);
		
		echo 
<<<EOT
				</span>
			</div>
		
EOT;
	}
}
	
}

$frame = new sigFramework (
	array(
		'general' => __('General Settings',$shortName),
		'head_layout' => __('Headlines - Layout',$shortName),
		'head_images' => __('Headlines - Images',$shortName),
		'carousel' => __('Headlines - Carousel',$shortName),
	)
);

function sigf_option( $option ) {
	$options = get_option( 'sigf_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}


?>