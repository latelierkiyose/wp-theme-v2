<?php
/**
 * L'Atelier Kiyose - Search results template.
 *
 * @package Kiyose
 * @since   0.1.0
 */

get_header();
?>

<main id="main" class="site-main">
	<section class="search-results">
		<header class="search-results__header">
			<h1>
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Résultats de recherche pour « %s »', 'kiyose' ),
					'<span class="search-results__query">' . esc_html( get_search_query() ) . '</span>'
				);
				?>
			</h1>
		</header>

		<div class="search-results__form">
			<?php get_search_form(); ?>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="search-results__list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'search-result' ); ?>>
						<h2 class="search-result__title">
							<a href="<?php the_permalink(); ?>">
								<?php the_title(); ?>
							</a>
						</h2>
						<div class="search-result__excerpt">
							<?php the_excerpt(); ?>
						</div>
						<a href="<?php the_permalink(); ?>" class="search-result__link">
							<?php esc_html_e( 'Lire la suite', 'kiyose' ); ?>
						</a>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 2,
					'prev_text'          => esc_html__( 'Précédent', 'kiyose' ),
					'next_text'          => esc_html__( 'Suivant', 'kiyose' ),
					'screen_reader_text' => esc_html__( 'Navigation des résultats', 'kiyose' ),
				)
			);
			?>

		<?php else : ?>
			<div class="search-results__empty">
				<p><?php esc_html_e( 'Aucun résultat trouvé pour votre recherche.', 'kiyose' ); ?></p>
				<p><strong><?php esc_html_e( 'Suggestions :', 'kiyose' ); ?></strong></p>
				<ul>
					<li><?php esc_html_e( 'Vérifiez l\'orthographe des mots-clés', 'kiyose' ); ?></li>
					<li><?php esc_html_e( 'Essayez des termes plus généraux', 'kiyose' ); ?></li>
					<li><?php esc_html_e( 'Utilisez le menu de navigation pour explorer le site', 'kiyose' ); ?></li>
				</ul>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
