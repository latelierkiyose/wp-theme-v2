<?php
/**
 * Tests for home page helpers.
 *
 * @package Kiyose
 * @since   0.2.7
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../inc/home.php';
require_once __DIR__ . '/../inc/content-filters.php';

/**
 * Test class for home page helper functions.
 */
class Test_Home extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		kiyose_reset_test_state();
	}

	public function test_kiyose_decode_json_meta_array_ofValidJson_returnsArray() {
		// Given
		$raw_value = '[{"label":"Art-thérapie","url":"/art-therapie/"}]';

		// When
		$result = $this->call_decode_json_meta_array( $raw_value );

		// Then
		$this->assertSame(
			array(
				array(
					'label' => 'Art-thérapie',
					'url'   => '/art-therapie/',
				),
			),
			$result
		);
	}

	public function test_kiyose_decode_json_meta_array_whenJsonIsInvalid_returnsEmptyArray() {
		// Given
		$raw_value = 'not-json';

		// When
		$result = $this->call_decode_json_meta_array( $raw_value );

		// Then
		$this->assertSame( array(), $result );
	}

	public function test_kiyose_decode_json_meta_array_ofEmptyString_returnsEmptyArray() {
		// Given
		$raw_value = '';

		// When
		$result = $this->call_decode_json_meta_array( $raw_value );

		// Then
		$this->assertSame( array(), $result );
	}

	public function test_kiyose_get_home_page_data_whenMetaFieldsAreEmpty_returnsDefaults() {
		// Given
		$post_id = 43;

		// When
		$result = $this->call_get_home_page_data( $post_id );

		// Then
		$this->assertSame(
			array(
				'welcome_title'    => 'L\'être humain est un vitrail, révélons ensemble sa lumière',
				'welcome_subtitle' => '',
				'welcome_text'     => '',
				'welcome_keywords' => array(),
				'welcome_slogan'   => '',
				'about_content'    => 'Au sein de L\'Atelier Kiyose, découvrez des pratiques de bien-être et de développement personnel qui vous accompagnent dans votre transformation : art-thérapie, rigologie, bols tibétains et ateliers philo.',
				'about_cta_text'   => 'En savoir plus sur l\'atelier',
				'about_cta_url'    => 'http://example.com/a-propos/',
				'about_image_id'   => '',
				'content1_qa'      => array(),
				'content1_slogan'  => '',
				'content2_text'    => '',
				'content2_quote'   => '',
				'content2_quote_author' => '',
			),
			$result
		);
	}

	public function test_kiyose_get_home_page_data_ofSavedMetaFields_returnsStructuredData() {
		// Given
		$post_id = 43;
		$GLOBALS['kiyose_test_post_meta'][43] = array(
			'kiyose_welcome_title'    => 'Bienvenue',
			'kiyose_welcome_subtitle' => 'Un sous-titre',
			'kiyose_welcome_text'     => 'Un texte',
			'kiyose_welcome_keywords' => '[{"label":"Rigologie","url":"/rigologie/"}]',
			'kiyose_welcome_slogan'   => 'Un slogan',
			'kiyose_hero_content'     => 'Contenu à propos',
			'kiyose_hero_cta_text'    => 'Lire la suite',
			'kiyose_hero_cta_url'     => '/a-propos/',
			'kiyose_hero_image_id'    => 123,
			'kiyose_content1_qa'      => '[{"question":"Une question ?","answer":"Une réponse."}]',
			'kiyose_content1_slogan'  => 'Slogan Q&R',
			'kiyose_content2_text'    => '<p>Texte libre</p>',
			'kiyose_content2_slogan'  => 'Slogan texte libre',
			'kiyose_content2_quote_author' => 'Michel Audiard',
		);

		// When
		$result = $this->call_get_home_page_data( $post_id );

		// Then
		$this->assertSame( 'Bienvenue', $result['welcome_title'] );
		$this->assertSame( 'Un sous-titre', $result['welcome_subtitle'] );
		$this->assertSame(
			array(
				array(
					'label' => 'Rigologie',
					'url'   => '/rigologie/',
				),
			),
			$result['welcome_keywords']
		);
		$this->assertSame( 'Contenu à propos', $result['about_content'] );
		$this->assertSame( '/a-propos/', $result['about_cta_url'] );
		$this->assertSame( 123, $result['about_image_id'] );
		$this->assertSame(
			array(
				array(
					'question' => 'Une question ?',
					'answer'   => 'Une réponse.',
				),
			),
			$result['content1_qa']
		);
		$this->assertSame( '<p>Texte libre</p>', $result['content2_text'] );
		$this->assertArrayHasKey( 'content2_quote', $result );
		$this->assertArrayHasKey( 'content2_quote_author', $result );
		$this->assertSame( 'Slogan texte libre', $result['content2_quote'] );
		$this->assertSame( 'Michel Audiard', $result['content2_quote_author'] );
	}

	public function test_contentFreeTemplate_ofQuoteAndAuthor_rendersSemanticQuoteBlock() {
		// Given
		$args = array(
			'content2_text'         => '<p>Texte libre</p>',
			'content2_quote'        => 'Heureux soient les fêlés, car ils laissent passer la lumière',
			'content2_quote_author' => 'Michel Audiard',
		);

		// When
		$html = $this->render_content_free_template( $args );

		// Then
		$this->assertStringContainsString( '<figure class="home-content2__quote-block">', $html );
		$this->assertStringContainsString( '<blockquote class="home-content2__quote home-emphase">', $html );
		$this->assertStringContainsString( '<p>Heureux soient les fêlés, car ils laissent passer la lumière</p>', $html );
		$this->assertStringContainsString( '<figcaption class="home-content2__quote-author">', $html );
		$this->assertStringContainsString( '<cite>Michel Audiard</cite>', $html );
		$this->assert_author_is_outside_blockquote( $html );
	}

	public function test_contentFreeTemplate_whenQuoteAuthorIsEmpty_omitsAttribution() {
		// Given
		$args = array(
			'content2_text'         => '',
			'content2_quote'        => 'Une citation sans auteurice',
			'content2_quote_author' => '',
		);

		// When
		$html = $this->render_content_free_template( $args );

		// Then
		$this->assertStringContainsString( '<blockquote class="home-content2__quote home-emphase">', $html );
		$this->assertStringNotContainsString( '<figcaption', $html );
		$this->assertStringNotContainsString( '<cite>', $html );
	}

	public function test_contentFreeTemplate_whenOnlyQuoteAuthorExists_omitsFreeContentSection() {
		// Given
		$args = array(
			'content2_text'         => '',
			'content2_quote'        => '',
			'content2_quote_author' => 'Michel Audiard',
		);

		// When
		$html = $this->render_content_free_template( $args );

		// Then
		$this->assertSame( '', trim( $html ) );
	}

	private function call_decode_json_meta_array( string $raw_value ): array {
		if ( ! function_exists( 'kiyose_decode_json_meta_array' ) ) {
			$this->fail( 'Expected kiyose_decode_json_meta_array() to exist.' );
		}

		return kiyose_decode_json_meta_array( $raw_value );
	}

	private function call_get_home_page_data( int $post_id ): array {
		if ( ! function_exists( 'kiyose_get_home_page_data' ) ) {
			$this->fail( 'Expected kiyose_get_home_page_data() to exist.' );
		}

		return kiyose_get_home_page_data( $post_id );
	}

	private function render_content_free_template( array $args ): string {
		if ( ! defined( 'KIYOSE_DISCOVERY_CALL_URL' ) ) {
			define( 'KIYOSE_DISCOVERY_CALL_URL', '/contact/' );
		}

		ob_start();
		require __DIR__ . '/../template-parts/home/content-free.php';

		return (string) ob_get_clean();
	}

	private function assert_author_is_outside_blockquote( string $html ): void {
		$blockquote_end_position = strpos( $html, '</blockquote>' );
		$cite_position           = strpos( $html, '<cite>Michel Audiard</cite>' );

		$this->assertNotFalse( $blockquote_end_position );
		$this->assertNotFalse( $cite_position );
		$this->assertGreaterThan( $blockquote_end_position, $cite_position );
	}
}
