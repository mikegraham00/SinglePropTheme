<?php

/**

 * Template Name: Property Details

 *

 *

 */





    // calling the header.php

    get_header();



    // action hook for placing content above #container

    thematic_abovecontainer();



?>



		<div id="container">

		

			<?php thematic_abovecontent(); ?>

		

			<div id="content" class="details">

			<?php the_post();?>

			

			<h1><?php the_title(); ?></h1>

			

				<?php 

				$property_info = new WP_Query();

				$property_info->query('post_type=property&showposts=1');

				if ($property_info->have_posts()):

					

				while($property_info->have_posts()):

					$property_info->the_post();

					$post_id = get_the_ID();

					

				?>

	            <div id="home-info">

      				<?php if(types_render_field("lifestyle-video", false)) : ?>

	            	<div id="neighborhood-video">

	            		

	            		<a id="neighborhood-video" class="iframe"  href="<?php echo get_post_meta($post_id,'wpcf-lifestyle-video', true);?>">

					View <?php echo get_post_meta($post_id, 'wpcf-lifestyle-video-title', true); ?>

				</a>

	            	</div>

				<?php endif; ?>

	            	

	            		            	

	            	<h2>Home</h2>

	            	

	            	

	            	

	            	<table class="property-info-table">

	            		<tbody>

	            		<tr>

	            			<td>Price:</td>

	            			<td><? echo do_shortcode('[types field="list-price"]'); ?></td>

	            		</tr>



	            		<tr>

	            			<td>Bedrooms:</td>

	            			<td><? echo do_shortcode('[types field="beds"]'); ?></td>

	            		</tr>



	            		<tr>

	            			<td>Full Bathrooms:</td>

	            			<td><? echo do_shortcode('[types field="baths"]'); ?></td>

	            		</tr>



	            		<?php if(types_render_field("half-baths", false)) : ?>

				<tr>

	            			<td>Half Bathrooms: </td>

	            			<td><? echo do_shortcode('[types field="half-baths"]'); ?> </td>

	            		</tr>

				<?php endif; ?>

				

				<?php if(types_render_field("size", false)) : ?>

	            		<tr>

	            			<td>Total Square Footage:</td>

	            			<td><? echo do_shortcode('[types field="size"]'); ?></td>

	            		</tr>

				<?php endif; ?>

				

				<?php if(types_render_field("lot-size", false)) : ?>

	            		<tr>

	            			<td>Lot Size:</td>

	            			<td><? echo do_shortcode('[types field="lot-size"]'); ?></td>

	            		</tr>

				<?php endif; ?>

	

				<?php if(types_render_field("year-built", false)) : ?>

	            		<tr>

	            			<td>Year Completed:</td>

	            			<td><? echo do_shortcode('[types field="year-built"]'); ?></td>

	            		</tr>

				<?php endif; ?>



				<?php if(types_render_field("living-areas", false)) : ?>

	            		<tr>

	            			<td>Living Areas:</td>

	            			<td><? echo do_shortcode('[types field="living-areas"]'); ?></td>

	            		</tr>

				<?php endif; ?>



                               <?php if(types_render_field("dining-areas", false)) : ?>

	            		<tr>

	            			<td>Dining Areas:</td>

	            			<td><? echo do_shortcode('[types field="dining-areas"]'); ?></td>

	            		</tr>

                                <?php endif; ?>



				<?php if(types_render_field("garage-spaces", false)) : ?>

	            		<tr>

	            			<td>Garage Spaces:</td>

	            			<td><? echo do_shortcode('[types field="garage-spaces"]'); ?></td>

	            		</tr>

				<?php endif; ?>



				<?php if(types_render_field("levels", false)) : ?>

	            		<tr>

	            			<td>Levels:</td>

	            			<td><? echo do_shortcode('[types field="levels"]'); ?></td>

	            		</tr>

				<?php endif; ?>



                                <?php if(types_render_field("builder", false)) : ?>

	            		<tr>

	            			<td>Builder:</td>

	            			<td><? echo do_shortcode('[types field="builder"]'); ?></td>

	            		</tr>

                                <?php endif; ?>



           			<?php if(types_render_field("property-taxes", false)) : ?>

	            		<tr>

	            			<td>Estimated Yearly Taxes:</td>

	            			<td><? echo do_shortcode('[types field="property-taxes"]'); ?></td>

	            		</tr>

				<?php endif; ?>

	            		

	            		</tbody>

	            	</table>

	            	

	            	<h2>Location</h2>

	            	<table class="property-info-table">

	            		<tbody>

					<?php if(types_render_field("subdivision", false)) : ?>

					<tr>

						<td>Community/Subdivision: </td>

						<td><?php echo do_shortcode('[types field="subdivision"]'); ?></td>

					</tr>

					<?php endif; ?>



					<?php if(types_render_field("area", false)) : ?>

	            			<tr>

								<td>MLS Area:</td>

								<td><? echo do_shortcode('[types field="area"]'); ?></td>

	            			</tr>

					<?php endif; ?>

	            		</tbody>

	            	</table>

	            	

	            	<h2>Description</h2>

	            	<p><? echo do_shortcode('[types field="property-description"]'); ?></p>

	            	

	            	<div id="schools-info">

	            	<h2>Schools</h2>

	            	<table class="property-info-table">

	            		<tbody>

	            		<tr>

	            			<td>Elementary: </td>

	            			<td><? echo do_shortcode('[types field="elementary"]'); ?></td>

	            		</tr>

	            		<tr>

	            			<td>Middle School: </td>

	            			<td><? echo do_shortcode('[types field="middle-school"]'); ?></td>

	            		</tr>

	            		<tr>

	            			<td>High School: </td>

	            			<td><? echo do_shortcode('[types field="high-school"]'); ?></td>

	            		</tr>

	            		</tbody>

	            	</table>

	            	</div>

	            	

			<?php if(types_render_field("property-video-code", false)) : ?>

	            	<div id="property-video">

	            		

	            		<a id="showcase-video" class="iframe"  href="<?php echo get_post_meta($post_id, 'wpcf-property-video-code', true);?>">View Property Showcase Video</a>

	            	</div>

			<?php endif; ?>

	            	

			<?php if(types_render_field("printable-flyer", false) || types_render_field("floorplan", false) || types_render_field("survey", false) || types_render_field("sellers-disclosure", false)) : ?>

	            	<div id="printable-info">

	            	

	            	<h2>Printable Information</h2>

	            	<ul id="printable-info">

				

				<?php if(types_render_field("printable-flyer", false)) : ?>

				<li><a target="_blank" href="<? echo do_shortcode('[types field="printable-flyer"]'); ?>">Printable Flyer</a> </li>

				<?php endif; ?>



				<?php if(types_render_field("floorplan", false)) : ?>

				<li><a target="_blank" href="<? echo do_shortcode('[types field="floorplan"]'); ?>">Floorplan</a></li>

				<?php endif; ?>



				<?php if(types_render_field("survey", false)) : ?>

				<li><a target="_blank" href="<? echo do_shortcode('[types field="survey"]'); ?>">Survey</a></li>

				<?php endif; ?>



				<?php if(types_render_field("sellers-disclosure", false)) : ?>

				<li><a target="_blank" href="<? echo do_shortcode('[types field="sellers-disclosure"]'); ?>">Seller's Disclosure</a></li>

				<?php endif; ?>

					</ul>

	            	</div>

			<?php endif; ?>

	            	

	            	<?php 

	            		

	            		endwhile;

	            		endif;

	            		wp_reset_query();

	        

	        thematic_belowpost();

	        

	        // calling the comments template

	        //thematic_comments_template();

	        

	       

	        

	        ?>

	

			</div><!-- #content --></div>

			

			<?php thematic_belowcontent(); ?> 

			

			<?php

			 // calling the widget area 'page-top'

	        get_sidebar('page-top');

	        ?>

			

		</div><!-- #container -->



<?php 



    // action hook for placing content below #container

    thematic_belowcontainer();

    

    // calling footer.php

    get_footer();



?>