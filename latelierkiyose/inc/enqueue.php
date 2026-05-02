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
			'condition' => 'kiyose_is_contact_template',
		),
		array(
			'type'      => 'style',
			'handle'    => 'kiyose-brevo-override',
			'path'      => '/assets/css/components/brevo-override.css',
			'deps'      => array( 'kiyose-variables', 'kiyose-plugins-common' ),
			'condition' => 'kiyose_should_load_brevo_styles',
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
			'condition' => 'kiyose_should_load_events_manager_styles',
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
 * Check if Events Manager styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_events_manager_styles(): bool {
	return kiyose_is_calendar_template()
		|| kiyose_is_home_template()
		|| kiyose_current_post_has_any_shortcode(
			array(
				'events_list',
				'events_calendar',
				'events_map',
				'event',
			)
		);
}

/**
 * Check if Brevo form styles are needed.
 *
 * @return bool
 */
function kiyose_should_load_brevo_styles(): bool {
	return function_exists( 'shortcode_exists' ) && shortcode_exists( 'sibwp_form' );
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
