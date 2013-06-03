<?php 
global $options;
$options = get_option( 'portfolio_slideshow_options' );

$cachedir = WP_CONTENT_DIR . '/cache';

$psp_errors = array();

if ( !is_dir( $cachedir ) ) { 
	$psp_errors[] = 'The plugin was not able to create the cache directory required for thumbnail and custom image size generation. Please see the <a href="http://madebyraygun.com/support/faq/why-arent-my-thumbnail-and-custom-sized-images-being-generated/">FAQ</a> for instructions on how to resolve this issue.'; 
}

if ( ! function_exists('gd_info')) {
	$psp_errors[] = 'Your server does not support the GD function. You may have trouble running some thumbnail related functions in this plugin.'; 
}

if ( PHP_VERSION > 5 ) { //don't bother showing this error message unless the server is running PHP5 or above
	if ( empty( $options['license'] ) ) {
		$psp_errors[] = 'The plugin will not be registered for auto-updates until you enter your email address in the "Diagnostic" section below.'; 
	}
}

if ( PHP_VERSION < 5 ) {
	$psp_errors[] = 'Your server is running an out-of-date version of PHP and the plugin cannot auto-update. It is strongly recommended that you update your web server to the current version of PHP.'; 
}

// Add a menu for our option page
add_action('admin_menu', 'portfolio_slideshow_add_page');
function portfolio_slideshow_add_page() {
	add_options_page( 'Portfolio Slideshow Pro', 'Portfolio Slideshow Pro', 'manage_options', 'portfolio_slideshow', 'portfolio_slideshow_option_page' );
}

if ( isset( $_GET['page'] ) && $_GET['page'] == "portfolio_slideshow" ) {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_register_script( 'portfolio-slideshow-admin', plugins_url( 'js/portfolio-slideshow-admin.js', __FILE__ ), false, $options['version'], true); 
	wp_enqueue_script( 'portfolio-slideshow-admin' );
	wp_register_style( 'portfolio-slideshow-admin', plugins_url( 'css/portfolio-slideshow-admin.css', __FILE__ ), false, $options['version'], 'screen' ); 
	wp_enqueue_style( 'portfolio-slideshow-admin' );
}


