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

	protected function setUp(): void {
		parent::setUp();

		kiyose_reset_test_state();
	}

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

	public function test_singleTemplate_ordersTitleBeforeDate() {
		// Given
		$template = file_get_contents( __DIR__ . '/../single.php' );

		// When
		$title_position = strpos( $template, 'blog-single__title' );
		$date_position  = strpos( $template, 'blog-single__date' );

		// Then
		$this->assertNotFalse( $title_position );
		$this->assertNotFalse( $date_position );
		$this->assertLessThan( $date_position, $title_position );
	}

	public function test_singleTemplate_usesSinglePostNavigationHelper() {
		// Given
		$template = file_get_contents( __DIR__ . '/../single.php' );

		// When / Then
		$this->assertStringContainsString( 'kiyose_get_single_post_navigation_html', $template );
		$this->assertStringNotContainsString( 'the_post_navigation(', $template );
	}

	public function test_kiyose_get_event_post_navigation_html_whenCurrentEventHasNeighbors_returnsChronologicalNavigation() {
		// Given
		$this->add_test_event( 12, 'Atelier courant', '2026-03-15', 'https://example.com/events/courant/' );
		$this->add_test_event( 11, 'Atelier précédent', '2026-01-20', 'https://example.com/events/precedent/' );
		$this->add_test_event( 13, 'Atelier suivant', '2026-04-10', 'https://example.com/events/suivant/' );

		// When
		$html = $this->call_event_post_navigation_html( 12 );

		// Then
		$this->assertStringContainsString( 'aria-label="Navigation entre événements"', $html );
		$this->assertStringContainsString( 'Événement précédent', $html );
		$this->assertStringContainsString( 'Atelier précédent', $html );
		$this->assertStringContainsString( 'https://example.com/events/precedent/', $html );
		$this->assertStringContainsString( 'Événement suivant', $html );
		$this->assertStringContainsString( 'Atelier suivant', $html );
		$this->assertStringNotContainsString( 'Atelier courant', $html );
		$this->assertSame( 'event', $GLOBALS['kiyose_test_last_get_posts_args']['post_type'] );
		$this->assertSame( 'publish', $GLOBALS['kiyose_test_last_get_posts_args']['post_status'] );
		$this->assertSame( 'ids', $GLOBALS['kiyose_test_last_get_posts_args']['fields'] );
	}

	public function test_kiyose_get_event_post_navigation_html_whenCurrentEventIsFirst_returnsOnlyNextNavigation() {
		// Given
		$this->add_test_event( 11, 'Atelier premier', '2026-01-20', 'https://example.com/events/premier/' );
		$this->add_test_event( 12, 'Atelier suivant', '2026-03-15', 'https://example.com/events/suivant/' );

		// When
		$html = $this->call_event_post_navigation_html( 11 );

		// Then
		$this->assertStringNotContainsString( 'nav-previous', $html );
		$this->assertStringNotContainsString( 'Événement précédent', $html );
		$this->assertStringContainsString( 'nav-next', $html );
		$this->assertStringContainsString( 'Atelier suivant', $html );
	}

	public function test_kiyose_get_event_post_navigation_html_whenCurrentEventIsLast_returnsOnlyPreviousNavigation() {
		// Given
		$this->add_test_event( 11, 'Atelier précédent', '2026-01-20', 'https://example.com/events/precedent/' );
		$this->add_test_event( 12, 'Atelier dernier', '2026-03-15', 'https://example.com/events/dernier/' );

		// When
		$html = $this->call_event_post_navigation_html( 12 );

		// Then
		$this->assertStringContainsString( 'nav-previous', $html );
		$this->assertStringContainsString( 'Atelier précédent', $html );
		$this->assertStringNotContainsString( 'nav-next', $html );
		$this->assertStringNotContainsString( 'Événement suivant', $html );
	}

	public function test_kiyose_get_event_post_navigation_html_whenCurrentEventHasNoDate_returnsEmptyString() {
		// Given
		$this->add_test_event( 11, 'Atelier précédent', '2026-01-20', 'https://example.com/events/precedent/' );
		$this->add_test_event( 12, 'Atelier sans date', '', 'https://example.com/events/sans-date/' );
		$this->add_test_event( 13, 'Atelier suivant', '2026-04-10', 'https://example.com/events/suivant/' );

		// When
		$html = $this->call_event_post_navigation_html( 12 );

		// Then
		$this->assertSame( '', $html );
	}

	public function test_kiyose_get_event_post_navigation_html_whenStartTimestampExists_usesItBeforeStartDate() {
		// Given
		$this->add_test_event( 11, 'Atelier précédent', '2026-01-20', 'https://example.com/events/precedent/' );
		$this->add_test_event( 12, 'Atelier courant', '2026-03-15', 'https://example.com/events/courant/', strtotime( '2026-02-20' ) );
		$this->add_test_event( 13, 'Atelier suivant', '2026-03-01', 'https://example.com/events/suivant/' );

		// When
		$html = $this->call_event_post_navigation_html( 12 );

		// Then
		$this->assertStringContainsString( 'Atelier précédent', $html );
		$this->assertStringContainsString( 'Atelier suivant', $html );
	}

	public function test_kiyose_get_single_post_navigation_html_whenPostIsBlogPost_returnsNativePostNavigation() {
		// Given
		$GLOBALS['kiyose_test_post_types'][ 21 ]             = 'post';
		$GLOBALS['kiyose_test_native_post_navigation_html'] = '<nav class="navigation post-navigation">Native navigation</nav>';

		// When
		$html = $this->call_single_post_navigation_html( 21 );

		// Then
		$this->assertSame( '<nav class="navigation post-navigation">Native navigation</nav>', $html );
		$this->assertStringContainsString( 'Article précédent', $GLOBALS['kiyose_test_last_post_navigation_args']['prev_text'] );
		$this->assertStringContainsString( 'Article suivant', $GLOBALS['kiyose_test_last_post_navigation_args']['next_text'] );
		$this->assertSame( 'Navigation entre articles', $GLOBALS['kiyose_test_last_post_navigation_args']['aria_label'] );
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

	private function call_event_post_navigation_html( $post_id ) {
		if ( ! function_exists( 'kiyose_get_event_post_navigation_html' ) ) {
			$this->fail( 'Expected kiyose_get_event_post_navigation_html() to exist.' );
		}

		return kiyose_get_event_post_navigation_html( $post_id );
	}

	private function call_single_post_navigation_html( $post_id ) {
		if ( ! function_exists( 'kiyose_get_single_post_navigation_html' ) ) {
			$this->fail( 'Expected kiyose_get_single_post_navigation_html() to exist.' );
		}

		return kiyose_get_single_post_navigation_html( $post_id );
	}

	private function add_test_event( $post_id, $title, $start_date, $permalink, $start_timestamp = null ) {
		$GLOBALS['kiyose_test_posts'][] = (object) array(
			'ID'          => $post_id,
			'post_status' => 'publish',
			'post_type'   => 'event',
		);

		$GLOBALS['kiyose_test_post_types'][ $post_id ] = 'event';
		$GLOBALS['kiyose_test_titles'][ $post_id ]     = $title;
		$GLOBALS['kiyose_test_permalinks'][ $post_id ] = $permalink;

		if ( null !== $start_timestamp ) {
			$GLOBALS['kiyose_test_post_meta'][ $post_id ]['_start_ts'] = $start_timestamp;
		}

		$GLOBALS['kiyose_test_post_meta'][ $post_id ]['_event_start_date'] = $start_date;
	}
}
