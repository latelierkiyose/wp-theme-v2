<?php
/**
 * L'Atelier Kiyose - Main template fallback.
 *
 * This template is rarely used when other specific templates exist.
 * It displays a basic loop for any content type.
 *
 * @package Kiyose
 * @since   0.1.0
 */

get_header();
?>

<main id="main" class="site-main">
	<div class="container">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php
						if ( is_singular() ) {
							the_title( '<h1 class="entry-title">', '</h1>' );
						} else {
							the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
						}
						?>
					</header>

					<div class="entry-content">
						<?php
						if ( is_singular() ) {
							the_content();
						} else {
							the_excerpt();
						}
						?>
					</div>
				</article>
				<?php
			endwhile;

			the_posts_navigation();
		else :
			?>
			<div class="no-content">
				<p><?php esc_html_e( 'Aucun contenu trouvé.', 'kiyose' ); ?></p>
			</div>
			<?php
		endif;
		?>
	</div>
</main>

<?php
get_footer();