// Draw the option page
function portfolio_slideshow_option_page() {
global $psp_errors;	
$options = get_option( 'portfolio_slideshow_settings' );

// set up some defaults if these fields are empty
?>
	<div class="wrap">
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1">Slideshow Settings</a></li>
			<li><a href="#tabs-2">Documentation</a></li>
		</ul>
		
		<div id="tabs-1">
		
		<?php screen_icon(); ?>
		<h2>Portfolio Slideshow Pro</h2>

		<?php if ( $psp_errors ) {
			foreach ( $psp_errors as $error ) { 
				echo '<div id="message" class="updated fade">
					<p>' . $error . '</p>
					</div>';	
			}
		}?>

		<form action="options.php" method="post">
			<?php settings_fields('portfolio_slideshow_options'); ?>
			<?php do_settings_sections('portfolio_slideshow'); ?>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
		
		</div><!--#tabs-1-->
				
		<div id="tabs-2">
		
		<h3>General usage</h3>
		
		<iframe src="http://player.vimeo.com/video/20679115?byline=0&amp;color=ffffff" width="601" height="378" frameborder="0"></iframe><p><a href="http://vimeo.com/20679115">Portfolio Slideshow Pro demo</a> from <a href="http://vimeo.com/madebyraygun">Raygun</a> on <a href="http://vimeo.com">Vimeo</a>.</p>

		<p>To use the plugin, upload your photos directly to a post or page using the WordPress media uploader, or use the standalone slideshow panel to add a slideshow anywhere. Use the [portfolio_slideshow] shortcode to display the slideshow in your page or post.</p>
			
		<h3>Shortcode Attributes</h3>

		<p>If you would like to customize your slideshows on a per-slideshow basis, you can add the following attributes to the shortcode, which will temporarily override the defaults.

		<p>To select a <strong>different page parent ID</strong> for the images:</p>
		<p><code>[portfolio_slideshow id=xxx]</code></p>
		
		<p>
		To <strong>add a custom class</strong> to the slideshow wrapper:
		</p>
		
		<p>
		<code>[portfolio_slideshow class=alignright]</code>
		</p>	

		<p>
		To change the <strong>image size</strong> you would use the size attribute in the shortcode like this:
		</p>
		
		<p>
		<code>[portfolio_slideshow size=thumbnail], [portfolio_slideshow size=medium], [portfolio_slideshow size=large], [portfolio_slideshow size=full], [portfolio_slideshow size=custom]</code>
		</p>
		<p>This setting can use any custom image size that you've registered in WordPress. Portfolio Slideshow Pro gives you the ability to create one custom image size, if you need more, take a look at the <a href="http://wordpress.org/extend/plugins/additional-image-sizes-zui/screenshots/">Additional Image Sizes</a> plugin.
		
		<p>You can specify a <strong>custom image size</strong> directly in the shortcode with the height and width attributes:</p>

		<code>[portfolio_slideshow width=600], [portfolio_slideshow height=450], [portfolio_slideshow width=600 height=450]</code> 

		<p>The height and width attributes override the size attribute and will not upscale or crop the images.<p>

		<p>
		You can add a custom <strong>slide container height</strong>: 
		</p>
		
		<p>
		<code>[portfolio_slideshow slideheight=400]</code>
		</p>
		
		<p>
		Useful if you don't want the page height to adjust with the slideshow.
		</p>
		
		<p>
		<strong>Image transition FX</strong>: 
		</p>
		
		<p>
		<code>[portfolio_slideshow trans=scrollHorz]</code>
		</p>
		
		<p>
		You can use this shortcode attribute to supply any transition effect supported by jQuery Cycle, even if they're not in the plugin! List of supported transitions <a href="http://jquery.malsup.com/cycle/begin.html">here</a> Not all transitions will work with all themes, if in doubt, stick with fade or none.
		</p>
				
		<p>
		<strong>Transition speed</strong>:
		</p>
		
		<p>
		<code>[portfolio_slideshow speed=400]</code>
		</p>
	
		<p>
		Add a <strong>delay</strong> to the beginning of the slideshow:
		</p>
		
		<p>
		<code>[portfolio_slideshow delay=1000]</code>
		</p>
			
		<p>
		<strong>Show titles, captions, or descriptions</strong>:
		</p>
		
		<p>
		<code>[portfolio_slideshow showtitles=true], [portfolio_slideshow showcaps=true], [portfolio_slideshow showdesc=true]</code>
		(use false to disable)</p>
		
		<p>
		<strong>Center the slideshow</strong>:
		</p>
		
		<p>
		<code>[portfolio_slideshow centered=true], [portfolio_slideshow centered=false]</code></p>
		
		
		<p>
		<strong>Time per slide when slideshow is playing (timeout)</strong>:
		</p>
		
		<p>
		<code>[portfolio_slideshow timeout=4000]</code>
		</p>

		
		<p>
		<strong>Autoplay</strong>:
		</p>
		<p>
		<code>[portfolio_slideshow autoplay=true]</code>
		</p>

		<p>
		<strong>Duration</strong>:
		</p>
		<p>You can set a time for the entire slideshow, in seconds, which will automatically calculate the per-slide timeout for you. This pairs nicely with the audio player support.</p>
		<p>
		<code>[portfolio_slideshow duration=300]</code>
		</p>


		<p>
		<strong>Audio</strong>:
		</p>
		<p>You can add audio to the slideshow by specifying any MP3 URL. This feature pairs nicely with the "Duration" attribute, you can create a slideshow that lasts exactly as long as the audio file. Be careful with autoplay! If you have multiple posts on a page (the blog homepage, for example), you'll end up with overlapping audio. Autoplay is recommended on single pages only. You must have our free <a href="http://madebyraygun.com/lab/haiku/">Haiku</a> audio player plugin installed and activated for this feature to work.</p>
		<p>
		<code>[portfolio_slideshow audio=http://madebyraygun.com/uploads/audiofile.mp3]</code>
		</p>

		<p>
		<strong>Randomize slideshow</strong>:
		</p>
		<p>
		<code>[portfolio_slideshow random=true]</code>
		</p>

		<p>
		<strong>Exclude featured image</strong>:
		</p>
		<p>
		<code>[portfolio_slideshow exclude_featured=true]</code>
		</p>

		<p>
		<strong>Disable slideshow wrapping</strong>: 
		</p>
		
		<p>
		<code>[portfolio_slideshow nowrap=true]</code>
		</p>
		
		<p>
		or enable it like this:
		</p>
		
		<p>
		<code>[portfolio_slideshow nowrap=false]</code>
		</p>
		

		<p>
		<strong>Clicking on a slideshow image:</strong>:
		</p>
		<p>Clicking on a slideshow image can advance the slideshow, open a custom URL (set in the media uploader), or open the full-size version of the image in a lightbox:
		<p>
		<code>[portfolio_slideshow click=advance] or [portfolio_slideshow click=openurl] or [portfolio_slideshow click=lightbox]</code>
		</p>

		<p>
		<strong>Navigation links</strong> can be placed at the top:
		</p>
		
		<p>
		<code>[portfolio_slideshow navpos=top]</code>
		</p>
		
		<p>
		or at the bottom:
		</p>
		
		<p>
		<code>[portfolio_slideshow navpos=bottom]</code></p>
		
		<p>Use <code>[portfolio_slideshow navpos=disabled]</code> to disable navigation altogether. Slideshow will still advance when clicking on slides, using the pager, or with autoplay.</p>
		
		<p><strong>Navigation link style</strong> can be selected:</p>
		
		<p>
		<code>[portfolio_slideshow navstyle=text]</code> or <code>[portfolio_slideshow navstyle=graphical]</code> </p>

		<p>The <strong>navigation info</strong> can be customized:</p>
		
		<p>Hide the play button: </p>
		<p><code>[portfolio_slideshow showplay=false]</code></p>

		<p>Hide the slidecounter: </p>
		<p><code>[portfolio_slideshow showinfo=false]</code></p>
		
		<p><strong>Pager style</strong> can be selected:</p>
		
		<p>
		<code>[portfolio_slideshow pagerstyle=thumbs]</code> or <code>[portfolio_slideshow pagerstyle=bullets]</code> or <code>[portfolio_slideshow pagerstyle=numbers]</code></p>
		
		<p><strong>The thumbnail carousel</strong> can be enabled:</p>
		
		<p>
		<code>[portfolio_slideshow carousel=true]</code> or disabled
		
		<code>[portfolio_slideshow carousel=false]</code>

		<p>You can change the number of<strong>thumbnails per row</strong>:</p>
		
		<p>
		<code>[portfolio_slideshow carouselsize=8]</code>

		<p><strong>Pager position</strong> can be selected:

	<code>[portfolio_slideshow pagerpos=top]</code> 
		</p>
		
		<p>
		or at the bottom:
		</p>
		
		<p>
		<code>[portfolio_slideshow pagerpos=bottom]</code></p> 

		<p>or disabled :
		</p>
		
		<p>
		<code>[portfolio_slideshow pagerpos=disabled]</code></p>
		<p>
		
		<p><strong>Toggle thumbnials</strong> can be enabled or disabled:

	<code>[portfolio_slideshow togglethumbs=true]</code> or </code> [portfolio_slideshow togglethumbs=false]
		</p>

		<p>
		<strong>Include or exclude slides</strong>:
		</p>
		
		<p>
		<code>[portfolio_slideshow include="1,2,3,4"]</code>
		</p>
		
		<p>
		<code>[portfolio_slideshow exclude="1,2,3,4"]</code>
		</p>
		
		<p>You need to specify the attachment ID, which you can find in your <a href="<?php bloginfo( 'wpurl' )?>/wp-admin/upload.php">Media Library</a> by hovering over the thumbnail. You can only include attachments which are attached to the current post. Do not use these attributes simultaneously, they are mutually exclusive.</p>

		<p>Instead of including or excluding slides, you may want to consider adding a <a href="<?php bloginfo( 'wpurl' )?>/wp-admin/edit.php?post_type=portfolio_slideshow">standalone slideshow</a> with just the images you need and inserting that instead.</p>
		
		<p>
		<strong>Multiple slideshows per post/page</strong>:
		</p>
		
		<p>
		You can insert as many slideshows as you want in a single post or page by using the include/exclude attributes, or by creating multiple standalone slideshows.</p>
		</p>
		
	</div><!--#tabs-2-->
	</div><!--#tabs-->

		<a href="http://madebyraygun.com"><img style="margin-top:30px;" src="<?php echo plugins_url( 'img/logo.png', __FILE__ );?>" width="225" height="70" alt="Made by Raygun" /></a>
		<p>You're using Portfolio Slideshow, made by <a href="http://madebyraygun.com">Raygun</a>. Check out our <a href="http://madebyraygun.com/lab/" target="_blank">other plugins</a>, and if you have any problems, stop by our <a href="http://madebyraygun.com/support/forum/" target="_blank">support forum</a>!</p>
		</div>
	<?php
}

// Register and define the settings
add_action('admin_init', 'portfolio_slideshow_admin_init');
function portfolio_slideshow_admin_init(){
	register_setting(
		'portfolio_slideshow_options',
		'portfolio_slideshow_options',
		'portfolio_slideshow_validate_options'
	);
	add_settings_section(
		'portfolio_slideshow_display',
		'Slideshow Display',
		'portfolio_slideshow_section_text',
		'portfolio_slideshow'
	);
	
	add_settings_section(
		'portfolio_slideshow_behavior',
		'Slideshow Behavior',
		'portfolio_slideshow_section_text',
		'portfolio_slideshow'
	);
	
	add_settings_section(
		'portfolio_slideshow_navigation',
		'Slideshow Navigation',
		'portfolio_slideshow_section_text',
		'portfolio_slideshow'
	);
	
	add_settings_section(
		'portfolio_slideshow_diagnostic',
		'Slideshow Diagnostic',
		'portfolio_slideshow_section_text',
		'portfolio_slideshow'
	);
	
	add_settings_field(
		'portfolio_slideshow_size',
		'Slideshow size <span class="vtip" title="You can change the default image sizes in the Media Settings control panel, or add a new custom image size of your own. If you change the settings for an existing (WordPress built-in) image size, you will need to regenerate your thumbnails to see the changes reflected in existing images. Search the WordPress plugin repository for the Regenerate Thumbnails plugin for information on how to do this.">?</span>',
		'portfolio_slideshow_size_input',
		'portfolio_slideshow',
		'portfolio_slideshow_display'
	);


	add_settings_field(
		'portfolio_slideshow_customsize',
		'Custom size: <span class="vtip" title="The size in pixels of your new default image size. This can be overridden in the shortcode with the height and width attributes, e.g.: [portfolio_slideshow width=500].">?</span>',
		'portfolio_slideshow_customsize_input',
		'portfolio_slideshow',
		'portfolio_slideshow_display'
	);
	
	add_settings_field(
		'portfolio_slideshow_trans',
		'Transition FX <span class="vtip" title="You can override these in the shortcode with any option that the jQuery Cycle plugin supports. Note: The scrollHorz transition does not work well with fluid-layout themes such as TwentyEleven.">?</span>',
		'portfolio_slideshow_trans_input',
		'portfolio_slideshow',
		'portfolio_slideshow_display'
	);
		
	add_settings_field(
		'portfolio_slideshow_speed',
		'Transition speed <span class="vtip" title="How long should the transition last when the slideshow is advanced? Enter in milliseconds, e.g. 400 = 0.4 seconds per transition."?">?</span>',
		'portfolio_slideshow_speed_input',
		'portfolio_slideshow',
		'portfolio_slideshow_display'
	);	
	
	add_settings_field(
		'portfolio_slideshow_showtitles',
		'Show titles, captions, & descriptions',
		'portfolio_slideshow_showtitles_input',
		'portfolio_slideshow',
		'portfolio_slideshow_display'
	);	

	/*add_settings_field(
		'portfolio_slideshow_hovercaps',
		'Hover captions over slideshow <span class="vtip" title="Displays captions in a floating window over the slideshow image area">?</span>',
		'portfolio_slideshow_hovercaps_input',
		'portfolio_slideshow',
		'portfolio_slideshow_display'
	);	*/

	add_settings_field(
		'portfolio_slideshow_centered',
		'Center slideshow <span class="vtip" title="Centers slideshow, nav, &amp; pager within the parent content area.">?</span>',
		'portfolio_slideshow_centered_input',
		'portfolio_slideshow',
		'portfolio_slideshow_display'
	);	

	add_settings_field(
		'portfolio_slideshow_allowfluid',
		'Support for fluid layouts <span class="vtip" title="If you have a fluid layout (like TwentyEleven) and want your slideshows to resize dynamically, leave this checked. If you have a fixed-width layout and notice the content area shifting before the slideshow loads, you can uncheck this to prevent that.">?</span>',
		'portfolio_slideshow_allowfluid_input',
		'portfolio_slideshow',
		'portfolio_slideshow_display'
	);	


	add_settings_field(
		'portfolio_slideshow_timeout',
		'Slideshow timing <span class="vtip" title="How long should each slide be displayed during an automatic slideshow? Enter in milliseconds, e.g. 3000 = 3 seconds per slide.">?</span>',
		'portfolio_slideshow_timeout_input',
		'portfolio_slideshow',
		'portfolio_slideshow_behavior'
	);
	
	add_settings_field(
		'portfolio_slideshow_autoplay',
		'Enable autoplay <span class="vtip" title="Starts slideshows automatically when the page is loaded.">?</span>',
		'portfolio_slideshow_autoplay_input',
		'portfolio_slideshow',
		'portfolio_slideshow_behavior'
	);
	
	add_settings_field(
		'portfolio_slideshow_random',
		'Randomize slideshow <span class="vtip" title="Play the slideshow back in a random order.">?</span>',
		'portfolio_slideshow_random_input',
		'portfolio_slideshow',
		'portfolio_slideshow_behavior'
	);
	
	add_settings_field(
		'portfolio_slideshow_exclude_featured',
		'Exclude featured images from slideshow <span class="vtip" title="If you use the featured image function to create gallery thumbnails but don\'t want those images to appear in the slideshow, use this option.">?</span>',
		'portfolio_slideshow_exclude_featured_input',
		'portfolio_slideshow',
		'portfolio_slideshow_behavior'
	);
	
	
	add_settings_field(
		'portfolio_slideshow_showloader',
		'Show loading<br />animation <span class="vtip" title="If you\'ve got a slow connection or lots of images, sometimes the slideshow can take a little while to load. Selecting this option will include a loading gif to show that something is happening. You can customize the placement of the loading gif with CSS.">?</span>',
		'portfolio_slideshow_showloader_input',
		'portfolio_slideshow',
		'portfolio_slideshow_behavior'
	);
	
	
	add_settings_field(
		'portfolio_slideshow_nowrap',
		'Disable slideshow wrapping <span class="vtip" title="Should the slideshow play through to the beginning after it gets to the end, or simply stop?">?</span>',
		'portfolio_slideshow_nowrap_input',
		'portfolio_slideshow',
		'portfolio_slideshow_behavior'
	);
	
	add_settings_field(
		'portfolio_slideshow_click',
		'Clicking on a<br />slideshow image: <span class="vtip" title="URLs for the <em>Opens a custom URL</em> option are set in the image uploader. The lighbox option links to the full-size version of the image, so make sure your full-size images aren\'t too big.">?</span>',
		'portfolio_slideshow_click_input',
		'portfolio_slideshow',
		'portfolio_slideshow_behavior'
	);
	
	add_settings_field(
		'portfolio_slideshow_click_target',
		'New URL opens in:',
		'portfolio_slideshow_click_target_input',
		'portfolio_slideshow',
		'portfolio_slideshow_behavior'
	);
	
	add_settings_field(
		'portfolio_slideshow_showhash',
		'Update URL with slide IDs <span class="vtip" title="You can enable this feature to udpate the URL of the page with the slide ID number, e.g: http://example.com/slideshow/#3 will link directly to the third slide in the slideshow.">?</span>',
		'portfolio_slideshow_showhash_input',
		'portfolio_slideshow',
		'portfolio_slideshow_behavior'
	);
	
	add_settings_field(
		'portfolio_slideshow_navstyle',
		'Navigation style: <span class="vtip" title="What kind of navigation would you like to use?">?</span>',
		'portfolio_slideshow_navstyle_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	

	add_settings_field(
		'portfolio_slideshow_navoptions',
		'Navigation display options:',
		'portfolio_slideshow_navoptions_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	
	
	add_settings_field(
		'portfolio_slideshow_enhanced_navigation',
		'Enhanced navigation:',
		'portfolio_slideshow_enhanced_navigation_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	
	
	add_settings_field(
		'portfolio_slideshow_navpos',
		'Navigation position <span class="vtip" title="Where should the navigation controls appear?">?</span>',
		'portfolio_slideshow_navpos_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	
	
	add_settings_field(
		'portfolio_slideshow_pagerstyle',
		'Pager style <span class="vtip" title="Which type of slideshow pager would you like to use?">?</span>',
		'portfolio_slideshow_pagerstyle_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	
	
	add_settings_field(
		'portfolio_slideshow_thumbsize',
		'Thumbnail size <span class="vtip" title="You can specify the size of the thumbnails in the pager. (px)">?</span>',
		'portfolio_slideshow_thumbsize_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	
	
	add_settings_field(
		'portfolio_slideshow_thumbnailmargin',
		'Thumbnail margin <span class="vtip" title="How much space between each thumbnail? (px)">?</span>',
		'portfolio_slideshow_thumbnailmargin_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	
	
	add_settings_field(
		'portfolio_slideshow_carouselsize',
		'Number of thumbs per row <span class="vtip" title="Number of thumbnails to display per row, applies to thumbnail view and the carousel.">?</span>',
		'portfolio_slideshow_carouselsize_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	
	
	add_settings_field(
		'portfolio_slideshow_togglethumbs',
		'Thumbnail toggle <span class="vtip" title="Hides thumbnails by default with an option to show them.">?</span>',
		'portfolio_slideshow_togglethumbs_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	
	
	add_settings_field(
		'portfolio_slideshow_carousel',
		'Enable thumbnail carousel <span class="vtip" title="Show a limited number of thumbnails at a time with the ability to scroll through and see more.">?</span>',
		'portfolio_slideshow_carousel_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	
	
	add_settings_field(
		'portfolio_slideshow_pagerpos',
		'Pager position <span class="vtip" title="Where should the slideshow pager appear?">?</span>',
		'portfolio_slideshow_pagerpos_input',
		'portfolio_slideshow',
		'portfolio_slideshow_navigation'
	);	
	
	add_settings_field(
		'portfolio_slideshow_license',
		'Email address <span class="vtip" title="The email address you used to purchase the plugin.">?</span>',
		'portfolio_slideshow_license_input',
		'portfolio_slideshow',
		'portfolio_slideshow_diagnostic'
	);
	
	add_settings_field(
		'portfolio_slideshow_jquery',
		'jQuery version <span class="vtip" title="If you\'re having trouble with the Javascript effects, you can try an older version of jQuery, or disable it altogether. This sometimes helps if you have plugins or themes that rely on their own version of jQuery. For best results, leave this at the default setting.">?</span>',
		'portfolio_slideshow_jquery_input',
		'portfolio_slideshow',
		'portfolio_slideshow_diagnostic'
	);
	
	add_settings_field(
		'portfolio_slideshow_fancybox',
		'Load Fancybox <span class="vtip" title="Should we load the Fancybox library for image zoom, or do you have your own plugin for that?">?</span>',
		'portfolio_slideshow_fancybox_input',
		'portfolio_slideshow',
		'portfolio_slideshow_diagnostic'
	);
	
	add_settings_field(
		'portfolio_slideshow_cycle',
		'Load Cycle <span class="vtip" title="If another plugin is loading a conflicting Cycle library, you can try disabling ours.">?</span>',
		'portfolio_slideshow_cycle_input',
		'portfolio_slideshow',
		'portfolio_slideshow_diagnostic'
	);
	
		add_settings_field(
		'portfolio_slideshow_debug',
		'Enable debug mode <span class="vtip" title="Don\'t load minified scripts and possibly output debug info to screen to help troubleshoot issues with the plugin">?</span>',
		'portfolio_slideshow_debug_input',
		'portfolio_slideshow',
		'portfolio_slideshow_diagnostic'
	);


	add_settings_field(
		'portfolio_slideshow_version',
		'Version',
		'portfolio_slideshow_version_input',
		'portfolio_slideshow',
		'portfolio_slideshow_diagnostic'
	);
}

// Draw the section header
function portfolio_slideshow_section_text() {
}

// Display and fill the form fields

function portfolio_slideshow_size_input() {
	$options = get_option( 'portfolio_slideshow_options' );
		
	echo "<select name='portfolio_slideshow_options[size]' id='portfolio_slideshow_options_size'  value='" . $options['size'] . "' />";
	ps_get_image_sizes();
	echo "</select></li>";
}

function portfolio_slideshow_customsize_input() {
	$options = get_option( 'portfolio_slideshow_options' );
	
	echo "<input name='portfolio_slideshow_options[customwidth]' type='text' size='5' value='$options[customwidth]' /> <span>width (px)</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name='portfolio_slideshow_options[customheight]' type='text' size='5' value='$options[customheight]' /> <span>height (px)</span>";
}

function portfolio_slideshow_trans_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<select name="portfolio_slideshow_options[trans]" value="<?php echo $options[trans]; ?>" />
					<option value="fade" <?php if ( $options['trans'] == 'fade' ) echo " selected='selected'";?>>fade</option>
					<option value="scrollHorz" <?php if ( $options['trans'] == 'scrollHorz' ) echo " selected='selected'";?>>scrollHorz *</option>
				</select>
<?php }

function portfolio_slideshow_speed_input() {
	$options = get_option( 'portfolio_slideshow_options' );
	echo "<input id='speed' name='portfolio_slideshow_options[speed]' type='text' size='5' value='$options[speed]' />";
}

function portfolio_slideshow_showtitles_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[showtitles]" value="true" <?php checked( "true", $options['showtitles'] ); ?> />
 	<span>titles</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="portfolio_slideshow_options[showcaps]" value="true" <?php checked( "true", $options['showcaps'] ); ?> />
 	<span>captions</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="portfolio_slideshow_options[showdesc]" value="true" <?php checked( "true", $options['showdesc'] ); ?> />
 	<span>descriptions</span>
<?php }

/*function portfolio_slideshow_hovercaps_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[hovercaps]" value="true" <?php checked( "true", $options['hovercaps'] ); ?> />
 
<?php }*/

function portfolio_slideshow_centered_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[centered]" value="true" <?php checked( "true", $options['centered'] ); ?> />
 
<?php }

function portfolio_slideshow_allowfluid_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	<input type="checkbox" name="portfolio_slideshow_options[allowfluid]" value="true" <?php checked( "true", $options['allowfluid'] ); ?> />
 
<?php }

function portfolio_slideshow_timeout_input() {
	// get option 'text_string' value from the database
	$options = get_option( 'portfolio_slideshow_options' );
		
	// echo the field
	echo "<input id='version' name='portfolio_slideshow_options[timeout]' type='text' size='5' value='$options[timeout]' />";
}

function portfolio_slideshow_autoplay_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[autoplay]" value="true" <?php checked( "true", $options['autoplay'] ); ?> />
 
<?php }

function portfolio_slideshow_random_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[random]" value="true" <?php checked( "true", $options['random'] ); ?> />
 
<?php }

function portfolio_slideshow_exclude_featured_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[exclude_featured]" value="true" <?php checked( "true", $options['exclude_featured'] ); ?> />
 
<?php }

function portfolio_slideshow_showloader_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[showloader]" value="true" <?php checked( "true", $options['showloader'] ); ?> />
 
<?php }

function portfolio_slideshow_nowrap_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[nowrap]" value="true" <?php checked( "true", $options['nowrap'] ); ?> />
 
<?php }

function portfolio_slideshow_click_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<select id="portfolio_slideshow_options_click" name="portfolio_slideshow_options[click]" value="<?php echo $options[click]; ?>" />
		<option value="advance" <?php if ( $options['click'] == "advance" ) echo " selected='selected'";?>>Advances the slideshow</option>
		<option value="openurl" <?php if ( $options['click'] == "openurl" ) echo " selected='selected'";?>>Opens a custom URL</option>
		<option value="lightbox" <?php if ( $options['click'] == "lightbox" ) echo " selected='selected'";?>>Opens the image in a lightbox</option>
	</select>
<?php }

function portfolio_slideshow_click_target_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<select name="portfolio_slideshow_options[click_target]" value="<?php echo $options[click]; ?>" />
		<option value="_self" <?php if ( $options['click_target'] == "_self" ) echo " selected='selected'";?>>Same window</option>
		<option value="_blank" <?php if ( $options['click_target'] == "_blank" ) echo " selected='selected'";?>>New window</option>
	</select>
<?php }

