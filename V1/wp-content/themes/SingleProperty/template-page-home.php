<?php
/**
 * Template Name: Home
 *
 * This Full Width template removes the primary and secondary asides so that content
 * can be displayed the entire width of the #content area.
 *
 */


    // calling the header.php
    get_header();
    

    // action hook for placing content above #container
    thematic_abovecontainer();
	?>

					<div id="contact-form"  class="home" style="display: none;">
						<h2>Inquire About This Property</h2>
						<?php echo do_shortcode('[contact-form-7 id="8" title="Property Contact Form"]'); ?>
					</div>
					<span id="contact-toggle" class="toggler" style="cursor: pointer;">request more info</span>
		
		<div id="container">
		
			<?php thematic_abovecontent(); ?>
		
			<div id="content" class="home">
	
	        <?php
	        
	        // calling the widget area 'page-bottom'
	        get_sidebar('page-bottom');
	        
	        ?>
	
			</div><!-- #content -->
			<?php if(is_home() || is_front_page()) : 
			$property_info = new WP_Query();
				$property_info->query('post_type=property&showposts=1');
				
				while($property_info->have_posts()):
					$property_info->the_post(); ?>
					<div id="home-page-feature">
							<h1>Offered at <?php echo do_shortcode('[types field="list-price"]'); ?></h1>
					</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>
			<?php endif; ?>
			<?php thematic_belowcontent(); ?> 
			
		</div><!-- #container -->

<?php 

    // action hook for placing content below #container
    thematic_belowcontainer();
    
    // calling footer.php
    get_footer();

?>