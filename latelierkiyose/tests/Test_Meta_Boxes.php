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

	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['kiyose_test_added_meta_boxes'] = array();
		$GLOBALS['kiyose_test_post_meta']        = array();
		$GLOBALS['kiyose_test_current_screen']   = null;
		$GLOBALS['kiyose_test_enqueued_styles']  = array();
		$GLOBALS['kiyose_test_enqueued_scripts'] = array();
		$GLOBALS['kiyose_test_localized_scripts'] = array();
		$GLOBALS['kiyose_test_did_enqueue_media'] = false;
	}

	private function set_page_template( int $post_id, string $template ): void {
		$GLOBALS['kiyose_test_post_meta'][ $post_id ]['_wp_page_template'] = $template;
	}

	private function registered_meta_box_ids(): array {
		return array_column( $GLOBALS['kiyose_test_added_meta_boxes'], 'id' );
	}

	private function enqueued_style_handles(): array {
		return array_column( $GLOBALS['kiyose_test_enqueued_styles'], 'handle' );
	}

	private function enqueued_script_handles(): array {
		return array_column( $GLOBALS['kiyose_test_enqueued_scripts'], 'handle' );
	}

	public function test_kiyose_page_uses_template_whenPostIsMissing_returnsFalse() {
		// Given.
		$post = null;

		// When.
		$result = function_exists( 'kiyose_page_uses_template' )
			? kiyose_page_uses_template( $post, 'templates/page-home.php' )
			: null;

		// Then.
		$this->assertFalse( $result );
	}

	public function test_kiyose_page_uses_template_whenTemplateMatches_returnsTrue() {
		// Given.
		$post = (object) array( 'ID' => 43 );
		$this->set_page_template( 43, 'templates/page-home.php' );

		// When.
		$result = function_exists( 'kiyose_page_uses_template' )
			? kiyose_page_uses_template( $post, 'templates/page-home.php' )
			: null;

		// Then.
		$this->assertTrue( $result );
	}

	public function test_kiyose_page_uses_template_whenTemplateDiffers_returnsFalse() {
		// Given.
		$post = (object) array( 'ID' => 43 );
		$this->set_page_template( 43, 'templates/page-contact.php' );

		// When.
		$result = function_exists( 'kiyose_page_uses_template' )
			? kiyose_page_uses_template( $post, 'templates/page-home.php' )
			: null;

		// Then.
		$this->assertFalse( $result );
	}

	public function test_kiyose_add_home_hero_meta_box_whenTemplateMatches_registersMetaBox() {
		// Given.
		$post = (object) array( 'ID' => 43 );
		$this->set_page_template( 43, 'templates/page-home.php' );

		// When.
		kiyose_add_home_hero_meta_box( $post );

		// Then.
		$this->assertContains( 'kiyose_home_hero', $this->registered_meta_box_ids() );
	}

	public function test_kiyose_add_home_hero_meta_box_whenTemplateDiffers_doesNotRegisterMetaBox() {
		// Given.
		$post = (object) array( 'ID' => 43 );
		$this->set_page_template( 43, 'templates/page-contact.php' );

		// When.
		kiyose_add_home_hero_meta_box( $post );

		// Then.
		$this->assertNotContains( 'kiyose_home_hero', $this->registered_meta_box_ids() );
	}

	public function test_kiyose_add_contact_photo_meta_box_whenTemplateMatches_registersMetaBox() {
		// Given.
		$post = (object) array( 'ID' => 43 );
		$this->set_page_template( 43, 'templates/page-contact.php' );

		// When.
		kiyose_add_contact_photo_meta_box( $post );

		// Then.
		$this->assertContains( 'kiyose_contact_photo', $this->registered_meta_box_ids() );
	}

	public function test_kiyose_add_contact_photo_meta_box_whenTemplateDiffers_doesNotRegisterMetaBox() {
		// Given.
		$post = (object) array( 'ID' => 43 );
		$this->set_page_template( 43, 'templates/page-home.php' );

		// When.
		kiyose_add_contact_photo_meta_box( $post );

		// Then.
		$this->assertNotContains( 'kiyose_contact_photo', $this->registered_meta_box_ids() );
	}

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

	public function test_kiyose_sanitize_welcome_keyword_items_whenJsonIsInvalid_returnsEmptyArray() {
		// Given.
		$raw_json = '{not-json';

		// When.
		$result = function_exists( 'kiyose_sanitize_welcome_keyword_items' )
			? kiyose_sanitize_welcome_keyword_items( $raw_json )
			: null;

		// Then.
		$this->assertSame( array(), $result );
	}

	public function test_kiyose_render_text_field_whenValueContainsHtml_escapesOutput() {
		// Given.
		$args = array(
			'id'          => 'kiyose_test_field',
			'name'        => 'kiyose_test_field',
			'label'       => 'Champ <dangereux>',
			'value'       => '"><script>alert(1)</script>',
			'description' => 'Description <strong>éditoriale</strong>',
		);

		// When.
		ob_start();
		if ( function_exists( 'kiyose_render_text_field' ) ) {
			kiyose_render_text_field( $args );
		}
		$html = ob_get_clean();

		// Then.
		$this->assertStringContainsString( 'Champ &lt;dangereux&gt;', $html );
		$this->assertStringContainsString( '&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;', $html );
		$this->assertStringContainsString( 'Description &lt;strong&gt;éditoriale&lt;/strong&gt;', $html );
		$this->assertStringNotContainsString( '<script>', $html );
		$this->assertStringNotContainsString( '<strong>', $html );
	}

	public function test_kiyose_enqueue_admin_meta_box_assets_whenEditingPage_enqueuesAssetsAndMedia() {
		// Given.
		$GLOBALS['kiyose_test_current_screen'] = (object) array( 'post_type' => 'page' );

		// When.
		if ( function_exists( 'kiyose_enqueue_admin_meta_box_assets' ) ) {
			kiyose_enqueue_admin_meta_box_assets( 'post.php' );
		}

		// Then.
		$this->assertContains( 'kiyose-admin-meta-boxes', $this->enqueued_style_handles() );
		$this->assertContains( 'kiyose-admin-meta-boxes', $this->enqueued_script_handles() );
		$this->assertTrue( $GLOBALS['kiyose_test_did_enqueue_media'] );
		$this->assertSame( 'kiyoseMetaBoxes', $GLOBALS['kiyose_test_localized_scripts'][0]['object_name'] );
	}

	public function test_kiyose_enqueue_admin_meta_box_assets_whenHookIsNotEditor_doesNothing() {
		// Given.
		$GLOBALS['kiyose_test_current_screen'] = (object) array( 'post_type' => 'page' );

		// When.
		if ( function_exists( 'kiyose_enqueue_admin_meta_box_assets' ) ) {
			kiyose_enqueue_admin_meta_box_assets( 'edit.php' );
		}

		// Then.
		$this->assertSame( array(), $this->enqueued_style_handles() );
		$this->assertSame( array(), $this->enqueued_script_handles() );
		$this->assertFalse( $GLOBALS['kiyose_test_did_enqueue_media'] );
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
