<?php
/**
 * Tests for CTA CSS rules.
 *
 * @package Kiyose
 * @since   2.1.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for reusable CTA styles.
 */
class Test_CTA_CSS extends TestCase {

	public function test_cta_wrapper_whenStyled_centersButton() {
		// Given
		$css = $this->get_cta_css();

		// When / Then
		$this->assert_css_rule_contains( $css, '.kiyose-cta-wrapper', 'display: flex;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta-wrapper', 'justify-content: center;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta-wrapper', 'margin-left: auto;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta-wrapper', 'margin-right: auto;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta-wrapper', 'margin-top: var(--kiyose-spacing-xl);' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta-wrapper', 'max-width: 100%;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta-wrapper', 'text-align: center;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta-wrapper', 'width: 100%;' );
	}

	public function test_cta_whenStyled_matchesHomeGoldenButton() {
		// Given
		$css = $this->get_cta_css();

		// When / Then
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'align-items: center;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'background-color: var(--kiyose-color-gold-light);' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'border: 2px solid var(--kiyose-color-gold-light);' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'border-radius: 8px;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'color: var(--kiyose-color-burgundy);' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'display: inline-flex;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'flex-wrap: wrap;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'font-size: var(--kiyose-font-size-lg);' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'font-weight: 600;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'justify-content: center;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'min-height: 44px;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'min-width: 44px;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'max-width: 100%;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'overflow-wrap: anywhere;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'padding: var(--kiyose-spacing-md) var(--kiyose-spacing-xl);' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta', 'text-align: center;' );
	}

	public function test_cta_whenInteractive_keepsAccessibleStates() {
		// Given
		$css = $this->get_cta_css();

		// When / Then
		$this->assert_css_rule_contains( $css, '.kiyose-cta:focus-visible', 'outline: 2px solid var(--kiyose-color-burgundy);' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta:focus-visible', 'outline-offset: 4px;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta:hover', 'background-color: #E0B45E;' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta:hover', 'color: var(--kiyose-color-burgundy);' );
		$this->assert_css_rule_contains( $css, '.kiyose-cta:active', 'transform: translateY(0);' );
	}

	public function test_cta_whenViewportIsNarrow_usesAvailableWrapperWidth() {
		// Given
		$css = $this->get_cta_css();

		// When / Then
		$this->assertStringContainsString( '@media (width <= 480px)', $css );
		$this->assertStringContainsString( 'padding-left: var(--kiyose-spacing-lg);', $css );
		$this->assertStringContainsString( 'padding-right: var(--kiyose-spacing-lg);', $css );
		$this->assertStringContainsString( 'width: 100%;', $css );
		$this->assertStringContainsString( 'font-size: var(--kiyose-font-size-base);', $css );
	}

	/**
	 * Get CTA source CSS.
	 *
	 * @return string
	 */
	private function get_cta_css(): string {
		$path = __DIR__ . '/../assets/css/components/cta.css';

		if ( ! is_readable( $path ) ) {
			$this->fail( 'Missing CTA stylesheet.' );
		}

		return file_get_contents( $path );
	}

	/**
	 * Assert that a selector rule contains an expected declaration.
	 *
	 * @param string $css CSS source.
	 * @param string $selector CSS selector to find.
	 * @param string $expected_declaration Expected CSS declaration.
	 */
	private function assert_css_rule_contains( string $css, string $selector, string $expected_declaration ): void {
		$rules = $this->get_css_rules_for_selector( $css, $selector );

		$this->assertNotSame( array(), $rules, sprintf( 'Missing CSS rule for selector "%s".', $selector ) );

		foreach ( $rules as $rule ) {
			if ( str_contains( $rule, $expected_declaration ) ) {
				$this->addToAssertionCount( 1 );
				return;
			}
		}

		$this->fail( sprintf( 'Missing declaration "%s" for selector "%s".', $expected_declaration, $selector ) );
	}

	/**
	 * Get all CSS declaration blocks that target a selector.
	 *
	 * @param string $css CSS source.
	 * @param string $selector CSS selector to find.
	 * @return array<int, string>
	 */
	private function get_css_rules_for_selector( string $css, string $selector ): array {
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
}
