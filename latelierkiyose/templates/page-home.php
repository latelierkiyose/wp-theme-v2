<?php
/**
 * Template Name: Page d'accueil
 *
 * @package Kiyose
 * @since   0.1.9
 */

get_header();

$kiyose_home_data = kiyose_get_home_page_data( get_the_ID() );
?>

<main id="main" class="site-main home-page" tabindex="-1">
	<?php
	get_template_part( 'template-parts/home/welcome', null, $kiyose_home_data );
	get_template_part( 'template-parts/home/about-mobile', null, $kiyose_home_data );

	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => '#ffffff',
			'from_bg' => '--kiyose-color-background',
		)
	);

	get_template_part( 'template-parts/home/content-qa', null, $kiyose_home_data );

	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => '--kiyose-color-background',
			'from_bg' => '#ffffff',
		)
	);

	get_template_part( 'template-parts/home/content-free', null, $kiyose_home_data );

	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => '#ffffff',
			'from_bg' => '--kiyose-color-background',
		)
	);

	get_template_part( 'template-parts/home/events' );

	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => '--kiyose-color-background',
			'from_bg' => '#ffffff',
		)
	);

	get_template_part( 'template-parts/home/testimonials' );

	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => '#ffffff',
			'from_bg' => '--kiyose-color-background',
		)
	);

	get_template_part( 'template-parts/home/blog' );
	get_template_part(
		'template-parts/home/decorative-shapes',
		null,
		array(
			'shapes' => kiyose_get_home_page_shapes(),
		)
	);
	?>
</main>

<?php
get_template_part( 'template-parts/home/overlays', null, $kiyose_home_data );

get_footer();
