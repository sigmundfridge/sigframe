<?php

class SigFramework {

	protected $themeName;
	protected $checkboxes;
	protected $shortName;
	protected $sections;
	protected $settings;
	protected $options;
	protected $validation_ref = array();


	public function __construct($sections) {
		$this->checkboxes = array();
		$this->settings = array();
		$this->sections = $sections;
		$this->sigf_get_settings();
		$this->themeName = get_current_theme();
	
		add_action( 'admin_menu', array( &$this, 'sigf_add_admin_pages' ) );
		add_action( 'admin_init', array( &$this, 'sigf_register_admin_settings' ) );
		add_action( 'after_setup_theme', array(&$this, 'sigf_theme_init'));
		
		if ( ! get_option( 'sigf_options' ) )
			$this->sigf_initialize_settings();
		else $this->options = get_option( 'sigf_options' );
		
	}

	public function sigf_add_admin_pages() {
		$admin_page = add_theme_page( __( 'Theme Options',$shortName), __( 'Theme Options',$shortName ), 'manage_options', 'sigf-options', array( &$this, 'sigf_display_page' ) );
		
		add_action( 'admin_print_scripts-' . $admin_page, array( &$this, 'sigf_admin_scripts' ) );
		add_action( 'admin_print_styles-' . $admin_page, array( &$this, 'sigf_admin_styles' ) );
		
	}

