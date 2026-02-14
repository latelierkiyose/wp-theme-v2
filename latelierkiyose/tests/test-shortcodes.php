<?php
/**
 * Tests for Shortcodes.
 *
 * @package Kiyose
 * @since   0.1.7
 */

/**
 * Test class for kiyose_testimonials shortcode.
 */
class Test_Shortcodes extends WP_UnitTestCase {

	/**
	 * Test that kiyose_testimonials shortcode is registered.
	 */
	public function test_kiyose_testimonials_shortcode_is_registered() {
		$this->assertTrue( shortcode_exists( 'kiyose_testimonials' ) );
	}

	/**
	 * Test shortcode returns HTML with testimonies.
	 */
	public function test_shortcode_returns_html_with_testimonies() {
		// Create test testimonies.
		$post_ids = $this->factory->post->create_many(
			3,
			array(
				'post_type'   => 'kiyose_testimony',
				'post_status' => 'publish',
			)
		);

		$output = do_shortcode( '[kiyose_testimonials]' );

		$this->assertStringContainsString( 'testimonials-grid', $output );
		$this->assertStringContainsString( 'testimonials-grid--columns-3', $output );
		$this->assertStringContainsString( 'testimony-card', $output );
	}

	/**
	 * Test shortcode returns empty message when no testimonies.
	 */
	public function test_shortcode_returns_empty_message_when_no_testimonies() {
		$output = do_shortcode( '[kiyose_testimonials]' );

		$this->assertStringContainsString( 'testimonials-empty', $output );
		$this->assertStringContainsString( 'Aucun témoignage disponible pour le moment.', $output );
	}

	/**
	 * Test shortcode columns attribute is sanitized.
	 */
	public function test_shortcode_columns_attribute_is_sanitized() {
		$output = do_shortcode( '[kiyose_testimonials columns="2"]' );
		$this->assertStringContainsString( 'testimonials-grid--columns-2', $output );

		// Test default when invalid value.
		$output = do_shortcode( '[kiyose_testimonials columns="10"]' );
		$this->assertStringContainsString( 'testimonials-grid--columns-3', $output );

		$output = do_shortcode( '[kiyose_testimonials columns="0"]' );
		$this->assertStringContainsString( 'testimonials-grid--columns-3', $output );
	}

	/**
	 * Test shortcode limit attribute works.
	 */
	public function test_shortcode_limit_attribute_works() {
		// Create 5 testimonies.
		$post_ids = $this->factory->post->create_many(
			5,
			array(
				'post_type'   => 'kiyose_testimony',
				'post_status' => 'publish',
			)
		);

		$output = do_shortcode( '[kiyose_testimonials limit="2"]' );

		$count = substr_count( $output, 'testimony-card' );
		$this->assertEquals( 2, $count );
	}

	/**
	 * Test shortcode context filter works.
	 */
	public function test_shortcode_context_filter_works() {
		// Create individual testimony.
		$individual_id = $this->factory->post->create(
			array(
				'post_type'   => 'kiyose_testimony',
				'post_status' => 'publish',
			)
		);
		update_post_meta( $individual_id, 'kiyose_testimony_context', 'individual' );

		// Create enterprise testimony.
		$enterprise_id = $this->factory->post->create(
			array(
				'post_type'   => 'kiyose_testimony',
				'post_status' => 'publish',
			)
		);
		update_post_meta( $enterprise_id, 'kiyose_testimony_context', 'enterprise' );

		// Test filter by enterprise.
		$output = do_shortcode( '[kiyose_testimonials context="enterprise"]' );

		$count = substr_count( $output, 'testimony-card' );
		$this->assertEquals( 1, $count );
	}

	/**
	 * Test shortcode display attribute validation.
	 */
	public function test_shortcode_display_attribute_validation() {
		$this->factory->post->create(
			array(
				'post_type'   => 'kiyose_testimony',
				'post_status' => 'publish',
			)
		);

		// Valid display: grid.
		$output = do_shortcode( '[kiyose_testimonials display="grid"]' );
		$this->assertStringContainsString( 'testimonials-grid', $output );

		// Invalid display defaults to grid.
		$output = do_shortcode( '[kiyose_testimonials display="invalid"]' );
		$this->assertStringContainsString( 'testimonials-grid', $output );
	}

	/**
	 * Test shortcode carousel mode renders carousel HTML.
	 */
	public function test_shortcode_carousel_mode_renders_carousel() {
		$this->factory->post->create(
			array(
				'post_type'   => 'kiyose_testimony',
				'post_status' => 'publish',
			)
		);

		$output = do_shortcode( '[kiyose_testimonials display="carousel"]' );
		$this->assertStringContainsString( 'class="carousel"', $output );
		$this->assertStringContainsString( 'role="region"', $output );
		$this->assertStringContainsString( 'aria-roledescription="carousel"', $output );
		$this->assertStringContainsString( 'carousel__controls', $output );
		$this->assertStringContainsString( 'carousel__prev', $output );
		$this->assertStringContainsString( 'carousel__next', $output );
		$this->assertStringContainsString( 'carousel__pause', $output );
		$this->assertStringContainsString( 'carousel__track', $output );
		$this->assertStringContainsString( 'carousel__slide', $output );
		$this->assertStringContainsString( 'testimony-card', $output );
	}

	/**
	 * Test wp_reset_postdata is called.
	 */
	public function test_wp_reset_postdata_is_called() {
		global $post;

		// Create a regular post and set it as global.
		$regular_post_id = $this->factory->post->create(
			array(
				'post_type' => 'post',
			)
		);
		$post            = get_post( $regular_post_id );
		setup_postdata( $post );

		// Create testimony.
		$this->factory->post->create(
			array(
				'post_type'   => 'kiyose_testimony',
				'post_status' => 'publish',
			)
		);

		// Execute shortcode.
		do_shortcode( '[kiyose_testimonials]' );

		// Global $post should be reset to regular post.
		$this->assertEquals( $regular_post_id, $post->ID );

		wp_reset_postdata();
	}
}
