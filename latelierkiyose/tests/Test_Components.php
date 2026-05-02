<?php
/**
 * Tests for reusable theme components.
 *
 * @package Kiyose
 * @since   2.1.0
 */

use PHPUnit\Framework\TestCase;

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

		kiyose_reset_test_state();
	}

	public function test_kiyose_get_testimonials_carousel_html_ofQuery_returnsAccessibleCarousel() {
		// Given
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

		// When
		$html = $this->call_testimonials_carousel_html( $query, 'Témoignages de participantes' );

		// Then
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

	public function test_kiyose_get_social_profiles_whenThemeModsAreEmpty_returnsDefaults() {
		// Given
		$GLOBALS['kiyose_test_theme_mods'] = array();

		// When
		$profiles = $this->call_social_profiles();

		// Then
		$this->assertCount( 3, $profiles );
		$this->assertSame( 'facebook', $profiles[0]['key'] );
		$this->assertSame( 'https://www.facebook.com/latelierkiyose', $profiles[0]['url'] );
		$this->assertSame( 'instagram', $profiles[1]['key'] );
		$this->assertSame( 'https://www.instagram.com/latelierkiyose', $profiles[1]['url'] );
		$this->assertSame( 'linkedin', $profiles[2]['key'] );
		$this->assertSame( 'https://www.linkedin.com/company/latelierkiyose', $profiles[2]['url'] );
	}

	public function test_kiyose_get_social_profiles_whenUrlsAreEmpty_returnsEmptyArray() {
		// Given
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_social_facebook'  => '',
			'kiyose_social_instagram' => '',
			'kiyose_social_linkedin'  => '',
		);

		// When
		$profiles = $this->call_social_profiles();

		// Then
		$this->assertSame( array(), $profiles );
	}

	public function test_kiyose_get_social_links_html_ofContactContext_returnsSocialLinksList() {
		// Given
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_social_facebook'  => 'https://facebook.example/kiyose',
			'kiyose_social_instagram' => '',
			'kiyose_social_linkedin'  => '',
		);

		// When
		$html = $this->call_social_links_html( 'contact' );

		// Then
		$this->assertStringContainsString( '<ul class="social-links">', $html );
		$this->assertStringContainsString( 'href="https://facebook.example/kiyose"', $html );
		$this->assertStringContainsString( 'aria-label="Facebook (ouverture dans un nouvel onglet)"', $html );
		$this->assertStringContainsString( '<span>Facebook</span>', $html );
		$this->assertStringContainsString( 'rel="noopener noreferrer"', $html );
	}

	public function test_kiyose_get_social_links_html_whenFooterProfilesAreEmpty_returnsEmptyString() {
		// Given
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_social_facebook'  => '',
			'kiyose_social_instagram' => '',
			'kiyose_social_linkedin'  => '',
		);

		// When
		$html = $this->call_social_links_html( 'footer' );

		// Then
		$this->assertSame( '', $html );
	}

	public function test_kiyose_get_post_share_links_html_ofPostId_returnsShareButtons() {
		// Given
		$post_id = 57;
		$GLOBALS['kiyose_test_permalinks'][ $post_id ] = 'https://example.com/un-article/';
		$GLOBALS['kiyose_test_titles'][ $post_id ]     = 'Un article utile';

		// When
		$html = $this->call_post_share_links_html( $post_id );

		// Then
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
