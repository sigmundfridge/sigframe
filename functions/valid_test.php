<?php
class validCheck {
	public $options;
	public $settings;
	public $validation_ref = array();
	
	public function __construct() {
		$this->options = array ( 'logo' => 'http://sigfrid.co.uk/frame/wp-content/uploads/2012/07/logo-300x61.png', 
					'favi' => 'http://sigfrid.co.uk/frame/wp-content/uploads/2012/07/favicon1.png',
					'featured' => 5 ,
					'head_tracker' =>'',
					'cat_order' => array ( 
						'8' => array ( 'post_no' => 1, 
										'home_no' => 2 ), 
						'6' => array ( 'post_no' => 3, 
										'home_no' => 4 ), 	
						'7' => array ( 'post_no' => 5, 
										'home_no' => 6 ),
						'1' => array ( 'post_no' => 7, 
										'home_no' =>8 ), 
						'9' => array ( 'post_no' => 9, 
										'home_no' => 10 ), 
						'14' => array ( 'post_no' => 11, 
										'home_no' => 12 ), 
						'10' => array ( 'post_no' => 13, 
										'home_no' => 14), 
						'11' => array ( 'post_no' => 15,
										'home_no' => 16 ), 
						'12' => array ( 'post_no' => 17,
										'home_no' => 18 ),
						'13' => array ( 'post_no' => 19,
										'home_no' => 20 )
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
			$result = $this->get_inputs($inputs, '');
			print '<p>Result    ';
			print_r($inputs);
			//print_r($this->validation_ref);//$check = $this->validation_check($option['value'], $this->validation_ref[$option['key']], null);
			//		print '<p>';
	//	}
			return $inputs;	
	}
	
	public function clean_input($value) {
	}
	
	
	public function get_inputs(&$input, $key) {
		if(!is_array($input)) {	
				print '<p>**************START******</p>';
				print '<p>**INPUT**</p>';
				print '<p><b>Element</b> is <i>'.$key.'</i> with a value of '.htmlentities($input);
				$result = $this->validate_element($input, $key);
				print '<p>**OUTPUT**</p>';
				print '<p>Result is : '.$result.'</p>';
				if($result === false) {
					print '<p>*Test FAILED</p>';
					$input = $result;
				}
				return $input;
			}
		foreach($input as $id => &$child) {
				$this->get_inputs($child, $id);
		}	
	}
	
	public function validate_element($input, $key) {
		if(array_key_exists($key,$this->validation_ref)) {
			$setting = $this->validation_ref[$key]['setting'];
		}
		else {
			$setting = $this->find_setting($key, $this->settings);
			$this->validation_ref[$key]['setting']=$setting;	
		}

		$type = $setting['validation']? $setting['validation']: $setting['type'];
		$this->validation_ref[$key]['type']=$type;
		$check = $this->validation_check($input, $type, $setting);	
		
		return $check;
	}
	
	
	public function find_setting($id, $settings) {
		$return = array();
		foreach($settings as $key => $setting) {
			if(strcmp($key,$id)==0) {
				return $setting;
			}
			elseif($setting['type']=='array'||$setting['type']=='category_filter') {
				$return = array_merge($return, $this->find_setting($id, $setting['children']));
			}		
		}
		return $return;
	}
	
	public function validation_check($value, $type, $setting) {
		print '<p>Checking '.htmlentities($value,ENT_QUOTES).' as '.$type.'<p>';//.' result  '.strcmp((string)$key,(string)$id);
		if(is_null($value)) 
			return '';

		if(get_magic_quotes_gpc())
				$value = stripslashes($value);
				
		switch($type) {
			case 'integer':
				return (preg_match("/^\-?\d+$/", $value))? $value: false;
			case 'js':
//				$value = htmlentities($value,ENT_QUOTES);
				$value = esc_js($value); //wordpress version....
				print 'html is '.$value;
				return $value;	
						/*		return (preg_match("/^
										([\d\w]*
											(
												&( 
													amp | apos | gt | lt | nbsp | quot | bull | hellip | [lr][ds]quo | [mn]dash | permil          
													| \#[1-9][0-9]{1,3} | [A-Za-z] [0-9A-Za-z]+ 
												);
												[\d\w]*
											)*
											[\d\w]*												
										)*
										$/i",$value))
					? $value:false;*/
			case 'text':
				$value = strip_tags($value);
				return (ctype_alnum($value))? $value: false;
			case 'select':
				return array_key_exists($value, $setting['choices'])?$value:false;
			case 'checkbox':
				return $values == 1 || $value == 0 ?$value: 0 ;
			case 'imgurl':
				return (preg_match("/^https?:\/\/[\w\d\.]+\.[\w\d\/\-]+\/[\w\d\-]+\.(jp?g|gif|png)$/i", $value))? $value: false;
		//		return (preg_match("/^https?:\/\/([a-z\-]+\.)+[a-z]{2,6}(/[^/#?]+)+\.(jpg|gif|png)$/i", $value))? true: false;
		//		return (preg_match("/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?.(?:jp?g|gif|png)$/i", $value))? true: false;
			default:
				return false;
			break;
		}
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
			'validation'=> 'imgurl'
		);
		
		$this->settings['favi'] = array(
			'title'   => 'Custom Favicon' ,
			'desc'    => 'Enter a URL or upload an image. Images should be 16x16 and ico, png or gif format',
			'std'     => '',
			'type'    => 'image',
			'section' => 'general',
			'alt' => 'Preview of site favicon',
			'button' => 'Upload Favicon',
			'validation'=> 'imgurl'
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
			'type'    => 'textarea_html',
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