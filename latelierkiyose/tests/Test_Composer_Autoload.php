<?php
/**
 * Tests for Composer autoload configuration.
 *
 * @package Kiyose
 * @since   2.1.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for Composer autoload configuration.
 */
class Test_Composer_Autoload extends TestCase {

	public function test_autoloadDev_whenConfigured_doesNotClassmapPhpunitTests() {
		// Given
		$composer_json_path = dirname( __DIR__, 2 ) . '/composer.json';
		$this->assertFileExists( $composer_json_path );

		// When
		$config           = json_decode( file_get_contents( $composer_json_path ), true, 512, JSON_THROW_ON_ERROR );
		$autoload_dev    = $config['autoload-dev'] ?? array();
		$classmap_paths  = $autoload_dev['classmap'] ?? array();
		$has_tests_path  = in_array( 'latelierkiyose/tests/', $classmap_paths, true );
		$has_tests_path |= in_array( 'latelierkiyose/tests', $classmap_paths, true );

		// Then
		$this->assertFalse(
			(bool) $has_tests_path,
			'PHPUnit discovers test files itself; classmapping them can autoload WordPress stubs while the same test file is being required.'
		);
	}
}
