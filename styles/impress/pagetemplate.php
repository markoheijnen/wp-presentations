<?php global $template_object; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title><?php wp_title(); ?></title>

    <link href="http://fonts.googleapis.com/css?family=Open+Sans:regular,semibold,italic,italicsemibold|PT+Sans:400,700,400italic,700italic|PT+Serif:400,700,400italic,700italic" rel="stylesheet" />
    
    <link rel="apple-touch-icon" href="apple-touch-icon.png" />
    
    <?php
    	wp_head();
    ?>
</head>
<body>

<div id="impress" class="impress-not-supported">

    <div class="fallback-message">
        <p>Your browser <b>doesn't support the features required</b> by impress.js, so you are presented with a simplified version of this presentation.</p>
        <p>For the best experience please use the latest <b>Chrome</b> or <b>Safari</b> browser. Firefox 10 (to be released soon) will also handle it.</p>
    </div>

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

    <?php $template_object->show_overview(); ?>

</div>

<?php if( ! is_user_logged_in() ) { ?>
<div class="hint">
    <p>Use a spacebar or arrow keys to navigate</p>
</div>
<script>
if ("ontouchstart" in document.documentElement) { 
    document.querySelector(".hint").innerHTML = "<p>Tap on the left or right to navigate</p>";
}
</script>
<?php
}

wp_footer();
?>

<script>var api = impress();</script>

</body>
</html>