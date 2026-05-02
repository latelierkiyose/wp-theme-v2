<?php
/**
 * Tests for service page CSS contracts.
 *
 * @package Kiyose
 * @since   2.1.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for service page CSS.
 */
class Test_Service_Page_CSS extends TestCase {

	public function test_servicePageCss_whenRoundImageStyleIsUsed_preservesGutenbergRoundStyle() {
		// Given
		$css = file_get_contents( __DIR__ . '/../assets/css/components/service-page.css' );

		// When / Then
		$this->assertIsString( $css );
		$this->assertStringContainsString( '.service-page__content .is-style-kiyose-round img', $css );
		$this->assertStringContainsString( 'border-radius: 50%;', $css );
	}

	public function test_servicePageCss_whenCoreRoundedImageStyleIsUsed_preservesWordPressRoundedStyle() {
		// Given
		$css = file_get_contents( __DIR__ . '/../assets/css/components/service-page.css' );

		// When / Then
		$this->assertIsString( $css );
		$this->assertStringContainsString( '.service-page__content .is-style-rounded img', $css );
		$this->assertStringContainsString( 'border-radius: 9999px;', $css );
	}
}
