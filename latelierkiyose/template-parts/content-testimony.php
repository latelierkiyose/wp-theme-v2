<?php
/**
 * Template part for displaying a testimony card.
 *
 * @package Kiyose
 * @since   0.1.6
 */

$kiyose_context_key = get_post_meta( get_the_ID(), 'kiyose_testimony_context', true );
if ( empty( $kiyose_context_key ) ) {
	$kiyose_context_key = 'individual';
}

$kiyose_context_label = kiyose_get_testimony_context_label( $kiyose_context_key );
?>

<blockquote class="testimony-card">
	<div class="testimony-card__quote">
		<?php the_content(); ?>
	</div>
	<footer class="testimony-card__attribution">
		<cite class="testimony-card__name">
			<?php the_title(); ?>
		</cite>
		<?php if ( ! empty( $kiyose_context_label ) ) : ?>
			<span class="testimony-card__context">
				<?php echo esc_html( $kiyose_context_label ); ?>
			</span>
		<?php endif; ?>
	</footer>
</blockquote>
