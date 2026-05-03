<?php
/**
 * L'Atelier Kiyose - Enqueue scripts and styles.
 *
 * @package Kiyose
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

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
 * Return the declarative registry for theme assets.
 *
 * @return array<int, array<string, mixed>> Theme asset declarations.
 * @since 2.1.0
 */
function kiyose_get_theme_assets(): array {
	return array(
		array(
			'type'   => 'style',
			'handle' => 'kiyose-fonts',
			'path'   => '/assets/css/fonts.css',
			'deps'   => array(),
		),
		array(
			'type'   => 'style',
			'handle' => 'kiyose-variables',
			'path'   => '/assets/css/variables.css',
			'deps'   => array(),
		),
		array(
			'type'   => 'style',
			'handle' => 'kiyose-plugins-common',
			'path'   => '/assets/css/components/plugins-common.css',
			'deps'   => array( 'kiyose-variables' ),
		),
		array(
			'type'   => 'style',
			'handle' => 'kiyose-header',
			'path'   => '/assets/css/components/header.css',
			'deps'   => array( 'kiyose-variables' ),
		),
		array(
			'type'   => 'style',
			'handle' => 'kiyose-navigation',
			'path'   => '/assets/css/components/navigation.css',
			'deps'   => array( 'kiyose-variables' ),
		),
		array(
			'type'   => 'style',
			'handle' => 'kiyose-footer',
			'path'   => '/assets/css/components/footer.css',
			'deps'   => array( 'kiyose-variables' ),
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-page',
			'path'      => '/assets/css/components/page.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_page_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-search',
			'path'      => '/assets/css/components/search.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_search_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-404',
			'path'      => '/assets/css/components/404.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_404_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-blog-card',
			'path'      => '/assets/css/components/blog-card.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_blog_card_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-blog-archive',
			'path'      => '/assets/css/components/blog-archive.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_blog_archive_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-blog-single',
			'path'      => '/assets/css/components/blog-single.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_blog_single_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-testimony',
			'path'      => '/assets/css/components/testimony.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_testimony_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-hero',
			'path'      => '/assets/css/components/hero.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_is_home_template',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-home-sections',
			'path'      => '/assets/css/components/home-sections.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_is_home_template',
		),
		array(
			'type'   => 'style',
			'handle' => 'kiyose-kintsugi',
			'path'   => '/assets/css/components/kintsugi.css',
			'deps'   => array( 'kiyose-variables' ),
		),
		array(
			'type'   => 'style',
			'handle' => 'kiyose-animations',
			'path'   => '/assets/css/components/animations.css',
			'deps'   => array(),
		),
		array(
			'type'   => 'style',
			'handle' => 'kiyose-gutenberg-blocks',
			'path'   => '/assets/css/components/gutenberg-blocks.css',
			'deps'   => array( 'kiyose-variables' ),
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-service-page',
			'path'      => '/assets/css/components/service-page.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_is_service_template',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-about-page',
			'path'      => '/assets/css/components/about-page.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_is_about_template',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-contact-page',
			'path'      => '/assets/css/components/contact-page.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_is_contact_template',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-cf7-override',
			'path'      => '/assets/css/components/cf7-override.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_contact_form_7_assets',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-brevo-override',
			'path'      => '/assets/css/components/brevo-override.css',
			'deps'      => array( 'kiyose-variables', 'kiyose-plugins-common' ),
			'condition' => 'kiyose_should_load_brevo_assets',
		),
		array(
			'type'   => 'style',
			'handle' => 'kiyose-main',
			'path'   => '/assets/css/main.css',
			'deps'   => array(
				'kiyose-fonts',
				'kiyose-variables',
				'kiyose-plugins-common',
				'kiyose-header',
				'kiyose-navigation',
				'kiyose-footer',
				'kiyose-kintsugi',
				'kiyose-animations',
				'kiyose-gutenberg-blocks',
			),
		),
		array(
			'type'      => 'script',
			'handle'    => 'kiyose-main',
			'path'      => '/assets/js/main.js',
			'deps'      => array(),
			'in_footer' => true,
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-events-manager',
			'path'      => '/assets/css/components/events-manager.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_events_manager_assets',
			'priority'  => 100,
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-testimonials-grid',
			'path'      => '/assets/css/components/testimonials-grid.css',
			'deps'      => array( 'kiyose-variables', 'kiyose-testimony' ),
			'condition' => 'kiyose_should_load_testimonials_grid',
		),
		array(
			'type'      => 'script',
			'handle'    => 'kiyose-masonry',
			'path'      => '/assets/js/modules/masonry.js',
			'deps'      => array(),
			'in_footer' => true,
			'condition' => 'kiyose_should_load_testimonials_grid',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-carousel',
			'path'      => '/assets/css/components/carousel.css',
			'deps'      => array( 'kiyose-variables', 'kiyose-testimony' ),
			'condition' => 'kiyose_should_load_carousel_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-signets',
			'path'      => '/assets/css/components/signets.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_signets_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-callout-box',
			'path'      => '/assets/css/components/callout-box.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_callout_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-cta',
			'path'      => '/assets/css/components/cta.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_should_load_cta_styles',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-home-animations',
			'path'      => '/assets/css/components/home-animations.css',
			'deps'      => array( 'kiyose-hero', 'kiyose-home-sections' ),
			'condition' => 'kiyose_is_home_template',
			'priority'  => 30,
		),
		array(
			'type'      => 'script',
			'handle'    => 'kiyose-home-animations',
			'path'      => '/assets/js/home-animations.js',
			'deps'      => array(),
			'in_footer' => true,
			'condition' => 'kiyose_is_home_template',
			'priority'  => 30,
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-welcome-block',
			'path'      => '/assets/css/components/welcome-block.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_is_home_template',
			'priority'  => 30,
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-home-content',
			'path'      => '/assets/css/components/home-content.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_is_home_template',
			'priority'  => 30,
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-about-overlay',
			'path'      => '/assets/css/components/about-overlay.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_is_home_template',
			'priority'  => 30,
		),
		array(
			'type'      => 'script',
			'handle'    => 'kiyose-about-overlay',
			'path'      => '/assets/js/modules/about-overlay.js',
			'deps'      => array(),
			'in_footer' => true,
			'condition' => 'kiyose_is_home_template',
			'priority'  => 30,
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-newsletter-overlay',
			'path'      => '/assets/css/components/newsletter-overlay.css',
			'deps'      => array( 'kiyose-variables' ),
			'condition' => 'kiyose_is_home_template',
			'priority'  => 30,
		),
		array(
			'type'      => 'script',
			'handle'    => 'kiyose-newsletter-overlay',
			'path'      => '/assets/js/modules/newsletter-overlay.js',
			'deps'      => array( 'kiyose-about-overlay' ),
			'in_footer' => true,
			'condition' => 'kiyose_is_home_template',
			'priority'  => 30,
		),
		array(
			'type'      => 'script',
			'handle'    => 'kiyose-deco-overlap',
			'path'      => '/assets/js/modules/deco-overlap.js',
			'deps'      => array(),
			'in_footer' => true,
			'priority'  => 30,
		),
		array(
			'type'      => 'script',
			'handle'    => 'kiyose-decorative-reveal',
			'path'      => '/assets/js/modules/decorative-reveal.js',
			'deps'      => array( 'kiyose-deco-overlap' ),
			'in_footer' => true,
			'priority'  => 30,
		),
	);
}

