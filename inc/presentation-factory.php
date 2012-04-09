<?php 
/**
 * Singleton that registers and instantiates Abstract_Presentation classes.
 *
 * @package WordPress
 * @subpackage Presentation
 * @since 2.8
 */
class Presentation_Factory {
	private $presentations = array();
	private $presentation_objects = array();

	function __construct() {
		
	}

	function register( $presentation_class ) {
		$object = new $presentation_class();

		$this->presentation_objects[ $presentation_class ] = $object;
		$this->presentations[ $object->get_id() ] = $object;
	}

	function unregister( $presentation_class ) {
		if ( isset( $this->presentation_objects[ $presentation_class ] ) ) {
			$object = $this->presentation_objects[ $presentation_class ];

			unset( $this->presentations[ $object->get_id() ] );
			unset( $this->presentation_objects[ $presentation_class ] );
		}
	}

	function get_presentation_objects() {
		return $this->presentation_objects;
	}

	function get_presentation_object( $presentation_id ) {
		if( isset( $this->presentations[ $presentation_id ] ) ) {
			return $this->presentations[ $presentation_id ];
		}
		return null;
	}
}

$presentation_factory = new Presentation_Factory;


/**
 * Get all Presentation views
 *
 * @since 1.0.0
 *
 * @see Abstract_Presentation
 * @see Presentation_Factory
 * @uses Presentation_Factory
 *
 */
function get_presentations() {
	global $presentation_factory;
	return $presentation_factory->get_presentation_objects();
}

/**
 * Register a Presentation view
 *
 * Registers a Abstract_Presentation object
 *
 * @since 1.0.0
 *
 * @see Abstract_Presentation
 * @see Presentation_Factory
 * @uses Presentation_Factory
 *
 * @param string $presentation_class The name of a class that extends Presentation_Factory
 */
function register_presentation( $presentation_class ) {
	global $presentation_factory;

	$presentation_factory->register( $presentation_class );
}


function get_current_presentation() {
	global $post, $presentation_factory;
	return $presentation_factory->get_presentation_object( get_post_meta( $post->ID, '_style', true ) );
}

?>