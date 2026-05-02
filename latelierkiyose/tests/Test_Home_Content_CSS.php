<?php
/**
 * Tests for home content quote CSS rules.
 *
 * @package Kiyose
 * @since   2.0.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for home content styles.
 */
class Test_Home_Content_CSS extends TestCase {

	public function test_home_content_quote_whenStyled_usesDedicatedQuoteBlock() {
		// Given
		$css = $this->get_home_content_css();

		// When / Then
		$this->assert_css_rule_contains( $css, '.home-content2__quote-block', 'margin: var(--kiyose-spacing-2xl) auto;' );
		$this->assert_css_rule_contains( $css, '.home-content2__quote-block', 'max-width: 700px;' );
		$this->assert_css_rule_contains( $css, '.home-content2__quote-block', 'text-align: center;' );
		$this->assert_css_rule_contains( $css, '.home-content2__quote', 'margin-bottom: 0;' );
		$this->assert_css_rule_contains( $css, '.home-content2__quote', 'margin-top: 0;' );
		$this->assert_css_rule_contains( $css, '.home-content2__quote p', 'display: inline;' );
		$this->assert_css_rule_contains( $css, '.home-content2__quote p', 'margin: 0;' );
	}

	public function test_home_content_quote_whenAuthorIsStyled_doesNotReceiveDecorativeQuotes() {
		// Given
		$home_content_css  = $this->get_home_content_css();
		$home_sections_css = file_get_contents( __DIR__ . '/../assets/css/components/home-sections.css' );
		$combined_css      = $home_content_css . "\n" . $home_sections_css;

		// When
		$author_rules = implode( "\n", $this->get_css_rules_for_selector( $combined_css, '.home-content2__quote-author' ) );

		// Then
		$this->assert_css_rule_contains( $home_content_css, '.home-content2__quote-author', 'color: var(--kiyose-color-burgundy);' );
		$this->assert_css_rule_contains( $home_content_css, '.home-content2__quote-author', 'font-family: var(--kiyose-font-body);' );
		$this->assert_css_rule_contains( $home_content_css, '.home-content2__quote-author', 'font-size: var(--kiyose-font-size-base);' );
		$this->assert_css_rule_contains( $home_content_css, '.home-content2__quote-author', 'font-weight: 600;' );
		$this->assert_css_rule_contains( $home_content_css, '.home-content2__quote-author', 'margin-top: var(--kiyose-spacing-sm);' );
		$this->assert_css_rule_contains( $home_content_css, '.home-content2__quote-author cite', 'font-style: normal;' );
		$this->assertStringNotContainsString( 'content:', $author_rules );
		$this->assertSame( array(), $this->get_css_rules_for_selector( $combined_css, '.home-content2__quote-author::before' ) );
		$this->assertSame( array(), $this->get_css_rules_for_selector( $combined_css, '.home-content2__quote-author::after' ) );
	}

	/**
	 * Get home content source CSS.
	 *
	 * @return string
	 */
	private function get_home_content_css() {
		return file_get_contents( __DIR__ . '/../assets/css/components/home-content.css' );
	}

	/**
	 * Assert that a selector rule contains an expected declaration.
	 *
	 * @param string $css CSS source.
	 * @param string $selector CSS selector to find.
	 * @param string $expected_declaration Expected CSS declaration.
	 */
	private function assert_css_rule_contains( $css, $selector, $expected_declaration ) {
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
}
