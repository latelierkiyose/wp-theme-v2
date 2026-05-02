<?php
/**
 * Blog archive helpers.
 *
 * @package Kiyose
 * @since   0.2.7
 */

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
