<?php
/**
 * Tests for navigation CSS contracts.
 *
 * @package Kiyose
 * @since   2.1.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for navigation styles.
 */
class Test_Navigation_CSS extends TestCase {

	public function test_navigationCss_whenDesktopSubmenuIsOpen_hasNoPointerGap() {
		// Given
		$css = $this->get_navigation_css();

		// When
		$submenu_rules = $this->get_css_rules_for_selector( $css, '.main-nav__list .sub-menu' );

		// Then
		$this->assertNotSame( array(), $submenu_rules, 'Missing desktop submenu CSS rule.' );
		$this->assert_css_rule_contains( $submenu_rules, 'top: 100%;' );
		$this->assert_css_rule_contains( $submenu_rules, 'margin: 0;' );
		$this->assert_css_rules_do_not_contain( $submenu_rules, 'margin: var(--kiyose-spacing-xs) 0 0 0;' );
	}

	private function get_navigation_css() {
		$css = file_get_contents( __DIR__ . '/../assets/css/components/navigation.css' );

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

		$this->fail( sprintf( 'Missing declaration "%s" in submenu CSS rule.', $expected_declaration ) );
	}

	/**
	 * Assert that no CSS rule contains a declaration.
	 *
	 * @param array<int, string> $rules CSS declaration blocks.
	 * @param string             $unexpected_declaration Unexpected CSS declaration.
	 */
	private function assert_css_rules_do_not_contain( array $rules, $unexpected_declaration ) {
		foreach ( $rules as $rule ) {
			$this->assertStringNotContainsString( $unexpected_declaration, $rule );
		}
	}
}