function portfolio_slideshow_showhash_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[showhash]" value="true" <?php checked( "true", $options['showhash'] ); ?> />
 
<?php }

function portfolio_slideshow_navstyle_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<select name="portfolio_slideshow_options[navstyle]" value="<?php echo $options[navstyle]; ?>" />
		<option value="text" <?php if ( $options['navstyle'] == "text" ) echo " selected='selected'";?>>text</option>
		<option value="graphical" <?php if ( $options['navstyle'] == "graphical" ) echo " selected='selected'";?>>graphical</option>
	</select>
<?php }

function portfolio_slideshow_navoptions_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[showplay]" value="true" <?php checked( "true", $options['showplay'] ); ?> />
 	<span>show play button</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="portfolio_slideshow_options[showinfo]" value="true" <?php checked( "true", $options['showinfo'] ); ?> />
 	<span>show slide numbers</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name='portfolio_slideshow_options[infotxt]' type='text' size='6' value=<?php echo $options['infotxt']?> /> <span>(1 <?php echo $options['infotxt'];?> 12)</span>
<?php }


function portfolio_slideshow_enhanced_navigation_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[touchswipe]" value="true" <?php checked( "true", $options['touchswipe'] ); ?> />
 	<span>Enable touch/swipe controls</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="portfolio_slideshow_options[keyboardnav]" value="true" <?php checked( "true", $options['keyboardnav'] ); ?> />
 	<span>Enable keyboard navigation</span>
