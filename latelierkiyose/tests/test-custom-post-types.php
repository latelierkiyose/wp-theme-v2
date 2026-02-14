<?php
/**
 * Tests for Custom Post Types.
 *
 * @package Kiyose
 * @since   0.1.6
 */

/**
 * Test class for kiyose_testimony custom post type.
 */
class Test_Custom_Post_Types extends WP_UnitTestCase {

	/**
	 * Test that kiyose_testimony CPT is registered.
	 */
	public function test_kiyose_testimony_cpt_is_registered() {
		$this->assertTrue( post_type_exists( 'kiyose_testimony' ) );
	}

	/**
	 * Test kiyose_testimony CPT configuration.
	 */
	public function test_kiyose_testimony_cpt_configuration() {
		$post_type_object = get_post_type_object( 'kiyose_testimony' );

		$this->assertNotNull( $post_type_object );
		$this->assertFalse( $post_type_object->public );
		$this->assertTrue( $post_type_object->show_ui );
		$this->assertTrue( $post_type_object->show_in_menu );
		$this->assertFalse( $post_type_object->has_archive );
		$this->assertFalse( $post_type_object->publicly_queryable );
		$this->assertEquals( 'dashicons-format-quote', $post_type_object->menu_icon );
	}

	/**
	 * Test kiyose_testimony CPT supports.
	 */
	public function test_kiyose_testimony_cpt_supports() {
		$this->assertTrue( post_type_supports( 'kiyose_testimony', 'title' ) );
		$this->assertTrue( post_type_supports( 'kiyose_testimony', 'editor' ) );
		$this->assertFalse( post_type_supports( 'kiyose_testimony', 'thumbnail' ) );
		$this->assertFalse( post_type_supports( 'kiyose_testimony', 'excerpt' ) );
	}

	/**
	 * Test testimony context meta save and retrieve.
	 */
	public function test_testimony_context_meta_save_and_retrieve() {
		$post_id = $this->factory->post->create(
			array(
				'post_type' => 'kiyose_testimony',
			)
		);

		// Simulate POST data.
		$_POST['kiyose_testimony_context_nonce'] = wp_create_nonce( 'kiyose_testimony_context_nonce_action' );
		$_POST['kiyose_testimony_context']       = 'enterprise';

		// Simulate user with edit capabilities.
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Trigger save function.
		kiyose_save_testimony_context_meta( $post_id );

		// Retrieve and verify.
		$context = get_post_meta( $post_id, 'kiyose_testimony_context', true );
		$this->assertEquals( 'enterprise', $context );
	}

	/**
	 * Test testimony context meta validation rejects invalid values.
	 */
	public function test_testimony_context_meta_validation_rejects_invalid_value() {
		$post_id = $this->factory->post->create(
			array(
				'post_type' => 'kiyose_testimony',
			)
		);

		// Simulate POST data with invalid value.
		$_POST['kiyose_testimony_context_nonce'] = wp_create_nonce( 'kiyose_testimony_context_nonce_action' );
		$_POST['kiyose_testimony_context']       = 'invalid_value';

		// Simulate user with edit capabilities.
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Trigger save function.
		kiyose_save_testimony_context_meta( $post_id );

		// Verify invalid value was not saved.
		$context = get_post_meta( $post_id, 'kiyose_testimony_context', true );
		$this->assertEmpty( $context );
	}

	/**
	 * Test kiyose_get_testimony_context_label function.
	 */
	public function test_kiyose_get_testimony_context_label_returns_correct_labels() {
		$this->assertEquals( '', kiyose_get_testimony_context_label( 'individual' ) );
		$this->assertEquals( 'Entreprise', kiyose_get_testimony_context_label( 'enterprise' ) );
		$this->assertEquals( 'Association / Structure', kiyose_get_testimony_context_label( 'structure' ) );
		$this->assertEquals( '', kiyose_get_testimony_context_label( 'unknown' ) );
	}

	/**
	 * Tear down: clean up $_POST.
	 */
	public function tearDown(): void {
		unset( $_POST['kiyose_testimony_context_nonce'] );
		unset( $_POST['kiyose_testimony_context'] );
		parent::tearDown();
	}
}
