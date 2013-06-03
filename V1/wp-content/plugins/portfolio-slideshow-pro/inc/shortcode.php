<?php

// create the shortcode
add_shortcode( 'portfolio_slideshow', 'portfolio_slideshow_pro_shortcode' );

// define the shortcode function
function portfolio_slideshow_pro_shortcode( $atts ) {
	
	STATIC $i=0;

	global $ps_options;

	extract( shortcode_atts( array(
		'size' => $ps_options['size'],
		'nowrap' => $ps_options['nowrap'],
		'speed' => $ps_options['speed'],
		'delay' => '0',
		'trans' => $ps_options['trans'],
		'centered' => $ps_options['centered'],
		'height'	=> '',
		'width'	=> '',
		'timeout' => $ps_options['timeout'],
		'exclude_featured'	=> $ps_options['exclude_featured'],
		'autoplay' => $ps_options['autoplay'],
		'duration'	=>	'',
		'audio'	=>	'',
		'showinfo' => $ps_options['showinfo'],
		'showplay' => $ps_options['showplay'],
		'pagerpos' => $ps_options['pagerpos'],
		'pagerstyle' => $ps_options['pagerstyle'],
		'togglethumbs' => $ps_options['togglethumbs'],
		'navpos' => $ps_options['navpos'],
		'random' => $ps_options['random'],
		'carousel' => $ps_options['carousel'],
		'carouselsize' => $ps_options['carouselsize'],
		'navstyle' => $ps_options['navstyle'],
		'showcaps' => $ps_options['showcaps'],
		'showtitles' => $ps_options['showtitles'],
		'showdesc' => $ps_options['showdesc'],
		'click' =>	$ps_options['click'],
		'fluid'	=>	$ps_options['allowfluid'],
		'slideheight' => '',
		'class' =>	'',
		'id' => '',
		'exclude' => '',
		'include' => ''
	), $atts ) );

	//has a custom post id been declared or should we use current page ID?
	if ( ! $id ) { $id = get_the_ID(); }

	//if the exclude_featured attribute is set, get the featured thumb ID and add it to the $exclude string
	if ( $exclude_featured == "true" ) {
		$featured_id = get_post_thumbnail_id( $id );
		if ( ! $include ) { // we don't need an exclude variable if $include is set
			if ( $exclude ) { //if $exclude is set, concatenate it
				$exclude = $exclude . "," . $featured_id;	
			 } else { //$exclude is simply equal to $featured_id 
			 	$exclude = $featured_id;
			 }
		}	
	} 

	//count the attachments
	if ( $include ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$attachments = get_children( array('post_parent' => $id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'include' => $include) );
		
	} elseif ( $exclude ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'exclude' => $exclude) );
	} else {
		$attachments = get_children( array ( 'post_parent' => $id, 'post_type' => 'attachment', 'post_mime_type' => 'image' ) );
	}
	
	global $ps_count;
	$ps_count = count( $attachments );

	// override the per-slide timeout if a full-slideshow duration is set
	if ( $duration ) { $timeout = ( $duration * 1000 ) / $ps_count; }

	// if we don't have enough images to show the carousel, disable it
	if ( $carouselsize >= $ps_count ) { 
		$carousel = "false";
	}
		
	// Navigation
	if ( ! is_feed() && $ps_count > 1 ) { //no need for navigation if there's only one slide
		switch ( $navstyle ) { //this is a switch because we'll be adding more nav options in the future
			
			default :
					$ps_nav = '<div id="slideshow-nav'.$i.'" class="slideshow-nav';
						
					if ( $navstyle == "graphical" ) {
						$ps_nav .= " graphical";
					}

					$ps_nav .= '">';
		
					if ( $showplay == "true" ) {
						$ps_nav .='<a class="pause" style="display:none" href="javascript:void(0);">Pause</a><a class="play" href="javascript:void(0);">Play</a><a class="restart" style="display:none" href="javascript: void(0);">Play</a>';
					} 

					$ps_nav .= '<a class="slideshow-prev" href="javascript: void(0);">Prev</a><span class="sep">|</span><a class="slideshow-next" href="javascript: void(0);">Next</a>';
					
					if ( $showinfo == "true" ) {
						$ps_nav .= '<span class="slideshow-info' . $i . ' slideshow-info"></span>';
					}	

					
					if ( $pagerstyle == "thumbs" && $pagerpos != "disabled" && $togglethumbs == "true" ) {
						$ps_nav .= '<span class="thumb-toggles"><a class="show" href="javascript:void(0);">show thumbs</a><a class="hide" href="javascript:void(0);">hide thumbs</a></span>';
						}
		
					$ps_nav .= '</div><!-- .slideshow-nav-->
					';
			break;
		}
	} 

	//Pager
	
	//Do we show thumbnails?
	if ( ! is_feed() &&  $ps_count > 1 ) {
		switch ( $pagerstyle ) {
			case "numbers": 
				$ps_pager = '<div id="pager' . $i . '" class="pager clearfix"><div class="numbers"></div><!--.numbers--></div><!--#pager-->';	
				break;
			
			case "bullets":
				$ps_pager = '<div id="pager' . $i . '" class="pager clearfix"><div class="bullets">'; 
					for ($k = 1; $k <= $ps_count; $k++) {
						$ps_pager .= '<a href="javascript: void(0);" class="bullet"></a>';
					}
				$ps_pager .= '	
				</div><!--.bullets--></div><!--#pager-->';	
				break;
				
			case "thumbs":

				$carouselwidth = ( $ps_options['thumbsize'] + $ps_options['thumbnailmargin'] ) * $carouselsize; //add the margin to the original thumbnail size and multiply it by the number of images in the row to find out how long the row width should be

				if ( $ps_options['carousel'] == "true" ) { // subtract the padding for the last item if we're using the carousel so everything fits

				$carouselwidth = $carouselwidth - $ps_options['thumbnailmargin']; }

				$ps_pager = '<div class="pscarousel';
					if ( $togglethumbs == "true" ) {
						$ps_pager .= ' toggle-thumbs';
					}						

					if ( $carousel != "true" ) {
						$ps_pager .= ' thumbnails';
					}
					
					if ( $carouselsize  >= $ps_count ) { //resize the individual thumbnail container if there aren't enough images to fill it
						$thisthumbsize = ( $ps_options['thumbsize'] + $ps_options['thumbnailmargin'] ) * $ps_count; //add the margin to the original thumbnail size, multiply it by the total number of images in the row
						$ps_pager .= '" style="width: '  . $thisthumbsize . 'px;';
					} else { //otherwise use the default container size
						$thisthumbsize = ( $ps_options['thumbsize'] + $ps_options['thumbnailmargin'] ) * $carouselsize; //add the margin to the original thumbnail size, multiply it by the total number of images in the row
						$ps_pager .= '" style="width: '  . $thisthumbsize . 'px;';
					}					
			
				$ps_pager .='">';
				if ( $carousel == "true" ) {	
					
					//this is the hidden nav for the carousel
					$pstabs = ceil( $ps_count/$carouselsize );
					$ps_pager .= '<ul id="carouselnav' . $i . '" class="navi">';
					for ($t = 1; $t <= $pstabs; $t++) {
						$ps_pager .= '<li><a href="javascript: void(0);">'.$t.'</a></li>';
					}
					$ps_pager .= '</ul>';	
				
					$ps_pager .= '<a class="prev browse left">left</a><div id= "scrollable' . $i . '" class="scrollable"'; 
					$ps_pager .= ' style="width: '  . $thisthumbsize . 'px;">';
				}
								   
				$ps_pager .= '<div id="pager' . $i . '" class="pager items clearfix">';
		
				if ( $include ) {
					$include = preg_replace( '/[^0-9,]+/', '', $include );
					$attachments = get_posts( array(
					'order'          => 'ASC',
					'orderby' 		 => 'menu_order ID',
					'post_type'      => 'attachment',
					'post_parent'    => $id,
					'post_mime_type' => 'image',
					'post_status'    => null,
					'numberposts'    => -1,
					'size'			 => 'ps-thumb',
					'include'		 => $include) );
				} elseif ( $exclude ) {
					$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
					$attachments = get_posts( array(
					'order'          => 'ASC',
					'orderby' 		 => 'menu_order ID',
					'post_type'      => 'attachment',
					'post_parent'    => $id,
					'post_mime_type' => 'image',
					'post_status'    => null,
					'numberposts'    => -1,
					'size'			 => 'ps-thumb',
					'exclude'		 => $exclude) );
				} else {
					$attachments = get_posts( array(
					'order'          => 'ASC',
					'orderby' 		 => 'menu_order ID',
					'post_type'      => 'attachment',
					'post_parent'    => $id,
					'post_mime_type' => 'image',
					'post_status'    => null,
					'numberposts'    => -1,
					'size'			 => 'ps-thumb') );
				}
							
				if ($attachments) {
					$j = 1;
					$ps_pager .='<div>';
					foreach ( $attachments as $attachment ) {
					
					$ps_pager .= '<img src="' . plugins_url( 'timthumb.php' , __FILE__ ) . "?w=$ps_options[thumbsize]&amp;h=$ps_options[thumbsize]&amp;zc=1&amp;q=80&amp;src=" . get_image_path( $attachment->ID ) . '" alt="' . $attachment->post_title . ' thumbnail" />';		

					if ( $j % $carouselsize == 0 && $j != $ps_count ) { $ps_pager .='</div>
					<div>'; }
					$j++;
					}
				}
				
				$ps_pager .= '</div>';
									
				$ps_pager .= '</div>';
					
				if ( $carousel == "true" ) {
				
				$ps_pager .= '</div><a class="next browse right">right</a>';}
				
				$ps_pager .= '</div><!--.pscarousel-->';
				break;	
					
		
			default :
				$ps_pager .= '<ul id="pager'.$i.'" class="pager"></ul>';
				break;
		}	
		
	}	
		
	if ( ! is_feed() ) { 

		if ( $audio ) { $psaudio = "true"; } else { $psaudio = "false"; } //We'll get an error if it's blank.
		
		$slideshow = 
		'<script type="text/javascript">/* <![CDATA[ */ psTimeout['.$i.']='.$timeout.';psAudio['.$i.']='.$psaudio.';psAutoplay['.$i.']='.$autoplay.';psDelay['.$i.']='.$delay.';psTrans['.$i.']=\''.$trans.'\';psNoWrap['.$i.']='.$nowrap.';psCarouselSize['.$i.']='.$carouselsize.';psSpeed['.$i.']='.$speed.';psRandom['.$i.']='.$random.';psPagerStyle['.$i.']=\''.$pagerstyle.'\';/* ]]> */</script>'; 
	
		//wrap the whole thing in a div for styling		
		$slideshow .= '<div id="slideshow-wrapper'.$i.'" class="slideshow-wrapper clearfix';
		
		if ( $centered == "true" ) { 
			$slideshow .= " centered"; 
		}

		if ( $fluid == "true" ) { 
			$slideshow .= " fluid"; 
		}

		if ( $trans == 'fade') {
			$slideshow .= ' fade';
		}		

		if ( $ps_options['showloader'] == "true" ) { 
			$slideshow .= " showloader"; 
		}
		
		if ( $class ) { 
			$slideshow .= " $class"; 
		}
		
		$slideshow .='"><a id="psprev'.$i.'" href="javascript: void(0);"></a><a id="psnext'.$i.'" href="javascript: void(0);"></a>
		';	
		
		if ( $navpos == "top" ) { 
			$slideshow .= $ps_nav;
		}
	
		if ( $pagerpos == "top" ) { 
			$slideshow .= $ps_pager;
		}
		
	} //end ! is_feed()

	$slideshow .= '<div id="portfolio-slideshow'.$i.'" class="portfolio-slideshow"';
	
	/* An inline style if they need to set a height for the main slideshow container */	
	
	if ( $slideheight ) {
		$slideshow .= ' style="min-height:' . $slideheight . 'px !important;"';
	}
	
	$slideshow .='>
	';

	$slideID = 0;
	
	if ( $include ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$attachments = get_posts( array( 'order'          => 'ASC',
		'orderby' 		 => 'menu_order ID',
		'post_type'      => 'attachment',
		'post_parent'    => $id,
		'post_mime_type' => 'image',
		'post_status'    => null,
		'numberposts'    => -1,
		'size'			 => $size,
		'include'		 => $include) );
		
	} elseif ( $exclude ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_posts( array( 'order'          => 'ASC',
		'orderby' 		 => 'menu_order ID',
		'post_type'      => 'attachment',
		'post_parent'    => $id,
		'post_mime_type' => 'image',
		'post_status'    => null,
		'numberposts'    => -1,
		'size'			 => $size,
		'exclude'		 => $exclude) );
	} else {
		$attachments = get_posts( array( 'order'          => 'ASC',
		'orderby' 		 => 'menu_order ID',
		'post_type'      => 'attachment',
		'post_parent'    => $id,
		'post_mime_type' => 'image',
		'post_status'    => null,
		'numberposts'    => -1,
		'size'			 => $size) );
	}

	if ( $attachments ) { //if attachments are found, run the slideshow
	
		//begin the slideshow loop
		foreach ( $attachments as $attachment ) {
			
			$alttext = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );

			if ( ! $alttext ) {
				$alttext = $attachment->post_title;
			}
				
			$slideshow .= '<div class="';
			if ( $slideID != "0" ) { $slideshow .= "not-first "; }
			$slideshow .= 'slideshow-next slideshow-content'; 
			$slideshow .= '">
			';
				 
			switch ( $click ) {

				case "lightbox" :	
					$imagelink = wp_get_attachment_url( $attachment->ID ) . '" class="fancybox" rel="group-'.$id;

					if ( $showcaps == "true" ) { 
						$caption = $attachment->post_excerpt;
						$imagelink = $imagelink . '" title="' . $caption . '"'; 
					}	

					break;

				case "openurl" :
					$imagelink = get_post_meta( $attachment->ID, '_ps_image_link', true );
					if ( $imagelink ) { $imagelink = $imagelink . '" target="' . $ps_options['click_target']; }
					break;

				default :
					$imagelink = 'javascript: void(0);" class="slideshow-next';
					break;	
			}		
			
			if ( $imagelink && $nowrap == "true" && $ps_count - 1 != $slideID || $imagelink && $nowrap == "0" || $imagelink && $nowrap == "true" && $click == "lightbox" ) { $slideshow .= '<a href="'.$imagelink.'">'; }
			
/*
 * This is the part of the loop that actually returns the images
 */
			
			$ps_placeholder = PORTFOLIO_SLIDESHOW_URL . '/img/tiny.png';
					 
			if ( $width || $height ) { 
			
			/* Determine if we've got an explicit height or width in the shortcode */
			
				if ( ! $width ) { $width = 0; };
				if ( ! $height ) { $height = 0; };
				
				$slidesrc = plugins_url( 'timthumb.php' , __FILE__ ) . "?w=$width&amp;h=$height&amp;zc=3&amp;q=95&amp;src=" . get_image_path( $attachment->ID, "full" );
									
				$slideshow .= '<img class="psp-active" data-img="' . $slidesrc . '"'; 
				
				if ( $slideID < 1 ) { 
					$slideshow .= ' src="' . $slidesrc . '"'; 
				} else {
					$slideshow .= ' src="' . $ps_placeholder . '"';
				}
				//include the src attribute for the first slide only
				
				
				$slideshow .= ' alt="' . $alttext . '" /><noscript><img src="' . $slidesrc . '" alt="' . $alttext . '" /></noscript>';	
					
			} elseif ( $size == "custom" ) { 
			/* If we're using a defined custom size */

				$slidesrc = plugins_url( 'timthumb.php' , __FILE__ ) . "?w=$ps_options[customwidth]&amp;h=$ps_options[customheight]&amp;zc=3&amp;q=95&amp;src=" . get_image_path( $attachment->ID, "full" );
																		
				$slideshow .= '<img class="psp-active" data-img="' . $slidesrc . '"';
				
				if ( $slideID < 1 ) { 
					$slideshow .= ' src="' . $slidesrc . '"'; 
				} else {
					$slideshow .= ' src="' . $ps_placeholder . '"';
				}
				//include the src attribute for the first slide only
				
				 $slideshow .= ' alt="' . $alttext . '"/><noscript><img src="' . $slidesrc . '" alt="' . $alttext . '" /></noscript>';
					
			} else { /* Otherwise it's just one of the WP defaults */
					
				$slideshow .= '<img class="psp-active" data-img="' . get_image_path( $attachment->ID, $size ) . '"'; 
				
				if ( $slideID < 1 ) { 
					$slideshow .= ' src="' . get_image_path( $attachment->ID, $size ) . '"';
				} else {
					$slideshow .= ' src="' . $ps_placeholder . '"';
				}
				//include the src attribute for the first slide only
				
				$slideshow .= ' height="' . get_image_path( $attachment->ID, $size, "height" ) . '" width="' . get_image_path( $attachment->ID, $size, "width" ) . '" alt="' . $alttext . '" /><noscript><img src="' . get_image_path( $attachment->ID, $size ) . '" height="' . get_image_path( $attachment->ID, $size, "height" ) . '" width="' . get_image_path( $attachment->ID, $size, "width" ) . '" alt="' . $alttext . '" /></noscript>';
									
			} 

/*
 * That's it for the images
 */			
			
			if ( $imagelink && $nowrap == "true" && $ps_count - 1 != $slideID || $imagelink && $nowrap == "0" || $imagelink && $nowrap == "true" && $click == "lightbox" ) { 
				$slideshow .= "</a>";
			}		

			if ( $showtitles == "true" || $showcaps == "true" || $showdesc == "true" ) {
				$slideshow .= '<div class="slideshow-meta">';
			}

			//if titles option is selected
			if ( $showtitles == "true" ) {
				$title = $attachment->post_title;
				if ( $title ) { 
					$slideshow .= '<p class="slideshow-title">'.$title.'</p>'; 
				} 
			}
			
			//if captions option is selected
			if ( $showcaps == "true" ) {			
				$caption = $attachment->post_excerpt;
				if ( $caption ) { 
					$slideshow .= '<p class="slideshow-caption">'.$caption.'</p>'; 
				}
			}
			
			//if descriptions option is selected
			if ( $showdesc == "true" ) {			
				$description = $attachment->post_content;
				if ( $description ) { 
					$slideshow .= '<p class="slideshow-description">'. wpautop( $description ) .'</p>'; 
				}
			}
			if ( $showtitles == "true" || $showcaps == "true" || $showdesc == "true" ) {
				$slideshow .= '</div>';
			}

			$slideshow .= '</div>
			';
			
			$slideID++;
					
		}  // end slideshow loop
	} // end if ( $attachments)

	$slideshow .= "</div><!--#portfolio-slideshow-->";
	
	if ( $navpos == "bottom" ) { 
		$slideshow .= $ps_nav;
	}
	
	if ( $pagerpos == "bottom" ) { 
		$slideshow .= $ps_pager;
	}
	
	$slideshow .='</div><!--#slideshow-wrapper-->';

	$i++;

	if ( $audio ) {
		$slideshow .= '<div id="haiku-text-player'.$i.'" class="haiku-text-player"></div><div style="display:none">';
		$slideshow .= do_shortcode("[haiku url=$audio graphical=false noplayerdiv=true]") . "</div>";		
	}

	return $slideshow;	//that's the slideshow
	
	
} //ends the portfolio_shortcode function ?>
