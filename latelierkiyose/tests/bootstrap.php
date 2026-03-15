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

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * Mock sanitize_text_field function.
	 *
	 * WordPress removes the content of script/style tags (not just the tags),
	 * then strips all remaining HTML tags. This mock approximates that behavior.
	 *
	 * @param string $text Text to sanitize.
	 * @return string Sanitized text.
	 */
	function sanitize_text_field( $text ) {
		$text = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', (string) $text );
		return trim( strip_tags( $text ) );
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( $text ) {
		return trim( (string) $text );
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url ) {
		return (string) $url;
	}
}

if ( ! function_exists( 'add_meta_box' ) ) {
	function add_meta_box( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default' ) {
		return true;
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( $post_id, $key = '', $single = false ) {
		return $single ? '' : array();
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '' ) {
		return 'http://example.com' . $path;
	}
}

if ( ! function_exists( 'wp_nonce_field' ) ) {
	function wp_nonce_field( $action, $name, $referer = true, $echo = true ) {
		return '';
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_textarea' ) ) {
	function esc_textarea( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $url ) {
		return htmlspecialchars( (string) $url, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'wp_get_attachment_image' ) ) {
	function wp_get_attachment_image( $attachment_id, $size = 'thumbnail', $icon = false, $attr = '' ) {
		return '';
	}
}

if ( ! function_exists( 'esc_html_e' ) ) {
	function esc_html_e( $text, $domain = 'default' ) {
		echo htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'checked' ) ) {
	function checked( $checked, $current = true, $echo = true ) {
		return $checked === $current ? ' checked="checked"' : '';
	}
}

if ( ! function_exists( 'update_post_meta' ) ) {
	function update_post_meta( $post_id, $meta_key, $meta_value ) {
		return true;
	}
}

if ( ! function_exists( 'delete_post_meta' ) ) {
	function delete_post_meta( $post_id, $meta_key ) {
		return true;
	}
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
	function wp_verify_nonce( $nonce, $action ) {
		return true;
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) {
		return is_array( $value ) ? array_map( 'stripslashes', $value ) : stripslashes( (string) $value );
	}
}

if ( ! function_exists( 'wp_slash' ) ) {
	function wp_slash( $value ) {
		if ( is_array( $value ) ) {
			return array_map( 'wp_slash', $value );
		}
		return is_string( $value ) ? addslashes( $value ) : $value;
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $maybeint ) {
		return abs( (int) $maybeint );
	}
}

if ( ! function_exists( 'current_user_can' ) ) {
	function current_user_can( $capability ) {
		return true;
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	/**
	 * Mock wp_json_encode function.
	 *
	 * @param mixed $data    Data to encode.
	 * @param int   $options JSON encode options.
	 * @param int   $depth   Maximum depth.
	 * @return string|false JSON encoded string, or false on failure.
	 */
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	/**
	 * Mock wp_kses_post function.
	 *
	 * @param string $data Content to filter.
	 * @return string Filtered content.
	 */
	function wp_kses_post( $data ) {
		return wp_kses( $data, array() );
	}
}

if ( ! function_exists( 'wp_kses' ) ) {
	/**
	 * Mock wp_kses function.
	 *
	 * @param string $data    Content to filter.
	 * @param array  $allowed_html Allowed HTML tags.
	 * @return string Filtered content.
	 */
	function wp_kses( $data, $allowed_html ) {
		return strip_tags( (string) $data );
	}
}

if ( ! function_exists( 'wp_editor' ) ) {
	/**
	 * Mock wp_editor function.
	 *
	 * @param string $content   Initial content.
	 * @param string $editor_id HTML ID for the textarea.
	 * @param array  $settings  Settings array.
	 * @return void
	 */
	function wp_editor( $content, $editor_id, $settings = array() ) {
		// No-op in tests.
	}
}

// Load theme files needed for tests.
require_once __DIR__ . '/../inc/custom-post-types.php';
require_once __DIR__ . '/../inc/shortcodes.php';
require_once __DIR__ . '/../inc/meta-boxes.php';
