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
}
