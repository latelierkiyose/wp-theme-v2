<?php
/**
 * L'Atelier Kiyose - Enqueue scripts and styles.
 *
 * @package Kiyose
 * @since   0.1.0
 */

/**
 * Get asset version based on file modification time.
 *
 * Returns the file modification time (filemtime) for cache busting.
 * Falls back to KIYOSE_VERSION if file doesn't exist (e.g., in production without source files).
 *
 * @param string $file_path Relative path to the asset file from theme directory.
 * @return string|int File modification timestamp or theme version.
 * @since 0.2.5
 */
function kiyose_get_asset_version( $file_path ) {
	$full_path = get_template_directory() . $file_path;

	if ( file_exists( $full_path ) ) {
		return filemtime( $full_path );
	}

	// Fallback to theme version if file doesn't exist.
	return KIYOSE_VERSION;
}

/**
 * Get minified asset suffix based on environment.
 *
 * Returns '.min' in production (WP_DEBUG = false), empty string in development.
 *
 * @return string Asset suffix ('.min' or '').
 * @since 2.0.0
 */
function kiyose_get_asset_suffix() {
	$is_production = ! defined( 'WP_DEBUG' ) || ! WP_DEBUG;
	return $is_production ? '.min' : '';
}

/**
 * Enqueue theme assets (CSS and JS).
 */
