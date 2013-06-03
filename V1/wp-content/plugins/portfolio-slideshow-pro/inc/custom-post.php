<?php 
if ( ! function_exists( 'ps_custom_post_type_functions' ) ) { 
	function ps_custom_post_type_functions() { //callback for custom post type for our js, css, and metabox.
		global $ps_options;
		wp_register_style( 'ps-custom-post-type', plugins_url( 'admin/css/ps-custom-post-type.css', dirname(__FILE__) ), false, $ps_options[version], 'screen' ); 
		wp_enqueue_style( 'ps-custom-post-type' );
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_register_script( 'ps-custom-post-type', plugins_url( 'admin/js/ps-custom-post-type.js', dirname(__FILE__) ), false, $ps_options[version], true); 
		wp_enqueue_script( 'ps-custom-post-type' );	
		add_meta_box('ps_custom_post_type_uploads', 'Upload Images', 'ps_custom_post_type_uploads', 'portfolio_slideshow', 'normal', 'default');
	}
	
	function ps_custom_post_type_uploads() {
		global $post;
	 
		echo '<p>
	
		 <a href="media-upload.php?post_id='.$post->ID.'&#038;type=image&#038;TB_iframe=1"  class="thickbox" ><input type="submit" name="Save" value="Upload and manage images" class="button-secondary" /></a></p><br />';
	
		$attachments = get_posts( array(
			'order'          => 'ASC',
			'orderby' 		 => 'menu_order ID',
			'post_type'      => 'attachment',
			'post_parent'    => $post->ID,
			'post_mime_type' => 'image',
			'post_status'    => null,
			'numberposts'    => -1,
			'size'			 => 'thumbnail') 
		);
	
	  if ( $attachments ) {
				echo '<ul id="images">';
			$i = 0;
			foreach ( $attachments as $attachment ) {
				echo '<li id="'. $attachment->ID .'">' . wp_get_attachment_image( $attachment->ID, array(50,50), false, false) . '</li>';
				$i++;
			}
			echo '</ul><div class="instructions"><small>Drag and drop to re-order</small><p><strong>Instructions</strong><br />To add this slideshow to a post, use the shortcode <code>[portfolio_slideshow id='.$post->ID.']</code>. To add it to a widget, use slideshow ID <strong>'.$post->ID.'</strong>. To add it directly to your template (header, sidebar, homepage, etc.), add this PHP to your template:<br /><code>&lt;?php echo do_shortcode(\'[portfolio_slideshow id='.$post->ID.']\');?&gt;</code></div>';
		} else { echo '<div class="instructions"><p>Be sure to save your changes in the gallery uploader, then click "Save Draft" to update this page for further instructions.</p></div>';}
	}
	
	/*
	Show IDs on custom post type list
	Based on: http://sivel.net/wordpress/simply-show-ids/
	
	Copyright (c) 2009-2010 Matt Martz (http://sivel.net)
	Simply Show IDs is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl-2.0.txt
	*/
	
	if ( is_admin() && isset( $_GET['post_type'] ) && $_GET['post_type'] == "portfolio_slideshow" ) { //no need to run this unless we're on our custom post type list
	
		// Prepend the new column to the columns array
		function ssid_column($cols) {
			$cols['ssid'] = 'ID';
			return $cols;
		}
	
		// Echo the ID for the new column
		function ssid_value($column_name, $id) {
			if ($column_name == 'ssid')
				echo $id;
		}
	
		function ssid_return_value($value, $column_name, $id) {
			if ($column_name == 'ssid')
				$value = $id;
			return $value;
		}
	
		// Output CSS for width of new column
		function ssid_css() {
		?>
		<style type="text/css">
			#ssid { width: 50px; } /* Simply Show IDs */
		</style>
		<?php	
		}
	
		// Actions/Filters for various tables and the css output
		function ssid_add() {
			add_action('admin_head', 'ssid_css');
	
			add_filter('manage_posts_columns', 'ssid_column');
			add_action('manage_posts_custom_column', 'ssid_value', 10, 2);
	
			add_filter('manage_pages_columns', 'ssid_column');
			add_action('manage_pages_custom_column', 'ssid_value', 10, 2);
	
			add_filter('manage_media_columns', 'ssid_column');
			add_action('manage_media_custom_column', 'ssid_value', 10, 2);
	
			add_filter('manage_link-manager_columns', 'ssid_column');
			add_action('manage_link_custom_column', 'ssid_value', 10, 2);
	
			add_action('manage_edit-link-categories_columns', 'ssid_column');
			add_filter('manage_link_categories_custom_column', 'ssid_return_value', 10, 3);
	
			foreach ( get_taxonomies() as $taxonomy ) {
				add_action("manage_edit-${taxonomy}_columns", 'ssid_column');			
				add_filter("manage_${taxonomy}_custom_column", 'ssid_return_value', 10, 3);
			}
	
			add_action('manage_users_columns', 'ssid_column');
			add_filter('manage_users_custom_column', 'ssid_return_value', 10, 3);
	
			add_action('manage_edit-comments_columns', 'ssid_column');
			add_action('manage_comments_custom_column', 'ssid_value', 10, 2);
		}
	
		add_action('admin_init', 'ssid_add');
	
	}
	
	function my_save_item_order() { //to save when we update the sort order
		global $wpdb;
	
		$order = explode(',', $_POST['order']);
		$counter = 0;
		foreach ($order as $item_id) {
	
			$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $item_id) );
			$counter++;
		}
		die(1);
	}
	add_action('wp_ajax_item_sort', 'my_save_item_order');
	add_action('wp_ajax_nopriv_item_sort', 'my_save_item_order');
	
	function ps_taxonomy_init() { // create a new taxonomy
		register_taxonomy(
			'raygun_slideshows',
			'portfolio_slideshow',
			array(
				'label' => __( 'Tags' ),
				'sort' => true,
				'args' => array( 'orderby' => 'term_order' ),
				'rewrite' => array( 'slug' => 'raygun_tags' )
			)
		);
	}
add_action( 'init', 'ps_taxonomy_init' );

	
	// Custom Post Type	
	if ( function_exists( 'register_post_type' ) ) {
		
		add_action( 'init', 'ps_create_post_type' );
	
		function ps_create_post_type() {
			register_post_type( 'portfolio_slideshow',
				array(
					'labels' => array(
						'name' => __( 'Slideshows' ),
						'singular_name' => __( 'Slideshow' ),
						'add_new_item'	=> __( 'Add new slideshow' )
					),
				'menu_icon' => plugins_url( 'admin/img/add-an-image.gif', dirname(__FILE__) ),
				'show_ui' => true,
				'public' => false,
				'has_archive' => false,
				'register_meta_box_cb' => 'ps_custom_post_type_functions'
				)
			);
		}
	}
}
?>