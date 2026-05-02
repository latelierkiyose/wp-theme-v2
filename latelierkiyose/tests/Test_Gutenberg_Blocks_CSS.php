<?php
/**
 * Tests for Gutenberg block CSS contracts.
 *
 * @package Kiyose
 * @since   2.1.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for Gutenberg block CSS.
 */
class Test_Gutenberg_Blocks_CSS extends TestCase {

	public function test_gutenbergBlocksCss_whenRoundImageStyleIsUsed_declaresRoundImageContract() {
		// Given
		$css = $this->get_gutenberg_blocks_css();

		// When / Then
		$this->assertStringContainsString( '.wp-block-image.is-style-kiyose-round', $css );
		$this->assertStringContainsString( 'aspect-ratio: 1;', $css );
		$this->assertStringContainsString( 'border-radius: 50%;', $css );
		$this->assertStringContainsString( 'object-fit: cover;', $css );
	}

	public function test_gutenbergBlocksCss_whenRoundImageFloatsOnDesktop_declaresCircularTextWrap() {
		// Given
		$css = $this->get_gutenberg_blocks_css();

		// When / Then
		$this->assertStringContainsString( '@media (width >= 1024px)', $css );
		$this->assertStringContainsString( '.wp-block-image.is-style-kiyose-round.alignleft', $css );
		$this->assertStringContainsString( '.wp-block-image.is-style-kiyose-round.alignright', $css );
		$this->assertStringContainsString( 'shape-margin:', $css );
		$this->assertStringContainsString( 'shape-outside: circle(50%);', $css );
	}

	public function test_gutenbergBlocksCss_whenRoundImageStyleWrapsAlignedFigure_declaresNestedGutenbergContract() {
		// Given
		$css = $this->get_gutenberg_blocks_css();

		// When / Then
		$this->assertStringContainsString( 'float: none;', $css );
		$this->assertStringContainsString( 'div.wp-block-image.is-style-kiyose-round', $css );
		$this->assertStringContainsString( '.wp-block-image.is-style-kiyose-round > figure', $css );
		$this->assertStringContainsString( '.wp-block-image.is-style-kiyose-round > img', $css );
		$this->assertStringContainsString( '.wp-block-image .alignleft', $css );
		$this->assertStringContainsString( '.wp-block-image .alignright', $css );
		$this->assertStringContainsString( '.wp-block-image.is-style-kiyose-round > figure.alignleft', $css );
		$this->assertStringContainsString( '.wp-block-image.is-style-kiyose-round > figure.alignright', $css );
	}

	private function get_gutenberg_blocks_css() {
		$css = file_get_contents( __DIR__ . '/../assets/css/components/gutenberg-blocks.css' );

		$this->assertIsString( $css );

		return $css;
	}
}
