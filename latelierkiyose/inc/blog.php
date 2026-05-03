<?php
/**
 * Blog archive helpers.
 *
 * @package Kiyose
 * @since   0.2.7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the blog card variant requested by a template part.
 *
 * @param array $args Template part arguments.
 * @return string Supported card variant.
 */
function kiyose_get_blog_card_variant( $args ) {
	$variant = isset( $args['variant'] ) ? sanitize_key( $args['variant'] ) : 'card';

	if ( 'archive' === $variant ) {
		return 'archive';
	}

	return 'card';
}

/**
 * Returns the CSS classes for a blog card variant.
 *
 * @param string $variant Blog card variant.
 * @return array Blog card CSS classes.
 */
function kiyose_get_blog_card_classes( $variant ) {
	$classes = array( 'blog-card' );

	if ( 'archive' === sanitize_key( $variant ) ) {
		$classes[] = 'blog-card--archive';
	}

	return $classes;
}

/**
 * Returns the arguments used to render blog sidebar categories.
 *
 * @return array WordPress category list arguments.
 */
function kiyose_get_blog_sidebar_categories_args() {
	return array(
		'title_li'   => '',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
		'show_count' => false,
	);
}

/**
 * Tells whether the blog sidebar has visible categories to render.
 *
 * @param array|null $categories Optional preloaded categories.
 * @return bool True when at least one visible category exists.
 */
function kiyose_has_visible_blog_categories( $categories = null ) {
	if ( null === $categories ) {
		$categories = get_categories( kiyose_get_blog_sidebar_categories_args() );
	}

	return ! empty( $categories );
}

/**
 * Returns the single post navigation HTML for the current content type.
 *
 * @param int $post_id Optional post ID. Defaults to current post.
 * @return string Navigation HTML.
 */
function kiyose_get_single_post_navigation_html( $post_id = 0 ) {
	$post_id = 0 === (int) $post_id ? get_the_ID() : absint( $post_id );

	if ( 'event' === get_post_type( $post_id ) ) {
		return kiyose_get_event_post_navigation_html( $post_id );
	}

	if ( ! function_exists( 'get_the_post_navigation' ) ) {
		return '';
	}

	return get_the_post_navigation(
		array(
			'prev_text'  => '<span class="nav-subtitle">' . esc_html__( 'Article précédent', 'kiyose' ) . '</span><span class="nav-title">%title</span>',
			'next_text'  => '<span class="nav-subtitle">' . esc_html__( 'Article suivant', 'kiyose' ) . '</span><span class="nav-title">%title</span>',
			'aria_label' => esc_html__( 'Navigation entre articles', 'kiyose' ),
		)
	);
}

/**
 * Returns previous/next event navigation ordered by event start date.
 *
 * @param int $post_id Optional event post ID. Defaults to current post.
 * @return string Navigation HTML, or an empty string when no valid navigation exists.
 */
function kiyose_get_event_post_navigation_html( $post_id = 0 ) {
	$current_post_id = 0 === (int) $post_id ? get_the_ID() : absint( $post_id );

	if ( 0 === $current_post_id || ! function_exists( 'get_posts' ) ) {
		return '';
	}

	$current_start_timestamp = kiyose_get_event_start_timestamp( $current_post_id );

	if ( null === $current_start_timestamp ) {
		return '';
	}

	$event_ids = get_posts(
		array(
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'order'                  => 'ASC',
			'orderby'                => 'ID',
			'post_status'            => 'publish',
			'post_type'              => 'event',
			'posts_per_page'         => -1,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		)
	);

	$events = array();

	foreach ( $event_ids as $event_id ) {
		$event_id        = absint( $event_id );
		$start_timestamp = kiyose_get_event_start_timestamp( $event_id );

		if ( null === $start_timestamp ) {
			continue;
		}

		$events[] = array(
			'id'    => $event_id,
			'start' => $start_timestamp,
		);
	}

	if ( empty( $events ) ) {
		return '';
	}

	usort(
		$events,
		static function ( $first_event, $second_event ) {
			if ( $first_event['start'] === $second_event['start'] ) {
				return $first_event['id'] <=> $second_event['id'];
			}

			return $first_event['start'] <=> $second_event['start'];
		}
	);

	$previous_event_id = 0;
	$next_event_id     = 0;

	foreach ( $events as $index => $event ) {
		if ( $current_post_id !== $event['id'] ) {
			continue;
		}

		$previous_event_id = isset( $events[ $index - 1 ] ) ? $events[ $index - 1 ]['id'] : 0;
		$next_event_id     = isset( $events[ $index + 1 ] ) ? $events[ $index + 1 ]['id'] : 0;
		break;
	}

	return kiyose_get_event_post_navigation_markup( $previous_event_id, $next_event_id );
}

