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
	<!-- Decorative bubbles (Kintsugi visual elements) -->
	<svg class="bubble bubble--1" aria-hidden="true" width="40" height="40">
		<circle cx="20" cy="20" r="15" fill="var(--color-primary)" opacity="0.7"/>
	</svg>
	<svg class="bubble bubble--2" aria-hidden="true" width="30" height="30">
		<circle cx="15" cy="15" r="12" fill="var(--color-secondary)" opacity="0.6"/>
	</svg>
</a>
