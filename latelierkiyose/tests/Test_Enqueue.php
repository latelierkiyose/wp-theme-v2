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

if ( ! function_exists( 'is_post_type_archive' ) ) {
	function is_post_type_archive( $post_types = '' ) {
		if ( empty( $GLOBALS['kiyose_test_is_post_type_archive'] ) ) {
			return false;
		}

		if ( empty( $post_types ) ) {
			return true;
		}

		$current_post_type = $GLOBALS['kiyose_test_post_type_archive_type'] ?? 'post';
		$post_types        = (array) $post_types;

		return in_array( $current_post_type, $post_types, true );
	}
}

if ( ! function_exists( 'is_home' ) ) {
	function is_home() {
		return ! empty( $GLOBALS['kiyose_test_is_home'] );
	}
}

if ( ! function_exists( 'is_tax' ) ) {
	function is_tax( $taxonomy = '' ) {
		if ( empty( $GLOBALS['kiyose_test_is_tax'] ) ) {
			return false;
		}

		if ( empty( $taxonomy ) ) {
			return true;
		}

		$current_taxonomy = $GLOBALS['kiyose_test_taxonomy'] ?? 'category';
		$taxonomies       = (array) $taxonomy;

		return in_array( $current_taxonomy, $taxonomies, true );
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

if ( ! function_exists( 'is_single' ) ) {
	function is_single( $post = '' ) {
		if ( empty( $GLOBALS['kiyose_test_is_singular'] ) ) {
			return false;
		}

		$current_post_type = $GLOBALS['kiyose_test_singular_post_type'] ?? 'post';

		if ( 'page' === $current_post_type ) {
			return false;
		}

		if ( empty( $post ) ) {
			return true;
		}

		$post_types = (array) $post;

		return in_array( $current_post_type, $post_types, true );
	}
}

/**
 * Test class for enqueue helpers.
 */
class Test_Enqueue extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		kiyose_reset_test_state();

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

	public function test_kiyose_get_theme_assets_returnsExpectedGlobalHandles() {
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

	public function test_kiyose_get_theme_assets_ofConditionalAssets_declaresConditions() {
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
			'kiyose-cta',
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

	public function test_kiyose_should_load_blog_single_styles_whenSingularEvent_returnsTrue() {
		// Given
		$GLOBALS['kiyose_test_is_singular']        = true;
		$GLOBALS['kiyose_test_singular_post_type'] = 'event';

		// When
		$result = kiyose_should_load_blog_single_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_blog_single_styles_whenSingularPage_returnsFalse() {
		// Given
		$GLOBALS['kiyose_test_is_singular']        = true;
		$GLOBALS['kiyose_test_singular_post_type'] = 'page';

		// When
		$result = kiyose_should_load_blog_single_styles();

		// Then
		$this->assertFalse( $result );
	}

	public function test_kiyose_should_load_search_styles_whenBlogIndexDisplaysSearchForm_returnsTrue() {
		// Given
		$GLOBALS['kiyose_test_is_home'] = true;

		// When
		$result = kiyose_should_load_search_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_search_styles_whenArchiveDisplaysSearchForm_returnsTrue() {
		// Given
		$GLOBALS['kiyose_test_is_archive'] = true;

		// When
		$result = kiyose_should_load_search_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_search_styles_when404DisplaysSearchForm_returnsTrue() {
		// Given
		$GLOBALS['kiyose_test_is_404'] = true;

		// When
		$result = kiyose_should_load_search_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_page_styles_whenGenericPage_returnsTrue() {
		// Given
		$GLOBALS['kiyose_test_is_page']        = true;
		$GLOBALS['kiyose_test_page_templates'] = array( 'default' );

		// When
		$result = kiyose_should_load_page_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_page_styles_whenDedicatedTemplate_returnsFalse() {
		// Given
		$GLOBALS['kiyose_test_is_page']        = true;
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-contact.php' );

		// When
		$result = kiyose_should_load_page_styles();

		// Then
		$this->assertFalse( $result );
	}

	public function test_kiyose_should_load_testimony_styles_whenTestimonialsShortcodeIsPresent_returnsTrue() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '[kiyose_testimonials]',
			'post_type'    => 'page',
		);

		// When
		$result = kiyose_should_load_testimony_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_testimony_styles_whenSingularTestimony_returnsTrue() {
		// Given
		$GLOBALS['kiyose_test_is_singular']        = true;
		$GLOBALS['kiyose_test_singular_post_type'] = 'kiyose_testimony';

		// When
		$result = kiyose_should_load_testimony_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_events_manager_assets_whenStandardPageWithoutShortcode_returnsFalse() {
		// Given
		$GLOBALS['kiyose_test_is_page']        = true;
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-services.php' );
		$GLOBALS['post']                       = (object) array(
			'post_content' => '<p>Une page de service sans événement.</p>',
			'post_type'    => 'page',
		);

		// When
		$this->assertTrue( function_exists( 'kiyose_should_load_events_manager_assets' ) );
		$result = kiyose_should_load_events_manager_assets();

		// Then
		$this->assertFalse( $result );
	}

	public function test_kiyose_should_load_events_manager_assets_whenSingularEvent_returnsTrue() {
		// Given
		$GLOBALS['kiyose_test_is_singular']        = true;
		$GLOBALS['kiyose_test_singular_post_type'] = 'event';

		// When
		$this->assertTrue( function_exists( 'kiyose_should_load_events_manager_assets' ) );
		$result = kiyose_should_load_events_manager_assets();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_events_manager_assets_whenContentHasEventShortcode_returnsTrue() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '[events_calendar]',
			'post_type'    => 'page',
		);

		// When
		$result = kiyose_should_load_events_manager_assets();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_events_manager_assets_whenContentHasKiyoseEventsListShortcode_returnsTrue() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '[kiyose_events_list]',
			'post_type'    => 'page',
		);

		// When
		$result = kiyose_should_load_events_manager_assets();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_contact_form_7_assets_whenContactTemplate_returnsTrue() {
		// Given
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-contact.php' );

		// When
		$this->assertTrue( function_exists( 'kiyose_should_load_contact_form_7_assets' ) );
		$result = kiyose_should_load_contact_form_7_assets();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_contact_form_7_assets_whenServiceTemplateWithoutShortcode_returnsFalse() {
		// Given
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-services.php' );
		$GLOBALS['post']                       = (object) array(
			'post_content' => '<p>Une page sans formulaire.</p>',
			'post_type'    => 'page',
		);

		// When
		$this->assertTrue( function_exists( 'kiyose_should_load_contact_form_7_assets' ) );
		$result = kiyose_should_load_contact_form_7_assets();

		// Then
		$this->assertFalse( $result );
	}

	public function test_kiyose_should_load_contact_form_7_assets_whenContentHasShortcode_returnsTrue() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '[contact-form-7 id="123" title="Contact"]',
			'post_type'    => 'page',
		);

		// When
		$this->assertTrue( function_exists( 'kiyose_should_load_contact_form_7_assets' ) );
		$result = kiyose_should_load_contact_form_7_assets();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_brevo_assets_whenHomeTemplateWithoutShortcode_returnsFalse() {
		// Given
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-home.php' );

		// When
		$this->assertTrue( function_exists( 'kiyose_should_load_brevo_assets' ) );
		$result = kiyose_should_load_brevo_assets();

		// Then
		$this->assertFalse( $result );
	}

	public function test_kiyose_should_load_brevo_assets_whenBrevoShortcodeExists_returnsTrue() {
		// Given
		add_shortcode(
			'sibwp_form',
			static function () {
				return '';
			}
		);

		// When
		$result = kiyose_should_load_brevo_assets();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_recaptcha_assets_whenBrevoRecaptchaIsDisabled_returnsFalse() {
		// Given
		add_shortcode(
			'sibwp_form',
			static function () {
				return '';
			}
		);
		$GLOBALS['kiyose_test_filters']['kiyose_brevo_recaptcha_enabled'] = false;

		// When
		$result = kiyose_should_load_recaptcha_assets();

		// Then
		$this->assertFalse( $result );
	}

	public function test_kiyose_should_load_events_manager_recaptcha_assets_whenEventsRecaptchaIsDisabled_returnsFalse() {
		// Given
		$GLOBALS['kiyose_test_is_singular']        = true;
		$GLOBALS['kiyose_test_singular_post_type'] = 'event';
		$GLOBALS['kiyose_test_filters']['kiyose_events_manager_recaptcha_enabled'] = false;

		// When
		$result = kiyose_should_load_events_manager_recaptcha_assets();

		// Then
		$this->assertFalse( $result );
	}

	public function test_kiyose_should_load_carousel_styles_whenTestimonialsShortcodeRequestsCarousel_returnsTrue() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '[kiyose_testimonials display="carousel"]',
			'post_type'    => 'page',
		);

		// When
		$result = kiyose_should_load_carousel_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_carousel_styles_whenTestimonialsShortcodeUsesSingleQuotes_returnsTrue() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => "[kiyose_testimonials display='carousel']",
			'post_type'    => 'page',
		);

		// When
		$result = kiyose_should_load_carousel_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_signets_styles_whenSignetsShortcodeIsPresent_returnsTrue() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '[kiyose_signets][/kiyose_signets]',
			'post_type'    => 'page',
		);

		// When
		$result = kiyose_should_load_signets_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_callout_styles_whenCalloutShortcodeIsPresent_returnsTrue() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '[kiyose_callout]Important[/kiyose_callout]',
			'post_type'    => 'page',
		);

		// When
		$result = kiyose_should_load_callout_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_cta_styles_whenCtaShortcodeIsPresent_returnsTrue() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '[kiyose_cta texte="Réserver votre appel découverte" lien="/contact/"]',
			'post_type'    => 'page',
		);

		// When
		$this->assertTrue( function_exists( 'kiyose_should_load_cta_styles' ), 'Missing kiyose_should_load_cta_styles().' );
		$result = kiyose_should_load_cta_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_should_load_cta_styles_whenHomeTemplate_returnsTrue() {
		// Given
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-home.php' );

		// When
		$this->assertTrue( function_exists( 'kiyose_should_load_cta_styles' ), 'Missing kiyose_should_load_cta_styles().' );
		$result = kiyose_should_load_cta_styles();

		// Then
		$this->assertTrue( $result );
	}

	public function test_kiyose_dequeue_unused_plugin_assets_whenStandardPageWithoutPluginShortcodes_dequeuesEventsAndCf7Handles() {
		// Given
		$GLOBALS['kiyose_test_is_page']        = true;
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-services.php' );
		$GLOBALS['post']                       = (object) array(
			'post_content' => '<p>Une page sans formulaire ni événement.</p>',
			'post_type'    => 'page',
		);

		// When
		$this->assertTrue( function_exists( 'kiyose_dequeue_unused_plugin_assets' ) );
		kiyose_dequeue_unused_plugin_assets();

		// Then
		$this->assertContains( 'events-manager', $GLOBALS['kiyose_test_dequeued_scripts'] );
		$this->assertContains( 'events-manager', $GLOBALS['kiyose_test_dequeued_styles'] );
		$this->assertContains( 'contact-form-7', $GLOBALS['kiyose_test_dequeued_scripts'] );
		$this->assertContains( 'contact-form-7', $GLOBALS['kiyose_test_dequeued_styles'] );
	}

	public function test_kiyose_dequeue_unused_plugin_assets_whenContactTemplate_keepsContactForm7AndDequeuesEventsManager() {
		// Given
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-contact.php' );

		// When
		$this->assertTrue( function_exists( 'kiyose_dequeue_unused_plugin_assets' ) );
		kiyose_dequeue_unused_plugin_assets();

		// Then
		$this->assertNotContains( 'contact-form-7', $GLOBALS['kiyose_test_dequeued_scripts'] );
		$this->assertNotContains( 'contact-form-7', $GLOBALS['kiyose_test_dequeued_styles'] );
		$this->assertContains( 'events-manager', $GLOBALS['kiyose_test_dequeued_scripts'] );
		$this->assertContains( 'events-manager', $GLOBALS['kiyose_test_dequeued_styles'] );
	}

	public function test_kiyose_add_recaptcha_resource_hints_whenNoPublicFormIsActive_returnsHintsUnchanged() {
		// Given
		$hints = array( 'https://example.com' );

		// When
		$this->assertTrue( function_exists( 'kiyose_add_recaptcha_resource_hints' ) );
		$result = kiyose_add_recaptcha_resource_hints( $hints, 'preconnect' );

		// Then
		$this->assertSame( $hints, $result );
	}

	public function test_kiyose_add_recaptcha_resource_hints_whenOnlyHomeEventsListIsActive_returnsHintsUnchanged() {
		// Given
		$hints                                   = array( 'https://example.com' );
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-home.php' );

		// When
		$this->assertTrue( function_exists( 'kiyose_add_recaptcha_resource_hints' ) );
		$result = kiyose_add_recaptcha_resource_hints( $hints, 'preconnect' );

		// Then
		$this->assertSame( $hints, $result );
	}

	public function test_kiyose_add_recaptcha_resource_hints_whenContactFormIsActive_addsGoogleOrigins() {
		// Given
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-contact.php' );

		// When
		$this->assertTrue( function_exists( 'kiyose_add_recaptcha_resource_hints' ) );
		$result = kiyose_add_recaptcha_resource_hints( array(), 'preconnect' );

		// Then
		$this->assertContains( 'https://www.google.com', $result );
		$this->assertContains(
			array(
				'href'        => 'https://www.gstatic.com',
				'crossorigin' => 'anonymous',
			),
			$result
		);
	}

	public function test_kiyose_dedupe_external_scripts_whenDuplicateRecaptchaUrls_dequeuesSecondHandle() {
		// Given
		$GLOBALS['wp_scripts'] = (object) array(
			'queue'      => array( 'contact-form-7-recaptcha', 'brevo-recaptcha', 'kiyose-main' ),
			'registered' => array(
				'contact-form-7-recaptcha' => (object) array(
					'src' => 'https://www.google.com/recaptcha/api.js?render=site-key',
				),
				'brevo-recaptcha'          => (object) array(
					'src' => 'https://www.google.com/recaptcha/api.js?render=site-key',
				),
				'kiyose-main'              => (object) array(
					'src' => 'https://example.com/wp-content/themes/latelierkiyose/assets/js/main.js',
				),
			),
		);

		// When
		$this->assertTrue( function_exists( 'kiyose_dedupe_external_scripts' ) );
		kiyose_dedupe_external_scripts();

		// Then
		$this->assertContains( 'brevo-recaptcha', $GLOBALS['kiyose_test_dequeued_scripts'] );
		$this->assertNotContains( 'contact-form-7-recaptcha', $GLOBALS['kiyose_test_dequeued_scripts'] );
		$this->assertSame( array( 'contact-form-7-recaptcha', 'kiyose-main' ), $GLOBALS['wp_scripts']->queue );
	}

	public function test_kiyose_dedupe_external_scripts_whenDuplicateBrevoSdkUrls_dequeuesSecondHandle() {
		// Given
		$GLOBALS['wp_scripts'] = (object) array(
			'queue'      => array( 'sib-front-js', 'sibwp-form-js' ),
			'registered' => array(
				'sib-front-js'  => (object) array(
					'src' => 'https://sibforms.com/forms/end-form/build/main.js',
				),
				'sibwp-form-js' => (object) array(
					'src' => 'https://sibforms.com/forms/end-form/build/main.js',
				),
			),
		);

		// When
		$this->assertTrue( function_exists( 'kiyose_dedupe_external_scripts' ) );
		kiyose_dedupe_external_scripts();

		// Then
		$this->assertContains( 'sibwp-form-js', $GLOBALS['kiyose_test_dequeued_scripts'] );
		$this->assertNotContains( 'sib-front-js', $GLOBALS['kiyose_test_dequeued_scripts'] );
		$this->assertSame( array( 'sib-front-js' ), $GLOBALS['wp_scripts']->queue );
	}

	public function test_kiyose_get_asset_version_whenFileIsMissing_returnsThemeVersion() {
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

	public function test_kiyose_load_main_as_module_whenHandleIsMain_addsModuleType() {
		// Given
		$tag = '<script type="text/javascript" src="https://example.com/main.js"></script>';

		// When
		$result = kiyose_load_main_as_module( $tag, 'kiyose-main' );

		// Then
		$this->assertSame( '<script type="module" src="https://example.com/main.js"></script>', $result );
	}

	public function test_kiyose_load_main_as_module_whenHandleDiffers_returnsOriginalTag() {
		// Given
		$tag = '<script type="text/javascript" src="https://example.com/other.js"></script>';

		// When
		$result = kiyose_load_main_as_module( $tag, 'kiyose-carousel' );

		// Then
		$this->assertSame( $tag, $result );
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
