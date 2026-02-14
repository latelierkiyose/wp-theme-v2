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
			'columns' => 3,
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
		$columns = 3;
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
			// Affichage carousel accessible.
			$total_slides = $testimonials_query->post_count;
			?>
			<div
				class="carousel"
				role="region"
				aria-roledescription="carousel"
				aria-label="<?php esc_attr_e( 'Témoignages de participants', 'kiyose' ); ?>"
			>
				<div class="carousel__controls">
					<button class="carousel__prev" aria-label="<?php esc_attr_e( 'Témoignage précédent', 'kiyose' ); ?>">←</button>
					<button class="carousel__pause" aria-label="<?php esc_attr_e( 'Mettre en pause le défilement', 'kiyose' ); ?>">⏸</button>
					<button class="carousel__next" aria-label="<?php esc_attr_e( 'Témoignage suivant', 'kiyose' ); ?>">→</button>
				</div>
				<div class="carousel__track" aria-live="polite">
					<?php
					$slide_index = 0;
					while ( $testimonials_query->have_posts() ) {
						$testimonials_query->the_post();
						++$slide_index;
						?>
						<div
							class="carousel__slide <?php echo 0 === ( $slide_index - 1 ) ? 'carousel__slide--active' : ''; ?>"
							role="group"
							aria-roledescription="slide"
							aria-label="<?php echo esc_attr( sprintf( /* translators: 1: current slide number, 2: total number of slides */ __( '%1$d sur %2$d', 'kiyose' ), $slide_index, $total_slides ) ); ?>"
							aria-hidden="<?php echo 0 === ( $slide_index - 1 ) ? 'false' : 'true'; ?>"
						>
							<?php get_template_part( 'template-parts/content', 'testimony' ); ?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
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
