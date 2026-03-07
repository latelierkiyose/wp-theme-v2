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
 *
 * Note: Tests requiring WordPress (shortcode execution, WP_Query, etc.) would require
 * the WordPress test suite. These tests only cover standalone functions that can be
 * tested without WordPress.
 */
class Test_Shortcodes extends TestCase {

	/**
	 * Test that shortcode files are loaded.
	 */
	public function test_shortcode_file_exists() {
		$this->assertFileExists( __DIR__ . '/../inc/shortcodes.php' );
	}

	/**
	 * Test shortcode file is valid PHP.
	 */
	public function test_shortcode_file_is_valid_php() {
		$file_content = file_get_contents( __DIR__ . '/../inc/shortcodes.php' );
		$this->assertStringContainsString( 'function kiyose_testimonials_shortcode', $file_content );
		$this->assertStringContainsString( 'function kiyose_signets_shortcode', $file_content );
	}
}
