<?php
class validCheck {
	public $options;
	public $settings;
	public $validation_ref = array();
	
	public function __construct() {
		$this->options = array ( 'logo' => 'http://sigfrid.co.uk/frame/wp-content/uploads/2012/07/logo-300x61.png', 
					'favi' => 'http://sigfrid.co.uk/frame/wp-content/uploads/2012/07/favicon1.png',
					'featured' => 5 ,
					'head_tracker' => null,
					'cat_order' => array ( 
						'8' => array ( 'post_no' => 3, 
										'home_no' => 5 ), 
						'6' => array ( 'post_no' => 3, 
										'home_no' => 5 ), 	
						'7' => array ( 'post_no' => 3, 
										'home_no' => 5 ),
						'1' => array ( 'post_no' => 3, 
										'home_no' => 5 ), 
						'9' => array ( 'post_no' => 3, 
										'home_no' => 5 ), 
						'14' => array ( 'post_no' => 3, 
										'home_no' => 5 ), 
						'10' => array ( 'post_no' => 3, 
										'home_no' => 5 ), 
						'11' => array ( 'post_no' => 3,
										'home_no' => 5 ), 
						'12' => array ( 'post_no' => 3,
										'home_no' => 5 ),
						'13' => array ( 'post_no' => 3,
										'home_no' => 5 )
					), 
					'max_cat' => 10, 
					'max_feat_h' => 150, 
					'wrap' => 'none', 
					'easing' => 'easeInQuad', 
					'trigger' =>'click', 
					'step' => 1, 
					'speed' => 100, 
					'autoscroll' => 0 );
			$this->get_settings();
			$this->do_valid();
	}



	public function do_valid() {
	
//		echo '<p>Settings are </p>';
//		print_r($this->settings);
//		echo '<p>Options are </p>';
//		print_r($this->options);
		
		$this->validate_settings($this->options);
	
	}
	
	public function validate_settings( $inputs ) {
			$results = $this->get_inputs($inputs, '');
			print '<p>';
	//		print_r($results);
		foreach($results as $option) {
			print $this->get_validation_type($option['key'], $option['value'],$this->settings);
			//$check = $this->validation_check($option['value'], $this->validation_ref[$option['key']], null);
		
		}
			return $inputs;	
	}
	
		//	else {
	//		$setting_pointer = find_setting ($key, $this->settings);
	//	}
	//		return $key;
		// return test_input ($input, $setting_pointer);

	
	public function get_inputs(&$input_arr, $key) {
		if(!is_array($input_arr)) return array(array('key'=>$key, 'value'=>$input_arr)); //i.e. nothing
		
		$return = array();
		foreach($input_arr as $id => $child) {
				$return = array_merge($return,$this->get_inputs($child, $id));
		}
		return $return;
	
	}
	
	public function get_validation_type($id, $value, $settings) {
	 //	if(array_key_exists($id,$this->validation_ref)) return $this->validation_ref[$key];
		if(array_key_exists($id,$this->validation_ref)) return $this->validation_check($value, $this->validation_ref[$key], $settings);
		foreach($settings as $key => $setting) {
		//	print '<p>Input '.$id.' against '.$key.' result  '.strcmp((string)$key,(string)$id);
			if(strcmp($key,$id)==0) {
				$type = $setting['validation']? $setting['validation']: $setting['type'];
				$this->validation_ref[$key]=$type;
				//return $type;
				return $this->validation_check($value, $type, $setting);//$type;
			}
			elseif($setting['type']=='array'||$setting['type']=='category_filter') {
				$this->get_validation_type($id, $value, $setting['children']);
			}		
		}
		
		return $return;	
	}
	
	public function validation_check($value, $type,$setting) {
		print '<p>Input '.$value.' as '.$type.'<p>';//.' result  '.strcmp((string)$key,(string)$id);
		print_r($setting);
		print '<br>';
		return 1;
	}



