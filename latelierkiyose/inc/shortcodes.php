<?php
/**
 * Shortcodes registration and handlers.
 *
 * @package Kiyose
 * @since   0.1.7
 */

/**
 * Display testimonials grid or carousel.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function kiyose_testimonials_shortcode( $atts ) {
	// Sanitize et merge des attributs.
	$args = shortcode_atts(
		array(
			'display' => 'grid',
			'limit'   => -1,
			'context' => '',
			'columns' => 2,
		),
		$atts,
		'kiyose_testimonials'
	);

	// Sanitization.
	$display = sanitize_key( $args['display'] );
	$limit   = intval( $args['limit'] );
	$context = sanitize_key( $args['context'] );
	$columns = intval( $args['columns'] );

	// Validation.
	if ( ! in_array( $display, array( 'grid', 'carousel' ), true ) ) {
		$display = 'grid';
	}
	if ( $columns < 1 || $columns > 4 ) {
		$columns = 2;
	}

	// WP_Query.
	$query_args = array(
		'post_type'      => 'kiyose_testimony',
		'posts_per_page' => $limit,
		'orderby'        => 'rand',
		'post_status'    => 'publish',
	);

	// Filtrer par contexte si spécifié.
	if ( ! empty( $context ) ) {
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Limited number of testimonies, acceptable use.
		$query_args['meta_query'] = array(
			array(
				'key'   => 'kiyose_testimony_context',
				'value' => $context,
			),
		);
	}

	$testimonials_query = new WP_Query( $query_args );

	// Début output buffering.
	ob_start();

	if ( $testimonials_query->have_posts() ) {
		if ( 'carousel' === $display ) {
			echo kiyose_get_testimonials_carousel_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$testimonials_query,
				__( 'Témoignages de participants', 'kiyose' )
			);
		} else {
			// Affichage grille.
			echo '<div class="testimonials-grid testimonials-grid--columns-' . esc_attr( $columns ) . '">';
			while ( $testimonials_query->have_posts() ) {
				$testimonials_query->the_post();
				get_template_part( 'template-parts/content', 'testimony' );
			}
			echo '</div>';
		}
	} else {
		echo '<p class="testimonials-empty">' . esc_html__( 'Aucun témoignage disponible pour le moment.', 'kiyose' ) . '</p>';
	}

	wp_reset_postdata();

	return ob_get_clean();
}
add_shortcode( 'kiyose_testimonials', 'kiyose_testimonials_shortcode' );

/**
 * Display page navigation signets (anchor links).
 *
 * Usage:
 * - Auto-scan H2: [kiyose_signets][/kiyose_signets]
 * - Manual: [kiyose_signets]Title | #anchor[/kiyose_signets]
 *
 * @param array  $atts    Shortcode attributes (unused).
 * @param string $content Shortcode content (optional).
 * @return string HTML output.
 */
function kiyose_signets_shortcode( $atts, $content = null ) {
	$links = array();

	// Parse manual content if provided.
	if ( ! empty( $content ) ) {
		$lines = explode( "\n", trim( $content ) );
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( empty( $line ) ) {
				continue;
			}

			// Format: "Link Text | #anchor".
			if ( strpos( $line, '|' ) !== false ) {
				list( $text, $anchor ) = array_map( 'trim', explode( '|', $line, 2 ) );
				if ( ! empty( $text ) && ! empty( $anchor ) ) {
					$links[] = array(
						'text'   => $text,
						'anchor' => $anchor,
					);
				}
			}
		}
	}

	// Fallback: Auto-scan H2 headings from page content.
	if ( empty( $links ) ) {
		global $post;
		if ( $post && ! empty( $post->post_content ) ) {
			// Extract H2 headings with optional IDs.
			// Match H2 tags and capture ID attribute if present.
			preg_match_all( '/<h2\b([^>]*)>(.*?)<\/h2>/i', $post->post_content, $matches, PREG_SET_ORDER );

			foreach ( $matches as $match ) {
				$attributes   = $match[1];
				$heading_text = wp_strip_all_tags( $match[2] );
				$heading_id   = '';

				// Extract ID from attributes if present.
				if ( preg_match( '/id=["\']([^"\']+)["\']/i', $attributes, $id_match ) ) {
					$heading_id = $id_match[1];
				}

				// Generate anchor from text if no ID.
				if ( empty( $heading_id ) ) {
					$heading_id = sanitize_title( $heading_text );
				}

				if ( ! empty( $heading_text ) && ! empty( $heading_id ) ) {
					$links[] = array(
						'text'   => $heading_text,
						'anchor' => '#' . $heading_id,
					);
				}
			}
		}
	}

	// No links found.
	if ( empty( $links ) ) {
		return '';
	}

	// Build HTML output.
	ob_start();
	?>
	<nav class="kiyose-signets" aria-label="<?php esc_attr_e( 'Navigation dans la page', 'kiyose' ); ?>">
		<ul class="kiyose-signets__list">
			<?php foreach ( $links as $link ) : ?>
				<li class="kiyose-signets__item">
					<a href="<?php echo esc_url( $link['anchor'] ); ?>" class="kiyose-signets__link">
						<?php echo esc_html( $link['text'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
	return ob_get_clean();
}
add_shortcode( 'kiyose_signets', 'kiyose_signets_shortcode' );

/**
 * Display a callout box to highlight important content.
 *
 * Usage:
 * - Simple: [kiyose_callout]Important content here[/kiyose_callout]
 * - With title: [kiyose_callout titre="Bon à savoir"]Content[/kiyose_callout]
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode enclosed content.
 * @return string HTML output.
 *
 * @since 2.1.0
 */
function kiyose_callout_shortcode( $atts, $content = null ) {
	if ( empty( $content ) ) {
		return '';
	}

	$args = shortcode_atts(
		array(
			'titre' => '',
		),
		$atts,
		'kiyose_callout'
	);

	$titre = sanitize_text_field( $args['titre'] );

	$output = '<div class="callout-box" role="note">';

	if ( ! empty( $titre ) ) {
		$output .= '<p class="callout-box__title"><strong>' . esc_html( $titre ) . '</strong></p>';
	}

	$output .= '<div class="callout-box__content">';
	$output .= wp_kses_post( wpautop( do_shortcode( $content ) ) );
	$output .= '</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode( 'kiyose_callout', 'kiyose_callout_shortcode' );
