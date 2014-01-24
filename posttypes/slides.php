<?php

class MH_Presentations_Slides {

	function __construct() {
		//init variables
		$this->pluginURL = plugins_url( '/', dirname( __FILE__ ) );

		//init hooks
		add_action( 'init', array( $this, 'create_posttype' ) );

		//add filter to insure the text of custom post types will displayed correctly when user updates
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );

		add_shortcode( 'slide', array( $this, 'shortcode_slide' ) );
	}

	public function create_posttype() {
		$labelsSlides = array(
		    'name' => __( 'Slides', 'MHPresentatie' ),
		    'singular_name' => __( 'Slide', 'MHPresentatie' ),
		    'add_new' => __( 'Add new', 'MHPresentatie' ),
		    'add_new_item' => __( 'Add New slide', 'MHPresentatie' ),
		    'edit_item' => __( 'Edit slide', 'MHPresentatie' ),
		    'new_item' => __( 'New slide', 'MHPresentatie' ),
		    'view_item' => __( 'View slide', 'MHPresentatie' ),
		    'search_items' => __( 'Search slides', 'MHPresentatie' ),
		    'not_found' =>  __( 'No slides found', 'MHPresentatie' ),
		    'not_found_in_trash' => __( 'No slides found in Trash', 'MHPresentatie' ), 
		    'parent_item_colon' => '',
		    'menu_name' => 'Slides'
		    );
  
		$args = array(
			'labels' => $labelsSlides,
			'public' => false,
			'public_queryable' => false,
			'show_ui' => true,
			'menu_position'   => 50,
			'capability_type' => 'post',
			'rewrite' => false,
			'supports' => array( 'title', 'editor' ),
			'menu_icon' => $this->pluginURL . 'images/icon.png'
		);
		register_post_type( 'slides' , $args );
	}

	function updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['slides'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Slide updated. <a href="%s">View slide</a>'), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Slide updated.'),

			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Slide restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Slide published. <a href="%s">View slide</a>'), esc_url( get_permalink($post_ID) ) ),
			7 => __('Slide saved.'),
			8 => sprintf( __('Slide submitted. <a target="_blank" href="%s">Preview slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Slide scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview slide</a>'),

			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __('Slide draft updated. <a target="_blank" href="%s">Preview slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);

		return $messages;
	}


	function add_meta_box() {
		add_meta_box(
			'slide_children',
			__( 'Children', 'mh-presentaties' ),
			array( $this, 'metabox_children' ),
			'slides',
			'side',
			'core'
	    );
	}

	function metabox_children( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'nonce_slide_children' );
		$children = get_post_meta( $post->ID , '_children', true );

		// The actual fields for data entry
		echo '<label for="myplugin_new_field">';
			_e( 'Children IDs', 'mh-presentaties' );
		echo '</label> ';
		echo '<input type="text" id="slides_children" name="slides_children" value="' . $children . '" size="25" />';
	}

	function save_meta_box( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;

		if ( ! isset( $_POST['nonce_slide_children'] ) || !wp_verify_nonce( $_POST['nonce_slide_children'], plugin_basename( __FILE__ ) ) )
			return;

		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		$children = esc_attr( $_POST['slides_children'] );
		update_post_meta( $post_id , '_children', $children );
	}








	function shortcode_slide( $args ) {
		global $template_object;

		if( !isset( $args['id'] ) && intval( $args['id'] ) == 0 ) {
			return;
		}

		$slide = get_post( $args['id'] );
		if( $slide->post_type != 'slides' ) {
			return;
		}

		return $template_object->show_slide( $slide );
	}
}


?>