<?php global $template_object; ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<title><?php wp_title(); ?></title>

	<link href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

	<?php
		wp_head();
	?>
</head>
<body>
		<div id="reveal">
			
			<!-- Any section element inside of this container is displayed as a slide -->
			<div class="slides">
			<?php
			$connected = new WP_Query( array(
							'connected_type' => 'presentation_to_slide',
							'connected_items' => get_queried_object_id(),
							'nopaging' => true,
						) );
		
			if ( $connected->have_posts() ) {
				while ( $connected->have_posts() ) {
					$connected->the_post();
					echo $template_object->show_slide( $connected->post );
				}
			}
			?>
			</div>

			<!-- The navigational controls UI -->
			<aside class="controls">
				<a class="left" href="#">&#x25C4;</a>
				<a class="right" href="#">&#x25BA;</a>
				<a class="up" href="#">&#x25B2;</a>
				<a class="down" href="#">&#x25BC;</a>
			</aside>

			<!-- Displays presentation progress, max value changes via JS to reflect # of slides -->
			<div class="progress"><span></span></div>
			
		</div>
		
<?php wp_footer(); ?>

		<script>
			// Parse the query string into a key/value object
			var query = {};
			location.search.replace( /[A-Z0-9]+?=(\w*)/gi, function(a) {
				query[ a.split( '=' ).shift() ] = a.split( '=' ).pop();
			} );

			Reveal.initialize({
				// Display controls in the bottom right corner
				controls: true,

				// Display a presentation progress bar
				progress: true,

				// If true; each slide will be pushed to the browser history
				history: true,

				// Flags if mouse wheel navigation should be enabled
				mouseWheel: false,

				// Apply a 3D roll to links on hover
				rollingLinks: false,

				// UI style
				theme: query.theme || 'marko', // default/neon

				// Transition style
				transition: query.transition || 'default' // default/cube/page/concave/linear(2d)
			});
		</script>
</body>
</html>