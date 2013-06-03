<?php 

if (get_option( "portfolio_slideshow_options['version']" )  < PORTFOLIO_SLIDESHOW_VERSION ) { // If the version numbers don't match, run the upgrade script
	require ( PORTFOLIO_SLIDESHOW_PATH . 'inc/upgrader.php' );
}

$ps_options = get_option( 'portfolio_slideshow_options' ); 

//lets set up the shortcode
require ( PORTFOLIO_SLIDESHOW_PATH . 'inc/shortcode.php' );

//allows us to run the shortcode in widgets
add_filter( 'widget_text', 'do_shortcode' );

//yeah, a widget!
require ( PORTFOLIO_SLIDESHOW_PATH . 'inc/widget.php' );

//our custom post & image size functions
require ( PORTFOLIO_SLIDESHOW_PATH . 'inc/custom-post.php' );

if ( ! function_exists( 'add_post_id' ) ) {
	// put the attachment ID on the media page
	function add_post_id( $content ) { 
		$showlink = "Attachment ID:" . get_the_ID( $post->ID, true );
		$content[] = $showlink;
		return $content;
	}
	add_filter ( 'media_row_actions', 'add_post_id' );
}

	if ( ! function_exists( 'ps_action_links' ) ) {
	//action link http://www.wpmods.com/adding-plugin-action-links
	function ps_action_links( $links, $file ) {
	 	static $this_plugin;

	    if ( !$this_plugin ) {
	        $this_plugin = PORTFOLIO_SLIDESHOW_LOCATION;
	    }

	    // check to make sure we are on the correct plugin
	    if ( $file == $this_plugin ) {
	        // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
	        $settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=portfolio_slideshow">Settings</a>';
	        // add the link to the list
	        array_unshift( $links, $settings_link );
	    }
	    return $links;
	}
	add_filter( 'plugin_action_links', 'ps_action_links', 10, 2 );
}

// automatic updater http://w-shadow.com/blog/2010/09/02/automatic-updates-for-any-plugin
if ( $ps_options['license']  && PHP_VERSION > 5 ) {
	$ps_update = true;
	require 'updater.php';
	$MyUpdateChecker = new PluginUpdateChecker(
		'http://madebyraygun.com/plugin-support/om0urOxipuz8/psp.php',
		PORTFOLIO_SLIDESHOW_LOCATION,
		'portfolio-slideshow-pro'
	);

	function addSecretKey( $query ){
		global $ps_options;
		$query['secret'] = $ps_options['license'];
		return $query;
	}
	$MyUpdateChecker->addQueryArgFilter( 'addSecretKey' );
}


if ( ! function_exists( 'ps_image_attachment_fields_to_edit' ) ) {
	//Adds custom fields to attachment page http://wpengineer.com/2076/add-custom-field-attachment-in-wordpress/
	function ps_image_attachment_fields_to_edit( $form_fields, $post) {  
		$form_fields['ps_image_link'] = array(  
			"label" => __( "<span style='color:#c43; padding:0'>Portfolio Slideshow<br />Slide link URL</span>" ),  
			"input" => "text",
			"value" => get_post_meta( $post->ID, "_ps_image_link", true )  
		);        
		return $form_fields;  
	}  
	add_filter( "attachment_fields_to_edit", "ps_image_attachment_fields_to_edit", null, 2 ); 
}

if ( ! function_exists( 'ps_image_attachment_fields_to_save' ) ) {
	function ps_image_attachment_fields_to_save( $post, $attachment) {    
		if( isset( $attachment['ps_image_link'] ) ){  
			update_post_meta( $post['ID'], '_ps_image_link', $attachment['ps_image_link'] );  
		}  
		return $post;  
	}  
	add_filter( "attachment_fields_to_save", "ps_image_attachment_fields_to_save", null, 2 );
}	

