<?php 


/* 
 *
 * Post type query
 *
 *
 */

function psp_post_query( $posttype, $taxname, $tax, $category, $tag, $limit, $order, $orderby ) {

	global $include, $post_titles, $metacontent, $slidelinks;

	$post_titles = ''; $include = ''; $post_titles = ''; $slidelinks = '';

	if ( $category ) {
		$taxname = "category_name";
		$tax = $category;
	}

	if ( $tag ) {
		$taxname = "tag_slug__in";
		$tax = $tag;
	}

	$attachments = array(
		'post_type' => $posttype,
		'order' => $order,
		'orderby' => $orderby,
		'post_status'    => 'publish',
		'posts_per_page' => $limit
	);

	if ( $tax && $taxname ) {
		$attachments[$taxname] = $tax;
	}

	$psp_posts = new WP_Query( $attachments );
	while( $psp_posts->have_posts() ) : $psp_posts->the_post();
		$id = get_the_ID();
		if ( has_post_thumbnail( $id ) ) { 
			$thumbnailID = get_post_thumbnail_id( $item ); 
			$include[] = $thumbnailID;
			$post_titles[] = get_the_title( $id ); 
			$slidelinks[] = get_permalink( $id );
		}
	endwhile;
}


/*
 * 
 * Slideshow Meta Block
 * 
 */

function psp_slideshow_meta( $showtitles, $showcaps, $showdesc, $post_titles, $metacontent, $slideKey, $psp_query, $centered ) {
	
	global $post;
	$slideID = 0;

	$slideshow_meta = '';

	if ( $metacontent || $showtitles == "true" || $showcaps == "true" || $showdesc == "true" ) {
		$slideshow_meta .= '<div id="slideshow-meta' . $slideKey . '" class="slideshow-meta';

		if ( $centered == "true" ) { 
			$slideshow_meta .= " centered"; 
		}

		$slideshow_meta .= '">';
	}

	while( $psp_query->have_posts() ) : $psp_query->the_post();

		$slideshow_meta .= '<div class="meta-content';

		if ( $slideID != "0" ) { $slideshow_meta .= ' not-first '; }

		$slideshow_meta .= '">';

		//if titles option is selected
		if ( $showtitles == "true" ) {

			if ( is_array( $post_titles ) ) {
				$title = $post_titles[$slideID];
			} else {
				$title = $post->post_title;	
			}
						
			if ( $title ) { 
				$slideshow_meta .= '<p class="slideshow-title">'.$title.'</p>'; 
			} 
		}

		//if captions option is selected
		if ( $showcaps == "true" ) {			
			
			$caption = $post->post_excerpt;
			
			if ( $caption ) { 
				$slideshow_meta .= '<p class="slideshow-caption">'.$caption.'</p>'; 
			}

			if ( $metacontent ) {
				if(isset($metacontent[$slideID])) {
					$meta = $metacontent[$slideID];
				}
				if ( $meta ) { 
					$slideshow_meta .= '<div class="custom-meta">'. wpautop( $meta ) .'</div>'; 
				}
			}
		}

		//if descriptions option is selected
		if ( $showdesc == "true" ) {			
			
			$description = $post->post_content;
			
			if ( $description ) { 
				$slideshow_meta .= '<div class="slideshow-description">'. wpautop( $description ) .'</div>'; 
			}
		}

		$slideshow_meta .= '</div><!-- .meta-content -->';	
		$slideID++;
	endwhile;
	wp_reset_postdata();

	if ( $metacontent || $showtitles == "true" || $showcaps == "true" || $showdesc == "true" ) {
		$slideshow_meta .= '</div>';
	}

	return $slideshow_meta;

}
/* End Slideshow Meta */


/*
 * Navigation Block
 */

