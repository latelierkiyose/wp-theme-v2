<?php
/**
 * L'Atelier Kiyose - Main template fallback.
 *
 * @package Kiyose
 * @since   0.1.0
 */

get_header();
?>

<main id="main" class="site-main">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header>

				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;

		the_posts_navigation();
	else :
		?>
		<p><?php esc_html_e( 'Aucun contenu trouvé.', 'kiyose' ); ?></p>
		<?php
	endif;
	?>
</main>

<?php
get_footer();