<?php }


function portfolio_slideshow_navpos_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<select name="portfolio_slideshow_options[navpos]" value="<?php echo $options[navpos]; ?>" />
		<option value="top" <?php if ($options['navpos'] == 'top' ) echo " selected='selected'";?>>top</option>
		<option value="bottom" <?php if ($options['navpos'] == 'bottom' ) echo " selected='selected'";?>>bottom</option>
		<option value="disabled" <?php if ($options['navpos'] == 'disabled' ) echo " selected='selected'";?>>disabled</option>
	</select>
<?php }

function portfolio_slideshow_pagerstyle_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<select id="portfolio_slideshow_options_pagerstyle" name="portfolio_slideshow_options[pagerstyle]" value="<?php echo $options[pagerstyle]; ?>" />
		<option value="thumbs" <?php if ( $options['pagerstyle'] == "thumbs" ) echo " selected='selected'";?>>thumbs</option>
		<option value="numbers" <?php if ( $options['pagerstyle'] == "numbers" ) echo " selected='selected'";?>>numbers</option>
		<option value="bullets" <?php if ( $options['pagerstyle'] == "bullets" ) echo " selected='selected'";?>>bullets</option>
	</select>
<?php }

function portfolio_slideshow_thumbsize_input() {
	$options = get_option( 'portfolio_slideshow_options' );
	
	echo "<input id='speed' name='portfolio_slideshow_options[thumbsize]' type='text' size='5' value='$options[thumbsize]' />";
}


