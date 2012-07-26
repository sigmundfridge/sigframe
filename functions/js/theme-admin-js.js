jQuery(document).ready(function($) {
	var result = $('#cat_order').sortable({
		axis:'y',
		containment:'document',
	});
	
	jQuery('#cat_order').parents('.cat_menu').addClass('cat_input');	
	jQuery("#tab_wrap").tabs();
	
	jQuery('.saved').animate({opacity: 0}, 3000, function(){jQuery(this).css('display','none')});
	
	window.formfield='';
	jQuery('.upload_image_button').click(function() {
		window.formfield = jQuery('.upload_field', jQuery(this).parent());
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});

	window.send_to_editor = function(html) {
		imgurl = jQuery('img',html).attr('src');
		window.formfield.val(imgurl);
		tb_remove();
		preview_img(window.formfield)
	};

	jQuery('.upload_field').change(function(){
		preview_img(jQuery(this));
	})
	
	
	function preview_img(selector){
		url = jQuery(selector).val()
		img_div = jQuery(selector).parent().find('.img-preview');
		img_div.animate({opacity: 0}, 100, function() {}).empty();
		img_hold = img_div.append('<img src="" class = "preview"/>');
		image = img_hold.find('.preview').attr('src',url);
		img_div.animate({opacity: 1}, 300, function() {image.animate({opacity: 1}, 300, function() {})});
	}

});