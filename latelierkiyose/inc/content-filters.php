<?php
/**
 * Content filters - Auto-generate heading IDs.
 *
 * Automatically adds ID attributes to H2 headings that don't have one,
 * to enable anchor navigation with [kiyose_signets] shortcode.
 *
 * @package Kiyose
 * @since   0.2.4
 */

/**
 * Auto-generate IDs for H2 headings without IDs.
 *
 * Filters the_content to add slugified IDs to H2 headings that don't already have one.
 * This ensures all H2s can be used as anchor targets for the signets navigation.
 *
 * @param string $content Post content.
 * @return string Modified content with IDs added to H2s.
 */
function kiyose_auto_add_heading_ids( $content ) {
	// Only process if content contains H2 headings.
	if ( false === strpos( $content, '<h2' ) ) {
		return $content;
	}

	// Track used IDs to prevent duplicates.
	$used_ids = array();

	// Callback function to process each H2.
	$callback = function ( $matches ) use ( &$used_ids ) {
		$full_tag = $matches[0];
		$attrs    = $matches[1];
		$text     = $matches[2];

		// Skip if already has an ID attribute.
		if ( preg_match( '/id=["\']([^"\']+)["\']/i', $attrs ) ) {
			return $full_tag;
		}

		// Generate ID from heading text.
		$heading_text = wp_strip_all_tags( $text );
		$heading_id   = sanitize_title( $heading_text );

		// Handle empty ID (shouldn't happen but safety first).
		if ( empty( $heading_id ) ) {
			$heading_id = 'heading-' . wp_rand( 1000, 9999 );
		}

		// Handle duplicate IDs by appending a number.
		$original_id = $heading_id;
		$counter     = 2;
		while ( in_array( $heading_id, $used_ids, true ) ) {
			$heading_id = $original_id . '-' . $counter;
			++$counter;
		}

		// Track this ID.
		$used_ids[] = $heading_id;

		// Add ID to the H2 tag.
		// If there are existing attributes, add ID to them; otherwise create attrs.
		if ( ! empty( trim( $attrs ) ) ) {
			$new_tag = '<h2 ' . trim( $attrs ) . ' id="' . esc_attr( $heading_id ) . '">' . $text . '</h2>';
		} else {
			$new_tag = '<h2 id="' . esc_attr( $heading_id ) . '">' . $text . '</h2>';
		}

		return $new_tag;
	};

	// Process all H2 headings.
	$content = preg_replace_callback(
		'/<h2\s*([^>]*)>(.*?)<\/h2>/i',
		$callback,
		$content
	);

	return $content;
}
add_filter( 'the_content', 'kiyose_auto_add_heading_ids', 10 );
