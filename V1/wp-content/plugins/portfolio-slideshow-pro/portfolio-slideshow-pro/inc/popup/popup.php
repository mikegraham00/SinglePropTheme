<?php /* Include Wordpress */
define('WP_USE_THEMES', false);
require('../../../../../wp-blog-header.php');?>

<?php 

/* Set up some defaults for a few things in case they aren't passed in the URL */
		global $ps_options;
		$height = 450;
		$width = 999;
		$slideheight = 460;
		$carousel = "true";
		$centered = $ps_options['centered'];
		$carouselsize = $ps_options['carouselsize'];
		$pagerpos = $ps_options['pagerpos'];
		$showtitles = $ps_options['showtitles'];
		$showcaps = $ps_options['showcaps'];
		$showdesc = $ps_options['showdesc'];
		$navstyle = $ps_options['navstyle'];
		$navpos = $ps_options['navpos'];
		$autoplay = $ps_options['autoplay'];
	
		
	if (isset($_GET['id'])) $id = esc_attr($_GET['id']);
	if (isset($_GET['h'])) $height = esc_attr($_GET['h']);
	if (isset($_GET['sh'])) $slideheight = esc_attr($_GET['sh']);
	if (isset($_GET['w']))	$width = esc_attr($_GET['w']);
	if (isset($_GET['carousel'])) $carousel = esc_attr($_GET['carousel']);
	if (isset($_GET['carouselsize'])) $carouselsize = esc_attr($_GET['carouselsize']);
	if (isset($_GET['caps'])) $showcaps = esc_attr($_GET['caps']);
	if (isset($_GET['titles'])) $showtitles = esc_attr($_GET['titles']);
	if (isset($_GET['desc'])) $showdesc = esc_attr($_GET['desc']);
	if (isset($_GET['centered'])) $centered = esc_attr($_GET['centered']);
	if (isset($_GET['nav'])) $shownav = esc_attr($_GET['nav']);
	if (isset($_GET['navstyle'])) $navstyle = esc_attr($_GET['navstyle']);
	if (isset($_GET['navpos'])) $navpos = esc_attr($_GET['navpos']);
	if (isset($_GET['autoplay'])) $autoplay = esc_attr($_GET['autoplay']);
?>

<?php if ( ! $id ) die; ?>

<!doctype html public>
<!--[if lt IE 7]> <html lang="en-us" class="no-js ie6"> <![endif]-->
<!--[if IE 7]>    <html lang="en-us" class="no-js ie7"> <![endif]-->
<!--[if IE 8]>    <html lang="en-us" class="no-js ie8"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en-us" class="no-js"> <!--<![endif]-->
<head>

 <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1" >
  
  <title dir="ltr"><?php echo get_the_title($id);?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">  

  <link rel="stylesheet" media="screen" href="<?php echo PORTFOLIO_SLIDESHOW_PRO_URL;?>/css/portfolio-slideshow.css" >
  <link rel="stylesheet" media="screen" href="<?php echo PORTFOLIO_SLIDESHOW_PRO_URL;?>/js/fancybox/jquery.fancybox-1.3.4.min.css" >
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>

 <?php portfolio_slideshow_head();?>
</head>
<body class="popup-viewer">

	<section class="main"> 
		
	<?php if ( $carousel == "true" ) { $pagerpos="bottom"; } else { $pagerpos="disabled"; }
		
	echo do_shortcode( "[portfolio_slideshow id=$id click=advance width=$width height=$height slideheight=$slideheight carousel=$carousel carouselsize=$carouselsize pagerpos=$pagerpos showtitles=$showtitles showcaps=$showcaps showdesc=$showdesc centered=$centered navstyle=$navstyle navpos=$navpos autoplay=$autoplay togglethumbs=false fancygrid=false]");
	
	?>

	</section>
	
	<script src="<?php echo PORTFOLIO_SLIDESHOW_PRO_URL;?>/js/jquery.cycle.all.min.js"></script>
	<script src="<?php echo PORTFOLIO_SLIDESHOW_PRO_URL;?>/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<script src="<?php echo PORTFOLIO_SLIDESHOW_PRO_URL;?>/js/scrollable.min.js"></script>
	<script src="<?php echo PORTFOLIO_SLIDESHOW_PRO_URL;?>/js/portfolio-slideshow.js"></script>

	<?php portfolio_slideshow_foot();?>
</body>
</html>