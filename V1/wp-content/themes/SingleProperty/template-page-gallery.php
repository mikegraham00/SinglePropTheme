<?php

/**

 * Template Name: Gallery

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

		

			<div id="content" class="gallery">

			<?php the_post();?>

			

			<h1><?php the_title(); ?></h1>

			

				

	            <div id="gallery">

	            <?php 

				$property_info = new WP_Query();

				$property_info->query('post_type=property&showposts=1');

				if ($property_info->have_posts()):

					

				while($property_info->have_posts()):

					$property_info->the_post();

					$post_id = get_the_ID();

					

		    if (get_post_meta($post_id, 'wpcf-virtual-tour-url', true)) : ?>

	            <a target="_blank" href="<?php echo get_post_meta($post_id, 'wpcf-virtual-tour-url', true);?>" id="virtual-tour-link">

	            		View Virtual Tour

	            	</a> 

	        	<?php 

                        endif;

	        		endwhile;

	        		endif;

	        		wp_reset_query();

	        	?>

	            	

	            	<?php the_content(); ?>

	            </div>

	        

	        <?php

	       

	        thematic_belowpost();

	        

	        // calling the comments template

	        //thematic_comments_template();

	        

	       

	        

	        ?>

	

			</div><!-- #content --></div>

			

			<?php thematic_belowcontent(); ?> 

			

			<?php

			 // calling the widget area 'page-top'

	        //get_sidebar('page-top');

	        ?>

			

		</div><!-- #container -->



<?php 



    // action hook for placing content below #container

    thematic_belowcontainer();

    

    // calling footer.php

    get_footer();



?>