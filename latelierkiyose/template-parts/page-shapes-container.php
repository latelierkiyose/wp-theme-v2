<?php
/**
 * Renders the decorative shapes container for non-homepage templates.
 * Called from each template before </main>. No output if no shapes are configured.
 *
 * @package Kiyose
 * @since   2.5.0
 */

defined( 'ABSPATH' ) || exit;

$kiyose_shapes = kiyose_get_page_shapes();

if ( empty( $kiyose_shapes ) ) {
	return;
}
?>
<div class="deco-page-shapes" aria-hidden="true" data-deco-density-spacing="180">
	<?php foreach ( $kiyose_shapes as $kiyose_shape ) : ?>
		<?php
		get_template_part(
			'template-parts/decorative',
			'shapes',
			$kiyose_shape
		);
		?>
	<?php endforeach; ?>
</div>
