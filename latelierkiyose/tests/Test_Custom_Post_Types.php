<?php
/**
 * Tests for Custom Post Types.
 *
 * @package Kiyose
 * @since   0.1.6
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for kiyose_testimony custom post type helper functions.
 *
 * Note: Tests requiring WordPress (CPT registration, meta boxes, etc.) would require
 * the WordPress test suite. These tests only cover standalone helper functions.
 */
class Test_Custom_Post_Types extends TestCase {

	/**
	 * Test kiyose_get_testimony_context_label function returns correct labels.
	 */
	public function test_kiyose_get_testimony_context_label_returns_correct_labels() {
		$this->assertEquals( '', kiyose_get_testimony_context_label( 'individual' ) );
		$this->assertEquals( 'Entreprise', kiyose_get_testimony_context_label( 'enterprise' ) );
		$this->assertEquals( 'Association / Structure', kiyose_get_testimony_context_label( 'structure' ) );
	}

	/**
	 * Test kiyose_get_testimony_context_label function returns empty string for unknown context.
	 */
	public function test_kiyose_get_testimony_context_label_returns_empty_for_unknown_context() {
		$this->assertEquals( '', kiyose_get_testimony_context_label( 'unknown' ) );
		$this->assertEquals( '', kiyose_get_testimony_context_label( '' ) );
	}
}
