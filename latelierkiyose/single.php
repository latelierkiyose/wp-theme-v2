<?php
/**
 * Template for displaying single blog posts.
 *
 * @package Kiyose
 * @since   0.1.0
 */

get_header();
?>

<main id="main" class="site-main" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-single' ); ?>>
			<header class="blog-single__header">
				<h1 class="blog-single__title"><?php the_title(); ?></h1>
				<time class="blog-single__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="blog-single__featured-image">
						<?php
						the_post_thumbnail(
							'large',
							array(
								'alt' => get_the_title(),
							)
						);
						?>
					</div>
				<?php endif; ?>
			</header>

			<div class="blog-single__content">
				<?php the_content(); ?>
			</div>

			<footer class="blog-single__footer">
				<?php if ( has_category() ) : ?>
					<div class="blog-single__categories">
						<span class="blog-single__categories-label"><?php esc_html_e( 'Catégories :', 'kiyose' ); ?></span>
						<?php the_category( ', ' ); ?>
					</div>
				<?php endif; ?>

				<?php if ( get_the_tag_list() ) : ?>
					<div class="blog-single__tags">
						<?php the_tags( '<span class="blog-single__tags-label">' . esc_html__( 'Tags :', 'kiyose' ) . '</span> ', ', ' ); ?>
					</div>
				<?php endif; ?>

				<div class="blog-single__share">
					<span class="blog-single__share-label"><?php esc_html_e( 'Partager :', 'kiyose' ); ?></span>
					<?php echo kiyose_get_post_share_links_html( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>

				<nav class="blog-single__navigation" aria-label="<?php esc_attr_e( 'Navigation entre articles', 'kiyose' ); ?>">
					<?php
					the_post_navigation(
						array(
							'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Article précédent', 'kiyose' ) . '</span><span class="nav-title">%title</span>',
							'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Article suivant', 'kiyose' ) . '</span><span class="nav-title">%title</span>',
						)
					);
					?>
				</nav>
			</footer>
		</article>

		<?php
	endwhile;
	?>
	<?php get_template_part( 'template-parts/page-shapes', 'container' ); ?>
</main>

<?php
get_footer();
