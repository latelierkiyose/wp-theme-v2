<?php
/**
 * Tests for Custom Post Types.
 *
 * @package Kiyose
 * @since   0.1.6
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for kiyose_testimony custom post type helper functions.
 */
class Test_Custom_Post_Types extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		kiyose_reset_test_state();
		$_POST = array();
	}

	private function registered_meta_box_ids(): array {
		return array_column( $GLOBALS['kiyose_test_added_meta_boxes'], 'id' );
	}

	public function test_kiyose_register_testimony_cpt_registersPrivateUiPostType() {
		// Given
		kiyose_register_testimony_cpt();

		// When
		$args = $GLOBALS['kiyose_test_registered_post_types']['kiyose_testimony'] ?? null;

		// Then
		$this->assertIsArray( $args );
		$this->assertFalse( $args['public'] );
		$this->assertTrue( $args['show_ui'] );
		$this->assertSame( array( 'title', 'editor' ), $args['supports'] );
		$this->assertFalse( $args['publicly_queryable'] );
	}

	public function test_kiyose_add_testimony_meta_box_registersContextMetaBox() {
		// Given

		// When
		kiyose_add_testimony_meta_box();

		// Then
		$this->assertContains( 'kiyose_testimony_context', $this->registered_meta_box_ids() );
		$this->assertSame( 'kiyose_testimony', $GLOBALS['kiyose_test_added_meta_boxes'][0]['screen'] );
		$this->assertSame( 'side', $GLOBALS['kiyose_test_added_meta_boxes'][0]['context'] );
	}

	public function test_kiyose_render_testimony_context_meta_box_whenContextExists_selectsSavedOption() {
		// Given
		$post = (object) array( 'ID' => 72 );
		$GLOBALS['kiyose_test_post_meta'][72]['kiyose_testimony_context'] = 'enterprise';

		// When
		ob_start();
		kiyose_render_testimony_context_meta_box( $post );
		$html = ob_get_clean();

		// Then
		$this->assertStringContainsString( 'name="kiyose_testimony_context"', $html );
		$this->assertMatchesRegularExpression( '/value="enterprise"\s+selected="selected"/', $html );
		$this->assertStringContainsString( 'Entreprise', $html );
	}

	public function test_kiyose_save_testimony_context_meta_ofValidContext_savesContext() {
		// Given
		$post_id = 72;
		$_POST   = array(
			'kiyose_testimony_context_nonce' => 'nonce',
			'kiyose_testimony_context'       => 'structure',
		);

		// When
		kiyose_save_testimony_context_meta( $post_id );

		// Then
		$this->assertSame( 'structure', $GLOBALS['kiyose_test_post_meta'][ $post_id ]['kiyose_testimony_context'] );
	}

	public function test_kiyose_save_testimony_context_meta_whenContextIsInvalid_doesNotSave() {
		// Given
		$post_id = 72;
		$_POST   = array(
			'kiyose_testimony_context_nonce' => 'nonce',
			'kiyose_testimony_context'       => 'administrator',
		);

		// When
		kiyose_save_testimony_context_meta( $post_id );

		// Then
		$this->assertArrayNotHasKey( $post_id, $GLOBALS['kiyose_test_post_meta'] );
	}

	public function test_kiyose_save_testimony_context_meta_whenNonceIsMissing_doesNotSave() {
		// Given
		$post_id = 72;
		$_POST   = array(
			'kiyose_testimony_context' => 'enterprise',
		);

		// When
		kiyose_save_testimony_context_meta( $post_id );

		// Then
		$this->assertArrayNotHasKey( $post_id, $GLOBALS['kiyose_test_post_meta'] );
	}

	public function test_kiyose_save_testimony_context_meta_whenCurrentUserCannotEdit_doesNotSave() {
		// Given
		$post_id = 72;
		$GLOBALS['kiyose_test_current_user_can'] = false;
		$_POST = array(
			'kiyose_testimony_context_nonce' => 'nonce',
			'kiyose_testimony_context'       => 'enterprise',
		);

		// When
		kiyose_save_testimony_context_meta( $post_id );

		// Then
		$this->assertArrayNotHasKey( $post_id, $GLOBALS['kiyose_test_post_meta'] );
	}

	/**
	 * Test kiyose_get_testimony_context_label function returns correct labels.
	 */
	public function test_kiyose_get_testimony_context_label_returns_correct_labels() {
		$this->assertEquals( '', kiyose_get_testimony_context_label( 'individual' ) );
		$this->assertEquals( 'Entreprise', kiyose_get_testimony_context_label( 'enterprise' ) );
		$this->assertEquals( 'Association / Structure', kiyose_get_testimony_context_label( 'structure' ) );
	}

	/**
	 * Test kiyose_get_testimony_context_label function returns empty string for unknown context.
	 */
	public function test_kiyose_get_testimony_context_label_returns_empty_for_unknown_context() {
		$this->assertEquals( '', kiyose_get_testimony_context_label( 'unknown' ) );
		$this->assertEquals( '', kiyose_get_testimony_context_label( '' ) );
	}
}
