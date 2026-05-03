<?php
/**
 * Tests for footer legal links.
 *
 * @package Kiyose
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for footer legal links.
 */
class Test_Footer_Legal_Links extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		kiyose_reset_test_state();

		$helper_path = __DIR__ . '/../inc/footer.php';

		if ( file_exists( $helper_path ) ) {
			require_once $helper_path;
		}
	}

	public function test_kiyose_get_footer_legal_links_whenPagesAreConfigured_returnsPublishedPagePermalinks() {
		// Given.
		$this->register_page( 10, 'https://example.com/mentions/' );
		$this->register_page( 20, 'https://example.com/confidentialite/' );
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_footer_legal_notice_page_id'   => 10,
			'kiyose_footer_privacy_policy_page_id' => 20,
		);

		// When.
		$links = function_exists( 'kiyose_get_footer_legal_links' )
			? kiyose_get_footer_legal_links()
			: array();

		// Then.
		$this->assertSame(
			array(
				array(
					'label' => 'Mentions légales',
					'url'   => 'https://example.com/mentions/',
				),
				array(
					'label' => 'Politique de confidentialité',
					'url'   => 'https://example.com/confidentialite/',
				),
			),
			$links
		);
	}

	public function test_kiyose_get_footer_legal_links_whenPageIsNotConfigured_ignoresIt() {
		// Given.
		$this->register_page( 20, 'https://example.com/confidentialite/' );
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_footer_legal_notice_page_id'   => 0,
			'kiyose_footer_privacy_policy_page_id' => 20,
		);

		// When.
		$links = function_exists( 'kiyose_get_footer_legal_links' )
			? kiyose_get_footer_legal_links()
			: array();

		// Then.
		$this->assertCount( 1, $links );
		$this->assertSame( 'Politique de confidentialité', $links[0]['label'] );
	}

	public function test_kiyose_get_footer_legal_links_whenPageDoesNotExist_ignoresIt() {
		// Given.
		$this->register_page( 20, 'https://example.com/confidentialite/' );
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_footer_legal_notice_page_id'   => 99,
			'kiyose_footer_privacy_policy_page_id' => 20,
		);

		// When.
		$links = function_exists( 'kiyose_get_footer_legal_links' )
			? kiyose_get_footer_legal_links()
			: array();

		// Then.
		$this->assertCount( 1, $links );
		$this->assertSame( 'Politique de confidentialité', $links[0]['label'] );
	}

	public function test_kiyose_get_footer_legal_links_whenPageIsNotPublished_ignoresIt() {
		// Given.
		$this->register_page( 10, 'https://example.com/mentions/', 'draft' );
		$this->register_page( 20, 'https://example.com/confidentialite/' );
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_footer_legal_notice_page_id'   => 10,
			'kiyose_footer_privacy_policy_page_id' => 20,
		);

		// When.
		$links = function_exists( 'kiyose_get_footer_legal_links' )
			? kiyose_get_footer_legal_links()
			: array();

		// Then.
		$this->assertCount( 1, $links );
		$this->assertSame( 'Politique de confidentialité', $links[0]['label'] );
	}

	public function test_footer_whenBothPagesAreValid_rendersBothLegalLinks() {
		// Given.
		$this->register_page( 10, 'https://example.com/mentions/' );
		$this->register_page( 20, 'https://example.com/confidentialite/' );
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_footer_legal_notice_page_id'   => 10,
			'kiyose_footer_privacy_policy_page_id' => 20,
		);

		// When.
		$html = $this->render_footer();

		// Then.
		$this->assertStringContainsString( 'class="site-footer__legal"', $html );
		$this->assertStringContainsString( 'href="https://example.com/mentions/"', $html );
		$this->assertStringContainsString( 'href="https://example.com/confidentialite/"', $html );
	}

	public function test_footer_whenOnePageIsValid_rendersOneLegalLink() {
		// Given.
		$this->register_page( 10, 'https://example.com/mentions/' );
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_footer_legal_notice_page_id'   => 10,
			'kiyose_footer_privacy_policy_page_id' => 99,
		);

		// When.
		$html = $this->render_footer();

		// Then.
		$this->assertStringContainsString( 'class="site-footer__legal"', $html );
		$this->assertSame( 1, substr_count( $html, '<a href="https://example.com/mentions/"' ) );
		$this->assertStringNotContainsString( 'Politique de confidentialité', $html );
	}

	public function test_footer_whenNoPageIsValid_omitsLegalLinksContainer() {
		// Given.
		$GLOBALS['kiyose_test_theme_mods'] = array(
			'kiyose_footer_legal_notice_page_id'   => 99,
			'kiyose_footer_privacy_policy_page_id' => 98,
		);

		// When.
		$html = $this->render_footer();

		// Then.
		$this->assertStringNotContainsString( 'class="site-footer__legal"', $html );
		$this->assertStringNotContainsString( 'Mentions légales', $html );
		$this->assertStringNotContainsString( 'Politique de confidentialité', $html );
	}

	private function render_footer(): string {
		ob_start();
		require __DIR__ . '/../footer.php';

		return (string) ob_get_clean();
	}

	private function register_page( int $post_id, string $permalink, string $status = 'publish' ): void {
		$GLOBALS['kiyose_test_post_objects'][ $post_id ] = (object) array(
			'ID'          => $post_id,
			'post_type'   => 'page',
			'post_status' => $status,
		);
		$GLOBALS['kiyose_test_permalinks'][ $post_id ] = $permalink;
	}
}
