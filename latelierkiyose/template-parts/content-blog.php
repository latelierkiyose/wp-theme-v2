<?php
/**
 * Template part for displaying blog posts in archives and search results.
 *
 * @package Kiyose
 * @since   0.1.0
 */

?>

<article class="blog-card" <?php post_class(); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
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
		<div class="blog-card__excerpt">
			<?php the_excerpt(); ?>
		</div>
		<a href="<?php the_permalink(); ?>" class="blog-card__read-more" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: title of the blog post */ __( 'Lire la suite de %s', 'kiyose' ), get_the_title() ) ); ?>">
			<?php esc_html_e( 'Lire la suite', 'kiyose' ); ?>
		</a>
	</div>
</article>
