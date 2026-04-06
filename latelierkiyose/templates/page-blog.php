<?php
/**
 * Template Name: Blog / Actualités
 *
 * @package Kiyose
 * @since   0.1.0
 */

get_header();

// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- $paged est lue depuis get_query_var, non modifiée globalement.
$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );

$kiyose_blog_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => get_option( 'posts_per_page' ),
		'paged'          => $paged,
	)
);
?>

<main id="main" class="site-main" tabindex="-1">
	<section class="blog-archive">
		<header class="blog-archive__header">
			<h1 class="blog-archive__title"><?php the_title(); ?></h1>
		</header>

		<?php if ( $kiyose_blog_query->have_posts() ) : ?>
			<div class="blog-archive__grid">
				<?php
				while ( $kiyose_blog_query->have_posts() ) :
					$kiyose_blog_query->the_post();
					get_template_part( 'template-parts/content', 'blog' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<?php
			// Pagination — swap temporaire de $wp_query pour the_posts_pagination().
			global $wp_query;
			$kiyose_original_query = $wp_query;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Swap temporaire pour pagination.
			$wp_query = $kiyose_blog_query;

			the_posts_pagination(
				array(
					'mid_size'           => 2,
					'prev_text'          => __( 'Précédent', 'kiyose' ),
					'next_text'          => __( 'Suivant', 'kiyose' ),
					'screen_reader_text' => __( 'Pagination du blog', 'kiyose' ),
				)
			);

			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restauration query originale.
			$wp_query = $kiyose_original_query;
			?>
		<?php else : ?>
			<p class="blog-archive__no-posts"><?php esc_html_e( 'Aucun article pour le moment.', 'kiyose' ); ?></p>
		<?php endif; ?>
	</section>
	<?php get_template_part( 'template-parts/page-shapes', 'container' ); ?>
</main>

<?php
get_footer();
