<?php /*
 * Functions required for the modal popup version of the slideshow
 */
 
/* Create the shortcode */
add_shortcode( 'popup-slideshow', 'portfolio_slideshow_pro_popup_shortcode' );
 
/* Define the shortcode function */
function portfolio_slideshow_pro_popup_shortcode( $atts ) {

	global $ps_options;
 
 	extract( shortcode_atts( array(
 		'id' => '',
 		'text' => '',
 		'thumbnail' => 'false',
 		'thumbsize' => '150',
 		'windowheight' => '625',
 		'windowwidth' => '625',
 		'height' => '',
 		'width' => '',
 		'slideheight' => '',
 		'centered' => '',
 		'carousel' => '',
 		'carouselsize' => '4',
 		'showcaps' => '',
 		'showtitles' => '',
 		'showdesc' => '',
 		'navstyle' => '',
 		'navpos' => '',
 		'autoplay' => ''
 		
 	), $atts ) );
 
	if ( ! $id ) { $id = get_the_ID(); }
 	
 	$url = PORTFOLIO_SLIDESHOW_PRO_URL . '/inc/popup/popup.php?id=' . $id; 
 	
 	$args = '';
 	
 	if ( $height ) $args .= '&amp;h=' . $height;
	if ( $width ) $args .= '&amp;w=' . $width;
	if ( $slideheight ) $args .= '&amp;sh=' . $slideheight; 
 	if ( $windowheight ) $args .= '&amp;wh=' . $windowheight;
 	if ( $windowwidth ) $args .= '&amp;ww=' . $windowwidth;
 	if ( $showcaps ) $args .= '&amp;caps=' . $showcaps;
	if ( $showtitles ) $args .= '&amp;titles=' . $showtitles;
	if ( $showdesc ) $args .= '&amp;desc=' . $showdesc;
	if ( $carousel ) $args .= '&amp;carousel=' .$carousel;
	if ( $carouselsize ) $args .= '&amp;carouselsize=' .$carouselsize;
	if ( $centered ) $args .= '&amp;centered=' . $centered;
	if ( $navstyle ) $args .= '&amp;nav=' . $navstyle;
	if ( $navpos ) $args .= '&amp;navpos=' . $navpos;
	if ( $autoplay ) $args .= '&amp;autoplay=' . $autoplay;
 
 	if ( ! $text ) {
 		$text = $url;
 	}
 	
 	if ( $thumbnail == "true" ) { // find the thumbnail image 
 
    $children = array(
      'numberposts' => 1,
      'order'=> 'ASC',
      'orderby' => 'menu_order',
      'post_mime_type' => 'image',
      'post_parent' => $id,
      'post_status' => null,
      'post_type' => 'attachment', 
      'post_status' => 'inheirit'
    );
        
    $attachments = get_children( $children );	
 		        
    if ( $attachments ) {
      foreach( $attachments as $attachment ) {
        $image = psp_resize( $attachment->ID, '', $thumbsize, $thumbsize, false );
      } // foreach( $attachments as $attachment )
    } // if ( $attachments )	
 	}
 	
 	$ps_popup_output = '<a class="slideshow-popup" href="' . $url . $args . '">';
 	
 	if ( $thumbnail == "true" ) {
 		$ps_popup_output .= '<img src="' . $image[url] . '" /></a><br />';
 		if ( $text != $url ) {
 			$ps_popup_output .= $text . '<br />';
 		}
 	} else {
 		$ps_popup_output .= $text . '</a>';
 	}
 	 
 
 	return $ps_popup_output;
}