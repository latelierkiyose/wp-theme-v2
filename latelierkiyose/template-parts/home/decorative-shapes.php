<?php
/**
 * Home decorative page shapes.
 *
 * @package Kiyose
 * @since   0.2.7
 *
 * @param array $args {
 *     Template part arguments.
 *
 *     @type array<int, array<string, mixed>> $shapes Shape configurations.
 * }
 */

$kiyose_home_shapes = $args['shapes'] ?? kiyose_get_home_page_shapes();
?>

<div class="deco-page-shapes" aria-hidden="true">
	<?php
	foreach ( $kiyose_home_shapes as $kiyose_shape ) {
		get_template_part( 'template-parts/decorative', 'shapes', $kiyose_shape );
	}
	?>
</div>
