<?php

class Presentation_Reveal extends Abstract_Presentation {
	function __construct() {
		parent::__construct( 'reveal', 'Reveal.js presentation' );
	}

	function get_template() {
		return plugin_dir_path( __FILE__ ) . 'reveal/pagetemplate.php';
	}

	function add_scripts() {
		$plugin = plugin_dir_url( __FILE__ );

		wp_enqueue_script( 'presentations', $plugin . 'reveal/reveal.js', array( ), false, true );
	}

	function add_styles() {
		$plugin = plugin_dir_url( __FILE__ );

		wp_enqueue_style( 'presentations.reset', $plugin . 'reveal/reset.css' );
		wp_enqueue_style( 'presentations', $plugin . 'reveal/main.css' );
	}

	function show_slide( $slide, $show_children = true ) {
		parent::show_slide( $slide );

		$out = '';
		$children = array();

		if( $show_children == true ) {
			$children = $this->get_children( $slide );
		}

		if( count( $children ) > 0 ) {
			$out .= '<section>';
		}

		$out .= '<section id="slide-' . $this->slide_counter . '">';
		$content = apply_filters( 'the_content', $slide->post_content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		
		$out .= html_entity_decode( $content );

		if( $this->slide_counter == 1 ) {
			$out .= '<script>';
			$out .= 'if( navigator.userAgent.match( /(iPhone|iPad|iPod|Android)/i ) )';
			$out .= 'document.write( \'<p style="color: rgba(0,0,0,0.3); text-shadow: none;">('+'Tap to navigate'+')</p>\' );';
			$out .= '</script>';
		}
		$out .= '</section>';

		if( count( $children ) > 0 ) {
			foreach( $children as $child ) {
				$out .= $this->show_slide( get_post( $child ), false );
			}

			$out .= '</section>';
		}

		return $out;
	}

	function disable_adminbar() {
		return true;
	}
}


?>