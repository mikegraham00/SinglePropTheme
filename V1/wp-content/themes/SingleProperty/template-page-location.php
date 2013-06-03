<?php

/**

 * Template Name: Location

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

		

			<div id="content" class="location">

			<?php the_post();?>

			

			<h1><?php the_title(); ?></h1>

			<?php if(has_post_thumbnail()) : ?>

			<div style="width: 640px; margin-bottom: 24px; background: #fff; padding: 4px; box-shadow: 12px 12px 8px #777; position: relative; "><?php the_post_thumbnail(); ?></div>

			<?php endif; ?>

			<?php

			echo do_shortcode('[mappress mapid="1"]');

	        

	        thematic_belowpost();

	        

	        // calling the comments template

	        //thematic_comments_template();

	        

	       

	        

	        ?>

	

			</div><!-- #content -->

			

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