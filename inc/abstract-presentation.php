<?php

class Abstract_Presentation {
	private $id_base;				// Root id for all presentations of this type.
	private $name;					// Name for this presentation type.
	private $option_name;
	private $presentation_options;

	protected $slide_counter = 1;

	private $number = false;	// Unique ID number of the current instance.
	private $id = false;		// Unique ID string of the current instance (id_base-number)

	function __construct( $id_base = false, $name, $presentation_options = array(), $control_options = array() ) {
		$this->id_base = empty($id_base) ? strtolower(get_class($this)) : strtolower($id_base);
		$this->name = $name;
		$this->option_name = 'presentations_' . $this->id_base;
		$this->presentation_options = wp_parse_args( $presentation_options, array('classname' => $this->option_name) );
	}

	function get_id() {
		return $this->id_base;
	}

	function get_name() {
		return $this->name;
	}

	function get_template() {
		return false;
	}

	function disable_adminbar() {
		return false;
	}

	function add_scripts() { }
	function add_styles() { }
	function show_slide( $slide ) {
		if( $slide->post_type != 'slides' ) {
			return;
		}

		$this->slide_counter++;
	}

	function get_children( $slide ) {
		$children         = array();
		$children_string  = get_post_meta( $slide->ID , '_children', true );
		$children_explode = explode( ",", $children_string );

		foreach( $children_explode as $child ) {
			$child = intval( trim( $child ) );
			if( $child > 0 ) {
				array_push( $children, $child );
			}
		}

		return $children;
	}

	function get_slide_options() {
		global $post;

		$default_meta = (array) get_post_meta( $post->ID, $this->option_name, true );
		$p2p_meta = p2p_get_meta( $post->p2p_id, null, true );

		foreach( $p2p_meta as $field_name => $field_data ) {
			if( isset( $field_data[0] ) && ( !empty( $field_data[0] ) || $field_data[0] == 0 ) ) {
				$default_meta[ $field_name ] = $field_data[0];
			}
		}
		
		return $default_meta;
	}
}

?>