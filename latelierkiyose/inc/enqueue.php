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

	// Plugins common styles (loaded early for all plugins).
	wp_enqueue_style(
		'kiyose-plugins-common',
		get_template_directory_uri() . '/assets/css/components/plugins-common.css',
		array( 'kiyose-variables' ),
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

	// Testimony component.
	wp_enqueue_style(
		'kiyose-testimony',
		get_template_directory_uri() . '/assets/css/components/testimony.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	// Homepage components.
	wp_enqueue_style(
		'kiyose-hero',
		get_template_directory_uri() . '/assets/css/components/hero.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	wp_enqueue_style(
		'kiyose-service-card',
		get_template_directory_uri() . '/assets/css/components/service-card.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	wp_enqueue_style(
		'kiyose-home-sections',
		get_template_directory_uri() . '/assets/css/components/home-sections.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION
	);

	// Service page component (conditional loading for page-services.php template).
	if ( is_page_template( 'templates/page-services.php' ) ) {
		wp_enqueue_style(
			'kiyose-service-page',
			get_template_directory_uri() . '/assets/css/components/service-page.css',
			array( 'kiyose-variables' ),
			KIYOSE_VERSION
		);
	}

	// About page component (conditional loading for page-about.php template).
	if ( is_page_template( 'templates/page-about.php' ) ) {
		wp_enqueue_style(
			'kiyose-about-page',
			get_template_directory_uri() . '/assets/css/components/about-page.css',
			array( 'kiyose-variables' ),
			KIYOSE_VERSION
		);
	}

	// Contact page components (conditional loading for page-contact.php template).
	if ( is_page_template( 'templates/page-contact.php' ) ) {
		wp_enqueue_style(
			'kiyose-contact-page',
			get_template_directory_uri() . '/assets/css/components/contact-page.css',
			array( 'kiyose-variables' ),
			KIYOSE_VERSION
		);

		wp_enqueue_style(
			'kiyose-cf7-override',
			get_template_directory_uri() . '/assets/css/components/cf7-override.css',
			array( 'kiyose-variables' ),
			KIYOSE_VERSION
		);
	}

	// Main stylesheet (depends on fonts, variables, and components).
	wp_enqueue_style(
		'kiyose-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'kiyose-fonts', 'kiyose-variables', 'kiyose-header', 'kiyose-navigation', 'kiyose-footer', 'kiyose-page', 'kiyose-search', 'kiyose-404', 'kiyose-blog-card', 'kiyose-blog-archive', 'kiyose-blog-single', 'kiyose-testimony', 'kiyose-hero', 'kiyose-service-card', 'kiyose-home-sections' ),
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
 * Override Events Manager styles with higher priority.
 *
 * Ensures our custom styles are loaded after Events Manager's default styles.
 *
 * @since 1.0.0
 */
function kiyose_override_events_manager_styles() {
	// Only on calendar page template.
	if ( ! is_page_template( 'templates/page-calendar.php' ) ) {
		return;
	}

	// Enqueue our custom Events Manager styles with high priority.
	wp_enqueue_style(
		'kiyose-events-manager',
		get_template_directory_uri() . '/assets/css/components/events-manager.css',
		array( 'kiyose-variables' ),
		KIYOSE_VERSION,
		'all'
	);
}
add_action( 'wp_enqueue_scripts', 'kiyose_override_events_manager_styles', 100 );

/**
 * Enqueue Brevo newsletter form styles.
 *
 * Loaded on:
 * - All pages (for footer newsletter form)
 * - Homepage (for dedicated newsletter section)
 *
 * @since 0.1.13
 */
function kiyose_enqueue_brevo_styles() {
	// Always load on all pages (footer newsletter).
	wp_enqueue_style(
		'kiyose-brevo-override',
		get_template_directory_uri() . '/assets/css/components/brevo-override.css',
		array( 'kiyose-variables', 'kiyose-plugins-common' ),
		KIYOSE_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_brevo_styles' );

/**
 * Enqueue testimonials grid styles conditionally.
 *
 * Only loads when the [kiyose_testimonials] shortcode is present on the page.
 *
 * @since 0.1.7
 */
function kiyose_enqueue_testimonials_styles() {
	global $post;

	// Enqueue carousel on home page template.
	if ( is_page_template( 'templates/page-home.php' ) ) {
		wp_enqueue_style(
			'kiyose-carousel',
			get_template_directory_uri() . '/assets/css/components/carousel.css',
			array( 'kiyose-variables' ),
			KIYOSE_VERSION
		);
	}

	// Enqueue testimonials grid when shortcode is present.
	if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'kiyose_testimonials' ) ) {
		wp_enqueue_style(
			'kiyose-testimonials-grid',
			get_template_directory_uri() . '/assets/css/components/testimonials-grid.css',
			array( 'kiyose-variables' ),
			KIYOSE_VERSION
		);

		// Check if carousel mode is used.
		if ( strpos( $post->post_content, 'display="carousel"' ) !== false ) {
			wp_enqueue_style(
				'kiyose-carousel',
				get_template_directory_uri() . '/assets/css/components/carousel.css',
				array( 'kiyose-variables' ),
				KIYOSE_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_testimonials_styles' );

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
