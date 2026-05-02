<?php
/**
 * Tests for compact social icon CSS rules.
 *
 * @package Kiyose
 * @since   2.0.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for compact social icon styles.
 */
class Test_Social_Icons_CSS extends TestCase {

	public function test_social_icons_whenCompactLinksAreStyled_useSharedBrandStates() {
		// Given
		$compact_links = $this->get_compact_social_link_styles();

		// When / Then
		foreach ( $compact_links as $selector => $css ) {
			$this->assert_css_rule_contains( $css, $selector, 'background-color: var(--kiyose-color-primary);' );
			$this->assert_css_rule_contains( $css, $selector, 'color: var(--kiyose-color-burgundy);' );
			$this->assert_css_rule_contains( $css, $selector . ':hover', 'background-color: var(--kiyose-color-burgundy);' );
			$this->assert_css_rule_contains( $css, $selector . ':hover', 'color: #ffffff;' );
			$this->assert_css_rule_contains( $css, $selector . ':focus-visible', 'background-color: var(--kiyose-color-burgundy);' );
			$this->assert_css_rule_contains( $css, $selector . ':focus-visible', 'color: #ffffff;' );
		}
	}

	public function test_social_icons_whenCompactLinksAreInteractive_doNotMove() {
		// Given
		$compact_links = $this->get_compact_social_link_styles();

		// When / Then
		foreach ( $compact_links as $selector => $css ) {
			$base_rules  = implode( "\n", $this->get_css_rules_for_selector( $css, $selector ) );
			$hover_rules = implode( "\n", $this->get_css_rules_for_selector( $css, $selector . ':hover' ) );
			$focus_rules = implode( "\n", $this->get_css_rules_for_selector( $css, $selector . ':focus-visible' ) );

			$this->assertStringNotContainsString( 'transform', $base_rules, sprintf( 'Compact social link "%s" must not transition transform.', $selector ) );
			$this->assertStringNotContainsString( 'transform:', $hover_rules, sprintf( 'Compact social link "%s" must not move on hover.', $selector ) );
			$this->assertStringNotContainsString( 'transform:', $focus_rules, sprintf( 'Compact social link "%s" must not move on focus.', $selector ) );
		}
	}

	public function test_social_icons_whenShareLinksAreStyled_doNotUseNetworkHoverColors() {
		// Given
		$css = file_get_contents( __DIR__ . '/../assets/css/components/blog-single.css' );

		// When
		$interactive_share_rules = implode( "\n", $this->get_interactive_share_link_rules( $css ) );

		// Then
		$this->assertStringNotContainsString( '#1877f2', $interactive_share_rules );
		$this->assertStringNotContainsString( '#0a66c2', $interactive_share_rules );
		$this->assertStringNotContainsString( 'radial-gradient', $interactive_share_rules );
	}

	/**
	 * Get source CSS keyed by compact social link selector.
	 *
	 * @return array<string, string>
	 */
	private function get_compact_social_link_styles() {
		return array(
			'.site-footer__social a' => file_get_contents( __DIR__ . '/../assets/css/components/footer.css' ),
			'.blog-single__share-link' => file_get_contents( __DIR__ . '/../assets/css/components/blog-single.css' ),
		);
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

	/**
	 * Get all hover and focus rules for share links.
	 *
	 * @param string $css CSS source.
	 * @return array<int, string>
	 */
	private function get_interactive_share_link_rules( $css ) {
		preg_match_all(
			'/(?<selectors>[^{}]+)\{(?<rules>[^{}]*)\}/',
			$css,
			$matches,
			PREG_SET_ORDER
		);

		$rules = array();

		foreach ( $matches as $match ) {
			$selector_list = preg_replace( '#/\*.*?\*/#s', '', $match['selectors'] );

			if (
				str_contains( $selector_list, '.blog-single__share-link' )
				&& ( str_contains( $selector_list, ':hover' ) || str_contains( $selector_list, ':focus' ) )
			) {
				$rules[] = $match['rules'];
			}
		}

		return $rules;
	}
}
