<?php
/**
 * Tests for service page CTA helpers.
 *
 * @package Kiyose
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for service CTA configuration.
 */
class Test_Service_CTAs extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		kiyose_reset_test_state();
	}

	public function test_kiyose_get_service_cta_links_whenPagesAreConfigured_returnsPublishedPagePermalinks() {
		// Given.
		$this->register_page( 10, 'https://example.com/contact/' );
		$this->register_page( 20, 'https://example.com/calendrier/' );
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_service_contact_page_id'  => 10,
			'kiyose_service_calendar_page_id' => 20,
		);

		// When.
		$links = function_exists( 'kiyose_get_service_cta_links' )
			? kiyose_get_service_cta_links( 43 )
			: array();

		// Then.
		$this->assertCount( 2, $links );
		$this->assertSame( 'Me contacter', $links[0]['label'] );
		$this->assertSame( 'https://example.com/contact/', $links[0]['url'] );
		$this->assertSame( 'Voir le calendrier', $links[1]['label'] );
		$this->assertSame( 'https://example.com/calendrier/', $links[1]['url'] );
	}

	public function test_kiyose_get_service_cta_links_whenCalendarPageIsMissing_omitsCalendarButton() {
		// Given.
		$this->register_page( 10, 'https://example.com/contact/' );
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_service_contact_page_id'  => 10,
			'kiyose_service_calendar_page_id' => 99,
		);

		// When.
		$links = function_exists( 'kiyose_get_service_cta_links' )
			? kiyose_get_service_cta_links( 43 )
			: array();

		// Then.
		$this->assertCount( 1, $links );
		$this->assertSame( 'Me contacter', $links[0]['label'] );
	}

	public function test_kiyose_get_service_cta_links_whenServiceHasCategories_addsCalendarQueryParameter() {
		// Given.
		$this->register_page( 10, 'https://example.com/contact/' );
		$this->register_page( 20, 'https://example.com/calendrier/' );
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_service_contact_page_id'  => 10,
			'kiyose_service_calendar_page_id' => 20,
		);
		$this->register_event_categories(
			array(
				'art-therapie',
				'ados',
			)
		);
		$GLOBALS['kiyose_test_post_meta'][43]['kiyose_service_event_categories'] = array( 'art-therapie', 'ados' );

		// When.
		$links = function_exists( 'kiyose_get_service_cta_links' )
			? kiyose_get_service_cta_links( 43 )
			: array();

		// Then.
		$this->assertCount( 2, $links );
		$this->assertSame( 'https://example.com/calendrier/?kiyose_event_categories=art-therapie%2Cados', $links[1]['url'] );
	}

	public function test_kiyose_sanitize_event_category_slugs_whenValuesAreUnknownOrUnsafe_ignoresThem() {
		// Given.
		$this->register_event_categories(
			array(
				'art-therapie',
				'ados',
			)
		);

		// When.
		$slugs = function_exists( 'kiyose_sanitize_event_category_slugs' )
			? kiyose_sanitize_event_category_slugs( array( 'art-therapie', '<script>alert(1)</script>', 'inconnue', 'ados' ) )
			: array();

		// Then.
		$this->assertSame( array( 'art-therapie', 'ados' ), $slugs );
	}

	private function register_page( int $post_id, string $permalink, string $status = 'publish' ): void {
		$GLOBALS['kiyose_test_post_objects'][ $post_id ] = (object) array(
			'ID'          => $post_id,
			'post_type'   => 'page',
			'post_status' => $status,
		);
		$GLOBALS['kiyose_test_permalinks'][ $post_id ] = $permalink;
	}

	/**
	 * @param array<int, string> $slugs Event category slugs.
	 */
	private function register_event_categories( array $slugs ): void {
		$GLOBALS['kiyose_test_terms']['event-categories'] = array_map(
			static function ( string $slug ) {
				return (object) array(
					'name' => ucwords( str_replace( '-', ' ', $slug ) ),
					'slug' => $slug,
				);
			},
			$slugs
		);
	}
}
