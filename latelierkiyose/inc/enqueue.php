<?php
/**
 * L'Atelier Kiyose - Enqueue scripts and styles.
 *
 * @package Kiyose
 * @since   0.1.0
 */

/**
 * Enqueue theme assets (CSS and JS).
 */
function kiyose_enqueue_assets() {
	// CSS principal.
	wp_enqueue_style(
		'kiyose-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array(),
		'0.1.0'
	);

	// JS principal.
	wp_enqueue_script(
		'kiyose-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		'0.1.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_assets' );

/**
 * Add defer attribute to the main script.
 *
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string Modified script tag.
 */
function kiyose_defer_main_script( $tag, $handle ) {
	if ( 'kiyose-main' === $handle ) {
		return str_replace( ' src', ' defer src', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'kiyose_defer_main_script', 10, 2 );
