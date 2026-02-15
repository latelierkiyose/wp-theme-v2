<?php
/**
 * PHPUnit Bootstrap File
 *
 * @package Kiyose
 */

// Define constants used in tests.
define( 'KIYOSE_TESTS', true );

// Load Composer autoloader.
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Mock WordPress functions for testing.
 *
 * Since we're running tests without a full WordPress installation,
 * we need to mock basic WordPress functions used in the theme.
 */

if ( ! function_exists( 'add_action' ) ) {
	/**
	 * Mock add_action function.
	 *
	 * @param string   $hook Hook name.
	 * @param callable $callback Callback function.
	 * @param int      $priority Priority.
	 * @param int      $accepted_args Number of arguments.
	 * @return bool
	 */
	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

if ( ! function_exists( 'register_post_type' ) ) {
	/**
	 * Mock register_post_type function.
	 *
	 * @param string $post_type Post type name.
	 * @param array  $args Arguments.
	 * @return object
	 */
	function register_post_type( $post_type, $args = array() ) {
		return (object) array(
			'name'   => $post_type,
			'labels' => $args['labels'] ?? array(),
		);
	}
}

if ( ! function_exists( '__' ) ) {
	/**
	 * Mock translation function.
	 *
	 * @param string $text Text to translate.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	/**
	 * Mock escaped translation function.
	 *
	 * @param string $text Text to translate.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function esc_html__( $text, $domain = 'default' ) {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'add_shortcode' ) ) {
	/**
	 * Mock add_shortcode function.
	 *
	 * @param string   $tag Shortcode tag.
	 * @param callable $callback Callback function.
	 * @return bool
	 */
	function add_shortcode( $tag, $callback ) {
		return true;
	}
}

if ( ! function_exists( 'shortcode_atts' ) ) {
	/**
	 * Mock shortcode_atts function.
	 *
	 * @param array  $defaults Default attributes.
	 * @param array  $atts User attributes.
	 * @param string $shortcode Shortcode name.
	 * @return array
	 */
	function shortcode_atts( $defaults, $atts, $shortcode = '' ) {
		return array_merge( $defaults, (array) $atts );
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	/**
	 * Mock sanitize_key function.
	 *
	 * @param string $key Key to sanitize.
	 * @return string
	 */
	function sanitize_key( $key ) {
		return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', $key ) );
	}
}

// Load theme files needed for tests.
require_once __DIR__ . '/../inc/custom-post-types.php';
require_once __DIR__ . '/../inc/shortcodes.php';