function portfolio_slideshow_thumbnailmargin_input() {
	$options = get_option( 'portfolio_slideshow_options' );
	
	echo "<input id='speed' name='portfolio_slideshow_options[thumbnailmargin]' type='text' size='5' value='$options[thumbnailmargin]' />";
}

function portfolio_slideshow_carouselsize_input() {
	$options = get_option( 'portfolio_slideshow_options' );
	
	echo "<input id='speed' name='portfolio_slideshow_options[carouselsize]' type='text' size='2' value='$options[carouselsize]' />";
}

function portfolio_slideshow_togglethumbs_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[togglethumbs]" value="true" <?php checked( "true", $options['togglethumbs'] ); ?> />
 
<?php }

function portfolio_slideshow_carousel_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[carousel]" value="true" <?php checked( "true", $options['carousel'] ); ?> />
 
<?php }

function portfolio_slideshow_pagerpos_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<select name="portfolio_slideshow_options[pagerpos]" value="<?php echo $options[pagerpos]; ?>" />
		<option value="top" <?php if ( $options['pagerpos'] == 'top' ) echo " selected='selected'";?>>top</option>
		<option value="bottom" <?php if ( $options['pagerpos'] == 'bottom' ) echo " selected='selected'";?>>bottom</option>
		<option value="disabled" <?php if ( $options['pagerpos'] == 'disabled' ) echo " selected='selected'";?>>disabled</option>
	</select>
