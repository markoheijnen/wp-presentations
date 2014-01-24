<?php

class MH_Presentations_Presentations {

	function __construct() {
		//init variables
		$this->pluginURL = plugins_url( '/', dirname( __FILE__ ) );

		//init hooks
		add_action( 'wp_loaded', array( $this, 'presentation_to_slide' ) );
		add_action( 'init', array( $this, 'create_posttype' ) );
		add_action( 'save_post', array( $this, 'metabox_save' ) );
		//add_action( 'admin_print_scripts', array( $this, 'editScripts' ) );

		//add filter to insure the text of custom post types will displayed correctly when user updates
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
	}

	public function presentation_to_slide() {
		if ( !function_exists( 'p2p_register_connection_type' ) )
			return;

		p2p_register_connection_type( array(
			'name' => 'presentation_to_slide',
			'from' => 'presentations',
			'to' => 'slides',
			'sortable' => 'from',
			'fields' => array(
					'data-x' => 'data-x',
					'data-y' => 'data-y',
					'data-z' => 'data-z',
					'data-scale' => 'data-scale',
					'data-rotate' => 'data-rotate'
				)
		) );
	}

	public function create_posttype() {
		$labelsPresentations = array(
		    'name' => __( 'Presentations', 'MHPresentatie' ),
		    'singular_name' => __( 'Presentation', 'MHPresentatie' ),
		    'add_new' => __( 'Add new', 'MHPresentatie' ),
		    'add_new_item' => __( 'Add New presentation', 'MHPresentatie' ),
		    'edit_item' => __( 'Edit presentation', 'MHPresentatie' ),
		    'new_item' => __( 'New presentation', 'MHPresentatie' ),
		    'view_item' => __( 'View presentation', 'MHPresentatie' ),
		    'search_items' => __( 'Search presentations', 'MHPresentatie' ),
		    'not_found' =>  __( 'No presentations found', 'MHPresentatie' ),
		    'not_found_in_trash' => __( 'No presentations found in Trash', 'MHPresentatie' ), 
		    'parent_item_colon' => '',
		    'menu_name' => 'Presentations'
		    );

		$args = array(
			'labels' => $labelsPresentations,
			'public' => true,
			'show_ui' => true,
			'menu_position'   => 50,
			'capability_type' => 'post',
			'rewrite' => array( 'slug' => 'presentations', 'with_front' => true ),
			'register_meta_box_cb' => array( $this, 'createMetaBoxes' ),
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'menu_icon' => $this->pluginURL . 'images/icon.png',
			'has_archive' => true
		);
		register_post_type( 'presentations' , $args );
	}


	function updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['presentations'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Presentation updated. <a href="%s">View presentation</a>' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.' ),
			3 => __( 'Custom field deleted.' ),
			4 => __( 'Presentation updated.' ),

			/* translators: %s: date and time of the revision */
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Presentation restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Presentation published. <a href="%s">View presentation</a>' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Presentation saved.' ),
			8 => sprintf( __( 'Presentation submitted. <a target="_blank" href="%s">Preview presentation</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Presentation scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview presentation</a>'),

			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Presentation draft updated. <a target="_blank" href="%s">Preview presentation</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}


	public function createMetaBoxes() {
		add_meta_box( 'MHPresentatie_style', __( 'Presentation style', 'MHPresentatie' ), array( $this, 'metabox_style' ), 'presentations', 'side', 'core' );
		add_meta_box( 'MHPresentatie_css', __( 'CSS styling', 'MHPresentatie' ), array( $this, 'metabox_css' ), 'presentations', 'normal', 'high' );
	}

	function editScripts() {
		global $post;
		if ( isset( $_GET['post'], $_GET['action'] ) && $_GET['action'] == "edit" && $post->post_type == "presentations" ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-sortable' );
		}
	}

	function metabox_style( $post ) {
		$presentations = get_presentations();
		$style = get_post_meta( $post->ID , '_style', true );

		echo '<select name="presentation_style">';
		foreach( $presentations as $presentation ) {
			if( $style == $presentation->get_id() ) {
				echo '<option value="' . $presentation->get_id() . '" selected="selected">' . $presentation->get_name() . '</option>';
			}
			else {
				echo '<option value="' . $presentation->get_id() . '">' . $presentation->get_name() . '</option>';
			}
		}
		echo '</select>';
	}

	function metabox_css( $post ) {
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'mhPresentatie_noncename' );

		$css = get_post_meta( $post->ID , '_css', true );

		echo '<textarea name="presentationCSS" rows="10" style="width:100%; max-width: 100%;">';
		echo $css;
		echo '</textarea>';
	}

	function metabox_save( $post_id ) {
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		global $wpdb;
		
		if( ! isset( $_POST['mhPresentatie_noncename'] ) || ! wp_verify_nonce( $_POST['mhPresentatie_noncename'], plugin_basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		{
			return $post_id;
		}

		// Check permissions
		if ( !current_user_can( 'edit_post', $post_id ) )
		{
			return $post_id;
		}

		update_post_meta( $post_id, '_style', $_POST['presentation_style'] );
		update_post_meta( $post_id, '_css', $_POST['presentationCSS'] );
	}

}


?>