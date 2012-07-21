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
			'value' => null
		);
			
		extract( wp_parse_args( $args, $defaults ) );
		
		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
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
	}
	
	/**
	 * Description for About section
	 *
	 * @since 1.0
	 */
	public function display_about_section() {
		
		// This displays on the "About" tab. Echo regular HTML here, like so:
		// echo '<p>Copyright 2011 me@example.com</p>';
		
	}


	/**
	 * HTML output for text field
	 *
	 * @since 1.0
	 */
	public function display_setting( $args = array() ) {
		
		extract( $args );
		
		$options = $this->options;
		
		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;
		
		switch ( $type ) {
			
			case 'heading':
				echo '</td></tr><tr valign="top"><td colspan="2"><h4>' . $desc . '</h4>';
				break;
			
			case 'checkbox':
				
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="sigf_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';		
				break;
			
			case 'select':
				echo '<select class="select' . $field_class . '" name="sigf_options[' . $id . ']">';
				
				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';
				
				echo '</select>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
			
			case 'radio':
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="sigf_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
			
			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="sigf_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
			
			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="sigf_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				
				break;
				
				case 'image':
				$value = esc_attr( $options[$id] ) ;
				if ( $desc != '' )
					$desc_html = '<br /><span class="description">' . $desc . '</span>';
		 		else $desc_html='';
		 		if($options[$id])
		 			$image  = '<img class = "exist-preview" id="logo-p" src="'.$options[$id].'" alt="'.$alt.'" />';
				else $image = '';
				
		 		echo 
<<<EOT
					<input class="regular-text upload_field $field_class " type="text" id=" $id " name="sigf_options[' $id ']" placeholder=" $std " value=" $value " />
					<input class="upload_image_button" type="button" value="Upload Image" />
					$desc_html
			 		<div id="logo-preview" class = "img-preview">
			 		$image
			 		</div>
EOT;
		 		break;
		 		 		
			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="sigf_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
		 		
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
			'title'   => __( 'Logo' ),
			'desc'    => __( 'Upload, or enter URL of image for site' ),
			'std'     => '',
			'type'    => 'image',
			'section' => 'general',
			'alt' => __('Preview of site logo')
		);
		
		$this->settings['favicon'] = array(
			'title'   => __( 'Faviscon' ),
			'desc'    => __( 'Upload, or enter URL of favicon for site' ),
			'std'     => '',
			'type'    => 'image',
			'section' => 'general',
			'alt' => __('Preview of site favicon')
		);
		
		$this->settings['example_textarea'] = array(
			'title'   => __( 'Example Textarea Input' ),
			'desc'    => __( 'This is a description for the textarea input.' ),
			'std'     => 'Default value',
			'type'    => 'textarea',
			'section' => 'general'
		);
		
		$this->settings['example_checkbox'] = array(
			'section' => 'general',
			'title'   => __( 'Example Checkbox' ),
			'desc'    => __( 'This is a description for the checkbox.' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		
		$this->settings['example_heading'] = array(
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
		
		/* Appearance
		===========================================*/
		
		$this->settings['header_logo'] = array(
			'section' => 'appearance',
			'title'   => __( 'Header Logo' ),
			'desc'    => __( 'Enter the URL to your logo for the theme header.' ),
			'type'    => 'text',
			'std'     => ''
		);
		
		$this->settings['favicon'] = array(
			'section' => 'appearance',
			'title'   => __( 'Favicon' ),
			'desc'    => __( 'Enter the URL to your custom favicon. It should be 16x16 pixels in size.' ),
			'type'    => 'text',
			'std'     => ''
		);
		
		$this->settings['custom_css'] = array(
			'title'   => __( 'Custom Styles' ),
			'desc'    => __( 'Enter any custom CSS here to apply it to your theme.' ),
			'std'     => '',
			'type'    => 'textarea',
			'section' => 'appearance',
			'class'   => 'code'
		);
				
		/* Reset
		===========================================*/
		
		$this->settings['reset_theme'] = array(
			'section' => 'reset',
			'title'   => __( 'Reset theme' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset theme options to their defaults.' )
		);
		
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
			if ( $slug == 'about' )
				add_settings_section( $slug, $title, array( &$this, 'display_about_section' ), 'sigf-options' );
			else
				add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'sigf-options' );
		}
		
		$this->get_settings();
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
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
		'carousel' => __('Carousel',$shortName),
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