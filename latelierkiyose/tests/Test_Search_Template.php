<?php
/**
 * Tests for the search results template.
 *
 * @package Kiyose
 * @since   2.0.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for search results template safeguards.
 */
class Test_Search_Template extends TestCase {

	public function test_searchTemplate_whenWordPressAddsSearchResultsBodyClass_usesDedicatedComponentClass() {
		// Given
		$template = file_get_contents( dirname( __DIR__ ) . '/search.php' );

		// When
		$has_reserved_component_class = false !== strpos( $template, '<section class="search-results"' );

		// Then
		$this->assertFalse( $has_reserved_component_class );
	}

	public function test_searchStyles_whenWordPressAddsSearchResultsBodyClass_doNotTargetReservedBodyClass() {
		// Given
		$stylesheet = file_get_contents( dirname( __DIR__ ) . '/assets/css/components/search.css' );

		// When
		$has_reserved_body_selector = 1 === preg_match( '/(^|[\n,{])\s*\.search-results\s*\{/m', $stylesheet );

		// Then
		$this->assertFalse( $has_reserved_body_selector );
	}
}
