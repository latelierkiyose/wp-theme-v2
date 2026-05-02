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
 * Reset observable WordPress mock state between tests.
 *
 * @return void
 */
function kiyose_reset_test_state(): void {
	$default_shortcodes = $GLOBALS['kiyose_test_default_shortcodes'] ?? array();

	$GLOBALS['kiyose_test_added_meta_boxes']    = array();
	$GLOBALS['kiyose_test_current_screen']      = null;
	$GLOBALS['kiyose_test_did_enqueue_media']  = false;
	$GLOBALS['kiyose_test_enqueued_scripts']   = array();
	$GLOBALS['kiyose_test_enqueued_styles']    = array();
	$GLOBALS['kiyose_test_last_get_posts_args'] = array();
	$GLOBALS['kiyose_test_last_query_args']    = array();
	$GLOBALS['kiyose_test_last_post_navigation_args'] = array();
	$GLOBALS['kiyose_test_localized_scripts']  = array();
	$GLOBALS['kiyose_test_native_post_navigation_html'] = '';
	$GLOBALS['kiyose_test_page_templates']     = array();
	$GLOBALS['kiyose_test_permalinks']         = array();
	$GLOBALS['kiyose_test_post_meta']          = array();
	$GLOBALS['kiyose_test_post_types']         = array();
	$GLOBALS['kiyose_test_posts']              = array();
	$GLOBALS['kiyose_test_query_posts']        = array();
	$GLOBALS['kiyose_test_shortcodes']         = $default_shortcodes;
	$GLOBALS['kiyose_test_singular_post_type'] = 'post';
	$GLOBALS['kiyose_test_theme_mods']         = array();
	$GLOBALS['kiyose_test_titles']             = array();
	$GLOBALS['kiyose_test_is_404']             = false;
	$GLOBALS['kiyose_test_is_archive']         = false;
	$GLOBALS['kiyose_test_is_home']            = false;
	$GLOBALS['kiyose_test_is_page']            = false;
	$GLOBALS['kiyose_test_is_search']          = false;
	$GLOBALS['kiyose_test_is_singular']        = false;
	$GLOBALS['post']                           = null;
}

kiyose_reset_test_state();

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

