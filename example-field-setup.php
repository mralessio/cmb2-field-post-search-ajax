<?php
/*
 * Example setup for cmb2 custom field : post search ajax.
 */
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function cmb2_post_search_ajax_metaboxes_example() {
	
	$example_meta = new_cmb2_box( array(
		'id'           => 'cmb2_post_search_ajax_field',
		'title'        => __( 'Related Posts', 'cmb2' ),
		'object_types' => array( 'post' ), // Post type
		'context'      => 'normal',
		'priority'     => 'high',
		'show_names'   => true, // Show field names on the left
	) );
	
	$example_meta->add_field( array(
		'name'      	=> __( 'Example Multiple', 'cmb2' ),
		'id'        	=> 'cmb2_post_search_ajax_demo_multiple',
		'type'      	=> 'post_search_ajax',
		'desc'			=> __( '(Start typing post title)', 'cmb2' ),
		// Optional :
		'limit'      	=> 10, 		// Limit selection to X items only (default 1)
		'sortable' 	 	=> true, 	// Allow selected items to be sortable (default false)
		'query_args'	=> array(
			'post_type'			=> array( 'post' ),
			'post_status'		=> array( 'publish' ),
			'posts_per_page'	=> -1
		)
	) );
	
	$example_meta->add_field( array(
		'name'      	=> __( 'Example Single', 'cmb2' ),
		'id'        	=> 'cmb2_post_search_ajax_demo_single',
		'type'      	=> 'post_search_ajax',
		'desc'			=> __( '(Start typing post title)', 'cmb2' ),
		// Optional :
		'limit'      	=> 1, 		// Limit selection to X items only (default 1)
		'sortable' 	 	=> false, 	// Allow selected items to be sortable (default false)
		'query_args'	=> array(
			'post_type'			=> array( 'post' ),
			'post_status'		=> array( 'publish' ),
			'posts_per_page'	=> -1
		)
	) );

	$example_meta->add_field( array(
		'name'      	=> __( 'Test user multiple', 'cmb2' ),
		'id'        	=> 'cmb2_post_search_ajax_demo_user_multiple',
		'type'      	=> 'post_search_ajax',
		'desc'			=> __( '(Start typing post title)', 'cmb2' ),
		// Optional :
		'limit'      	=> 10, 		// Limit selection to X items only (default 1)
		'sortable' 	 	=> true, 	// Allow selected items to be sortable (default false)
		'object_type'	=> 'user',	// Define queried object type (Available : post, user, term - Default : post)
		'query_args'	=> array(
			'blog_id' => '1',
		)
	) );

	$example_meta->add_field( array(
		'name'      	=> __( 'Test user single', 'cmb2' ),
		'id'        	=> 'cmb2_post_search_ajax_demo_user_single',
		'type'      	=> 'post_search_ajax',
		'desc'			=> __( '(Start typing post title)', 'cmb2' ),
		// Optional :
		'limit'      	=> 1, 		// Limit selection to X items only (default 1)
		'sortable' 	 	=> false, 	// Allow selected items to be sortable (default false)
		'object_type'	=> 'user',	// Define queried object type (Available : post, user, term - Default : post)
		'query_args'	=> array(
			 'role' => 'Administrator'
		)
	) );
	
}
add_action( 'cmb2_init', 'cmb2_post_search_ajax_metaboxes_example' );


/ Templating for Autocomplete examples

// For simplicity you can use short tags {{link}} and {{value}}. But {{value}} is necessary if you want Autocomplete highlighting works!
// In case of many posts and a complex template it'd be a good idea to limit a number of retured posts to speed things up  ('posts_per_page' => 20)

// Make template for all 'post_search_ajax' fields with 'object_type' => 'post'
function post_search_custom_template_demo( $tmpl, $id, $field_id, $object ) {

	if ( 'post' !== $object ) {
		return;
	}

	$img_id = get_post_thumbnail_id( $id );
	$img_src = wp_get_attachment_image_src( $img_id, $size = 'widget-small' );
	$author_id = get_post_field( 'post_author', $id );
	$img_author = get_avatar( $author_id, 32, '', '', array(
		'class' => 'media-author-xs',
	) );

	ob_start();	?>
	
	<div class="media media-middle">
		<div class="media-left"><img src="<?php echo esc_url( $img_src[0] ); ?>" class="media-object"></div>
		<div class="media-body">
		<?php if ( 'mag_cmb_post_search_template' == current_filter() ) : ?>
			<a href="{{link}}" target="_blank" class="edit-link"><div class="media-heading">{{value}}</div></a>
		<?php else : ?>
			<div class="media-heading">{{value}}</div>
		<?php endif; ?>
	  		<span class="media-subtext"><?php echo $img_author; ?>&nbsp;
	  			<?php echo esc_attr( get_the_author_meta( 'display_name', $author_id ) ); ?> &bull; <i class="fa fa-clock-o" aria-hidden="true"></i>
	  			<?php echo esc_attr( get_the_date( '', $id ) ); ?>
	  		</span>
		</div>
	</div>
	
	<?php
	return ob_get_clean();
}
add_filter( 'mag_cmb_post_search_template', 'post_search_custom_template_demo', 10, 4 );
add_filter( 'mag_cmb_post_search_results_template', 'post_search_custom_template_demo', 10, 4 );


// Make template for 'cmb2_post_search_ajax_demo_user_multiple' field.
function user_search_custom_template_demo( $tmpl, $author_id, $field_id, $object ) {

	return sprintf('<a href="{{link}}" target="_blank" class="edit-link"><div class="media"><div class="media-left">%s</div>
	  <div class="media-body media-middle">{{value}}</div></div></a>',
		get_avatar( $author_id, 32 )
	);

}
// Hooks 'mag_{$field_id}_search_template' and 'mag_{$field_id}_search_template_results'
add_filter( 'mag_cmb2_post_search_ajax_demo_user_multiple_search_template', 'user_search_custom_template_demo', 10, 4 );
add_filter( 'mag_cmb2_post_search_ajax_demo_user_multiple_search_results_template', 'user_search_custom_template_demo', 10, 4 );

// Enqueue templates styles.
function mag_cmb2_post_search_template_styles() {
	wp_enqueue_style( 'mag-post-search-ajax-tmpl', plugins_url( 'cmb2-field-post-search-ajax/css/search-ajax-template.css' ), '' );
}

add_action( 'admin_enqueue_scripts', 'mag_cmb2_post_search_template_styles' );