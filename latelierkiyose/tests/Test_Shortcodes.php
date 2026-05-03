<?php
/**
 * Tests for Shortcodes.
 *
 * @package Kiyose
 * @since   0.1.7
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for shortcode helper functions.
 */
class Test_Shortcodes extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		kiyose_reset_test_state();
	}

	public function test_kiyose_callout_shortcode_whenContentIsEmpty_returnsEmptyString() {
		// Given
		$content = '';

		// When
		$html = kiyose_callout_shortcode( array(), $content );

		// Then
		$this->assertSame( '', $html );
	}

	public function test_kiyose_callout_shortcode_ofTitleAndContent_returnsAccessibleNote() {
		// Given
		$atts    = array(
			'titre' => 'Bon à savoir <script>alert("xss")</script>',
		);
		$content = 'Un contenu important.';

		// When
		$html = kiyose_callout_shortcode( $atts, $content );

		// Then
		$this->assertStringContainsString( '<div class="callout-box" role="note">', $html );
		$this->assertStringContainsString( '<p class="callout-box__title"><strong>Bon à savoir</strong></p>', $html );
		$this->assertStringContainsString( '<div class="callout-box__content"><p>Un contenu important.</p>', $html );
		$this->assertStringNotContainsString( '<script>', $html );
	}

	public function test_kiyose_cta_shortcode_ofTextAndLink_returnsCenteredCta() {
		// Given
		$atts = array(
			'texte' => 'Réserver votre appel découverte',
			'lien'  => '/contact/',
		);

		// When
		$html = $this->render_cta_shortcode( $atts );

		// Then
		$this->assertStringContainsString( '<div class="kiyose-cta-wrapper">', $html );
		$this->assertStringContainsString( '<a href="/contact/" class="kiyose-cta">', $html );
		$this->assertStringContainsString( 'Réserver votre appel découverte', $html );
	}

	public function test_kiyose_cta_shortcode_whenTextContainsMarkup_escapesOutput() {
		// Given
		$atts = array(
			'texte' => 'Réserver <script>alert("xss")</script>',
			'lien'  => '/contact/',
		);

		// When
		$html = $this->render_cta_shortcode( $atts );

		// Then
		$this->assertStringContainsString( '>Réserver</a>', $html );
		$this->assertStringNotContainsString( '<script>', $html );
		$this->assertStringNotContainsString( 'alert', $html );
	}

	public function test_kiyose_cta_shortcode_whenLinkIsEmpty_returnsEmptyString() {
		// Given
		$atts = array(
			'texte' => 'Réserver votre appel découverte',
			'lien'  => '',
		);

		// When
		$html = $this->render_cta_shortcode( $atts );

		// Then
		$this->assertSame( '', $html );
	}

	public function test_kiyose_cta_shortcode_whenTextIsEmpty_returnsEmptyString() {
		// Given
		$atts = array(
			'texte' => '',
			'lien'  => '/contact/',
		);

		// When
		$html = $this->render_cta_shortcode( $atts );

		// Then
		$this->assertSame( '', $html );
	}

	public function test_kiyose_signets_shortcode_ofManualLinks_returnsNavigation() {
		// Given
		$content = "Introduction | #introduction\nPratiques & soins | #pratiques";

		// When
		$html = kiyose_signets_shortcode( array(), $content );

		// Then
		$this->assertStringContainsString( '<nav class="kiyose-signets" aria-label="Navigation dans la page">', $html );
		$this->assertStringContainsString( '<a href="#introduction" class="kiyose-signets__link">', $html );
		$this->assertStringContainsString( 'Introduction', $html );
		$this->assertStringContainsString( '<a href="#pratiques" class="kiyose-signets__link">', $html );
		$this->assertStringContainsString( 'Pratiques &amp; soins', $html );
	}

	public function test_kiyose_signets_shortcode_whenPostHasPlainH2_generatesNavigation() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '<p>Intro</p><h2>Art-thérapie créative</h2><p>Texte</p>',
		);

		// When
		$html = kiyose_signets_shortcode( array(), '' );

		// Then
		$this->assertStringContainsString( '<a href="#art-therapie-creative" class="kiyose-signets__link">', $html );
		$this->assertStringContainsString( 'Art-thérapie créative', $html );
	}

	public function test_kiyose_signets_shortcode_whenPostHeadingHasId_usesExistingAnchor() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '<h2 id="ancre-existante">Pratiques & soins</h2>',
		);

		// When
		$html = kiyose_signets_shortcode( array(), '' );

		// Then
		$this->assertStringContainsString( '<a href="#ancre-existante" class="kiyose-signets__link">', $html );
		$this->assertStringContainsString( 'Pratiques &amp; soins', $html );
	}

	public function test_kiyose_signets_shortcode_whenNoLinksFound_returnsEmptyString() {
		// Given
		$GLOBALS['post'] = (object) array(
			'post_content' => '<p>Un contenu sans titre de section.</p>',
		);

		// When
		$html = kiyose_signets_shortcode( array(), '' );

		// Then
		$this->assertSame( '', $html );
	}

	public function test_kiyose_events_list_shortcode_whenNoCategoryQuery_returnsEventsListWithoutCategory() {
		// Given.
		$_GET = array();

		// When.
		$html = function_exists( 'kiyose_events_list_shortcode' )
			? kiyose_events_list_shortcode()
			: '';

		// Then.
		$this->assertSame( '[events_list limit="20" scope="future" orderby="event_start_date" order="ASC"]', $html );
	}

	public function test_kiyose_events_list_shortcode_whenCategoryQueryIsValid_addsCategoryAttribute() {
		// Given.
		$GLOBALS['kiyose_test_terms']['event-categories'] = array(
			(object) array( 'slug' => 'art-therapie', 'name' => 'Art-thérapie' ),
			(object) array( 'slug' => 'ados', 'name' => 'Ados' ),
		);
		$_GET = array(
			'kiyose_event_categories' => 'art-therapie,<script>alert(1)</script>,ados',
		);

		// When.
		$html = function_exists( 'kiyose_events_list_shortcode' )
			? kiyose_events_list_shortcode()
			: '';

		// Then.
		$this->assertSame( '[events_list limit="20" scope="future" orderby="event_start_date" order="ASC" category="art-therapie,ados"]', $html );
	}

	public function test_kiyose_testimonials_shortcode_whenDisplayIsInvalid_fallsBackToGrid() {
		// Given
		$GLOBALS['kiyose_test_query_posts'] = array(
			(object) array(
				'ID'         => 27,
				'post_title' => 'Un témoignage',
			),
		);

		// When
		$html = kiyose_testimonials_shortcode(
			array(
				'display' => 'cards',
				'limit'   => 1,
			)
		);

		// Then
		$this->assertStringContainsString( 'class="testimonials-grid testimonials-grid--columns-2"', $html );
		$this->assertStringContainsString( '<article class="testimony-card">Un témoignage</article>', $html );
		$this->assertStringNotContainsString( 'aria-roledescription="carousel"', $html );
		$this->assertSame( 'kiyose_testimony', $GLOBALS['kiyose_test_last_query_args']['post_type'] );
		$this->assertSame( 1, $GLOBALS['kiyose_test_last_query_args']['posts_per_page'] );
	}

	public function test_kiyose_testimonials_shortcode_usesBoundedDefaultLimitAndStableOrder() {
		// Given
		$atts = array();

		// When
		kiyose_testimonials_shortcode( $atts );

		// Then
		$this->assertSame( 6, $GLOBALS['kiyose_test_last_query_args']['posts_per_page'] );
		$this->assertSame( 'date', $GLOBALS['kiyose_test_last_query_args']['orderby'] );
		$this->assertSame( 'DESC', $GLOBALS['kiyose_test_last_query_args']['order'] );
	}

	public function test_kiyose_testimonials_shortcode_whenLimitIsNegative_usesDefaultLimit() {
		// Given
		$atts = array(
			'limit' => '-1',
		);

		// When
		kiyose_testimonials_shortcode( $atts );

		// Then
		$this->assertSame( 6, $GLOBALS['kiyose_test_last_query_args']['posts_per_page'] );
	}

	public function test_kiyose_testimonials_shortcode_whenLimitIsTooHigh_capsToMaximumLimit() {
		// Given
		$atts = array(
			'limit' => '999',
		);

		// When
		kiyose_testimonials_shortcode( $atts );

		// Then
		$this->assertSame( 50, $GLOBALS['kiyose_test_last_query_args']['posts_per_page'] );
	}

	public function test_kiyose_testimonials_shortcode_whenLimitIsNotNumeric_usesDefaultLimit() {
		// Given
		$atts = array(
			'limit' => 'abc',
		);

		// When
		kiyose_testimonials_shortcode( $atts );

		// Then
		$this->assertSame( 6, $GLOBALS['kiyose_test_last_query_args']['posts_per_page'] );
	}

	public function test_kiyose_testimonials_shortcode_whenOrderIsRandom_usesOptInRandomOrderAndCapsLimit() {
		// Given
		$atts = array(
			'limit' => '999',
			'ordre' => 'aleatoire',
		);

		// When
		kiyose_testimonials_shortcode( $atts );

		// Then
		$this->assertSame( 50, $GLOBALS['kiyose_test_last_query_args']['posts_per_page'] );
		$this->assertSame( 'rand', $GLOBALS['kiyose_test_last_query_args']['orderby'] );
		$this->assertArrayNotHasKey( 'order', $GLOBALS['kiyose_test_last_query_args'] );
	}

	/**
	 * Render the CTA shortcode after asserting its callback exists.
	 *
	 * @param array<string, string> $atts Shortcode attributes.
	 * @return string
	 */
	private function render_cta_shortcode( array $atts ): string {
		$this->assertTrue( function_exists( 'kiyose_cta_shortcode' ), 'Missing kiyose_cta_shortcode().' );

		return kiyose_cta_shortcode( $atts );
	}
}
