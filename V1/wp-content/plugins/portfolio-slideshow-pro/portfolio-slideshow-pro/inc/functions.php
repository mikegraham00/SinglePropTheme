<?php 

$ps_options = get_option( 'portfolio_slideshow_options' ); 

if ( $ps_options['version'] < PORTFOLIO_SLIDESHOW_PRO_VERSION ) { // If the version numbers don't match, run the upgrade script
	require ( PORTFOLIO_SLIDESHOW_PRO_PATH . 'inc/upgrader.php' );
}

$ps_options = get_option( 'portfolio_slideshow_options' ); 

/* Lets set up the shortcode */
require ( PORTFOLIO_SLIDESHOW_PRO_PATH . 'inc/shortcode.php' );
require ( PORTFOLIO_SLIDESHOW_PRO_PATH . 'inc/shortcode-functions.php' );

/* Allows us to run the shortcode in widgets  */
add_filter( 'widget_text', 'do_shortcode' );

/* Load the widget */
require ( PORTFOLIO_SLIDESHOW_PRO_PATH . 'inc/widget.php' );

/* Custom post & image size functions */
require ( PORTFOLIO_SLIDESHOW_PRO_PATH . 'inc/custom-post.php' );

/* include Custom Fields Creator API */
require ( PORTFOLIO_SLIDESHOW_PRO_PATH . 'inc/metabox/metabox.php');

/* Load the popup slideshow */
require ( PORTFOLIO_SLIDESHOW_PRO_PATH . 'inc/popup/popup-functions.php'); 

/* A small function to determine if a particular plugin is active */
function ps_plugin_is_active($plugin_var) {
	return in_array( $plugin_var. '/' .$plugin_var. '.php', apply_filters( 'active_plugins',get_option( 'active_plugins' ) ) );
}

/*
 *	Deactivate Portfolio Slideshow if present
 */

function ps_deactivate_conflicts() {
	$plugins = get_option('active_plugins' );
	$plugin_deactivate = array_keys( $plugins, 'portfolio-slideshow/portfolio-slideshow.php' );
	if ( $plugin_deactivate ) {
		unset( $plugins[$plugin_deactivate[0]]); }
	update_option( 'active_plugins', $plugins );
}

add_action('init', 'ps_deactivate_conflicts');

if ( ! function_exists( 'ps_add_post_id' ) ) {
	// put the attachment ID on the media page
	function ps_add_post_id( $content ) {
		if(isset($post)) :
			$showlink = "Attachment ID:" . get_the_ID( $post->ID, true );
			$content[] = $showlink;
			return $content;
		endif;
	}
	add_filter ( 'media_row_actions', 'ps_add_post_id' );
}

