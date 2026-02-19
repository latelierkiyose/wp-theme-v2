<?php
/**
 * Template part for decorative collage elements.
 *
 * Outputs torn paper SVG shapes and micro-doodle elements.
 * All elements are purely decorative (aria-hidden, pointer-events: none).
 *
 * @package Kiyose
 * @since   2.0.0
 *
 * @param array $args {
 *     @type array $papers Array of torn paper definitions.
 *     @type array $micros Array of micro-element definitions.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$kiyose_papers = isset( $args['papers'] ) ? $args['papers'] : array();
$kiyose_micros = isset( $args['micros'] ) ? $args['micros'] : array();
?>

<?php foreach ( $kiyose_papers as $kiyose_paper ) : ?>
	<svg
		class="collage-paper <?php echo esc_attr( $kiyose_paper['class'] ); ?>"
		aria-hidden="true"
		role="presentation"
		viewBox="0 0 <?php echo esc_attr( $kiyose_paper['size'][0] ); ?> <?php echo esc_attr( $kiyose_paper['size'][1] ); ?>"
		fill="var(<?php echo esc_attr( $kiyose_paper['color'] ); ?>)"
		xmlns="http://www.w3.org/2000/svg"
	>
		<path d="<?php echo esc_attr( $kiyose_paper['path'] ); ?>"/>
	</svg>
<?php endforeach; ?>

<?php foreach ( $kiyose_micros as $kiyose_index => $kiyose_micro ) : ?>
	<svg
		class="collage-micro collage-micro--<?php echo esc_attr( $kiyose_micro['type'] ); ?>"
		aria-hidden="true"
		role="presentation"
		style="<?php echo esc_attr( $kiyose_micro['style'] ); ?>; transition-delay: <?php echo esc_attr( $kiyose_index * 100 ); ?>ms"
		viewBox="0 0 <?php echo esc_attr( $kiyose_micro['viewbox'] ); ?>"
		xmlns="http://www.w3.org/2000/svg"
	>
		<?php echo $kiyose_micro['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-built SVG markup. ?>
	</svg>
<?php endforeach; ?>
