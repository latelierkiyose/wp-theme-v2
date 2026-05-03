<?php
/**
 * Template part for rendering small dynamic SVG decorative elements.
 *
 * @package Kiyose
 * @since   0.2.0
 *
 * @param array $args {
 *     Optional. Element parameters.
 *
 *     @type string $type     Element type: 'spiral', 'squiggle', 'mini-splash', 'virgules', 'cross'. Default 'spiral'.
 *     @type string $color    CSS variable name for color. Default '--kiyose-color-accent'.
 *     @type int    $size     Size in pixels. Default 80.
 *     @type int    $rotation Rotation in degrees. Default 0.
 *     @type bool   $float    Whether to add float animation class. Default false.
 *     @type string $top      CSS value for top position. Default unset.
 *     @type string $right    CSS value for right position. Default unset.
 *     @type string $bottom   CSS value for bottom position. Default unset.
 *     @type string $left     CSS value for left position. Default unset.
 * }
 */

defined( 'ABSPATH' ) || exit;

$kiyose_args = isset( $args ) && is_array( $args ) ? $args : array();

echo kiyose_render_decorative_scribble_svg( $kiyose_args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
