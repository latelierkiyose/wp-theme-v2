<?php
/**
 * Template part for displaying blog posts in archives and search results.
 *
 * @package Kiyose
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

$kiyose_variant       = kiyose_get_blog_card_variant( $args ?? array() );
$kiyose_card_classes  = kiyose_get_blog_card_classes( $kiyose_variant );
$kiyose_has_thumbnail = has_post_thumbnail();
$kiyose_body_classes  = 'blog-card__body';

if ( $kiyose_has_thumbnail ) {
	$kiyose_body_classes .= ' blog-card__body--with-thumbnail';
}
?>

<article <?php post_class( $kiyose_card_classes ); ?>>
	<?php if ( $kiyose_has_thumbnail && 'card' === $kiyose_variant ) : ?>
		<div class="blog-card__thumbnail">
			<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
				the_post_thumbnail(
					'medium_large',
					array(
						'loading' => 'lazy',
						'alt'     => get_the_title(),
					)
				);
				?>
			</a>
		</div>
	<?php endif; ?>

	<div class="blog-card__content">
		<header class="blog-card__header">
			<time class="blog-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
			<h2 class="blog-card__title">
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h2>
		</header>

		<?php if ( 'archive' === $kiyose_variant ) : ?>
			<div class="<?php echo esc_attr( $kiyose_body_classes ); ?>">
				<?php if ( $kiyose_has_thumbnail ) : ?>
					<div class="blog-card__thumbnail">
						<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
							<?php
							the_post_thumbnail(
								'medium_large',
								array(
									'loading' => 'lazy',
									'alt'     => get_the_title(),
								)
							);
							?>
						</a>
					</div>
				<?php endif; ?>

				<div class="blog-card__excerpt">
					<?php the_excerpt(); ?>
				</div>
			</div>
		<?php else : ?>
			<div class="blog-card__excerpt">
				<?php the_excerpt(); ?>
			</div>
		<?php endif; ?>

		<a
			href="<?php the_permalink(); ?>"
			class="blog-card__read-more"
			aria-label="<?php echo esc_attr( sprintf( /* translators: %s: title of the blog post */ __( 'Lire la suite de %s', 'kiyose' ), get_the_title() ) ); ?>"
		>
			<?php esc_html_e( 'Lire la suite', 'kiyose' ); ?>
		</a>
	</div>
</article>
