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

	/**
	 * Read the design tokens stylesheet.
	 *
	 * @return string Stylesheet contents.
	 */
	private function get_variables_css(): string {
		return (string) file_get_contents( __DIR__ . '/../assets/css/variables.css' );
	}

	public function test_landingCss_whenPageIsRendered_paintsThemeBackgroundOnBody() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( 'background-color: var(--kiyose-color-background);', $css );
		$this->assertStringContainsString( 'color: var(--kiyose-color-burgundy);', $css );
	}

	public function test_variablesCss_whenGoldTitlesArePainted_declaresAccessibleDarkGoldToken() {
		// Given
		$css = $this->get_variables_css();

		// When / Then
		$this->assertStringContainsString( '--kiyose-color-gold-dark: #8A5D00;', $css );
	}

	public function test_landingCss_whenHeadingsAreRendered_useBurgundyScriptTypography() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( 'font-family: var(--kiyose-font-heading);', $css );
		$this->assertStringContainsString( 'color: var(--kiyose-color-burgundy);', $css );
		$this->assertStringContainsString( 'font-size: var(--kiyose-font-size-4xl);', $css );
	}

	public function test_landingCss_whenTitleNeedsEmphasis_offersAccessibleGoldModifier() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '.landing__title--gold {', $css );
		$this->assertStringContainsString( 'color: var(--kiyose-color-gold-dark);', $css );
	}

	public function test_landingCss_whenLinksAreRendered_areUnderlined() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( 'text-decoration: underline;', $css );
	}

	public function test_landingCss_whenContentIsLaidOut_constrainsContainerToReadingWidth() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '--kiyose-landing-container-width: 64rem;', $css );
		$this->assertStringContainsString( '.landing__container', $css );
		$this->assertStringContainsString( 'max-width: var(--kiyose-landing-container-width);', $css );
		$this->assertStringContainsString( 'margin-inline: auto;', $css );
	}

	public function test_landingCss_whenParagraphNeedsToBeNarrower_offersANarrowWidth() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '--kiyose-landing-narrow-width: 34rem;', $css );
		$this->assertStringContainsString( '.landing__narrow {', $css );
		$this->assertStringContainsString( 'max-width: var(--kiyose-landing-narrow-width);', $css );
	}

	public function test_landingCss_whenHeroPhotoNeedsToReachTheEdge_offersABleedWidth() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '.landing__bleed {', $css );
		$this->assertStringContainsString( 'margin-inline: calc(50% - 50vw);', $css );
		$this->assertStringContainsString( 'max-width: 100vw;', $css );
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

	public function test_landingCss_whenTwoColumnSectionsAreNarrow_stackColumns() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '.landing__columns {', $css );
		$this->assertStringContainsString( 'display: flex;', $css );
		$this->assertStringContainsString( 'flex-direction: column;', $css );
		$this->assertStringContainsString( '@media (width >= 768px) {', $css );
	}

	public function test_landingCss_whenColumnWidthIsSetInTheEditor_respectsIt() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '.landing__columns > .wp-block-column {', $css );
		$this->assertStringContainsString( 'flex-grow: 1;', $css );
		$this->assertStringNotContainsString( 'flex: 1;', $css );
	}

	public function test_landingCss_whenCtaBlockIsPainted_usesAccessibleGoldPair() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '.landing__cta {', $css );
		$this->assertStringContainsString( 'background-color: var(--kiyose-color-gold-light);', $css );
		$this->assertStringContainsString( 'color: var(--kiyose-color-burgundy);', $css );
		$this->assertStringNotContainsString( 'text-align: center;', $css );
	}

	public function test_landingCss_whenBrevoFormSitsInCtaBlock_overridesItsColorsForContrast() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '.landing__cta form[id^="sib_signup_form_"] input[type="text"],', $css );
		$this->assertStringContainsString( 'border-color: var(--kiyose-color-burgundy) !important;', $css );
		$this->assertStringContainsString( '.landing__cta form[id^="sib_signup_form_"] .sib-default-btn,', $css );
		$this->assertStringContainsString( 'background-color: var(--kiyose-color-burgundy) !important;', $css );
		$this->assertStringContainsString( 'color: var(--kiyose-color-white) !important;', $css );
	}

	public function test_landingCss_whenBioPhotoIsDisplayed_cropsItToACircle() {
		// Given
		$css = $this->get_landing_css();

		// When / Then
		$this->assertStringContainsString( '.landing__photo--circle {', $css );
		$this->assertStringContainsString( 'border-radius: 50%;', $css );
		$this->assertStringContainsString( 'object-fit: cover;', $css );
	}
}
