<?php
/**
 * Home events section.
 *
 * @package Kiyose
 * @since   0.2.7
 */

defined( 'ABSPATH' ) || exit;

?>

<section class="home-events" aria-labelledby="events-title">
	<?php
	get_template_part(
		'template-parts/decorative',
		'scribbles',
		array(
			'type'     => 'squiggle',
			'color'    => '--kiyose-color-warm-yellow',
			'size'     => 120,
			'rotation' => 15,
			'float'    => true,
			'top'      => '8%',
			'right'    => '3%',
		)
	);
	get_template_part(
		'template-parts/decorative',
		'scribbles',
		array(
			'type'     => 'spiral',
			'color'    => '--kiyose-color-primary',
			'size'     => 65,
			'rotation' => -22,
			'float'    => false,
			'bottom'   => '12%',
			'left'     => '3%',
		)
	);
	get_template_part(
		'template-parts/decorative',
		'scribbles',
		array(
			'type'     => 'mini-splash',
			'color'    => '--kiyose-color-accent',
			'size'     => 50,
			'rotation' => 8,
			'float'    => false,
			'top'      => '45%',
			'left'     => '1%',
		)
	);
	?>
	<h2 id="events-title" class="home-events__title"><?php esc_html_e( 'Prochaines dates', 'kiyose' ); ?></h2>
	<div class="home-events__list">
		<?php
		if ( shortcode_exists( 'events_list' ) ) {
			echo do_shortcode( '[events_list limit="4" scope="future" orderby="event_start_date" order="ASC"]' );
		} else {
			?>
			<p class="home-events__placeholder">
				<?php esc_html_e( 'Les prochains événements seront affichés ici une fois le plugin Events Manager activé.', 'kiyose' ); ?>
			</p>
			<?php
		}
		?>
	</div>
	<a href="<?php echo esc_url( home_url( '/calendrier-tarifs/' ) ); ?>" class="button button--secondary home-events__cta">
		<?php esc_html_e( 'Voir tout le calendrier', 'kiyose' ); ?>
	</a>
</section>
