<?php
/**
 * Template Name: Page de service
 *
 * Template générique pour les pages de services
 * (art-thérapie, rigologie, bols tibétains, ateliers philosophie).
 *
 * @package Kiyose
 */

get_header();
?>

<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'service-page' ); ?>>
			<header class="service-page__header">
				<h1 class="service-page__title"><?php the_title(); ?></h1>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="service-page__hero-image">
						<?php the_post_thumbnail( 'kiyose-hero', array( 'alt' => get_the_title() ) ); ?>
					</div>
				<?php endif; ?>
			</header>

			<div class="service-page__content">
				<?php the_content(); ?>
			</div>

			<footer class="service-page__footer">
				<div class="service-page__cta">
					<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="button button--primary">
						Me contacter
					</a>
					<a href="<?php echo esc_url( home_url( '/calendrier-tarifs/' ) ); ?>" class="button button--secondary">
						Voir le calendrier
					</a>
				</div>
			</footer>
		</article>
		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
