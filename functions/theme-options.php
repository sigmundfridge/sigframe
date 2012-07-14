<?php
/* TO DO: 201/202

*/
	$defaults = array('empty_cat'=>0,'max_cat'=>8, 'home_img'=>1, 'other_img'=>0, 'feat_tag'=>-1, 'max_feat_h'=>150,'cat'=>array(1,1,0,0,0,0));
	$options = get_option( 'nMod_theme_options', $defaults );
/**
 * Load up the menu page
 */
	

if ( function_exists( 'add_theme_support' ) ) { 
	add_theme_support( 'post-thumbnails' );
	if (isset($options['max_feat_h'])) $height = $options['max_feat_h'];
	else $height = 170;
	set_post_thumbnail_size( 134, $height, true ); // Normal post thumbnails
	add_image_size( 'featured_full', 400, 9999 ); // Permalink thumbnail size
}

add_action( 'admin_menu', 'theme_options_add_page' );
add_action( 'admin_init', 'theme_options_init' );

$themeName = 'Nick Theme';
$shortName = 'nMod';


function theme_options_add_page() {
	$theme_hook = add_theme_page( __( 'Theme Options', $shortName ), __( 'Theme Options', $shortName ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
	add_action( 'admin_print_scripts-'.$theme_hook, 'admin_scripts' );
	add_action( 'admin_print_styles-'.$theme_hook, 'admin_styles' );

}
/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'nMod_options', 'nMod_theme_options', '' );
//	register_setting( 'nMod_options', 'nMod_theme_options', 'theme_options_validate' );
}

function admin_scripts(){
 	wp_enqueue_script('jquery-ui-sortable');
  	wp_enqueue_script('jquery-ui-tabs');
 	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('admin-scripts', get_stylesheet_directory_uri()."/functions/js/theme-admin-js.js");
}

function admin_styles(){
 	wp_enqueue_style("theme_style", get_stylesheet_directory_uri()."/functions/css/admin.css", false, "1.0", "all");
	//wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/base/jquery-ui.css'); 
	wp_enqueue_style('thickbox');
}


/**
 * Create the options page
 */
