<?php
/**
 * Tests for theme asset registration.
 *
 * @package Kiyose
 * @since   2.1.0
 */

use PHPUnit\Framework\TestCase;

if ( ! defined( 'KIYOSE_VERSION' ) ) {
	define( 'KIYOSE_VERSION', 'test-version' );
}

if ( ! class_exists( 'WP_Post' ) ) {
	/**
	 * Minimal post object for enqueue condition tests.
	 */
	class WP_Post {
		/**
		 * Post content.
		 *
		 * @var string
		 */
		public $post_content = '';

		/**
		 * Post type.
		 *
		 * @var string
		 */
		public $post_type = 'post';
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

if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		$GLOBALS['kiyose_test_enqueued_styles'][ $handle ] = array(
			'src'    => $src,
			'deps'   => $deps,
			'ver'    => $ver,
			'media'  => $media,
		);
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $args = array() ) {
		$GLOBALS['kiyose_test_enqueued_scripts'][ $handle ] = array(
			'src'  => $src,
			'deps' => $deps,
			'ver'  => $ver,
			'args' => $args,
		);
	}
}

if ( ! function_exists( 'is_page' ) ) {
	function is_page() {
		return ! empty( $GLOBALS['kiyose_test_is_page'] );
	}
}

if ( ! function_exists( 'is_page_template' ) ) {
	function is_page_template( $template = '' ) {
		return in_array( $template, $GLOBALS['kiyose_test_page_templates'] ?? array(), true );
	}
}

if ( ! function_exists( 'is_search' ) ) {
	function is_search() {
		return ! empty( $GLOBALS['kiyose_test_is_search'] );
	}
}

if ( ! function_exists( 'is_404' ) ) {
	function is_404() {
		return ! empty( $GLOBALS['kiyose_test_is_404'] );
	}
}

if ( ! function_exists( 'is_archive' ) ) {
	function is_archive() {
		return ! empty( $GLOBALS['kiyose_test_is_archive'] );
	}
}

if ( ! function_exists( 'is_home' ) ) {
	function is_home() {
		return ! empty( $GLOBALS['kiyose_test_is_home'] );
	}
}

if ( ! function_exists( 'is_singular' ) ) {
	function is_singular( $post_types = '' ) {
		if ( empty( $GLOBALS['kiyose_test_is_singular'] ) ) {
			return false;
		}

		if ( empty( $post_types ) ) {
			return true;
		}

		$current_post_type = $GLOBALS['kiyose_test_singular_post_type'] ?? 'post';
		$post_types        = (array) $post_types;

		return in_array( $current_post_type, $post_types, true );
	}
}

if ( ! function_exists( 'has_shortcode' ) ) {
	function has_shortcode( $content, $tag ) {
		return 1 === preg_match( '/\[' . preg_quote( $tag, '/' ) . '(\s|\]|\s[^\]]*\])/', (string) $content );
	}
}

if ( ! function_exists( 'shortcode_exists' ) ) {
	function shortcode_exists( $tag ) {
		return in_array( $tag, $GLOBALS['kiyose_test_existing_shortcodes'] ?? array(), true );
	}
}

/**
 * Test class for enqueue helpers.
 */