<?php }

function portfolio_slideshow_license_input() {
	$options = get_option( 'portfolio_slideshow_options' );
	
	if ( PHP_VERSION > 5 ) {
	
	echo "<input name='portfolio_slideshow_options[license]' type='text' size='50' value='$options[license]' /><span><br />The email address you used to purchase the plugin is your license key to enable automatic updates.</span>";
	
	} 
}
				
function portfolio_slideshow_jquery_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<select name="portfolio_slideshow_options[jquery]" value="<?php echo $options[jquery]; ?>" />
		<option value="1.6.1" <?php if ( $options['jquery'] == "1.6.1" ) echo " selected='selected'";?>>1.6.1</option>
		<option value="1.4.4" <?php if ( $options['jquery'] == "1.4.4" ) echo " selected='selected'";?>>1.4.4</option>
		<option value="disabled" <?php if ( $options['jquery'] == "disabled" ) echo " selected='selected'";?>>disabled</option>
	</select>
<?php }


function portfolio_slideshow_debug_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[debug]" value="true" <?php checked( "true", $options['debug'] ); ?> />
 
<?php }

function portfolio_slideshow_fancybox_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[fancybox]" value="true" <?php checked( "true", $options['fancybox'] ); ?> />
 
<?php }

function portfolio_slideshow_cycle_input() {
	$options = get_option( 'portfolio_slideshow_options' );?>
	
	<input type="checkbox" name="portfolio_slideshow_options[cycle]" value="true" <?php checked( "true", $options['cycle'] ); ?> />
 
<?php }
				
