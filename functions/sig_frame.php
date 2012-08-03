<?php

class SigFramework {

	protected $themeName;
	protected $checkboxes;
	protected $shortName;
	protected $sections;
	protected $settings;
	protected $options;
	protected $validation;

	public function __construct($sections) {
		$this->checkboxes = array();
		$this->settings = array();
		$this->sections = $sections;
		$this->get_settings();
		$this->themeName = get_current_theme();
	
		add_action( 'admin_menu', array( &$this, 'add_admin_pages' ) );
		add_action( 'admin_init', array( &$this, 'register_admin_settings' ) );
		add_action( 'after_setup_theme', array(&$this, 'theme_init'));
		
		if ( ! get_option( 'sigf_options' ) )
			$this->initialize_settings();
		else $this->options = get_option( 'sigf_options' );
		
	}

	public function add_admin_pages() {
		$admin_page = add_theme_page( __( 'Theme Options',$shortName), __( 'Theme Options',$shortName ), 'manage_options', 'sigf-options', array( &$this, 'display_page' ) );
		
		add_action( 'admin_print_scripts-' . $admin_page, array( &$this, 'admin_scripts' ) );
		add_action( 'admin_print_styles-' . $admin_page, array( &$this, 'admin_styles' ) );
		
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
	//	print_r($this->options);
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
			$value = 0;		
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;

		switch ( $type ) {
			
			case 'array':
				foreach($children as $key=>$child){
					$quoted_key = $key;
					$old_list = $id_list;
					array_push($id_list,$quoted_key);
					$child['id']= $id_list;
					$child['value'] = $value;
					$this->display_setting($child);
					$id_list=$old_list;
				}
				if ( $desc != '' )
					echo '<span class="description array">' . $desc . '</span>';
				
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
					echo '<span class="description">' . $desc . '</span>';
				
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
					echo '<span class="description">' . $desc . '</span>';
				
				break;
			
			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="sigf_options[' . $name . ']" placeholder="' . $std . '" rows="5" cols="30">' . html_entity_decode( $value ) . '</textarea>';
				
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span>';
				
				break;
			
			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="sigf_options[' . $name . ']" value="' . esc_attr( $value ) . '" />';
				
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span>';
				
				break;
				
				case 'image':
				if ( $desc != '' )
					$desc_html = '<span class="description">' . $desc . '</span>';
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
				echo '<div class = '.$field_class.'>'.$header.'<ul id = "'.$id.'">';
				$desc = $args['desc'];
				unset($args['desc']);
				foreach($choices as $choice=>$label) {
					echo '<li><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$label;
					$args ['type'] = 'array';
					$old_args=$args['id'];
					$args ['id'] = array($args['id'], $choice);
					$this->display_setting($args);
					echo '</li>';
					$args['id']=$old_args;
				};	
			echo '</ul></div>
					<span class = "text_desc">'.$desc.'</span>';
			break;
		 	
			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="sigf_options[' . $name . ']" placeholder="' . $std . '" value="' . esc_attr( $value ) . '" />';
		 		if ( $desc != '' )
		 			echo '<span class="description">' . $desc . '</span>';
		 		
		 		break;
		 	
		}
		
	}
	
	/**
	 * Settings and defaults
	 * 
	 * @since 1.0
	 */
	public function get_settings() {
		
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
	public function register_admin_settings() {
		
		register_setting( 'sigf_options', 'sigf_options', array ( &$this, 'validate_settings' ) );
		
		$this->register_sections();
		
		$this->get_settings();
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$setting['value']=$this->options[$id];
			$this->create_setting( $setting );
			}
	}
	