if ( ! function_exists( 'ps_get_image_sizes' ) ) {
	//Create a list of image sizes to use in the dropdown
	function ps_get_image_sizes() {
		global $ps_options;

		// Get the intermediate image sizes, add full & custom sizes size to the array.
		$sizes = get_intermediate_image_sizes();
		$sizes[] = 'custom';
		$sizes[] = 'full';

		// Loop through each of the image sizes.
		foreach ( $sizes as $size ) {
			if ( $size != "ps-thumb" ) {
				echo "<option value='$size'";
				if ( $ps_options['size'] == $size ){
					echo " selected='selected'"; 
				}
				echo ">$size</option>";
			}
		}
	}
}

// Output the javascript & css here
if( ! is_admin()){
   
	switch ( $ps_options['jquery'] ) {
	
		case "1.4.4" :	
			wp_deregister_script( 'jquery' ); 
			wp_register_script( 'jquery', ( "http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js" ), false, '1.4.4', false); 
			wp_enqueue_script( 'jquery' );
			break;

		case "disabled" :
			// do nothing
			break;
			
		default :
			wp_deregister_script( 'jquery' ); 
			wp_register_script( 'jquery', ( "https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" ), false, '1.6.1', false ); 
			wp_enqueue_script( 'jquery' );
			break;
	}
		
	//malsup cycle script
	wp_register_script( 'cycle', plugins_url( 'js/jquery.cycle.all.min.js', dirname(__FILE__) ), false, '2.99', true ); 
	wp_enqueue_script( 'cycle' );

		//carousel script
	wp_register_script( 'scrollable', plugins_url( 'js/scrollable.min.js', dirname(__FILE__) ), false, '1.2.5', true ); 
	wp_enqueue_script( 'scrollable' );

	

	if ( $ps_options['debug'] == "true" ) {
		//our script
		wp_register_script( 'portfolio-slideshow', plugins_url( 'js/portfolio-slideshow.js', dirname(__FILE__) ), false, $ps_options['version'], true ); 
		wp_enqueue_script( 'portfolio-slideshow' );
		//our style 
		wp_register_style( 'portfolio_slideshow', plugins_url( "css/portfolio-slideshow.css", dirname(__FILE__) ), false, $ps_options['version'], 'screen' );
		wp_enqueue_style( 'portfolio_slideshow' ); 
	} else {
		wp_register_script( 'portfolio-slideshow', plugins_url( 'js/portfolio-slideshow.min.js', dirname(__FILE__) ), false, $ps_options['version'], true ); 
		wp_enqueue_script( 'portfolio-slideshow' );
		wp_register_style( 'portfolio_slideshow', plugins_url( "css/portfolio-slideshow.min.css", dirname(__FILE__) ), false, $ps_options['version'], 'screen' );
		wp_enqueue_style( 'portfolio_slideshow' ); 
	}	

	if ( $ps_options['fancybox'] == "true" ) {
		wp_register_script( 'fancybox', plugins_url( 'js/fancybox/jquery.fancybox-1.3.4.pack.js', dirname(__FILE__) ), false, '1.3.4', true ); 
		wp_enqueue_script( 'fancybox' );

		wp_register_style( 'fancybox', plugins_url( 'js/fancybox/jquery.fancybox-1.3.4.css', dirname(__FILE__) ), false, '1.3.4', 'screen' ); 
		wp_enqueue_style( 'fancybox' );
	}
	
}

