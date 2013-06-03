    </div><!-- #main -->

    

    <?php

    

    // action hook for placing content above the footer

    thematic_abovefooter();

    

    ?>    



	<div id="footer">

    

        <?php

        

        // action hook creating the footer 

        thematic_footer();

        

        query_posts('post_type=agent&limit=1');

        while (have_posts() ) : the_post ();

        	

        ?>

        <div id="footer-wrap">

        	<div id="agent-info">

			   <div id="agent-image">

					<?php

					$param = array('output' => 'text', 'style'=>'none', 'raw'=>'true', 'show_name' => 'false');

					?>

					<a href="<?php echo types_render_field('agent-website', $param); ?> " target="_blank">

						<?php echo do_shortcode('[types field="agent-photo"]'); ?>

					</a>

				</div>

				<div id="agent">

						<h2> <?php the_title(); ?> </h2 >

						<p class="agent-title"><?php echo do_shortcode('[types field="agent-title"]'); ?></p> 

						<p>O: <?php echo do_shortcode('[types field="agent-office-phone"]'); ?></p>

						<p>M: <?php echo do_shortcode('[types field="agent-mobile-phone"]'); ?></p>

						<p><a href="<?php echo types_render_field('agent-website', $param); ?> " target="_blank"><?php echo types_render_field('agent-website', $param); ?></a></p>

						

				</div>

			</div>

			

			<div id="agency-info">

				<div id="agency-image">

					<a href="<?php echo types_render_field('agency-website', $param); ?>">

						<?php echo do_shortcode('[types field="agency-logo" width="182"]'); ?>

					</a>

				</div>

					<p><?php echo do_shortcode('[types field="agency-address"]'); ?><br />

					Austin, TX 78737</p>

					<p><?php echo do_shortcode('[types field="agency-phone-number"]'); ?></p>

					<p><?php echo do_shortcode('[types field="agency-toll-free-number"]'); ?></p>

					<p><a href="<?php echo types_render_field('agency-website', $param);?>" target="_blank">moreland.com</a></p>

		</div>

        

     

        <?php endwhile; ?>

        <?php wp_reset_query(); ?>

	</div><!-- #footer -->

	

    <?php

    

    // action hook for placing content below the footer

    thematic_belowfooter();

    

    if (apply_filters('thematic_close_wrapper', true)) {

    	echo '</div><!-- #wrapper .hfeed -->';

    }

    

    ?>  



<?php 



// calling WordPress' footer action hook

wp_footer();



// action hook for placing content before closing the BODY tag

thematic_after(); 



?>



<!--GOOGLE ANALYTICS-->



</body>

</html>