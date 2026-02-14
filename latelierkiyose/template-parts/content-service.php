<?php
/**
 * Template part for displaying a service card.
 *
 * Used in the homepage "4 Piliers" section to display service cards.
 *
 * @package Kiyose
 * @since   0.1.9
 */

?>

<a href="<?php the_permalink(); ?>" class="service-card" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: service title */ __( 'En savoir plus sur %s', 'kiyose' ), get_the_title() ) ); ?>">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="service-card__image">
			<?php
			the_post_thumbnail(
				'medium',
				array(
					'loading' => 'lazy',
					'alt'     => '',
					'role'    => 'presentation',
				)
			);
			?>
		</div>
	<?php endif; ?>
	<div class="service-card__content">
		<h3 class="service-card__title"><?php the_title(); ?></h3>
		<p class="service-card__description"><?php echo esc_html( get_the_excerpt() ); ?></p>
	</div>
</a>
