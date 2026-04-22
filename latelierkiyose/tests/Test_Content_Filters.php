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
