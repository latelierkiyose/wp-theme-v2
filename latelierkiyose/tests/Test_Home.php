<?php
/**
 * Tests for home page helpers.
 *
 * @package Kiyose
 * @since   0.2.7
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../inc/home.php';

/**
 * Test class for home page helper functions.
 */
class Test_Home extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['kiyose_test_post_meta'] = array();
	}

	public function test_kiyose_decode_json_meta_array_ofValidJson_returnsArray() {
		// Given.
		$raw_value = '[{"label":"Art-thérapie","url":"/art-therapie/"}]';

		// When.
		$result = $this->call_decode_json_meta_array( $raw_value );

		// Then.
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
		// Given.
		$raw_value = 'not-json';

		// When.
		$result = $this->call_decode_json_meta_array( $raw_value );

		// Then.
		$this->assertSame( array(), $result );
	}

	public function test_kiyose_decode_json_meta_array_ofEmptyString_returnsEmptyArray() {
		// Given.
		$raw_value = '';

		// When.
		$result = $this->call_decode_json_meta_array( $raw_value );

		// Then.
		$this->assertSame( array(), $result );
	}

	public function test_kiyose_get_home_page_data_whenMetaFieldsAreEmpty_returnsDefaults() {
		// Given.
		$post_id = 43;

		// When.
		$result = $this->call_get_home_page_data( $post_id );

		// Then.
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
				'content2_slogan'  => '',
			),
			$result
		);
	}

	public function test_kiyose_get_home_page_data_ofSavedMetaFields_returnsStructuredData() {
		// Given.
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
		);

		// When.
		$result = $this->call_get_home_page_data( $post_id );

		// Then.
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
		$this->assertSame( 'Slogan texte libre', $result['content2_slogan'] );
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
}