	public function get_settings() {
		
		/* General Settings
		===========================================*/
		
		$this->settings['logo'] = array(
			'title'   =>  'Custom Logo' ,
			'desc'    =>  'Enter a URL or upload an image' ,
			'std'     => '',
			'type'    => 'image',
			'section' => 'general',
			'alt' => 'Preview of site logo',
			'button' => 'Upload Logo',
			'validation'=> 'text'
		);
		
		$this->settings['favi'] = array(
			'title'   => 'Custom Favicon' ,
			'desc'    => 'Enter a URL or upload an image. Images should be 16x16 and ico, png or gif format',
			'std'     => '',
			'type'    => 'image',
			'section' => 'general',
			'alt' => 'Preview of site favicon',
			'button' => 'Upload Favicon',
			'validation'=> 'text'
		);
		
		
		$choices = array(5=>'test',4=>'test2');
		
		$this->settings['featured'] = array(
			'title'   =>  'Featured Tag' ,
			'desc'    =>  'Posts with this tag will appear as "Featured Posts" in the header',
			'type'    => 'select',
			'std'     => '-1',
			'section' => 'general',
			'choices' => array(
							'-1' => '*All Posts*',
							'0' => '*No Headline*')
							+ $choices
		);
		
		$this->settings['head_tracker'] = array(
			'title'   =>  'Tracking Code',
			'desc'    =>  'Paste your analytics code here. It will be inserted into the head tag of your site',
			'std'     => '',
			'type'    => 'textarea',
			'section' => 'general',
			'validation' => 'html'
		);
		
		/* Headline Layout
		===========================================*/
		$all_ids = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14);	
		$choices = $all_ids;
		
