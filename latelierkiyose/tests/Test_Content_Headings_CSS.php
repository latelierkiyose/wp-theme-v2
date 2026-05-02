<?php
/**
 * Tests for editorial content heading CSS rules.
 *
 * @package Kiyose
 * @since   2.0.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for content heading alignment styles.
 */
class Test_Content_Headings_CSS extends TestCase {

	public function test_content_headings_whenInsideJustifiedContainers_areLeftAligned() {
		// Given
		$component_files = array(
			'../assets/css/components/page.css'         => array( '.page__content' ),
			'../assets/css/components/blog-single.css'  => array( '.blog-single__content' ),
			'../assets/css/components/about-page.css'   => array( '.about-page__content' ),
			'../assets/css/components/service-page.css' => array( '.service-page__content' ),
		);

		// When / Then
		foreach ( $component_files as $component_file => $containers ) {
			$css = file_get_contents( __DIR__ . '/' . $component_file );

			foreach ( $containers as $container ) {
				$this->assert_css_rule_contains( $css, $container, 'text-align: justify;' );
				$this->assert_content_heading_rules_are_left_aligned( $css, $container );
			}
		}
	}

	/**
	 * Assert that every editorial heading rule inside a container is left-aligned.
	 *
	 * @param string $css CSS source.
	 * @param string $container Container selector.
	 */
	private function assert_content_heading_rules_are_left_aligned( $css, $container ) {
		foreach ( array( 'h2', 'h3', 'h4', 'h5', 'h6' ) as $heading ) {
			$this->assert_css_rule_contains( $css, $container . ' ' . $heading, 'text-align: left;' );
		}
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