if ( ! function_exists( 'add_filter' ) ) {
	/**
	 * Mock add_filter function.
	 *
	 * @param string   $hook Hook name.
	 * @param callable $callback Callback function.
	 * @param int      $priority Priority.
	 * @param int      $accepted_args Number of arguments.
	 * @return bool
	 */
	function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
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

if ( ! function_exists( 'esc_html' ) ) {
	/**
	 * Mock escaped HTML function.
	 *
	 * @param string $text Text to escape.
	 * @return string
	 */
	function esc_html( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr_e' ) ) {
	/**
	 * Mock escaped attribute translation echo function.
	 *
	 * @param string $text Text to translate.
	 * @param string $domain Text domain.
	 * @return void
	 */
	function esc_attr_e( $text, $domain = 'default' ) {
		echo htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
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
		$GLOBALS['kiyose_test_shortcodes'][ $tag ] = $callback;

		return true;
	}
}

if ( ! function_exists( 'shortcode_exists' ) ) {
	/**
	 * Mock shortcode_exists function.
	 *
	 * @param string $tag Shortcode tag.
	 * @return bool
	 */
	function shortcode_exists( $tag ) {
		return array_key_exists( $tag, $GLOBALS['kiyose_test_shortcodes'] ?? array() );
	}
}

if ( ! function_exists( 'kiyose_test_parse_shortcode_atts' ) ) {
	/**
	 * Parse simple shortcode attributes for tests.
	 *
	 * @param string $raw_atts Raw shortcode attributes.
	 * @return array<string, string>
	 */
	function kiyose_test_parse_shortcode_atts( string $raw_atts ): array {
		$atts = array();

		$match_count = preg_match_all( '/([a-zA-Z0-9_-]+)\s*=\s*(["\'])(.*?)\2/', $raw_atts, $matches, PREG_SET_ORDER );

		if ( false === $match_count || 0 === $match_count ) {
			return $atts;
		}

		foreach ( $matches as $match ) {
			$atts[ $match[1] ] = $match[3];
		}

		return $atts;
	}
}

if ( ! function_exists( 'do_shortcode' ) ) {
	/**
	 * Mock do_shortcode function for registered shortcode callbacks.
	 *
	 * @param string $content Content to parse.
	 * @return string Parsed content.
	 */
	function do_shortcode( $content ) {
		$content = (string) $content;

		return preg_replace_callback(
			'/\[([a-zA-Z0-9_-]+)([^\]]*)\](.*?)\[\/\1\]/s',
			static function ( array $matches ) {
				$tag       = $matches[1];
				$callback  = $GLOBALS['kiyose_test_shortcodes'][ $tag ] ?? null;
				$shortcode = $matches[3];

				if ( ! is_callable( $callback ) ) {
					return $matches[0];
				}

				return (string) call_user_func(
					$callback,
					kiyose_test_parse_shortcode_atts( $matches[2] ),
					$shortcode,
					$tag
				);
			},
			$content
		);
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
		$GLOBALS['kiyose_test_added_meta_boxes'][] = array(
			'id'       => $id,
			'title'    => $title,
			'callback' => $callback,
			'screen'   => $screen,
			'context'  => $context,
			'priority' => $priority,
		);

		return true;
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( $post_id, $key = '', $single = false ) {
		if (
			isset( $GLOBALS['kiyose_test_post_meta'] )
			&& isset( $GLOBALS['kiyose_test_post_meta'][ $post_id ] )
			&& '' !== $key
			&& array_key_exists( $key, $GLOBALS['kiyose_test_post_meta'][ $post_id ] )
		) {
			return $GLOBALS['kiyose_test_post_meta'][ $post_id ][ $key ];
		}

		return $single ? '' : array();
	}
}

if ( ! function_exists( 'get_posts' ) ) {
	function get_posts( $args = array() ) {
		$GLOBALS['kiyose_test_last_get_posts_args'] = $args;

		$posts = $GLOBALS['kiyose_test_posts'] ?? array();

		if ( isset( $args['post_type'] ) ) {
			$expected_post_types = (array) $args['post_type'];
			$posts               = array_filter(
				$posts,
				static function ( $post ) use ( $expected_post_types ) {
					return in_array( $post->post_type ?? 'post', $expected_post_types, true );
				}
			);
		}

		if ( isset( $args['post_status'] ) ) {
			$expected_post_statuses = (array) $args['post_status'];
			$posts                  = array_filter(
				$posts,
				static function ( $post ) use ( $expected_post_statuses ) {
					return in_array( $post->post_status ?? 'publish', $expected_post_statuses, true );
				}
			);
		}

		if ( isset( $args['fields'] ) && 'ids' === $args['fields'] ) {
			return array_map(
				static function ( $post ) {
					return (int) $post->ID;
				},
				$posts
			);
		}

		return array_values( $posts );
	}
}

if ( ! function_exists( 'get_post_type' ) ) {
	function get_post_type( $post = null ) {
		if ( is_object( $post ) && isset( $post->post_type ) ) {
			return $post->post_type;
		}

		if ( is_numeric( $post ) && isset( $GLOBALS['kiyose_test_post_types'][ (int) $post ] ) ) {
			return $GLOBALS['kiyose_test_post_types'][ (int) $post ];
		}

		$current_post = $GLOBALS['post'] ?? null;

		if ( is_object( $current_post ) && isset( $current_post->post_type ) ) {
			return $current_post->post_type;
		}

		return $GLOBALS['kiyose_test_singular_post_type'] ?? 'post';
	}
}

if ( ! function_exists( 'get_the_ID' ) ) {
	function get_the_ID() {
		$post = $GLOBALS['post'] ?? null;

		return is_object( $post ) && isset( $post->ID ) ? (int) $post->ID : 0;
	}
}

if ( ! function_exists( 'get_permalink' ) ) {
	function get_permalink( $post_id = 0 ) {
		$permalinks = $GLOBALS['kiyose_test_permalinks'] ?? array();
		$post_id    = is_object( $post_id ) && isset( $post_id->ID ) ? (int) $post_id->ID : (int) $post_id;

		return $permalinks[ $post_id ] ?? 'https://example.com/article/';
	}
}

if ( ! function_exists( 'get_the_title' ) ) {
	function get_the_title( $post_id = 0 ) {
		$titles  = $GLOBALS['kiyose_test_titles'] ?? array();
		$post_id = is_object( $post_id ) && isset( $post_id->ID ) ? (int) $post_id->ID : (int) $post_id;

		return $titles[ $post_id ] ?? 'Titre article';
	}
}

if ( ! function_exists( 'get_the_post_navigation' ) ) {
	function get_the_post_navigation( $args = array() ) {
		$GLOBALS['kiyose_test_last_post_navigation_args'] = $args;

		return $GLOBALS['kiyose_test_native_post_navigation_html'] ?? '';
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '' ) {
		return 'http://example.com' . $path;
	}
}

if ( ! function_exists( 'get_template_directory' ) ) {
	function get_template_directory() {
		return dirname( __DIR__ );
	}
}

if ( ! function_exists( 'get_template_directory_uri' ) ) {
	function get_template_directory_uri() {
		return 'https://example.com/wp-content/themes/latelierkiyose';
	}
}

if ( ! function_exists( 'get_current_screen' ) ) {
	function get_current_screen() {
		return $GLOBALS['kiyose_test_current_screen'] ?? null;
	}
}

if ( ! class_exists( 'WP_Query' ) ) {
	/**
	 * Minimal WP_Query test double.
	 */
	class WP_Query {
		/**
		 * Number of posts returned by the query.
		 *
		 * @var int
		 */
		public $post_count = 0;

		/**
		 * Test posts.
		 *
		 * @var array<int, object>
		 */
		private $posts = array();

		/**
		 * Current post index.
		 *
		 * @var int
		 */
		private $current_index = 0;

		/**
		 * Constructor.
		 *
		 * @param array<string|int, mixed> $args Query arguments, or direct test posts.
		 */
		public function __construct( $args = array() ) {
			$is_posts_list = isset( $args[0] ) && is_object( $args[0] );

			if ( $is_posts_list ) {
				$this->posts = $args;
			} else {
				$GLOBALS['kiyose_test_last_query_args'] = $args;
				$this->posts                            = $GLOBALS['kiyose_test_query_posts'] ?? array();
			}

			$this->post_count = count( $this->posts );
		}

		/**
		 * Whether the query still has posts.
		 *
		 * @return bool
		 */
		public function have_posts() {
			return $this->current_index < $this->post_count;
		}

		/**
		 * Advance the query and expose the current post globally.
		 *
		 * @return void
		 */
		public function the_post() {
			$GLOBALS['post'] = $this->posts[ $this->current_index ];
			++$this->current_index;
		}
	}
}

if ( ! function_exists( 'get_template_part' ) ) {
	function get_template_part( $slug, $name = null, $args = array() ) {
		$post = $GLOBALS['post'] ?? null;
		echo '<article class="testimony-card">' . esc_html( $post->post_title ?? 'Témoignage' ) . '</article>';
	}
}

if ( ! function_exists( 'wp_reset_postdata' ) ) {
	function wp_reset_postdata() {
		$GLOBALS['post'] = null;
	}
}

if ( ! function_exists( 'get_theme_mod' ) ) {
	function get_theme_mod( $name, $default = false ) {
		$theme_mods = $GLOBALS['kiyose_test_theme_mods'] ?? array();

		if ( array_key_exists( $name, $theme_mods ) ) {
			return $theme_mods[ $name ];
		}

		return $default;
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $text ) {
		$text = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', (string) $text );
		return strip_tags( $text );
	}
}

if ( ! function_exists( 'sanitize_title' ) ) {
	function sanitize_title( $title ) {
		$title = strtolower( remove_accents( wp_strip_all_tags( (string) $title ) ) );
		$title = preg_replace( '/[^a-z0-9]+/', '-', $title );

		return trim( $title, '-' );
	}
}

if ( ! function_exists( 'remove_accents' ) ) {
	function remove_accents( $text ) {
		return strtr(
			(string) $text,
			array(
				'à' => 'a',
				'â' => 'a',
				'ä' => 'a',
				'ç' => 'c',
				'é' => 'e',
				'è' => 'e',
				'ê' => 'e',
				'ë' => 'e',
				'î' => 'i',
				'ï' => 'i',
				'ô' => 'o',
				'ö' => 'o',
				'ù' => 'u',
				'û' => 'u',
				'ü' => 'u',
			)
		);
	}
}

if ( ! function_exists( 'has_shortcode' ) ) {
	function has_shortcode( $content, $tag ) {
		return 1 === preg_match( '/\[' . preg_quote( $tag, '/' ) . '(\s|\]|\s[^\]]*\])/', (string) $content );
	}
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		$GLOBALS['kiyose_test_enqueued_styles'][ $handle ] = array(
			'handle' => $handle,
			'src'    => $src,
			'deps'   => $deps,
			'ver'    => $ver,
			'media'  => $media,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $args = array() ) {
		$GLOBALS['kiyose_test_enqueued_scripts'][ $handle ] = array(
			'handle' => $handle,
			'src'    => $src,
			'deps'   => $deps,
			'ver'    => $ver,
			'args'   => $args,
		);

		return true;
	}
}

if ( ! function_exists( 'wp_enqueue_media' ) ) {
	function wp_enqueue_media() {
		$GLOBALS['kiyose_test_did_enqueue_media'] = true;
	}
}

if ( ! function_exists( 'wp_localize_script' ) ) {
	function wp_localize_script( $handle, $object_name, $l10n ) {
		$GLOBALS['kiyose_test_localized_scripts'][] = array(
			'handle'      => $handle,
			'object_name' => $object_name,
			'l10n'        => $l10n,
		);

		return true;
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

if ( ! function_exists( 'wpautop' ) ) {
	function wpautop( $text ) {
		$text = trim( (string) $text );

		if ( '' === $text ) {
			return '';
		}

		if ( 1 === preg_match( '/^<(p|ul|ol|div|blockquote|h[1-6])\b/i', $text ) ) {
			return $text;
		}

		$paragraphs = preg_split( "/\n\s*\n/", $text );
		$paragraphs = array_map(
			static function ( $paragraph ) {
				return '<p>' . str_replace( "\n", "<br />\n", trim( $paragraph ) ) . '</p>';
			},
			$paragraphs
		);

		return implode( "\n\n", $paragraphs );
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
		$GLOBALS['kiyose_test_post_meta'][ $post_id ][ $meta_key ] = $meta_value;

		return true;
	}
}

if ( ! function_exists( 'delete_post_meta' ) ) {
	function delete_post_meta( $post_id, $meta_key ) {
		unset( $GLOBALS['kiyose_test_post_meta'][ $post_id ][ $meta_key ] );

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
		$data = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', (string) $data );

		return strip_tags( $data, '<p><br><strong><em><a><ul><ol><li>' );
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
		if ( empty( $allowed_html ) ) {
			return strip_tags( (string) $data );
		}

		$allowed_tags = '';

		foreach ( array_keys( $allowed_html ) as $tag ) {
			$allowed_tags .= '<' . $tag . '>';
		}

		return strip_tags( (string) $data, $allowed_tags );
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
require_once __DIR__ . '/../inc/accessibility.php';
require_once __DIR__ . '/../inc/components.php';
require_once __DIR__ . '/../inc/custom-post-types.php';
require_once __DIR__ . '/../inc/shortcodes.php';
require_once __DIR__ . '/../inc/meta-boxes.php';
require_once __DIR__ . '/../inc/blog.php';

$GLOBALS['kiyose_test_default_shortcodes'] = $GLOBALS['kiyose_test_shortcodes'];
kiyose_reset_test_state();
