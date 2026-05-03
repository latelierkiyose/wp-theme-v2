<?php
/**
 * Tests for the theme preview screenshot.
 *
 * @package Kiyose
 * @since   2.1.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for theme screenshot assets.
 */
class Test_Theme_Screenshot extends TestCase {

	public function test_screenshot_whenThemeBundleIsBuilt_existsWithWordPressPreviewDimensions() {
		// Given
		$screenshot_path = dirname( __DIR__ ) . '/screenshot.png';

		// When
		$screenshot_size = file_exists( $screenshot_path ) ? getimagesize( $screenshot_path ) : false;

		// Then
		$this->assertFileExists( $screenshot_path );
		$this->assertIsArray( $screenshot_size );
		$this->assertSame( IMAGETYPE_PNG, $screenshot_size[2] );
		$this->assertSame( 1200, $screenshot_size[0] );
		$this->assertSame( 900, $screenshot_size[1] );
	}
}
