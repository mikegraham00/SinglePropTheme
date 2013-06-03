<?php

// create the shortcode
add_shortcode( 'portfolio_slideshow', 'portfolio_slideshow_pro_shortcode' );

// define the shortcode function
function portfolio_slideshow_pro_shortcode( $atts ) {

	global $ps_options;

	extract( shortcode_atts( array(
		'size' => $ps_options['size'],
		'nowrap' => '',  //kept for compatibility with previous versions of the plugin
		'loop' => $ps_options['loop'],
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
		'audio_loop' => false,
		'showinfo' => $ps_options['showinfo'],
		'showplay' => $ps_options['showplay'],
		'pagerpos' => $ps_options['pagerpos'],
		'metapos' => $ps_options['metapos'],
		'metacontent' => '',
		'slidelinks' => '',
		'post_titles' => '',
		'pagerstyle' => $ps_options['pagerstyle'],
		'togglethumbs' => $ps_options['togglethumbs'],
		'togglestate' => $ps_options['togglestate'],
		'thumbnailsize' => $ps_options['thumbsize'],
		'thumbnailmargin' => $ps_options['thumbnailmargin'],
		'proportionalthumbs' => $ps_options['proportionalthumbs'],
		'video' => false,
		'navpos' => $ps_options['navpos'],
		'fancygrid' => $ps_options['fancygrid'],
		'fullscreen' => $ps_options['fullscreen'],
		'random' => $ps_options['random'],
		'carousel' => '', //kept for compatibility with previous versions of the plugin
		'carouselsize' => $ps_options['carouselsize'],
		'navstyle' => $ps_options['navstyle'],
		'showcaps' => $ps_options['showcaps'],
		'showtitles' => $ps_options['showtitles'],
		'showdesc' => $ps_options['showdesc'],
		'click' => $ps_options['click'],
		'target' => $ps_options['target'],
		'fluid'	=>	$ps_options['allowfluid'],
		'crop'	=> $ps_options['crop'],
		'slideheight' => '',
		'pagerwidth' => '',
		'class' =>	'',
		'id' => '',
		'limit' => '-1',
		'posttype' => '',
		'order' => 'DESC',
		'orderby' => 'date',
		'taxname' => '',
		'tax' => '',
		'category' => '',
		'tag' => '',
		'exclude' => '',
		'include' => '',
		'ids' => ''
	), $atts ) );
	
	/* Has a custom post id been declared or should we use current page ID? */
	
	if ( ! $id ) $id = get_the_ID();
	$slideKey = rand(1,999999);

	if ( $include ) $include = explode(',', $include);
	if ( $exclude ) $exclude = explode(',', $exclude);
	if ( $ids ) $include = explode(',', $ids);

	if ( !$include ) $slidemeta = get_post_meta( $id, '_portfolio_slideshow', true );

	if ( $slidemeta ) {
		foreach ( $slidemeta as $item ) {
			if ( !empty( $item['image'] ) ) {
				$include[] = $item['image'];
			}

			if ( !empty( $item['caption'] ) ) {
				$metacontent[] = $item['caption'];
			} else {
				$metacontent[] = '';
			}

			if ( !empty( $item['url'] ) ) {
				$slidelinks[] = $item['url'];
			} else {
				$slidelinks[] = '';
			}
		}
	}

	if ( ps_plugin_is_active('portfolio-slideshow-pro-image-taxonomies') && !$posttype ) {
		if ( $category || $tag ) {
			global $include, $post_titles, $metacontent, $slidelinks;
			psp_taxonomy_query( $category, $tag, $limit );
			wp_reset_postdata();
		}
	} 

	if ( $posttype ) {
		global $include, $post_titles, $slidelinks;
		psp_post_query( $posttype, $taxname, $tax, $category, $tag, $limit, $order, $orderby );
		wp_reset_postdata();
	} 

	if ( $metacontent && is_string($metacontent) ) $metacontent = explode('@@@', $metacontent);
	if ( $slidelinks && is_string($slidelinks) ) $slidelinks = explode('@@@', $slidelinks);

	/* If the exclude_featured attribute is set, get the featured thumb ID and add it to the $exclude string */
	if ( $exclude_featured == "true" && current_theme_supports( 'post-thumbnails' ) ) {
		$featured_id = get_post_thumbnail_id( $id );
		$exclude[] = $featured_id;	 
	} 
	
	/* Now we exclude any images that have been marked as "exclude" in the Gallery tab */
	$attachments = get_children( array('post_parent' => $id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'meta_key' => '_ps_exclude_checkbox', 'meta_value' => 'exclude') );
		
	if ( $attachments ) { 
		foreach ( $attachments as $attachment ) {
			$exclude[] = $attachment->ID;
		}
	}
	
	/* Turn thumbnail size into array if necessary */
	$thumbnailarray = strpos($thumbnailsize, ',');
	if ( $thumbnailarray ) {
		$thumbnailsize = explode(',', $thumbnailsize);
	} else {
		$newsize[] = $thumbnailsize;
		$newsize[] = $thumbnailsize;
		$thumbnailsize = $newsize;
	}

	/* Custom orderby if random == true */
	if ( $random == "true" ) {
		$orderby="rand";
	} else {
		$orderby="menu_order ID";
	}
	
	if ( $include ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$attachments = array(
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'post__in' 		 => $include,
			'posts_per_page' => -1
		);
	} elseif ( $exclude ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = array(
			'order'          => 'ASC',
			'orderby' 		 => $orderby,
			'post_type'      => 'attachment',
			'post_parent'    => $id,
			'post_mime_type' => 'image',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'post__not_in'	 => $exclude 
		);
	} else {
		$attachments = array(
			'order'          => 'ASC',
			'orderby' 		 => $orderby,
			'post_type'      => 'attachment',
			'post_parent'    => $id,
			'post_mime_type' => 'image',
			'post_status'    => 'any',
            'posts_per_page' => -1
		);
	}
	
	/* The main query */

	$psp_query = new WP_Query($attachments);

	if ( $include )	{
		// set the menu_order
		psp_set_include_order($psp_query, $include);
			
		// and actually sort the posts in our query
		usort($psp_query->posts, 'psp_menu_order_sort'); 	
	}

	global $ps_count;
	$ps_count = $psp_query->post_count;

	/*
	 * Overrides
	 */
	
	if ( $audio ) { $psaudio = "true"; } else { $psaudio = "false"; } 
		
	/* If carousel is true, change the pager option (legacy support) */
	if ( $carousel == "true" ) $pagerstyle = "carousel";
	
	/* If a click target is set, map that to the proper options */
	if ( $target == "current" || $target == "" || $target == "_self" ) { 
		$target = "_self"; 
	} elseif ( $target == "parent" ) {
		$target = "_parent";
	} else { 
		$target = "_blank"; 
	}
	
	/* If fancygrid is active, force the correct options */
	if ( $fancygrid == "true" ) {
		$pagerpos = "top";
		$pagerstyle = "thumbs";		
		$trans = "fade";
		$speed = 100;	
		$togglethumbs = "true";
	}
	
	if ( $crop != "true" ) { $crop = false; }
	
	/* Preserve the nowrap option if people are still using it */
	if ( $nowrap == "false" || $loop == "true" ) { $loop = "true"; } else { $loop = "false"; }
		
	/* Override the per-slide timeout if a full-slideshow duration is set */
	if ( $duration ) { $timeout = ( $duration * 1000 ) / $ps_count; }
			
	
	if ( ! is_feed() && $ps_count != 0 ) { 
		$slideshow = 
		'<script type="text/javascript">/* <![CDATA[ */ psTimeout['.$slideKey.']='.$timeout.';psAudio['.$slideKey.']='.$psaudio.';psAutoplay['.$slideKey.']='.$autoplay.';psDelay['.$slideKey.']='.$delay.';psTrans['.$slideKey.']=\''.$trans.'\';psLoop['.$slideKey.']='.$loop.';psCarouselSize['.$slideKey.']='.$carouselsize.';psSpeed['.$slideKey.']='.$speed.';psPagerStyle['.$slideKey.']=\''.$pagerstyle.'\';psClickOpens['.$slideKey.']=\''.$click.'\';/* ]]> */</script>'; 

		//wrap the whole thing in a div for styling		
		$slideshow .= '<div id="slideshow-wrapper'.$slideKey.'" class="slideshow-wrapper clearfix';
		
		if ( $centered == "true" ) { 
			$slideshow .= " centered"; 
		}
		
		if ( $fancygrid == "true" ) { 
			$slideshow .= " fancygrid"; 
		}
		if ( $click == "fullscreen" || $fullscreen == "true" ) {
			$slideshow .= " photoswipe";
		}
 		
		if ( $togglestate == "open" ) { 
			$slideshow .= " toggle-open"; 
		} else {
			$slideshow .= " toggle-closed";
		}
		
		if ( $fluid == "true" ) { 
			$slideshow .= " fluid"; 
		}
		
		if ( $class ) { 
			$slideshow .= " $class"; 
		}
		
		$slideshow .='"><a id="psprev'.$slideKey.'" href="javascript: void(0);"></a><a id="psnext'.$slideKey.'" href="javascript: void(0);"></a>
		';	

		if ( $navpos == 'top' ) { 
			$slideshow .= psp_slideshow_navigation( $slideKey, $ps_count, $navstyle, $pagerstyle, $pagerpos, $togglethumbs, $fullscreen, $showinfo, $showplay );
		}
	
		if ( $pagerpos == 'top' ) { 
			$slideshow .= psp_slideshow_pager( $slideKey, $ps_count, $psp_query, $pagerstyle, $carouselsize, $togglethumbs, $proportionalthumbs, $centered, $pagerwidth, $thumbnailmargin, $thumbnailsize );
		}

		if ( $metapos == 'top' ) {
			$slideshow .= psp_slideshow_meta( $showtitles, $showcaps, $showdesc, $post_titles, $metacontent, $slideKey, $psp_query, $centered );
		}
		
		$slideshow .= '<div id="portfolio-slideshow'.$slideKey.'" class="portfolio-slideshow';
			
		if ( $ps_options['showloader'] == "true" ) { 
			$slideshow .= " showloader"; 
		}

		$slideshow .= '"';
		
		/* An inline style if they need to set a height for the main slideshow container */	
		
		if ( $slideheight ) {
			$slideshow .= ' style="min-height:' . $slideheight . 'px !important;"';
		}
		
		$slideshow .='>
		';

} //end ! is_feed()
	
	$slideID = 0;
	//begin the slideshow loop
	while( $psp_query->have_posts() ) : $psp_query->the_post();
		global $post;
		$custom_fields = get_post_custom($id);	
		if ( $slidelinks ) {
			if(isset($slidelinks[$slideID])) :
				$imagelink = $slidelinks[$slideID];
			endif;
		} else {
			$imagelink = get_post_meta( $post->ID, '_ps_image_link', true );
		}
		
		if ( $width && $height ) { 
			$videowidth = $width;
			$videoheight = $height;
		} else {
			$videowidth = '640';
			$videoheight = '360';
		}
		
		if (preg_match('/ww=[0-9]+/i', $imagelink, $ww)) {
			$videowidth = str_replace('ww=','',$ww[0]); 
		}
		
		if (preg_match('/wh=[0-9]+/i', $imagelink, $wh)) {
			$videoheight = str_replace('wh=','',$wh[0]); 
		}
		
		if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $imagelink, $match)) {
		
		$video = true;
			 
		$videoembed = '<div class="ps-video-wrapper"><iframe class="ps-video" width="' . $videowidth . '" height="' . $videoheight . '" data-src="http://www.youtube.com/embed/' . $match[1] . '" frameborder="0" allowfullscreen></iframe></div>';
				}
		
		if (preg_match('%(?:vimeo\.com/(\d+))%i', $imagelink, $match)) {
		
			$video = true;
			$videoembed = '<div class="ps-video-wrapper"><iframe class="ps-video" data-src="http://player.vimeo.com/video/' . $match[1] . '" width="' . $videowidth . '" height="' . $videoheight . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>';	
		
		} 
			
		$alttext = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );

		if ( ! $alttext ) {
			$alttext = $post->post_title;
		}
				
		$slideshow .= '<div class="';
		if ( $slideID != "0" ) { $slideshow .= "not-first "; }
		if ( $loop != "true" && $slideKey == ( $ps_count - 1 ) && $click == "advance" || $click == "none" ) { $slideshow .= "psp-no-click "; }
		$slideshow .= 'slideshow-content'; 
		$slideshow .= '">
		';
		
		if ( $ps_count == 1 && $click == "advance" ) $click = "none";
		switch ( $click ) {
		
			case "lightbox" :	
				$image = psp_resize( $post->ID, '', 1600, 1600, false );
				$imagelink = $image['url'] . '" class="fancybox" rel="group-'.$slideKey;

				if ( $showcaps == "true" ) { 
					$caption = $post->post_excerpt;
					$imagelink = $imagelink . '" title="' . $caption . ''; 
				}	

				break;

			case "openurl" :
				
				if ( is_array( $slidelinks ) ) {
					$imagelink = $slidelinks[$slideID];
				} else {
					$imagelink = get_post_meta( $post->ID, '_ps_image_link', true );
				}
														
				if ( $imagelink ) { $imagelink = $imagelink . '" target="' . $target . '" class="' . $target; } else {
					$imagelink = 'javascript: void(0);" class="slideshow-next' . $target;
				}
				
				break;
				
			case "postlink":
				
				if ( is_array( $slidelinks ) ) {
					$imagelink = $slidelinks[$slideID];
				} else {
					$imagelink = 'javascript: void(0);" class="slideshow-next';
				}	
				break;

			case "attachment" :

				$imagelink = get_attachment_link();

				break;
				
			case "fullscreen" :

				$photoswipe_imgsrc = wp_get_attachment_image_src( $post->ID, 'full' );
				$imagelink = $photoswipe_imgsrc[0] . '" class="ps-photoswipe';
			
				break;
			
			case "none" :
			
				$imagelink = 'javascript: void(0);" class="psp-no-click';
				break;	
			
			default :
				$imagelink = 'javascript: void(0);" class="slideshow-next';
				break;	
		}	

		if ( $video != true ) {
			$slideshow .= '<a href="' . $imagelink . '">';
		}

