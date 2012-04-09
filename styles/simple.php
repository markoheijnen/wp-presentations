<?php

class Presentation_Simple extends Abstract_Presentation {

	function __construct() {
		parent::__construct( 'simple', 'A simple presentation' );
	}

	function add_scripts() {
		$plugin = plugin_dir_url( __FILE__ );

		// Add CSS
		wp_enqueue_style('presentations', $plugin . 'simple/style.css');

		// Add JavaScript
		wp_enqueue_script( 'jquery-scrollTo', $plugin . 'simple/jquery.scrollTo-min.js', array( 'jquery' ), '1.4.2', true );
		wp_enqueue_script( 'presentations', $plugin . 'simple/main.js', array( 'jquery', 'jquery-scrollTo' ), false, true );
	}

	function show_slide( $slide ) {
		$out  = '<div id="' . $slide->ID . '" class="slide">';
		$out .= '<div class="slide-content">';
		//$out .= '<h2>'. $slide->post_title .'</h2>';
		$out .= do_shortcode( $slide->post_content );
		$out .= '</div></div>';
		return $out;
	}
}


?>