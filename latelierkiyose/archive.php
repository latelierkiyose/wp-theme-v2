<?php
/**
 * Template for displaying blog archives.
 *
 * @package Kiyose
 * @since   0.1.0
 */

get_header();
?>

<main id="main" class="site-main">
	<section class="blog-archive">
		<header class="blog-archive__header">
			<h1 class="blog-archive__title"><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<div class="blog-archive__description">', '</div>' ); ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="blog-archive__grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'blog' );
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
	</section>
</main>

<?php
get_footer();
