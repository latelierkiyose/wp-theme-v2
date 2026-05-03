<?php
/**
 * Template part for the blog archive sidebar.
 *
 * @package Kiyose
 * @since   0.2.7
 */

defined( 'ABSPATH' ) || exit;

$kiyose_blog_categories = get_categories( kiyose_get_blog_sidebar_categories_args() );
?>

<div class="blog-sidebar">
	<section class="blog-sidebar__section blog-sidebar__section--search" aria-labelledby="blog-sidebar-search-title">
		<h2 id="blog-sidebar-search-title" class="blog-sidebar__title"><?php esc_html_e( 'Recherche', 'kiyose' ); ?></h2>
		<?php get_search_form(); ?>
	</section>

	<?php if ( kiyose_has_visible_blog_categories( $kiyose_blog_categories ) ) : ?>
		<section class="blog-sidebar__section blog-sidebar__section--categories" aria-labelledby="blog-sidebar-categories-title">
			<h2 id="blog-sidebar-categories-title" class="blog-sidebar__title"><?php esc_html_e( 'Catégories', 'kiyose' ); ?></h2>
			<ul class="blog-sidebar__categories">
				<?php
				wp_list_categories( kiyose_get_blog_sidebar_categories_args() );
				?>
			</ul>
		</section>
	<?php endif; ?>
</div>