/**
 * Enqueue a registered asset with the current environment suffix.
 *
 * @param array<string, mixed> $asset  Asset declaration.
 * @param string               $suffix Asset suffix.
 * @return void
 * @since 2.1.0
 */
function kiyose_enqueue_registered_asset( array $asset, string $suffix ): void {
	$path    = kiyose_add_asset_suffix( (string) $asset['path'], $suffix );
	$handle  = (string) $asset['handle'];
	$deps    = $asset['deps'] ?? array();
	$version = kiyose_get_asset_version( $path );
	$src     = get_template_directory_uri() . $path;

	if ( 'script' === $asset['type'] ) {
		wp_enqueue_script(
			$handle,
			$src,
			$deps,
			$version,
			(bool) ( $asset['in_footer'] ?? false )
		);
		return;
	}

	wp_enqueue_style(
		$handle,
		$src,
		$deps,
		$version,
		$asset['media'] ?? 'all'
	);
}

/**
 * Check whether an asset condition matches the current request.
 *
 * @param array<string, mixed> $asset Asset declaration.
 * @return bool True when the asset should be loaded.
 * @since 2.1.0
 */
function kiyose_asset_condition_matches( array $asset ): bool {
	if ( ! isset( $asset['condition'] ) ) {
		return true;
	}

	if ( ! is_callable( $asset['condition'] ) ) {
		return false;
	}

	return (bool) call_user_func( $asset['condition'] );
}