	public function sigf_create_setting( $args = array() ) {
		
		$defaults = array(
			'id'      => 'default_field',
			'title'   => __( 'Default Field',$shortName ),
			'desc'    => __( 'This is a default description.',$shortName ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general',
			'choices' => array(),
			'class'   => '',
			'value'   => null,
			'button'  =>	'Upload',
			'alt' 	  =>'',
			'header'  => '',
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
			'header'	=> $header,
			'children'	=> $children
		);
		
		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;
		
		add_settings_field( $id, $title, array( $this, 'sigf_display_setting' ), 'sigf-options', $section, $field_args );
	}


	public function sigf_display_page() {
			
//	if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false;
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true )
			$saved = "<div class='saved'><p><strong>Options saved</strong></p></div>";
		else $saved = '';	
			$name = esc_html($this->themeName);
		
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
			<h2>$name Theme Options</h2>	
					<div id = "tab_wrap">
						<ul>
EOT;
		foreach($this->sections as $id => $title) {
echo 		"<li><a href='#".esc_attr($id)."'>".esc_html($title)."</a></li>";
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
	public function sigf_display_section() {
		settings_errors('sigf_options');
	}


	public function sigf_get_field_value($id,$options) {
		if(is_array($id) && count($id)===1) $id = $id[0];
		
		if(!is_array($id)) {
			return $options[$id];
		}
		else {
			$id_this = array_shift($id); 
			return $this->sigf_get_field_value($id, $options[$id_this]);
		}
	}
	
	public function sigf_get_field_labels($id) {
		if(!is_array($id)) {
			return array('name'=>$id, 'id_label'=>$id);
		}
		else {
			return array('name'=>implode("][",$id),'id_label'=>str_replace ("'", '', implode('__', $id))) ;
		}
	}

	
	/**
	 * HTML output for text field
	 *
	 * @since 1.0
	 */
	public function sigf_display_setting( $args = array() ) {
		
		extract( $args );
		$value = $this->sigf_get_field_value($id, $this->options);
		extract($this->sigf_get_field_labels($id));
				
		if ( empty( $value ) && $type != 'checkbox' )
			$value = $std;
		elseif ( empty( $value ) )
			$value = 0;		
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . esc_attr($class);

		switch ( $type ) {
			
			case 'array':
				foreach($children as $key=>$child){
					$new_list = (array)$id;
					$new_list[] = $key;
					$child['id']= $new_list;
					$child['value'] = $value;
					$this->sigf_display_setting($child);
				}
				if ( $desc != '' )
					echo '<span class="description array">' . esc_html($desc) . '</span>';
				
				break;		
				
			case 'checkbox':
				
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . esc_attr($id_label) . '" name="sigf_options[' . esc_attr($name) . ']" value="1" ' . checked( $value, 1, false ) . ' /> <label for="' . esc_attr($id_label) . '">' . esc_html($desc) . '</label>';		
				break;
				
			case 'select':
				echo '<select class="select' . $field_class . '" name="sigf_options[' . $name . ']">';
				
				foreach ( $choices as $opt_value => $label )
					echo '<option value="' . esc_attr( $opt_value ) . '"' . selected( $value, $opt_value, false ) . '>' . esc_html($label) . '</option>';
				
				echo '</select>';
				
				if ( $desc != '' )
					echo '<span class="description">' . esc_html($desc) . '</span>';
				
				break;
			
			case 'radio':
				$i = 0;
				foreach ( $choices as $opt_value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="sigf_options[' . $name . ']" id="' . esc_attr($id_label . $i) . '" value="' . esc_attr( $opt_value ) . '" ' . checked( $value, $opt_value, false ) . '> <label for="' . esc_attr($id_label . $i) . '">' . esc_html($label) . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}
				
				if ( $desc != '' )
					echo '<span class="description">' . esc_html($desc) . '</span>';
				
				break;
			
			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . esc_attr($id_label) . '" name="sigf_options[' . $name . ']" placeholder="' . esc_attr($std) . '" rows="5" cols="30">' .esc_textarea(html_entity_decode( $value )) . '</textarea>';
				
				if ( $desc != '' )
					echo '<span class="description">' . esc_html($desc) . '</span>';
				
				break;
			
			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . esc_attr($id_label) . '" name="sigf_options[' . $name . ']" value="' . esc_attr( $value ) . '" />';
				
				if ( $desc != '' )
					echo '<span class="description">' . esc_html($desc) . '</span>';
				
				break;
				
			case 'image':
				if ( $desc != '' )
					$desc_html = '<span class="description">' . esc_html($desc) . '</span>';
		 		else $desc_html='';
		 		$value = esc_url($value);
		 		if($value)
		 			$image  = '<img class = "exist-preview" id="logo-p" src="'.$value.'" alt="'.esc_attr($alt).'" />';
				else $image = '';
				
		 		echo 
<<<EOT
					<input class="regular-text upload_field $field_class" type="text" id=" $id_label " name="sigf_options[$name]" placeholder=" $std " value="$value" />
					<input class="upload_image_button" type="button" value="$button" />
					$desc_html
			 		<div id="logo-preview" class = "img-preview">
			 		$image
			 		</div>
EOT;
		 		break;
		 		 		
			case 'hidden':
		 		echo '<input type="hidden" id="' . esc_attr($id_label) . '" name="sigf_options[' . $name . ']" value="' . esc_attr( $options[$id] ) . '" />';
			break;
			
			case 'category_filter':
				echo '<div class = '.$field_class.'>'.$header.'<ul id = "'.esc_attr($id_label).'">';
				$desc = $args['desc'];
				unset($args['desc']);
				foreach($choices as $choice=>$label) {
					echo '<li><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$label;
					$args ['type'] = 'array';
					$old_args=$args['id'];
					$args ['id'] = array($args['id'], $choice);
					$this->sigf_display_setting($args);
					echo '</li>';
					$args['id']=$old_args;
				};	
			echo '</ul></div>
					<span class = "text_desc">'.$desc.'</span>';
			break;
		 	
			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" type="text" id="' . esc_attr($id_label) . '" name="sigf_options[' . $name . ']" placeholder="' . esc_attr($std) . '" value="' . esc_attr( $value ) . '" />';
		 		if ( $desc != '' )
		 			echo '<span class="description">' . esc_html($desc) . '</span>';
		 		
		 		break;
		 	
		}
		
	}
	
	/**
	 * Settings and defaults
	 * 
	 * @since 1.0
	 */
	public function sigf_get_settings() {
		
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
	public function sigf_initialize_settings() {
		
		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' ){
				$default_settings[$id] = $setting['std'];
			}
			if($setting['type'] == 'array') {
				foreach($setting['children'] as $sub_id => $child) {
					$default_settings[$id][$sub_id] = $child['std'];
				}
			}
			elseif($setting['type'] == 'filter') {
				foreach($setting['choices'] as $sub2_id => $choice){
					foreach($setting['children'] as $sub_id => $child) {
						$default_settings[$id][$sub2_id][$sub_id] = $child['std'];
					}
				}
			}
		}
		update_option( 'sigf_options', $default_settings );
		
	}
	
	/**
	* Register settings
	*
	* @since 1.0
	*/
	public function sigf_register_admin_settings() {
		
		register_setting( 'sigf_options', 'sigf_options', array ( &$this, 'sigf_validate_settings' ) );
		
		$this->sigf_register_sections();
		
		$this->sigf_get_settings();
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$setting['value']=$this->options[$id];
			$this->sigf_create_setting( $setting );
			}
	}
	
	public function sigf_register_sections(){
		foreach ( $this->sections as $slug => $title ) {
			add_settings_section( $slug, $title, array( &$this, 'sigf_display_section' ), 'sigf-options' );
		}
	}
	
	/**
	* jQuery Tabs
	*
	* @since 1.0
	*/
	public function sigf_admin_scripts() {
	 	wp_enqueue_script('jquery-ui-sortable');
  		wp_enqueue_script('jquery-ui-tabs');
 		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
	}
	
	/**
	* Styling for the theme options page
	*
	* @since 1.0
	*/
	public function sigf_admin_styles() {
		
		wp_enqueue_style("theme_style", get_stylesheet_directory_uri()."/functions/css/admin.css", false, "1.0", "all");
		wp_enqueue_style('thickbox');
	
	}	
	
	public function sigf_theme_init() {
		add_action('wp_print_scripts', array(&$this, 'sigf_do_theme_script')); // For use on the Front end (ie. Theme)
		add_action('wp_print_styles', array(&$this, 'sigf_do_theme_style')); // For use on the Front end (ie. Theme)		
	}
	
	public function sigf_do_theme_script() {
	}

	public function sigf_do_theme_style() {
	}
	
	public function sigf_register_thumbs($height) {
		if ( function_exists( 'add_theme_support' ) ) { 
			add_theme_support( 'post-thumbnails' );
			if (!isset($height)) $height = 170;
			set_post_thumbnail_size( 134, $height, true ); // Normal post thumbnails
			add_image_size( 'featured_full', 400, 9999 ); // Permalink thumbnail size
		}
	}	
	
	public function sigf_do_settings_sections($page) {
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

	public function sigf_do_settings_fields($page, $section) {
		global $wp_settings_fields;
	
		if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
			return;
	
		foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
			echo 
<<<EOT
			<div class = 'option'>
				<label for='{$field['args']['label_for']}' class = 'main_label'> {$field['title']} </label>
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
	
	public function sigf_generate_featured($id){
		global $post;
		$tag = $this->options[$id];
		if($tag):
			if($tag == -1) $tag = '';
			$rand_posts = get_posts( array( 'numberposts' => 1, 'orderby' => 'rand', 'tag_id' => $tag ) );
			if(!empty($rand_posts)):
				foreach( $rand_posts as $post ) : ?>
				<h2 id = 'headline'>Featured Post | <a href="<?php esc_url(the_permalink());?>"><?php esc_html(the_title()); ?></a></h2>
				<?php endforeach;
			endif;
		endif;
	}

	public function sigf_generate_headline_list($order_id, $max_id, $empty_id, $show_images, $post_labels){
		$options = $this->options;
		global $post;
		
		$max_cat = $options[$max_id];		
		$show_empty_cat=$options[$empty_id];
				
		$cat_list = $options[$order_id];
			
		if(is_array($post_labels)) {
			foreach($post_labels as $label => $test) {
				if($test) $display_opt_key = $label;
			}
			if(empty($display_opt_key)) $display_opt_key = $post_labels['default'];
		}
		else $display_opt_key = $post_labels;
			
		//reorder categories with id as a key
		$categories = get_categories('hide_empty=0');

		foreach($categories as $category) {
			$cat_all[$category->term_id] =  $category;
		}
			
		$list = '';
		$empty_cats = 0;
		$i = 0;
		foreach($cat_list as $id => $cat_settings) {
			$feat_posts = array();
			if($cat_all[$id]->category_count>0 || $show_empty_cat){
				$list .= '<li>
							<ul class="latest">
								<li><h2 class="latest"><a href="'.esc_url(get_category_link( $id )).'">'.esc_html(get_cat_name($id)).'</a></h2></li>';											
				$args = array( 'numberposts' => $cat_settings[$display_opt_key], 'category' =>$id);
				$i++;
				$feat_posts = get_posts( $args );
				foreach($feat_posts as $post) {
					setup_postdata( $post );
					$list .= '<li>
								<ul class = "latestPost">
									<li class="list-time">'.esc_html(get_the_time('d')).' '.esc_html(get_the_time('M')).'</li>
									<li class="list-title"><a href="'.esc_url(get_permalink()). 'rel="bookmark">'.esc_html(get_the_title()).'</a></li>';
					if(has_post_thumbnail()&&$show_images) {
						$list .= '	<li><a href="'.esc_url(get_permalink()).'" title="'.esc_attr(the_title_attribute(array('echo'=>0))).'" >'.get_the_post_thumbnail().'</a></li>';
					}
					$list .= '		<li class="latest-excerpt">'.esc_html(get_the_excerpt()).'</li>
								</ul>
							 </li>';						
					wp_reset_postdata();
				}
				$list .= '</ul></li>';
				if($i<$max_cat) continue;
				else break;
			}
		}
			
		return $list;
	}
		
	public function sigf_whereami(){
		return array(	'home'=>is_home(),
						'posts'=>is_single(), 
						'pages'=>is_page(),
						'archives'=>is_archive(),
						'search'=>is_search(),
					);
	}
		
	public function sigf_shouldihere($input_arr){
		if($input_arr['never']) return 0;
		elseif($input_arr['all']) return 1;
		elseif(!is_array($input_arr)) return 1;
		else {
			$compare =  array_intersect_assoc($input_arr, $this->sigf_whereami());
			return  !empty($compare);
		}
	}
	
	public function sigf_category_count($cat_list, $max_cat, $show_empty=0) {
		$all_cats = get_categories(array('hide_empty'=>0));		
		$all_cat_by_id = array();
		foreach($all_cats as $cat) $all_cat_by_id[$cat->term_id]=$cat;	
		$i=0;
		foreach($cat_list as $id =>$cat) {
			if($all_cat_by_id[$id]->category_count>0 || $show_empty) $i++;
			if($i<$max_cat) continue;
			else break;
		}	
		return $i;
	}	
	

	public function sigf_validate_settings( $inputs ) {
		$result = $this->sigf_get_inputs($inputs, '');
		return $inputs;	
	}
	
	public function sigf_get_inputs(&$input, $key) {
		if(!is_array($input)) {	
			$result = $this->sigf_validate_element($input, $key);
			$input = $result;			
			return $input;
		}
		foreach($input as $id => &$child) {
				$this->sigf_get_inputs($child, $id);
		}	
	}
	
	public function sigf_validate_element($input, $key) {
		if(array_key_exists($key,$this->validation_ref)) {
			$setting = $this->validation_ref[$key]['setting'];
		}
		else {
			$setting = $this->sigf_find_setting($key, $this->settings);
			$this->validation_ref[$key]['setting']=$setting;	
		}

		$type = $setting['validation']? $setting['validation']: $setting['type'];
		$this->validation_ref[$key]['type']=$type;
		$result = $this->sigf_validation_check($input, $type, $setting);			
		if($result === false) {
			add_settings_error( 'sigf_options', $key.'_err', 'The value for '.$setting['title'].' is not valid', 'error' );
		}
		
		return $result;
	}
	
	
	public function sigf_find_setting($id, $settings) {
		$return = array();
		foreach($settings as $key => $setting) {
			if(strcmp($key,$id)==0) {
				return $setting;
			}
			elseif($setting['type']=='array'||$setting['type']=='category_filter') {
				$return = array_merge($return, $this->sigf_find_setting($id, $setting['children']));
			}		
		}
		return $return;
	}
	
	public function sigf_validation_check($value, $type, $setting) {
		if(is_null($value))	return '';

		if(get_magic_quotes_gpc())	$value = stripslashes($value);
				
		switch($type) {
			case 'integer':
				return (preg_match("/^\-?\d+$/", $value))? $value: false;
			case 'js':
				$value = htmlentities($value);
				return $value;	
			case 'html':
				$value = esc_html($value);
				return $value;	
			case 'text':
				$value = sanitize_text_field($value);
				return (ctype_alnum($value))? $value: false;
			case 'select':
				return array_key_exists($value, $setting['choices'])?$value:false;
			case 'checkbox':
				return $value == 1 || $value == 0 ?$value: false ;
			case 'imgurl':
				return (preg_match("/^https?:\/\/[\w\d\.]+\.[\w\d\/\-]+\/[\w\d\-]+\.(jp?g|gif|png)$/i", $value))? $value: false;
			default:
				return false;
			break;
		}
	}

	
}

function sigf_option( $option ) {
	$options = get_option( 'sigf_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}

?>