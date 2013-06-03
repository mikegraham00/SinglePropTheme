<?php



//automatically create home, details, gallery and location page



if (isset($_GET['activated']) && is_admin()){







	//Add Homepage



	$new_page_title = "Home";



	$new_page_content = "";



	$new_page_template = "template-page-home.php"; //ex. template-custom.php. Leave blank if you don't want a custom page template.







	//don't change the code below, unless you know what you're doing







	$page_check = get_page_by_title($new_page_title);



	$new_page = array(



		'post_type' => 'page',



		'post_title' => $new_page_title,



		'post_content' => $new_page_content,



		'post_status' => 'publish',



		'post_author' => 1,



	);



	if(!isset($page_check->ID)){



		$new_page_id = wp_insert_post($new_page);



		if(!empty($new_page_template)){



			update_post_meta($new_page_id, '_wp_page_template', $new_page_template);



		}



	}



	



	//Add Details Page



	$new_page_title = "Details";



	$new_page_content = "";



	$new_page_template = "template-page-property-details.php"; //ex. template-custom.php. Leave blank if you don't want a custom page template.







	//don't change the code below, unless you know what you're doing







	$page_check = get_page_by_title($new_page_title);



	$new_page = array(



		'post_type' => 'page',



		'post_title' => $new_page_title,



		'post_content' => $new_page_content,



		'post_status' => 'publish',



		'post_author' => 1,



	);



	if(!isset($page_check->ID)){



		$new_page_id = wp_insert_post($new_page);



		if(!empty($new_page_template)){



			update_post_meta($new_page_id, '_wp_page_template', $new_page_template);



		}



	}



	



	//Add Gallery Page



	$new_page_title = "Gallery";



	$new_page_content = "";



	$new_page_template = "template-page-gallery.php"; //ex. template-custom.php. Leave blank if you don't want a custom page template.







	//don't change the code below, unless you know what you're doing







	$page_check = get_page_by_title($new_page_title);



	$new_page = array(



		'post_type' => 'page',



		'post_title' => $new_page_title,



		'post_content' => $new_page_content,



		'post_status' => 'publish',



		'post_author' => 1,



	);



	if(!isset($page_check->ID)){



		$new_page_id = wp_insert_post($new_page);



		if(!empty($new_page_template)){



			update_post_meta($new_page_id, '_wp_page_template', $new_page_template);



		}



	}



	



	//Add Location Page



	$new_page_title = "Location";



	$new_page_content = "";



	$new_page_template = "template-page-location.php"; //ex. template-custom.php. Leave blank if you don't want a custom page template.







	//don't change the code below, unless you know what you're doing







	$page_check = get_page_by_title($new_page_title);



	$new_page = array(



		'post_type' => 'page',



		'post_title' => $new_page_title,



		'post_content' => $new_page_content,



		'post_status' => 'publish',



		'post_author' => 1,



	);



	if(!isset($page_check->ID)){



		$new_page_id = wp_insert_post($new_page);



		if(!empty($new_page_template)){



			update_post_meta($new_page_id, '_wp_page_template', $new_page_template);



		}



	}







}















//remove anything we don't need 



function remove_stuff(){



    remove_action('thematic_footer','thematic_siteinfo', 30 );



    remove_action('thematic_footer', 'thematic_siteinfoopen', 20);



    remove_action('thematic_footer', 'thematic_siteinfoclose', 40);



}



add_action('init','remove_stuff');







//replace the standard doctype declaration and html tag opening...



function html5_create_doctype($content) {



 $content = "<!DOCTYPE html>";



 $content .= "\n";



 $content .= "<html";



 return $content;



}



add_filter('thematic_create_doctype', 'html5_create_doctype');



 



//replace the lang attribute in the opening <html> tag...



function html5_language_attributes($content) {



	$content = "lang=\"en\"";



	return $content;



}



add_filter('language_attributes', 'html5_language_attributes');



 



//replace the <head> tag opening to remove the now defunct profile attribute and add the <meta charset="utf-8"> tag...



function html5_head($content) {



 $content = "<head>";



 $content .= "\n";



 $content .= "<meta charset=\"utf-8\">";



 $content .= "\n";



 return $content;



}



add_filter('thematic_head_profile', 'html5_head');



 



//remove the now defunct <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> tag...



function html5_create_charset($content) {



 $content = "";



 return $content;



}



add_filter('thematic_create_contenttype', 'html5_create_charset');







