<?php
/**
 * Tests for about page CSS contracts.
 *
 * @package Kiyose
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for about page CSS.
 */
class Test_About_Page_CSS extends TestCase {

	public function test_aboutPageCss_whenRoundImageStyleIsUsed_preservesGutenbergRoundStyle() {
		// Given
		$css = file_get_contents( __DIR__ . '/../assets/css/components/about-page.css' );

		// When / Then
		$this->assertIsString( $css );
		$this->assertStringContainsString( '.about-page__content .is-style-kiyose-round img', $css );
		$this->assertStringContainsString( 'border-radius: 50%;', $css );
	}

	public function test_aboutPageCss_whenCoreRoundedImageStyleIsUsed_preservesWordPressRoundedStyle() {
		// Given
		$css = file_get_contents( __DIR__ . '/../assets/css/components/about-page.css' );

		// When / Then
		$this->assertIsString( $css );
		$this->assertStringContainsString( '.about-page__content .is-style-rounded img', $css );
		$this->assertStringContainsString( 'border-radius: 9999px;', $css );
	}
}
