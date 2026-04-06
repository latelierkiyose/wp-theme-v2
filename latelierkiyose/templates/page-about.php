<?php
/**
 * Template Name: À propos
 *
 * Template pour la page "À propos" présentant Virginie Le Mignon,
 * sa philosophie Kintsugi, son parcours et ses qualifications.
 *
 * @package Kiyose
 */

get_header();
?>

<main id="main" class="site-main" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'about-page' ); ?>>
			<header class="about-page__header">
				<h1 class="about-page__title"><?php the_title(); ?></h1>
			</header>

			<div class="about-page__content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
	?>
	<?php get_template_part( 'template-parts/page-shapes', 'container' ); ?>
</main>

<?php
get_footer();