function psp_slideshow_navigation( $slideKey, $ps_count, $navstyle, $pagerstyle, $pagerpos, $togglethumbs, $fullscreen, $showinfo, $showplay ) { 

	if ( ! is_feed() && $ps_count > 1 ) { //no need for navigation if there's only one slide
		
		switch ( $navstyle ) { 
			
			case "simple" : 
				$ps_nav .= '<div id="slideshow-nav'.$slideKey.'"><a class="slideshow-prev" href="javascript: void(0);">' . __( 'Prev', 'portfolio-slideshow-pro' ) . '</a><a class="slideshow-next" href="javascript: void(0);">' . __( 'Next', 'portfolio-slideshow-pro' ) . '</a></div>';
			
				break;
			
			default :
				
				$ps_nav = '<div id="slideshow-nav'.$slideKey.'" class="slideshow-nav';
						
				if ( $navstyle == "graphical" ) {
					$ps_nav .= " graphical";
				}

				if ( $navstyle == "text" ) {
					$ps_nav .= " ps-text-nav";
				}

				$ps_nav .= '">';
		
				if ( $showplay == "true" ) {
					$ps_nav .='<a class="pause psp-icon psp-icon-replace" data-icon="&#35;" style="display:none" href="javascript:void(0);">' . __( 'Pause', 'portfolio-slideshow-pro' ) . '</a><a class="play psp-icon psp-icon-replace" data-icon="&#34;" href="javascript:void(0);">' . __( 'Play', 'portfolio-slideshow-pro' ) . '</a><a class="restart psp-icon psp-icon-replace" data-icon="&#34;" style="display:none" href="javascript: void(0);">' . __( 'Play', 'portfolio-slideshow-pro' ) . '</a>';
				} 

				$ps_nav .= '<a class="slideshow-prev psp-icon psp-icon-replace" data-icon="&#37;" href="javascript: void(0);">' . __( 'Prev', 'portfolio-slideshow-pro' ) . '</a><span class="sep">' . __( '|', 'portfolio-slideshow-pro' ) . '</span><a class="slideshow-next psp-icon psp-icon-replace" data-icon="&#38;" href="javascript: void(0);">' . __( 'Next', 'portfolio-slideshow-pro' ) . '</a>';
					
				if ( ( $pagerstyle == "thumbs" || $pagerstyle == "carousel" )  && $pagerpos != "disabled" && $togglethumbs == "true" ) {
					$ps_nav .= '<span class="thumb-toggles"><a class="show psp-toggle psp-icon psp-icon-replace" data-icon="&#33;" href="javascript:void(0);">' . __( 'Show thumbnails', 'portfolio-slideshow-pro' ) . '</a><a class="hide psp-toggle psp-icon psp-icon-replace" data-icon="&#33;" href="javascript:void(0);">' . __( 'Hide thumbnails', 'portfolio-slideshow-pro' ) . '</a></span>';
				}

				if ( $fullscreen == "true" ) {
					$ps_nav .= '<a class="activate-fullscreen psp-icon psp-icon-replace" data-icon="&#39;" href="javascript:void(0);" title="'. __('Activate fullscreen slideshow','portfolio-slideshow-pro').'">'.__('Fullscreen','portfolio-slideshow-pro').'</a>';
				}
					
				if ( $showinfo == "true" ) {
					$ps_nav .= '<span class="slideshow-info' . $slideKey . ' slideshow-info"></span>';
				}	
					
				$ps_nav .= '</div><!-- .slideshow-nav-->
				';
			break;
		}
	} 

	if ( isset( $ps_nav ) ) return $ps_nav;
}

/* End PSP Navigation */

/*
 * Pagers
 */

