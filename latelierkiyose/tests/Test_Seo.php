<?php
/**
 * Tests for SEO helpers — guard against invalid BreadcrumbList nodes.
 *
 * @package Kiyose
 * @since   2.2.1
 */

use PHPUnit\Framework\TestCase;

if ( ! function_exists( 'is_page_template' ) ) {
	function is_page_template( $template = '' ) {
		return in_array( $template, $GLOBALS['kiyose_test_page_templates'] ?? array(), true );
	}
}

// kiyose_filter_landing_robots() s'appuie sur le prédicat de template déclaré ici.
require_once __DIR__ . '/../inc/enqueue.php';
require_once __DIR__ . '/../inc/seo.php';

/**
 * Test class for kiyose_remove_invalid_breadcrumbs().
 */
class Test_Seo extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		kiyose_reset_test_state();
	}

	// ----------------------------------------------------------------
	// kiyose_remove_invalid_breadcrumbs
	// ----------------------------------------------------------------

	public function test_kiyose_remove_invalid_breadcrumbs_whenBreadcrumbListEmpty_removesNode() {
		// Given — BreadcrumbList présent mais itemListElement vide (tableau vide)
		$graph = array(
			array(
				'@type'           => 'BreadcrumbList',
				'@id'             => 'https://example.com/#breadcrumb',
				'itemListElement' => array(),
			),
			array(
				'@type' => 'WebSite',
				'@id'   => 'https://example.com/#website',
			),
		);

		// When
		$result = kiyose_remove_invalid_breadcrumbs( $graph );

		// Then
		$this->assertCount( 1, $result );
		$this->assertSame( 'WebSite', $result[0]['@type'] );
	}

	public function test_kiyose_remove_invalid_breadcrumbs_whenItemListElementMissing_removesNode() {
		// Given — BreadcrumbList sans clé itemListElement
		$graph = array(
			array(
				'@type' => 'BreadcrumbList',
				'@id'   => 'https://example.com/#breadcrumb',
			),
			array(
				'@type' => 'Organization',
				'@id'   => 'https://example.com/#organization',
			),
		);

		// When
		$result = kiyose_remove_invalid_breadcrumbs( $graph );

		// Then
		$this->assertCount( 1, $result );
		$this->assertSame( 'Organization', $result[0]['@type'] );
	}

	public function test_kiyose_remove_invalid_breadcrumbs_whenWebPageReferencesMissingBreadcrumb_dropsReference() {
		// Given — cas live : WebPage référence un @id de BreadcrumbList qui n'existe pas
		// (Yoast génère la référence mais pas le nœud sur les archives EM)
		$graph = array(
			array(
				'@type'      => 'WebPage',
				'@id'        => 'https://example.com/events/categories/marche-joyeuse/',
				'breadcrumb' => array(
					'@id' => 'https://example.com/events/categories/marche-joyeuse/#breadcrumb',
				),
			),
			array(
				'@type' => 'WebSite',
				'@id'   => 'https://example.com/#website',
			),
			array(
				'@type' => 'Organization',
				'@id'   => 'https://example.com/#organization',
			),
		);

		// When
		$result = kiyose_remove_invalid_breadcrumbs( $graph );

		// Then — le graphe garde ses 3 nœuds, mais WebPage n'a plus de clé breadcrumb
		$this->assertCount( 3, $result );
		$webpage = $result[0];
		$this->assertSame( 'WebPage', $webpage['@type'] );
		$this->assertArrayNotHasKey( 'breadcrumb', $webpage );
		$this->assertSame( 'WebSite', $result[1]['@type'] );
		$this->assertSame( 'Organization', $result[2]['@type'] );
	}

	public function test_kiyose_remove_invalid_breadcrumbs_whenBreadcrumbListValid_keepsGraphUnchanged() {
		// Given — BreadcrumbList valide (2 items) + WebPage qui le référence correctement
		$breadcrumb_id = 'https://example.com/atelier/#breadcrumb';
		$graph         = array(
			array(
				'@type'           => 'BreadcrumbList',
				'@id'             => $breadcrumb_id,
				'itemListElement' => array(
					array(
						'@type'    => 'ListItem',
						'position' => 1,
						'name'     => 'Accueil',
						'item'     => 'https://example.com/',
					),
					array(
						'@type'    => 'ListItem',
						'position' => 2,
						'name'     => 'L\'Atelier',
						'item'     => 'https://example.com/atelier/',
					),
				),
			),
			array(
				'@type'      => 'WebPage',
				'@id'        => 'https://example.com/atelier/',
				'breadcrumb' => array( '@id' => $breadcrumb_id ),
			),
		);

		// When
		$result = kiyose_remove_invalid_breadcrumbs( $graph );

		// Then — graphe intact (ré-indexé mais aucun nœud supprimé)
		$this->assertCount( 2, $result );
		$this->assertSame( 'BreadcrumbList', $result[0]['@type'] );
		$this->assertCount( 2, $result[0]['itemListElement'] );
		$this->assertSame( 'WebPage', $result[1]['@type'] );
		$this->assertArrayHasKey( 'breadcrumb', $result[1] );
	}

	public function test_kiyose_remove_invalid_breadcrumbs_whenTypeIsArray_matchesBreadcrumbList() {
		// Given — @type est un tableau (ex. types multiples schema.org), itemListElement vide
		$graph = array(
			array(
				'@type'           => array( 'BreadcrumbList', 'ItemList' ),
				'@id'             => 'https://example.com/#breadcrumb',
				'itemListElement' => array(),
			),
		);

		// When
		$result = kiyose_remove_invalid_breadcrumbs( $graph );

		// Then
		$this->assertCount( 0, $result );
	}

	public function test_kiyose_remove_invalid_breadcrumbs_ofNonArrayInput_returnsInputUnchanged() {
		// Given — entrée non-tableau (robustesse)
		$non_array_inputs = array( null, 'string', 42 );

		foreach ( $non_array_inputs as $input ) {
			// When
			$result = kiyose_remove_invalid_breadcrumbs( $input );

			// Then
			$this->assertSame( $input, $result, "Failed for input: " . var_export( $input, true ) );
		}
	}

	// ----------------------------------------------------------------
	// kiyose_filter_landing_robots
	// ----------------------------------------------------------------

	public function test_kiyose_filter_landing_robots_whenLandingIsMarkedNoindex_replacesIndexWithNoindexFollow() {
		// Given
		$this->given_landing_page( true );

		// When
		$result = kiyose_filter_landing_robots( array( 'index' => true ) );

		// Then
		$this->assertArrayNotHasKey( 'index', $result );
		$this->assertTrue( $result['noindex'] );
		$this->assertTrue( $result['follow'] );
	}

	public function test_kiyose_filter_landing_robots_whenLandingIsIndexable_leavesDirectivesUnchanged() {
		// Given
		$this->given_landing_page( false );

		// When
		$result = kiyose_filter_landing_robots( array( 'index' => true ) );

		// Then
		$this->assertSame( array( 'index' => true ), $result );
	}

	public function test_kiyose_filter_landing_robots_whenTemplateIsNotLanding_leavesDirectivesUnchanged() {
		// Given — la meta est présente mais la page utilise un autre template
		$this->given_landing_page( true );
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-about.php' );

		// When
		$result = kiyose_filter_landing_robots( array( 'index' => true ) );

		// Then
		$this->assertSame( array( 'index' => true ), $result );
	}

	/**
	 * Place la requête courante sur une landing page.
	 *
	 * @param bool $is_noindex Whether the page is marked as noindex.
	 * @return void
	 */
	private function given_landing_page( bool $is_noindex ): void {
		$post     = new stdClass();
		$post->ID = 42;

		$GLOBALS['post']                       = $post;
		$GLOBALS['kiyose_test_page_templates'] = array( 'templates/page-landing.php' );
		$GLOBALS['kiyose_test_post_meta'][42]  = array(
			'kiyose_landing_noindex' => $is_noindex ? '1' : '',
		);
	}
}