if ( ! function_exists( 'portfolio_slideshow_head' ) ) {
	function portfolio_slideshow_head() {
		global $ps_count, $ps_options;
			echo '
<!-- Portfolio Slideshow-->
<noscript><link rel="stylesheet" type="text/css" href="' .  plugins_url( "css/portfolio-slideshow-noscript.css?ver=" . $ps_options['version'], dirname(__FILE__) ) . '" /></noscript>';


$pagerpos_left = $ps_options['thumbnailmargin'] / 2;
$navpos_left = $ps_options['thumbsize'] / 2;
$navpos_right = $ps_options['thumbsize'] / 2 + 10;
echo '<style type="text/css">.centered .pscarousel {left: '. $pagerpos_left .'px; } .centered a.next.browse.right {left: '. ( 10 + $pagerpos_left ) .'px; } .scrollable {height:'. $ps_options['thumbsize'] .'px;} a.prev.browse.left {top:'. $navpos_left .'px}a.next.browse.right {top:-'. $navpos_right .'px}div.slideshow-wrapper .pager img {padding-right:'. $ps_options['thumbnailmargin'] .'px !important; padding-bottom:'. $ps_options['thumbnailmargin'] .'px !important;}</style>';

echo '<script type="text/javascript">/* <![CDATA[ */var psTimeout = new Array(); psAudio = new Array(); var psAutoplay = new Array(); var psDelay = new Array(); var psFluid = new Array(); var psTrans = new Array(); var psRandom = new Array(); var psCarouselSize = new Array(); var touchWipe = new Array(); var keyboardNav = new Array(); var psPagerStyle = new Array(); psCarousel = new Array(); var psSpeed = new Array(); var psNoWrap = new Array();/* ]]> */</script>
<!--//Portfolio Slideshow-->
';
	} // end portfolio_head 
}
add_action( 'wp_head', 'portfolio_slideshow_head' );

if ( ! function_exists( 'portfolio_slideshow_foot' ) ) {
	function portfolio_slideshow_foot() {
		global $ps_options;
		// Set up js variables
		//$ps_showhash should always be false on any non-singular page
		if ( !is_singular() ) { $ps_options['showhash'] = 0; }
echo "<script type='text/javascript'>/* <![CDATA[ */ var portfolioSlideshowOptions = { psFancyBox:$ps_options[fancybox], psHash:$ps_options[showhash], psThumbSize:'$ps_options[thumbsize]', psLoader:$ps_options[showloader], psFluid:$ps_options[allowfluid], psTouchSwipe:$ps_options[touchswipe], psKeyboardNav:$ps_options[keyboardnav], psInfoTxt:'$ps_options[infotxt]' };/* ]]> */</script>";
	}    
}
add_action( 'wp_footer', 'portfolio_slideshow_foot' );

/* 
* Get's the actual image source if we're in WPMU
* Via http://www.binarymoon.co.uk/2009/10/timthumb-wordpress-mu/
*/

function get_image_path ($attachment_id = null, $imgsize = "large", $return = "src") {
	
	$imageAtts = wp_get_attachment_image_src( $attachment_id, $imgsize );
	$theImageSrc = $imageAtts[0];
	global $blog_id;
	if (isset($blog_id) && $blog_id > 1) { // if it's a WPMU installation, we need to dig up the actual path for the files
		$imageParts = explode('/files/', $imageAtts[0]);
		if (isset($imageParts[1])) {
			$theImageSrc = '/' . str_replace( ABSPATH, '', WP_CONTENT_DIR ) . '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
		}
	} else { // if it's a standard WP installation, just use the relative path (for TimThumb compatibility)
	$url = 'http://' . $_SERVER['HTTP_HOST'];
	$theImageSrc = str_replace( $url, '', $imageAtts[0] );
	
	}
		
	if ( $return === "src" ) {
		return $theImageSrc;
	}
	
	if ( $return === "width" ) {
		return $imageAtts[1];
	}
	
	if ( $return === "height" ) {
		return $imageAtts[2];
	}
}

$url = 'http://localhost/wp-content/uploads/2011/06/sample.jpg';
$uploads = wp_upload_dir();
$path = str_replace( $uploads['baseurl'], $uploads['basedir'], $url);

/*
* TinyMCE button
*/

function add_ps_button() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_ps_tinymce_plugin");
     add_filter('mce_buttons', 'register_ps_button');
   }
}
 
function register_ps_button($buttons) {
   array_push($buttons, "|", "psbutton");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_ps_tinymce_plugin($plugin_array) {
	$fscb_base_dir = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));

   $plugin_array['psbutton'] = $fscb_base_dir . 'tinymce/ps-buttons.js';
   return $plugin_array;
  
}
 
function my_refresh_mce($ver) {
  $ver += 5;
  return $ver;
}

// init process for button control
add_filter( 'tiny_mce_version', 'my_refresh_mce');
add_action('init', 'add_ps_button');
?>
