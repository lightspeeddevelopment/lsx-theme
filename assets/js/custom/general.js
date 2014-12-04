jQuery(document).ready(function($) {
	$('table#wp-calendar').addClass('table');

	jQuery(window).scroll(function(){
	    if(jQuery(window).scrollTop() > 100) {
	        jQuery('header.banner').addClass('scrolled');
	    } else {
	        jQuery('header.banner').removeClass('scrolled');
	    }
	});

	jQuery(window).on('load', function(){
	    jQuery(window).resize();
	});
});