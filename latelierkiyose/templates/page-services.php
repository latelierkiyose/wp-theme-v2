<?php
/**
 * Template Name: Page de service
 *
 * Template générique pour les pages de services
 * (art-thérapie, rigologie, bols tibétains, ateliers philosophie).
 *
 * @package Kiyose
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main" tabindex="-1">
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

			<?php
			$kiyose_service_cta_links = kiyose_get_service_cta_links( get_the_ID() );
			if ( ! empty( $kiyose_service_cta_links ) ) :
				?>
				<footer class="service-page__footer">
					<div class="service-page__cta">
						<?php foreach ( $kiyose_service_cta_links as $kiyose_service_cta_link ) : ?>
							<a href="<?php echo esc_url( $kiyose_service_cta_link['url'] ); ?>" class="<?php echo esc_attr( $kiyose_service_cta_link['class'] ); ?>">
								<?php echo esc_html( $kiyose_service_cta_link['label'] ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</footer>
			<?php endif; ?>
		</article>
		<?php
	endwhile;
	?>
	<?php get_template_part( 'template-parts/page-shapes', 'container' ); ?>
</main>

<?php
get_footer();