function kiyose_enqueue_assets() {
	$suffix = kiyose_get_asset_suffix();

	// Fonts CSS.
	wp_enqueue_style(
		'kiyose-fonts',
		get_template_directory_uri() . "/assets/css/fonts{$suffix}.css",
		array(),
		kiyose_get_asset_version( "/assets/css/fonts{$suffix}.css" )
	);

	// Variables CSS.
	wp_enqueue_style(
		'kiyose-variables',
		get_template_directory_uri() . "/assets/css/variables{$suffix}.css",
		array(),
		kiyose_get_asset_version( "/assets/css/variables{$suffix}.css" )
	);

	// Plugins common styles (loaded early for all plugins).
	wp_enqueue_style(
		'kiyose-plugins-common',
		get_template_directory_uri() . "/assets/css/components/plugins-common{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/plugins-common{$suffix}.css" )
	);

	// Component stylesheets.
	wp_enqueue_style(
		'kiyose-header',
		get_template_directory_uri() . "/assets/css/components/header{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/header{$suffix}.css" )
	);

	wp_enqueue_style(
		'kiyose-navigation',
		get_template_directory_uri() . "/assets/css/components/navigation{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/navigation{$suffix}.css" )
	);

	wp_enqueue_style(
		'kiyose-footer',
		get_template_directory_uri() . "/assets/css/components/footer{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/footer{$suffix}.css" )
	);

	// Template-specific components.
	wp_enqueue_style(
		'kiyose-page',
		get_template_directory_uri() . "/assets/css/components/page{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/page{$suffix}.css" )
	);

	wp_enqueue_style(
		'kiyose-search',
		get_template_directory_uri() . "/assets/css/components/search{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/search{$suffix}.css" )
	);

	wp_enqueue_style(
		'kiyose-404',
		get_template_directory_uri() . "/assets/css/components/404{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/404{$suffix}.css" )
	);

	// Blog components.
	wp_enqueue_style(
		'kiyose-blog-card',
		get_template_directory_uri() . "/assets/css/components/blog-card{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/blog-card{$suffix}.css" )
	);

	wp_enqueue_style(
		'kiyose-blog-archive',
		get_template_directory_uri() . "/assets/css/components/blog-archive{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/blog-archive{$suffix}.css" )
	);

	wp_enqueue_style(
		'kiyose-blog-single',
		get_template_directory_uri() . "/assets/css/components/blog-single{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/blog-single{$suffix}.css" )
	);

	// Testimony component.
	wp_enqueue_style(
		'kiyose-testimony',
		get_template_directory_uri() . "/assets/css/components/testimony{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/testimony{$suffix}.css" )
	);

	// Homepage components.
	wp_enqueue_style(
		'kiyose-hero',
		get_template_directory_uri() . "/assets/css/components/hero{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/hero{$suffix}.css" )
	);

	wp_enqueue_style(
		'kiyose-service-card',
		get_template_directory_uri() . "/assets/css/components/service-card{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/service-card{$suffix}.css" )
	);

	wp_enqueue_style(
		'kiyose-home-sections',
		get_template_directory_uri() . "/assets/css/components/home-sections{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/home-sections{$suffix}.css" )
	);

	// Kintsugi decorative elements.
	wp_enqueue_style(
		'kiyose-kintsugi',
		get_template_directory_uri() . "/assets/css/components/kintsugi{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/kintsugi{$suffix}.css" )
	);

	// Global animations and transitions.
	wp_enqueue_style(
		'kiyose-animations',
		get_template_directory_uri() . "/assets/css/components/animations{$suffix}.css",
		array(),
		kiyose_get_asset_version( "/assets/css/components/animations{$suffix}.css" )
	);

	// Gutenberg blocks override (buttons, columns, etc.).
	wp_enqueue_style(
		'kiyose-gutenberg-blocks',
		get_template_directory_uri() . "/assets/css/components/gutenberg-blocks{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/gutenberg-blocks{$suffix}.css" )
	);

	// Service page component (conditional loading for page-services.php template).
	if ( is_page_template( 'templates/page-services.php' ) ) {
		wp_enqueue_style(
			'kiyose-service-page',
			get_template_directory_uri() . "/assets/css/components/service-page{$suffix}.css",
			array( 'kiyose-variables' ),
			kiyose_get_asset_version( "/assets/css/components/service-page{$suffix}.css" )
		);
	}

	// About page component (conditional loading for page-about.php template).
	if ( is_page_template( 'templates/page-about.php' ) ) {
		wp_enqueue_style(
			'kiyose-about-page',
			get_template_directory_uri() . "/assets/css/components/about-page{$suffix}.css",
			array( 'kiyose-variables' ),
			kiyose_get_asset_version( "/assets/css/components/about-page{$suffix}.css" )
		);
	}

	// Contact page components (conditional loading for page-contact.php template).
	if ( is_page_template( 'templates/page-contact.php' ) ) {
		wp_enqueue_style(
			'kiyose-contact-page',
			get_template_directory_uri() . "/assets/css/components/contact-page{$suffix}.css",
			array( 'kiyose-variables' ),
			kiyose_get_asset_version( "/assets/css/components/contact-page{$suffix}.css" )
		);

		wp_enqueue_style(
			'kiyose-cf7-override',
			get_template_directory_uri() . "/assets/css/components/cf7-override{$suffix}.css",
			array( 'kiyose-variables' ),
			kiyose_get_asset_version( "/assets/css/components/cf7-override{$suffix}.css" )
		);
	}

	// Main stylesheet (depends on fonts, variables, and components).
	wp_enqueue_style(
		'kiyose-main',
		get_template_directory_uri() . "/assets/css/main{$suffix}.css",
		array( 'kiyose-fonts', 'kiyose-variables', 'kiyose-header', 'kiyose-navigation', 'kiyose-footer', 'kiyose-page', 'kiyose-search', 'kiyose-404', 'kiyose-blog-card', 'kiyose-blog-archive', 'kiyose-blog-single', 'kiyose-testimony', 'kiyose-hero', 'kiyose-service-card', 'kiyose-home-sections', 'kiyose-kintsugi', 'kiyose-animations', 'kiyose-gutenberg-blocks' ),
		kiyose_get_asset_version( "/assets/css/main{$suffix}.css" )
	);

	// JS principal.
	wp_enqueue_script(
		'kiyose-main',
		get_template_directory_uri() . "/assets/js/main{$suffix}.js",
		array(),
		kiyose_get_asset_version( "/assets/js/main{$suffix}.js" ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_assets' );

/**
 * Override Events Manager styles with higher priority.
 *
 * Ensures our custom styles are loaded after Events Manager's default styles.
 * Loads on:
 * - Calendar page (template page-calendar.php)
 * - Homepage (template page-home.php with hardcoded shortcode)
 * - Any page with Events Manager shortcodes in content
 *
 * @since 1.0.0
 */
function kiyose_override_events_manager_styles() {
	$suffix = kiyose_get_asset_suffix();
	global $post;

	$should_load = false;

	// Load on calendar and home page templates.
	if ( is_page_template( 'templates/page-calendar.php' ) || is_page_template( 'templates/page-home.php' ) ) {
		$should_load = true;
	}

	// Load if Events Manager shortcode is present in content.
	if ( is_a( $post, 'WP_Post' ) ) {
		$em_shortcodes = array( 'events_list', 'events_calendar', 'events_map', 'event' );
		foreach ( $em_shortcodes as $shortcode ) {
			if ( has_shortcode( $post->post_content, $shortcode ) ) {
				$should_load = true;
				break;
			}
		}
	}

	if ( ! $should_load ) {
		return;
	}

	// Enqueue our custom Events Manager styles with high priority.
	wp_enqueue_style(
		'kiyose-events-manager',
		get_template_directory_uri() . "/assets/css/components/events-manager{$suffix}.css",
		array( 'kiyose-variables' ),
		kiyose_get_asset_version( "/assets/css/components/events-manager{$suffix}.css" ),
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
	$suffix = kiyose_get_asset_suffix();
	// Always load on all pages (footer newsletter).
	wp_enqueue_style(
		'kiyose-brevo-override',
		get_template_directory_uri() . "/assets/css/components/brevo-override{$suffix}.css",
		array( 'kiyose-variables', 'kiyose-plugins-common' ),
		kiyose_get_asset_version( "/assets/css/components/brevo-override{$suffix}.css" )
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
			get_template_directory_uri() . "/assets/css/components/carousel{$suffix}.css",
			array( 'kiyose-variables' ),
			kiyose_get_asset_version( "/assets/css/components/carousel{$suffix}.css" )
		);
	}

	// Enqueue testimonials grid when shortcode is present.
	if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'kiyose_testimonials' ) ) {
		wp_enqueue_style(
			'kiyose-testimonials-grid',
			get_template_directory_uri() . "/assets/css/components/testimonials-grid{$suffix}.css",
			array( 'kiyose-variables' ),
			kiyose_get_asset_version( "/assets/css/components/testimonials-grid{$suffix}.css" )
		);

		// Check if carousel mode is used.
		if ( strpos( $post->post_content, 'display="carousel"' ) !== false ) {
			wp_enqueue_style(
				'kiyose-carousel',
				get_template_directory_uri() . "/assets/css/components/carousel{$suffix}.css",
				array( 'kiyose-variables' ),
				kiyose_get_asset_version( "/assets/css/components/carousel{$suffix}.css" )
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_testimonials_styles' );

/**
 * Enqueue signets styles conditionally.
 *
 * Only loads when the [kiyose_signets] shortcode is present on the page.
 *
 * @since 0.2.4
 */
function kiyose_enqueue_signets_styles() {
	global $post;

	// Enqueue signets CSS when shortcode is present.
	if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'kiyose_signets' ) ) {
		wp_enqueue_style(
			'kiyose-signets',
			get_template_directory_uri() . "/assets/css/components/signets{$suffix}.css",
			array( 'kiyose-variables' ),
			kiyose_get_asset_version( "/assets/css/components/signets{$suffix}.css" )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_signets_styles' );

/**
 * Enqueue home page animations (CSS + JS).
 *
 * Conditionally loads animation styles and scripts only on the homepage.
 *
 * @since 0.2.4
 * @return void
 */
function kiyose_enqueue_home_animations() {
	$suffix = kiyose_get_asset_suffix();
	// Only load on homepage template.
	if ( ! is_page_template( 'templates/page-home.php' ) ) {
		return;
	}

	// Enqueue CSS.
	wp_enqueue_style(
		'kiyose-home-animations',
		get_template_directory_uri() . "/assets/css/components/home-animations{$suffix}.css",
		array( 'kiyose-hero', 'kiyose-home-sections' ), // Dependencies.
		filemtime( get_template_directory() . '/assets/css/components/home-animations.css' ),
		'all'
	);

	// Enqueue JS.
	wp_enqueue_script(
		'kiyose-home-animations',
		get_template_directory_uri() . "/assets/js/home-animations{$suffix}.js",
		array(), // No dependencies (Vanilla JS).
		filemtime( get_template_directory() . '/assets/js/home-animations.js' ),
		true // Load in footer.
	);
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_home_animations', 30 );

/**
 * Preload critical fonts.
 *
 * @since 1.0.0
 */
function kiyose_preload_fonts() {
	?>
	<link rel="preload" href="<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/caveat-v21-latin-regular.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/mkabel-v1-latin-regular.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
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