function psp_slideshow_pager( $slideKey, $ps_count, $psp_query, $pagerstyle, $carouselsize, $togglethumbs, $proportionalthumbs, $centered, $pagerwidth, $thumbnailmargin, $thumbnailsize ) { 

	if ( ! is_feed() &&  $ps_count > 1 ) {

		global $post, $ps_options;
					
		switch ( $pagerstyle ) {
			case "numbers": 
				$ps_pager = '<div id="pager' . $slideKey . '" class="pager clearfix"><div class="numbers">';
						
				foreach ( range( 1, $ps_count ) as $q ) { 
					$ps_pager .= '<a href="javascript:void(\'0\');">' . $q .'</a>'; 
				}
						
				$ps_pager .= '</div><!--.numbers--></div><!--#pager-->';	
				break;
				
			case "titles": 
				$ps_pager = '<div id="pager' . $slideKey . '" class="pager clearfix"><ul class="psp-titles">';
				$s = 0;		
				while( $psp_query->have_posts() ) : $psp_query->the_post();
					
					$title = $post->post_title;
					
					$ps_pager .= '<li><a href="javascript:void(\'0\');">' . $title .'</a></li>'; 
					$s++;
				endwhile;
				wp_reset_postdata();
						
				$ps_pager .= '</li><!--.psp-titles--></div><!--#pager-->';	
				break;
			
			
			case "bullets":
				$ps_pager = '<div id="pager' . $slideKey . '" class="pager clearfix"><div class="bullets">'; 
					for ($k = 1; $k <= $ps_count; $k++) {
						$ps_pager .= '<a href="javascript: void(0);" class="bullet psp-icon psp-icon-replace" data-icon="&#36;"></a>';
					}
				$ps_pager .= '	
				</div><!--.bullets--></div><!--#pager-->';	
				break;
					
				case "carousel":
			
					$carouselwidth = ( $ps_options['carousel_thumbsize'] + $ps_options['carousel_thumbnailmargin'] ) * $carouselsize; /* Add the margin to the original thumbnail size and multiply it by the number of images in the row to find out how long the row width should be */
	
					$carouselwidth = $carouselwidth - $ps_options['carousel_thumbnailmargin']; /* Subtract the width of one margin so everything fits */
					
					$ps_pager = '<div class="pscarousel';
					
					if ( $togglethumbs == "true" ) {
						$ps_pager .= ' toggle-thumbs';
					}				
					
					if ( $carouselsize >= $ps_count ) $ps_pager .= " deactivated";

					$ps_pager .= '" style="width: '  . $carouselwidth . 'px;';			
						  
					$ps_pager .='">';

					/* This is the hidden nav for the carousel */
					$pstabs = ceil( $ps_count/$carouselsize );
					$ps_pager .= '<ul id="carouselnav' . $slideKey . '" class="navi">';
					for ($t = 1; $t <= $pstabs; $t++) {
						$ps_pager .= '<li><a href="javascript: void(0);">'.$t.'</a></li>';
					}
					$ps_pager .= '</ul>';	
				
					$ps_pager .= '<a class="ps-prev browse ps-left hidden psp-icon psp-icon-replace" data-icon="&#37;">left</a><div id= "scrollable' . $slideKey . '" class="scrollable"'; 
					$ps_pager .= ' style="width: '  . $carouselwidth . 'px;">';
									   
					$ps_pager .= '<div id="pager' . $slideKey . '" class="pager items clearfix">';
												
					$j = 1;
					$ps_pager .='<div>';
					while( $psp_query->have_posts() ) : $psp_query->the_post();
						global $post;
				
						$image = psp_resize( $post->ID, '', $ps_options['carousel_thumbsize'], $ps_options['carousel_thumbsize'], true );
												
						$ps_pager .= '<img height="' . $image['height'] . '" width="' . $image['height'] . '" src="' . $image['url'] . '" alt="' . $post->post_title . ' thumbnail" />';
																				
						if ( $j % $carouselsize == 0 && $j != $ps_count ) { 
							$ps_pager .='</div>
							<div>';
						}

						$j++;
						
					endwhile;
					wp_reset_postdata();
					
					$ps_pager .= '</div></div></div><a class="ps-next browse ps-right psp-icon psp-icon-replace" data-icon="&#38;">right</a></div><!--.pscarousel-->';
					break;				
			
			case "thumbs":
				
				if ( $proportionalthumbs == "true" ) {
					$thumbcrop = false;	
				} else {
					$thumbcrop = true;
				}
								
				$ps_pager = '<div class="psthumbs';
					if ( $togglethumbs == "true" ) {
						$ps_pager .= ' toggle-thumbs';
					}				
				$ps_pager .='">';
												   
				$ps_pager .= '<div id="pager' . $slideKey;
				
				if ( $pagerwidth || $centered ) {
					$ps_pager .= '" style="';
				}

				if ( $pagerwidth ) {
					$ps_pager .= 'width:' . $pagerwidth . 'px;';
				}				
				
				if ( $centered == "true" ) {
					$ps_pager .= 'margin-left:' . $thumbnailmargin . 'px;';
				} 
					
				$ps_pager .= '" class="pager items clearfix';
				
				if ( $proportionalthumbs == "true" ) {
					$ps_pager .= ' proportional';
				}
				
				$ps_pager .= '">';
																											
				while( $psp_query->have_posts() ) : $psp_query->the_post();
					global $post;
									
					$image = psp_resize( $post->ID, '', $thumbnailsize[0], $thumbnailsize[1], $thumbcrop );
					$ps_pager .= '<div><span style="height:' .  $image['height'] . 'px; width:' . $image['width'] . 'px;"><span></span><img style="margin-right:' . $thumbnailmargin . 'px; margin-bottom:' . $thumbnailmargin . 'px;" src="' . $image['url'] . '" alt="' . $post->post_title . ' thumbnail" /></span></div>';			
				
				endwhile;
				wp_reset_postdata();
				
				$ps_pager .= '</div><!--.pager--></div><!--.psthumbs-->';
				break;	
		
			default :
				$ps_pager .= '<ul id="pager'.$slideKey.'" class="pager"></ul>';
				break;
		}			
	}	

	if ( isset( $ps_pager ) ) return $ps_pager;
}
