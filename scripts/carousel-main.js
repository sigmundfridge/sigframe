(function($) {	
    $('html').addClass('js');
	$(document).ready(function(){ 		
		$('#latestPosts').jcarousel({
			size: parseInt(carouselParam.carousel_size),
			scroll: parseInt(carouselParam.carousel_step),
			wrap: carouselParam.carousel_wrap,
			animation: parseInt(carouselParam.carousel_speed),
			easing:carouselParam.carousel_easing,
			auto:parseInt(carouselParam.carousel_auto),
			buttonNextEvent:carouselParam.carousel_trigger,
			buttonPrevEvent:carouselParam.carousel_trigger,
	//		initCallback:show_on_init(),
			setupCallback:show_on_ready()
		});
	});

	function show_on_init() {
		$('.carousel').show('slide',{direction:'down'});
	};

	function show_on_ready() {
		$('.carousel').fadeIn();
	};
	
})(jQuery);