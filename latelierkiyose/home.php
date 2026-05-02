<?php
/**
 * Template for displaying the blog posts index.
 *
 * @package Kiyose
 * @since   0.2.7
 */

get_header();
?>

<main id="main" class="site-main" tabindex="-1">
	<section class="blog-archive blog-archive--index" aria-labelledby="blog-archive-title">
		<header class="blog-archive__header">
			<h1 id="blog-archive-title" class="blog-archive__title"><?php esc_html_e( 'Actus / Blog', 'kiyose' ); ?></h1>
		</header>

		<div class="blog-archive__layout">
			<div class="blog-archive__main">
				<?php if ( have_posts() ) : ?>
					<div class="blog-archive__grid">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part(
								'template-parts/content',
								'blog',
								array(
									'variant' => 'archive',
								)
							);
						endwhile;
						?>
					</div>

					<?php
					the_posts_pagination(
						array(
							'mid_size'           => 2,
							'prev_text'          => __( 'Précédent', 'kiyose' ),
							'next_text'          => __( 'Suivant', 'kiyose' ),
							'screen_reader_text' => __( 'Navigation des articles', 'kiyose' ),
						)
					);
					?>
				<?php else : ?>
					<p class="blog-archive__no-posts"><?php esc_html_e( 'Aucun article pour le moment.', 'kiyose' ); ?></p>
				<?php endif; ?>
			</div>

			<aside class="blog-archive__sidebar" aria-label="<?php esc_attr_e( 'Navigation des actualités', 'kiyose' ); ?>">
				<?php get_template_part( 'template-parts/blog-sidebar' ); ?>
			</aside>
		</div>
	</section>
	<?php get_template_part( 'template-parts/page-shapes', 'container' ); ?>
</main>

<?php
get_footer();
