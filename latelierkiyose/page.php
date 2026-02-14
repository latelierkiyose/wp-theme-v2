<?php
/**
 * L'Atelier Kiyose - Default page template.
 *
 * @package Kiyose
 * @since   0.1.0
 */

get_header();
?>

<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'page' ); ?>>
			<header class="page__header">
				<h1 class="page__title"><?php the_title(); ?></h1>
			</header>

			<div class="page__content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
