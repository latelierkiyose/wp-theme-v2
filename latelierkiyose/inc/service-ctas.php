<?php
/**
 * Service page CTA configuration helpers.
 *
 * @package Kiyose
 * @since   0.2.5
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return the Events Manager category taxonomy used by the theme.
 *
 * @return string Taxonomy name.
 */
function kiyose_get_event_category_taxonomy(): string {
	return 'event-categories';
}

/**
 * Return Events Manager category terms.
 *
 * @return array<int, object> Category terms.
 */
function kiyose_get_event_category_terms(): array {
	$terms = get_terms(
		array(
			'taxonomy'   => kiyose_get_event_category_taxonomy(),
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$terms,
			static function ( $term ) {
				return is_object( $term ) && isset( $term->slug );
			}
		)
	);
}

/**
 * Return allowed Events Manager category slugs.
 *
 * @return array<int, string> Allowed slugs.
 */
function kiyose_get_allowed_event_category_slugs(): array {
	$terms = kiyose_get_event_category_terms();
	$slugs = array();

	foreach ( $terms as $term ) {
		$slug = sanitize_title( (string) $term->slug );

		if ( '' !== $slug ) {
			$slugs[] = $slug;
		}
	}

	return array_values( array_unique( $slugs ) );
}

/**
 * Sanitize Events Manager category slugs against existing categories.
 *
 * @param mixed $raw_slugs Raw slugs from post meta, query string, or form input.
 * @return array<int, string> Sanitized known slugs.
 */
function kiyose_sanitize_event_category_slugs( $raw_slugs ): array {
	if ( is_string( $raw_slugs ) ) {
		$raw_slugs = explode( ',', $raw_slugs );
	}

	if ( ! is_array( $raw_slugs ) ) {
		return array();
	}

	$allowed_slugs = kiyose_get_allowed_event_category_slugs();
	$sanitized     = array();

	foreach ( $raw_slugs as $raw_slug ) {
		$slug = sanitize_title( (string) $raw_slug );

		if ( '' === $slug || ! in_array( $slug, $allowed_slugs, true ) || in_array( $slug, $sanitized, true ) ) {
			continue;
		}

		$sanitized[] = $slug;
	}

	return $sanitized;
}

/**
 * Return sanitized event category slugs configured on a service page.
 *
 * @param int $post_id Service page post ID.
 * @return array<int, string> Category slugs.
 */
function kiyose_get_service_event_category_slugs( int $post_id ): array {
	$stored_slugs = get_post_meta( $post_id, 'kiyose_service_event_categories', true );

	return kiyose_sanitize_event_category_slugs( $stored_slugs );
}

/**
 * Return sanitized event category slugs requested through the calendar URL.
 *
 * @return array<int, string> Category slugs.
 */
function kiyose_get_requested_event_category_slugs(): array {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only filter parameter for the calendar page.
	if ( ! isset( $_GET['kiyose_event_categories'] ) ) {
		return array();
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Public read-only filter; sanitized by kiyose_sanitize_event_category_slugs() after splitting into individual slugs.
	return kiyose_sanitize_event_category_slugs( wp_unslash( $_GET['kiyose_event_categories'] ) );
}

/**
 * Return a configured published page permalink.
 *
 * @param mixed $page_id Page ID from theme settings.
 * @return string Page URL, or empty string when invalid.
 */
function kiyose_get_configured_page_permalink( $page_id ): string {
	$page_id = absint( $page_id );

	if ( 0 === $page_id ) {
		return '';
	}

	$page = get_post( $page_id );

	if ( ! is_object( $page ) || 'page' !== ( $page->post_type ?? '' ) || 'publish' !== ( $page->post_status ?? '' ) ) {
		return '';
	}

	$permalink = get_permalink( $page_id );

	return is_string( $permalink ) ? $permalink : '';
}

/**
 * Return CTA links for a service page.
 *
 * @param int $post_id Service page post ID.
 * @return array<int, array{label:string,url:string,class:string}> CTA links.
 */
function kiyose_get_service_cta_links( int $post_id ): array {
	$links       = array();
	$contact_url = kiyose_get_configured_page_permalink( get_theme_mod( 'kiyose_service_contact_page_id', 0 ) );

	if ( '' !== $contact_url ) {
		$links[] = array(
			'label' => __( 'Me contacter', 'kiyose' ),
			'url'   => $contact_url,
			'class' => 'button button--primary',
		);
	}

	$calendar_url = kiyose_get_configured_page_permalink( get_theme_mod( 'kiyose_service_calendar_page_id', 0 ) );

	if ( '' !== $calendar_url ) {
		$category_slugs = kiyose_get_service_event_category_slugs( $post_id );

		if ( ! empty( $category_slugs ) ) {
			$calendar_url = add_query_arg(
				'kiyose_event_categories',
				implode( ',', $category_slugs ),
				$calendar_url
			);
		}

		$links[] = array(
			'label' => __( 'Voir le calendrier', 'kiyose' ),
			'url'   => $calendar_url,
			'class' => 'button button--secondary',
		);
	}

	return $links;
}
