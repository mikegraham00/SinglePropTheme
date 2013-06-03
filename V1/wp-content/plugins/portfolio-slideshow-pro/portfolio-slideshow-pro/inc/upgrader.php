<?php 

if ( get_option( 'portfolio_slideshow_options' ) === false ) { 
//Options don't exist yet so we'll set them up		
	$ps_options = array(
			'version'	=>	PORTFOLIO_SLIDESHOW_PRO_VERSION,
			'size'	=> 'medium',
			'customwidth'	=> '500',
			'customheight'	=> '500',
			'trans'	=> 'fade',
			'speed'	=> '400', 
			'showtitles'	=>	"false", 
			'showcaps'	=>	"false", 
			'showdesc'	=>	"false", 
			'centered'	=>	"false", 
			'pagerpos'	=>	'middle',
			'pagerstyle'	=>	'thumbs',
			'thumbsize'	=>	'75',
			'carouselsize'	=>	'6',
			'carousel_thumbsize'	=> '75',
			'carousel_thumbnailmargin'	=> '8',
			'thumbnailmargin'	=>	'5',
			'togglethumbs'	=>	"false",
			'togglestate'	=> "closed",
			'proportionalthumbs'	=>	"false",
			'thumbborder'	=>	'FFFFFF',
			'navpos'	=>	'top', 
			'metapos'	=> 'bottom',
			'fancygrid'	=>	'false', 
			'fullscreen'	=>	'false', 
			'random'	=>	"false", 
			'navstyle'	=>	'graphical',
			'showplay'	=>	"true",
			'crop'	=> "false",
			'showinfo'	=> 	"true",
			'touchswipe'	=>	"true",
			'keyboardnav'	=>	"true",
			'backgroundimages'	=>	"false", 
			'infotxt'	=>	'of',
			'allowfluid'	=>	'false',
			'nowrap'	=>	"false",
			'loop'	=> 	"true",
			'showhash'	=>	"false", 
			'click'	=>	'advance', 
			'target'	=>	'_blank', 
			'timeout'	=>	'4000',
			'autoplay'	=>	'false', 
			'exclude_featured'	=>	"false", 
			'showloader'	=>	"false",
			'jquery'	=>	"wp",
			'fancybox'	=>	"true",
			'scrollable'	=> "true",
			'photoswipe' => "true",
			'cycle'	=>	"true",
			'legacy' => "false",
			'load_scripts'	=>	"true", 
			'license'	=>	'',
			'debug'	=>	'false'
		);	
	
	update_option( 'portfolio_slideshow_options', $ps_options );

} else { // If we've already got options, check to see if we need to add new tables, run the standard upgrade script
	
	$ps_options = get_option( 'portfolio_slideshow_options' );
	
	if ( $ps_options['version'] < '1.4.1' ) { //added a new option in 1.4.1
		$ps_options['allowfluid'] = "false"; 
		$ps_options['proportionalthumbs'] = "false"; 
	}
	
	if ( $ps_options['version'] < '1.4.6' ) { //added a new option in 1.4.6
		$ps_options['touchswipe'] = "true"; 
		$ps_options['keyboardnav'] = "true"; 
	}
	
	if ( $ps_options['version'] < '1.4.8' ) { //added a new option in 1.4.8
		$ps_options['cycle'] = "true";
	}
	
	if ( $ps_options['version'] < '1.5.0' ) { //added new options in 1.5.0
		$ps_options['fancygrid'] = "false";
		$ps_options['carousel_thumbsize'] = '75';
		$ps_options['carousel_thumbnailmargin'] = '8';
		if ( $ps_options['carousel'] == "true" ) 
			{ $ps_options['pagerstyle'] == "carousel"; }
		if ( $ps_options['jquery'] == "1.6.1" )
			{ $ps_options['jquery'] == "1.7.1"; }
	} 
	
	if ( $ps_options['version'] < '1.6.0' ) { //added new options in 1.5.8
		$ps_options['togglestate'] = "closed";
		$ps_options['cycle'] = "true";
		$ps_options['fancybox'] = "true";
		$ps_options['photoswipe'] = "true";
		$ps_options['crop'] = "false";
		$ps_options['loop'] = "true";
		$ps_options['backgroundimages']	=	"false";
	} 
	
	if ( $ps_options['version'] < '1.7.0' ) { //added new options in 1.7.0
		$ps_options['fullscreen'] = "false";
		$ps_options['scrollable'] = "true";
		if ( $ps_options['jquery'] == "none" ) {	 
			$ps_options['jquery'] = "false"; 
		} else {
			$ps_options['jquery'] = "true";
		}
	} 

	if ( $ps_options['version'] < '1.7.3' ) { //Changed jQuery option in 1.7.3
		if ( $ps_options['jquery'] == "true" ) {
			$ps_options['jquery'] = "wp";
		}
	}

	if ( $ps_options['version'] < '1.8.0' ) { //added new options in 1.7.0
		$ps_options['metapos'] = "bottom";
		$ps_options['legacy'] = "true";
	} 

	if ( $ps_options['version'] < '1.8.2' ) { //added new options in 1.8.2
		$ps_options['gallery'] = "false";
	} 

	$ps_options['version'] = PORTFOLIO_SLIDESHOW_PRO_VERSION;
	update_option( 'portfolio_slideshow_options', $ps_options );		
}	

?>