/**
 * Returns the normalized Events Manager start timestamp for an event.
 *
 * @param int $post_id Event post ID.
 * @return int|null Unix timestamp, or null when no supported date meta is valid.
 */
function kiyose_get_event_start_timestamp( $post_id ) {
	$start_timestamp = kiyose_get_event_meta_value( $post_id, '_start_ts' );

	if ( is_numeric( $start_timestamp ) && (int) $start_timestamp > 0 ) {
		return (int) $start_timestamp;
	}

	foreach ( array( '_event_start', '_event_start_date' ) as $meta_key ) {
		$start_date = kiyose_get_event_meta_value( $post_id, $meta_key );

		if ( '' === $start_date ) {
			continue;
		}

		$timestamp = strtotime( $start_date );

		if ( false !== $timestamp ) {
			return $timestamp;
		}
	}

	return null;
}

/**
 * Returns a scalar event meta value.
 *
 * @param int    $post_id  Event post ID.
 * @param string $meta_key Meta key.
 * @return string Scalar meta value.
 */
function kiyose_get_event_meta_value( $post_id, $meta_key ) {
	$value = get_post_meta( $post_id, $meta_key, true );

	if ( is_array( $value ) ) {
		$value = reset( $value );
	}

	return trim( (string) $value );
}

/**
 * Builds previous/next event navigation markup.
 *
 * @param int $previous_event_id Previous event post ID.
 * @param int $next_event_id     Next event post ID.
 * @return string Navigation HTML.
 */
function kiyose_get_event_post_navigation_markup( $previous_event_id, $next_event_id ) {
	$links = '';

	if ( 0 !== $previous_event_id ) {
		$links .= kiyose_get_event_post_navigation_link( $previous_event_id, true );
	}

	if ( 0 !== $next_event_id ) {
		$links .= kiyose_get_event_post_navigation_link( $next_event_id, false );
	}

	if ( '' === $links ) {
		return '';
	}

	return sprintf(
		'<nav class="navigation post-navigation" aria-label="%1$s"><div class="nav-links">%2$s</div></nav>',
		esc_attr( __( 'Navigation entre événements', 'kiyose' ) ),
		$links
	);
}

/**
 * Builds one event navigation link.
 *
 * @param int  $event_id    Event post ID.
 * @param bool $is_previous Whether the link points to the previous event.
 * @return string Navigation link HTML.
 */
function kiyose_get_event_post_navigation_link( $event_id, $is_previous ) {
	$class = $is_previous ? 'nav-previous' : 'nav-next';
	$label = $is_previous ? esc_html__( 'Événement précédent', 'kiyose' ) : esc_html__( 'Événement suivant', 'kiyose' );
	$rel   = $is_previous ? 'prev' : 'next';
	$title = get_the_title( $event_id );
	$url   = get_permalink( $event_id );

	return sprintf(
		'<div class="%1$s"><a href="%2$s" rel="%3$s"><span class="nav-subtitle">%4$s</span><span class="nav-title">%5$s</span></a></div>',
		esc_attr( $class ),
		esc_url( $url ),
		esc_attr( $rel ),
		$label,
		esc_html( $title )
	);
}
