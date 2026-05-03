<?php
/**
 * Tests for template markup contracts.
 *
 * @package Kiyose
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for static template accessibility contracts.
 */
class Test_Template_Markup extends TestCase {

	public function test_headerTemplate_rendersSkipLinkAndMobileMenuDialogContract() {
		// Given
		$template = file_get_contents( __DIR__ . '/../header.php' );

		// When
		$result = $template;

		// Then
		$this->assertStringContainsString( 'kiyose_get_skip_link()', $result );
		$this->assertStringContainsString( 'aria-expanded="false"', $result );
		$this->assertStringContainsString( 'aria-controls="mobile-menu"', $result );
		$this->assertStringContainsString( 'id="mobile-menu"', $result );
		$this->assertStringContainsString( 'role="dialog"', $result );
		$this->assertStringContainsString( 'aria-hidden="true"', $result );
	}

	public function test_headerTemplate_whenCustomLogoIsConfigured_keepsLogoInsideBubbleWrapper() {
		// Given
		$template = file_get_contents( __DIR__ . '/../header.php' );

		// When
		$has_logo_bubble_before_custom_logo_branch = preg_match(
			'/<div class="site-header__logo-bubble">\s*<\?php if \( has_custom_logo\(\) \) : \?>/m',
			$template
		);

		// Then
		$this->assertSame( 1, $has_logo_bubble_before_custom_logo_branch );
	}

	public function test_contactTemplate_whenPhotoIsRendered_usesConfiguredAltText() {
		// Given
		$template = file_get_contents( __DIR__ . '/../templates/page-contact.php' );

		// When
		$result = $template;

		// Then
		$this->assertStringContainsString( 'wp_get_attachment_image', $result );
		$this->assertStringContainsString( '$kiyose_contact_photo_alt', $result );
		$this->assertStringContainsString( "array( 'alt' => esc_attr( \$kiyose_contact_photo_alt ) )", $result );
	}

	public function test_serviceTemplate_usesConfiguredCtaLinksInsteadOfHardcodedUrls() {
		// Given
		$template = file_get_contents( __DIR__ . '/../templates/page-services.php' );

		// When
		$result = $template;

		// Then
		$this->assertStringContainsString( 'kiyose_get_service_cta_links', $result );
		$this->assertStringNotContainsString( "home_url( '/contact/' )", $result );
		$this->assertStringNotContainsString( "home_url( '/calendrier-tarifs/' )", $result );
	}

	public function test_footerTemplate_usesConfiguredLegalLinksInsteadOfHardcodedUrls() {
		// Given.
		$template = file_get_contents( __DIR__ . '/../footer.php' );

		// When.
		$result = $template;

		// Then.
		$this->assertStringContainsString( 'kiyose_get_footer_legal_links', $result );
		$this->assertStringNotContainsString( '/mentions-legales/', $result );
		$this->assertStringNotContainsString( '/politique-de-confidentialite/', $result );
	}

	public function test_contentBlogTemplate_whenThumbnailLinkIsDecorative_hidesItFromAssistiveTech() {
		// Given
		$template = file_get_contents( __DIR__ . '/../template-parts/content-blog.php' );

		// When
		$result = $template;

		// Then
		$this->assertStringContainsString( 'aria-hidden="true" tabindex="-1"', $result );
		$this->assertStringContainsString( 'blog-card__read-more', $result );
		$this->assertStringContainsString( "__( 'Lire la suite de %s', 'kiyose' )", $result );
	}
}