/**
 * Enqueue theme assets for a specific hook priority.
 *
 * @param int $priority Current wp_enqueue_scripts priority.
 * @return void
 * @since 2.1.0
 */
function kiyose_enqueue_registered_theme_assets( int $priority ): void {
	$suffix = kiyose_get_asset_suffix();

	foreach ( kiyose_get_theme_assets() as $asset ) {
		$asset_priority = (int) ( $asset['priority'] ?? 10 );

		if ( $priority !== $asset_priority || ! kiyose_asset_condition_matches( $asset ) ) {
			continue;
		}

		kiyose_enqueue_registered_asset( $asset, $suffix );
	}
}

/**
 * Enqueue default priority theme assets.
 *
 * @return void
 */
function kiyose_enqueue_assets(): void {
	kiyose_enqueue_registered_theme_assets( 10 );
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_assets' );

/**
 * Enqueue assets that should load after the main theme bundle.
 *
 * @return void
 */
function kiyose_enqueue_deferred_assets(): void {
	kiyose_enqueue_registered_theme_assets( 30 );
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_deferred_assets', 30 );

/**
 * Enqueue late plugin overrides.
 *
 * @return void
 */
function kiyose_enqueue_late_assets(): void {
	kiyose_enqueue_registered_theme_assets( 100 );
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_late_assets', 100 );

/**
 * Return native plugin handles controlled by the theme on production pages.
 *
 * Handles come from the supported plugins documented for the project. The filter
 * keeps the list adjustable if a plugin changes a handle in a later release.
 *
 * @return array<string, array<string, mixed>> Plugin asset groups.
 */
function kiyose_get_plugin_asset_handles(): array {
	$asset_handles = array(
		'events_manager' => array(
			'condition' => 'kiyose_should_load_events_manager_assets',
			'styles'    => array(
				'events-manager',
				'events-manager-css',
				'events-manager-dynamic',
				'events-manager-pro',
				'em-events-manager',
				'em-bookings',
				'em-calendar',
				'em-fonts',
			),
			'scripts'   => array(
				'events-manager',
				'events-manager-js',
				'em-events-manager',
				'em-events',
				'em-bookings',
				'em-google-maps',
				'em-maps',
				'em-osm',
			),
		),
		'contact_form_7' => array(
			'condition' => 'kiyose_should_load_contact_form_7_assets',
			'styles'    => array(
				'contact-form-7',
				'wpcf7-recaptcha',
			),
			'scripts'   => array(
				'contact-form-7',
				'wpcf7-recaptcha',
			),
		),
		'brevo'          => array(
			'condition' => 'kiyose_should_load_brevo_assets',
			'styles'    => array(
				'sib-front-css',
				'sibwp-form',
				'sibwp-form-css',
				'sib-form-css',
				'mailin-front-css',
				'brevo-form-css',
			),
			'scripts'   => array(
				'sib-front-js',
				'sibwp-form',
				'sibwp-form-js',
				'sendinblue-js',
				'mailin-front-js',
				'mailin-tracking',
				'brevo-conversations',
				'brevo-widget',
				'sib-conversations',
			),
		),
		'recaptcha'      => array(
			'condition' => 'kiyose_should_load_recaptcha_assets',
			'styles'    => array(),
			'scripts'   => array(
				'google-recaptcha',
				'recaptcha',
				'grecaptcha',
			),
		),
	);

	if ( function_exists( 'apply_filters' ) ) {
		return (array) apply_filters( 'kiyose_plugin_asset_handles', $asset_handles );
	}

	return $asset_handles;
}

/**
 * Dequeue native plugin assets when the current request does not render them.
 *
 * @return void
 */
function kiyose_dequeue_unused_plugin_assets(): void {
	foreach ( kiyose_get_plugin_asset_handles() as $asset_group ) {
		$condition = $asset_group['condition'] ?? null;

		if ( is_callable( $condition ) && (bool) call_user_func( $condition ) ) {
			continue;
		}

		kiyose_dequeue_style_handles( (array) ( $asset_group['styles'] ?? array() ) );
		kiyose_dequeue_script_handles( (array) ( $asset_group['scripts'] ?? array() ) );
	}
}
add_action( 'wp_enqueue_scripts', 'kiyose_dequeue_unused_plugin_assets', 120 );
add_action( 'wp_print_styles', 'kiyose_dequeue_unused_plugin_assets', 100 );
add_action( 'wp_print_scripts', 'kiyose_dequeue_unused_plugin_assets', 90 );

/**
 * Dequeue and deregister style handles.
 *
 * @param array<int, string> $handles Style handles.
 * @return void
 */
function kiyose_dequeue_style_handles( array $handles ): void {
	foreach ( array_unique( $handles ) as $handle ) {
		if ( function_exists( 'wp_dequeue_style' ) ) {
			wp_dequeue_style( $handle );
		}

		if ( function_exists( 'wp_deregister_style' ) ) {
			wp_deregister_style( $handle );
		}
	}
}

/**
 * Dequeue and deregister script handles.
 *
 * @param array<int, string> $handles Script handles.
 * @return void
 */
function kiyose_dequeue_script_handles( array $handles ): void {
	foreach ( array_unique( $handles ) as $handle ) {
		if ( function_exists( 'wp_dequeue_script' ) ) {
			wp_dequeue_script( $handle );
		}

		if ( function_exists( 'wp_deregister_script' ) ) {
			wp_deregister_script( $handle );
		}
	}
}

/**
 * Add reCAPTCHA preconnect origins only when a public form can use reCAPTCHA.
 *
 * @param array<int, array<string, string>|string> $urls          Existing resource hints.
 * @param string                                   $relation_type Resource hint relation type.
 * @return array<int, array<string, string>|string> Filtered resource hints.
 */
function kiyose_add_recaptcha_resource_hints( array $urls, string $relation_type ): array {
	if ( 'preconnect' !== $relation_type || ! kiyose_should_load_recaptcha_assets() ) {
		return $urls;
	}

	$urls[] = 'https://www.google.com';
	$urls[] = array(
		'href'        => 'https://www.gstatic.com',
		'crossorigin' => 'anonymous',
	);

	return kiyose_unique_resource_hints( $urls );
}
add_filter( 'wp_resource_hints', 'kiyose_add_recaptcha_resource_hints', 10, 2 );

/**
 * Remove duplicated resource hints while preserving first occurrence order.
 *
 * @param array<int, array<string, string>|string> $urls Resource hints.
 * @return array<int, array<string, string>|string> Unique resource hints.
 */
function kiyose_unique_resource_hints( array $urls ): array {
	$unique_urls = array();
	$seen_urls   = array();

	foreach ( $urls as $url ) {
		$key = is_array( $url ) ? (string) ( $url['href'] ?? '' ) : (string) $url;

		if ( '' === $key || isset( $seen_urls[ $key ] ) ) {
			continue;
		}

		$seen_urls[ $key ] = true;
		$unique_urls[]     = $url;
	}

	return $unique_urls;
}

/**
 * Dequeue exact duplicate external reCAPTCHA and Brevo SDK script URLs.
 *
 * @return void
 */
function kiyose_dedupe_external_scripts(): void {
	global $wp_scripts;

	if ( ! is_object( $wp_scripts ) || empty( $wp_scripts->queue ) || empty( $wp_scripts->registered ) ) {
		return;
	}

	$seen_urls = array();

	foreach ( (array) $wp_scripts->queue as $handle ) {
		$registered_script = $wp_scripts->registered[ $handle ] ?? null;

		if ( ! is_object( $registered_script ) || empty( $registered_script->src ) ) {
			continue;
		}

		$url = kiyose_normalize_dedupable_external_script_url( (string) $registered_script->src );

		if ( '' === $url ) {
			continue;
		}

		if ( isset( $seen_urls[ $url ] ) ) {
			kiyose_dequeue_script_handles( array( (string) $handle ) );
			continue;
		}

		$seen_urls[ $url ] = (string) $handle;
	}
}
add_action( 'wp_print_scripts', 'kiyose_dedupe_external_scripts', 100 );

/**
 * Normalize external script URLs that the theme can safely dedupe.
 *
 * @param string $src Script source URL.
 * @return string Normalized URL, or an empty string when the URL is not targeted.
 */
function kiyose_normalize_dedupable_external_script_url( string $src ): string {
	if ( str_starts_with( $src, '//' ) ) {
		$src = 'https:' . $src;
	}

	if ( 1 !== preg_match( '#^https?://#i', $src ) ) {
		return '';
	}

	$parts = wp_parse_url( $src );

	if ( false === $parts || empty( $parts['host'] ) || empty( $parts['path'] ) ) {
		return '';
	}

	$host = strtolower( (string) $parts['host'] );
	$path = (string) $parts['path'];

	if ( ! kiyose_is_dedupable_external_script_url( $host, $path ) ) {
		return '';
	}

	$scheme = strtolower( (string) ( $parts['scheme'] ?? 'https' ) );
	$query  = isset( $parts['query'] ) ? '?' . $parts['query'] : '';

	return $scheme . '://' . $host . $path . $query;
}

/**
 * Check if an external script URL belongs to a dedupe-targeted provider.
 *
 * @param string $host URL host.
 * @param string $path URL path.
 * @return bool True when the URL can be deduped.
 */
function kiyose_is_dedupable_external_script_url( string $host, string $path ): bool {
	return ( 'www.google.com' === $host && '/recaptcha/api.js' === $path )
		|| ( 'www.gstatic.com' === $host && str_contains( $path, '/recaptcha/' ) )
		|| kiyose_host_matches_any_domain(
			$host,
			array(
				'brevo.com',
				'sendinblue.com',
				'sibautomation.com',
				'sibforms.com',
			)
		);
}

/**
 * Check if a host is exactly one of the domains or one of their subdomains.
 *
 * @param string             $host    Host to test.
 * @param array<int, string> $domains Domains to match.
 * @return bool True when the host matches.
 */
function kiyose_host_matches_any_domain( string $host, array $domains ): bool {
	foreach ( $domains as $domain ) {
		if ( $host === $domain || str_ends_with( $host, '.' . $domain ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Add the current suffix before the file extension.
 *
 * @param string $path   Relative asset path.
 * @param string $suffix Asset suffix.
 * @return string Suffixed asset path.
 */
function kiyose_add_asset_suffix( string $path, string $suffix ): string {
	if ( '' === $suffix ) {
		return $path;
	}

	$extension = pathinfo( $path, PATHINFO_EXTENSION );

	if ( '' === $extension ) {
		return $path . $suffix;
	}

	return substr( $path, 0, -1 * ( strlen( $extension ) + 1 ) ) . $suffix . '.' . $extension;
}

/**
 * Check if the current page uses the home template.
 *
 * @return bool
 */
function kiyose_is_home_template(): bool {
	return function_exists( 'is_page_template' ) && is_page_template( 'templates/page-home.php' );
}

/**
 * Check if the current page uses the service template.
 *
 * @return bool
 */
function kiyose_is_service_template(): bool {
	return function_exists( 'is_page_template' ) && is_page_template( 'templates/page-services.php' );
}

/**
 * Check if the current page uses the about template.
 *
 * @return bool
 */
function kiyose_is_about_template(): bool {
	return function_exists( 'is_page_template' ) && is_page_template( 'templates/page-about.php' );
}

/**
 * Check if the current page uses the contact template.
 *
 * @return bool
 */
function kiyose_is_contact_template(): bool {
	return function_exists( 'is_page_template' ) && is_page_template( 'templates/page-contact.php' );
}

/**
 * Check if the current page uses the calendar template.
 *
 * @return bool
 */
function kiyose_is_calendar_template(): bool {
	return function_exists( 'is_page_template' ) && is_page_template( 'templates/page-calendar.php' );
}

/**
 * Check if a current page template is one of the dedicated templates.
 *
 * @return bool
 */
function kiyose_is_dedicated_page_template(): bool {
	return kiyose_is_home_template()
		|| kiyose_is_service_template()
		|| kiyose_is_about_template()
		|| kiyose_is_contact_template()
		|| kiyose_is_calendar_template();
}

/**
 * Check if the generic page component is needed.
 *
 * @return bool
 */
function kiyose_should_load_page_styles(): bool {
	return function_exists( 'is_page' ) && is_page() && ! kiyose_is_dedicated_page_template();
}

/**
 * Check if the search component is needed.
 *
 * @return bool
 */
function kiyose_should_load_search_styles(): bool {
	return ( function_exists( 'is_search' ) && is_search() )
		|| ( function_exists( 'is_home' ) && is_home() )
		|| ( function_exists( 'is_archive' ) && is_archive() )
		|| ( function_exists( 'is_404' ) && is_404() );
}

/**
 * Check if the 404 component is needed.
 *
 * @return bool
 */
function kiyose_should_load_404_styles(): bool {
	return function_exists( 'is_404' ) && is_404();
}

/**
 * Check if blog card styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_blog_card_styles(): bool {
	return kiyose_is_home_template()
		|| ( function_exists( 'is_archive' ) && is_archive() )
		|| ( function_exists( 'is_home' ) && is_home() )
		|| ( function_exists( 'is_search' ) && is_search() );
}

/**
 * Check if blog archive styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_blog_archive_styles(): bool {
	return ( function_exists( 'is_archive' ) && is_archive() )
		|| ( function_exists( 'is_home' ) && is_home() );
}

/**
 * Check if blog single styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_blog_single_styles(): bool {
	if ( function_exists( 'is_single' ) ) {
		return is_single();
	}

	return function_exists( 'is_singular' ) && is_singular( 'post' );
}

/**
 * Check if testimony card styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_testimony_styles(): bool {
	return kiyose_is_home_template()
		|| kiyose_current_post_has_shortcode( 'kiyose_testimonials' )
		|| ( function_exists( 'is_singular' ) && is_singular( 'kiyose_testimony' ) )
		|| ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( 'kiyose_testimony' ) );
}

/**
 * Check if Events Manager assets are needed.
 *
 * @return bool
 */
function kiyose_should_load_events_manager_assets(): bool {
	return kiyose_is_calendar_template()
		|| kiyose_is_home_template()
		|| kiyose_is_events_manager_context()
		|| kiyose_current_post_has_any_shortcode(
			array(
				'events_list',
				'events_calendar',
				'events_map',
				'kiyose_events_list',
				'event',
				'event_form',
				'event_search_form',
				'locations_list',
				'location',
			)
		);
}

/**
 * Check if Events Manager styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_events_manager_styles(): bool {
	return kiyose_should_load_events_manager_assets();
}

/**
 * Check if the current request belongs to Events Manager content.
 *
 * @return bool
 */
function kiyose_is_events_manager_context(): bool {
	$events_post_types = array( 'event', 'event-recurring', 'location' );
	$events_taxonomies = array( 'event-categories', 'event-tags', 'event-category', 'event-tag', 'location-categories' );

	if ( function_exists( 'is_singular' ) && is_singular( $events_post_types ) ) {
		return true;
	}

	if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( $events_post_types ) ) {
		return true;
	}

	return function_exists( 'is_tax' ) && is_tax( $events_taxonomies );
}

/**
 * Check if Contact Form 7 assets are needed.
 *
 * @return bool
 */
function kiyose_should_load_contact_form_7_assets(): bool {
	return kiyose_is_contact_template()
		|| kiyose_current_post_has_shortcode( 'contact-form-7' );
}

/**
 * Check if Brevo form assets are needed.
 *
 * @return bool
 */
function kiyose_should_load_brevo_assets(): bool {
	return kiyose_should_render_brevo_newsletter()
		|| kiyose_current_post_has_shortcode( 'sibwp_form' );
}

/**
 * Check if Brevo form styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_brevo_styles(): bool {
	return kiyose_should_load_brevo_assets();
}

/**
 * Check if the theme can render the configured Brevo newsletter form.
 *
 * @return bool
 */
function kiyose_should_render_brevo_newsletter(): bool {
	return function_exists( 'shortcode_exists' ) && shortcode_exists( 'sibwp_form' );
}

/**
 * Check if a public form can load reCAPTCHA.
 *
 * @return bool
 */
function kiyose_should_load_recaptcha_assets(): bool {
	return kiyose_should_load_contact_form_7_assets()
		|| kiyose_should_load_events_manager_recaptcha_assets()
		|| ( kiyose_should_load_brevo_assets() && kiyose_is_brevo_recaptcha_enabled() );
}

/**
 * Check if the current Events Manager context can render a booking form.
 *
 * @return bool
 */
function kiyose_should_load_events_manager_recaptcha_assets(): bool {
	return kiyose_is_events_manager_booking_context() && kiyose_is_events_manager_recaptcha_enabled();
}

/**
 * Check whether the current request can include an Events Manager booking form.
 *
 * @return bool
 */
function kiyose_is_events_manager_booking_context(): bool {
	if ( function_exists( 'is_singular' ) && is_singular( array( 'event', 'event-recurring' ) ) ) {
		return true;
	}

	return kiyose_current_post_has_any_shortcode(
		array(
			'event',
			'event_form',
		)
	);
}

/**
 * Return whether Events Manager reCAPTCHA should be treated as active.
 *
 * @return bool True when Events Manager reCAPTCHA should be considered active.
 */
function kiyose_is_events_manager_recaptcha_enabled(): bool {
	if ( function_exists( 'apply_filters' ) ) {
		return (bool) apply_filters( 'kiyose_events_manager_recaptcha_enabled', true );
	}

	return true;
}

/**
 * Return whether Brevo reCAPTCHA should be treated as active.
 *
 * @return bool True when Brevo reCAPTCHA should be considered active.
 */
function kiyose_is_brevo_recaptcha_enabled(): bool {
	if ( function_exists( 'apply_filters' ) ) {
		return (bool) apply_filters( 'kiyose_brevo_recaptcha_enabled', true );
	}

	return true;
}

/**
 * Check if testimonials grid assets are needed.
 *
 * @return bool
 */
function kiyose_should_load_testimonials_grid(): bool {
	return kiyose_current_post_has_shortcode( 'kiyose_testimonials' );
}

/**
 * Check if carousel styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_carousel_styles(): bool {
	return kiyose_is_home_template()
		|| kiyose_current_post_has_shortcode_attribute( 'kiyose_testimonials', 'display', 'carousel' );
}

/**
 * Check if signets styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_signets_styles(): bool {
	return kiyose_current_post_has_shortcode( 'kiyose_signets' );
}

/**
 * Check if callout styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_callout_styles(): bool {
	return kiyose_current_post_has_shortcode( 'kiyose_callout' );
}

/**
 * Check if CTA styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_cta_styles(): bool {
	return kiyose_is_home_template()
		|| kiyose_current_post_has_shortcode( 'kiyose_cta' );
}

/**
 * Check whether the current post content has one shortcode.
 *
 * @param string $shortcode Shortcode tag.
 * @return bool
 */
function kiyose_current_post_has_shortcode( string $shortcode ): bool {
	global $post;

	if ( ! function_exists( 'has_shortcode' ) || ! is_object( $post ) || ! isset( $post->post_content ) ) {
		return false;
	}

	return has_shortcode( $post->post_content, $shortcode );
}

/**
 * Check whether the current post content has at least one shortcode.
 *
 * @param array<int, string> $shortcodes Shortcode tags.
 * @return bool
 */
function kiyose_current_post_has_any_shortcode( array $shortcodes ): bool {
	foreach ( $shortcodes as $shortcode ) {
		if ( kiyose_current_post_has_shortcode( $shortcode ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Check whether the current post has a shortcode attribute value.
 *
 * @param string $shortcode Shortcode tag.
 * @param string $attribute Attribute name.
 * @param string $value     Expected attribute value.
 * @return bool
 */
function kiyose_current_post_has_shortcode_attribute( string $shortcode, string $attribute, string $value ): bool {
	global $post;

	if ( ! kiyose_current_post_has_shortcode( $shortcode ) || ! is_object( $post ) || ! isset( $post->post_content ) ) {
		return false;
	}

	$pattern = '/\[' . preg_quote( $shortcode, '/' )
		. '[^\]]*\s' . preg_quote( $attribute, '/' )
		. '\s*=\s*(["\']?)' . preg_quote( $value, '/' ) . '\1(?:\s|\])/i';

	return 1 === preg_match( $pattern, $post->post_content );
}

/**
 * Preload critical fonts.
 *
 * @since 1.0.0
 */
function kiyose_preload_fonts() {
	?>
	<link rel="preload" href="<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/dancing-script-v29-latin-regular.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="<?php echo esc_url( get_template_directory_uri() . '/assets/fonts/nunito-v32-latin-regular.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
	<?php
}
add_action( 'wp_head', 'kiyose_preload_fonts' );

/**
 * Load the main script as an ES module.
 *
 * Replaces WordPress's default type="text/javascript" with type="module"
 * so that ES import/export syntax is supported. Module scripts are deferred
 * by default, so no explicit defer attribute is needed.
 *
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string Modified script tag.
 */
function kiyose_load_main_as_module( $tag, $handle ) {
	if ( 'kiyose-main' !== $handle ) {
		return $tag;
	}

	$tag = preg_replace( '/\s+type=["\'][^"\']*["\']/', '', $tag );
	$tag = str_replace( '<script ', '<script type="module" ', $tag );

	return $tag;
}
add_filter( 'script_loader_tag', 'kiyose_load_main_as_module', 10, 2 );
