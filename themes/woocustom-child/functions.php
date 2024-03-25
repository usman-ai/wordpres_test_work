<?php
// Enqueue parent theme's stylesheet
add_action( 'wp_enqueue_scripts', 'enqueue_parent_theme_styles' );
function enqueue_parent_theme_styles() {
	// Get parent theme version
	$parent_style_version = wp_get_theme( 'woocustom' )->get( 'Version' );

	// Enqueue parent theme's stylesheet
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array(), $parent_style_version );
}
