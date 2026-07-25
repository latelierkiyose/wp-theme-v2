<?php
/**
 * Tests for landing page CSS contracts.
 *
 * @package Kiyose
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for landing page CSS.
 */
class Test_Landing_CSS extends TestCase {

	/**
	 * Read the landing stylesheet.
	 *
	 * @return string Stylesheet contents.
	 */
	private function get_landing_css(): string {
		return (string) file_get_contents( __DIR__ . '/../assets/css/components/landing.css' );
	}

	public function test_landingCss_whenPageIsRendered_paintsThemeBackgroundOnBody() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( 'background-color: var(--kiyose-color-background);', $css );
		$this->assertStringContainsString( 'color: var(--kiyose-color-text);', $css );
	}

	public function test_landingCss_whenContentIsLaidOut_constrainsContainerToReadingWidth() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '--kiyose-landing-container-width: 50rem;', $css );
		$this->assertStringContainsString( '.landing__container', $css );
		$this->assertStringContainsString( 'max-width: var(--kiyose-landing-container-width);', $css );
		$this->assertStringContainsString( 'margin-inline: auto;', $css );
	}

	public function test_landingCss_whenThemeBundleIsAbsent_carriesItsOwnSkipLinkStyles() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '.skip-link {', $css );
		$this->assertStringContainsString( '.skip-link:focus {', $css );
		$this->assertStringContainsString( 'top: -999px;', $css );
	}

	public function test_landingCss_whenElementsAreFocused_keepsFocusVisible() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( ':focus-visible {', $css );
		$this->assertStringContainsString( 'outline: 2px solid var(--kiyose-color-burgundy);', $css );
	}

	public function test_landingCss_whenBoxModelIsReset_appliesBorderBoxSizing() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( 'box-sizing: border-box;', $css );
		$this->assertStringContainsString( 'max-width: 100%;', $css );
	}
}
