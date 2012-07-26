(function($) {
	$(document).ready(function() {
		var result = $('#cat_order').sortable({
			axis:'y',
			containment:'document',
		})
		.parents('.cat_menu').addClass('cat_input')
	
		$("#tab_wrap").tabs();
	
		$('.saved').animate({opacity: 0}, 3000, function(){$(this).css('display','none')});
		
		window.formfield='';
		$('.upload_image_button').click(function() {
			window.formfield = $('.upload_field', $(this).parent());
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
			return false;
		});
	
		$.send_to_editor = function(html) {
			imgurl = $('img',html).attr('src');
			window.formfield.val(imgurl);
			tb_remove();
			preview_img(window.formfield)
		};
	
		$('.upload_field').change(function(){
			preview_img($(this));
		})
	
	
		function preview_img(selector){
			url = $(selector).val()	
			img_div = jQuery(selector).parent().find('.img-preview');	
			img_div.animate({opacity: 0}, 100, function() {}).empty();
			img_hold = img_div.append('<img src="" class = "preview"/>');
			image = img_hold.find('.preview').attr('src',url);
			img_div.animate({opacity: 1}, 300, function() {image.animate({opacity: 1}, 300, function() {})});
		}
		$('.deselect').click(function(){
			if(this.checked){$(this).siblings(':checkbox').removeAttr('checked');}
			else {}
		});
	});

})(jQuery);