<?php /* include Custom Fields Creator API */
require ( PORTFOLIO_SLIDESHOW_PRO_PATH . 'inc/metabox/wordpress-creation-kit.php');

$fint = array( 
		array( 'type' => 'upload', 'title' => 'Image', 'description' => '', 'required' => true ),
		array( 'type' => 'textarea', 'title' => 'Caption', 'description' => __('This field supports basic HTML tags like &lt;em>&lt;strong>&lt;a>, etc.', 'portfolio-slideshow-pro') ),
		array( 'type' => 'text', 'title' => 'URL', 'description' => __('If you want this image to link to a URL, add it here.<br />You can also put YouTube or Vimeo links here to include videos in the slideshow.', 'portfolio-slideshow-pro') ), 
	);

$args[] = array(
	'metabox_id' => 'portfolio_slideshow',
	'metabox_title' => 'Portfolio Slideshow',
	'post_type' => 'post',
	'meta_name' => '_portfolio_slideshow',
	'meta_array' => $fint	
);

$args[] = array(
	'metabox_id' => 'portfolio_slideshow',
	'metabox_title' => 'Portfolio Slideshow',
	'post_type' => 'page',
	'meta_name' => '_portfolio_slideshow',
	'meta_array' => $fint	
);

$args[] = array(
	'metabox_id' => 'portfolio_slideshow',
	'metabox_title' => 'Portfolio Slideshow',
	'post_type' => 'portfolio_slideshow',
	'meta_name' => '_portfolio_slideshow',
	'meta_array' => $fint	
);

$args[] = array(
	'metabox_id' => 'portfolio_slideshow',
	'metabox_title' => 'Image Details',
	'post_type' => 'psp_image',
	'single' => true,
	'meta_name' => '_portfolio_slideshow',
	'meta_array' => $fint	
);

foreach ( $args as $arg ) :
	new PSP_Wordpress_Creation_Kit( $arg );
endforeach;