if ( ! function_exists( 'ps_action_links' ) ) {
	/* Action link http://www.wpmods.com/adding-plugin-action-links */
	function ps_action_links( $links, $file ) {
	 	static $this_plugin;

	    if ( !$this_plugin ) {
	        $this_plugin = PORTFOLIO_SLIDESHOW_PRO_LOCATION;
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
		'http://plugins.madebyraygun.com/plugin-support/om0urOxipuz8/psp.php',
		PORTFOLIO_SLIDESHOW_PRO_LOCATION,
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
			"label" => __( "<span style='color:#c43; padding:0'>Portfolio Slideshow<br />slide link URL</span>", 'portfolio-slideshow-pro'),  
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

add_action('init', 'psp_setup');

if ( ! function_exists('psp_setup') ) {

	function psp_setup() {
		global $ps_options;
		
		// Output the javascript & css here
		if( ! is_admin()){
		    
		    // jQuery  
			if ( $ps_options['jquery'] == "force" ) {
				wp_deregister_script( 'jquery' ); 
				wp_register_script( 'jquery', ( "http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" ), false, '1', false); 
				wp_enqueue_script( 'jquery' );
			} elseif ( $ps_options['jquery'] == "true" || $ps_options['jquery'] == "wp" ) {
				wp_enqueue_script( 'jquery' );
			}
					
			//carousel script
			if ( $ps_options['scrollable'] == "true" ) {
				wp_enqueue_script( 'scrollable', plugins_url( 'js/scrollable.min.js', dirname(__FILE__) ), false, '1.2.5', true ); 
			}

			if ( $ps_options['debug'] == "true" ) {
				//our script
				wp_enqueue_script( 'portfolio-slideshow', plugins_url( 'js/portfolio-slideshow.js', dirname(__FILE__) ), false, $ps_options['version'], true );
				//our style
				wp_enqueue_style( 'portfolio_slideshow', plugins_url( "css/portfolio-slideshow.css", dirname(__FILE__) ), false, $ps_options['version'], 'screen' );
			} else {
				wp_enqueue_script( 'portfolio-slideshow', plugins_url( 'js/portfolio-slideshow.min.js', dirname(__FILE__) ), false, $ps_options['version'], true );
				wp_enqueue_style( 'portfolio_slideshow', plugins_url( "css/portfolio-slideshow.min.css", dirname(__FILE__) ), false, $ps_options['version'], 'screen' );
			}
			
			if ( $ps_options['fancybox'] == "true" ) {
				wp_enqueue_script( 'fancybox', plugins_url( 'js/fancybox/jquery.fancybox-1.3.4.pack.js', dirname(__FILE__) ), false, '1.3.4a', true );
				wp_enqueue_style( 'fancybox', plugins_url( 'js/fancybox/jquery.fancybox-1.3.4.css', dirname(__FILE__) ), false, '1.3.4a', 'screen' );
			}
			
			if ($ps_options['photoswipe'] == "true") {
				// register photoswipe scripts & photoswipe-dependent portfolio-slideshow script
				wp_enqueue_script('ps-photoswipe-script', plugins_url('js/code.photoswipe.jquery-3.0.4.min.js', dirname(__FILE__)), false, '3.0.4', true);
				wp_enqueue_style('ps-photoswipe-style', plugins_url('css/photoswipe.min.css', dirname(__FILE__)), false, $ps_options['version'], 'screen');
			}
			
			if ( $ps_options['cycle'] == "true" ) {
				//malsup cycle script
				wp_enqueue_script( 'cycle', plugins_url( 'js/jquery.cycle.all.min.js', dirname(__FILE__) ), false, '2.99', true );
			}

		} else {
		/* Only on admin page */ 
		 
		 if ( isset( $_GET['page'] ) && $_GET['page'] == "portfolio_slideshow" ) {
			 	wp_enqueue_script( 'jquery' );
			 	wp_enqueue_script( 'jquery-ui-core' );
			 	wp_enqueue_script( 'jquery-ui-tabs' );
			 	wp_register_script( 'portfolio-slideshow-admin', PORTFOLIO_SLIDESHOW_PRO_URL . '/admin/js/portfolio-slideshow-admin.js', false, $ps_options['version'], true); 
			 	wp_enqueue_script( 'portfolio-slideshow-admin' );
			 	wp_register_style( 'portfolio-slideshow-admin', PORTFOLIO_SLIDESHOW_PRO_URL . '/admin/css/portfolio-slideshow-admin.css', false, $ps_options['version'], 'screen' ); 
			 	wp_enqueue_style( 'portfolio-slideshow-admin' );
		 }
		} 
	}		
}


function replace_gallery($content) { //finds the gallery shortcode and rewrites it
  $content = preg_replace('/\[gallery/','[portfolio_slideshow',$content,-1);
  return $content;
}

if ( !empty($ps_options['gallery']) && $ps_options['gallery'] == "true" ) {
	add_filter('the_content', 'replace_gallery');
}

if ( ! function_exists( 'portfolio_slideshow_head' ) ) {
	function portfolio_slideshow_head() {
		global $ps_count, $ps_options;
			echo '
<!-- Portfolio Slideshow-->
<noscript><link rel="stylesheet" type="text/css" href="' .  plugins_url( "css/portfolio-slideshow-noscript.css?ver=" . $ps_options['version'], dirname(__FILE__) ) . '" /></noscript>';

if ( $ps_options['scrollable'] == "true" ) {
	$pagerpos_left = $ps_options['carousel_thumbnailmargin'] / 2;
	$navpos_left = $ps_options['carousel_thumbsize'] / 2 +5;
	$navpos_right = $ps_options['carousel_thumbsize'] / 2 + 10;
	echo '<style type="text/css">.centered .ps-next {} .scrollable {height:'. $ps_options['carousel_thumbsize'] .'px;} .ps-prev {top:'. $navpos_left .'px} .ps-next {top:-'. $navpos_right .'px} .slideshow-wrapper .pscarousel img {margin-right:'. $ps_options['carousel_thumbnailmargin'] .'px !important; margin-bottom:'. $ps_options['carousel_thumbnailmargin'] .'px !important;}</style>';
}

echo '<script type="text/javascript">/* <![CDATA[ */var psTimeout = new Array(); psAudio = new Array(); var psAutoplay = new Array(); var psDelay = new Array(); var psFluid = new Array(); var psTrans = new Array(); var psRandom = new Array(); var psCarouselSize = new Array(); var touchWipe = new Array(); var psPagerStyle = new Array(); psCarousel = new Array(); var psSpeed = new Array(); var psLoop = new Array(); var psClickOpens = new Array(); /* ]]> */</script>
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
echo "<script type='text/javascript'>/* <![CDATA[ */ var portfolioSlideshowOptions = { psFancyBox:$ps_options[fancybox], psHash:$ps_options[showhash], psThumbSize:'$ps_options[thumbsize]', psFluid:$ps_options[allowfluid], psTouchSwipe:$ps_options[touchswipe], psKeyboardNav:$ps_options[keyboardnav], psBackgroundImages:$ps_options[backgroundimages], psInfoTxt:'$ps_options[infotxt]' };/* ]]> */</script>";
	}    
}
add_action( 'wp_footer', 'portfolio_slideshow_foot' );

add_action('pp_mobile_head', 'psp_pp_compatibility');

function psp_pp_compatibility() {
	echo '
	<link rel="stylesheet" type="text/css" href="' .  site_url('/') . 'wp-content/plugins/portfolio-slideshow-pro/css/portfolio-slideshow.min.css" />
	<link rel="stylesheet" type="text/css" href="' .  site_url('/') . 'wp-content/plugins/portfolio-slideshow-pro/js/fancybox/jquery.fancybox-1.3.4.css" />';
	
	echo '<script type="text/javascript">/* <![CDATA[ */var psTimeout = new Array(); psAudio = new Array(); var psAutoplay = new Array(); var psDelay = new Array(); var psFluid = new Array(); var psTrans = new Array(); var psRandom = new Array(); var psCarouselSize = new Array(); var touchWipe = new Array(); var psPagerStyle = new Array(); psCarousel = new Array(); var psSpeed = new Array(); var psLoop = new Array(); var psClickOpens = new Array(); /* ]]> */</script>';
		
	echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js?ver=1.7.1"></script>
	<script type="text/javascript" src="' .  site_url('/') . 'wp-content/plugins/portfolio-slideshow-pro/js/fancybox/jquery.fancybox-1.3.4.pack.js?ver=1.3.4a"></script>
	<script type="text/javascript" src="' .  site_url('/') . 'wp-content/plugins/portfolio-slideshow-pro/js/scrollable.min.js?ver=1.2.5"></script>
	<script type="text/javascript" src="' .  site_url('/') . 'wp-content/plugins/portfolio-slideshow-pro/js/jquery.cycle.all.min.js?ver=2.99"></script>
	<script type="text/javascript" src="' .  site_url('/') . 'wp-content/plugins/portfolio-slideshow-pro/js/portfolio-slideshow.min.js"></script>';
	
	global $ps_count, $ps_options;
			
	if ( !is_singular() ) { $ps_options['showhash'] = 0; }
	echo "<script type='text/javascript'>/* <![CDATA[ */ var portfolioSlideshowOptions = { psFancyBox:$ps_options[fancybox], psHash:$ps_options[showhash], psThumbSize:'$ps_options[thumbsize]', psFluid:$ps_options[allowfluid], psTouchSwipe:$ps_options[touchswipe], psKeyboardNav:$ps_options[keyboardnav], psBackgroundImages:$ps_options[backgroundimages], psInfoTxt:'$ps_options[infotxt]' };/* ]]> */</script>";

	echo '<style type="text/css">.carousel {display:none;} .browse {display:none !important; }</style>';
	
}


/*
* TinyMCE button
*/
if ( ! function_exists('add_ps_button') ) {
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
}

if ( ! function_exists('register_ps_button') ) {
	function register_ps_button($buttons) {
	   array_push($buttons, "|", "psbutton");
	   return $buttons;
	}
}
 
if ( ! function_exists('add_ps_tinymce_plugin') ) {
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
	function add_ps_tinymce_plugin($plugin_array) {
		$fscb_base_dir = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));
	
	   $plugin_array['psbutton'] = $fscb_base_dir . 'tinymce/ps-buttons.js';
	   return $plugin_array;
	  
	}
}

if ( ! function_exists('ps_refresh_mce') ) {
	function ps_refresh_mce($ver) {
	  $ver += 5;
	  return $ver;
	}
}

// init process for button control
add_filter( 'tiny_mce_version', 'ps_refresh_mce');
add_action('init', 'add_ps_button');


/* Utility functions to return images in the requested order 
 * http://wordpress.stackexchange.com/questions/11055/how-to-return-results-of-a-get-posts-in-explicitly-defined-order
 * Props to Dougall Campbell
 */

// Set post menu order based on our list  
function psp_set_include_order(&$query, $list) {
    // Map post ID to its order in the list:
    $map = array_flip($list);

    // Set menu_order according to the list     
    foreach ($query->posts as &$post) {
      if (isset($map[$post->ID])) {
        $post->menu_order = $map[$post->ID];
      }
    }  
}

// Sort posts by $post->menu_order.                                 
function psp_menu_order_sort($a, $b) {
  if ($a->menu_order == $b->menu_order) {
    return 0;
  }
  return ($a->menu_order < $b->menu_order) ? -1 : 1;
}


/*-----------------------------------------------------------------------------------*/
/* psp_resize - Resize images dynamically using wp built in functions
/*-----------------------------------------------------------------------------------*/
/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * php 5.2+
 *
 * Exemplo de uso:
 *
 * <?php
 * $thumb = get_post_thumbnail_id();
 * $image = psp_resize( $thumb, '', 140, 110, true );
 * ?>
 * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */
if ( !function_exists( 'psp_resize') ) {
	function psp_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {

		// this is an attachment, so we have the ID
		if ( $attach_id ) {

			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$file_path = get_attached_file( $attach_id );

		// this is not an attachment, let's use the image url
		} else if ( $img_url ) {

			$file_path = parse_url( $img_url );
			$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

			//$file_path = ltrim( $file_path['path'], '/' );
			//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];

			$orig_size = getimagesize( $file_path );

			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}

		$file_info = pathinfo( $file_path );

		// check if file exists
		$base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
		if ( !file_exists($base_file) )
		 return;

		$extension = '.'. $file_info['extension'];

		// the image path without the extension
		$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];
		
		/* Calculate the eventual height and width for accurate file name */
	
		if ( $crop == false ) {
			$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
			$width = $proportional_size[0];
			$height = $proportional_size[1];
		}
		
		$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

		// checking if the file size is larger than the target size
		// if it is smaller or the same size, stop right here and return
		if ( $image_src[1] > $width || $image_src[2] > $height ) {

			// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
			if ( file_exists( $cropped_img_path ) ) {

				$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
				$psp_image = array (
					'url' => $cropped_img_url,
					'width' => $width,
					'height' => $height
				);

				return $psp_image;
			}

			// $crop = false or no height set
			if ( $crop == false OR !$height ) {

				// calculate the size proportionaly
				$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
				$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;

				// checking if the file already exists
				if ( file_exists( $resized_img_path ) ) {

					$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

					$psp_image = array (
						'url' => $resized_img_url,
						'width' => $proportional_size[0],
						'height' => $proportional_size[1]
					);

					return $psp_image;
				}
			}

			// check if image width is smaller than set width
			$img_size = getimagesize( $file_path );
			if ( $img_size[0] <= $width ) $width = $img_size[0];
			
			// Check if GD Library installed
			if (!function_exists ('imagecreatetruecolor')) {
			    echo 'GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library';
			    return;
			}

			// no cache files - let's finally resize it
			$new_img_path = image_resize( $file_path, $width, $height, $crop );		
			if ( is_wp_error( $new_img_path ) ) die ( $new_img_path->get_error_message() );	
			$new_img_size = getimagesize( $new_img_path );
			$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

			// resized output
			$psp_image = array (
				'url' => $new_img,
				'width' => $new_img_size[0],
				'height' => $new_img_size[1]
			);

			return $psp_image;
		}

		// default output - without resizing
		$psp_image = array (
			'url' => $image_src[0],
			'width' => $image_src[1],
			'height' => $image_src[2]
		);

		return $psp_image;
	}
}