/*
* This is the part of the loop that actually returns the images
*/
			if ( is_feed() ) { /* No slideshow if we're in the feed */
			 	$feedimg = wp_get_attachment_image_src( $post->ID, 'large' );		
				$slideshow .= '<img style="margin-bottom:15px" src="' . $feedimg[0] . '"/><br />';
				
			} else { /* Slideshow output */
			
 			$ps_placeholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
 								 
			if ( $width || $height ) { 
			
			/* Determine if we've got an explicit height or width in the shortcode */
							
				if ( ! $width ) { $width = 9999; $crop = false; }
				if ( ! $height ) { $height = 9999; $crop = false; }
					
				$image = psp_resize( $post->ID, '', $width, $height, $crop );
					
			} elseif ( $size == "custom" ) { 

				/* If we're using a defined custom size */
				
				$image = psp_resize( $post->ID, '', $ps_options['customwidth'], $ps_options['customheight'], $crop );
										
			} else { 
				
				/* Otherwise it's just one of the WP defaults, so create an array to match our other formats  */
								
				$imgsrc = wp_get_attachment_image_src( $post->ID, $size );
				
				$image = array(
				    "url" => $imgsrc[0],
				    "width" => $imgsrc[1],
				    "height" => $imgsrc[2]
				);

			}		
							
			if ( $video == true ) {
							
				$slideshow .= $videoembed;
			
			} else { // if it's not a video, return the usual slideshow 
			
				$slideshow .= '<img class="psp-active"';
				
				if ( $fluid != "true" ) {
					$slideshow .= ' height="' . $image['height'] . '" width="' . $image['width'] . '"';
				 }
				
				$slideshow .= ' data-img="' . $image['url'] . '"'; 
				
				if ( $slideID < 2 ) { 
					$slideshow .= ' src="' . $image['url'] . '"';
				} else {
					$slideshow .= ' src="' . $ps_placeholder . '"';
				}
				
				//include the real src attribute for the first two slides only
									
				$slideshow .= ' alt="' . $alttext . '" /><noscript><img src="' . $image['url'] . '" alt="' . $alttext . '" /></noscript>';
			
			} /* End $video == true */
								 						
			} /* End is_feed determination for slideshow */

		/*
		* That's it for the images
		*/			
		
		if ( $video != true ) {		
			$slideshow .= "</a>";
		}
		
		if ( $fullscreen == "true" && $click != "fullscreen") {
			$photoswipe_imgsrc = wp_get_attachment_image_src( $post->ID, 'full' );
			$imagelink = $photoswipe_imgsrc[0];
			$slideshow .= '<a href="' . $imagelink . '" class="ps-photoswipe" style="display:none"></a>';
		}		
		
		$slideshow .= '</div><!-- .slideshow-content -->
		';
	
		$slideID++;
		
		$video = false;			
	endwhile;
	wp_reset_postdata();
	// end slideshow loop

	if ( ! is_feed() && $ps_count != 0 ) {
		
		$slideshow .= "</div><!--#portfolio-slideshow-->";
		
		if ( $metapos == 'middle' ) {
			$slideshow .= psp_slideshow_meta( $showtitles, $showcaps, $showdesc, $post_titles, $metacontent, $slideKey, $psp_query, $centered );
		}

		if ( $navpos == "middle" ) { 
			$slideshow .= psp_slideshow_navigation( $slideKey, $ps_count, $navstyle, $pagerstyle, $pagerpos, $togglethumbs, $fullscreen, $showinfo, $showplay );
		}
		
		if ( $pagerpos == "middle" ) { 
			$slideshow .= psp_slideshow_pager( $slideKey, $ps_count, $psp_query, $pagerstyle, $carouselsize, $togglethumbs, $proportionalthumbs, $centered, $pagerwidth, $thumbnailmargin, $thumbnailsize );
		}

		if ( $metapos == 'bottom' ) {
			$slideshow .= psp_slideshow_meta( $showtitles, $showcaps, $showdesc, $post_titles, $metacontent, $slideKey, $psp_query, $centered );
		}

		if ( $navpos == "bottom" ) { 
			$slideshow .= psp_slideshow_navigation( $slideKey, $ps_count, $navstyle, $pagerstyle, $pagerpos, $togglethumbs, $fullscreen, $showinfo, $showplay );
		}
		
		if ( $pagerpos == "bottom" ) { 
			$slideshow .= psp_slideshow_pager( $slideKey, $ps_count, $psp_query, $pagerstyle, $carouselsize, $togglethumbs, $proportionalthumbs, $centered, $pagerwidth, $thumbnailmargin, $thumbnailsize );
		}
		
		$slideshow .='</div><!--#slideshow-wrapper-->';

	} /* End ! is_feed() */
	
	if ( $audio ) {
		if(!empty($audio_loop) && $audio_loop == 'true') {
			$slideshow .= do_shortcode("[haiku url=$audio class='portfolio_slideshow_audio' defaultpath=disabled graphical=false loop=true]");
		} else {
			$slideshow .= do_shortcode("[haiku url=$audio class='portfolio_slideshow_audio'  defaultpath=disabled graphical=false loop=false]");
		}	
	}

	if ( isset( $slideshow ) ) return $slideshow;	//that's the slideshow
	
	
} //ends the portfolio_shortcode function ?>