<?php
/**
 * Tests for Meta Boxes helper functions.
 *
 * @package Kiyose
 * @since   0.2.6
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for meta box helper functions.
 */
class Test_Meta_Boxes extends TestCase {

	public function test_kiyose_sanitize_welcome_keyword_item_ofValidInputs_returnsArray() {
		$result = kiyose_sanitize_welcome_keyword_item( 'Art-thérapie', 'https://example.com/art-therapie' );

		$this->assertIsArray( $result );
		$this->assertSame( 'Art-thérapie', $result['label'] );
		$this->assertSame( 'https://example.com/art-therapie', $result['url'] );
	}

	public function test_kiyose_sanitize_welcome_keyword_item_whenLabelIsEmpty_returnsNull() {
		$result = kiyose_sanitize_welcome_keyword_item( '', 'https://example.com' );

		$this->assertNull( $result );
	}

	public function test_kiyose_sanitize_welcome_keyword_item_whenUrlIsEmpty_returnsLabelWithEmptyUrl() {
		$result = kiyose_sanitize_welcome_keyword_item( 'Rigologie', '' );

		$this->assertIsArray( $result );
		$this->assertSame( 'Rigologie', $result['label'] );
		$this->assertSame( '', $result['url'] );
	}

	public function test_kiyose_sanitize_welcome_keyword_item_whenBothEmpty_returnsNull() {
		$result = kiyose_sanitize_welcome_keyword_item( '', '' );

		$this->assertNull( $result );
	}

	public function test_kiyose_sanitize_welcome_keyword_item_whenLabelHasHtmlTags_stripsHtml() {
		$result = kiyose_sanitize_welcome_keyword_item( '<b>Rigologie</b>', 'https://example.com' );

		$this->assertNotNull( $result );
		$this->assertSame( 'Rigologie', $result['label'] );
	}
}
