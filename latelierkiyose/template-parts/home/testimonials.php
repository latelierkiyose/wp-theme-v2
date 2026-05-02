<?php
/**
 * Home testimonials carousel section.
 *
 * @package Kiyose
 * @since   0.2.7
 */

?>

<section class="home-testimonials" aria-labelledby="testimonials-title">
	<?php
	get_template_part(
		'template-parts/decorative',
		'scribbles',
		array(
			'type'     => 'mini-splash',
			'color'    => '--kiyose-color-warm-yellow',
			'size'     => 70,
			'rotation' => 20,
			'float'    => false,
			'top'      => '10%',
			'left'     => '5%',
		)
	);
	get_template_part(
		'template-parts/decorative',
		'scribbles',
		array(
			'type'     => 'squiggle',
			'color'    => '--kiyose-color-primary',
			'size'     => 90,
			'rotation' => -12,
			'float'    => true,
			'bottom'   => '15%',
			'right'    => '2%',
		)
	);
	get_template_part(
		'template-parts/decorative',
		'scribbles',
		array(
			'type'     => 'cross',
			'color'    => '--kiyose-color-accent',
			'size'     => 40,
			'rotation' => 35,
			'float'    => false,
			'top'      => '55%',
			'right'    => '8%',
		)
	);
	?>
	<h2 id="testimonials-title" class="home-testimonials__title"><?php esc_html_e( 'Témoignages', 'kiyose' ); ?></h2>
	<?php
	$kiyose_testimonials_query = new WP_Query(
		array(
			'post_type'      => 'kiyose_testimony',
			'posts_per_page' => 5,
			'orderby'        => 'rand',
		)
	);

	if ( $kiyose_testimonials_query->have_posts() ) :
		?>
		<div
			class="carousel"
			role="region"
			aria-roledescription="carousel"
			aria-label="<?php esc_attr_e( 'Témoignages de participants', 'kiyose' ); ?>"
		>
		<div class="carousel__controls">
			<button class="carousel__prev" aria-label="<?php esc_attr_e( 'Témoignage précédent', 'kiyose' ); ?>">
				<?php echo kiyose_carousel_icon_svg( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<button class="carousel__pause" aria-label="<?php esc_attr_e( 'Mettre en pause le défilement', 'kiyose' ); ?>">
				<span class="carousel__icon carousel__icon--pause"><?php echo kiyose_carousel_icon_svg( 'pause' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="carousel__icon carousel__icon--play" hidden><?php echo kiyose_carousel_icon_svg( 'play' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</button>
			<button class="carousel__next" aria-label="<?php esc_attr_e( 'Témoignage suivant', 'kiyose' ); ?>">
				<?php echo kiyose_carousel_icon_svg( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
			<div class="carousel__track" aria-live="polite">
				<?php
				while ( $kiyose_testimonials_query->have_posts() ) :
					$kiyose_testimonials_query->the_post();
					?>
					<div class="carousel__slide" role="group" aria-roledescription="slide" aria-label="">
						<?php get_template_part( 'template-parts/content', 'testimony' ); ?>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
		<?php
	else :
		?>
		<p class="home-testimonials__empty"><?php esc_html_e( 'Aucun témoignage disponible pour le moment.', 'kiyose' ); ?></p>
		<?php
	endif;
	?>
</section>
