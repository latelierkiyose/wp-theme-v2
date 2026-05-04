<?php
/**
 * Tests for default page template CSS contracts.
 *
 * @package Kiyose
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for default page template CSS.
 */
class Test_Page_CSS extends TestCase {

	public function test_pageCss_whenRoundImageStyleIsUsed_preservesGutenbergRoundStyle() {
		// Given
		$css = file_get_contents( __DIR__ . '/../assets/css/components/page.css' );

		// When / Then
		$this->assertIsString( $css );
		$this->assertStringContainsString( '.page__content .is-style-kiyose-round img', $css );
		$this->assertStringContainsString( 'border-radius: 50%;', $css );
	}

	public function test_pageCss_whenCoreRoundedImageStyleIsUsed_preservesWordPressRoundedStyle() {
		// Given
		$css = file_get_contents( __DIR__ . '/../assets/css/components/page.css' );

		// When / Then
		$this->assertIsString( $css );
		$this->assertStringContainsString( '.page__content .is-style-rounded img', $css );
		$this->assertStringContainsString( 'border-radius: 9999px;', $css );
	}
}