		$this->settings['cat_order'] = array(
			'title'   =>  'Choose category order and post count for headlines',
			'desc'    =>  '<p>Drag the category titles to set the order they will be displayed in the headlines.</p>
							<p>The number in the brackets is the total number of posts under each category</p>
							<p>Use the input boxes next to each category to determine how many posts will be displayed under each headline category on the front page, and on all other pages</p>
							<p>The default is 3 posts on the home page, and 1 on all other pages</p>
							',
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
									'desc'   => '',
									'type'    => 'text',
									'std'	  => '3',
									'validation' => 'integer'
							),
								'home_no' => array(
					'desc'   => '',
					'type'    => 'text',
					'std'	  => '5',
					'validation' => 'integer'
				)
			)
		);
		
		$max = array();
		$max_values = range(1, count($all_ids));
		$max = array_combine(range(1, count($all_ids)),array_values($max_values));		
		$this->settings['max_cat'] = array(
			'title'   =>  'Maximum headline categories' ,
			'desc'    =>  'Select the max number of headline categories to display (empty ones may be ignored)',
			'type'    => 'select',
			'std'     => count($all_ids),
			'section' => 'head_layout',
			'choices' => $max
		);
	
		
		$this->settings['empty_cat'] = array(
			'title'   =>  'Show empty categories',
			'desc'    =>  'Tick to show empty headline categories' ,
			'type'    => 'checkbox',
			'section' => 'head_layout',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		
		$this->settings['cat_pages'] = array(
			'title'   =>  'Pages to display headlines' ,
			'desc'    =>  'Show categories on ticked pages' ,
			'type'    => 'array',
			'section' => 'head_layout',
			'children'   => array(
				'never' => array(
					'desc'   =>  'Never display (overrides all other boxes)',
					'type'    => 'checkbox',
					'std'	  => '0',
					'class'	  => 'deselect'
				),
				'home'=>array(
					'desc'   =>  'Homepage',
					'type'    => 'checkbox',
					'std'	  => '1'
				),		
				'posts'=>array(
					'desc'   =>  'Posts',
					'type'    => 'checkbox',
					'std'	  => '1'
				),		
				'pages'=>array(
					'desc'   =>  'Pages',
					'type'    => 'checkbox',
					'std'	  => '0'
				),		
				'archives'=>array(
					'desc'   =>  'Archives',
					'type'    => 'checkbox',
					'std'	  => '0'
				),		
				'search'=>array(
					'desc'   =>  'Search',
					'type'    => 'checkbox',
					'std'	  => '0'
				),		
			)
		);

		/* Headline Images
		===========================================*/

		$this->settings['head_img'] = array(
			'title'   =>  'Pages to display featured images under headline categories',
			'desc'    =>  'Show headline featured images on ticked pages',
			'type'    => 'array',
			'section' => 'head_images',
			'children'   => array(
				'never' => array(
					'desc'   =>  'Never display (overrides all other boxes)',
					'type'    => 'checkbox',
					'std'	  => '0',
					'class'	  => 'deselect'
				),
				'home'=>array(
					'desc'   =>  'Homepage',
					'type'    => 'checkbox',
					'std'	  => '1'
				),		
				'posts'=>array(
					'desc'   =>  'Posts',
					'type'    => 'checkbox',
					'std'	  => '1'
				),		
				'pages'=>array(
					'desc'   =>  'Pages' ,
					'type'    => 'checkbox',
					'std'	  => '0'
				),		
				'all'=>array(
					'desc'   =>  'All Headlines' ,
					'type'    => 'checkbox',
					'std'	  => '0'
				),			
			)
		);
		
	
		$this->settings['max_feat_h'] = array(
			'type'    => 'text',
			'title'   =>  'Maximum height of featured images' ,
			'desc'    =>  'Enter an integer maximum height for all featured images' ,
			'std'     => '150',
			'section' => 'head_images',
			'validation' => 'integer'
		);	
		
		
		/* Carousel
		===========================================*/		
		
		$wrap_options = array('none','circular', 'first', 'last', 'both');
		$this->settings['wrap'] = array(
			'type'    => 'select',
			'title'   =>  'Wrap style',
			'desc'    =>  'Choose a wrap method i.e. how the carousel behaves when it reaches the last/first category',
			'std'     => 'none',
			'section' => 'carousel',
			'choices' => array_combine(array_values($wrap_options), array_values($wrap_options))
		);
		
		$anim = array('linear','jswing','easeInQuad','easeOutQuad','easeInOutQuad','easeInCubic','easeOutCubic','easeInOutCubic','easeInQuart','easeOutQuart','easeInOutQuart','easeInSine','easeOutSine','easeInOutSine','easeInExpo','easeOutExpo','easeInOutExpo','easeInQuint','easeOutQuint','easeInOutQuint','easeInCirc','easeOutCirc','easeInOutCirc','easeInElastic','easeOutElastic','easeInOutElastic','easeInBack','easeOutBack','easeInOutBack','easeInBounce','easeOutBounce','easeInOutBounce');
		$this->settings['easing'] = array(
			'type'    => 'select',
			'title'   =>  'Animation',
			'desc'    =>  'Choose an animation style. For more information see the<a href="http://jqueryui.com/demos/effect/easing.html">jQuery easing demos</a>',
			'std'     => 'easeInQuad',
			'section' => 'carousel',
			'choices' => array_combine(array_values($anim), array_values($anim))
		);
		
		$trigger = array('click','mouseover');
		$this->settings['trigger'] = array(
			'type'    => 'select',
			'title'   =>  'Trigger for scrolling' ,
			'desc'    =>  'Scrolling can be triggered by left click or moving the cursor over the scroll bar',
			'std'     => 'click',
			'section' => 'carousel',
			'choices' => array_combine(array_values($trigger), array_values($trigger))
		);
		
		$all_ids = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14);
		$choice_values = range(1, count($all_ids));
		$step = array_combine(range(1, count($all_ids)),array_values($choice_values));
		$this->settings['step'] = array(
			'type'    => 'select',
			'title'   =>  'Step size' ,
			'desc'    =>  'How many categories to scroll on each cycle' ,
			'std'     => '1',
			'section' => 'carousel',
			'choices' => $step
		);
			
		$this->settings['speed'] = array(
			'type'    => 'text',
			'title'   =>  'Animation speed (ms)' ,
			'desc'    =>  'Enter an integer value for the animation speed in ms. Type "0" for off' ,
			'std'     => '100',
			'section' => 'carousel',
			'validation' => 'integer'
		);	
		
		$this->settings['autoscroll'] = array(
			'type'    => 'text',
			'title'   =>  'Auto scroll delay (seconds)' ,
			'desc'    =>  'Enter an integer value for the delay before the headlines auto-scroll (in seconds). Type "0" for no auto scrolling' ,
			'std'     => '0',
			'section' => 'carousel',
			'validation' => 'integer'
		);	

		/* Reset
		===========================================*/
/*		
		$this->settings['reset_theme'] = array(
			'section' => 'reset',
			'title'   =>  'Reset theme' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    =>  'Check this box and click "Save Changes" below to reset theme options to their defaults.' )
		);
*/		
	}
}

$test = new validCheck();

//echo '<p>';
//if(' ') $test = true;

//print '<p> blank '.$test.'</p>';
//print_no(10);
//print reverse_str('stri ngle');
//is_prime(7);


function reverse_str($string) {
    if($string) {
    	$new = substr($string,1);
    	if($string[0]==' ') print '<p>space!';
    	return reverse_str($new).$string[0];
    }
    else return '';
}

function is_prime($orig, $divisor=2) {
	if(is_int($orig/$divisor)) print '<p>not prime  '.$divisor;
	elseif($divisor<=$orig/2) return is_prime($orig,$divisor+1);
	else print 'Prime';
}

function print_no($n) {
	if($n >= 0) {
		print $n;
		return print_no($n-1)	;
	}
}


?>