	public function register_sections(){
		foreach ( $this->sections as $slug => $title ) {
			add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'sigf-options' );
		}
	}
	
	/**
	* jQuery Tabs
	*
	* @since 1.0
	*/
	public function admin_scripts() {
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
	public function admin_styles() {
		
		wp_enqueue_style("theme_style", get_stylesheet_directory_uri()."/functions/css/admin.css", false, "1.0", "all");
		wp_enqueue_style('thickbox');
	
	}	
	
	public function theme_init() {
		add_action('wp_print_scripts', array(&$this, 'do_theme_script')); // For use on the Front end (ie. Theme)
		add_action('wp_print_styles', array(&$this, 'do_theme_style')); // For use on the Front end (ie. Theme)		
	}
	
	public function do_theme_script() {
	}

	public function do_theme_style() {
	}
	
	public function register_thumbs($height) {
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
				<h2 id = 'headline'>Featured Post | <a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
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
								<li><h2 class="latest"><a href="'.esc_url(get_category_link( $id )).'">'.get_cat_name($id).'</a></h2></li>';											
				$args = array( 'numberposts' => $cat_settings[$display_opt_key], 'category' =>$id);
				$i++;
				$feat_posts = get_posts( $args );
				foreach($feat_posts as $post) {
					setup_postdata( $post );
					$list .= '<li>
								<ul class = "latestPost">
									<li class="list-time">'.get_the_time('d').' '.get_the_time('M').'</li>
									<li class="list-title"><a href="'.get_permalink(). 'rel="bookmark">'.get_the_title().'</a></li>';
					if(has_post_thumbnail()&&$show_images) {
						$list .= '	<li><a href="'.get_permalink().'" title="'.the_title_attribute(array('echo'=>0)).'" >'.get_the_post_thumbnail().'</a></li>';
					}
					$list .= '		<li class="latest-excerpt">'.get_the_excerpt().'</li>
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
		elseif($input_array['all']) return 1;
		else {
			$compare =  array_intersect_assoc($input_arr, $this->sigf_whereami());
			return  !empty($compare);
		}
	}
	
	public function category_count($cat_list, $max_cat, $show_empty=0) {
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
	
	/**
	* Validate settings
	*
	* @since 1.0
	public function validate_settings( $inputs ) {
		if ( ! isset( $input['reset_theme'] ) ) {
			$options = get_option( 'sigf_options' );
			
			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}
			
			return $input;
		}
		return false;	
	}*/
	public function validate_settings( $inputs ) {
		foreach($inputs as $key => $input) {
			$test = $this->check_input($key, $input);
		//	if(!$test) echo 'input test '. false;
		}		
		return $inputs;	
	}
	
	public function check_input($key, $input) {
		if(is_array($input)) {
			foreach($input as $key => $child) {
				return $this->check_input($key, $child);
			}
		}
	//	else {
	//		$setting_pointer = find_setting ($key, $this->settings);
	//	}
			return $key;
		// return test_input ($input, $setting_pointer);
	}
	
	public function find_setting($option_id, $settings) {
	/*	if(in_array($option_id,$this->validation) //.......
		else {
			foreach($settings as $id => $setting) {
				if($option_id == $id) 
					{
						$this->validation[]=array($option_id, &$setting);
						return &$setting;
					}
				elseif($setting['type']=='array'||$setting['type']='filter'){
					foreach($settings['children'] as $child_id => $child) {
						find_setting($option_id, $child);
					}
				else return false;
				}
			}
		}
	*/	
	}
	
	public function test_input($input, $setting){
	//	if(isset($setting['validation']) $check = $setting['validation'];
	//	else $check = $setting['type'];
	//	print '<p>checked '.$input
		
/*		switch($setting) {
			case 'integer':
				if(!preg_match("/^\-?\d+$/", $input)) return false;
				else return true;
			case 'html':
//				if(//check) return false;
//				else return true;
			case 'text':
//				if(//check) return false;
//				else return true;
			case 'select':
//				if(not in settings choices values) return false;
//				else return true;
			case 'checkbox':
				return $input == 1 || $input == 0 ? 1 : 0 ;
			break;
		}
*/		
		return true;
		
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