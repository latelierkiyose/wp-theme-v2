<?php
/**
 * Tests for Content Filters.
 *
 * @package Kiyose
 * @since   0.2.6
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../inc/content-filters.php';

/**
 * Test class for kiyose_fr_nbsp() — French thin non-breaking space injection.
 */
class Test_Content_Filters extends TestCase {

	private const NBSP = '&#8239;';

	public function test_kiyose_auto_add_heading_ids_ofPlainH2_addsSlugId() {
		// Given
		$content = '<p>Avant</p><h2>Art-thérapie créative</h2><p>Après</p>';

		// When
		$result = kiyose_auto_add_heading_ids( $content );

		// Then
		$this->assertSame( '<p>Avant</p><h2 id="art-therapie-creative">Art-thérapie créative</h2><p>Après</p>', $result );
	}

	public function test_kiyose_auto_add_heading_ids_whenHeadingAlreadyHasId_preservesExistingId() {
		// Given
		$content = '<h2 id="ancre-existante">Une section</h2>';

		// When
		$result = kiyose_auto_add_heading_ids( $content );

		// Then
		$this->assertSame( $content, $result );
	}

	public function test_kiyose_auto_add_heading_ids_whenHeadingsHaveSameText_addsIncrementalSuffix() {
		// Given
		$content = '<h2>Atelier</h2><h2>Atelier</h2>';

		// When
		$result = kiyose_auto_add_heading_ids( $content );

		// Then
		$this->assertSame( '<h2 id="atelier">Atelier</h2><h2 id="atelier-2">Atelier</h2>', $result );
	}

	public function test_kiyose_auto_add_heading_ids_whenHeadingHasAttributes_preservesAttributes() {
		// Given
		$content = '<h2 class="wp-block-heading">Questions fréquentes</h2>';

		// When
		$result = kiyose_auto_add_heading_ids( $content );

		// Then
		$this->assertSame( '<h2 class="wp-block-heading" id="questions-frequentes">Questions fréquentes</h2>', $result );
	}

	public function test_kiyose_auto_add_heading_ids_whenHeadingTextHasNoSlug_usesFallbackId() {
		// Given
		$content = '<h2><span aria-hidden="true">***</span></h2>';

		// When
		$result = kiyose_auto_add_heading_ids( $content );

		// Then
		$this->assertMatchesRegularExpression( '/^<h2 id="heading-\d{4}"><span aria-hidden="true">\*\*\*<\/span><\/h2>$/', $result );
	}

	public function test_kiyose_fr_nbsp_ofEmptyString_returnsEmptyString() {
		// Given
		$input = '';

		// When
		$result = kiyose_fr_nbsp( $input );

		// Then
		$this->assertSame( '', $result );
	}

	public function test_kiyose_fr_nbsp_ofTextWithoutPunctuation_returnsUnchanged() {
		// Given
		$input = 'Bienvenue à L Atelier Kiyose';

		// When
		$result = kiyose_fr_nbsp( $input );

		// Then
		$this->assertSame( $input, $result );
	}

	public function test_kiyose_fr_nbsp_ofQuestionMark_insertsNbspBefore() {
		// Given
		$input = 'Mais où est Charlie ?';

		// When
		$result = kiyose_fr_nbsp( $input );

		// Then
		$this->assertSame( 'Mais où est Charlie' . self::NBSP . '?', $result );
	}

	public function test_kiyose_fr_nbsp_ofExclamation_insertsNbspBefore() {
		// Given
		$input = 'Quelle lumière !';

		// When
		$result = kiyose_fr_nbsp( $input );

		// Then
		$this->assertSame( 'Quelle lumière' . self::NBSP . '!', $result );
	}

	public function test_kiyose_fr_nbsp_ofColon_insertsNbspBefore() {
		// Given
		$input = 'Notre mission : révéler';

		// When
		$result = kiyose_fr_nbsp( $input );

		// Then
		$this->assertSame( 'Notre mission' . self::NBSP . ': révéler', $result );
	}

	public function test_kiyose_fr_nbsp_ofSemicolon_insertsNbspBefore() {
		// Given
		$input = 'Respirer ; écouter';

		// When
		$result = kiyose_fr_nbsp( $input );

		// Then
		$this->assertSame( 'Respirer' . self::NBSP . '; écouter', $result );
	}

	public function test_kiyose_fr_nbsp_ofMultiplePunctuations_insertsAllNbsps() {
		// Given
		$input = 'Prêt ? Allons-y !';

		// When
		$result = kiyose_fr_nbsp( $input );

		// Then
		$this->assertSame( 'Prêt' . self::NBSP . '? Allons-y' . self::NBSP . '!', $result );
	}

	public function test_kiyose_fr_nbsp_ofPunctuationWithoutSpace_doesNothing() {
		// Given — pas d'espace entre le mot et la ponctuation (URL, acronyme, etc.)
		$input = 'FAQ?section=1';

		// When
		$result = kiyose_fr_nbsp( $input );

		// Then
		$this->assertSame( $input, $result );
	}

	public function test_kiyose_fr_nbsp_ofUnicodeInput_preservesCharacters() {
		// Given
		$input = 'L être humain : où va-t-il ?';

		// When
		$result = kiyose_fr_nbsp( $input );

		// Then
		$this->assertSame( 'L être humain' . self::NBSP . ': où va-t-il' . self::NBSP . '?', $result );
	}
}
