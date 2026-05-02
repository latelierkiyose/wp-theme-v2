<?php
/**
 * Tests for blog single CSS rules.
 *
 * @package Kiyose
 * @since   2.0.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for blog single component styles.
 */
class Test_Blog_Single_CSS extends TestCase {

	public function test_blog_single_featured_image_whenDesktopViewport_isCenteredAndConstrained() {
		// Given
		$css = file_get_contents( __DIR__ . '/../assets/css/components/blog-single.css' );

		// When
		$match_count = preg_match(
			'/@media\s*\(width\s*>=\s*1024px\)\s*\{\s*\.blog-single__featured-image\s*\{(?<rules>[^}]*)\}\s*\}/',
			$css,
			$matches
		);
		$rules       = $matches['rules'] ?? '';

		// Then
		$this->assertSame( 1, $match_count );
		$this->assertStringContainsString( 'margin-left: auto;', $rules );
		$this->assertStringContainsString( 'margin-right: auto;', $rules );
		$this->assertStringContainsString( 'max-width: 50%;', $rules );
		$this->assertStringContainsString( 'width: 420px;', $rules );
	}
}