function childtheme_doctitle() {



 



 // You don't want to change this one.



 $site_name = get_bloginfo('name');



 



 // But you like to have a different separator



 $separator = '&raquo;';



 



 // We will keep the original code



 if ( is_single() ) {



 $content = single_post_title('', FALSE);



 }



 elseif ( is_home() || is_front_page() ) {



 $content = get_bloginfo('description');



 }



 elseif ( is_page() ) {



 $content = single_post_title('', FALSE);



 }



 elseif ( is_search() ) {



 $content = __('Search Results for:', 'thematic');



 $content .= ' ' . wp_specialchars(stripslashes(get_search_query()), true);



 }



 elseif ( is_category() ) {



 $content = __('Category Archives:', 'thematic');



 $content .= ' ' . single_cat_title("", false);;



 }



 elseif ( is_tag() ) {



 $content = __('Tag Archives:', 'thematic');



 $content .= ' ' . thematic_tag_query();



 }



 elseif ( is_404() ) {



 $content = __('Not Found', 'thematic');



 }



 else {



 $content = get_bloginfo('description');



 }



 



 if (get_query_var('paged')) {



 $content .= ' ' .$separator. ' ';



 $content .= 'Page';



 $content .= ' ';



 $content .= get_query_var('paged');



 }



 



 // until we reach this point. You want to have the site_name everywhere?



 // Ok .. here it is.



 $my_elements = array(



 'site_name' => $site_name,



 'separator' => $separator,



 'content' => $content



 );



 



 // and now we're reversing the array as long as we're not on home or front_page



 if (!( is_home() || is_front_page() )) {



 $my_elements = array_reverse($my_elements);



 }



 



 // And don't forget to return your new creation



 return $my_elements;



}



 



// Add the filter to the original function



add_filter('thematic_doctitle', 'childtheme_doctitle');







//set page background image to value of custom field "Page Background Image"



function new_background(){



	 global $post;



	



	 if (get_post_meta($post->ID, 'wpcf-page-background-image', true)) :



 	 	$background = get_post_meta($post->ID, 'wpcf-page-background-image', true);



     else :  //set a default background image



     	$background = get_post_meta(23, 'wpcf-page-background-image', true);



	endif;



	



	echo '<img src="'.$background.'"  class="bg"/>';







}



add_action('thematic_before','new_background');











// change Thematic #access menu for a Wordpress 3.0 menu



function child_access_menu() {



	$menu_sys = 'wp_nav_menu';



	return $menu_sys;



}



add_filter('thematic_menu_type', 'child_access_menu');







//add header wrap



function open_header_wrap() {



	echo "<div id=\"header-wrap\">\n";



}



add_action('thematic_aboveheader', 'open_header_wrap');







function close_header_wrap() {



	echo "</div><!--close header wrap-->\n";



}



add_action('thematic_belowheader', 'close_header_wrap');







//change default header text



function childtheme_override_blogtitle() {



	$args =array('post_type'=>'property');



	$property_array = get_posts($args);



	$propID = $property_array[0]->ID;



	



	?><div id="blog-title">



			<a href="<?php bloginfo('url') ?>/" title="<?php bloginfo('name') ?>" rel="home"><?php echo get_post_meta($propID, 'wpcf-street-address', true); ?></a> | 



			<?php echo get_post_meta($propID, 'wpcf-city', true) .", ". get_post_meta($propID, 'wpcf-state', true); ?>



		</div>



	<div id="status"><?php echo get_post_meta($propID, 'wpcf-status', true); ?></div>



	<?php



	



}



add_action('thematic_header', 'thematic_blogtitle', 3);







function childtheme_override_blogdescription() {



	//purposely left blank



}







function childtheme_page_top() { 	?>



	<div id="contact-form">



	<h2>Inquire About This Property</h2>



	<?php echo do_shortcode('[contact-form-7 id="10" title="Property Contact Form"]'); ?>



	</div>



	<?php 



}



add_filter('widget_area_page_top', 'childtheme_page_top');







function childtheme_abovecontainer() {



	



}



add_action('thematic_abovecontainer', 'childtheme_abovecontainer');







function add_toggle_script() {



	?>



	<script type="text/javascript">



		jQuery(document).ready(function($){



		  $("#contact-toggle").click(function(){



			$("#contact-form").slideToggle(1000, function(){



				



				$("#contact-toggle").text($(this).is(':visible') ? "close form" : "request more info");







				});



		  });



		});



	</script>



	<?php



}



add_action('thematic_head_scripts', 'add_toggle_script');







function add_fancybox() {



	?>



	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/fancybox/jquery.fancybox-1.3.4.pack.js"></script>



	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/fancybox/jquery.fancybox-1.3.4.css" type="text/css" />



	<script type="text/javascript">



		jQuery(document).ready(function($) {



			/* Apply fancybox to multiple items */



			



			$(".iframe").fancybox();



		});



</script>







<!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->



<?php



}



add_action('thematic_head_scripts', 'add_fancybox');







?>