function portfolio_slideshow_version_input() {
	// get option 'text_string' value from the database
	$options = get_option( 'portfolio_slideshow_options' );
		
	// echo the field
	echo "<input type='text' readonly='readonly' id='version' name='portfolio_slideshow_options[version]' type='text' value='$options[version]' />";
}

// Validate user input
function portfolio_slideshow_validate_options( $input ) {
	
	if ( ! $input['speed'] )
    $input['speed'] = '400';
    
    if ( ! $input['infotxt'] )
    $input['infotxt'] = 'of';
    
    if ( ! $input['timeout'] )
    $input['timeout'] = '3000';
    
    if ( ! $input['thumbsize'] )
    $input['thumbsize'] = '75';
    
    if ( ! $input['thumbnailmargin'] )
    $input['thumbnailmargin'] = '8';
    
    if ( ! $input['carouselsize'] )
    $input['carouselsize'] = '7';  
	
	if ( ! isset( $input['showtitles'] ) )
    $input['showtitles'] = null;
	$input['showtitles'] = ( $input['showtitles'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['showcaps'] ) )
    $input['showcaps'] = null;
	$input['showcaps'] = ( $input['showcaps'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['showdesc'] ) )
    $input['showdesc'] = null;
	$input['showdesc'] = ( $input['showdesc'] == "true" ? "true" : "false" );
	
	/*if ( ! isset( $input['hovercaps'] ) )
    $input['hovercaps'] = null;
	$input['hovercaps'] = ( $input['hovercaps'] == "true" ? "true" : "false" );*/


	if ( ! isset( $input['centered'] ) )
    $input['centered'] = null;
	$input['centered'] = ( $input['centered'] == "true" ? "true" : "false" );

	if ( ! isset( $input['allowfluid'] ) )
    $input['allowfluid'] = null;
	$input['allowfluid'] = ( $input['allowfluid'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['autoplay'] ) )
    $input['autoplay'] = null;
	$input['autoplay'] = ( $input['autoplay'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['random'] ) )
    $input['random'] = null;
	$input['random'] = ( $input['random'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['exclude_featured'] ) )
    $input['exclude_featured'] = null;
	$input['exclude_featured'] = ( $input['exclude_featured'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['showloader'] ) )
    $input['showloader'] = null;
	$input['showloader'] = ( $input['showloader'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['showhash'] ) )
    $input['showhash'] = null;
	$input['showhash'] = ( $input['showhash'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['nowrap'] ) )
    $input['nowrap'] = null;
	$input['nowrap'] = ( $input['nowrap'] == "true" ? "true" : "0" );
	
	if ( ! isset( $input['togglethumbs'] ) )
    $input['togglethumbs'] = null;
	$input['togglethumbs'] = ( $input['togglethumbs'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['showplay'] ) )
    $input['showplay'] = null;
	$input['showplay'] = ( $input['showplay'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['showinfo'] ) )
    $input['showinfo'] = null;
	$input['showinfo'] = ( $input['showinfo'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['touchswipe'] ) )
    $input['touchswipe'] = null;
	$input['touchswipe'] = ( $input['touchswipe'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['keyboardnav'] ) )
    $input['keyboardnav'] = null;
	$input['keyboardnav'] = ( $input['keyboardnav'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['carousel'] ) )
    $input['carousel'] = null;
	$input['carousel'] = ( $input['carousel'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['fancybox'] ) )
    $input['fancybox'] = null;
	$input['fancybox'] = ( $input['fancybox'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['cycle'] ) )
    $input['cycle'] = null;
	$input['cycle'] = ( $input['cycle'] == "true" ? "true" : "false" );
	
	if ( ! isset( $input['debug'] ) )
    $input['debug'] = null;
	$input['debug'] = ( $input['debug'] == "true" ? "true" : "false" );
	
	$input['version'] =  wp_filter_nohtml_kses($input['version']); // Sanitize textarea input (strip html tags, and escape characters)
	$input['timeout'] =  wp_filter_nohtml_kses($input['timeout']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;

}
