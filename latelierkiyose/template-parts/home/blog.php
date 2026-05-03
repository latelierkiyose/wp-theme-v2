<?php
/**
 * Home blog section.
 *
 * @package Kiyose
 * @since   0.2.7
 */

defined( 'ABSPATH' ) || exit;

?>

<section class="home-blog" aria-labelledby="blog-title">
	<?php
	get_template_part(
		'template-parts/decorative',
		'scribbles',
		array(
			'type'     => 'cross',
			'color'    => '--kiyose-color-accent',
			'size'     => 50,
			'rotation' => 22,
			'float'    => true,
			'top'      => '6%',
			'right'    => '7%',
		)
	);
	get_template_part(
		'template-parts/decorative',
		'scribbles',
		array(
			'type'     => 'virgules',
			'color'    => '--kiyose-color-primary',
			'size'     => 50,
			'rotation' => -18,
			'float'    => false,
			'bottom'   => '10%',
			'left'     => '2%',
		)
	);
	get_template_part(
		'template-parts/decorative',
		'scribbles',
		array(
			'type'     => 'mini-splash',
			'color'    => '--kiyose-color-warm-yellow',
			'size'     => 55,
			'rotation' => 12,
			'float'    => false,
			'top'      => '40%',
			'left'     => '6%',
		)
	);
	?>
	<h2 id="blog-title" class="home-blog__title"><?php esc_html_e( 'Actualités', 'kiyose' ); ?></h2>
	<?php
	$kiyose_blog_query = new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( $kiyose_blog_query->have_posts() ) :
		?>
		<div class="home-blog__grid">
			<?php
			while ( $kiyose_blog_query->have_posts() ) :
				$kiyose_blog_query->the_post();
				get_template_part( 'template-parts/content', 'blog' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="button button--secondary home-blog__cta">
			<?php esc_html_e( 'Toutes les actualités', 'kiyose' ); ?>
		</a>
		<?php
	else :
		?>
		<p class="home-blog__empty"><?php esc_html_e( 'Aucune actualité disponible pour le moment.', 'kiyose' ); ?></p>
		<?php
	endif;
	?>
</section>
