<?php
/**
 * Tests for blog archive helpers.
 *
 * @package Kiyose
 * @since   0.2.7
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for blog archive helper functions.
 */
class Test_Blog extends TestCase {

	public function test_kiyose_get_blog_card_variant_ofEmptyArgs_returnsCard() {
		// Given
		$args = array();

		// When
		$result = $this->call_blog_card_variant( $args );

		// Then
		$this->assertSame( 'card', $result );
	}

	public function test_kiyose_get_blog_card_variant_ofArchive_returnsArchive() {
		// Given
		$args = array(
			'variant' => 'archive',
		);

		// When
		$result = $this->call_blog_card_variant( $args );

		// Then
		$this->assertSame( 'archive', $result );
	}

	public function test_kiyose_get_blog_card_variant_ofUnsupportedVariant_returnsCard() {
		// Given
		$args = array(
			'variant' => 'featured<script>',
		);

		// When
		$result = $this->call_blog_card_variant( $args );

		// Then
		$this->assertSame( 'card', $result );
	}

	public function test_kiyose_get_blog_card_classes_ofArchive_includesArchiveModifier() {
		// Given
		$variant = 'archive';

		// When
		$result = $this->call_blog_card_classes( $variant );

		// Then
		$this->assertSame( array( 'blog-card', 'blog-card--archive' ), $result );
	}

	public function test_kiyose_get_blog_card_classes_ofCard_returnsBaseClassOnly() {
		// Given
		$variant = 'card';

		// When
		$result = $this->call_blog_card_classes( $variant );

		// Then
		$this->assertSame( array( 'blog-card' ), $result );
	}

	public function test_kiyose_get_blog_sidebar_categories_args_returnsExpectedWordPressArguments() {
		// Given / When
		$result = $this->call_blog_sidebar_categories_args();

		// Then
		$this->assertSame(
			array(
				'title_li'   => '',
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'show_count' => false,
			),
			$result
		);
	}

	public function test_kiyose_has_visible_blog_categories_ofEmptyList_returnsFalse() {
		// Given
		$categories = array();

		// When
		$result = $this->call_has_visible_blog_categories( $categories );

		// Then
		$this->assertFalse( $result );
	}

	public function test_kiyose_has_visible_blog_categories_ofNonEmptyList_returnsTrue() {
		// Given
		$categories = array(
			(object) array(
				'name' => 'Actualités',
			),
		);

		// When
		$result = $this->call_has_visible_blog_categories( $categories );

		// Then
		$this->assertTrue( $result );
	}

	private function call_blog_card_variant( $args ) {
		if ( ! function_exists( 'kiyose_get_blog_card_variant' ) ) {
			$this->fail( 'Expected kiyose_get_blog_card_variant() to exist.' );
		}

		return kiyose_get_blog_card_variant( $args );
	}

	private function call_blog_card_classes( $variant ) {
		if ( ! function_exists( 'kiyose_get_blog_card_classes' ) ) {
			$this->fail( 'Expected kiyose_get_blog_card_classes() to exist.' );
		}

		return kiyose_get_blog_card_classes( $variant );
	}

	private function call_blog_sidebar_categories_args() {
		if ( ! function_exists( 'kiyose_get_blog_sidebar_categories_args' ) ) {
			$this->fail( 'Expected kiyose_get_blog_sidebar_categories_args() to exist.' );
		}

		return kiyose_get_blog_sidebar_categories_args();
	}

	private function call_has_visible_blog_categories( $categories ) {
		if ( ! function_exists( 'kiyose_has_visible_blog_categories' ) ) {
			$this->fail( 'Expected kiyose_has_visible_blog_categories() to exist.' );
		}

		return kiyose_has_visible_blog_categories( $categories );
	}
}
