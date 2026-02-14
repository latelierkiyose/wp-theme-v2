<?php
/**
 * Template Name: Calendrier et tarifs
 *
 * Template for displaying the Calendar and Pricing page with Events Manager integration.
 *
 * @package Kiyose
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'calendar-page' ); ?>>
			<header class="calendar-page__header">
				<h1 class="calendar-page__title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<div class="calendar-page__intro">
						<?php the_excerpt(); ?>
					</div>
				<?php endif; ?>
			</header>

			<div class="calendar-page__content">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'kiyose' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>
		</article>

		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