class Test_Enqueue extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['kiyose_test_enqueued_styles']    = array();
		$GLOBALS['kiyose_test_enqueued_scripts']   = array();
		$GLOBALS['kiyose_test_existing_shortcodes'] = array();
		$GLOBALS['kiyose_test_is_404']             = false;
		$GLOBALS['kiyose_test_is_archive']         = false;
		$GLOBALS['kiyose_test_is_home']            = false;
		$GLOBALS['kiyose_test_is_page']            = false;
		$GLOBALS['kiyose_test_is_search']          = false;
		$GLOBALS['kiyose_test_is_singular']        = false;
		$GLOBALS['kiyose_test_page_templates']     = array();
		$GLOBALS['post']                           = null;

		$this->load_enqueue_file();
	}

	public function test_kiyose_get_asset_suffix_whenWpDebugIsUndefined_returnsMinSuffix() {
		// Given
		$this->assertFalse( defined( 'WP_DEBUG' ) );

		// When
		$result = kiyose_get_asset_suffix();

		// Then
		$this->assertSame( '.min', $result );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_kiyose_get_asset_suffix_whenWpDebugIsTrue_returnsEmptySuffix() {
		// Given
		define( 'WP_DEBUG', true );
		$this->load_enqueue_file();

		// When
		$result = kiyose_get_asset_suffix();

		// Then
		$this->assertSame( '', $result );
	}

	public function test_kiyose_get_theme_assets_containsRequiredGlobalHandles() {
		// Given
		$required_handles = array(
			'kiyose-fonts',
			'kiyose-variables',
			'kiyose-header',
			'kiyose-navigation',
			'kiyose-footer',
			'kiyose-plugins-common',
			'kiyose-main',
			'kiyose-gutenberg-blocks',
			'kiyose-kintsugi',
			'kiyose-animations',
		);

		// When
		$global_handles = array_column(
			array_filter(
				$this->get_theme_assets(),
				static function ( $asset ) {
					return ! isset( $asset['condition'] );
				}
			),
			'handle'
		);

		// Then
		foreach ( $required_handles as $handle ) {
			$this->assertContains( $handle, $global_handles );
		}
	}

	public function test_kiyose_get_theme_assets_whenAssetIsConditional_exposesCondition() {
		// Given
		$conditional_handles = array(
			'kiyose-page',
			'kiyose-search',
			'kiyose-404',
			'kiyose-blog-card',
			'kiyose-blog-archive',
			'kiyose-blog-single',
			'kiyose-hero',
			'kiyose-home-sections',
			'kiyose-service-page',
			'kiyose-about-page',
			'kiyose-contact-page',
			'kiyose-cf7-override',
			'kiyose-events-manager',
			'kiyose-testimonials-grid',
			'kiyose-masonry',
			'kiyose-carousel',
			'kiyose-signets',
			'kiyose-callout-box',
			'kiyose-home-animations',
			'kiyose-welcome-block',
			'kiyose-home-content',
			'kiyose-about-overlay',
			'kiyose-newsletter-overlay',
		);

		// When
		$assets_by_handle = $this->get_theme_assets_by_handle();

		// Then
		foreach ( $conditional_handles as $handle ) {
			$this->assertArrayHasKey( $handle, $assets_by_handle );
			$this->assertArrayHasKey( 'condition', $assets_by_handle[ $handle ] );
			$this->assertIsCallable( $assets_by_handle[ $handle ]['condition'] );
		}
	}

	public function test_kiyose_get_asset_version_whenFileIsAbsent_returnsThemeVersion() {
		// Given
		$missing_asset_path = '/assets/css/components/not-found.css';

		// When
		$result = kiyose_get_asset_version( $missing_asset_path );

		// Then
		$this->assertSame( KIYOSE_VERSION, $result );
	}

	public function test_kiyose_asset_condition_matches_whenConditionIsAbsent_returnsTrue() {
		// Given
		$asset = array(
			'type'   => 'style',
			'handle' => 'kiyose-test',
			'path'   => '/assets/css/test.css',
			'deps'   => array(),
		);

		// When
		$result = $this->call_asset_condition_matches( $asset );

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_asset_condition_matches_whenConditionIsFalse_returnsFalse() {
		// Given
		$asset = array(
			'type'      => 'style',
			'handle'    => 'kiyose-test',
			'path'      => '/assets/css/test.css',
			'deps'      => array(),
			'condition' => static function () {
				return false;
			},
		);

		// When
		$result = $this->call_asset_condition_matches( $asset );

		// Then
		$this->assertFalse( $result );
	}

	public function test_kiyose_enqueue_registered_asset_ofStyle_enqueuesSuffixedAsset() {
		// Given
		$asset = array(
			'type'   => 'style',
			'handle' => 'kiyose-test-style',
			'path'   => '/assets/css/components/page.css',
			'deps'   => array( 'kiyose-variables' ),
		);

		// When
		$this->call_enqueue_registered_asset( $asset, '.min' );

		// Then
		$this->assertSame(
			'https://example.com/wp-content/themes/latelierkiyose/assets/css/components/page.min.css',
			$GLOBALS['kiyose_test_enqueued_styles']['kiyose-test-style']['src']
		);
		$this->assertSame(
			array( 'kiyose-variables' ),
			$GLOBALS['kiyose_test_enqueued_styles']['kiyose-test-style']['deps']
		);
	}

	public function test_kiyose_enqueue_registered_asset_ofScript_enqueuesInFooter() {
		// Given
		$asset = array(
			'type'      => 'script',
			'handle'    => 'kiyose-test-script',
			'path'      => '/assets/js/modules/masonry.js',
			'deps'      => array(),
			'in_footer' => true,
		);

		// When
		$this->call_enqueue_registered_asset( $asset, '.min' );

		// Then
		$this->assertSame(
			'https://example.com/wp-content/themes/latelierkiyose/assets/js/modules/masonry.min.js',
			$GLOBALS['kiyose_test_enqueued_scripts']['kiyose-test-script']['src']
		);
		$this->assertTrue( $GLOBALS['kiyose_test_enqueued_scripts']['kiyose-test-script']['args'] );
	}

	public function test_kiyose_preload_fonts_outputsExistingCriticalFonts() {
		// Given
		ob_start();

		// When
		kiyose_preload_fonts();
		$output = ob_get_clean();

		// Then
		$this->assertStringContainsString( 'dancing-script-v29-latin-regular.woff2', $output );
		$this->assertStringContainsString( 'nunito-v32-latin-regular.woff2', $output );
		$this->assertStringNotContainsString( 'caveat-v21-latin-regular.woff2', $output );
	}

	private function load_enqueue_file(): void {
		require_once __DIR__ . '/../inc/enqueue.php';
	}

	private function get_theme_assets(): array {
		if ( ! function_exists( 'kiyose_get_theme_assets' ) ) {
			$this->fail( 'Expected kiyose_get_theme_assets() to exist.' );
		}

		return kiyose_get_theme_assets();
	}

	private function get_theme_assets_by_handle(): array {
		$assets_by_handle = array();

		foreach ( $this->get_theme_assets() as $asset ) {
			$assets_by_handle[ $asset['handle'] ] = $asset;
		}

		return $assets_by_handle;
	}

	private function call_asset_condition_matches( array $asset ): bool {
		if ( ! function_exists( 'kiyose_asset_condition_matches' ) ) {
			$this->fail( 'Expected kiyose_asset_condition_matches() to exist.' );
		}

		return kiyose_asset_condition_matches( $asset );
	}

	private function call_enqueue_registered_asset( array $asset, string $suffix ): void {
		if ( ! function_exists( 'kiyose_enqueue_registered_asset' ) ) {
			$this->fail( 'Expected kiyose_enqueue_registered_asset() to exist.' );
		}

		kiyose_enqueue_registered_asset( $asset, $suffix );
	}
}
