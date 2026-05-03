<?php
/**
 * Tests for header CSS contracts.
 *
 * @package Kiyose
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for header styles.
 */
class Test_Header_CSS extends TestCase {

	public function test_headerCss_whenLogoUsesWordPressCustomLogoLink_sizesImageFromBubbleWrapper() {
		// Given
		$css = $this->get_header_css();

		// When
		$rules = $this->get_css_rules_for_selector( $css, '.site-header__logo-bubble img' );

		// Then
		$this->assertNotSame( array(), $rules, 'Missing CSS rule for logo images inside the bubble wrapper.' );
		$this->assert_css_rule_contains( $rules, 'height: clamp(60px, 15vw, 250px);' );
		$this->assert_css_rule_contains( $rules, 'width: auto;' );
	}

	private function get_header_css() {
		$css = file_get_contents( __DIR__ . '/../assets/css/components/header.css' );

		$this->assertIsString( $css );

		return $css;
	}

	/**
	 * Get all CSS declaration blocks that target a selector.
	 *
	 * @param string $css CSS source.
	 * @param string $selector CSS selector to find.
	 * @return array<int, string>
	 */
	private function get_css_rules_for_selector( $css, $selector ) {
		preg_match_all(
			'/(?<selectors>[^{}]+)\{(?<rules>[^{}]*)\}/',
			$css,
			$matches,
			PREG_SET_ORDER
		);

		$rules = array();

		foreach ( $matches as $match ) {
			$selector_list = preg_replace( '#/\*.*?\*/#s', '', $match['selectors'] );
			$selectors     = array_map( 'trim', explode( ',', $selector_list ) );

			if ( in_array( $selector, $selectors, true ) ) {
				$rules[] = $match['rules'];
			}
		}

		return $rules;
	}

	/**
	 * Assert that at least one CSS rule contains a declaration.
	 *
	 * @param array<int, string> $rules CSS declaration blocks.
	 * @param string             $expected_declaration Expected CSS declaration.
	 */
	private function assert_css_rule_contains( array $rules, $expected_declaration ) {
		foreach ( $rules as $rule ) {
			if ( str_contains( $rule, $expected_declaration ) ) {
				$this->addToAssertionCount( 1 );
				return;
			}
		}

		$this->fail( sprintf( 'Missing declaration "%s" in CSS rule.', $expected_declaration ) );
	}
}
