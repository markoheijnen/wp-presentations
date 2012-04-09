<?php

class Presentation_Impress extends Abstract_Presentation {
	private $minimal_x = null;
	private $maximal_x = null;
	private $minimal_y = null;
	private $maximal_y = null;
	private $minimal_z = null;
	private $maximal_z = null;

	function __construct() {
		parent::__construct( 'impress', 'Impress presentation' );
	}

	function get_template() {
		return plugin_dir_path( __FILE__ ) . 'impress/pagetemplate.php';
	}

	function add_scripts() {
		$plugin = plugin_dir_url( __FILE__ );

		wp_enqueue_script( 'presentations', $plugin . 'impress/impress.js', array( ), false, true );
	}

	function add_styles() {
		$plugin = plugin_dir_url( __FILE__ );

		wp_enqueue_style( 'presentations', $plugin . 'impress/impress-demo.css' );
	}

	function show_slide( $slide ) {
		$options = $this->get_slide_options();

		$out  = '<div class="step slide"';
		if( isset( $options['data-x'] ) ) {
			$out .= ' data-x="' . $options['data-x'] . '"';

			if( (int)$options['data-x'] < $this->minimal_x || $this->minimal_x == null ) {
				$this->minimal_x = (int)$options['data-x'];
			}
			if( (int)$options['data-x'] > $this->maximal_x || $this->maximal_x == null ) {
				$this->maximal_x = (int)$options['data-x'];
			}
		}
		if( isset( $options['data-y'] ) ) {
			$out .= ' data-y="' . $options['data-y'] . '"';
			
			if( (int)$options['data-y'] < $this->minimal_y || $this->minimal_y == null ) {
				$this->minimal_y = (int)$options['data-y'];
			}
			if( (int)$options['data-y'] > $this->maximal_y || $this->maximal_y == null ) {
				$this->maximal_y = (int)$options['data-y'];
			}
		}
		if( isset( $options['data-z'] ) ) {
			$out .= ' data-z="' . $options['data-z'] . '"';
			
			if( (int)$options['data-z'] < $this->minimal_z || $this->minimal_z == null ) {
				$this->minimal_z = (int)$options['data-z'];
			}
			if( (int)$options['data-z'] > $this->maximal_z || $this->maximal_z == null ) {
				$this->maximal_z = (int)$options['data-z'];
			}
		}
		if( isset( $options['data-scale'] ) ) {
			$out .= ' data-scale="' . $options['data-scale'] . '"';
		}
		if( isset( $options['data-rotate'] ) ) {
			$out .= ' data-rotate="' . $options['data-rotate'] . '"';
		}
		$out .= '>';

		$content = apply_filters( 'the_content', $slide->post_content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		$out .= '<q>' . do_shortcode( $content ) . '</q>';

		$out .= '</div>';

		return $out;
	}

	function disable_adminbar() {
		return true;
	}

	
	function show_overview() {
		$scale_x = $scale = ( ( $this->maximal_x + 900 ) - $this->minimal_x ) / 900;
		$scale_y = ( ( $this->maximal_y + 700 )  - $this->minimal_y ) / 700;
		if( $scale_y > $scale_x ) {
			$scale = $scale_y;
		}

		echo '<div id="overview" class="step" data-x="' . ( ( $this->maximal_x - $this->minimal_x ) / 2 + $this->minimal_x ) . '" data-y="' . ( ( $this->maximal_y - $this->minimal_y ) / 2 + $this->minimal_y ) . '" data-scale="' . $scale . '"></div>';
	}
}


?>