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
		echo kiyose_get_testimonials_carousel_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$kiyose_testimonials_query,
			__( 'Témoignages de participants', 'kiyose' )
		);
		wp_reset_postdata();
	else :
		?>
		<p class="home-testimonials__empty"><?php esc_html_e( 'Aucun témoignage disponible pour le moment.', 'kiyose' ); ?></p>
		<?php
	endif;
	?>
</section>
