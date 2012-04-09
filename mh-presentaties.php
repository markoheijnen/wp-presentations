<?php
/**
 * @package MH - Presentaties
 * @version 1.0
 */
/*
Plugin Name: MH Presentaties
Plugin URI: http://presentaties.markoheijnen.com
Description: Tool om presentaties bij te houden.
Author: Marko Heijnen
Version: 1.0
Author URI: http://markoheijnen.com

Depends: posts-to-posts
*/

include 'inc/presentation-factory.php';
include 'inc/abstract-presentation.php';

include 'styles/reveal.php';
include 'styles/impress.php';
include 'styles/simple.php';

include "posttypes/presentations.php";
include "posttypes/slides.php";

class MH_Presentations {
		private $pluginURL;

		function __construct() {
			//init hooks
			add_action( 'init', array( &$this, 'do_register_presentations' ) );
			add_action( 'presentations_init', array( &$this, 'register_presentations' ) );
			add_action( 'wp', array( &$this, 'init_frontend_functions' ), 99 );

			// Register post types, creating meta boxes and data functions
			new MH_Presentations_Presentations();
			new MH_Presentations_Slides();
		}

		function do_register_presentations() {
			do_action( 'presentations_init' );
		}

		function register_presentations() {
			register_presentation( 'Presentation_Simple' );
			register_presentation( 'Presentation_Impress' );
			register_presentation( 'Presentation_Reveal' );
		}

		function init_frontend_functions() {
			if( is_single() && 'presentations' == get_post_type() && post_password_required() == false ) {
				$template_object = get_current_presentation();

				if( $template_object != null ) {
					if( $template_object->get_template() ) {
						add_filter( 'template_include', array( &$this, 'presentation_template' ) );
					}
					else {
						add_action( 'the_content', array( &$this, 'the_content' ) );
					}
	
					add_action( 'wp_enqueue_scripts', array( $template_object, 'add_scripts' ), 11 );
					add_action( 'wp_enqueue_scripts', array( $template_object, 'add_styles' ), 11 );
					
					if( $template_object->disable_adminbar() == true ) {
						show_admin_bar( false );
						add_filter( 'show_admin_bar', '__return_false' );
						remove_action( 'wp_head', 'wp_admin_bar_header' );
						remove_action( 'wp_head', '_admin_bar_bump_cb' );
					}
				}
			}
		}

		function presentation_template( $path ) {
			global $template_object;

			$template_object = get_current_presentation();
			$path = $template_object->get_template();

			return $path;
		}

		function the_content($content) {
			global $post;

			if( get_post_type( $post ) == "presentations" AND ! locate_template( array( 'single-presentations.php' ), true ) ) {
				return $this->show_presentation( $post );
			}
			return $content;
		}

		public function show_presentation( $post ) {
			$template_object = get_current_presentation();
			$presentationInfo = get_post_meta( $post->ID , '_mhPresentationInfo', true );

			if( ! empty($presentationInfo['css'] ) ) {
				echo '<style type="text/css">';
				echo $presentationInfo['css'];
				echo '</style>';
			}

			$out = $post->post_content;

			if( is_single() ) {
				$out.= '<div id="presentation-' . $post->ID . '" class="presentation">';
				$out.= '<div class="presentation-content">';

				$connected = new WP_Query( array(
					'connected_type' => 'presentation_to_slide',
					'connected_items' => get_queried_object_id(),
					'nopaging' => true,
				) );

				if ( $connected->have_posts() ) {
					while ( $connected->have_posts() ) {
						$connected->the_post();

						$out.= $template_object->show_slide( $connected->post );
					}
				}
				wp_reset_postdata();

				$out.= '</div></div>';
			}

			return $out;
		}
}

new MH_Presentations;

?>