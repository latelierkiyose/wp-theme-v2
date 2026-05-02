<?php
/**
 * Template part for rendering large organic SVG decorative shapes.
 *
 * @package Kiyose
 * @since   0.2.0
 *
 * @param array $args {
 *     Optional. Shape parameters.
 *
 *     @type string $mode     Rendering mode: 'shape' or 'separator'. Default 'shape'.
 *     @type string $type     Shape type: 'splash', 'blob', 'brushstroke', 'spiral'. Default 'splash'.
 *     @type string $color    CSS variable name for fill color. Default '--kiyose-color-primary'.
 *     @type float  $opacity  Fill opacity, 0 to 1. Default 0.4.
 *     @type string $size     Size preset: 'sm' (200px), 'md' (350px), 'lg' (500px). Default 'md'.
 *     @type string $position Position class suffix. Default 'top-left'.
 *     @type int    $rotation Rotation in degrees. Default 0.
 *     @type string $top      Vertical offset for margin positions. Default ''.
 *     @type string $from_bg  Separator source background. Default '#ffffff'.
 * }
 */

$kiyose_args = isset( $args ) && is_array( $args ) ? $args : array();
$kiyose_mode = isset( $kiyose_args['mode'] ) ? $kiyose_args['mode'] : 'shape';

if ( 'separator' === $kiyose_mode ) {
	echo kiyose_render_wave_separator_svg( $kiyose_args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

echo kiyose_render_decorative_shape_svg( $kiyose_args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
