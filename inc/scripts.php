<?php
if ( ! defined( 'ABSPATH' ) ) return; // Exit if accessed directly

/**
 * Enqueue scripts and styles.
 *
 * @package 	lsx
 * @subpackage	scripts
 */
function lsx_scripts() {
	global $content_width;
	
	wp_enqueue_style('lsx_main_style', get_template_directory_uri() . '/style.css', array(), LSX_VERSION);
	
	wp_enqueue_style('lsx_main', get_template_directory_uri() . '/css/app.css', array( 'lsx_main_style', 'fontawesome', 'medium-break' ), LSX_VERSION);
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/vendor/bootstrap.min.js', array('jquery'), LSX_VERSION, false);
	wp_enqueue_script('modernizr', get_template_directory_uri() . '/js/vendor/modernizr-2.7.0.min.js', array('jquery'), LSX_VERSION, false);
	wp_enqueue_script('mousewheel', get_template_directory_uri() . '/js/vendor/jquery.mousewheel.min.js', array('jquery'), LSX_VERSION, false);
	wp_enqueue_script('smoothscroll', get_template_directory_uri() . '/js/vendor/jquery.simplr.smoothscroll.min.js', array('jquery'), LSX_VERSION, false);
	wp_enqueue_script('sticky', get_template_directory_uri() . '/js/vendor/jquery.sticky.min.js', array('jquery'), LSX_VERSION, false);
	wp_enqueue_script('picturefill', get_template_directory_uri() . '/js/vendor/picturefill.min.js', array(), LSX_VERSION, false);

	wp_enqueue_script('masonry');
	wp_enqueue_script('imagesLoaded', get_template_directory_uri().'/js/vendor/imagesloaded.pkgd.min.js', array('jquery','masonry'), LSX_VERSION);	
	if(defined('WP_DEBUG') && true === WP_DEBUG){
		wp_enqueue_script('lsx_script', get_template_directory_uri() . '/js/lsx-script.js', array('masonry'), LSX_VERSION, false);
	}else{
		wp_enqueue_script('lsx_script', get_template_directory_uri() . '/js/lsx-script.min.js', array('masonry'), LSX_VERSION, false);
	}
	
	//Set some parameters that we can use in the JS
	$is_portfolio = false;
	if(is_post_type_archive('jetpack-portfolio') || is_tax('jetpack-portfolio-type') || is_tax('jetpack-portfolio-tag') || is_page_template('page-templates/template-portfolio.php')){
		$is_portfolio = true;
	}
	$param_array = array(
			'is_portfolio' => $is_portfolio
	);
	//Set the columns for the archives
	$param_array['columns'] = apply_filters('lsx_archive_column_number',3);
	wp_localize_script( 'lsx_script', 'lsx_params', $param_array );

	wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), LSX_VERSION );
	
	wp_enqueue_style('medium-break', get_template_directory_uri() . '/css/medium-nav-break.css', array(), LSX_VERSION);
	
	$font = get_theme_mod('lsx_font','raleway_open_sans');
	switch($font){
		case 'raleway_open_sans':
			$header_font_location = 'Raleway';
			$body_font_location = 'Open+Sans';
		break;

		case 'noto_serif_noto_sans':
			$header_font_location = 'Noto+Serif';
			$body_font_location = 'Noto+Sans';
		break;
		
		case 'noto_sans_noto_sans':
			$header_font_location = 'Noto+Sans';
			$body_font_location = 'Noto+Sans';
		break;		

		case 'alegreya_open_sans':
			$header_font_location = 'Alegreya';
			$body_font_location = 'Open+Sans';
		break;	
				
		//raleway_open_sans
		default:
			$header_font_location = 'Raleway'; 
			$body_font_location = 'Open+Sans';
		break;
	}
	
	$http_var = 'http';
	if(is_ssl()){ $http_var .= 's'; }
	
	//Call the Google Fonts and then Enque them.
	wp_register_style('lsx-header-font', $http_var.'://fonts.googleapis.com/css?family='.$header_font_location);
	wp_register_style('lsx-body-font', $http_var.'://fonts.googleapis.com/css?family='.$body_font_location);
	wp_enqueue_style( 'lsx-header-font' );
	wp_enqueue_style( 'lsx-body-font' );
	
	wp_enqueue_style('lsx_font_scheme', esc_url( get_template_directory_uri() . '/css/'.$font.'.css' ), array(), LSX_VERSION);
	
}
add_action( 'wp_enqueue_scripts', 'lsx_scripts' );

/**
 * Enqueue scripts and styles (for child theme).
 *
 * @package 	lsx
 * @subpackage	scripts
 */
function lsx_scripts_child_theme() {
	global $content_width;
	if(is_child_theme() && file_exists(get_stylesheet_directory() . '/custom.css')) {
		wp_enqueue_style( 'child-css', get_stylesheet_directory_uri() . '/custom.css', array( 'lsx_main' ), LSX_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'lsx_scripts_child_theme', 1999 );

/**
 * Defer JavaScript
 *
 * @package 	lsx
 * @subpackage	scripts
 */
function lsx_scripts_defer_parsing( $url ) {
	if ( ! ( is_admin() ) ) {
		if ( FALSE === strpos( $url, '.js' ) ) return $url;
		if ( strpos( $url, 'jquery.js' ) ) return $url;
		if ( strpos( $url, ' defer ' ) ) return $url;
		return "$url' defer onload='";
	}

	return $url;
}
add_filter( 'clean_url', 'lsx_scripts_defer_parsing', 11, 1 );
