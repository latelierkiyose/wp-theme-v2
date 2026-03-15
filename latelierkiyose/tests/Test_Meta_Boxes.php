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

	public function test_kiyose_sanitize_qa_items_ofValidJson_returnsSanitizedArray() {
		$json = wp_json_encode(
			array(
				array( 'question' => 'Qu\'est-ce que l\'art-thérapie ?', 'answer' => 'Une pratique créative.' ),
			)
		);

		$result = kiyose_sanitize_qa_items( $json );

		$this->assertCount( 1, $result );
		$this->assertSame( 'Qu\'est-ce que l\'art-thérapie ?', $result[0]['question'] );
		$this->assertSame( 'Une pratique créative.', $result[0]['answer'] );
	}

	public function test_kiyose_sanitize_qa_items_ofEmptyJson_returnsEmptyArray() {
		$result = kiyose_sanitize_qa_items( '[]' );

		$this->assertSame( array(), $result );
	}

	public function test_kiyose_sanitize_qa_items_whenJsonIsInvalid_returnsEmptyArray() {
		$result = kiyose_sanitize_qa_items( 'not-json' );

		$this->assertSame( array(), $result );
	}

	public function test_kiyose_sanitize_qa_items_whenItemHasHtmlTags_stripsHtml() {
		$json = wp_json_encode(
			array(
				array( 'question' => '<b>Question ?</b>', 'answer' => '<script>alert(1)</script>Réponse.' ),
			)
		);

		$result = kiyose_sanitize_qa_items( $json );

		$this->assertCount( 1, $result );
		$this->assertSame( 'Question ?', $result[0]['question'] );
		$this->assertSame( 'Réponse.', $result[0]['answer'] );
	}

	public function test_kiyose_sanitize_qa_items_whenJsonHasUnicodeEscapes_preservesAccents() {
		// JSON.stringify peut encoder "à" comme \u00e0 — le décodage doit préserver les accents.
		$json = '[{"question":"Vous vous sentez \u00e0 contre-courant ?","answer":"R\u00e9ponse."}]';

		$result = kiyose_sanitize_qa_items( $json );

		$this->assertCount( 1, $result );
		$this->assertSame( 'Vous vous sentez à contre-courant ?', $result[0]['question'] );
		$this->assertSame( 'Réponse.', $result[0]['answer'] );
	}

	public function test_kiyose_sanitize_qa_items_roundTrip_preservesAccentsAfterWpSlash() {
		$items = kiyose_sanitize_qa_items(
			'[{"question":"Vous vous sentez \u00e0 contre-courant ?","answer":"R\u00e9ponse."}]'
		);

		// Simule le cycle save/load WordPress : wp_slash() avant stockage, wp_unslash() à la lecture.
		$stored   = wp_unslash( wp_slash( wp_json_encode( $items ) ) );
		$reloaded = kiyose_sanitize_qa_items( $stored );

		$this->assertSame( 'Vous vous sentez à contre-courant ?', $reloaded[0]['question'] );
		$this->assertSame( 'Réponse.', $reloaded[0]['answer'] );
	}

	public function test_kiyose_sanitize_qa_items_whenBothFieldsEmpty_skipsItem() {
		$json = wp_json_encode(
			array(
				array( 'question' => '', 'answer' => '' ),
				array( 'question' => 'Vraie question ?', 'answer' => 'Vraie réponse.' ),
			)
		);

		$result = kiyose_sanitize_qa_items( $json );

		$this->assertCount( 1, $result );
		$this->assertSame( 'Vraie question ?', $result[0]['question'] );
	}
}
