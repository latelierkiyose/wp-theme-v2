<?php
/**
 * Tests for reusable theme components.
 *
 * @package Kiyose
 * @since   2.1.0
 */

use PHPUnit\Framework\TestCase;

if ( ! class_exists( 'WP_Query' ) ) {
	/**
	 * Minimal WP_Query test double for component rendering tests.
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
		 * @param array<int, object> $posts Test posts.
		 */
		public function __construct( $posts = array() ) {
			$this->posts      = $posts;
			$this->post_count = count( $posts );
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

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr_e' ) ) {
	function esc_attr_e( $text, $domain = 'default' ) {
		echo esc_attr( $text );
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

if ( ! function_exists( 'get_permalink' ) ) {
	function get_permalink( $post_id = 0 ) {
		$permalinks = $GLOBALS['kiyose_test_permalinks'] ?? array();

		return $permalinks[ $post_id ] ?? 'https://example.com/article/';
	}
}

if ( ! function_exists( 'get_the_title' ) ) {
	function get_the_title( $post_id = 0 ) {
		$titles = $GLOBALS['kiyose_test_titles'] ?? array();

		return $titles[ $post_id ] ?? 'Titre article';
	}
}

require_once __DIR__ . '/../inc/accessibility.php';

if ( file_exists( __DIR__ . '/../inc/components.php' ) ) {
	require_once __DIR__ . '/../inc/components.php';
}

/**
 * Test class for reusable component helpers.
 */
class Test_Components extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['kiyose_test_theme_mods'] = array();
		$GLOBALS['kiyose_test_permalinks'] = array();
		$GLOBALS['kiyose_test_titles']     = array();
		$GLOBALS['post']                   = null;
	}

	public function test_kiyose_get_testimonials_carousel_html_ofTwoPosts_returnsAccessibleSlides() {
		// Given.
		$query = new WP_Query(
			array(
				(object) array(
					'ID'         => 11,
					'post_title' => 'Premier témoignage',
				),
				(object) array(
					'ID'         => 12,
					'post_title' => 'Deuxième témoignage',
				),
			)
		);

		// When.
		$html = $this->call_testimonials_carousel_html( $query, 'Témoignages de participantes' );

		// Then.
		$this->assertStringContainsString( 'class="carousel"', $html );
		$this->assertStringContainsString( 'role="region"', $html );
		$this->assertStringContainsString( 'aria-roledescription="carousel"', $html );
		$this->assertStringContainsString( 'aria-label="Témoignages de participantes"', $html );
		$this->assertSame( 2, substr_count( $html, 'class="carousel__slide' ) );
		$this->assertStringContainsString( 'aria-label="1 sur 2"', $html );
		$this->assertStringContainsString( 'aria-label="2 sur 2"', $html );
		$this->assertStringContainsString( 'carousel__slide carousel__slide--active"', $html );
		$this->assertStringContainsString( 'aria-hidden="false"', $html );
		$this->assertStringContainsString( 'aria-hidden="true"', $html );
	}

	public function test_kiyose_get_social_profiles_whenUrlsAreEmpty_returnsEmptyArray() {
		// Given.
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_social_facebook'  => '',
			'kiyose_social_instagram' => '',
			'kiyose_social_linkedin'  => '',
		);

		// When.
		$profiles = $this->call_social_profiles();

		// Then.
		$this->assertSame( array(), $profiles );
	}

	public function test_kiyose_get_social_links_html_ofContact_returnsSocialLinksList() {
		// Given.
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_social_facebook'  => 'https://facebook.example/kiyose',
			'kiyose_social_instagram' => '',
			'kiyose_social_linkedin'  => '',
		);

		// When.
		$html = $this->call_social_links_html( 'contact' );

		// Then.
		$this->assertStringContainsString( '<ul class="social-links">', $html );
		$this->assertStringContainsString( 'href="https://facebook.example/kiyose"', $html );
		$this->assertStringContainsString( 'aria-label="Facebook (ouverture dans un nouvel onglet)"', $html );
		$this->assertStringContainsString( '<span>Facebook</span>', $html );
		$this->assertStringContainsString( 'rel="noopener noreferrer"', $html );
	}

	public function test_kiyose_get_social_links_html_whenFooterProfilesAreEmpty_returnsEmptyString() {
		// Given.
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_social_facebook'  => '',
			'kiyose_social_instagram' => '',
			'kiyose_social_linkedin'  => '',
		);

		// When.
		$html = $this->call_social_links_html( 'footer' );

		// Then.
		$this->assertSame( '', $html );
	}

	public function test_kiyose_get_post_share_links_html_ofPost_returnsShareActions() {
		// Given.
		$post_id = 57;
		$GLOBALS['kiyose_test_permalinks'][ $post_id ] = 'https://example.com/un-article/';
		$GLOBALS['kiyose_test_titles'][ $post_id ]     = 'Un article utile';

		// When.
		$html = $this->call_post_share_links_html( $post_id );

		// Then.
		$this->assertStringContainsString( '<ul class="blog-single__share-links">', $html );
		$this->assertStringContainsString( 'blog-single__share-link blog-single__share-link--facebook', $html );
		$this->assertStringContainsString( 'blog-single__share-link blog-single__share-link--linkedin', $html );
		$this->assertStringContainsString( 'blog-single__share-link blog-single__share-link--instagram js-copy-link', $html );
		$this->assertStringContainsString( 'data-url="https://example.com/un-article/"', $html );
		$this->assertStringContainsString( 'aria-label="Copier le lien de Un article utile pour Instagram"', $html );
	}

	private function call_testimonials_carousel_html( WP_Query $query, string $aria_label ): string {
		if ( ! function_exists( 'kiyose_get_testimonials_carousel_html' ) ) {
			$this->fail( 'Expected kiyose_get_testimonials_carousel_html() to exist.' );
		}

		return kiyose_get_testimonials_carousel_html( $query, $aria_label );
	}

	private function call_social_profiles(): array {
		if ( ! function_exists( 'kiyose_get_social_profiles' ) ) {
			$this->fail( 'Expected kiyose_get_social_profiles() to exist.' );
		}

		return kiyose_get_social_profiles();
	}

	private function call_social_links_html( string $context ): string {
		if ( ! function_exists( 'kiyose_get_social_links_html' ) ) {
			$this->fail( 'Expected kiyose_get_social_links_html() to exist.' );
		}

		return kiyose_get_social_links_html( $context );
	}

	private function call_post_share_links_html( int $post_id ): string {
		if ( ! function_exists( 'kiyose_get_post_share_links_html' ) ) {
			$this->fail( 'Expected kiyose_get_post_share_links_html() to exist.' );
		}

		return kiyose_get_post_share_links_html( $post_id );
	}
}