function theme_options_do_page() {
	global $shortName, $themeName, $options;
	
	if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false;
	?>
	<div class='wrap'>
		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class='saved'><p><strong><?php _e( 'Options saved', $shortName ); ?></strong></p></div>
		<?php endif; ?>
			

		<div class = 'options'>
			<form method='post' action='options.php'>
			<?php settings_fields( 'nMod_options' );?>
					
			<?php screen_icon(); echo '<h2>' . get_current_theme() . __( ' Theme Options', $shortName ) . '</h2>'; ?>
			<div id = "tab_wrap">
				<ul>
					<li><a href="#tab1">General</a></li>
					<li><a href="#tab2">Headline Categories - Layout</a></li>
					<li><a href="#tab3">Headline Categories - Images</a></li>
					<li><a href="#tab4">Headline Categories - Options</a></li>
				</ul>
			
				<div class = "section" id="tab1">
				<h3>General</h3>
					<?php
				/**
				 * Custom logo
				 */
					?>	
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Custom Logo', $shortName ); ?>
						</span>
						<span class = 'opt_input'>					
							<input id="upload_image" class='regular-text upload_field' type="text" size="36" name='nMod_theme_options[logo]' value="<?php _e($options['logo'])?>" />						
							<input class="upload_image_button" type="button" value="Upload Image" />
							<label class='description upload_desc' for='nMod_theme_options[logo]'><?php _e( 'Enter a URL or upload an image', $shortName ); ?></label>
							<div id="logo-preview" class = "img-preview">
						<?php if($options['logo']) { ?>
								<img class = 'exist-preview' id="logo-p" src="<?php echo $options['logo']; ?>" alt="Current Logo" />
						<?php } ?>
							</div>
						</span>
					</div>
						<?php
					/**
					 * Custom favicon
					 */
						?>	
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Custom Favicon', $shortName ); ?>
						</span>
						<span class = 'opt_input'>					
							<input id="upload_favicon" class='regular-text upload_field' type="text" size="36" name='nMod_theme_options[favicon]' value="<?php _e($options['favicon'])?>" />						
							<input class="upload_image_button" type="button" value="Upload Favicon" />
							<label class='description upload_desc' for='nMod_theme_options[favicon]'><?php _e( 'Enter a URL or upload an image. Images should be 16x16 and ico, png or gif format', $shortName ); ?></label>
							<div id="fav-preview" class = "img-preview">
					<?php if($options['favicon']) { ?>
								<img class = 'exist-preview' id="fav-p" src="<?php echo $options['favicon']; ?>" alt="Current Favicon" />
					<?php } ?>
							</div>
						</span>
					</div>

						<?php 	/**
					 * Tag lists
					 */
						?>		
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Featured Tag:', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>
							<select name='nMod_theme_options[feat_tag]'>
								<?php
									$p = '';
									$p .= '\n\t<option value = "0"';
									if($options['feat_tag'] == 0) $p.= 'selected="selected"';
									$p.= '>*No Headline*</option>';
									$p .= '\n\t<option value = "-1"';
									if($options['feat_tag'] == "-1") $p.= 'selected="selected"';
									$p.='>*All Posts*</option>';								
									$tags = get_tags();
									foreach ($tags as $tag) {
										$p .= '\n\t<option';
										if ( $tag->term_id == $options['feat_tag'] ) $p.= ' selected="selected"';
										$p.= " value='" . $tag->term_id . "'>".$tag->name."</option>";
									}
									echo $p
								?>
							</select>
							<label class='description' for='nMod_theme_options[feat_tag]'><?php _e( 'Posts with this tag will appear as "Featured Posts" in the header', $shortName ); ?></label>
						</span>
					</div>	
			
					<div class = 'option'>
						<span class = 'opt_label'>				
							<?php _e( 'Tracking Code', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>						
							<textarea id='nMod_theme_options[head_tracker]' class='regular-text' name='nMod_theme_options[head_tracker]' columns = "7" rows = "7"><?php esc_attr_e( $options['head_tracker'] ); ?> </textarea>
							<label class='description' for='nMod_theme_options[head_tracker]'><?php _e( 'Paste your analytics code here. It will be inserted into the head tag of your site', $shortName ); ?></label>
						</span>
					</div>
				
					
					<div class="clear"></div>		
				
				</div>	
				
				<div class = "section" id="tab2">
				<h3>Headline Categories - Layout</h3>
			
						<?php 
					/**
					 * Choose category order and number of posts to display
					 */
						?>	
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Choose category order and post count for headlines', $shortName ); ?>
						</span>
						<span class = 'opt_input' id='cat_input'>
							<div id = 'sort_title'>
								<div class = 'cat left'>Category (Total post count)</div><div class = 'right post_no'><span class = 'head'>Number of posts to display:</span><span class = 'post_home post_head'>Home</span><span class = 'post_other post_head'>Other</span></div>
							</div>
							<div class = 'clear'></div>
							<div class = 'cat_menu'>
								<ul id = 'sort_cat'>
									<?php
									$cat_order = $options['cat_order']['id'];
									$home_nos = array_pad(array(), count($cat_order), 3);
									$post_nos = array_pad(array(), count($cat_order), 1);
									$home_nos = $options['cat_order']['home_no'];
									$post_nos = $options['cat_order']['post_no'];
									if(empty($home_nos))   $home_nos = array();								
									if(empty($post_nos))   $post_nos = array();								
									
									$all_ids = get_all_category_ids();
									if(empty($cat_order))   $cat_order = $all_ids;								
									$diff = array_diff($all_ids, $cat_order);
									$cat_order = array_merge($cat_order, $diff);
									
									$categories = get_categories('hide_empty=0');
						
									foreach($cat_order as $id) { 
										$home_no = each($home_nos);
										$post_no = each($post_nos);
										foreach($categories as $category) {
											if($id == $category->cat_ID) {
												echo "\n\t<li>";
												echo "<span class='ui-icon ui-icon-arrowthick-2-n-s'></span><input type='hidden' name = 'nMod_theme_options[cat_order][id][]' value = '".esc_attr( $id )."' />"
												. $category->cat_name." (".$category->category_count.")";
												echo "<input id='nMod_theme_options[cat_order][post_no][]' class='post_no' type='text' name='nMod_theme_options[cat_order][post_no][]' value='".esc_attr( !empty($post_no[1]) ? $post_no[1] : 1 )."' />&nbsp;";
												echo "<input id='nMod_theme_options[cat_order][home_no][]' class='home_no' type='text' name='nMod_theme_options[cat_order][home_no][]' value='".esc_attr( !empty($home_no[1]) ? $home_no[1] : 3 )."' />&nbsp;";
												if($category->category_count==0) echo "<input type='hidden' name = 'nMod_theme_options[cat_order][empty][]' value = '1' />";
												else echo "<input type='hidden' name = 'nMod_theme_options[cat_order][empty][]' value = '0' />";
												echo "</li>";
												break;
											}
										}
									}	
									?>
								</ul>
							</div>
						</span>
						<span class = "text_desc">
							<p>Drag the category titles to set the order they will be displayed in the headlines.</p>
							<p>The number in the brackets is the total number of posts under each category</p>
							<p>Use the input boxes next to each category to determine how many posts will be displayed under each headline category on the front page, and on all other pages</p>
							<p>The default is 3 posts on the home page, and 1 on all other pages</p>
						</span>

					</div>
				
						<?php
					/**
					 * Number of categories
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Maximum headline categories', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>
							<select name='nMod_theme_options[max_cat]'>
								<?php
									$p = '';
									for ( $i=1; $i<=count($cat_order); $i++ ) {
										$p .= '\n\t<option';
										if ( $i == $options['max_cat'] ) $p.= ' selected="selected"';
										$p.= " value='" . $i . "'>".$i."</option>";
									}
									echo $p
								?>
							</select>
							<label class='description' for='nMod_theme_options[max_cat]'><?php _e( 'Select the max number of headline categories to display (empty ones may be ignored) ', $shortName ); ?></label>
						</span>
					</div>

						<?php
					/**
					 * Show empty categories
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Show empty categories', $shortName ); ?>
						</span>
						<span class = 'opt_input'>
							<input id='nMod_theme_options[empty_cat]' name='nMod_theme_options[empty_cat]' type='checkbox' value='1' <?php checked( '1', $options['empty_cat'] ); ?> />
							<label class='description' for='nMod_theme_options[empty_cat]'><?php _e( 'Choose to show or ignore empty headline categories', $shortName ); ?></label>
						</span>
					</div>	
					
						<?php
					/**
					 * Show categories on ...
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Pages to display headlines', $shortName ); ?></th>
						</span>
						<?php if(!isset($options['cat'])) $options['cat']=array('home'=>1, 'post'=>1);?>
						<span class = 'opt_input'>	
							<input id='nMod_theme_options[cat][never]' name='nMod_theme_options[cat][never]' type='checkbox' value='1' <?php checked( '1', $options['cat']['never'] ); ?> />
							<label class='checkbox_desc description' for='nMod_theme_options[cat][never]'><?php _e( 'Never display (overrides all other boxes)', $shortName ); ?></label>
							<input id='nMod_theme_options[cat][home]' name='nMod_theme_options[cat][home]' type='checkbox' value='1' <?php checked( '1', $options['cat']['home'] ); ?> />
							<label class='checkbox_desc description' for='nMod_theme_options[cat][home]'><?php _e( 'homepage', $shortName ); ?></label>
							<input id='nMod_theme_options[cat][post]' name='nMod_theme_options[cat][post]' type='checkbox' value='1' <?php checked( '1', $options['cat']['post'] ); ?> />
							<label class='checkbox_desc description' for='nMod_theme_options[cat][post]'><?php _e( 'posts', $shortName ); ?></label>
							<input id='nMod_theme_options[cat][page]' name='nMod_theme_options[cat][page]' type='checkbox' value='1' <?php checked( '1', $options['cat']['page'] ); ?> />
							<label class='checkbox_desc description' for='nMod_theme_options[cat][page]'><?php _e( 'pages', $shortName ); ?></label>
							<input id='nMod_theme_options[cat][archive]' name='nMod_theme_options[cat][archive]' type='checkbox' value='1' <?php checked( '1', $options['cat']['archive'] ); ?> />
							<label class='checkbox_desc description' for='nMod_theme_options[cat][archive]'><?php _e( 'archives', $shortName ); ?></label>
							<input id='nMod_theme_options[cat][search]' name='nMod_theme_options[cat][search]' type='checkbox' value='1' <?php checked( '1', $options['cat']['search'] ); ?> />
							<label class='checkbox_desc description' for='nMod_theme_options[cat][search]'><?php _e( 'search', $shortName ); ?></label>
						</span>
					</div>	
					<div class="clear"></div>
				</div>
				<div class = "section" id="tab3">
				<h3>Headline Categories - Images</h3>
				
						<?php
					/**
					 * Show featured image on homepage
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Pages to display featured images under headline categories', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>						
							<input id='nMod_theme_options[home_img]' name='nMod_theme_options[home_img]' type='checkbox' value='1' <?php checked( '1', $options['home_img'] ); ?> />
							<label class='checkbox_desc description' for='nMod_theme_options[home_img]'><?php _e( 'homepage', $shortName ); ?></label>
							<input id='nMod_theme_options[other_img]' name='nMod_theme_options[other_img]' type='checkbox' value='1' <?php checked( '1', $options['other_img'] ); ?> />
							<label class='checkbox_desc description' for='nMod_theme_options[other_img]'><?php _e( 'other pages', $shortName ); ?></label>
						</span>
					</div>	
		
						<?php
					/**
					 * Maximum featured image thumbnail height
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>				
							<?php _e( 'Maximum height of featured images', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>						
							<input id='nMod_theme_options[max_feat_h]' class='regular-text' type='text' name='nMod_theme_options[max_feat_h]' value="<?php esc_attr_e( $options['max_feat_h'] ); ?>" />
							<label class='description' for='nMod_theme_options[max_feat_h]'><?php _e( 'Enter an integer maximum height for all featured images', $shortName ); ?></label>
						</span>
					</div>
									
					<div class="clear"></div>
				</div>	
				
				<div class = "section" id="tab4">
				<h3>Headline Categories - Options</h3>
				<p>
					The headline category display uses jCarousel. For more information see the<a href="http://sorgalla.com/projects/jcarousel/">jCarousel homepage</a>
				</p>				
						<?php
					/**
					 * Wrap style
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Wrap style', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>
							<select name='nMod_theme_options[carousel][wrap]'>
								<?php 
									$select = array('none','circular', 'first', 'last', 'both');
									foreach($select as $option) {
								?>
									<option value = "<?php _e($option) ?>" <?php if($option == $options['carousel']['wrap']) _e('selected="selected"'); ?>><?php echo _e($option); ?></option>
								<?php
									}
								?>
							</select>
							<label class='description' for='nMod_theme_options[carousel][wrap]'><?php _e( 'Choose a wrap method i.e. how the carousel behaves when it reaches the last/first category', $shortName ); ?></label>
						</span>
					</div>

						<?php
					/**
					 * Easing style
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Animation', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>
							<select name='nMod_theme_options[carousel][easing]'>
								<?php 
									$select = array('linear','jswing','easeInQuad','easeOutQuad','easeInOutQuad','easeInCubic','easeOutCubic','easeInOutCubic','easeInQuart','easeOutQuart','easeInOutQuart','easeInSine','easeOutSine','easeInOutSine','easeInExpo','easeOutExpo','easeInOutExpo','easeInQuint','easeOutQuint','easeInOutQuint','easeInCirc','easeOutCirc','easeInOutCirc','easeInElastic','easeOutElastic','easeInOutElastic','easeInBack','easeOutBack','easeInOutBack','easeInBounce','easeOutBounce','easeInOutBounce');
									foreach($select as $option) {
								?>
									<option value = "<?php _e($option) ?>" <?php if($option == $options['carousel']['easing']) _e('selected="selected"'); ?>><?php echo _e($option); ?></option>
								<?php
									}
								?>
							</select>
							<label class='description' for='nMod_theme_options[carousel][easing]'><?php _e( 'Choose an animation style. For more information see the<a href="http://jqueryui.com/demos/effect/easing.html">jQuery easing demos</a>', $shortName ); ?></label>
						</span>
					</div>
	
					<?php
					/**
					 * Event trigger
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Trigger for scrolling', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>
							<select name='nMod_theme_options[carousel][trigger]'>
								<?php 
									$select = array('click','mouseover');
									foreach($select as $option) {
								?>
									<option value = "<?php _e($option) ?>" <?php if($option == $options['carousel']['trigger']) _e('selected="selected"'); ?>><?php echo _e($option); ?></option>
								<?php
									}
								?>
							</select>
							<label class='description' for='nMod_theme_options[carousel][trigger]'><?php _e( 'Scrolling can be triggered by left click or moving the cursor over the scroll bar', $shortName ); ?></label>
						</span>
					</div>
					
						<?php
					/**
					 * Step size
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Step size', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>
							<select name='nMod_theme_options[carousel][step]'>
								<?php
									$p = '';
									for ( $i=1; $i<=count($cat_order); $i++ ) {
										$p .= '\n\t<option';
										if ( $i == $options['carousel']['step'] ) $p.= ' selected="selected"';
										$p.= " value='" . $i . "'>".$i."</option>";
									}
									echo $p
								?>		</select>
							<label class='description' for='nMod_theme_options[carousel][step]'><?php _e( 'How many categories to scroll on each cycle', $shortName ); ?></label>
						</span>
					</div>
					
						<?php
					/**
					 * Speed
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Animation speed (ms)', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>						
							<input id='nMod_theme_options[carousel][speed]' class='regular-text' type='text' name='nMod_theme_options[carousel][speed]' value="<?php esc_attr_e( $options['carousel']['speed'] ); ?>" />
							<label class='description' for='nMod_theme_options[carousel][speed]'><?php _e( 'Enter an integer value for the animation speed in ms. Type "0" for off', $shortName ); ?></label>
						</span>
					</div>
					
						<?php
					/**
					 * Auto scrolling
					 */
						?>
					<div class = 'option'>
						<span class = 'opt_label'>
							<?php _e( 'Auto scroll delay (seconds)', $shortName ); ?></th>
						</span>
						<span class = 'opt_input'>						
							<input id='nMod_theme_options[carousel][autoscroll]' class='regular-text' type='text' name='nMod_theme_options[carousel][autoscroll]' value="<?php esc_attr_e( $options['carousel']['autoscroll'] ); ?>" />
							<label class='description' for='nMod_theme_options[carousel][autoscroll]'><?php _e( 'Enter an integer value for the delay before the headlines auto-scroll (in seconds). Type "0" for no auto scrolling', $shortName ); ?></label>
						</span>
					</div>
					
					<div class="clear"></div>
	
				</div>
			</div>
			<p class='submit'>
				<input type='submit' class='button-primary' value="<?php _e( 'Save Options', $shortName ); ?>" />
			</p>

			</form>
		</div>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function theme_options_validate( $input ) {
	global $select_options, $radio_options;
	
	function checkbox ($value) {
		// Our checkbox value is either 0 or 1
		if ( !isset( $value ) )
			$value = null;
		return $value == 1 ? 1 : 0 ;	
	}	
	
	function checkInt ($value) {
		if(!preg_match("/^\-?\d+$/", $value))
			$value = null;
		return $value;
	}

	$input['empty_cat'] = checkbox($input['empty_cat']);
	$input['home_img'] = checkbox($input['home_img']);	
	$input['other_img'] = checkbox($input['other_img']);	
	
	// Max category number is an integer
	$input['max_cat'] = checkInt($input['max_cat']);
	$input['feat_tag'] = checkInt($input['feat_tag']);
	
	// Similar for height in pixels
	$input['max_feat_h'] = checkInt($input['max_feat_h']);
	
	// Say our text option must be safe text with no HTML tags
	$input['sometext'] = wp_filter_nohtml_kses( $input['sometext'] );

	// Our select option must actually be in our array of select options
	if ( !array_key_exists( $input['selectinput'], $select_options ) )
		$input['selectinput'] = null;


	// Our radio option must actually be in our array of radio options
	if ( !isset( $input['radioinput'] ) )
		$input['radioinput'] = null;
	if ( !array_key_exists( $input['radioinput'], $radio_options ) )
		$input['radioinput'] = null;

	// Say our textarea option must be safe text with the allowed tags for posts
	$input['sometextarea'] = wp_filter_post_kses( $input['sometextarea'] );

	return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/