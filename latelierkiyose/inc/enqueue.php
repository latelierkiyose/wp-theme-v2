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
	// Fonts CSS.
	wp_enqueue_style(
		'kiyose-fonts',
		get_template_directory_uri() . '/assets/css/fonts.css',
		array(),
		KIYOSE_VERSION
	);

	// Variables CSS.
	wp_enqueue_style(
		'kiyose-variables',
		get_template_directory_uri() . '/assets/css/variables.css',
		array(),
		KIYOSE_VERSION
	);

	// Component stylesheets.
	wp_enqueue_style(
		'kiyose-header',
		get_template_directory_uri() . '/assets/css/components/header.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	wp_enqueue_style(
		'kiyose-navigation',
		get_template_directory_uri() . '/assets/css/components/navigation.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	wp_enqueue_style(
		'kiyose-footer',
		get_template_directory_uri() . '/assets/css/components/footer.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	// Template-specific components.
	wp_enqueue_style(
		'kiyose-page',
		get_template_directory_uri() . '/assets/css/components/page.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	wp_enqueue_style(
		'kiyose-search',
		get_template_directory_uri() . '/assets/css/components/search.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	wp_enqueue_style(
		'kiyose-404',
		get_template_directory_uri() . '/assets/css/components/404.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	// Blog components.
	wp_enqueue_style(
		'kiyose-blog-card',
		get_template_directory_uri() . '/assets/css/components/blog-card.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	wp_enqueue_style(
		'kiyose-blog-archive',
		get_template_directory_uri() . '/assets/css/components/blog-archive.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	wp_enqueue_style(
		'kiyose-blog-single',
		get_template_directory_uri() . '/assets/css/components/blog-single.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	// Main stylesheet (depends on fonts, variables, and components).
	wp_enqueue_style(
		'kiyose-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'kiyose-fonts', 'kiyose-variables', 'kiyose-header', 'kiyose-navigation', 'kiyose-footer', 'kiyose-page', 'kiyose-search', 'kiyose-404', 'kiyose-blog-card', 'kiyose-blog-archive', 'kiyose-blog-single' ),
		KIYOSE_VERSION
	);

	// JS principal.
	wp_enqueue_script(
		'kiyose-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		KIYOSE_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_assets' );

/**
 * Preload critical fonts.
 *
 * @since 1.0.0
 */
function kiyose_preload_fonts() {
	?>
	<link rel="preload" href="<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/lora-v37-latin-regular.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/nunito-v32-latin-regular.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
	<?php
}
add_action( 'wp_head', 'kiyose_preload_fonts' );

/**
 * Add defer attribute to the main script.
 *
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string Modified script tag.
 */
function kiyose_defer_main_script( $tag, $handle ) {
	if ( 'kiyose-main' === $handle ) {
		$tag = str_replace( ' src', ' defer src', $tag );
		$tag = str_replace( '<script', '<script type="module"', $tag );
		return $tag;
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'kiyose_defer_main_script', 10, 2 );
